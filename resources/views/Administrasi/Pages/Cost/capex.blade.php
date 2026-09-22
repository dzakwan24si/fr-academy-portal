<x-admin-layout>

<x-slot name="title">CAPEX | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">CAPEX | FR Academy Enterprise Portal</x-slot>


{{-- ============================== PAGE HEADER ============================== --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">CAPEX</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Capital Expenditure Management & Realization Tracking</p>
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

        {{-- Download Template Dropdown --}}
        <div class="relative inline-block text-left" id="templateDropdownContainer">
            <button onclick="toggleTemplateMenu(event)"
                    class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs sm:text-sm font-medium rounded-lg hover:bg-[#F9FAFB] hover:border-[#D1D5DB] transition-all shadow-sm">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-blue-600"></i>
                <span>Template Excel</span>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#71717A]"></i>
            </button>
            <div id="templateMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border border-[#E5E7EB] rounded-xl shadow-lg z-30 py-1 divide-y divide-gray-100">
                <div class="py-1">
                    <a href="{{ route('administrasi.capex.template', ['type' => 'rab']) }}"
                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-[#374151] hover:bg-[#F9FAFB] hover:text-[#4B6A34]">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-500"></i>
                        <div>
                            <p class="font-semibold">Template RAB</p>
                            <p class="text-[10px] text-[#A1A1AA]">Kolom: rab_id, lokasi, qty, harga...</p>
                        </div>
                    </a>
                    <a href="{{ route('administrasi.capex.template', ['type' => 'realisasi']) }}"
                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-[#374151] hover:bg-[#F9FAFB] hover:text-[#4B6A34]">
                        <i data-lucide="receipt" class="w-4 h-4 text-amber-500"></i>
                        <div>
                            <p class="font-semibold">Template Realisasi</p>
                            <p class="text-[10px] text-[#A1A1AA]">Kolom: realisasi_id, debit, kredit...</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Export Dropdown --}}
        <div class="relative inline-block text-left" id="exportDropdownContainer">
            <button onclick="toggleExportMenu(event)"
                    class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs sm:text-sm font-medium rounded-lg hover:bg-[#F9FAFB] hover:border-[#D1D5DB] transition-all shadow-sm">
                <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i>
                <span>Export Excel</span>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#71717A]"></i>
            </button>
            <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-[#E5E7EB] rounded-xl shadow-lg z-30 py-1">
                <a href="{{ route('administrasi.capex.export', ['year' => $selectedYear, 'month' => $selectedMonth, 'type' => 'all']) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-[#374151] hover:bg-[#F9FAFB] hover:text-[#4B6A34]">
                    <i data-lucide="file-archive" class="w-3.5 h-3.5 text-[#71717A]"></i>
                    <span>Export Semua Data (Excel)</span>
                </a>
                <a href="{{ route('administrasi.capex.export', ['year' => $selectedYear, 'month' => $selectedMonth, 'type' => 'rab']) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-[#374151] hover:bg-[#F9FAFB] hover:text-[#4B6A34]">
                    <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-blue-500"></i>
                    <span>Export RAB Saja (Excel)</span>
                </a>
                <a href="{{ route('administrasi.capex.export', ['year' => $selectedYear, 'month' => $selectedMonth, 'type' => 'realisasi']) }}"
                   class="flex items-center gap-2 px-4 py-2 text-xs text-[#374151] hover:bg-[#F9FAFB] hover:text-[#4B6A34]">
                    <i data-lucide="receipt" class="w-3.5 h-3.5 text-amber-500"></i>
                    <span>Export Realisasi Saja (Excel)</span>
                </a>
            </div>
        </div>

        @can('edit_cost')
        {{-- Delete All Button --}}
        <button onclick="openDeleteAllModal()"
                class="flex items-center gap-2 px-3.5 py-2 bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm font-semibold rounded-lg hover:bg-red-100 hover:border-red-300 transition-all shadow-2xs"
                title="Hapus semua data CAPEX">
            <i data-lucide="trash" class="w-4 h-4 text-red-600"></i>
            <span>Hapus Semua</span>
        </button>

        {{-- Add Buttons Dropdown --}}
        <div class="relative inline-block text-left" id="addDropdownContainer">
            <button onclick="toggleAddMenu(event)"
                    class="flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-[#3d5829] transition-all shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Data</span>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
            </button>
            <div id="addMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border border-[#E5E7EB] rounded-xl shadow-xl z-30 py-1 divide-y divide-gray-100">
                <div class="p-1">
                    <button onclick="openAddRabModal()" class="w-full text-left flex items-center gap-2.5 px-3 py-2 text-xs text-[#374151] hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors">
                        <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center text-blue-600">
                            <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                        </div>
                        <div>
                            <p class="font-semibold">+ Tambah RAB</p>
                            <p class="text-[10px] text-[#71717A]">Rencana Anggaran</p>
                        </div>
                    </button>
                    <button onclick="openAddRealisasiModal()" class="w-full text-left flex items-center gap-2.5 px-3 py-2 text-xs text-[#374151] hover:bg-amber-50 hover:text-amber-700 rounded-lg transition-colors">
                        <div class="w-6 h-6 rounded-md bg-amber-100 flex items-center justify-center text-amber-600">
                            <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                        </div>
                        <div>
                            <p class="font-semibold">+ Tambah Realisasi</p>
                            <p class="text-[10px] text-[#71717A]">Transaksi Aktual</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        @endcan
    </div>
</div>

{{-- ============================== YEAR NAVIGATION CARD ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-3.5 mb-5 shadow-2xs">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        {{-- Left: Year Label + Scrollable container --}}
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
                @foreach ($years as $year)
                    <a href="{{ route('administrasi.capex.index', array_merge(request()->query(), ['year' => $year])) }}"
                       id="year-tab-{{ $year }}"
                       class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ (int)$selectedYear === (int)$year ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
                        {{ $year }}
                    </a>
                @endforeach
            </div>

            {{-- Scroll Right Button --}}
            <button type="button" onclick="scrollYearTabs('right')" class="p-1.5 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] hover:bg-gray-100 text-[#71717A] hover:text-[#18181B] transition-colors shrink-0" title="Scroll ke tahun berikutnya">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Right: Tambah Tahun Button --}}
        @can('edit_cost')
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="openTambahTahunModal()"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#4B6A34] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 flex items-center gap-1.5 transition-colors shadow-2xs">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Tambah Tahun</span>
            </button>
        </div>
        @endcan
    </div>
</div>

{{-- ============================== MONTH NAVIGATION CARD ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-3.5 mb-5 shadow-2xs">
    <div class="flex items-center gap-1.5 overflow-x-auto py-0.5 px-1 scroll-smooth" style="scrollbar-width: thin;">
        <div class="flex items-center gap-1.5 px-2 text-[#71717A] shrink-0">
            <i data-lucide="filter" class="w-4 h-4 text-[#4B6A34]"></i>
            <span class="text-xs font-bold text-[#18181B] uppercase tracking-wider">Bulan:</span>
        </div>
        <a href="{{ route('administrasi.capex.index', array_merge(request()->query(), ['month' => 'all'])) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $selectedMonth === 'all' ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
            Semua Bulan
        </a>
        @foreach($months as $num => $label)
            <a href="{{ route('administrasi.capex.index', array_merge(request()->query(), ['month' => $num])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ (string)$selectedMonth === (string)$num ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- ============================== LOCATION & SEARCH CONTROLS ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl p-4 mb-6 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        
        {{-- Location Filter Tabs --}}
        <div class="flex items-center gap-1 p-1 bg-[#F4F4F5] rounded-xl w-fit">
            <a href="{{ route('administrasi.capex.index', array_merge(request()->query(), ['location' => 'all', 'year' => $selectedYear, 'month' => $selectedMonth])) }}"
               class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ $selectedLocation === 'all' ? 'bg-white text-[#18181B] shadow-sm font-semibold' : 'text-[#71717A] hover:text-[#18181B]' }}">
                <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                <span>Semua Lokasi</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] {{ $selectedLocation === 'all' ? 'bg-[#4B6A34]/10 text-[#4B6A34] font-bold' : 'bg-gray-200 text-gray-600' }}">
                    {{ $rabKantor->total() + $rabMess->total() }} RAB
                </span>
            </a>
            <a href="{{ route('administrasi.capex.index', array_merge(request()->query(), ['location' => 'kantor', 'year' => $selectedYear, 'month' => $selectedMonth])) }}"
               class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ $selectedLocation === 'kantor' ? 'bg-white text-blue-700 shadow-sm font-semibold' : 'text-[#71717A] hover:text-[#18181B]' }}">
                <i data-lucide="building-2" class="w-3.5 h-3.5 text-blue-500"></i>
                <span>Kantor</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] {{ $selectedLocation === 'kantor' ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-gray-200 text-gray-600' }}">
                    {{ $rabKantor->total() }} item
                </span>
            </a>
            <a href="{{ route('administrasi.capex.index', array_merge(request()->query(), ['location' => 'mess', 'year' => $selectedYear, 'month' => $selectedMonth])) }}"
               class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ $selectedLocation === 'mess' ? 'bg-white text-purple-700 shadow-sm font-semibold' : 'text-[#71717A] hover:text-[#18181B]' }}">
                <i data-lucide="hotel" class="w-3.5 h-3.5 text-purple-500"></i>
                <span>Mess</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] {{ $selectedLocation === 'mess' ? 'bg-purple-100 text-purple-700 font-bold' : 'bg-gray-200 text-gray-600' }}">
                    {{ $rabMess->total() }} item
                </span>
            </a>
        </div>

        {{-- Search filter --}}
        <form method="GET" action="{{ route('administrasi.capex.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="location" value="{{ $selectedLocation }}">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <div class="relative w-full md:w-64">
                <i data-lucide="search" class="w-3.5 h-3.5 text-[#A1A1AA] absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / kelompok / merk..."
                       class="w-full pl-8 pr-3 py-1.5 bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg text-xs focus:bg-white focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] outline-none transition-all">
            </div>
            @if(!empty($search))
                <a href="{{ route('administrasi.capex.index', ['location' => $selectedLocation, 'year' => $selectedYear, 'month' => $selectedMonth]) }}"
                   class="p-1.5 text-xs text-[#71717A] hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Reset Search">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>
