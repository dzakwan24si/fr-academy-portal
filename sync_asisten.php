<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use Illuminate\Support\Facades\DB;

$csvPath = __DIR__ . '/storage/app/alumni_monitoring.csv';
$handle = fopen($csvPath, 'r');
$header = fgetcsv($handle, 0, ';', '"', '\\');

$ketIdx = array_search('Keterangan', $header);
$ketResignIdx = array_search('Keterangan Resign', $header);
$tglResignIdx = array_search('Tanggal Resign', $header);
$lvlIdx = array_search('LVL', $header);
$jabIdx = array_search('Jabatan Terakhir', $header);
$programIdx = array_search('Program', $header);

echo "Indices: LVL=$lvlIdx, Jab=$jabIdx, Prog=$programIdx, Ket=$ketIdx, KetResign=$ketResignIdx, TglResign=$tglResignIdx\n";

$rowNum = 0;
$updateCount = 0;
DB::beginTransaction();
try {
    while (($row = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
        if (empty(array_filter($row))) continue;
        $rowNum++;

        $ket = trim($row[$ketIdx] ?? '');
        $ketResign = trim($row[$ketResignIdx] ?? '');
        $tglResign = trim($row[$tglResignIdx] ?? '');
        $lvl = trim($row[$lvlIdx] ?? '');
        $jab = trim($row[$jabIdx] ?? '');
        $prog = trim($row[$programIdx] ?? '');

        $ketLower = strtolower($ket);
        $ketResLower = strtolower($ketResign);

        $isCulled = (str_contains($ketLower, 'culled') || str_contains($ketLower, 'gagal'));
        $isResign = (str_contains($ketLower, 'resign') || str_contains($ketLower, 'phk') || str_contains($ketResLower, 'resign') || str_contains($ketResLower, 'phk') || !empty($tglResign));
        $isEksis = ($ketLower === 'eksis' || $ketLower === 'aktif') && !$isCulled && !$isResign;

        $finalKet = 'Resign';
        $finalStatusResign = 'Resign';
        if ($isEksis) {
            $finalKet = 'Aktif';
            $finalStatusResign = 'Aktif';
        } elseif ($isCulled) {
            $finalKet = 'Gagal';
            $finalStatusResign = 'Gagal';
        }

        // Update corresponding record by row / id
        // Asisten IDs are 1 to 2754
        DB::table('asisten')->where('id', $rowNum)->update([
            'keterangan' => $finalKet,
            'status_resign' => $finalStatusResign,
            'level_jabatan' => $lvl ?: null,
            'jabatan_terakhir' => $jab ?: null,
            'program' => $prog ?: null,
        ]);
        $updateCount++;
    }
    DB::commit();
    echo "Successfully synced {$updateCount} records into table 'asisten'.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
fclose($handle);

echo "\nNew DB breakdown:\n";
$byKet = Asisten::selectRaw('keterangan, count(*) as cnt')->groupBy('keterangan')->get();
foreach ($byKet as $b) {
    echo "  {$b->keterangan}: {$b->cnt}\n";
}

$byLvl = Asisten::where('keterangan', 'Aktif')->selectRaw('level_jabatan, count(*) as cnt')->groupBy('level_jabatan')->get();
echo "Active by level_jabatan:\n";
foreach ($byLvl as $b) {
    echo "  " . ($b->level_jabatan ?? 'NULL') . ": {$b->cnt}\n";
}
