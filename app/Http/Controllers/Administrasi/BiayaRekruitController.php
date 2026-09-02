<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\BiayaRekruit;
use Illuminate\Http\Request;

class BiayaRekruitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Dynamic Year List
        $dbYears   = BiayaRekruit::select('tahun')->distinct()->pluck('tahun')->toArray();
        $baseYears = range(2010, date('Y') + 1);
        $years     = array_unique(array_merge($baseYears, $dbYears));
        sort($years);

        // 2. Selected Year & Search
        $selectedYear = (int) $request->get('year', date('Y'));
        $search       = trim($request->get('search', ''));

        // 3. Query
        $query = BiayaRekruit::where('tahun', $selectedYear)
            ->orderBy('jenis_rekruit')
            ->orderBy('penanggung_jawab')
            ->orderBy('tanggal');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jenis_rekruit', 'like', "%{$search}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            });
        }

        $allRecords = $query->get();

        // 4. Group: jenis_rekruit → penanggung_jawab → items
        $grouped = [];
        foreach ($allRecords as $row) {
            $jenis = $row->jenis_rekruit ?? 'Lainnya';
            $pj    = $row->penanggung_jawab ?? 'Tanpa Penanggung Jawab';
            $grouped[$jenis][$pj][] = $row;
        }

        // 5. Summary
        $summary = [
            'total_biaya'    => (float) $allRecords->sum('total_biaya'),
            'total_transaksi' => $allRecords->count(),
        ];

        return view('Administrasi.Pages.biaya_rekruit', compact(
            'years', 'selectedYear', 'grouped', 'summary', 'search'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'total_biaya' => 'required|numeric|min:0',
            'tahun' => 'required|integer',
            'jenis_rekruit' => 'nullable|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        $record = BiayaRekruit::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Biaya rekruit berhasil ditambahkan.',
                'data' => $record
            ]);
        }

        return redirect()->route('administrasi.biaya-rekruit.index', [
            'year' => $validated['tahun']
        ])->with('success', 'Biaya rekruit "' . $validated['nama'] . '" berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $record = BiayaRekruit::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $record
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $record = BiayaRekruit::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'total_biaya' => 'required|numeric|min:0',
            'jenis_rekruit' => 'nullable|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        $record->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Biaya rekruit berhasil diperbarui.',
                'data' => $record
            ]);
        }

        return redirect()->route('administrasi.biaya-rekruit.index', [
            'year' => $record->tahun
        ])->with('success', 'Biaya rekruit "' . $record->nama . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $record = BiayaRekruit::findOrFail($id);
        $year = $record->tahun;
        $name = $record->nama;

        $record->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Biaya rekruit '{$name}' berhasil dihapus."
            ]);
        }

        return redirect()->route('administrasi.biaya-rekruit.index', [
            'year' => $year
        ])->with('success', "Biaya rekruit '{$name}' berhasil dihapus.");
    }

    /**
     * Add a new reporting year.
     */
    public function storeYear(Request $request)
    {
        $request->validate([
            'new_year' => 'required|integer|min:2000|max:2099'
        ]);

        $newYear = (int) $request->new_year;

        return redirect()->route('administrasi.biaya-rekruit.index', [
            'year' => $newYear
        ])->with('success', "Tahun {$newYear} berhasil dipilih sebagai periode aktif.");
    }

    /**
     * Export recruitment expenses report.
     */
    public function export(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $filename = "Biaya_Rekruit_Report_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($year) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Tanggal',
                'Nama / Referensi',
                'Total Biaya (IDR)',
                'Jenis Rekruit',
                'Penanggung Jawab',
                'Tahun'
            ]);

            $records = BiayaRekruit::where('tahun', $year)
                ->orderBy('tanggal', 'desc')
                ->get();

            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->tanggal->format('d/m/Y'),
                    $row->nama,
                    $row->total_biaya,
                    $row->jenis_rekruit ?? '-',
                    $row->penanggung_jawab ?? '-',
                    $row->tahun
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