</div>

{{-- ============================== SUMMARY CARDS ============================== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    {{-- Total RAB --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 relative overflow-hidden shadow-sm hover:border-blue-300 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-[#71717A] uppercase tracking-wider">Total RAB Anggaran</span>
            <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shadow-xs">
                <i data-lucide="wallet" class="w-4 h-4 text-blue-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-[#18181B] tracking-tight">
            Rp {{ number_format($summary['totalRab'], 0, ',', '.') }}
        </p>
        <div class="flex items-center justify-between text-xs text-[#71717A] mt-2 pt-2 border-t border-[#F4F4F5]">
            <span>Kantor: Rp {{ number_format($summary['rabKantorTotal'], 0, ',', '.') }}</span>
            <span>Mess: Rp {{ number_format($summary['rabMessTotal'], 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Total Realisasi --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 relative overflow-hidden shadow-sm hover:border-amber-300 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-[#71717A] uppercase tracking-wider">Total Realisasi Aktual</span>
            <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center shadow-xs">
                <i data-lucide="receipt" class="w-4 h-4 text-amber-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-[#18181B] tracking-tight">
            Rp {{ number_format($summary['totalRealisasi'], 0, ',', '.') }}
        </p>
        <div class="flex items-center justify-between text-xs text-[#71717A] mt-2 pt-2 border-t border-[#F4F4F5]">
            <span class="text-emerald-700 font-medium">Debit: Rp {{ number_format($summary['totalDebit'], 0, ',', '.') }}</span>
            <span class="text-rose-600 font-medium">Kredit: Rp {{ number_format($summary['totalKredit'], 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Sisa RAB (Budget Balance) --}}
    @php
        $isOver = $summary['sisaRab'] < 0;
        $pctSpent = $summary['totalRab'] > 0 ? round(($summary['totalRealisasi'] / $summary['totalRab']) * 100, 1) : 0;
    @endphp
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 relative overflow-hidden shadow-sm {{ $isOver ? 'hover:border-red-300' : 'hover:border-green-300' }} transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-[#71717A] uppercase tracking-wider">Sisa Anggaran (Balance)</span>
            <div class="w-9 h-9 rounded-xl {{ $isOver ? 'bg-red-50 border border-red-100 text-red-600' : 'bg-green-50 border border-green-100 text-green-600' }} flex items-center justify-center shadow-xs">
                <i data-lucide="{{ $isOver ? 'alert-octagon' : 'piggy-bank' }}" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <p class="text-2xl font-bold {{ $isOver ? 'text-red-600' : 'text-green-600' }} tracking-tight">
                Rp {{ number_format(abs($summary['sisaRab']), 0, ',', '.') }}
            </p>
            @if($isOver)
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 uppercase">Defisit</span>
            @else
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">Sisa</span>
            @endif
        </div>
        <div class="flex items-center justify-between text-xs text-[#71717A] mt-2 pt-2 border-t border-[#F4F4F5]">
            <span>Persentase Penyerapan:</span>
            <span class="font-bold {{ $pctSpent > 100 ? 'text-red-600' : 'text-[#18181B]' }}">{{ $pctSpent }}%</span>
        </div>
    </div>
</div>

{{-- ============================== SUCCESS & ERROR MESSAGES ============================== --}}
@if(session('success'))
<div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl shadow-xs" id="flash-success">
    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
    <span class="font-medium">{{ session('success') }}</span>
    <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-600 hover:text-emerald-900"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

@if(session('error'))
<div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl shadow-xs" id="flash-error">
    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
    <span class="font-medium">{{ session('error') }}</span>
    <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-600 hover:text-red-900"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

{{-- =========================================================================
     2x2 COMPARISON GRID (SHARED PARENT HORIZONTAL SCROLL CONTAINER)
========================================================================= --}}
<div class="w-full overflow-x-auto pb-4 scroll-smooth">
    <div class="min-w-[1500px] space-y-8">

        {{-- ============================== SECTION: KANTOR ============================== --}}
        @if($selectedLocation === 'all' || $selectedLocation === 'kantor')
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-600"></div>
                    <h2 class="text-base font-bold text-[#18181B] uppercase tracking-wide">CAPEX KANTOR (RAB VS REALISASI)</h2>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-[#71717A]">
                    <span>Selisih Kantor:</span>
                    @php $diffKantor = $summary['rabKantorTotal'] - $summary['realisasiKantorTotal']; @endphp
                    <span class="{{ $diffKantor >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        Rp {{ number_format(abs($diffKantor), 0, ',', '.') }} {{ $diffKantor >= 0 ? '(Sisa)' : '(Over)' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-stretch">
                {{-- ============ RAB KANTOR TABLE ============ --}}
                <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col shadow-xs min-w-[720px]">
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 rounded bg-blue-500"></span>
                            <h3 class="font-bold text-xs text-[#18181B] uppercase tracking-wider">RAB Kantor</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">
                                {{ $rabKantor->total() }} Data
                            </span>
                        </div>
                        @can('edit_cost')
                        <button onclick="openAddRabModalPreset('kantor')"
                                class="flex items-center gap-1 text-xs font-semibold text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah RAB
                        </button>
                        @endcan
                    </div>

                    <div class="flex-1">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#71717A] uppercase text-[11px] tracking-wider">
                                    <th class="px-3 py-2.5 text-center w-8">No</th>
                                    <th class="px-2 py-2.5 text-center w-10">Foto</th>
                                    <th class="px-3 py-2.5 text-left">Kel. Barang</th>
                                    <th class="px-3 py-2.5 text-left">Type / Merk</th>
                                    <th class="px-2 py-2.5 text-center">Satuan</th>
                                    <th class="px-2 py-2.5 text-right">Qty</th>
                                    <th class="px-3 py-2.5 text-right">Harga (Rp)</th>
                                    <th class="px-3 py-2.5 text-right">Total (Rp)</th>
                                    <th class="px-2 py-2.5 text-center w-14">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F4F4F5]">
                                @forelse($rabKantor as $i => $row)
                                <tr class="hover:bg-[#FAFAFA] transition-colors">
                                    <td class="px-3 py-2.5 text-center text-[#71717A] font-mono">{{ $rabKantor->firstItem() + $i }}</td>
                                    <td class="px-2 py-2.5 text-center">
                                        @if(!empty($row->foto_barang))
                                            <button onclick="showImageModal('{{ asset($row->foto_barang) }}', '{{ addslashes($row->kel_barang_kapital) }}')"
                                                    class="w-7 h-7 rounded border border-gray-200 overflow-hidden hover:opacity-80 transition-opacity mx-auto inline-block">
                                                <img src="{{ asset($row->foto_barang) }}" alt="" class="w-full h-full object-cover">
                                            </button>
                                        @else
                                            <span class="w-7 h-7 rounded bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-[10px]">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 font-semibold text-[#18181B]">{{ $row->kel_barang_kapital }}</td>
                                    <td class="px-3 py-2.5 text-[#52525B]">{{ $row->type ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-center text-[#52525B]">{{ $row->satuan ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-right font-medium text-[#18181B]">{{ number_format($row->jumlah_unit, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2.5 text-right text-[#52525B] whitespace-nowrap">{{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2.5 text-right font-bold text-blue-700 whitespace-nowrap">{{ number_format($row->total_rp, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2.5 text-center whitespace-nowrap">
                                        @can('edit_cost')
                                        <div class="flex items-center justify-center gap-1">
                                            <button onclick="openEditRabModal({{ $row->rab_id }})"
                                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <button onclick="openDeleteModal('rab', {{ $row->rab_id }}, '{{ addslashes($row->kel_barang_kapital) }}')"
                                                    class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-[#A1A1AA]">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                        <p class="text-xs">Belum ada data RAB Kantor untuk tahun {{ $selectedYear }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-blue-50/70 border-t-2 border-blue-200">
                                    <td colspan="7" class="px-3 py-3 text-xs font-bold text-blue-800 uppercase tracking-wider">
                                        Total RAB Kantor
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-blue-900 whitespace-nowrap">
                                        Rp {{ number_format($summary['rabKantorTotal'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($rabKantor->hasPages())
                    <div class="px-4 py-2 border-t border-[#E5E7EB] bg-[#F9FAFB]">
                        {{ $rabKantor->appends(request()->except('rab_kantor_page'))->links('pagination::simple-tailwind') }}
                    </div>
                    @endif
                </div>

                {{-- ============ REALISASI KANTOR TABLE ============ --}}
                <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col shadow-xs min-w-[720px]">
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 rounded bg-amber-500"></span>
                            <h3 class="font-bold text-xs text-[#18181B] uppercase tracking-wider">Realisasi Kantor</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800">
                                {{ $realisasiKantor->total() }} Data
                            </span>
                        </div>
                        @can('edit_cost')
                        <button onclick="openAddRealisasiModalPreset('kantor')"
                                class="flex items-center gap-1 text-xs font-semibold text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Realisasi
                        </button>
                        @endcan
                    </div>

                    <div class="flex-1">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#71717A] uppercase text-[11px] tracking-wider">
                                    <th class="px-3 py-2.5 text-center w-8">No</th>
                                    <th class="px-2 py-2.5 text-center">Tgl</th>
                                    <th class="px-2 py-2.5 text-center w-10">Foto</th>
                                    <th class="px-3 py-2.5 text-left">Kel. / Nama Barang</th>
                                    <th class="px-3 py-2.5 text-left">Type</th>
                                    <th class="px-3 py-2.5 text-right">Debit (Rp)</th>
                                    <th class="px-3 py-2.5 text-right">Kredit (Rp)</th>
                                    <th class="px-3 py-2.5 text-right">Total (Rp)</th>
                                    <th class="px-2 py-2.5 text-center w-14">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F4F4F5]">
                                @forelse($realisasiKantor as $i => $row)
                                <tr class="hover:bg-[#FAFAFA] transition-colors">
                                    <td class="px-3 py-2.5 text-center text-[#71717A] font-mono">{{ $realisasiKantor->firstItem() + $i }}</td>
                                    <td class="px-2 py-2.5 text-center text-[#52525B] whitespace-nowrap">
                                        {{ $row->tanggal ? $row->tanggal->format('d/m/y') : '-' }}
                                    </td>
                                    <td class="px-2 py-2.5 text-center">
                                        @if(!empty($row->foto_barang))
                                            <button onclick="showImageModal('{{ asset($row->foto_barang) }}', '{{ addslashes($row->nama_barang) }}')"
                                                    class="w-7 h-7 rounded border border-gray-200 overflow-hidden hover:opacity-80 transition-opacity mx-auto inline-block">
                                                <img src="{{ asset($row->foto_barang) }}" alt="" class="w-full h-full object-cover">
                                            </button>
                                        @else
                                            <span class="w-7 h-7 rounded bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-[10px]">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-semibold text-[#18181B]">{{ $row->nama_barang }}</div>
                                        <div class="text-[11px] text-[#71717A] flex items-center gap-1 mt-0.5">
                                            <span>{{ $row->kel_barang_kapital }}</span>
                                            @if($row->rab_id)
                                                <span class="px-1 py-0.2 rounded bg-blue-50 text-blue-600 text-[9px] font-mono font-semibold" title="Linked to RAB #{{ $row->rab_id }}">
                                                    #RAB{{ $row->rab_id }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-[#52525B]">{{ $row->type ?? '-' }}</td>
                                    <td class="px-3 py-2.5 text-right font-medium text-emerald-600 whitespace-nowrap">
                                        {{ number_format($row->debit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-medium text-rose-500 whitespace-nowrap">
                                        {{ number_format($row->kredit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-bold text-amber-700 whitespace-nowrap">
                                        {{ number_format($row->total_rp, 0, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-2.5 text-center whitespace-nowrap">
                                        @can('edit_cost')
                                        <div class="flex items-center justify-center gap-1">
                                            <button onclick="openEditRealisasiModal({{ $row->realisasi_id }})"
                                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <button onclick="openDeleteModal('realisasi', {{ $row->realisasi_id }}, '{{ addslashes($row->nama_barang) }}')"
                                                    class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-[#A1A1AA]">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                        <p class="text-xs">Belum ada data Realisasi Kantor untuk tahun {{ $selectedYear }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-amber-50/70 border-t-2 border-amber-200">
                                    <td colspan="5" class="px-3 py-3 text-xs font-bold text-amber-800 uppercase tracking-wider">
                                        Total Realisasi Kantor
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-emerald-700 whitespace-nowrap" title="Total Debit">
                                        Rp {{ number_format($summary['debitKantorTotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-rose-700 whitespace-nowrap" title="Total Kredit">
                                        Rp {{ number_format($summary['kreditKantorTotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-amber-900 whitespace-nowrap" title="Total Realisasi (Debit - Kredit)">
                                        Rp {{ number_format($summary['realisasiKantorTotal'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($realisasiKantor->hasPages())
                    <div class="px-4 py-2 border-t border-[#E5E7EB] bg-[#F9FAFB]">
                        {{ $realisasiKantor->appends(request()->except('rel_kantor_page'))->links('pagination::simple-tailwind') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ============================== SECTION: MESS ============================== --}}
        @if($selectedLocation === 'all' || $selectedLocation === 'mess')
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-purple-600"></div>
                    <h2 class="text-base font-bold text-[#18181B] uppercase tracking-wide">CAPEX MESS (RAB VS REALISASI)</h2>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-[#71717A]">
                    <span>Selisih Mess:</span>
                    @php $diffMess = $summary['rabMessTotal'] - $summary['realisasiMessTotal']; @endphp
                    <span class="{{ $diffMess >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        Rp {{ number_format(abs($diffMess), 0, ',', '.') }} {{ $diffMess >= 0 ? '(Sisa)' : '(Over)' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-stretch">
                {{-- ============ RAB MESS TABLE ============ --}}
                <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col shadow-xs min-w-[720px]">
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 rounded bg-purple-500"></span>
                            <h3 class="font-bold text-xs text-[#18181B] uppercase tracking-wider">RAB Mess</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-800">
                                {{ $rabMess->total() }} Data
                            </span>
                        </div>
                        @can('edit_cost')
                        <button onclick="openAddRabModalPreset('mess')"
                                class="flex items-center gap-1 text-xs font-semibold text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah RAB
                        </button>
                        @endcan
                    </div>

                    <div class="flex-1">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#71717A] uppercase text-[11px] tracking-wider">
                                    <th class="px-3 py-2.5 text-center w-8">No</th>
                                    <th class="px-2 py-2.5 text-center w-10">Foto</th>
                                    <th class="px-3 py-2.5 text-left">Kel. Barang</th>
                                    <th class="px-3 py-2.5 text-left">Type / Merk</th>
                                    <th class="px-2 py-2.5 text-center">Satuan</th>
                                    <th class="px-2 py-2.5 text-right">Qty</th>
                                    <th class="px-3 py-2.5 text-right">Harga (Rp)</th>
                                    <th class="px-3 py-2.5 text-right">Total (Rp)</th>
                                    <th class="px-2 py-2.5 text-center w-14">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F4F4F5]">
                                @forelse($rabMess as $i => $row)
                                <tr class="hover:bg-[#FAFAFA] transition-colors">
                                    <td class="px-3 py-2.5 text-center text-[#71717A] font-mono">{{ $rabMess->firstItem() + $i }}</td>
                                    <td class="px-2 py-2.5 text-center">
                                        @if(!empty($row->foto_barang))
                                            <button onclick="showImageModal('{{ asset($row->foto_barang) }}', '{{ addslashes($row->kel_barang_kapital) }}')"
                                                    class="w-7 h-7 rounded border border-gray-200 overflow-hidden hover:opacity-80 transition-opacity mx-auto inline-block">
                                                <img src="{{ asset($row->foto_barang) }}" alt="" class="w-full h-full object-cover">
                                            </button>
                                        @else
                                            <span class="w-7 h-7 rounded bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-[10px]">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 font-semibold text-[#18181B]">{{ $row->kel_barang_kapital }}</td>
                                    <td class="px-3 py-2.5 text-[#52525B]">{{ $row->type ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-center text-[#52525B]">{{ $row->satuan ?? '-' }}</td>
                                    <td class="px-2 py-2.5 text-right font-medium text-[#18181B]">{{ number_format($row->jumlah_unit, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2.5 text-right text-[#52525B] whitespace-nowrap">{{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2.5 text-right font-bold text-purple-700 whitespace-nowrap">{{ number_format($row->total_rp, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2.5 text-center whitespace-nowrap">
                                        @can('edit_cost')
                                        <div class="flex items-center justify-center gap-1">
                                            <button onclick="openEditRabModal({{ $row->rab_id }})"
                                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <button onclick="openDeleteModal('rab', {{ $row->rab_id }}, '{{ addslashes($row->kel_barang_kapital) }}')"
                                                    class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-[#A1A1AA]">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                        <p class="text-xs">Belum ada data RAB Mess untuk tahun {{ $selectedYear }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-purple-50/70 border-t-2 border-purple-200">
                                    <td colspan="7" class="px-3 py-3 text-xs font-bold text-purple-800 uppercase tracking-wider">
                                        Total RAB Mess
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-purple-900 whitespace-nowrap">
                                        Rp {{ number_format($summary['rabMessTotal'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($rabMess->hasPages())
                    <div class="px-4 py-2 border-t border-[#E5E7EB] bg-[#F9FAFB]">
                        {{ $rabMess->appends(request()->except('rab_mess_page'))->links('pagination::simple-tailwind') }}
                    </div>
                    @endif
                </div>

                {{-- ============ REALISASI MESS TABLE ============ --}}
                <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col shadow-xs min-w-[720px]">
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-[#E5E7EB] bg-[#F9FAFB]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 rounded bg-rose-500"></span>
                            <h3 class="font-bold text-xs text-[#18181B] uppercase tracking-wider">Realisasi Mess</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800">
                                {{ $realisasiMess->total() }} Data
                            </span>
                        </div>
                        @can('edit_cost')
                        <button onclick="openAddRealisasiModalPreset('mess')"
                                class="flex items-center gap-1 text-xs font-semibold text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Realisasi
                        </button>
                        @endcan
                    </div>

                    <div class="flex-1">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#71717A] uppercase text-[11px] tracking-wider">
                                    <th class="px-3 py-2.5 text-center w-8">No</th>
                                    <th class="px-2 py-2.5 text-center">Tgl</th>
                                    <th class="px-2 py-2.5 text-center w-10">Foto</th>
                                    <th class="px-3 py-2.5 text-left">Kel. / Nama Barang</th>
                                    <th class="px-3 py-2.5 text-left">Type</th>
                                    <th class="px-3 py-2.5 text-right">Debit (Rp)</th>
                                    <th class="px-3 py-2.5 text-right">Kredit (Rp)</th>
                                    <th class="px-3 py-2.5 text-right">Total (Rp)</th>
                                    <th class="px-2 py-2.5 text-center w-14">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F4F4F5]">
                                @forelse($realisasiMess as $i => $row)
                                <tr class="hover:bg-[#FAFAFA] transition-colors">
                                    <td class="px-3 py-2.5 text-center text-[#71717A] font-mono">{{ $realisasiMess->firstItem() + $i }}</td>
                                    <td class="px-2 py-2.5 text-center text-[#52525B] whitespace-nowrap">
                                        {{ $row->tanggal ? $row->tanggal->format('d/m/y') : '-' }}
                                    </td>
                                    <td class="px-2 py-2.5 text-center">
                                        @if(!empty($row->foto_barang))
                                            <button onclick="showImageModal('{{ asset($row->foto_barang) }}', '{{ addslashes($row->nama_barang) }}')"
                                                    class="w-7 h-7 rounded border border-gray-200 overflow-hidden hover:opacity-80 transition-opacity mx-auto inline-block">
                                                <img src="{{ asset($row->foto_barang) }}" alt="" class="w-full h-full object-cover">
                                            </button>
                                        @else
                                            <span class="w-7 h-7 rounded bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-[10px]">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-semibold text-[#18181B]">{{ $row->nama_barang }}</div>
                                        <div class="text-[11px] text-[#71717A] flex items-center gap-1 mt-0.5">
                                            <span>{{ $row->kel_barang_kapital }}</span>
                                            @if($row->rab_id)
                                                <span class="px-1 py-0.2 rounded bg-purple-50 text-purple-600 text-[9px] font-mono font-semibold" title="Linked to RAB #{{ $row->rab_id }}">
                                                    #RAB{{ $row->rab_id }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-[#52525B]">{{ $row->type ?? '-' }}</td>
                                    <td class="px-3 py-2.5 text-right font-medium text-emerald-600 whitespace-nowrap">
                                        {{ number_format($row->debit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-medium text-rose-500 whitespace-nowrap">
                                        {{ number_format($row->kredit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right font-bold text-rose-700 whitespace-nowrap">
                                        {{ number_format($row->total_rp, 0, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-2.5 text-center whitespace-nowrap">
                                        @can('edit_cost')
                                        <div class="flex items-center justify-center gap-1">
                                            <button onclick="openEditRealisasiModal({{ $row->realisasi_id }})"
                                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <button onclick="openDeleteModal('realisasi', {{ $row->realisasi_id }}, '{{ addslashes($row->nama_barang) }}')"
                                                    class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-[#A1A1AA]">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                        <p class="text-xs">Belum ada data Realisasi Mess untuk tahun {{ $selectedYear }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-rose-50/70 border-t-2 border-rose-200">
                                    <td colspan="5" class="px-3 py-3 text-xs font-bold text-rose-800 uppercase tracking-wider">
                                        Total Realisasi Mess
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-emerald-700 whitespace-nowrap" title="Total Debit">
                                        Rp {{ number_format($summary['debitMessTotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-rose-700 whitespace-nowrap" title="Total Kredit">
                                        Rp {{ number_format($summary['kreditMessTotal'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-xs sm:text-sm font-bold text-rose-900 whitespace-nowrap" title="Total Realisasi (Debit - Kredit)">
                                        Rp {{ number_format($summary['realisasiMessTotal'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($realisasiMess->hasPages())
                    <div class="px-4 py-2 border-t border-[#E5E7EB] bg-[#F9FAFB]">
                        {{ $realisasiMess->appends(request()->except('rel_mess_page'))->links('pagination::simple-tailwind') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- =========================================================================
     MODALS SECTION
================================================================ insolvency --}}

@can('edit_cost')
{{-- ============================== 1. MODAL TAMBAH RAB ============================== --}}
<div id="modalAddRab" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeAddRabModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                </div>
                <h2 class="text-base font-bold text-[#18181B]">Tambah Data RAB CAPEX</h2>
            </div>
            <button onclick="closeAddRabModal()" class="p-1 hover:bg-[#E5E7EB] rounded-lg transition-colors text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('administrasi.capex.rab.store') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            {{-- Lokasi Selection --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Lokasi Investasi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="kantor" id="add_rab_lokasi_kantor" checked class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-blue-500 peer-checked:bg-blue-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-blue-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-blue-800">Kantor</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="mess" id="add_rab_lokasi_mess" class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-purple-500 peer-checked:bg-purple-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="hotel" class="w-4 h-4 text-purple-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-purple-800">Mess</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Kelompok Barang Kapital --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Kelompok Barang Kapital <span class="text-red-500">*</span></label>
                <input type="text" name="kel_barang_kapital" placeholder="Contoh: Kendaraan Operasional, AC & Pendingin, Laptop..." required
                       class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            {{-- Type / Merk & Satuan --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Type / Merk</label>
                    <input type="text" name="type" placeholder="Contoh: Toyota Hilux, Daikin 1 PK..."
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Satuan</label>
                    <input type="text" name="satuan" placeholder="Contoh: Unit, Pcs, Set..." value="Unit"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Qty & Harga Satuan --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Jumlah Unit (Qty) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" min="0" name="jumlah_unit" id="add_rab_qty" value="1" required oninput="calcAddRabTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" min="0" name="harga_satuan" id="add_rab_harga" placeholder="0" required oninput="calcAddRabTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Total Preview --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-blue-800">Total RAB (Kalkulasi):</span>
                <span id="add_rab_total_preview" class="text-sm font-bold text-blue-900">Rp 0</span>
            </div>

            {{-- Foto Barang (File / URL) --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Foto Barang (Opsional)</label>
                <input type="file" name="foto_file" accept="image/*"
                       class="w-full text-xs text-[#71717A] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4B6A34]/10 file:text-[#4B6A34] hover:file:bg-[#4B6A34]/20 cursor-pointer">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeAddRabModal()"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">
                    Simpan RAB
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== 2. MODAL TAMBAH REALISASI ============================== --}}
<div id="modalAddRealisasi" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeAddRealisasiModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
                <h2 class="text-base font-bold text-[#18181B]">Tambah Data Realisasi CAPEX</h2>
            </div>
            <button onclick="closeAddRealisasiModal()" class="p-1 hover:bg-[#E5E7EB] rounded-lg transition-colors text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('administrasi.capex.realisasi.store') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            {{-- Link to existing RAB Dropdown --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Pautkan ke Item RAB (Opsional)</label>
                <select name="rab_id" id="add_rel_rab_id" onchange="onRabSelected(this)"
                        class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none bg-white">
                    <option value="">— Tidak terpaut ke RAB / Transaksi Bebas —</option>
                    @foreach($availableRabs as $rab)
                        <option value="{{ $rab->rab_id }}"
                                data-lokasi="{{ $rab->lokasi }}"
                                data-kelompok="{{ $rab->kel_barang_kapital }}"
                                data-type="{{ $rab->type }}"
                                data-harga="{{ $rab->harga_satuan }}">
                            [{{ strtoupper($rab->lokasi) }}] #{{ $rab->rab_id }} - {{ $rab->kel_barang_kapital }} ({{ $rab->type ?? '-' }}) - Rp {{ number_format($rab->total_rp, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-[#A1A1AA] mt-1">Memilih RAB akan otomatis mengisi Kelompok Barang, Lokasi, dan Type.</p>
            </div>

            {{-- Lokasi Selection --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Lokasi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="kantor" id="add_rel_lokasi_kantor" checked class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-amber-500 peer-checked:bg-amber-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-amber-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-amber-800">Kantor</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="mess" id="add_rel_lokasi_mess" class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-rose-500 peer-checked:bg-rose-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="hotel" class="w-4 h-4 text-rose-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-rose-800">Mess</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Nama Barang & Kelompok Barang --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Nama Barang / Transaksi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_barang" id="add_rel_nama_barang" placeholder="Contoh: Pembelian Laptop Asus..." required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Kelompok Barang Kapital <span class="text-red-500">*</span></label>
                    <input type="text" name="kel_barang_kapital" id="add_rel_kel_barang" placeholder="Contoh: Komputer & Laptop..." required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Type & Tanggal --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Type / Merk</label>
                    <input type="text" name="type" id="add_rel_type" placeholder="Contoh: Asus ROG / Apple..."
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Debit & Kredit --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Debit / Pengeluaran (Rp)</label>
                    <input type="number" step="any" min="0" name="debit" id="add_rel_debit" placeholder="0" oninput="calcAddRelTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Kredit / Pengembalian (Rp)</label>
                    <input type="number" step="any" min="0" name="kredit" id="add_rel_kredit" placeholder="0" oninput="calcAddRelTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Total Realisasi Preview --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-800">Total Realisasi (Debit - Kredit):</span>
                <span id="add_rel_total_preview" class="text-sm font-bold text-amber-900">Rp 0</span>
            </div>

            {{-- Foto Barang (File / URL) --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Foto Bukti / Barang (Opsional)</label>
                <input type="file" name="foto_file" accept="image/*"
                       class="w-full text-xs text-[#71717A] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4B6A34]/10 file:text-[#4B6A34] hover:file:bg-[#4B6A34]/20 cursor-pointer">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeAddRealisasiModal()"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">
                    Simpan Realisasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== 3. MODAL EDIT RAB ============================== --}}
<div id="modalEditRab" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeEditRabModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                </div>
                <h2 class="text-base font-bold text-[#18181B]">Edit Data RAB CAPEX</h2>
            </div>
            <button onclick="closeEditRabModal()" class="p-1 hover:bg-[#E5E7EB] rounded-lg transition-colors text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditRab" method="POST" action="" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="tahun" id="edit_rab_tahun" value="{{ $selectedYear }}">

            {{-- Lokasi Selection --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Lokasi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="kantor" id="edit_rab_lokasi_kantor" class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-blue-500 peer-checked:bg-blue-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-blue-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-blue-800">Kantor</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="mess" id="edit_rab_lokasi_mess" class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-purple-500 peer-checked:bg-purple-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="hotel" class="w-4 h-4 text-purple-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-purple-800">Mess</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Kelompok Barang Kapital --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Kelompok Barang Kapital <span class="text-red-500">*</span></label>
                <input type="text" name="kel_barang_kapital" id="edit_rab_kel_barang" required
                       class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            {{-- Type & Satuan --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Type / Merk</label>
                    <input type="text" name="type" id="edit_rab_type"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Satuan</label>
                    <input type="text" name="satuan" id="edit_rab_satuan"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Qty & Harga Satuan --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Jumlah Unit (Qty) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" min="0" name="jumlah_unit" id="edit_rab_qty" required oninput="calcEditRabTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" min="0" name="harga_satuan" id="edit_rab_harga" required oninput="calcEditRabTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Total Preview --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-blue-800">Total RAB (Kalkulasi):</span>
                <span id="edit_rab_total_preview" class="text-sm font-bold text-blue-900">Rp 0</span>
            </div>

            {{-- Foto Barang (File / URL) --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Ganti Foto Barang (Opsional)</label>
                <input type="file" name="foto_file" accept="image/*"
                       class="w-full text-xs text-[#71717A] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4B6A34]/10 file:text-[#4B6A34] hover:file:bg-[#4B6A34]/20 cursor-pointer">
                <input type="hidden" name="foto_barang" id="edit_rab_foto_barang">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeEditRabModal()"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== 4. MODAL EDIT REALISASI ============================== --}}
<div id="modalEditRealisasi" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeEditRealisasiModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                </div>
                <h2 class="text-base font-bold text-[#18181B]">Edit Data Realisasi CAPEX</h2>
            </div>
            <button onclick="closeEditRealisasiModal()" class="p-1 hover:bg-[#E5E7EB] rounded-lg transition-colors text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditRealisasi" method="POST" action="" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="tahun" id="edit_rel_tahun" value="{{ $selectedYear }}">

            {{-- Link to existing RAB Dropdown --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Pautkan ke Item RAB (Opsional)</label>
                <select name="rab_id" id="edit_rel_rab_id"
                        class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none bg-white">
                    <option value="">— Tidak terpaut ke RAB / Transaksi Bebas —</option>
                    @foreach($availableRabs as $rab)
                        <option value="{{ $rab->rab_id }}">
                            [{{ strtoupper($rab->lokasi) }}] #{{ $rab->rab_id }} - {{ $rab->kel_barang_kapital }} ({{ $rab->type ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lokasi Selection --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Lokasi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="kantor" id="edit_rel_lokasi_kantor" class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-amber-500 peer-checked:bg-amber-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="building-2" class="w-4 h-4 text-amber-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-amber-800">Kantor</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="lokasi" value="mess" id="edit_rel_lokasi_mess" class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-rose-500 peer-checked:bg-rose-50/60 rounded-xl p-3 text-center transition-all flex items-center justify-center gap-2">
                            <i data-lucide="hotel" class="w-4 h-4 text-rose-600"></i>
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-rose-800">Mess</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Nama Barang & Kelompok Barang --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Nama Barang / Transaksi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_barang" id="edit_rel_nama_barang" required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Kelompok Barang Kapital <span class="text-red-500">*</span></label>
                    <input type="text" name="kel_barang_kapital" id="edit_rel_kel_barang" required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Type & Tanggal --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Type / Merk</label>
                    <input type="text" name="type" id="edit_rel_type"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" id="edit_rel_tanggal"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Debit & Kredit --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Debit (Rp)</label>
                    <input type="number" step="any" min="0" name="debit" id="edit_rel_debit" oninput="calcEditRelTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Kredit (Rp)</label>
                    <input type="number" step="any" min="0" name="kredit" id="edit_rel_kredit" oninput="calcEditRelTotal()"
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            {{-- Total Realisasi Preview --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-800">Total Realisasi:</span>
                <span id="edit_rel_total_preview" class="text-sm font-bold text-amber-900">Rp 0</span>
            </div>

            {{-- Foto Barang (File / URL) --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Ganti Foto Bukti / Barang (Opsional)</label>
                <input type="file" name="foto_file" accept="image/*"
                       class="w-full text-xs text-[#71717A] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4B6A34]/10 file:text-[#4B6A34] hover:file:bg-[#4B6A34]/20 cursor-pointer">
                <input type="hidden" name="foto_barang" id="edit_rel_foto_barang">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeEditRealisasiModal()"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== 5. MODAL DELETE ============================== --}}
<div id="modalDelete" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden p-6 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 text-red-600">
            <i data-lucide="trash-2" class="w-6 h-6"></i>
        </div>
        <h3 class="text-base font-bold text-[#18181B] mb-1">Hapus Data CAPEX</h3>
        <p class="text-xs text-[#71717A] mb-2">Apakah Anda yakin ingin menghapus data ini?</p>
        <p class="text-xs font-bold text-[#18181B] bg-[#F4F4F5] px-3 py-2 rounded-lg mb-4 truncate" id="deleteItemName">—</p>
        <p class="text-[11px] text-red-500 mb-5">Tindakan ini permanen dan tidak dapat dibatalkan.</p>

        <form id="formDelete" method="POST" action="" class="flex gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                Batal
            </button>
            <button type="submit"
                    class="flex-1 px-4 py-2 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

{{-- ============================== 5B. MODAL DELETE ALL CAPEX ============================== --}}
<div id="modalDeleteAll" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeDeleteAllModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden p-6 text-center">
        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
            <i data-lucide="alert-octagon" class="w-7 h-7"></i>
        </div>
        <h3 class="text-base font-bold text-[#18181B] mb-1">Hapus Semua Data CAPEX?</h3>
        <p class="text-xs text-[#71717A] mb-4">
            Tindakan ini akan menghapus <strong>seluruh data RAB dan Realisasi CAPEX</strong> yang tersimpan di sistem.
        </p>
        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-[11px] text-red-800 text-left mb-5">
            <p class="font-bold flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i> Perhatian:</p>
            <p class="mt-0.5">Data yang telah dihapus tidak dapat dikembalikan kecuali Anda melakukan import ulang file CSV.</p>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteAllModal()"
                    class="flex-1 px-4 py-2.5 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                Batal
            </button>
            <form method="POST" action="{{ route('administrasi.capex.destroyAll', ['location' => $selectedLocation]) }}" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                    Ya, Hapus Semua
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ============================== 6. MODAL IMPORT CSV ============================== --}}
<div id="modalImport" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeImportModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#4B6A34]/10 flex items-center justify-center text-[#4B6A34]">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#18181B]">Import Data CAPEX dari CSV</h2>
                    <p class="text-[10px] text-[#71717A]">Mendukung format RAB dan Realisasi secara otomatis</p>
                </div>
            </div>
            <button onclick="closeImportModal()" class="p-1 hover:bg-[#E5E7EB] rounded-lg transition-colors text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('administrasi.capex.import') }}" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            {{-- Format Selector --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tipe Dataset CSV</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="import_type" value="auto" checked class="peer sr-only">
                        <div class="border border-[#E5E7EB] peer-checked:border-[#4B6A34] peer-checked:bg-[#4B6A34]/5 rounded-lg p-2.5 text-center transition-all">
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-[#4B6A34]">Otomatis</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="import_type" value="rab" class="peer sr-only">
                        <div class="border border-[#E5E7EB] peer-checked:border-blue-500 peer-checked:bg-blue-50/60 rounded-lg p-2.5 text-center transition-all">
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-blue-800">Format RAB</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="import_type" value="realisasi" class="peer sr-only">
                        <div class="border border-[#E5E7EB] peer-checked:border-amber-500 peer-checked:bg-amber-50/60 rounded-lg p-2.5 text-center transition-all">
                            <span class="text-xs font-bold text-[#374151] peer-checked:text-amber-800">Format Realisasi</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Mode Selector --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Metode Import</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="import_mode" value="append" checked class="peer sr-only">
                        <div class="border border-[#E5E7EB] peer-checked:border-[#4B6A34] peer-checked:bg-[#4B6A34]/5 rounded-xl p-3 transition-all">
                            <p class="text-xs font-bold text-[#374151] peer-checked:text-[#4B6A34]">Tambah ke Data Ada</p>
                            <p class="text-[10px] text-[#A1A1AA] mt-0.5">Mempertahankan data lama</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="import_mode" value="replace" class="peer sr-only">
                        <div class="border border-[#E5E7EB] peer-checked:border-red-500 peer-checked:bg-red-50/60 rounded-xl p-3 transition-all">
                            <p class="text-xs font-bold text-[#374151] peer-checked:text-red-700">Timpa Data Tahun Ini</p>
                            <p class="text-[10px] text-[#A1A1AA] mt-0.5">Hapus data tahun {{ $selectedYear }}</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- File Drop Area --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Pilih File CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-[#D1D5DB] rounded-xl p-6 text-center hover:border-[#4B6A34] transition-colors bg-[#FAFAFA]">
                    <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto text-[#71717A] mb-2"></i>
                    <p class="text-xs font-semibold text-[#374151]">Klik atau Seret file CSV ke sini</p>
                    <p class="text-[10px] text-[#A1A1AA] mt-1">Maksimal ukuran file 10MB (.csv, .txt)</p>
                    <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required
                           class="w-full text-xs text-[#71717A] mt-3 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-[#4B6A34] file:text-white cursor-pointer">
                </div>
            </div>

            {{-- Download Template Helper Links --}}
            <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl p-3.5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#374151]">Belum punya format CSV?</p>
                    <p class="text-[10px] text-[#71717A]">Unduh template siap pakai dari sini:</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('administrasi.capex.template', ['type' => 'rab']) }}"
                       class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-md text-[11px] font-semibold transition-colors">
                        Template RAB
                    </a>
                    <a href="{{ route('administrasi.capex.template', ['type' => 'realisasi']) }}"
                       class="px-2.5 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-md text-[11px] font-semibold transition-colors">
                        Template Realisasi
                    </a>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeImportModal()"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">
                    Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ============================== 8. MODAL IMAGE PREVIEW ============================== --}}
<div id="modalImagePreview" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeImageModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden p-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h4 class="text-xs font-bold text-[#18181B] truncate" id="modalImageTitle">Foto Barang</h4>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="py-4 text-center">
            <img id="modalImageSrc" src="" alt="Foto Barang" class="max-h-80 mx-auto rounded-lg object-contain">
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================================
// DROPDOWNS HANDLING
// ============================================================
function toggleTemplateMenu(e) {
    e.stopPropagation();
    document.getElementById('templateMenu').classList.toggle('hidden');
    document.getElementById('exportMenu').classList.add('hidden');
    document.getElementById('addMenu').classList.add('hidden');
}

function toggleExportMenu(e) {
    e.stopPropagation();
    document.getElementById('exportMenu').classList.toggle('hidden');
    document.getElementById('templateMenu').classList.add('hidden');
    document.getElementById('addMenu').classList.add('hidden');
}

function toggleAddMenu(e) {
    e.stopPropagation();
    document.getElementById('addMenu').classList.toggle('hidden');
    document.getElementById('templateMenu').classList.add('hidden');
    document.getElementById('exportMenu').classList.add('hidden');
}

document.addEventListener('click', function() {
    const tm = document.getElementById('templateMenu');
    const em = document.getElementById('exportMenu');
    const am = document.getElementById('addMenu');
    if (tm) tm.classList.add('hidden');
    if (em) em.classList.add('hidden');
    if (am) am.classList.add('hidden');
});

// ============================================================
// MODAL CONTROLS: ADD RAB
// ============================================================
function openAddRabModal() {
    document.getElementById('modalAddRab').classList.remove('hidden');
}

function openAddRabModalPreset(lokasi) {
    if (lokasi === 'kantor') {
        const el = document.getElementById('add_rab_lokasi_kantor');
        if (el) el.checked = true;
    } else if (lokasi === 'mess') {
        const el = document.getElementById('add_rab_lokasi_mess');
        if (el) el.checked = true;
    }
    openAddRabModal();
}

function closeAddRabModal() {
    document.getElementById('modalAddRab').classList.add('hidden');
}

function calcAddRabTotal() {
    const qty = parseFloat(document.getElementById('add_rab_qty')?.value) || 0;
    const harga = parseFloat(document.getElementById('add_rab_harga')?.value) || 0;
    const total = qty * harga;
    const preview = document.getElementById('add_rab_total_preview');
    if (preview) preview.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ============================================================
// MODAL CONTROLS: ADD REALISASI
// ============================================================
function openAddRealisasiModal() {
    document.getElementById('modalAddRealisasi').classList.remove('hidden');
}

function openAddRealisasiModalPreset(lokasi) {
    if (lokasi === 'kantor') {
        const el = document.getElementById('add_rel_lokasi_kantor');
        if (el) el.checked = true;
    } else if (lokasi === 'mess') {
        const el = document.getElementById('add_rel_lokasi_mess');
        if (el) el.checked = true;
    }
    openAddRealisasiModal();
}

function closeAddRealisasiModal() {
    document.getElementById('modalAddRealisasi').classList.add('hidden');
}

function onRabSelected(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (!selectedOption || !selectedOption.value) return;

    const lokasi = selectedOption.getAttribute('data-lokasi');
    const kelompok = selectedOption.getAttribute('data-kelompok');
    const type = selectedOption.getAttribute('data-type');
    const harga = selectedOption.getAttribute('data-harga');

    if (lokasi === 'kantor') {
        const el = document.getElementById('add_rel_lokasi_kantor');
        if (el) el.checked = true;
    } else if (lokasi === 'mess') {
        const el = document.getElementById('add_rel_lokasi_mess');
        if (el) el.checked = true;
    }

    if (kelompok) document.getElementById('add_rel_kel_barang').value = kelompok;
    if (type && type !== '-') document.getElementById('add_rel_type').value = type;
    if (harga) {
        document.getElementById('add_rel_debit').value = harga;
        calcAddRelTotal();
    }
}

function calcAddRelTotal() {
    const debit = parseFloat(document.getElementById('add_rel_debit')?.value) || 0;
    const kredit = parseFloat(document.getElementById('add_rel_kredit')?.value) || 0;
    const total = debit - kredit;
    const preview = document.getElementById('add_rel_total_preview');
    if (preview) preview.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ============================================================
// MODAL CONTROLS: EDIT RAB
// ============================================================
function openEditRabModal(id) {
    fetch(`/administrasi/capex/rab/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;

            document.getElementById('formEditRab').action = `/administrasi/capex/rab/${id}`;
            document.getElementById('edit_rab_tahun').value = d.tahun;

            if (d.lokasi === 'mess') {
                document.getElementById('edit_rab_lokasi_mess').checked = true;
            } else {
                document.getElementById('edit_rab_lokasi_kantor').checked = true;
            }

            document.getElementById('edit_rab_kel_barang').value = d.kel_barang_kapital || '';
            document.getElementById('edit_rab_type').value = d.type || '';
            document.getElementById('edit_rab_satuan').value = d.satuan || 'Unit';
            document.getElementById('edit_rab_qty').value = d.jumlah_unit || 1;
            document.getElementById('edit_rab_harga').value = d.harga_satuan || 0;
            document.getElementById('edit_rab_foto_barang').value = d.foto_barang || '';

            calcEditRabTotal();
            document.getElementById('modalEditRab').classList.remove('hidden');
        });
}

function closeEditRabModal() {
    document.getElementById('modalEditRab').classList.add('hidden');
}

function calcEditRabTotal() {
    const qty = parseFloat(document.getElementById('edit_rab_qty')?.value) || 0;
    const harga = parseFloat(document.getElementById('edit_rab_harga')?.value) || 0;
    const total = qty * harga;
    const preview = document.getElementById('edit_rab_total_preview');
    if (preview) preview.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ============================================================
// MODAL CONTROLS: EDIT REALISASI
// ============================================================
function openEditRealisasiModal(id) {
    fetch(`/administrasi/capex/realisasi/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;

            document.getElementById('formEditRealisasi').action = `/administrasi/capex/realisasi/${id}`;
            document.getElementById('edit_rel_tahun').value = d.tahun;
            document.getElementById('edit_rel_rab_id').value = d.rab_id || '';

            if (d.lokasi === 'mess') {
                document.getElementById('edit_rel_lokasi_mess').checked = true;
            } else {
                document.getElementById('edit_rel_lokasi_kantor').checked = true;
            }

            document.getElementById('edit_rel_nama_barang').value = d.nama_barang || '';
            document.getElementById('edit_rel_kel_barang').value = d.kel_barang_kapital || '';
            document.getElementById('edit_rel_type').value = d.type || '';
            document.getElementById('edit_rel_tanggal').value = d.tanggal ? d.tanggal.substring(0, 10) : '';
            document.getElementById('edit_rel_debit').value = d.debit || 0;
            document.getElementById('edit_rel_kredit').value = d.kredit || 0;
            document.getElementById('edit_rel_foto_barang').value = d.foto_barang || '';

            calcEditRelTotal();
            document.getElementById('modalEditRealisasi').classList.remove('hidden');
        });
}

function closeEditRealisasiModal() {
    document.getElementById('modalEditRealisasi').classList.add('hidden');
}

function calcEditRelTotal() {
    const debit = parseFloat(document.getElementById('edit_rel_debit')?.value) || 0;
    const kredit = parseFloat(document.getElementById('edit_rel_kredit')?.value) || 0;
    const total = debit - kredit;
    const preview = document.getElementById('edit_rel_total_preview');
    if (preview) preview.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ============================================================
// MODAL CONTROLS: DELETE
// ============================================================
function openDeleteModal(type, id, name) {
    document.getElementById('deleteItemName').textContent = name;
    if (type === 'rab') {
        document.getElementById('formDelete').action = `/administrasi/capex/rab/${id}`;
    } else {
        document.getElementById('formDelete').action = `/administrasi/capex/realisasi/${id}`;
    }
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

// ============================================================
// MODAL CONTROLS: IMPORT
// ============================================================
function openImportModal() {
    document.getElementById('modalImport').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('modalImport').classList.add('hidden');
}

// ============================================================
// IMAGE PREVIEW MODAL
// ============================================================
function showImageModal(src, title) {
    document.getElementById('modalImageSrc').src = src;
    document.getElementById('modalImageTitle').textContent = title || 'Foto Barang';
    document.getElementById('modalImagePreview').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('modalImagePreview').classList.add('hidden');
}

// ============================================================
// MODAL: TAMBAH TAHUN & SCROLL
// ============================================================
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

// ============================================================
// INITIALIZE
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    const activeTab = document.getElementById('year-tab-{{ $selectedYear }}');
    if (activeTab) {
        activeTab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
});
</script>

{{-- ============================== MODAL: TAMBAH TAHUN ============================== --}}
@can('edit_cost')
<div id="modalTambahTahun" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeTambahTahunModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden p-6 z-10 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#4B6A34] flex items-center justify-center font-bold">
                    <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#18181B]">Tambah / Pilih Tahun</h3>
                    <p class="text-[11px] text-[#71717A]">Buka atau tambahkan periode tahun baru</p>
                </div>
            </div>
            <button type="button" onclick="closeTambahTahunModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formTambahTahun" onsubmit="handleTambahTahun(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Tahun Anggaran / Periode</label>
                <input type="number" id="inputTahunBaru" min="2000" max="2099" step="1"
                       value="{{ max($years) + 1 }}" required
                       class="w-full px-3 py-2 border border-[#E5E7EB] rounded-xl text-sm font-bold text-center text-[#18181B] focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div class="flex items-center justify-center gap-2">
                <button type="button" onclick="document.getElementById('inputTahunBaru').value = {{ min($years) - 1 }}"
                        class="px-2.5 py-1 text-[11px] font-medium bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-700">
                    {{ min($years) - 1 }}
                </button>
                <button type="button" onclick="document.getElementById('inputTahunBaru').value = {{ date('Y') }}"
                        class="px-2.5 py-1 text-[11px] font-medium bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-700">
                    Tahun Ini ({{ date('Y') }})
                </button>
                <button type="button" onclick="document.getElementById('inputTahunBaru').value = {{ max($years) + 1 }}"
                        class="px-2.5 py-1 text-[11px] font-medium bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-700">
                    {{ max($years) + 1 }}
                </button>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeTambahTahunModal()"
                        class="flex-1 px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-xl hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 text-xs font-bold text-white bg-[#4B6A34] rounded-xl hover:bg-[#3d5829] shadow-sm">
                    Buka Tahun
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
@endpush
</x-admin-layout>
