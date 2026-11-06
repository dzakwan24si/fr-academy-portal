<?php

namespace Tests\Unit;

use App\Services\FileArchiveService;
use PHPUnit\Framework\TestCase;

class FileArchiveServiceTest extends TestCase
{
    public function test_creates_zip_archive_successfully(): void
    {
        $f1 = tempnam(sys_get_temp_dir(), 'test_f1_');
        $f2 = tempnam(sys_get_temp_dir(), 'test_f2_');
        file_put_contents($f1, 'Isi tugas bagian 1');
        file_put_contents($f2, 'Isi tugas bagian 2');

        $zipPath = sys_get_temp_dir().'/test_archive_'.uniqid().'.zip';

        $created = FileArchiveService::createZip([
            ['path' => $f1, 'name' => '1_tugas1.txt'],
            ['path' => $f2, 'name' => 'folder/2_tugas2.txt'],
        ], $zipPath);

        $this->assertTrue($created);
        $this->assertFileExists($zipPath);
        $this->assertGreaterThan(0, filesize($zipPath));

        @unlink($f1);
        @unlink($f2);
        @unlink($zipPath);
    }

    public function test_pure_php_zip_creation_is_valid(): void
    {
        $f1 = tempnam(sys_get_temp_dir(), 'test_pure1_');
        $f2 = tempnam(sys_get_temp_dir(), 'test_pure2_');
        file_put_contents($f1, 'Sample data A');
        file_put_contents($f2, 'Sample data B in subfolder');

        $zipPath = sys_get_temp_dir().'/test_pure_'.uniqid().'.zip';

        $reflection = new \ReflectionClass(FileArchiveService::class);
        $method = $reflection->getMethod('createWithPurePhp');
        $method->setAccessible(true);

        $result = $method->invoke(null, [
            ['path' => $f1, 'name' => 'file1.txt'],
            ['path' => $f2, 'name' => 'folder/file2.txt'],
        ], $zipPath);

        $this->assertTrue($result);
        $this->assertFileExists($zipPath);
        $this->assertGreaterThan(0, filesize($zipPath));

        // Verify that standard zip tools can open and extract it
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive;
            $opened = $zip->open($zipPath);
            $this->assertTrue($opened);
            $this->assertSame(2, $zip->numFiles);
            $this->assertSame('file1.txt', $zip->getNameIndex(0));
            $this->assertSame('Sample data A', $zip->getFromIndex(0));
            $this->assertSame('folder/file2.txt', $zip->getNameIndex(1));
            $this->assertSame('Sample data B in subfolder', $zip->getFromIndex(1));
            $zip->close();
        }

        @unlink($f1);
        @unlink($f2);
        @unlink($zipPath);
    }
}
