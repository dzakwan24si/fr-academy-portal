<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Cost;
use Illuminate\Http\Request;

class CostController extends Controller
{
    private const MONTH_NAMES = [
        1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
        4  => 'April',     5  => 'Mei',       6  => 'Juni',
        7  => 'Juli',      8  => 'Agustus',   9  => 'September',
        10 => 'Oktober',   11 => 'November',  12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $selectedYear  = (int) $request->input('year',  date('Y'));
        $selectedMonth = (int) $request->input('month', date('n'));

        // Dynamic year list
        $dbYears = Cost::selectRaw('DISTINCT tahun')->orderBy('tahun')->pluck('tahun')->toArray();
        $years   = array_unique(array_merge(range(date('Y') - 3, date('Y') + 2), $dbYears));
        sort($years);

        // ------------------------------------------------------------------
        // Core query: aggregate per (kategori, nama_item) for selected year
        // ------------------------------------------------------------------
        $accounts = Cost::where('tahun', $selectedYear)
            ->selectRaw("
                MIN(cost_id) as cost_id,
                kategori,
                nama_item,
                SUM(CASE WHEN bulan = ? THEN realisasi ELSE 0 END) as bulan_ini,
                SUM(CASE WHEN bulan <= ? THEN realisasi ELSE 0 END) as ytd_realisasi,
                SUM(CASE WHEN bulan <= ? THEN budget    ELSE 0 END) as ytd_budget,
                SUM(budget)    as annual_budget,
                SUM(realisasi) as annual_realisasi
            ", [$selectedMonth, $selectedMonth, $selectedMonth])
            ->groupBy('kategori', 'nama_item')
            ->orderBy('kategori')
            ->orderBy('nama_item')
            ->get();

        // Group by kategori for the view
        $grouped = [];
        foreach ($accounts as $row) {
            $grouped[$row->kategori][] = $row;
        }

        // ------------------------------------------------------------------
        // Summary totals
        // ------------------------------------------------------------------
        $budgetTotal    = (float) Cost::where('tahun', $selectedYear)->where('bulan', $selectedMonth)->sum('budget');
        $ytdRealisasi   = (float) Cost::where('tahun', $selectedYear)->where('bulan', '<=', $selectedMonth)->sum('realisasi');
        $ytdBudget      = (float) Cost::where('tahun', $selectedYear)->where('bulan', '<=', $selectedMonth)->sum('budget');
        $annualBudget   = (float) Cost::where('tahun', $selectedYear)->sum('budget');

        $summary = compact('budgetTotal', 'ytdRealisasi', 'ytdBudget', 'annualBudget');

        return view('Administrasi.Pages.cost', compact(
            'years', 'selectedYear', 'selectedMonth',
            'grouped', 'summary'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'        => 'required|integer',
            'bulan'        => 'required|integer|min:1|max:12',
            'kategori'     => 'required|string|max:100',
            'sub_kategori' => 'nullable|string|max:100',
            'nama_item'    => 'required|string|max:255',
            'keterangan'   => 'nullable|string|max:500',
            'budget'       => 'nullable|numeric|min:0',
            'realisasi'    => 'nullable|numeric|min:0',
            'tanggal'      => 'nullable|date',
            'no_bukti'     => 'nullable|string|max:100',
        ]);

        Cost::create($validated);

        return redirect()
            ->route('administrasi.cost.index', ['year' => $validated['tahun'], 'month' => $validated['bulan']])
            ->with('success', 'Data Cost berhasil ditambahkan.');
    }

    public function show($id)
    {
        $record = Cost::findOrFail($id);
        return response()->json(['success' => true, 'data' => $record]);
    }

    public function update(Request $request, $id)
    {
        $record    = Cost::findOrFail($id);
        $validated = $request->validate([
            'kategori'     => 'required|string|max:100',
            'sub_kategori' => 'nullable|string|max:100',
            'nama_item'    => 'required|string|max:255',
            'keterangan'   => 'nullable|string|max:500',
            'budget'       => 'nullable|numeric|min:0',
            'realisasi'    => 'nullable|numeric|min:0',
            'tanggal'      => 'nullable|date',
            'no_bukti'     => 'nullable|string|max:100',
        ]);

        $record->update($validated);

        return redirect()
            ->route('administrasi.cost.index', ['year' => $record->tahun, 'month' => $record->bulan])
            ->with('success', 'Data Cost berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $record = Cost::findOrFail($id);
        [$tahun, $bulan] = [$record->tahun, $record->bulan];
        $record->delete();

        return redirect()
            ->route('administrasi.cost.index', ['year' => $tahun, 'month' => $bulan])
            ->with('success', 'Data Cost berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $year  = (int) $request->input('year',  date('Y'));
        $month = (int) $request->input('month', date('n'));

        $records = Cost::where('tahun', $year)
            ->where('bulan', $month)
            ->orderBy('kategori')
            ->get();

        $monthName = self::MONTH_NAMES[$month] ?? $month;
        $headers   = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"Cost_{$monthName}_{$year}.csv\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['cost_id','tahun','bulan','kategori','sub_kategori','nama_item','keterangan','budget','realisasi','tanggal','no_bukti']);
            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->cost_id, $r->tahun, $r->bulan, $r->kategori, $r->sub_kategori,
                    $r->nama_item, $r->keterangan, $r->budget, $r->realisasi,
                    $r->tanggal?->format('Y-m-d'), $r->no_bukti,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
