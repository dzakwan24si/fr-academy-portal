<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FileArchiveService
{
    /**
     * Create a ZIP archive from an array of files.
     * Each item in $files should be: ['path' => '/abs/path/on/disk', 'name' => 'in_zip/filename.ext']
     *
     * @param  array<int, array{path: string, name: string}>  $files
     */
    public static function createZip(array $files, string $destinationZipPath): bool
    {
        if (empty($files)) {
            return false;
        }

        $validFiles = [];
        foreach ($files as $file) {
            $path = $file['path'] ?? '';
            $name = $file['name'] ?? '';

            if (empty($path) || ! file_exists($path) || ! is_readable($path)) {
                continue;
            }

            // Clean zip path: forward slashes, no leading slash or relative dots
            $normalizedName = str_replace('\\', '/', $name);
            $normalizedName = ltrim($normalizedName, '/');
            $normalizedName = preg_replace('#\.\./#', '', $normalizedName);

            if (! empty($normalizedName)) {
                $validFiles[] = [
                    'path' => $path,
                    'name' => $normalizedName,
                ];
            }
        }

        if (empty($validFiles)) {
            self::logWarning('FileArchiveService: No valid physical files found to archive.');

            return false;
        }

        self::ensureDirectory(dirname($destinationZipPath));
        if (file_exists($destinationZipPath)) {
            @unlink($destinationZipPath);
        }

        // Method 1: Native PHP \ZipArchive
        if (class_exists(\ZipArchive::class)) {
            try {
                $zip = new \ZipArchive;
                if ($zip->open($destinationZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                    foreach ($validFiles as $item) {
                        $zip->addFile($item['path'], $item['name']);
                    }
                    $zip->close();

                    if (file_exists($destinationZipPath) && filesize($destinationZipPath) > 0) {
                        return true;
                    }
                }
            } catch (\Throwable $e) {
                self::logWarning('FileArchiveService: ZipArchive failed, falling back: '.$e->getMessage());
                if (file_exists($destinationZipPath)) {
                    @unlink($destinationZipPath);
                }
            }
        }

        // Method 2: System 'zip' CLI binary (Linux / Unix / Windows)
        if (self::hasSystemZip()) {
            try {
                if (self::createWithSystemZip($validFiles, $destinationZipPath)) {
                    return true;
                }
            } catch (\Throwable $e) {
                self::logWarning('FileArchiveService: System zip command failed, falling back to pure PHP: '.$e->getMessage());
                if (file_exists($destinationZipPath)) {
                    @unlink($destinationZipPath);
                }
            }
        }

        // Method 3: Pure PHP ZIP writer (zero extensions required, 100% portable)
        try {
            return self::createWithPurePhp($validFiles, $destinationZipPath);
        } catch (\Throwable $e) {
            self::logError('FileArchiveService: Pure PHP zip writer failed: '.$e->getMessage());
            if (file_exists($destinationZipPath)) {
                @unlink($destinationZipPath);
            }

            return false;
        }
    }

    /**
     * Check if system 'zip' command line binary is available.
     */
    public static function hasSystemZip(): bool
    {
        if (! function_exists('shell_exec') || ! function_exists('exec')) {
            return false;
        }

        $checkCmd = DIRECTORY_SEPARATOR === '\\' ? 'where zip 2>nul' : 'which zip 2>/dev/null';
        $output = @shell_exec($checkCmd);

        return ! empty(trim((string) $output));
    }

    /**
     * Archive files using system 'zip' binary.
     *
     * @param  array<int, array{path: string, name: string}>  $files
     */
    protected static function createWithSystemZip(array $files, string $destinationZipPath): bool
    {
        $baseTemp = function_exists('storage_path') ? storage_path('app/temp') : sys_get_temp_dir();
        $stagingDir = $baseTemp.'/stage_'.uniqid();
        self::ensureDirectory($stagingDir);

        try {
            foreach ($files as $item) {
                $targetPath = $stagingDir.'/'.$item['name'];
                self::ensureDirectory(dirname($targetPath));
                copy($item['path'], $targetPath);
            }

            $escapedZip = escapeshellarg($destinationZipPath);
            $escapedStage = escapeshellarg($stagingDir);

            if (DIRECTORY_SEPARATOR === '\\') {
                $cmd = "cd /d {$escapedStage} && zip -r -q {$escapedZip} .";
            } else {
                $cmd = "cd {$escapedStage} && zip -r -q {$escapedZip} . 2>&1";
            }

            @exec($cmd, $output, $returnCode);

            self::removeDirectory($stagingDir);

            return $returnCode === 0 && file_exists($destinationZipPath) && filesize($destinationZipPath) > 0;
        } catch (\Throwable $e) {
            self::removeDirectory($stagingDir);
            throw $e;
        }
    }

    /**
     * Pure PHP ZIP generator with standard ZIP PK format.
     * Supports DEFLATE compression via gzdeflate (if zlib loaded) or STORE method.
     *
     * @param  array<int, array{path: string, name: string}>  $files
     */
    protected static function createWithPurePhp(array $files, string $destinationZipPath): bool
    {
        $fp = fopen($destinationZipPath, 'wb');
        if (! $fp) {
            return false;
        }

        $entries = [];
        $hasDeflate = function_exists('gzdeflate');

        foreach ($files as $item) {
            $data = file_get_contents($item['path']);
            if ($data === false) {
                continue;
            }

            $offset = ftell($fp);
            $name = $item['name'];
            $usize = strlen($data);
            $crc = crc32($data);

            if ($hasDeflate) {
                $cdata = gzdeflate($data, 6);
                $csize = strlen($cdata);
                $method = 8; // DEFLATE
            } else {
                $cdata = $data;
                $csize = $usize;
                $method = 0; // STORE
            }

            // DOS timestamp from file modification time
            $mtime = filemtime($item['path']) ?: time();
            [$dtime, $ddate] = self::dosDateTime($mtime);

            // Local file header (30 bytes + name + compressed data)
            $header = pack('VvvvvvVVVvv', 0x04034B50, 20, 0x0800, $method, $dtime, $ddate, $crc, $csize, $usize, strlen($name), 0);
            fwrite($fp, $header.$name.$cdata);

            $entries[] = [
                'name' => $name,
                'method' => $method,
                'dtime' => $dtime,
                'ddate' => $ddate,
                'crc' => $crc,
                'csize' => $csize,
                'usize' => $usize,
                'offset' => $offset,
            ];

            unset($data, $cdata);
        }

        if (empty($entries)) {
            fclose($fp);
            @unlink($destinationZipPath);

            return false;
        }

        // Central Directory Headers
        $cdOffset = ftell($fp);
        foreach ($entries as $e) {
            // Central directory header (46 bytes + name)
            $cd = pack('VvvvvvvVVVvvvvvVV', 0x02014B50, 20, 20, 0x0800, $e['method'], $e['dtime'], $e['ddate'], $e['crc'], $e['csize'], $e['usize'], strlen($e['name']), 0, 0, 0, 0, 0x20, $e['offset']);
            fwrite($fp, $cd.$e['name']);
        }
        $cdSize = ftell($fp) - $cdOffset;

        // End of Central Directory Record (22 bytes)
        $eocd = pack('VvvvvVVv', 0x06054B50, 0, 0, count($entries), count($entries), $cdSize, $cdOffset, 0);
        fwrite($fp, $eocd);
        fclose($fp);

        return file_exists($destinationZipPath) && filesize($destinationZipPath) > 0;
    }

    /**
     * Convert Unix timestamp to MS-DOS date and time format.
     *
     * @return array{0: int, 1: int}
     */
    protected static function dosDateTime(int $timestamp): array
    {
        $timeArray = getdate($timestamp);
        if ($timeArray['year'] < 1980) {
            $year = 1980;
            $mon = 1;
            $mday = 1;
            $hours = 0;
            $minutes = 0;
            $seconds = 0;
        } else {
            $year = $timeArray['year'];
            $mon = $timeArray['mon'];
            $mday = $timeArray['mday'];
            $hours = $timeArray['hours'];
            $minutes = $timeArray['minutes'];
            $seconds = $timeArray['seconds'];
        }

        $dtime = (($hours << 11) | ($minutes << 5) | ($seconds >> 1));
        $ddate = ((($year - 1980) << 9) | ($mon << 5) | $mday);

        return [$dtime, $ddate];
    }

    /**
     * Recursively ensure directory exists.
     */
    protected static function ensureDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    /**
     * Recursively delete directory.
     */
    protected static function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = @scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                self::removeDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }

    protected static function logWarning(string $message): void
    {
        try {
            if (class_exists(Log::class)) {
                Log::warning($message);
            }
        } catch (\Throwable) {
        }
    }

    protected static function logError(string $message): void
    {
        try {
            if (class_exists(Log::class)) {
                Log::error($message);
            }
        } catch (\Throwable) {
        }
    }
}
