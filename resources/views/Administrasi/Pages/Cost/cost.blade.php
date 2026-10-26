<x-admin-layout>

<x-slot name="title">Cost Management | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">Cost Management | FR Academy Enterprise Portal</x-slot>

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush



{{-- ============================== PAGE HEADER ============================== --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Cost Management</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Laporan Pengeluaran & Anggaran Bulanan dengan Agregasi Otomatis (VRA, Capex, Rekruit)</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @can('edit_cost')
        {{-- Import CSV Button --}}
        <button onclick="openImportModal()"
                class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs sm:text-sm font-medium rounded-lg hover:bg-[#F9FAFB] hover:border-[#D1D5DB] transition-all shadow-sm">
            <i data-lucide="upload" class="w-4 h-4 text-[#4B6A34]"></i>
            <span>Import CSV</span>
        </button>
        @endcan

        {{-- Download Template CSV --}}
        <a href="{{ route('administrasi.cost.template') }}"
           class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs sm:text-sm font-medium rounded-lg hover:bg-[#F9FAFB] hover:border-[#D1D5DB] transition-all shadow-sm"
           title="Unduh format template Excel Cost">
            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-blue-600"></i>
            <span>Template Excel</span>
        </a>

        {{-- Export Report --}}
        <a href="{{ route('administrasi.cost.export', ['year' => $selectedYear, 'month' => $selectedMonth, 'capex' => $selectedCapex]) }}"
           class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs sm:text-sm font-medium rounded-lg hover:bg-[#F9FAFB] hover:border-[#D1D5DB] transition-all shadow-sm">
            <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i>
            <span>Export Excel</span>
        </a>

        @can('edit_cost')
        {{-- Hapus Semua Data Button --}}
        <button onclick="openDeleteAllModal()"
                class="flex items-center gap-2 px-3.5 py-2 bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm font-semibold rounded-lg hover:bg-red-100 hover:border-red-300 transition-all shadow-sm"
                title="Hapus semua data Cost periode ini">
            <i data-lucide="trash" class="w-4 h-4 text-red-600"></i>
            <span>Hapus Semua</span>
        </button>

        {{-- Tambah Cost Button --}}
        <button onclick="openAddModal()"
                class="flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-[#3d5829] transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Cost</span>
        </button>
        @endcan
    </div>
</div>

{{-- ============================== CAPEX VIEW TOGGLE TABS (DENGAN CAPEX vs TANPA CAPEX) ============================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <div class="inline-flex p-1 bg-white border border-[#E5E7EB] rounded-2xl shadow-2xs">
        {{-- Tab 1: Dengan Capex --}}
        <a href="{{ route('administrasi.cost.index', ['year' => $selectedYear, 'month' => $selectedMonth, 'capex' => 'with']) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $selectedCapex !== 'without' ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-gray-50' }}"
           title="Menampilkan semua data pengeluaran termasuk alokasi CAPEX">
            <i data-lucide="layers" class="w-4 h-4"></i>
            <span>Dengan Capex</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $selectedCapex !== 'without' ? 'bg-white/20 text-white' : 'bg-gray-100 text-[#71717A]' }}">
                Termasuk CAPEX
            </span>
        </a>

        {{-- Tab 2: Tanpa Capex --}}
        <a href="{{ route('administrasi.cost.index', ['year' => $selectedYear, 'month' => $selectedMonth, 'capex' => 'without']) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $selectedCapex === 'without' ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-gray-50' }}"
           title="Menampilkan data pengeluaran operasional murni tanpa alokasi CAPEX">
            <i data-lucide="filter" class="w-4 h-4"></i>
            <span>Tanpa Capex</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $selectedCapex === 'without' ? 'bg-white/20 text-white' : 'bg-gray-100 text-[#71717A]' }}">
                Tanpa Biaya CAPEX
            </span>
        </a>
    </div>

    {{-- Info Status Badge --}}
    <div class="flex items-center gap-2">
        @if($selectedCapex === 'without')
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600"></i>
                <span>Mode Aktif: Biaya Operasional <strong>(Tanpa CAPEX)</strong></span>
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                <span>Mode Aktif: <strong>Semua Biaya Lengkap</strong> (OPEX + CAPEX)</span>
            </span>
        @endif
    </div>
</div>

{{-- ============================== UNIFIED FILTER & NAVIGATION CARD ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 mb-6 shadow-2xs space-y-3.5">
    {{-- Row 1: Year Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#F4F4F5]">
        <div class="flex items-center gap-2 flex-1 min-w-0">
            <div class="flex items-center gap-1.5 px-2 text-[#71717A] shrink-0">
                <i data-lucide="calendar" class="w-4 h-4 text-[#4B6A34]"></i>
                <span class="text-xs font-bold text-[#18181B] uppercase tracking-wider">Tahun:</span>
            </div>

            {{-- Scroll Left Button --}}
            <button type="button" onclick="scrollYearTabs('left')" class="p-1.5 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] hover:bg-gray-100 text-[#71717A] hover:text-[#18181B] transition-colors shrink-0" title="Scroll ke tahun sebelumnya">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>

            {{-- Horizontal Scrollable List --}}
            <div id="yearTabsContainer" class="flex items-center gap-1.5 overflow-x-auto py-1 px-1 scroll-smooth" style="scrollbar-width: thin;">
                @foreach ($years as $y)
                    <a href="{{ route('administrasi.cost.index', ['year' => $y, 'month' => $selectedMonth, 'capex' => $selectedCapex]) }}"
                       id="year-tab-{{ $y }}"
                       class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ (int)$selectedYear === (int)$y ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
                        {{ $y }}
                    </a>
                @endforeach
            </div>

            {{-- Scroll Right Button --}}
            <button type="button" onclick="scrollYearTabs('right')" class="p-1.5 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] hover:bg-gray-100 text-[#71717A] hover:text-[#18181B] transition-colors shrink-0" title="Scroll ke tahun berikutnya">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Tambah Tahun Button --}}
        @can('edit_cost')
        <div class="shrink-0 flex items-center gap-2">
            <button type="button" onclick="openTambahTahunModal()"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#4B6A34] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 flex items-center gap-1.5 transition-colors shadow-2xs">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Tambah Tahun</span>
            </button>
        </div>
        @endcan
    </div>

    {{-- Row 2: Month Navigation Tabs --}}
    <div class="flex items-center gap-1 overflow-x-auto pb-1 scrollbar-thin">
        <a href="{{ route('administrasi.cost.index', ['year' => $selectedYear, 'month' => 'all', 'capex' => $selectedCapex]) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all whitespace-nowrap
                  {{ $selectedMonth === 'all'
                      ? 'bg-[#4B6A34] text-white shadow-xs'
                      : 'bg-[#F9FAFB] text-[#71717A] hover:bg-[#F4F4F5] hover:text-[#18181B]' }}">
            🌟 Semua (Tahunan)
        </a>
        @foreach($monthNames as $num => $label)
        <a href="{{ route('administrasi.cost.index', ['year' => $selectedYear, 'month' => $num, 'capex' => $selectedCapex]) }}"
           class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all whitespace-nowrap
                  {{ (string)$num === (string)$selectedMonth
                      ? 'bg-[#4B6A34] text-white shadow-xs'
                      : 'bg-[#F9FAFB] text-[#71717A] hover:bg-[#F4F4F5] hover:text-[#18181B]' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- ============================== KPI SUMMARY CARDS ============================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card 1: Total Budget Tahunan --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#71717A]">Budget Tahunan</span>
            <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                <i data-lucide="calendar-range" class="w-4 h-4"></i>
            </span>
        </div>
        <p class="text-xl font-bold text-[#18181B]">Rp {{ number_format($summary['totalBudgetTahunan'], 0, ',', '.') }}</p>
        <p class="text-xs text-[#71717A] mt-1">Total alokasi anggaran 12 bulan</p>
    </div>

    {{-- Card 2: Total Realisasi (Aktual) --}}
    <div class="bg-[#F0F5EB] border-2 border-[#4B6A34] rounded-xl p-5 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#4B6A34]">Realisasi Aktual</span>
            <span class="p-1.5 bg-[#4B6A34] text-white rounded-lg">
                <i data-lucide="trending-up" class="w-4 h-4"></i>
            </span>
        </div>
        <p class="text-xl font-bold text-[#4B6A34]">Rp {{ number_format($summary['totalRealisasi'], 0, ',', '.') }}</p>
        <div class="flex items-center gap-1.5 mt-1">
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $summary['pctRealisasi'] > 100 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-800' }}">
                {{ number_format($summary['pctRealisasi'], 1) }}% Penyerapan
            </span>
            <span class="text-[11px] text-[#71717A]">dari budget</span>
        </div>
    </div>

    {{-- Card 3: Budget Periode Terpilih --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#71717A]">Budget Periode</span>
            <span class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                <i data-lucide="wallet" class="w-4 h-4"></i>
            </span>
        </div>
        <p class="text-xl font-bold text-[#18181B]">Rp {{ number_format($summary['totalBudget'], 0, ',', '.') }}</p>
        <p class="text-xs text-[#71717A] mt-1">{{ $selectedMonth === 'all' ? 'Budget Seluruh Periode' : 'Budget Bulan ' . ($monthNames[$selectedMonth] ?? $selectedMonth) }}</p>
    </div>

    {{-- Card 4: Sisa Budget (Variance) --}}
    @php
        $isDeficit = $summary['sisaBudget'] < 0;
    @endphp
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#71717A]">Sisa Anggaran / Variance</span>
            <span class="p-1.5 {{ $isDeficit ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }} rounded-lg">
                <i data-lucide="{{ $isDeficit ? 'alert-triangle' : 'shield-check' }}" class="w-4 h-4"></i>
            </span>
        </div>
        <p class="text-xl font-bold {{ $isDeficit ? 'text-red-600' : 'text-emerald-700' }}">
            Rp {{ number_format(abs($summary['sisaBudget']), 0, ',', '.') }}
        </p>
        <p class="text-xs {{ $isDeficit ? 'text-red-500 font-medium' : 'text-[#71717A]' }} mt-1">
            {{ $isDeficit ? '⚠️ Overbudget / Defisit' : '✓ Anggaran aman tersisa' }}
            <span class="block text-[11px] text-[#71717A] mt-0.5 font-normal">
                (Budget Thn - Realisasi {{ $selectedMonth === 'all' ? 'Tahunan' : 'Jan s/d ' . ($monthNames[$selectedMonth] ?? $selectedMonth) }})
            </span>
        </p>
    </div>
</div>

{{-- ============================== FLASH ALERTS ============================== --}}
@if(session('success'))
<div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl shadow-sm" id="flash-msg">
    <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600 flex-shrink-0"></i>
    <span class="font-medium">{{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto text-green-600 hover:text-green-800 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

@if(session('error'))
<div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl shadow-sm" id="flash-err">
    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
    <span class="font-medium">{{ session('error') }}</span>
    <button onclick="document.getElementById('flash-err').remove()" class="ml-auto text-red-600 hover:text-red-800 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

{{-- ============================== VISUAL ANALYTICS & CHARTS ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 mb-6 shadow-2xs transition-all" id="visualAnalyticsCard">
    {{-- Card Header with Toggle --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#F4F4F5]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#4B6A34]/10 text-[#4B6A34] flex items-center justify-center shrink-0">
                <i data-lucide="line-chart" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="font-bold text-base text-[#18181B] tracking-tight">Visualisasi & Analisis Pengeluaran</h3>
                    <span class="text-[11px] font-semibold text-[#4B6A34] bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                        Tahun {{ $selectedYear }} {{ $selectedMonth === 'all' ? '(Tahunan)' : '- ' . ($monthNames[$selectedMonth] ?? $selectedMonth) }}
                    </span>
                    <span class="text-[11px] font-semibold {{ $selectedCapex === 'without' ? 'text-amber-800 bg-amber-50 border-amber-200' : 'text-emerald-800 bg-emerald-50 border-emerald-200' }} border px-2 py-0.5 rounded-full">
                        {{ $selectedCapex === 'without' ? 'Tanpa CAPEX' : 'Dengan CAPEX' }}
                    </span>
                </div>
                <p class="text-xs text-[#71717A] mt-0.5">Komparasi tren penyerapan anggaran bulanan dan distribusi pengeluaran per kategori</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="toggleAnalyticsBody()" id="toggleAnalyticsBtn"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] hover:bg-gray-100 text-[#71717A] hover:text-[#18181B] text-xs font-semibold transition-colors">
                <i data-lucide="chevron-up" id="toggleAnalyticsIcon" class="w-4 h-4 transition-transform duration-200"></i>
                <span id="toggleAnalyticsText">Sembunyikan Grafik</span>
            </button>
        </div>
    </div>

    {{-- Analytics Body --}}
    <div id="analyticsBody" class="pt-4">
        {{-- 2 Columns Chart Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            {{-- Column 1: Tren Bulanan (Budget vs Realisasi) - 7 cols --}}
            <div class="lg:col-span-7 bg-[#FAFBF9] border border-[#E5E7EB] rounded-xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-[#E5E7EB]">
                    <div>
                        <h4 class="text-xs font-bold text-[#18181B] uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="bar-chart-3" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                            <span>Tren Penyerapan: Budget vs Realisasi (Jan - Des {{ $selectedYear }})</span>
                        </h4>
                        <p class="text-[11px] text-[#71717A] mt-0.5">Perbandingan alokasi anggaran bulanan dengan pengeluaran aktual</p>
                    </div>
                </div>

                <div class="h-64 sm:h-72 w-full relative">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4 text-xs pt-3 mt-1 border-t border-[#E5E7EB] text-[#71717A]">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#CBD5E1]"></span> Budget Anggaran
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#4B6A34]"></span> Realisasi Aktual (Aman)
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-xs bg-[#EF4444]"></span> Realisasi (Overbudget)
                    </span>
                </div>
            </div>

            {{-- Column 2: Proporsi Pengeluaran per Kategori - 5 cols --}}
            <div class="lg:col-span-5 bg-[#FAFBF9] border border-[#E5E7EB] rounded-xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-[#E5E7EB]">
                    <div>
                        <h4 class="text-xs font-bold text-[#18181B] uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="pie-chart" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                            <span>Distribusi per Kategori Biaya</span>
                        </h4>
                        <p class="text-[11px] text-[#71717A] mt-0.5">
                            {{ $selectedMonth === 'all' ? 'Akumulasi seluruh tahun ' . $selectedYear : 'Periode bulan ' . ($monthNames[$selectedMonth] ?? $selectedMonth) }}
                        </p>
                    </div>
                </div>

                @if(count($chartAnalytics['categoryValues']) > 0)
                <div class="h-52 sm:h-56 w-full relative flex items-center justify-center my-1">
                    <canvas id="categoryDonutChart"></canvas>
                </div>

                {{-- Legend list with percentage --}}
                <div class="space-y-1.5 pt-2 border-t border-[#E5E7EB] max-h-36 overflow-y-auto text-xs pr-1">
                    @php
                        $badgeBgColors = ['bg-[#4B6A34]', 'bg-[#2563EB]', 'bg-[#D97706]', 'bg-[#7C3AED]', 'bg-[#DB2777]', 'bg-[#64748B]'];
                    @endphp
                    @foreach($chartAnalytics['categoryBreakdown'] as $i => $cat)
                    <div class="flex items-center justify-between py-0.5 text-xs">
                        <div class="flex items-center gap-2 truncate pr-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $badgeBgColors[$i % count($badgeBgColors)] }}"></span>
                            <span class="text-[#374151] font-medium truncate" title="{{ $cat['name'] }}">{{ $cat['name'] }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-bold text-[#18181B]">Rp {{ number_format($cat['amount'], 0, ',', '.') }}</span>
                            <span class="text-[#71717A] text-[10px] ml-1">({{ $cat['percent'] }}%)</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="h-64 flex flex-col items-center justify-center text-[#71717A] text-xs">
                    <i data-lucide="inbox" class="w-8 h-8 text-gray-300 mb-2"></i>
                    <span>Belum ada realisasi pengeluaran pada periode ini.</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================== COST DATA TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-[#E5E7EB] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-[#FAFBF9]">
        <div>
            <h3 class="font-bold text-base text-[#18181B] flex items-center gap-2">
                <span>Daftar Total Cost & Realisasi</span>
                <span class="text-xs font-normal text-[#71717A]">
                    ({{ count($grouped) }} Kategori, Periode: {{ $selectedMonth === 'all' ? 'Semua Bulan' : ($monthNames[$selectedMonth] ?? $selectedMonth) }} {{ $selectedYear }})
                </span>
                @if($selectedCapex === 'without')
                    <span class="text-[11px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                        Tanpa Biaya CAPEX
                    </span>
                @endif
            </h3>
        </div>
        <div class="flex items-center gap-2 text-xs text-[#71717A]">
            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Auto-Sync VRA / Capex / Rekruit</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-[#F4F4F5] border-b border-[#E5E7EB] text-[#52525B]">
                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider w-12 text-center">No</th>
                    <th class="px-3 py-3 text-xs font-bold uppercase tracking-wider w-36 max-w-[140px]">GL Account</th>
                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider">Account Name</th>
                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-right">Budget (Rp)</th>
                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-right">Realisasi (Rp)</th>
                    @if($selectedMonth === 'all')
                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-right">Budget Tahunan (Rp)</th>
                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-center">% Realisasi</th>
                    @endif
                    @can('edit_cost')
                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-center w-24">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F4F4F5]">

                @php $globalRowIndex = 1; @endphp

                @forelse($grouped as $kategoriName => $items)

                {{-- ================= CATEGORY HEADER ================= --}}
                <tr class="bg-[#F9FAFB] border-t-2 border-[#E5E7EB]">
                    <td colspan="{{ $selectedMonth === 'all' ? (auth()->user()?->can('edit_cost') ? 8 : 7) : (auth()->user()?->can('edit_cost') ? 6 : 5) }}" class="px-4 py-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-sm bg-[#4B6A34]"></span>
                                <span class="text-xs font-bold text-[#18181B] uppercase tracking-wider">{{ $kategoriName }}</span>
                                <span class="text-[11px] font-semibold text-[#71717A] bg-white border border-[#E5E7EB] px-2 py-0.5 rounded-full">
                                    {{ count($items) }} item
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>

                {{-- ================= CATEGORY ITEMS ================= --}}
                @php
                    $subtotalBudget   = 0;
                    $subtotalReal     = 0;
                    $subtotalTahunan  = 0;
                @endphp

                @foreach($items as $row)
                @php
                    $subtotalBudget  += (float) $row->budget;
                    $subtotalReal    += (float) $row->realisasi;
                    $subtotalTahunan += (float) $row->budget_tahunan;

                    $baseTarget = $row->budget_tahunan > 0 ? $row->budget_tahunan : ($row->budget > 0 ? $row->budget : 0);
                    $pct = $baseTarget > 0 ? round(($row->realisasi / $baseTarget) * 100, 1) : 0;
                    $isOver = $pct > 100;

                    $isAutoSync = str_contains(strtoupper($row->account_name), 'ALOKASI') ||
                                  str_contains(strtoupper($row->account_name), 'CAPEX') ||
                                  strtoupper($row->account_name) === 'REKRUT';
                @endphp
                <tr class="hover:bg-[#FAFBF9] transition-colors">
                    {{-- No --}}
                    <td class="px-4 py-3 text-center text-xs text-[#71717A] font-medium">
                        {{ $globalRowIndex++ }}
                    </td>

                    {{-- GL Account --}}
                    <td class="px-3 py-3 font-mono text-xs text-[#52525B] max-w-[140px]" title="{{ $row->GL_ACCOUNT }}">
                        @if($row->GL_ACCOUNT)
                            <span class="inline-block max-w-full truncate bg-[#F4F4F5] px-2 py-0.5 rounded text-[10px] font-semibold text-[#374151] border border-[#E5E7EB]">
                                {{ $row->GL_ACCOUNT }}
                            </span>
                        @else
                            <span class="text-[#A1A1AA] italic text-xs">-</span>
                        @endif
                    </td>

                    {{-- Account Name --}}
                    <td class="px-4 py-3 text-xs font-semibold text-[#18181B]">
                        <div class="flex items-center gap-1.5">
                            <span>{{ $row->account_name }}</span>
                            @if($isAutoSync)
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-emerald-100 text-emerald-700 tracking-wider uppercase" title="Diagregasi otomatis dari modul VRA/Capex/Rekruit">
                                    Auto
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Budget --}}
                    <td class="px-4 py-3 text-right text-xs font-mono font-medium text-[#52525B]">
                        {{ $row->budget > 0 ? number_format($row->budget, 0, ',', '.') : '-' }}
                    </td>

                    {{-- Realisasi --}}
                    <td class="px-4 py-3 text-right text-xs font-mono font-bold text-[#18181B]">
                        {{ $row->realisasi > 0 ? number_format($row->realisasi, 0, ',', '.') : '-' }}
                    </td>

                    @if($selectedMonth === 'all')
                    {{-- Budget Tahunan --}}
                    <td class="px-4 py-3 text-right text-xs font-mono font-medium text-[#52525B]">
                        {{ $row->budget_tahunan > 0 ? number_format($row->budget_tahunan, 0, ',', '.') : '-' }}
                    </td>

                    {{-- % Realisasi --}}
                    <td class="px-4 py-3 text-center">
                        @if($baseTarget > 0)
                            <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-bold {{ $isOver ? 'bg-red-100 text-red-700' : 'bg-[#EBF3E6] text-[#4B6A34]' }}">
                                {{ number_format($pct, 1) }}%
                            </span>
                        @else
                            <span class="text-[#A1A1AA] text-xs">-</span>
                        @endif
                    </td>
                    @endif

                    @can('edit_cost')
                    {{-- Aksi --}}
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openEditModal({{ $row->cost_id }})"
                                    class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Edit Cost">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            </button>
                            <button onclick="openDeleteModal({{ $row->cost_id }}, '{{ addslashes($row->account_name) }}')"
                                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Hapus Cost">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </td>
                    @endcan
                </tr>
                @endforeach

                {{-- ================= SUBTOTAL ROW ================= --}}
                @php
                    $subTarget = $subtotalTahunan > 0 ? $subtotalTahunan : ($subtotalBudget > 0 ? $subtotalBudget : 0);
                    $subPct = $subTarget > 0 ? round(($subtotalReal / $subTarget) * 100, 1) : 0;
                    $subOver = $subPct > 100;
                @endphp
                <tr class="bg-[#F4F4F5] border-t border-[#E5E7EB] font-bold text-xs">
                    <td colspan="3" class="px-4 py-2.5 text-right text-[#374151] uppercase tracking-wide">
                        Subtotal {{ $kategoriName }}
                    </td>
                    <td class="px-4 py-2.5 text-right font-mono text-[#18181B]">
                        {{ number_format($subtotalBudget, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-right font-mono text-[#4B6A34]">
                        {{ number_format($subtotalReal, 0, ',', '.') }}
                    </td>
                    @if($selectedMonth === 'all')
                    <td class="px-4 py-2.5 text-right font-mono text-[#18181B]">
                        {{ number_format($subtotalTahunan, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-center {{ $subOver ? 'text-red-600' : 'text-[#4B6A34]' }}">
                        {{ $subTarget > 0 ? number_format($subPct, 1) . '%' : '-' }}
                    </td>
                    @endif
                    <td></td>
                </tr>

                @empty
                <tr>
                    <td colspan="{{ $selectedMonth === 'all' ? 8 : 6 }}" class="px-5 py-16 text-center text-[#A1A1AA]">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-semibold text-[#374151]">Belum ada data Cost untuk periode ini</p>
                        <p class="text-xs text-[#71717A] mt-1">Gunakan tombol "Import CSV" atau "+ Tambah Cost" untuk mulai mengisi data.</p>
                    </td>
                </tr>
                @endforelse

            </tbody>

            {{-- ================= GRAND TOTAL FOOTER ================= --}}
            @if(count($grouped) > 0)
            <tfoot>
                <tr class="bg-[#4B6A34] text-white font-bold text-sm">
                    <td colspan="3" class="px-4 py-3.5 uppercase tracking-wider text-right">
                        TOTAL COST & ANGGARAN
                    </td>
                    <td class="px-4 py-3.5 text-right font-mono">
                        Rp {{ number_format($summary['totalBudget'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3.5 text-right font-mono text-emerald-200">
                        Rp {{ number_format($summary['totalRealisasi'], 0, ',', '.') }}
                    </td>
                    @if($selectedMonth === 'all')
                    <td class="px-4 py-3.5 text-right font-mono">
                        Rp {{ number_format($summary['totalBudgetTahunan'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="bg-white/20 px-2 py-0.5 rounded text-xs">
                            {{ number_format($summary['pctRealisasi'], 1) }}%
                        </span>
                    </td>
                    @endif
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ================================================================
     MODALS SECTION
================================================================ --}}

@can('edit_cost')
{{-- ---------------- MODAL: IMPORT CSV ---------------- --}}
<div id="modalImport" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeImportModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#FAFBF9]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#4B6A34]/10 flex items-center justify-center text-[#4B6A34]">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#18181B]">Import Data Cost dari CSV</h2>
                    <p class="text-xs text-[#71717A]">Unggah file CSV dengan data anggaran dan realisasi</p>
                </div>
            </div>
            <button onclick="closeImportModal()" class="p-1.5 hover:bg-[#F4F4F5] rounded-lg text-[#71717A]"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form method="POST" action="{{ route('administrasi.cost.import') }}" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">
            <input type="hidden" name="current_month" value="{{ $selectedMonth }}">

            {{-- Target Bulan Import --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Target Bulan</label>
                <select name="bulan"
                        class="w-full px-3.5 py-2 border border-[#E5E7EB] rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                    <option value="">— Sesuai Kolom CSV (Otomatis) —</option>
                    @foreach($monthNames as $num => $lbl)
                        <option value="{{ $num }}" {{ (string)$num === (string)$selectedMonth ? 'selected' : '' }}>
                            Bulan {{ $lbl }} (Bulan {{ $num }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-[#71717A] mt-1">Pilih bulan spesifik jika file CSV tidak memuat kolom bulan atau ingin diarahkan ke bulan tertentu.</p>
            </div>

            {{-- Import Mode --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Metode Import <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-start gap-2.5 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:bg-[#F9FAFB] transition-all has-[:checked]:border-[#4B6A34] has-[:checked]:bg-[#F0F5EB]">
                        <input type="radio" name="import_mode" value="append" checked class="mt-0.5 text-[#4B6A34] focus:ring-[#4B6A34]">
                        <div>
                            <p class="text-xs font-bold text-[#18181B]">Tambahkan / Update</p>
                            <p class="text-[11px] text-[#71717A]">Upsert baris tanpa menghapus data lain</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-2.5 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:bg-[#F9FAFB] transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                        <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-red-600 focus:ring-red-500">
                        <div>
                            <p class="text-xs font-bold text-red-700">Timpa Semua Data</p>
                            <p class="text-[11px] text-red-600">Hapus data tahun ini lalu masukkan dari CSV</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- File Input / Dropzone --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Pilih File CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-[#D1D5DB] rounded-xl p-5 text-center hover:border-[#4B6A34] transition-colors bg-[#FAFBF9]">
                    <i data-lucide="file-text" class="w-8 h-8 text-[#71717A] mx-auto mb-2"></i>
                    <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required
                           class="block w-full text-xs text-[#71717A] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4B6A34] file:text-white hover:file:bg-[#3d5829] cursor-pointer">
                    <p class="text-[11px] text-[#A1A1AA] mt-2">Maksimal ukuran file: 10MB (format .csv)</p>
                </div>
            </div>

            {{-- Info Format Kolom --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex items-start gap-3">
                <i data-lucide="info" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <div class="text-[11px] text-blue-800 space-y-1">
                    <p class="font-semibold">Format Header CSV yang Didukung:</p>
                    <p class="font-mono text-[10px] bg-blue-100/70 p-1 rounded">cost_id, GL_ACCOUNT, jenis_cost, account_name, bulan, budget, realisasi, budget_tahunan</p>
                    <a href="{{ route('administrasi.cost.template') }}" class="inline-flex items-center gap-1 font-bold text-blue-700 hover:underline pt-1">
                        <i data-lucide="download" class="w-3 h-3"></i> Unduh File Template CSV
                    </a>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeImportModal()"
                        class="px-4 py-2 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm flex items-center gap-1.5">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                    <span>Mulai Import Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ---------------- MODAL: ADD COST ---------------- --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Tambah Data Cost</h2>
            <button onclick="closeAddModal()" class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form method="POST" action="{{ route('administrasi.cost.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">GL Account (Kode Akun)</label>
                    <input type="text" name="GL_ACCOUNT" placeholder="Contoh: 135.80.00001/850.91.01000"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Bulan (Periode)</label>
                    <select name="bulan"
                            class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                        <option value="">— Berlaku Semua Bulan —</option>
                        @foreach($monthNames as $num => $lbl)
                            <option value="{{ $num }}" {{ (string)$num === (string)$selectedMonth ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis Cost / Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="jenis_cost" list="jenisListAdd" required placeholder="Contoh: BIAYA KARYAWAN"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none uppercase">
                    <datalist id="jenisListAdd">
                        @foreach($jenisList ?? [] as $j)
                            <option value="{{ $j }}">
                        @endforeach
                        <option value="BIAYA KARYAWAN">
                        <option value="BIAYA PEMELIHARAAN">
                        <option value="BIAYA ADMINISTRASI">
                        <option value="BIAYA PERJALANAN DINAS">
                        <option value="CAPEX">
                        <option value="REKRUT">
                    </datalist>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Account Name <span class="text-red-500">*</span></label>
                    <input type="text" name="account_name" required placeholder="Contoh: STAFF, LISTRIK, dsb."
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Budget (Rp)</label>
                    <input type="number" name="budget" min="0" step="0.01" placeholder="0"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Realisasi (Rp)</label>
                    <input type="number" name="realisasi" min="0" step="0.01" placeholder="0"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Budget Tahunan (Rp)</label>
                    <input type="number" name="budget_tahunan" min="0" step="0.01" placeholder="0"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---------------- MODAL: EDIT COST ---------------- --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Edit Data Cost</h2>
            <button onclick="closeEditModal()" class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form id="formEdit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="tahun" id="edit_tahun" value="{{ $selectedYear }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">GL Account (Kode Akun)</label>
                    <input type="text" name="GL_ACCOUNT" id="edit_GL_ACCOUNT"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Bulan (Periode)</label>
                    <select name="bulan" id="edit_bulan"
                            class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                        <option value="">— Berlaku Semua Bulan —</option>
                        @foreach($monthNames as $num => $lbl)
                            <option value="{{ $num }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis Cost / Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="jenis_cost" id="edit_jenis_cost" list="jenisListAdd" required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none uppercase">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Account Name <span class="text-red-500">*</span></label>
                    <input type="text" name="account_name" id="edit_account_name" required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Budget (Rp)</label>
                    <input type="number" name="budget" id="edit_budget" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Realisasi (Rp)</label>
                    <input type="number" name="realisasi" id="edit_realisasi" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Budget Tahunan (Rp)</label>
                    <input type="number" name="budget_tahunan" id="edit_budget_tahunan" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---------------- MODAL: DELETE COST (SINGLE) ---------------- --}}
<div id="modalDelete" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="px-6 py-6 text-center">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="trash-2" class="w-7 h-7 text-red-500"></i>
            </div>
            <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Data Cost</h3>
            <p class="text-xs text-[#71717A] mb-2">Apakah Anda yakin ingin menghapus account cost ini?</p>
            <p class="text-sm font-bold text-[#18181B] bg-red-50 py-2 px-3 rounded-lg border border-red-100 mb-5" id="delete_name_label">—</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <form id="formDelete" method="POST" action="" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ---------------- MODAL: DELETE ALL COSTS ---------------- --}}
<div id="modalDeleteAll" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteAllModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-6 text-center">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
                <i data-lucide="alert-octagon" class="w-7 h-7"></i>
            </div>
            <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Semua Data Cost?</h3>
            <p class="text-xs text-[#71717A] mb-4">
                Tindakan ini akan menghapus seluruh data cost pada periode terpilih:
                <strong class="text-red-600">{{ $selectedMonth === 'all' ? 'Seluruh Tahun ' . $selectedYear : 'Bulan ' . ($monthNames[$selectedMonth] ?? $selectedMonth) . ' ' . $selectedYear }}</strong>.
            </p>
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-[11px] text-red-800 text-left mb-5">
                <p class="font-bold flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i> Perhatian:</p>
                <p class="mt-0.5">Data yang telah dihapus tidak dapat dikembalikan kecuali Anda melakukan import ulang atau menekan tombol <strong>Sync Sumber Data</strong>.</p>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteAllModal()"
                        class="flex-1 px-4 py-2.5 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <form method="POST" action="{{ route('administrasi.cost.destroyAll', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                        Ya, Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
{{-- Modal Tambah Tahun --}}
<div id="modalTambahTahun" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-[#E5E7EB] animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <i data-lucide="calendar-plus" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <h3 class="font-bold text-[#18181B] text-base">Tambah Tahun Baru</h3>
            </div>
            <button type="button" onclick="closeTambahTahunModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form onsubmit="handleTambahTahun(event)" class="space-y-4">
            <div>
                <label for="inputTahunBaru" class="block text-xs font-semibold text-[#374151] mb-1.5">Masukkan Tahun</label>
                <input type="number" id="inputTahunBaru" min="2000" max="2100" value="{{ max($years) + 1 }}" required
                       class="w-full px-3.5 py-2.5 text-sm bg-gray-50 border border-[#E5E7EB] rounded-xl text-[#18181B] font-semibold focus:bg-white focus:border-[#4B6A34] focus:ring-2 focus:ring-[#4B6A34]/20 outline-none transition-all"
                       placeholder="Contoh: 2027">
                <p class="text-[11px] text-[#71717A] mt-1">Tahun ini akan ditambahkan ke filter dan dapat langsung dipilih.</p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="button" onclick="closeTambahTahunModal()"
                        class="flex-1 py-2.5 px-4 rounded-xl border border-[#E5E7EB] text-xs font-semibold text-[#374151] hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-[#4B6A34] hover:bg-[#3d5829] text-xs font-semibold text-white transition-colors shadow-xs">
                    Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openImportModal() {
    document.getElementById('modalImport').classList.remove('hidden');
}
function closeImportModal() {
    document.getElementById('modalImport').classList.add('hidden');
}

function openAddModal() {
    document.getElementById('modalAdd').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('modalAdd').classList.add('hidden');
}

function openEditModal(id) {
    fetch(`/administrasi/cost/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;
            document.getElementById('formEdit').action = `/administrasi/cost/${id}`;
            document.getElementById('edit_tahun').value          = d.tahun || '{{ $selectedYear }}';
            document.getElementById('edit_bulan').value          = d.bulan || '';
            document.getElementById('edit_GL_ACCOUNT').value     = d.GL_ACCOUNT || '';
            document.getElementById('edit_jenis_cost').value     = d.jenis_cost || '';
            document.getElementById('edit_account_name').value   = d.account_name || '';
            document.getElementById('edit_budget').value         = d.budget || 0;
            document.getElementById('edit_realisasi').value      = d.realisasi || 0;
            document.getElementById('edit_budget_tahunan').value = d.budget_tahunan || 0;
            document.getElementById('modalEdit').classList.remove('hidden');
        });
}
function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function openDeleteModal(id, name) {
    document.getElementById('delete_name_label').textContent = name;
    document.getElementById('formDelete').action = `/administrasi/cost/${id}`;
    document.getElementById('modalDelete').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('modalDelete').classList.add('hidden');
}

function openDeleteAllModal() {
    document.getElementById('modalDeleteAll').classList.remove('hidden');
}
function closeDeleteAllModal() {
    document.getElementById('modalDeleteAll').classList.add('hidden');
}

function scrollYearTabs(direction) {
    const container = document.getElementById('yearTabsContainer');
    if (!container) return;
    const scrollAmount = 240;
    container.scrollBy({ left: direction === 'left' ? -scrollAmount : scrollAmount, behavior: 'smooth' });
}

function openTambahTahunModal() {
    const modal = document.getElementById('modalTambahTahun');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('inputTahunBaru')?.focus(), 100);
    }
}

function closeTambahTahunModal() {
    const modal = document.getElementById('modalTambahTahun');
    if (modal) modal.classList.add('hidden');
}

function handleTambahTahun(e) {
    e.preventDefault();
    const year = document.getElementById('inputTahunBaru')?.value;
    if (!year) return;
    const url = new URL(window.location.href);
    url.searchParams.set('year', year);
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function () {
    const activeTab = document.getElementById('year-tab-{{ $selectedYear }}');
    if (activeTab) {
        activeTab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
});

function toggleAnalyticsBody() {
    const body = document.getElementById('analyticsBody');
    const icon = document.getElementById('toggleAnalyticsIcon');
    const text = document.getElementById('toggleAnalyticsText');
    if (!body) return;
    const isHidden = body.classList.toggle('hidden');
    if (isHidden) {
        if (icon) icon.classList.add('rotate-180');
        if (text) text.textContent = 'Tampilkan Grafik';
        localStorage.setItem('cost_analytics_hidden', 'true');
    } else {
        if (icon) icon.classList.remove('rotate-180');
        if (text) text.textContent = 'Sembunyikan Grafik';
        localStorage.setItem('cost_analytics_hidden', 'false');
    }
}

// Format Currency Helpers
function formatRupiahShort(value) {
    if (value >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
    return 'Rp ' + (value || 0).toLocaleString('id-ID');
}

function formatRupiahFull(value) {
    return 'Rp ' + Math.round(value || 0).toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function () {
    // Restore collapsed state
    if (localStorage.getItem('cost_analytics_hidden') === 'true') {
        const body = document.getElementById('analyticsBody');
        const icon = document.getElementById('toggleAnalyticsIcon');
        const text = document.getElementById('toggleAnalyticsText');
        if (body && icon && text) {
            body.classList.add('hidden');
            icon.classList.add('rotate-180');
            text.textContent = 'Tampilkan Grafik';
        }
    }

    // Chart 1: Monthly Trend Bar Chart
    const trendCanvas = document.getElementById('monthlyTrendChart');
    if (trendCanvas && typeof Chart !== 'undefined') {
        const monthlyLabels = @json($chartAnalytics['monthlyLabels']);
        const monthlyBudgets = @json($chartAnalytics['monthlyBudgets']);
        const monthlyRealisasis = @json($chartAnalytics['monthlyRealisasis']);

        // Dynamic color: green if <= budget, red if overbudget
        const realisasiColors = monthlyRealisasis.map((r, i) => {
            const b = monthlyBudgets[i] || 0;
            return (b > 0 && r > b) ? '#EF4444' : '#4B6A34';
        });

        new Chart(trendCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        label: 'Budget Anggaran',
                        data: monthlyBudgets,
                        backgroundColor: '#CBD5E1',
                        hoverBackgroundColor: '#94A3B8',
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8,
                    },
                    {
                        label: 'Realisasi Aktual',
                        data: monthlyRealisasis,
                        backgroundColor: realisasiColors,
                        hoverBackgroundColor: realisasiColors.map(c => c === '#EF4444' ? '#DC2626' : '#3d5829'),
                        borderRadius: 6,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8,
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
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#18181B',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + formatRupiahFull(context.parsed.y);
                            },
                            afterBody: function (contexts) {
                                if (contexts.length >= 2) {
                                    const b = contexts[0].parsed.y;
                                    const r = contexts[1].parsed.y;
                                    const diff = b - r;
                                    if (b > 0) {
                                        const pct = ((r / b) * 100).toFixed(1);
                                        const status = diff >= 0
                                            ? `✓ Sisa: ${formatRupiahFull(diff)} (${pct}% serap)`
                                            : `⚠️ Overbudget: ${formatRupiahFull(Math.abs(diff))} (${pct}% serap)`;
                                        return '\n' + status;
                                    }
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#71717A'
                        }
                    },
                    y: {
                        grid: {
                            color: '#F4F4F5',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 10 },
                            color: '#A1A1AA',
                            callback: function (val) {
                                return formatRupiahShort(val);
                            }
                        }
                    }
                }
            }
        });
    }

    // Chart 2: Category Donut Chart
    const donutCanvas = document.getElementById('categoryDonutChart');
    if (donutCanvas && typeof Chart !== 'undefined') {
        const catLabels = @json($chartAnalytics['categoryLabels']);
        const catValues = @json($chartAnalytics['categoryValues']);
        const palette = ['#4B6A34', '#2563EB', '#D97706', '#7C3AED', '#DB2777', '#64748B'];

        if (catValues.length > 0) {
            new Chart(donutCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catValues,
                        backgroundColor: palette.slice(0, catValues.length),
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#18181B',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = context.parsed;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${formatRupiahFull(val)} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
</x-admin-layout>
