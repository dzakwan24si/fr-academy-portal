<?php

namespace App\Models\Administrasi;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mpp extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'MPP';

    protected $table = 'mpp';
    protected $primaryKey = 'mpp_id';

    public const JENIS_JABATAN = ['Staff', 'PBT', 'KHT', 'KHL'];

    protected $fillable = [
        'nama_jabatan',
        'jenis_jabatan',
        'jumlah',
        'tahun',
        'bulan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tahun' => 'integer',
        'bulan' => 'integer',
    ];

    /**
     * Retrieve Matrix Data for specific year & month
     */
    public static function getMatrixData(int $year, int $month, ?string $search = null)
    {
        $query = self::where('tahun', $year)->where('bulan', $month);

        if (!empty($search)) {
            $query->where('nama_jabatan', 'like', "%{$search}%");
        }

        $records = $query->get();

        // Unique jabatan names ordered alphabetically
        $jabatanList = $records->pluck('nama_jabatan')->unique()->values();

        $matrix = [];
        $totals = [
            'Staff' => 0,
            'PBT' => 0,
            'KHT' => 0,
            'KHL' => 0,
            'total_overall' => 0,
        ];

        foreach ($jabatanList as $jabatan) {
            $row = [
                'nama_jabatan' => $jabatan,
                'Staff' => 0,
                'PBT' => 0,
                'KHT' => 0,
                'KHL' => 0,
            ];

            foreach (self::JENIS_JABATAN as $type) {
                $rec = $records->first(function ($item) use ($jabatan, $type) {
                    return $item->nama_jabatan === $jabatan && $item->jenis_jabatan === $type;
                });

                $count = $rec ? (int) $rec->jumlah : 0;
                $row[$type] = $count;
                $totals[$type] += $count;
            }

            $matrix[] = $row;
        }

        $totals['total_overall'] = $totals['Staff'] + $totals['PBT'] + $totals['KHT'] + $totals['KHL'];

        return [
            'matrix' => $matrix,
            'totals' => $totals,
        ];
    }
}
