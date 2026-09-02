<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Capex;
use Illuminate\Http\Request;

class CapexController extends Controller
{
    /**
     * Display the main CAPEX management page.
     */
    public function index(Request $request)
    {
        $selectedYear = (int) $request->input('year', date('Y'));

        // Dynamically collect all years that have records, always include current year
        $dbYears = Capex::selectRaw('DISTINCT tahun')
            ->orderBy('tahun')
            ->pluck('tahun')
            ->toArray();

        $years = array_unique(array_merge(
            range(2010, max($selectedYear, (int)date('Y'))),
            $dbYears
        ));
        sort($years);

        // RAB Kantor
        $rabKantor = Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'kantor')
            ->where('jenis_catatan', 'rab')
            ->orderBy('capex_id')
            ->paginate(10, ['*'], 'rab_kantor_page');

        // Realisasi Kantor
        $realisasiKantor = Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'kantor')
            ->where('jenis_catatan', 'realisasi')
            ->orderBy('tanggal')
            ->paginate(10, ['*'], 'rel_kantor_page');

        // RAB Mess
        $rabMess = Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'mess')
            ->where('jenis_catatan', 'rab')
            ->orderBy('capex_id')
            ->paginate(10, ['*'], 'rab_mess_page');

        // Realisasi Mess
        $realisasiMess = Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'mess')
            ->where('jenis_catatan', 'realisasi')
            ->orderBy('tanggal')
            ->paginate(10, ['*'], 'rel_mess_page');

        // Summary totals
        $rabKantorTotal = (float) Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'kantor')
            ->where('jenis_catatan', 'rab')
            ->sum('total_rab');

        $rabMessTotal = (float) Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'mess')
            ->where('jenis_catatan', 'rab')
            ->sum('total_rab');

        $realisasiKantorTotal = (float) Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'kantor')
            ->where('jenis_catatan', 'realisasi')
            ->selectRaw('SUM(debit) - SUM(kredit) as total')
            ->value('total');

        $realisasiMessTotal = (float) Capex::where('tahun', $selectedYear)
            ->where('jenis_capex', 'mess')
            ->where('jenis_catatan', 'realisasi')
            ->selectRaw('SUM(debit) - SUM(kredit) as total')
            ->value('total');

        $totalRab        = $rabKantorTotal + $rabMessTotal;
        $totalRealisasi  = $realisasiKantorTotal + $realisasiMessTotal;
        $sisaRab         = $totalRab - $totalRealisasi;

        $summary = compact(
            'rabKantorTotal',
            'rabMessTotal',
            'realisasiKantorTotal',
            'realisasiMessTotal',
            'totalRab',
            'totalRealisasi',
            'sisaRab'
        );

        return view('Administrasi.Pages.capex', compact(
            'years',
            'selectedYear',
            'rabKantor',
            'realisasiKantor',
            'rabMess',
            'realisasiMess',
            'summary'
        ));
    }

    /**
     * Store a new CAPEX record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'          => 'required|integer',
            'jenis_capex'    => 'required|in:kantor,mess',
            'jenis_catatan'  => 'required|in:rab,realisasi',
            'nama_barang'    => 'required|string|max:255',
            'type_merk'      => 'nullable|string|max:255',
            'satuan'         => 'nullable|string|max:100',
            // RAB
            'jumlah_unit'    => 'nullable|numeric|min:0',
            'harga_satuan'   => 'nullable|numeric|min:0',
            // Realisasi
            'tanggal'        => 'nullable|date',
            'nama_transaksi' => 'nullable|string|max:255',
            'debit'          => 'nullable|numeric|min:0',
            'kredit'         => 'nullable|numeric|min:0',
        ]);

        // Auto-calculate total_rab for RAB entries
        if ($validated['jenis_catatan'] === 'rab') {
            $jumlah   = (float) ($validated['jumlah_unit'] ?? 0);
            $harga    = (float) ($validated['harga_satuan'] ?? 0);
            $validated['total_rab'] = $jumlah * $harga;
        }

        Capex::create($validated);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $validated['tahun']])
            ->with('success', 'Data CAPEX berhasil ditambahkan.');
    }

    /**
     * Return JSON for a single record (used by Edit modal).
     */
    public function show($id)
    {
        $record = Capex::findOrFail($id);
        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Update a CAPEX record.
     */
    public function update(Request $request, $id)
    {
        $record = Capex::findOrFail($id);

        $validated = $request->validate([
            'nama_barang'    => 'required|string|max:255',
            'type_merk'      => 'nullable|string|max:255',
            'satuan'         => 'nullable|string|max:100',
            // RAB
            'jumlah_unit'    => 'nullable|numeric|min:0',
            'harga_satuan'   => 'nullable|numeric|min:0',
            // Realisasi
            'tanggal'        => 'nullable|date',
            'nama_transaksi' => 'nullable|string|max:255',
            'debit'          => 'nullable|numeric|min:0',
            'kredit'         => 'nullable|numeric|min:0',
        ]);

        // Auto-calculate total_rab for RAB entries
        if ($record->jenis_catatan === 'rab') {
            $jumlah   = (float) ($validated['jumlah_unit'] ?? $record->jumlah_unit ?? 0);
            $harga    = (float) ($validated['harga_satuan'] ?? $record->harga_satuan ?? 0);
            $validated['total_rab'] = $jumlah * $harga;
        }

        $record->update($validated);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $record->tahun])
            ->with('success', 'Data CAPEX berhasil diperbarui.');
    }

    /**
     * Delete a CAPEX record.
     */
    public function destroy($id)
    {
        $record = Capex::findOrFail($id);
        $tahun  = $record->tahun;
        $record->delete();

        return redirect()
            ->route('administrasi.capex.index', ['year' => $tahun])
            ->with('success', 'Data CAPEX berhasil dihapus.');
    }

    /**
     * Add a new reporting year (redirect to that year's page).
     */
    public function storeYear(Request $request)
    {
        $request->validate(['new_year' => 'required|integer|min:2000|max:2099']);
        $year = (int) $request->input('new_year');

        return redirect()
            ->route('administrasi.capex.index', ['year' => $year])
            ->with('success', "Periode tahun {$year} berhasil ditambahkan.");
    }

    /**
     * Export all CAPEX data for a year as CSV.
     */
    public function export(Request $request)
    {
        $year    = (int) $request->input('year', date('Y'));
        $records = Capex::where('tahun', $year)->orderBy('jenis_capex')->orderBy('jenis_catatan')->orderBy('capex_id')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"CAPEX_Report_{$year}.csv\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['capex_id', 'tahun', 'jenis_capex', 'jenis_catatan', 'nama_barang', 'type_merk', 'satuan', 'jumlah_unit', 'harga_satuan', 'total_rab', 'tanggal', 'nama_transaksi', 'debit', 'kredit']);
            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->capex_id, $r->tahun, $r->jenis_capex, $r->jenis_catatan,
                    $r->nama_barang, $r->type_merk, $r->satuan,
                    $r->jumlah_unit, $r->harga_satuan, $r->total_rab,
                    $r->tanggal?->format('Y-m-d'), $r->nama_transaksi,
                    $r->debit, $r->kredit,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
