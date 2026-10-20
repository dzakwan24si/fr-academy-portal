<x-admin-layout>

<x-slot name="title">Training & Monitoring Report | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">Training & Monitoring Report | FR Academy Enterprise Portal</x-slot>

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .fr-header-bg {
        background: linear-gradient(135deg, #1e3a17 0%, #2d5a27 50%, #3f7435 100%);
    }
    .fr-table-header {
        background-color: #2d5a27;
        color: #ffffff;
    }
    .fr-table-subheader {
        background-color: #3b6e34;
        color: #f1f5f9;
    }
    .fr-table-total {
        background-color: #f0fdf4;
        font-weight: 700;
        border-top: 2px solid #2d5a27;
    }
</style>
@endpush


<div class="space-y-6 pb-12">

    {{-- ============================== TOP EXECUTIVE HEADER ============================== --}}
    <div class="fr-header-bg rounded-2xl p-6 text-white shadow-lg border border-emerald-800/40">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 tracking-wider">
                        FIRST RESOURCES LEARNING CENTER (FRLC)
                    </span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black tracking-tight text-white drop-shadow-sm">
                    EXECUTIVE TRAINING & MONITORING REPORT
                </h1>
                <p class="text-emerald-100/90 text-sm mt-1">
                    Dashboard Pencapaian Batch, Peserta, Monitoring Trainee & Jalur Karir Lulusan
                </p>
            </div>

            {{-- Year & Month Filter Selector & Fullscreen --}}
            <div class="flex flex-wrap items-center gap-3 self-start md:self-auto bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4 text-emerald-200"></i>
                        <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Tahun:</span>
                        <select name="year" onchange="this.form.submit()" class="bg-emerald-900/80 text-white text-xs font-bold rounded-lg px-3 py-1.5 border border-emerald-500/50 focus:outline-hidden focus:ring-2 focus:ring-emerald-400 cursor-pointer">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ (int)($year ?? 2026) === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <i data-lucide="filter" class="w-3.5 h-3.5 text-emerald-200"></i>
                        <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Bulan:</span>
                        <select name="month" onchange="this.form.submit()" class="bg-emerald-900/80 text-white text-xs font-bold rounded-lg px-3 py-1.5 border border-emerald-500/50 focus:outline-hidden focus:ring-2 focus:ring-emerald-400 cursor-pointer">
                            <option value="all" {{ ($rawMonth ?? 'all') === 'all' ? 'selected' : '' }}>Semua Bulan (s/d Des)</option>
                            @foreach($monthList as $mNum => $mCode)
                                <option value="{{ $mNum }}" {{ (int)($selectedMonthNum ?? 0) === (int)$mNum ? 'selected' : '' }}>{{ $mCode }} (Kumulatif s/d {{ $mCode }})</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="w-px h-5 bg-white/20 hidden sm:block"></div>

                <button type="button" onclick="toggleDashboardFullscreen()" class="btn-toggle-fullscreen flex items-center gap-1.5 bg-emerald-700/80 hover:bg-emerald-600/90 text-white text-xs font-bold rounded-lg px-3 py-1.5 border border-emerald-400/40 transition-colors cursor-pointer">
                    <i data-lucide="maximize-2" class="w-3.5 h-3.5 fullscreen-btn-icon"></i>
                    <span class="fullscreen-btn-text">Full Screen</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================================= --}}
    {{-- SECTION 1: REKAP TRAINING REPORT (Reguler, Softskill, Technical)                          --}}
    {{-- ========================================================================================= --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <div class="w-3 h-6 bg-[#2d5a27] rounded-full"></div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">1. REKAP TRAINING REPORT</h2>
                    <p class="text-xs text-gray-500">Pencapaian Program Reguler, Softskill, dan Technical/Refreshment</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full">
                Tahun {{ $year ?? 2026 }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            {{-- Summary Table --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-xs text-center border-collapse">
                    <thead>
                        <tr class="fr-table-header uppercase text-[11px] font-bold">
                            <th rowspan="2" class="p-3 text-left border-r border-emerald-700/60 min-w-[200px]">PROGRAM</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#24491f]">PLAN</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#1e3c1a]">REALISASI</th>
                            <th colspan="2" class="p-2 bg-[#2d5a27]">%</th>
                        </tr>
                        <tr class="fr-table-subheader uppercase text-[10px] font-semibold border-t border-emerald-700/40">
                            <th class="p-2 border-r border-emerald-700/40 w-24">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-28">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-24">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-28">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-20">BATCH</th>
                            <th class="p-2 w-20">PESERTA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800 font-medium">
                        @php
                            $sumPlanB = 0; $sumPlanP = 0; $sumRealB = 0; $sumRealP = 0;
                        @endphp
                        @foreach(['reguler' => 'REGULER', 'softskill' => 'SOFTSKILL', 'technical' => 'TECHNICAL/REFRESHMENT'] as $key => $title)
                            @php
                                $r = $rekapTraining['rows'][$key] ?? ['name' => $title, 'plan_batch' => 0, 'plan_participant' => 0, 'realisasi_batch' => 0, 'realisasi_participant' => 0, 'pct_batch' => 0, 'pct_participant' => 0];
                                $sumPlanB += $r['plan_batch'];
                                $sumPlanP += $r['plan_participant'];
                                $sumRealB += $r['realisasi_batch'];
                                $sumRealP += $r['realisasi_participant'];
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-3 text-left font-bold text-gray-900 border-r border-gray-200">{{ $r['name'] }}</td>
                                <td class="p-2.5 border-r border-gray-200">{{ number_format($r['plan_batch']) }}</td>
                                <td class="p-2.5 border-r border-gray-200 font-semibold">{{ number_format($r['plan_participant']) }}</td>
                                <td class="p-2.5 border-r border-gray-200">{{ number_format($r['realisasi_batch']) }}</td>
                                <td class="p-2.5 border-r border-gray-200 font-semibold">{{ number_format($r['realisasi_participant']) }}</td>
                                <td class="p-2.5 border-r border-gray-200">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $r['pct_batch'] >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $r['pct_batch'] }}%
                                    </span>
                                </td>
                                <td class="p-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $r['pct_participant'] >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $r['pct_participant'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @php
                            $totPctB = $sumPlanB > 0 ? round(($sumRealB / $sumPlanB) * 100) : 0;
                            $totPctP = $sumPlanP > 0 ? round(($sumRealP / $sumPlanP) * 100) : 0;
                        @endphp
                        <tr class="fr-table-total text-gray-900">
                            <td class="p-3 text-left font-extrabold border-r border-gray-300">TOTAL</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($sumPlanB) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($sumPlanP) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($sumRealB) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($sumRealP) }}</td>
                            <td class="p-2.5 border-r border-gray-300 font-bold text-emerald-800">{{ $totPctB }}%</td>
                            <td class="p-2.5 font-bold text-emerald-800">{{ $totPctP }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Dual Combo Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Chart Batch --}}
                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center justify-between">
                        <span>Pencapaian Batch (Plan vs Realisasi)</span>
                        <span class="text-[10px] text-gray-500 font-normal">Combo Bar + % Line</span>
                    </h3>
                    <div class="h-64">
                        <canvas id="chartRekapBatch"></canvas>
                    </div>
                </div>

                {{-- Chart Peserta --}}
                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center justify-between">
                        <span>Pencapaian Peserta (Plan vs Realisasi)</span>
                        <span class="text-[10px] text-gray-500 font-normal">Combo Bar + % Line</span>
                    </h3>
                    <div class="h-64">
                        <canvas id="chartRekapPeserta"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================================= --}}
    {{-- SECTION 2 & 3: MONITORING TRAINEE UNDER 2 YEARS & KARIR LULUSAN FRLC                      --}}
    {{-- ========================================================================================= --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- SECTION 2: MONITORING TRAINEE UNDER 2 YEARS --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden flex flex-col">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-6 bg-emerald-600 rounded-full"></div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">2. MONITORING TRAINEE UNDER 2 YEARS</h2>
                        <p class="text-xs text-gray-500">Status kelulusan & retensi trainee (2 tahun terakhir)</p>
                    </div>
                </div>
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between space-y-6">
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-xs text-center border-collapse">
                        <thead>
                            <tr class="fr-table-header uppercase text-[10px] font-bold">
                                <th class="p-2.5 text-left border-r border-emerald-700/60">PROGRAM</th>
                                <th class="p-2 border-r border-emerald-700/60">TRAINEE</th>
                                <th class="p-2 border-r border-emerald-700/60">CULLED (%)</th>
                                <th class="p-2 border-r border-emerald-700/60">PASSED (%)</th>
                                <th class="p-2 border-r border-emerald-700/60">RESIGN (%)</th>
                                <th class="p-2">EXISTING (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            @foreach($traineeUnder2Years['rows'] as $prog => $t)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-2.5 text-left font-bold text-gray-900 border-r border-gray-200">{{ $t['program'] }}</td>
                                    <td class="p-2 border-r border-gray-200 font-semibold">{{ $t['trainee'] }}</td>
                                    <td class="p-2 border-r border-gray-200">{{ $t['culled'] }} <span class="text-rose-600 font-bold text-[10px]">({{ $t['pct_culled'] }}%)</span></td>
                                    <td class="p-2 border-r border-gray-200">{{ $t['passed'] }} <span class="text-blue-600 font-bold text-[10px]">({{ $t['pct_passed'] }}%)</span></td>
                                    <td class="p-2 border-r border-gray-200">{{ $t['resign'] }} <span class="text-amber-600 font-bold text-[10px]">({{ $t['pct_resign'] }}%)</span></td>
                                    <td class="p-2 font-bold text-emerald-700">{{ $t['existing'] }} <span class="text-[10px]">({{ $t['pct_existing'] }}%)</span></td>
                                </tr>
                            @endforeach
                            @php $totTrainee = $traineeUnder2Years['total']; @endphp
                            <tr class="fr-table-total text-gray-900">
                                <td class="p-2.5 text-left font-extrabold border-r border-gray-300">TOTAL</td>
                                <td class="p-2 border-r border-gray-300">{{ $totTrainee['trainee'] }}</td>
                                <td class="p-2 border-r border-gray-300">{{ $totTrainee['culled'] }} <span class="text-rose-700 font-bold text-[10px]">({{ $totTrainee['pct_culled'] }}%)</span></td>
                                <td class="p-2 border-r border-gray-300">{{ $totTrainee['passed'] }} <span class="text-blue-700 font-bold text-[10px]">({{ $totTrainee['pct_passed'] }}%)</span></td>
                                <td class="p-2 border-r border-gray-300">{{ $totTrainee['resign'] }} <span class="text-amber-700 font-bold text-[10px]">({{ $totTrainee['pct_resign'] }}%)</span></td>
                                <td class="p-2 font-extrabold text-emerald-800">{{ $totTrainee['existing'] }} <span class="text-[10px]">({{ $totTrainee['pct_existing'] }}%)</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Grouped Bar Chart --}}
                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Persentase Evaluasi Trainee (%)</h3>
                    <div class="h-60">
                        <canvas id="chartTraineeUnder2Years"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: MONITORING KARIR LULUSAN FRLC --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden flex flex-col">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-6 bg-teal-600 rounded-full"></div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">3. MONITORING KARIR LULUSAN FRLC</h2>
                        <p class="text-xs text-gray-500">Distribusi jenjang jabatan saat ini per program trainee</p>
                    </div>
                </div>
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between space-y-6">
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-xs text-center border-collapse">
                        <thead>
                            <tr class="fr-table-header uppercase text-[10px] font-bold">
                                <th class="p-2.5 text-left border-r border-emerald-700/60">PROGRAM</th>
                                <th class="p-2 border-r border-emerald-700/60">PC</th>
                                <th class="p-2 border-r border-emerald-700/60">GM</th>
                                <th class="p-2 border-r border-emerald-700/60">MK / MM</th>
                                <th class="p-2 border-r border-emerald-700/60">ASKEP / KTU</th>
                                <th class="p-2 border-r border-emerald-700/60">ASST</th>
                                <th class="p-2">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            @foreach($karirLulusan['rows'] as $prog => $k)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-2.5 text-left font-bold text-gray-900 border-r border-gray-200">{{ $k['program'] }}</td>
                                    <td class="p-2 border-r border-gray-200">{{ $k['pc'] }} <span class="text-gray-500 text-[10px]">({{ $k['pct_pc'] }}%)</span></td>
                                    <td class="p-2 border-r border-gray-200">{{ $k['gm'] }} <span class="text-gray-500 text-[10px]">({{ $k['pct_gm'] }}%)</span></td>
                                    <td class="p-2 border-r border-gray-200">{{ $k['mk_mm'] }} <span class="text-gray-500 text-[10px]">({{ $k['pct_mk_mm'] }}%)</span></td>
                                    <td class="p-2 border-r border-gray-200">{{ $k['askep_ktu'] }} <span class="text-gray-500 text-[10px]">({{ $k['pct_askep_ktu'] }}%)</span></td>
                                    <td class="p-2 border-r border-gray-200 font-semibold">{{ $k['asst'] }} <span class="text-gray-500 text-[10px]">({{ $k['pct_asst'] }}%)</span></td>
                                    <td class="p-2 font-bold text-gray-900 bg-gray-50/50">{{ $k['total'] }}</td>
                                </tr>
                            @endforeach
                            @php $totKarir = $karirLulusan['total']; @endphp
                            <tr class="fr-table-total text-gray-900">
                                <td class="p-2.5 text-left font-extrabold border-r border-gray-300">TOTAL</td>
                                <td class="p-2 border-r border-gray-300">{{ $totKarir['pc'] }} <span class="text-[10px] text-emerald-800">({{ $totKarir['pct_pc'] }}%)</span></td>
                                <td class="p-2 border-r border-gray-300">{{ $totKarir['gm'] }} <span class="text-[10px] text-emerald-800">({{ $totKarir['pct_gm'] }}%)</span></td>
                                <td class="p-2 border-r border-gray-300">{{ $totKarir['mk_mm'] }} <span class="text-[10px] text-emerald-800">({{ $totKarir['pct_mk_mm'] }}%)</span></td>
                                <td class="p-2 border-r border-gray-300">{{ $totKarir['askep_ktu'] }} <span class="text-[10px] text-emerald-800">({{ $totKarir['pct_askep_ktu'] }}%)</span></td>
                                <td class="p-2 border-r border-gray-300 font-bold">{{ $totKarir['asst'] }} <span class="text-[10px] text-emerald-800">({{ $totKarir['pct_asst'] }}%)</span></td>
                                <td class="p-2 font-extrabold text-emerald-900 bg-emerald-100/50">{{ $totKarir['total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Career Summary Metric Badges --}}
                <div class="grid grid-cols-5 gap-2 pt-2">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-2.5 text-center">
                        <span class="text-[10px] uppercase font-bold text-indigo-700">PC / Controller</span>
                        <p class="text-sm font-black text-indigo-900 mt-0.5">{{ $totKarir['pc'] }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-2.5 text-center">
                        <span class="text-[10px] uppercase font-bold text-blue-700">GM</span>
                        <p class="text-sm font-black text-blue-900 mt-0.5">{{ $totKarir['gm'] }}</p>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-2.5 text-center">
                        <span class="text-[10px] uppercase font-bold text-emerald-700">MK / MM</span>
                        <p class="text-sm font-black text-emerald-900 mt-0.5">{{ $totKarir['mk_mm'] }}</p>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-2.5 text-center">
                        <span class="text-[10px] uppercase font-bold text-amber-700">Askep / KTU</span>
                        <p class="text-sm font-black text-amber-900 mt-0.5">{{ $totKarir['askep_ktu'] }}</p>
                    </div>
                    <div class="bg-teal-50 border border-teal-200 rounded-xl p-2.5 text-center">
                        <span class="text-[10px] uppercase font-bold text-teal-700">Asisten</span>
                        <p class="text-sm font-black text-teal-900 mt-0.5">{{ $totKarir['asst'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================================= --}}
    {{-- SECTION 4: STAFF FRESH GRADUATE (ASISTEN TRAINEE)                                         --}}
    {{-- ========================================================================================= --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-6 bg-[#2d5a27] rounded-full"></div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">4. STAFF FRESH GRADUATE (ASISTEN TRAINEE)</h2>
                    <p class="text-xs text-gray-500">Pencapaian Program FAT, MAT, DMT, TAT, dan AAT</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full">
                Tahun {{ $year ?? 2026 }}
            </span>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            {{-- Table (7 cols) --}}
            <div class="lg:col-span-7 overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-xs text-center border-collapse">
                    <thead>
                        <tr class="fr-table-header uppercase text-[11px] font-bold">
                            <th rowspan="2" class="p-3 text-left border-r border-emerald-700/60 min-w-[140px]">PROGRAM</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#24491f]">PLAN</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#1e3c1a]">REALISASI</th>
                            <th colspan="2" class="p-2 bg-[#2d5a27]">%</th>
                        </tr>
                        <tr class="fr-table-subheader uppercase text-[10px] font-semibold border-t border-emerald-700/40">
                            <th class="p-2 border-r border-emerald-700/40 w-20">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-24">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-20">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-24">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-16">BATCH</th>
                            <th class="p-2 w-16">PESERTA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800 font-medium">
                        @foreach($staffFreshGraduate['rows'] as $prog => $s)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-2.5 text-left font-bold text-gray-900 border-r border-gray-200">{{ $s['name'] }}</td>
                                <td class="p-2 border-r border-gray-200">{{ number_format($s['plan_batch']) }}</td>
                                <td class="p-2 border-r border-gray-200 font-semibold">{{ number_format($s['plan_participant']) }}</td>
                                <td class="p-2 border-r border-gray-200">{{ number_format($s['realisasi_batch']) }}</td>
                                <td class="p-2 border-r border-gray-200 font-semibold">{{ number_format($s['realisasi_participant']) }}</td>
                                <td class="p-2 border-r border-gray-200">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $s['pct_batch'] >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $s['pct_batch'] }}%
                                    </span>
                                </td>
                                <td class="p-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $s['pct_participant'] >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $s['pct_participant'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @php $totFresh = $staffFreshGraduate['total']; @endphp
                        <tr class="fr-table-total text-gray-900">
                            <td class="p-3 text-left font-extrabold border-r border-gray-300">{{ $totFresh['name'] }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totFresh['plan_batch']) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totFresh['plan_participant']) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totFresh['realisasi_batch']) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totFresh['realisasi_participant']) }}</td>
                            <td class="p-2.5 border-r border-gray-300 font-bold text-emerald-800">{{ $totFresh['pct_batch'] }}%</td>
                            <td class="p-2.5 font-bold text-emerald-800">{{ $totFresh['pct_participant'] }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Combo Chart (5 cols) --}}
            <div class="lg:col-span-5 bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center justify-between">
                    <span>Peserta Fresh Graduate (Plan vs Realisasi)</span>
                    <span class="text-[10px] text-gray-500 font-normal">Combo Bar + % Line</span>
                </h3>
                <div class="h-64">
                    <canvas id="chartFreshGraduate"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================================= --}}
    {{-- SECTION 5: PROGRAM SOFTSKILL (REGIONAL BREAKDOWN)                                         --}}
    {{-- ========================================================================================= --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-6 bg-[#2d5a27] rounded-full"></div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">5. PROGRAM SOFTSKILL</h2>
                    <p class="text-xs text-gray-500">Pencapaian Softskill Berdasarkan Wilayah Operasional (Riau, Kalbar, Kubar/Kaltim)</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full">
                Tahun {{ $year ?? 2026 }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            {{-- Table --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-xs text-center border-collapse">
                    <thead>
                        <tr class="fr-table-header uppercase text-[11px] font-bold">
                            <th rowspan="2" class="p-3 text-left border-r border-emerald-700/60 min-w-[160px]">PROGRAM / WILAYAH</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#24491f]">PLAN</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#1e3c1a]">REALISASI</th>
                            <th colspan="2" class="p-2 bg-[#2d5a27]">%</th>
                        </tr>
                        <tr class="fr-table-subheader uppercase text-[10px] font-semibold border-t border-emerald-700/40">
                            <th class="p-2 border-r border-emerald-700/40 w-24">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-28">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-24">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-28">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-20">BATCH</th>
                            <th class="p-2 w-20">PESERTA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800 font-medium">
                        @foreach($softskillRegional['rows'] as $reg => $s)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-2.5 text-left font-bold text-gray-900 border-r border-gray-200">{{ $s['region'] }}</td>
                                <td class="p-2 border-r border-gray-200">{{ number_format($s['plan_batch']) }}</td>
                                <td class="p-2 border-r border-gray-200 font-semibold">{{ number_format($s['plan_participant']) }}</td>
                                <td class="p-2 border-r border-gray-200">{{ number_format($s['realisasi_batch']) }}</td>
                                <td class="p-2 border-r border-gray-200 font-semibold">{{ number_format($s['realisasi_participant']) }}</td>
                                <td class="p-2 border-r border-gray-200">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $s['pct_batch'] >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $s['pct_batch'] }}%
                                    </span>
                                </td>
                                <td class="p-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $s['pct_participant'] >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $s['pct_participant'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @php $totSoft = $softskillRegional['total']; @endphp
                        <tr class="fr-table-total text-gray-900">
                            <td class="p-3 text-left font-extrabold border-r border-gray-300">{{ $totSoft['region'] }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totSoft['plan_batch']) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totSoft['plan_participant']) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totSoft['realisasi_batch']) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($totSoft['realisasi_participant']) }}</td>
                            <td class="p-2.5 border-r border-gray-300 font-bold text-emerald-800">{{ $totSoft['pct_batch'] }}%</td>
                            <td class="p-2.5 font-bold text-emerald-800">{{ $totSoft['pct_participant'] }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Dual Combo Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center justify-between">
                        <span>Batch Softskill per Wilayah</span>
                        <span class="text-[10px] text-gray-500 font-normal">Combo Bar + % Line</span>
                    </h3>
                    <div class="h-64">
                        <canvas id="chartSoftskillBatch"></canvas>
                    </div>
                </div>

                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center justify-between">
                        <span>Peserta Softskill per Wilayah</span>
                        <span class="text-[10px] text-gray-500 font-normal">Combo Bar + % Line</span>
                    </h3>
                    <div class="h-64">
                        <canvas id="chartSoftskillPeserta"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================================= --}}
    {{-- SECTION 6: PROGRAM NON-REGULER TEKNIKAL (CATEGORY & REGIONAL BREAKDOWN)                   --}}
    {{-- ========================================================================================= --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-6 bg-[#2d5a27] rounded-full"></div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">6. PROGRAM NON-REGULER TEKNIKAL</h2>
                    <p class="text-xs text-gray-500">Breakdown Kategori (Estate, Mill, Traction, Downstream, Administrasi) & Regional</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full">
                Tahun {{ $year ?? 2026 }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            {{-- Hierarchical Table --}}
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="w-full text-xs text-center border-collapse">
                    <thead>
                        <tr class="fr-table-header uppercase text-[11px] font-bold">
                            <th rowspan="2" class="p-3 text-left border-r border-emerald-700/60 min-w-[220px]">KATEGORI & WILAYAH</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#24491f]">PLAN</th>
                            <th colspan="2" class="p-2 border-r border-emerald-700/60 bg-[#1e3c1a]">REALISASI</th>
                            <th colspan="2" class="p-2 bg-[#2d5a27]">%</th>
                        </tr>
                        <tr class="fr-table-subheader uppercase text-[10px] font-semibold border-t border-emerald-700/40">
                            <th class="p-2 border-r border-emerald-700/40 w-24">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-28">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-24">BATCH</th>
                            <th class="p-2 border-r border-emerald-700/40 w-28">PESERTA</th>
                            <th class="p-2 border-r border-emerald-700/40 w-20">BATCH</th>
                            <th class="p-2 w-20">PESERTA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-800 font-medium">
                        @php
                            $grandPlanB = 0; $grandPlanP = 0; $grandRealB = 0; $grandRealP = 0;
                        @endphp
                        @foreach($teknikalBreakdown['groups'] as $catKey => $group)
                            @php
                                $tot = $group['total'];
                                $grandPlanB += $tot['plan_batch'];
                                $grandPlanP += $tot['plan_participant'];
                                $grandRealB += $tot['realisasi_batch'];
                                $grandRealP += $tot['realisasi_participant'];
                            @endphp
                            {{-- Group Category Header Row --}}
                            <tr class="bg-gray-100/90 font-extrabold text-gray-900 border-t-2 border-gray-300">
                                <td class="p-2.5 text-left border-r border-gray-300 uppercase tracking-wide flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#2d5a27]"></span>
                                    <span>{{ $tot['name'] }}</span>
                                </td>
                                <td class="p-2 border-r border-gray-300">{{ number_format($tot['plan_batch']) }}</td>
                                <td class="p-2 border-r border-gray-300">{{ number_format($tot['plan_participant']) }}</td>
                                <td class="p-2 border-r border-gray-300">{{ number_format($tot['realisasi_batch']) }}</td>
                                <td class="p-2 border-r border-gray-300">{{ number_format($tot['realisasi_participant']) }}</td>
                                <td class="p-2 border-r border-gray-300 text-emerald-800 font-bold">{{ $tot['pct_batch'] }}%</td>
                                <td class="p-2 text-emerald-800 font-bold">{{ $tot['pct_participant'] }}%</td>
                            </tr>

                            {{-- Item Breakdown Rows --}}
                            @foreach($group['items'] as $item)
                                <tr class="hover:bg-gray-50/80 transition-colors text-gray-700">
                                    <td class="p-2 pl-7 text-left border-r border-gray-200 font-normal">
                                        <span class="text-gray-400 mr-1.5">•</span> {{ $item['name'] }}
                                    </td>
                                    <td class="p-2 border-r border-gray-200">{{ number_format($item['plan_batch']) }}</td>
                                    <td class="p-2 border-r border-gray-200">{{ number_format($item['plan_participant']) }}</td>
                                    <td class="p-2 border-r border-gray-200">{{ number_format($item['realisasi_batch']) }}</td>
                                    <td class="p-2 border-r border-gray-200">{{ number_format($item['realisasi_participant']) }}</td>
                                    <td class="p-2 border-r border-gray-200">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-sm text-[11px] font-bold {{ $item['pct_batch'] >= 100 ? 'text-emerald-700' : 'text-gray-600' }}">
                                            {{ $item['pct_batch'] }}%
                                        </span>
                                    </td>
                                    <td class="p-2">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-sm text-[11px] font-bold {{ $item['pct_participant'] >= 100 ? 'text-emerald-700' : 'text-gray-600' }}">
                                            {{ $item['pct_participant'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                        {{-- GRAND TOTAL TEKNIKAL --}}
                        @php
                            $grandPctB = $grandPlanB > 0 ? round(($grandRealB / $grandPlanB) * 100) : 0;
                            $grandPctP = $grandPlanP > 0 ? round(($grandRealP / $grandPlanP) * 100) : 0;
                        @endphp
                        <tr class="fr-table-total text-gray-900 border-t-2 border-emerald-900">
                            <td class="p-3 text-left font-extrabold border-r border-gray-300 uppercase tracking-wide">
                                TOTAL TECHNICAL / REFRESHMENT
                            </td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($grandPlanB) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($grandPlanP) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($grandRealB) }}</td>
                            <td class="p-2.5 border-r border-gray-300">{{ number_format($grandRealP) }}</td>
                            <td class="p-2.5 border-r border-gray-300 font-black text-emerald-900">{{ $grandPctB }}%</td>
                            <td class="p-2.5 font-black text-emerald-900">{{ $grandPctP }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Dual Combo Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center justify-between">
                        <span>Peserta Non-Reguler Teknikal per Kategori</span>
                        <span class="text-[10px] text-gray-500 font-normal">Combo Bar + % Line</span>
                    </h3>
                    <div class="h-64">
                        <canvas id="chartTeknikalPeserta"></canvas>
                    </div>
                </div>

                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 flex items-center justify-between">
                        <span>Batch Non-Reguler Teknikal per Kategori</span>
                        <span class="text-[10px] text-gray-500 font-normal">Combo Bar + % Line</span>
                    </h3>
                    <div class="h-64">
                        <canvas id="chartTeknikalBatch"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart Defaults
    Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#64748b';

    // Helper for Combo Chart creation
    function createComboChart(canvasId, labels, planData, realData, pctData, valueLabel = 'Jumlah') {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Plan',
                        data: planData,
                        backgroundColor: '#2563eb', // Royal Blue
                        borderColor: '#1d4ed8',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'Realisasi',
                        data: realData,
                        backgroundColor: '#e11d48', // Red / Crimson
                        borderColor: '#be123c',
                        borderWidth: 1,
                        borderRadius: 4,
                        barPercentage: 0.7,
                        categoryPercentage: 0.7,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: '% Pencapaian',
                        data: pctData,
                        borderColor: '#16a34a', // Vivid Green
                        backgroundColor: '#16a34a',
                        pointBackgroundColor: '#16a34a',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2.5,
                        tension: 0.2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 12,
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.dataset.yAxisID === 'y1') {
                                    return label + context.parsed.y + '%';
                                }
                                return label + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        title: {
                            display: true,
                            text: valueLabel,
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        },
                        title: {
                            display: true,
                            text: 'Pencapaian (%)',
                            font: { size: 10, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }

    // 1. REKAP TRAINING CHARTS
    const rekapBatchData = @json($rekapTraining['chart_batch']);
    createComboChart('chartRekapBatch', rekapBatchData.categories, rekapBatchData.plan, rekapBatchData.realisasi, rekapBatchData.pct, 'Batch');

    const rekapPesertaData = @json($rekapTraining['chart_participant']);
    createComboChart('chartRekapPeserta', rekapPesertaData.categories, rekapPesertaData.plan, rekapPesertaData.realisasi, rekapPesertaData.pct, 'Peserta');

    // 2. MONITORING TRAINEE UNDER 2 YEARS CHART
    const traineeChartData = @json($traineeUnder2Years['chart']);
    const ctxTrainee = document.getElementById('chartTraineeUnder2Years');
    if (ctxTrainee) {
        new Chart(ctxTrainee, {
            type: 'bar',
            data: {
                labels: traineeChartData.categories,
                datasets: [
                    {
                        label: 'Culled (%)',
                        data: traineeChartData.culled,
                        backgroundColor: '#f59e0b', // Amber
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                    },
                    {
                        label: 'Resign (%)',
                        data: traineeChartData.resign,
                        backgroundColor: '#ef4444', // Merah
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 10,
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    }

    // 4. FRESH GRADUATE CHART
    const freshData = @json($staffFreshGraduate['chart']);
    createComboChart('chartFreshGraduate', freshData.categories, freshData.plan, freshData.realisasi, freshData.pct, 'Peserta');

    // 5. SOFTSKILL REGIONAL CHARTS
    const softData = @json($softskillRegional['chart']);
    createComboChart('chartSoftskillBatch', softData.categories, softData.plan_batch, softData.realisasi_batch, softData.pct_batch, 'Batch');
    createComboChart('chartSoftskillPeserta', softData.categories, softData.plan_participant, softData.realisasi_participant, softData.pct_participant, 'Peserta');

    // 6. TEKNIKAL BREAKDOWN CHARTS
    const tekPesertaData = @json($teknikalBreakdown['chart_participant']);
    createComboChart('chartTeknikalPeserta', tekPesertaData.categories, tekPesertaData.plan, tekPesertaData.realisasi, tekPesertaData.pct, 'Peserta');

    const tekBatchData = @json($teknikalBreakdown['chart_batch']);
    createComboChart('chartTeknikalBatch', tekBatchData.categories, tekBatchData.plan, tekBatchData.realisasi, tekBatchData.pct, 'Batch');
});
</script>
@endpush
</x-admin-layout>
