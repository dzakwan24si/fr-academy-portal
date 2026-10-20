<x-admin-layout>

<x-slot name="title">VRA (Vehicle Running Account) | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">VRA (Vehicle Running Account) | FR Academy Enterprise Portal</x-slot>



{{-- ============================== ALERT NOTIFICATIONS ============================== --}}
@if(session('success'))
    <div id="alertSuccess" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs mb-5 animate-in fade-in duration-200">
        <div class="flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button onclick="document.getElementById('alertSuccess').remove()" class="text-emerald-600 hover:text-emerald-800">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div id="alertError" class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs mb-5 animate-in fade-in duration-200">
        <div class="flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
        <button onclick="document.getElementById('alertError').remove()" class="text-rose-600 hover:text-rose-800">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
@endif

{{-- ============================== 1. PAGE HEADER ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight flex items-center gap-2.5">
            <i data-lucide="truck" class="w-6 h-6 text-[#4B6A34]"></i>
            VRA (Vehicle Running Account)
        </h1>
        <p class="text-sm text-[#71717A] mt-0.5">Pengelolaan biaya operasional, efisiensi bahan bakar (KM/L), dan biaya per kilometer kendaraan</p>
    </div>

    <div class="flex flex-wrap items-center gap-2.5">
        @can('edit_cost')
        <!-- Import CSV Button -->
        <button onclick="openModal('modalImport')"
                class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs">
            <i data-lucide="upload" class="w-4 h-4 text-[#4B6A34]"></i>
            Import CSV
        </button>
        @endcan

        <!-- Template CSV Button -->
        <a href="{{ route('administrasi.vra.template') }}"
           class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs"
           title="Download Format Template Excel">
            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-blue-600"></i>
            Template Excel
        </a>

        <!-- Export CSV Button -->
        <a href="{{ route('administrasi.vra.export', ['year' => $selectedYear, 'period' => $selectedPeriod]) }}"
           class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs">
            <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i>
            Export Excel
        </a>

        @can('edit_cost')
        <!-- Delete All Button -->
        <button onclick="openModal('modalDeleteAll')"
                class="flex items-center gap-2 px-3.5 py-2 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-100 hover:border-red-300 transition-colors shadow-2xs"
                title="Hapus semua data VRA periode ini">
            <i data-lucide="trash" class="w-4 h-4 text-red-600"></i>
            Hapus Semua
        </button>

        <!-- Add Data Button -->
        <button onclick="openAddModal()"
                class="flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-xs font-semibold rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Data
        </button>
        @endcan
    </div>
</div>


{{-- ============================== 2. YEAR FILTER NAVIGATION ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-3.5 mb-5 shadow-2xs">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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
                    <a href="{{ route('administrasi.vra.index', ['year' => $y, 'period' => $selectedPeriod, 'search' => $search]) }}"
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
</div>


{{-- ============================== 3. PERIOD (MONTH) NAVIGATION ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl p-3 mb-5">
    @php
    $monthShort = [
        1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun',
        7=>'Jul', 8=>'Ags', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'
    ];
    @endphp
    <div class="flex items-center gap-1 overflow-x-auto">
        <!-- Overall Tab -->
        <a href="{{ route('administrasi.vra.index', ['year' => $selectedYear, 'period' => 'overall', 'search' => $search]) }}"
           class="flex-1 min-w-[70px] text-center py-2 rounded-lg text-xs font-semibold transition-all
                  {{ $selectedPeriod === 'overall' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#71717A] hover:bg-[#F4F4F5]' }}">
            Overall
        </a>

        @foreach($monthShort as $mNum => $mLabel)
            <a href="{{ route('administrasi.vra.index', ['year' => $selectedYear, 'period' => $mNum, 'search' => $search]) }}"
               class="flex-1 min-w-[50px] text-center py-2 rounded-lg text-xs font-semibold transition-all
                      {{ $selectedPeriod == (string)$mNum ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#71717A] hover:bg-[#F4F4F5]' }}">
                {{ $mLabel }}
            </a>
        @endforeach
    </div>
</div>

{{-- ============================== 4. PERIOD BADGE ============================== --}}
<div class="flex items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-2 bg-[#F0F5EB] border border-[#C6D9B3] text-[#4B6A34] text-xs font-bold px-4 py-2 rounded-full shadow-2xs">
        <span class="w-2 h-2 bg-[#4B6A34] rounded-full animate-pulse"></span>
        @if($selectedPeriod === 'overall')
            Akumulasi Tahunan (Overall) — {{ $selectedYear }}
        @else
            Laporan VRA — {{ $months[(int)$selectedPeriod] ?? '' }} {{ $selectedYear }}
        @endif
    </div>

    <div class="text-xs text-[#71717A] font-medium hidden sm:block">
        Total <span class="font-bold text-[#18181B]">{{ $totals['count'] }}</span> kendaraan tercatat
    </div>
</div>

{{-- ============================== 5. SUMMARY KPI CARDS ============================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    {{-- Card 1: Total Biaya Operasional --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Total Biaya</span>
            <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                <i data-lucide="wallet" class="w-3.5 h-3.5 text-emerald-600"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] font-mono">
            Rp {{ number_format($totals['total_biaya'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-[#71717A] mt-0.5">Upah + BBM + Maint + Lain</p>
    </div>

    {{-- Card 2: Total Jarak Tempuh --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Total Jarak</span>
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                <i data-lucide="gauge" class="w-3.5 h-3.5 text-blue-600"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] font-mono">
            {{ number_format($totals['km'], 0, ',', '.') }} <span class="text-xs font-normal text-[#71717A]">KM</span>
        </p>
        <p class="text-[11px] text-[#71717A] mt-0.5">Total pergerakan unit</p>
    </div>

    {{-- Card 3: Biaya per KM (VRA) --}}
    <div class="bg-white border-2 border-[#4B6A34]/40 bg-[#F0F5EB]/30 rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4B6A34]">Rasio VRA</span>
            <div class="w-7 h-7 rounded-lg bg-[#4B6A34] text-white flex items-center justify-center">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#4B6A34] font-mono">
            Rp {{ number_format($totals['avg_vra'] ?? $totals['vra'], 0, ',', '.') }} <span class="text-xs font-normal text-[#4B6A34]">/KM</span>
        </p>
        <p class="text-[11px] text-[#71717A] mt-0.5">Total Biaya / Total KM</p>
    </div>

    {{-- Card 4: Efisiensi BBM --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Efisiensi BBM</span>
            <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                <i data-lucide="fuel" class="w-3.5 h-3.5 text-amber-600"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] font-mono">
            {{ number_format($totals['avg_bbm_km_liter'] ?? $totals['bbm_km_liter'], 2, ',', '.') }} <span class="text-xs font-normal text-[#71717A]">KM/L</span>
        </p>
        <p class="text-[11px] text-[#71717A] mt-0.5">{{ number_format($totals['bbm_liter'], 1, ',', '.') }} Liter terpakai</p>
    </div>

    {{-- Card 5: Total Biaya BBM --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Biaya BBM</span>
            <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-purple-600"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] font-mono">
            Rp {{ number_format($totals['bbm'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-[#71717A] mt-0.5">Total biaya bahan bakar</p>
    </div>
</div>

{{-- ============================== 6. MAIN DATA TABLE CONTAINER ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl shadow-sm overflow-hidden flex flex-col">
    <!-- Card Header with Title & Search -->
    <div class="px-5 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-sm text-[#18181B]">
                @if($selectedPeriod === 'overall')
                    Ringkasan VRA Kumulatif — Tahun {{ $selectedYear }}
                @else
                    Data Rincian VRA — {{ $months[(int)$selectedPeriod] ?? '' }} {{ $selectedYear }}
                @endif
            </h3>
            <p class="text-xs text-[#71717A] mt-0.5">
                Menampilkan rincian seluruh atribut operasional per unit kendaraan
            </p>
        </div>

        <!-- Search input -->
        <form action="{{ route('administrasi.vra.index') }}" method="GET" class="relative w-full md:w-72">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">

            <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA] pointer-events-none">
                <i data-lucide="search" class="w-3.5 h-3.5"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Polisi / Kendaraan..."
                   class="w-full bg-white border border-[#E5E7EB] rounded-lg py-2 pl-9 pr-8 text-xs focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] text-[#18181B]">

            @if($search)
                <a href="{{ route('administrasi.vra.index', ['year' => $selectedYear, 'period' => $selectedPeriod]) }}"
                   class="absolute inset-y-0 right-3 flex items-center text-xs text-red-500 hover:text-red-700">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Table Responsive Wrapper -->
    <div class="overflow-x-auto relative">
        <table class="w-full text-xs text-left border-collapse min-w-[1300px]">
            <thead>
                <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[#71717A] font-semibold">
                    <th class="py-3 px-3.5 text-center w-10">No</th>
                    <th class="py-3 px-3.5 text-left sticky left-0 bg-[#F9FAFB] z-10 shadow-xs w-36">No. Polisi</th>
                    <th class="py-3 px-3.5 text-left sticky left-36 bg-[#F9FAFB] z-10 shadow-xs w-40">Kendaraan</th>
                    @if($selectedPeriod === 'overall')
                        <th class="py-3 px-3 text-center">Bulan Tercatat</th>
                        <th class="py-3 px-3.5 text-right">Upah Thn (Rp)</th>
                        <th class="py-3 px-3.5 text-right">BBM Thn (Rp)</th>
                        <th class="py-3 px-3.5 text-right">BBM Thn Ref (Rp)</th>
                        <th class="py-3 px-3.5 text-right">Prev. Maint Thn (Rp)</th>
                        <th class="py-3 px-3.5 text-right">Biaya Lain Thn (Rp)</th>
                        <th class="py-3 px-3.5 text-right font-bold text-[#18181B] bg-emerald-50/50">Total Biaya Thn (Rp)</th>
                        <th class="py-3 px-3 text-right">KM Awal</th>
                        <th class="py-3 px-3 text-right">KM Akhir</th>
                        <th class="py-3 px-3.5 text-right font-semibold text-[#18181B]">Total KM Thn</th>
                        <th class="py-3 px-3.5 text-right">BBM (L)</th>
                        <th class="py-3 px-3.5 text-center">KM / Liter (Avg)</th>
                        <th class="py-3 px-3.5 text-center font-bold text-[#4B6A34] bg-[#F0F5EB]/60" title="VRA = Total Biaya (s/d Desember) ÷ Total KM (s/d Desember)">VRA Thn (Rp/KM)</th>
                        <th class="py-3 px-3 text-right">Budget Thn</th>
                    @else
                        <th class="py-3 px-3 text-center">Bln</th>
                        <th class="py-3 px-3.5 text-right">Upah (Rp)</th>
                        <th class="py-3 px-3.5 text-right">BBM (Rp)</th>
                        <th class="py-3 px-3.5 text-right">BBM Thn (Rp)</th>
                        <th class="py-3 px-3.5 text-right">Prev. Maint (Rp)</th>
                        <th class="py-3 px-3.5 text-right">Biaya Lain (Rp)</th>
                        <th class="py-3 px-3.5 text-right font-bold text-[#18181B] bg-emerald-50/50">Total Biaya (Rp)</th>
                        <th class="py-3 px-3 text-right">KM Awal</th>
                        <th class="py-3 px-3 text-right">KM Akhir</th>
                        <th class="py-3 px-3.5 text-right font-semibold text-[#18181B]">Total KM</th>
                        <th class="py-3 px-3.5 text-right">BBM (L)</th>
                        <th class="py-3 px-3.5 text-center">KM / Liter</th>
                        <th class="py-3 px-3.5 text-center font-bold text-[#4B6A34] bg-[#F0F5EB]/60" title="VRA = Total Biaya Bulan ÷ KM">VRA (Rp/KM)</th>
                        <th class="py-3 px-3 text-right">Budget Thn</th>
                    @endif
                    <th class="py-3 px-3 text-center w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F4F4F5] text-[#18181B]">
                @forelse ($records as $index => $item)
                    <tr class="hover:bg-[#FAFAFA] transition-colors group">
                        <!-- No -->
                        <td class="py-3 px-3.5 text-center text-[#71717A]">
                            {{ $records->firstItem() + $index }}
                        </td>

                        <!-- No. Polisi (Sticky 1) -->
                        <td class="py-3 px-3.5 font-bold text-[#18181B] sticky left-0 bg-white group-hover:bg-[#FAFAFA] z-10 shadow-xs whitespace-nowrap">
                            {{ $item->nomor_polisi }}
                        </td>

                        <!-- Kendaraan (Sticky 2) -->
                        <td class="py-3 px-3.5 font-medium text-[#374151] sticky left-36 bg-white group-hover:bg-[#FAFAFA] z-10 shadow-xs whitespace-nowrap">
                            {{ $item->kendaraan }}
                        </td>

                        <!-- Bulan -->
                        <td class="py-3 px-3 text-center">
                            @if($selectedPeriod === 'overall')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">
                                    {{ $item->total_bulan_tercatat ?? 1 }} bln
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700">
                                    {{ $months[(int)$item->bulan] ?? $item->bulan }}
                                </span>
                            @endif
                        </td>

                        <!-- Upah -->
                        <td class="py-3 px-3.5 text-right font-mono text-[#52525B]">
                            {{ number_format($item->upah, 0, ',', '.') }}
                        </td>

                        <!-- BBM -->
                        <td class="py-3 px-3.5 text-right font-mono text-[#52525B]">
                            {{ number_format($item->BBM, 0, ',', '.') }}
                        </td>

                        <!-- BBM Tahun -->
                        <td class="py-3 px-3.5 text-right font-mono text-[#71717A]">
                            {{ number_format($item->BBM_tahun, 0, ',', '.') }}
                        </td>

                        <!-- Preventif Maintenance -->
                        <td class="py-3 px-3.5 text-right font-mono text-[#52525B]">
                            {{ number_format($item->preventif_maintenance, 0, ',', '.') }}
                        </td>

                        <!-- Biaya Lainnya -->
                        <td class="py-3 px-3.5 text-right font-mono text-[#52525B]">
                            {{ number_format($item->biaya_lainnya, 2, ',', '.') }}
                        </td>

                        <!-- Total Biaya Bulan (Highlight) -->
                        <td class="py-3 px-3.5 text-right font-mono font-bold text-[#18181B] bg-emerald-50/40">
                            {{ number_format($item->total_biaya_bulan, 2, ',', '.') }}
                        </td>

                        <!-- KM Awal -->
                        <td class="py-3 px-3 text-right font-mono text-[#71717A]">
                            {{ number_format($item->KM_awal, 0, ',', '.') }}
                        </td>

                        <!-- KM Akhir -->
                        <td class="py-3 px-3 text-right font-mono text-[#71717A]">
                            {{ number_format($item->KM_akhir, 0, ',', '.') }}
                        </td>

                        <!-- KM (Total KM Bulan / Tahun) -->
                        <td class="py-3 px-3.5 text-right font-mono font-semibold text-[#18181B]">
                            {{ number_format($item->KM, 0, ',', '.') }}
                        </td>

                        <!-- BBM Liter -->
                        <td class="py-3 px-3 text-right font-mono text-[#52525B]">
                            {{ number_format($item->BBM_liter, 2, ',', '.') }}
                        </td>

                        <!-- BBM KM / Liter (Rasio) -->
                        <td class="py-3 px-3.5 text-center font-mono">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold
                                {{ $item->BBM_KM_liter >= 8 ? 'bg-green-50 text-green-700' : ($item->BBM_KM_liter >= 6 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                {{ number_format($item->BBM_KM_liter, 2, ',', '.') }}
                            </span>
                        </td>

                        <!-- VRA (Cost / KM) -->
                        <td class="py-3 px-3.5 text-center font-mono bg-[#F0F5EB]/40">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold text-[#4B6A34] bg-[#F0F5EB] border border-[#C6D9B3]">
                                Rp {{ number_format($item->VRA, 0, ',', '.') }}
                            </span>
                        </td>

                        <!-- Budget Tahun -->
                        <td class="py-3 px-3 text-right font-mono text-[#71717A]">
                            {{ number_format($item->budget_tahun, 0, ',', '.') }}
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="showDetailModal({{ $item->vra_id }})"
                                        class="p-1 text-gray-500 hover:text-primary-600 hover:bg-gray-100 rounded transition-colors" title="Lihat Detail">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                </button>
                                @can('edit_cost')
                                <button type="button" onclick="openEditModal({{ $item->vra_id }})"
                                        class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button type="button" onclick="openDeleteModal({{ $item->vra_id }}, '{{ addslashes($item->nomor_polisi) }}')"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="18" class="py-16 text-center text-[#A1A1AA]">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                            <p class="text-sm font-semibold text-[#18181B]">Belum ada data VRA untuk periode ini</p>
                            @can('edit_cost')
                            <p class="text-xs mt-1 text-[#71717A]">Klik "+ Tambah Data" atau "Import CSV" untuk menambahkan catatan operasional kendaraan</p>
                            @endcan
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- ==================== TOTAL FOOTER ROW ==================== --}}
            @if($records->count() > 0)
                <tfoot>
                    <tr class="bg-[#F0F5EB] text-[#18181B] font-bold border-t-2 border-[#C6D9B3]">
                        <td colspan="4" class="py-3.5 px-3.5 sticky left-0 bg-[#F0F5EB] z-10 text-xs text-[#4B6A34] uppercase tracking-wide">
                            Total ({{ $totals['count'] }} Unit)
                        </td>
                        <td class="py-3.5 px-3.5 text-right font-mono text-[#18181B]">
                            {{ number_format($totals['upah'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3.5 text-right font-mono text-[#18181B]">
                            {{ number_format($totals['bbm'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3.5 text-right font-mono text-[#71717A]">
                            {{ number_format($totals['bbm_tahun'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3.5 text-right font-mono text-[#18181B]">
                            {{ number_format($totals['maintenance'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3.5 text-right font-mono text-[#18181B]">
                            {{ number_format($totals['lainnya'], 2, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3.5 text-right font-mono text-emerald-800 text-sm bg-emerald-100/60">
                            Rp {{ number_format($totals['total_biaya'], 2, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3 text-right font-mono text-[#71717A]">-</td>
                        <td class="py-3.5 px-3 text-right font-mono text-[#71717A]">-</td>
                        <td class="py-3.5 px-3.5 text-right font-mono text-[#18181B] text-sm">
                            {{ number_format($totals['km'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3 text-right font-mono text-[#18181B]">
                            {{ number_format($totals['bbm_liter'], 2, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3.5 text-center font-mono text-xs">
                            {{ number_format($totals['bbm_km_liter'], 2, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3.5 text-center font-mono text-sm text-[#4B6A34] bg-emerald-100/60">
                            Rp {{ number_format($totals['vra'], 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-3 text-right font-mono text-[#71717A]">
                            {{ number_format($totals['budget_tahun'], 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <!-- Pagination Section -->
    @if($records->hasPages())
        <div class="px-5 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB]">
            {{ $records->links('pagination::tailwind') }}
        </div>
    @endif
</div>

{{-- ================================================================
     MODALS
================================================================ --}}

@can('edit_cost')
{{-- ---- 1. MODAL: TAMBAH DATA VRA ---- --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeModal('modalAdd')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto z-10 animate-in fade-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div>
                <h2 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-[#4B6A34]"></i>
                    Tambah Data VRA
                </h2>
                <p class="text-xs text-[#71717A] mt-0.5">
                    Periode: <span class="font-bold text-[#4B6A34]">{{ $selectedPeriod === 'overall' ? 'Mei' : ($months[(int)$selectedPeriod] ?? 'Jan') }} {{ $selectedYear }}</span>
                </p>
            </div>
            <button onclick="closeModal('modalAdd')" class="p-1 hover:bg-gray-200 rounded-lg text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('administrasi.vra.store') }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">
            <input type="hidden" name="bulan" value="{{ $selectedPeriod === 'overall' ? 5 : (int)$selectedPeriod }}">

            <!-- Section 1: Data Kendaraan -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#4B6A34] mb-3 flex items-center gap-1.5">
                    <i data-lucide="truck" class="w-4 h-4"></i> 1. Identitas Kendaraan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">No. Polisi <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_polisi" required placeholder="Contoh: Hilux BM 8177 QG"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-medium uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Jenis / Nama Kendaraan <span class="text-red-500">*</span></label>
                        <input type="text" name="kendaraan" required placeholder="Contoh: Mobil Pool/Hilux"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Budget Tahun (KM)</label>
                        <input type="number" step="0.01" min="0" name="budget_tahun" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 2: Biaya Operasional (Bulan & Tahun) -->
            <div class="pt-3 border-t border-[#E5E7EB]">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#4B6A34] mb-3 flex items-center gap-1.5">
                    <i data-lucide="wallet" class="w-4 h-4"></i> 2. Biaya Operasional
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Upah (Rp)</label>
                        <input type="number" step="0.01" min="0" name="upah" id="add_upah" oninput="calcAddMetrics()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya BBM / Bulan (Rp)</label>
                        <input type="number" step="0.01" min="0" name="BBM" id="add_bbm" oninput="calcAddMetrics()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya BBM / Tahun (Rp)</label>
                        <input type="number" step="0.01" min="0" name="BBM_tahun" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Preventive Maintenance (Rp)</label>
                        <input type="number" step="0.01" min="0" name="preventif_maintenance" id="add_maint" oninput="calcAddMetrics()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Lainnya (Rp)</label>
                        <input type="number" step="0.01" min="0" name="biaya_lainnya" id="add_lainnya" oninput="calcAddMetrics()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 3: Penggunaan Kilometer & Bahan Bakar -->
            <div class="pt-3 border-t border-[#E5E7EB]">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#4B6A34] mb-3 flex items-center gap-1.5">
                    <i data-lucide="gauge" class="w-4 h-4"></i> 3. Kilometer & BBM Liter
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">KM Awal</label>
                        <input type="number" step="0.01" min="0" name="KM_awal" id="add_km_awal" oninput="calcAddKM()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">KM Akhir</label>
                        <input type="number" step="0.01" min="0" name="KM_akhir" id="add_km_akhir" oninput="calcAddKM()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Total KM (Bulan)</label>
                        <input type="number" step="0.01" min="0" name="KM" id="add_km" oninput="calcAddMetrics()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono font-bold bg-[#F9FAFB]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">BBM (Liter)</label>
                        <input type="number" step="0.01" min="0" name="BBM_liter" id="add_bbm_liter" oninput="calcAddMetrics()" placeholder="0"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- Live Calculation Preview Card -->
            <div class="bg-[#F0F5EB] border border-[#C6D9B3] rounded-xl p-4 grid grid-cols-3 gap-3 text-center">
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#71717A] block">Total Biaya (Auto)</span>
                    <span id="preview_add_total_biaya" class="text-sm font-bold text-[#18181B] font-mono">Rp 0</span>
                </div>
                <div class="border-x border-[#C6D9B3]">
                    <span class="text-[10px] uppercase font-bold text-[#71717A] block">KM / Liter (Auto)</span>
                    <span id="preview_add_km_liter" class="text-sm font-bold text-[#4B6A34] font-mono">0.00 KM/L</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#71717A] block">VRA (Rp/KM) (Auto)</span>
                    <span id="preview_add_vra" class="text-sm font-bold text-[#4B6A34] font-mono">Rp 0 / KM</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeModal('modalAdd')"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">
                    Simpan Data VRA
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ---- 2. MODAL: EDIT DATA VRA ---- --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeModal('modalEdit')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto z-10 animate-in fade-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div>
                <h2 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                    <i data-lucide="pencil" class="w-5 h-5 text-blue-600"></i>
                    Edit Data VRA
                </h2>
                <p class="text-xs text-[#71717A] mt-0.5" id="edit_periode_info">Ubah rincian data operasional kendaraan</p>
            </div>
            <button onclick="closeModal('modalEdit')" class="p-1 hover:bg-gray-200 rounded-lg text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEdit" method="POST" action="" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">

            <!-- Section 1: Data Kendaraan -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#4B6A34] mb-3 flex items-center gap-1.5">
                    <i data-lucide="truck" class="w-4 h-4"></i> 1. Identitas Kendaraan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">No. Polisi <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_polisi" id="edit_nomor_polisi" required
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-medium uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Jenis / Nama Kendaraan <span class="text-red-500">*</span></label>
                        <input type="text" name="kendaraan" id="edit_kendaraan" required
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Budget Tahun (KM)</label>
                        <input type="number" step="0.01" min="0" name="budget_tahun" id="edit_budget_tahun"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 2: Biaya Operasional -->
            <div class="pt-3 border-t border-[#E5E7EB]">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#4B6A34] mb-3 flex items-center gap-1.5">
                    <i data-lucide="wallet" class="w-4 h-4"></i> 2. Biaya Operasional
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Upah (Rp)</label>
                        <input type="number" step="0.01" min="0" name="upah" id="edit_upah" oninput="calcEditMetrics()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya BBM / Bulan (Rp)</label>
                        <input type="number" step="0.01" min="0" name="BBM" id="edit_bbm" oninput="calcEditMetrics()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya BBM / Tahun (Rp)</label>
                        <input type="number" step="0.01" min="0" name="BBM_tahun" id="edit_bbm_tahun"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Preventive Maintenance (Rp)</label>
                        <input type="number" step="0.01" min="0" name="preventif_maintenance" id="edit_maint" oninput="calcEditMetrics()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Lainnya (Rp)</label>
                        <input type="number" step="0.01" min="0" name="biaya_lainnya" id="edit_lainnya" oninput="calcEditMetrics()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 3: Kilometer & Liter -->
            <div class="pt-3 border-t border-[#E5E7EB]">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#4B6A34] mb-3 flex items-center gap-1.5">
                    <i data-lucide="gauge" class="w-4 h-4"></i> 3. Kilometer & BBM Liter
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">KM Awal</label>
                        <input type="number" step="0.01" min="0" name="KM_awal" id="edit_km_awal" oninput="calcEditKM()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">KM Akhir</label>
                        <input type="number" step="0.01" min="0" name="KM_akhir" id="edit_km_akhir" oninput="calcEditKM()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">Total KM</label>
                        <input type="number" step="0.01" min="0" name="KM" id="edit_km" oninput="calcEditMetrics()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono font-bold bg-[#F9FAFB]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1">BBM (Liter)</label>
                        <input type="number" step="0.01" min="0" name="BBM_liter" id="edit_bbm_liter" oninput="calcEditMetrics()"
                               class="w-full px-3 py-2 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                    </div>
                </div>
            </div>

            <!-- Live Calculation Preview Card -->
            <div class="bg-[#F0F5EB] border border-[#C6D9B3] rounded-xl p-4 grid grid-cols-3 gap-3 text-center">
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#71717A] block">Total Biaya (Auto)</span>
                    <span id="preview_edit_total_biaya" class="text-sm font-bold text-[#18181B] font-mono">Rp 0</span>
                </div>
                <div class="border-x border-[#C6D9B3]">
                    <span class="text-[10px] uppercase font-bold text-[#71717A] block">KM / Liter (Auto)</span>
                    <span id="preview_edit_km_liter" class="text-sm font-bold text-[#4B6A34] font-mono">0.00 KM/L</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-[#71717A] block">VRA (Rp/KM) (Auto)</span>
                    <span id="preview_edit_vra" class="text-sm font-bold text-[#4B6A34] font-mono">Rp 0 / KM</span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeModal('modalEdit')"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ---- 3. MODAL: DETAIL DATA VRA ---- --}}
<div id="modalDetail" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeModal('modalDetail')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto z-10 animate-in fade-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#F0F5EB] flex items-center justify-center text-[#4B6A34]">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#18181B]" id="detail_nopol">Nomor Polisi</h2>
                    <p class="text-xs text-[#71717A]" id="detail_kendaraan">Kendaraan</p>
                </div>
            </div>
            <button onclick="closeModal('modalDetail')" class="p-1 hover:bg-gray-200 rounded-lg text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-6 space-y-5">
            <!-- Period Badge -->
            <div class="bg-[#F0F5EB] border border-[#C6D9B3] rounded-xl p-3.5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-[#71717A]">Periode Tercatat</span>
                    <p class="text-xs font-bold text-[#4B6A34]" id="detail_periode">-</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase text-[#71717A]">Budget Tahun (KM)</span>
                    <p class="text-xs font-bold text-[#18181B] text-right font-mono" id="detail_budget_tahun">0 KM</p>
                </div>
            </div>

            <!-- Key Ratios -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 border border-[#E5E7EB] rounded-xl p-3 text-center">
                    <span class="text-[10px] font-bold uppercase text-[#71717A] block mb-1">Rasio Efisiensi BBM</span>
                    <span id="detail_km_liter" class="text-lg font-bold text-emerald-700 font-mono">0.00 KM/L</span>
                </div>
                <div class="bg-gray-50 border border-[#E5E7EB] rounded-xl p-3 text-center">
                    <span class="text-[10px] font-bold uppercase text-[#71717A] block mb-1">Rasio VRA (Biaya / KM)</span>
                    <span id="detail_vra" class="text-lg font-bold text-[#4B6A34] font-mono">Rp 0 / KM</span>
                </div>
            </div>

            <!-- Cost Breakdown Table -->
            <div class="border border-[#E5E7EB] rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 bg-[#F9FAFB] border-b border-[#E5E7EB]">
                    <span class="text-xs font-bold text-[#18181B] uppercase tracking-wider">Rincian Biaya Operasional</span>
                </div>
                <div class="p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-[#71717A]">Biaya Upah:</span>
                        <span id="detail_upah" class="font-mono font-semibold text-[#18181B]">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#71717A]">Biaya BBM (Bulan):</span>
                        <span id="detail_bbm" class="font-mono font-semibold text-[#18181B]">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#71717A]">Biaya BBM (Tahun):</span>
                        <span id="detail_bbm_tahun" class="font-mono text-[#71717A]">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#71717A]">Preventive Maintenance:</span>
                        <span id="detail_maint" class="font-mono font-semibold text-[#18181B]">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#71717A]">Biaya Lainnya:</span>
                        <span id="detail_lainnya" class="font-mono font-semibold text-[#18181B]">Rp 0</span>
                    </div>
                    <div class="border-t border-[#E5E7EB] pt-2.5 flex justify-between font-bold text-sm">
                        <span class="text-[#4B6A34]">Total Biaya Operasional:</span>
                        <span id="detail_total_biaya" class="font-mono text-emerald-800">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Kilometer & Fuel Breakdown -->
            <div class="border border-[#E5E7EB] rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 bg-[#F9FAFB] border-b border-[#E5E7EB]">
                    <span class="text-xs font-bold text-[#18181B] uppercase tracking-wider">Kilometer & Bahan Bakar</span>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[#71717A] block">KM Awal:</span>
                        <span id="detail_km_awal" class="font-mono font-semibold text-[#18181B]">0 KM</span>
                    </div>
                    <div>
                        <span class="text-[#71717A] block">KM Akhir:</span>
                        <span id="detail_km_akhir" class="font-mono font-semibold text-[#18181B]">0 KM</span>
                    </div>
                    <div>
                        <span class="text-[#71717A] block">Jarak Tempuh (KM):</span>
                        <span id="detail_km" class="font-mono font-bold text-[#18181B]">0 KM</span>
                    </div>
                    <div>
                        <span class="text-[#71717A] block">Volume BBM (Liter):</span>
                        <span id="detail_bbm_liter" class="font-mono font-bold text-[#18181B]">0 Liter</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB] flex justify-end">
            <button onclick="closeModal('modalDetail')"
                    class="px-4 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829]">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ---- 4. MODAL: IMPORT CSV ---- --}}
<div id="modalImport" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeModal('modalImport')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden z-10 animate-in fade-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#18181B]">Import Data CSV VRA</h2>
                    <p class="text-xs text-[#71717A]">Unggah file CSV untuk memasukkan data VRA secara massal</p>
                </div>
            </div>
            <button onclick="closeModal('modalImport')" class="p-1 hover:bg-gray-200 rounded-lg text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.vra.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <input type="hidden" name="period" value="{{ $selectedPeriod }}">

            <!-- File Upload Dropzone -->
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-2">Pilih File CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-[#CBD5E1] hover:border-[#4B6A34] rounded-xl p-6 text-center cursor-pointer transition-colors bg-[#F9FAFB]"
                     onclick="document.getElementById('csvFileInput').click()">
                    <i data-lucide="file-spreadsheet" class="w-10 h-10 mx-auto text-[#4B6A34] mb-2"></i>
                    <p class="text-xs font-semibold text-[#18181B]" id="fileNameDisplay">Klik atau seret file CSV ke sini</p>
                    <p class="text-[11px] text-[#71717A] mt-1">Format: .csv, .txt (Maks 10 MB)</p>
                    <input type="file" name="csv_file" id="csvFileInput" accept=".csv,text/csv,text/plain" required class="hidden"
                           onchange="handleFileSelected(this)">
                </div>
            </div>

            <!-- Import Mode Selection -->
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-2">Mode Import</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-start gap-2.5 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:bg-[#F9FAFB] transition-colors has-[:checked]:border-[#4B6A34] has-[:checked]:bg-[#F0F5EB]/30">
                        <input type="radio" name="import_mode" value="append" checked class="mt-0.5 text-[#4B6A34] focus:ring-[#4B6A34]">
                        <div>
                            <span class="text-xs font-bold text-[#18181B] block">Tambah / Update</span>
                            <span class="text-[11px] text-[#71717A]">Update data jika no. polisi & periode sama</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-2.5 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:bg-[#F9FAFB] transition-colors has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30">
                        <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="text-xs font-bold text-red-600 block">Timpa Seluruhnya</span>
                            <span class="text-[11px] text-[#71717A]">Hapus semua data VRA lalu isi dari CSV</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Columns Supported Info -->
            <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-3 text-[11px] text-[#334155] space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-blue-900 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-blue-600"></i> Struktur Kolom CSV
                    </span>
                    <a href="{{ route('administrasi.vra.template') }}" class="text-blue-700 hover:underline font-semibold flex items-center gap-1">
                        Download Template <i data-lucide="arrow-down-to-line" class="w-3 h-3"></i>
                    </a>
                </div>
                <p class="text-[#64748B] font-mono text-[10px] break-all leading-relaxed">
                    vra_id, nomor_polisi, kendaraan, bulan, upah, BBM, BBM_tahun, preventif_maintenance, biaya_lainnya, total_biaya_bulan, KM_awal, KM_akhir, KM, BBM_liter, BBM_KM_liter, VRA, budget_tahun
                </p>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeModal('modalImport')"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm flex items-center gap-2">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i> Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ---- 5. MODAL: DELETE CONFIRMATION ---- --}}
<div id="modalDelete" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeModal('modalDelete')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center z-10 animate-in fade-in duration-150">
        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="trash-2" class="w-7 h-7 text-red-500"></i>
        </div>
        <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Data VRA</h3>
        <p class="text-xs text-[#71717A] mb-1">Apakah Anda yakin ingin menghapus data kendaraan berikut?</p>
        <p class="text-sm font-bold text-[#18181B] mb-5 font-mono" id="delete_nopol_label">—</p>
        <p class="text-[11px] text-red-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeModal('modalDelete')"
                    class="flex-1 px-4 py-2.5 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                Batal
            </button>
            <form id="formDelete" method="POST" action="" class="flex-1">
                @csrf
                @method('DELETE')
                <input type="hidden" name="period" value="{{ $selectedPeriod }}">
                <button type="submit"
                        class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ---- 6. MODAL: DELETE ALL VRA ---- --}}
<div id="modalDeleteAll" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeModal('modalDeleteAll')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center z-10 animate-in fade-in duration-150">
        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
            <i data-lucide="alert-octagon" class="w-7 h-7"></i>
        </div>
        <h3 class="text-base font-bold text-[#18181B] mb-1">Hapus Semua Data VRA?</h3>
        <p class="text-xs text-[#71717A] mb-4">
            Tindakan ini akan menghapus <strong>seluruh data VRA</strong> pada periode:
            <strong class="text-red-600">{{ $selectedPeriod === 'overall' ? 'Seluruh Tahun ' . $selectedYear : ($months[(int)$selectedPeriod] ?? 'Bulan ' . $selectedPeriod) . ' ' . $selectedYear }}</strong>.
        </p>
        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-[11px] text-red-800 text-left mb-5">
            <p class="font-bold flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i> Perhatian:</p>
            <p class="mt-0.5">Data yang telah dihapus tidak dapat dikembalikan kecuali Anda melakukan import ulang file CSV.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeModal('modalDeleteAll')"
                    class="flex-1 px-4 py-2.5 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                Batal
            </button>
            <form method="POST" action="{{ route('administrasi.vra.destroyAll', ['year' => $selectedYear, 'period' => $selectedPeriod]) }}" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                    Ya, Hapus Semua
                </button>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Modal Tambah Tahun --}}
@can('edit_cost')
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
@endcan

@push('scripts')
<script>
// ============================================================
// MODAL CONTROLLERS
// ============================================================
function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('hidden');
        if (window.lucide) lucide.createIcons();
    }
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileNameDisplay').innerText = input.files[0].name;
    }
}

// ============================================================
// ADD MODAL LIVE CALCULATION
// ============================================================
function calcAddKM() {
    const kmAwal = parseFloat(document.getElementById('add_km_awal').value) || 0;
    const kmAkhir = parseFloat(document.getElementById('add_km_akhir').value) || 0;
    if (kmAkhir > kmAwal) {
        document.getElementById('add_km').value = (kmAkhir - kmAwal).toFixed(2);
    }
    calcAddMetrics();
}

function calcAddMetrics() {
    const upah = parseFloat(document.getElementById('add_upah').value) || 0;
    const bbm = parseFloat(document.getElementById('add_bbm').value) || 0;
    const maint = parseFloat(document.getElementById('add_maint').value) || 0;
    const lainnya = parseFloat(document.getElementById('add_lainnya').value) || 0;
    const km = parseFloat(document.getElementById('add_km').value) || 0;
    const bbmLiter = parseFloat(document.getElementById('add_bbm_liter').value) || 0;

    const totalBiaya = upah + bbm + maint + lainnya;
    const kmLiter = bbmLiter > 0 ? (km / bbmLiter) : 0;
    const vra = km > 0 ? (totalBiaya / km) : 0;

    document.getElementById('preview_add_total_biaya').innerText = 'Rp ' + Math.round(totalBiaya).toLocaleString('id-ID');
    document.getElementById('preview_add_km_liter').innerText = kmLiter.toFixed(2) + ' KM/L';
    document.getElementById('preview_add_vra').innerText = 'Rp ' + Math.round(vra).toLocaleString('id-ID') + ' / KM';
}

function openAddModal() {
    openModal('modalAdd');
    calcAddMetrics();
}

// ============================================================
// EDIT MODAL LIVE CALCULATION
// ============================================================
function calcEditKM() {
    const kmAwal = parseFloat(document.getElementById('edit_km_awal').value) || 0;
    const kmAkhir = parseFloat(document.getElementById('edit_km_akhir').value) || 0;
    if (kmAkhir > kmAwal) {
        document.getElementById('edit_km').value = (kmAkhir - kmAwal).toFixed(2);
    }
    calcEditMetrics();
}

function calcEditMetrics() {
    const upah = parseFloat(document.getElementById('edit_upah').value) || 0;
    const bbm = parseFloat(document.getElementById('edit_bbm').value) || 0;
    const maint = parseFloat(document.getElementById('edit_maint').value) || 0;
    const lainnya = parseFloat(document.getElementById('edit_lainnya').value) || 0;
    const km = parseFloat(document.getElementById('edit_km').value) || 0;
    const bbmLiter = parseFloat(document.getElementById('edit_bbm_liter').value) || 0;

    const totalBiaya = upah + bbm + maint + lainnya;
    const kmLiter = bbmLiter > 0 ? (km / bbmLiter) : 0;
    const vra = km > 0 ? (totalBiaya / km) : 0;

    document.getElementById('preview_edit_total_biaya').innerText = 'Rp ' + Math.round(totalBiaya).toLocaleString('id-ID');
    document.getElementById('preview_edit_km_liter').innerText = kmLiter.toFixed(2) + ' KM/L';
    document.getElementById('preview_edit_vra').innerText = 'Rp ' + Math.round(vra).toLocaleString('id-ID') + ' / KM';
}

function openEditModal(id) {
    fetch(`/administrasi/vra/${id}`)
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                const d = response.data;
                document.getElementById('formEdit').action = `/administrasi/vra/${id}`;
                document.getElementById('edit_nomor_polisi').value = d.nomor_polisi || '';
                document.getElementById('edit_kendaraan').value = d.kendaraan || '';
                document.getElementById('edit_budget_tahun').value = d.budget_tahun || 0;
                document.getElementById('edit_upah').value = d.upah || 0;
                document.getElementById('edit_bbm').value = d.BBM || 0;
                document.getElementById('edit_bbm_tahun').value = d.BBM_tahun || 0;
                document.getElementById('edit_maint').value = d.preventif_maintenance || 0;
                document.getElementById('edit_lainnya').value = d.biaya_lainnya || 0;
                document.getElementById('edit_km_awal').value = d.KM_awal || 0;
                document.getElementById('edit_km_akhir').value = d.KM_akhir || 0;
                document.getElementById('edit_km').value = d.KM || 0;
                document.getElementById('edit_bbm_liter').value = d.BBM_liter || 0;

                calcEditMetrics();
                openModal('modalEdit');
            }
        })
        .catch(err => console.error('Error fetching VRA detail:', err));
}

// ============================================================
// DETAIL MODAL
// ============================================================
function showDetailModal(id) {
    fetch(`/administrasi/vra/${id}`)
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data) {
                const d = response.data;
                const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                document.getElementById('detail_nopol').innerText = d.nomor_polisi || '-';
                document.getElementById('detail_kendaraan').innerText = d.kendaraan || '-';
                document.getElementById('detail_periode').innerText = (monthNames[d.bulan] || d.bulan) + ' ' + d.tahun;
                document.getElementById('detail_budget_tahun').innerText = Number(d.budget_tahun || 0).toLocaleString('id-ID') + ' KM';

                document.getElementById('detail_km_liter').innerText = Number(d.BBM_KM_liter || 0).toFixed(2) + ' KM/L';
                document.getElementById('detail_vra').innerText = 'Rp ' + Number(d.VRA || 0).toLocaleString('id-ID') + ' / KM';

                document.getElementById('detail_upah').innerText = 'Rp ' + Number(d.upah || 0).toLocaleString('id-ID');
                document.getElementById('detail_bbm').innerText = 'Rp ' + Number(d.BBM || 0).toLocaleString('id-ID');
                document.getElementById('detail_bbm_tahun').innerText = 'Rp ' + Number(d.BBM_tahun || 0).toLocaleString('id-ID');
                document.getElementById('detail_maint').innerText = 'Rp ' + Number(d.preventif_maintenance || 0).toLocaleString('id-ID');
                document.getElementById('detail_lainnya').innerText = 'Rp ' + Number(d.biaya_lainnya || 0).toLocaleString('id-ID');
                document.getElementById('detail_total_biaya').innerText = 'Rp ' + Number(d.total_biaya_bulan || 0).toLocaleString('id-ID');

                document.getElementById('detail_km_awal').innerText = Number(d.KM_awal || 0).toLocaleString('id-ID') + ' KM';
                document.getElementById('detail_km_akhir').innerText = Number(d.KM_akhir || 0).toLocaleString('id-ID') + ' KM';
                document.getElementById('detail_km').innerText = Number(d.KM || 0).toLocaleString('id-ID') + ' KM';
                document.getElementById('detail_bbm_liter').innerText = Number(d.BBM_liter || 0).toFixed(2) + ' Liter';

                openModal('modalDetail');
            }
        })
        .catch(err => console.error('Error fetching VRA detail:', err));
}

// ============================================================
// DELETE MODAL
// ============================================================
function openDeleteModal(id, nopol) {
    document.getElementById('delete_nopol_label').innerText = nopol;
    document.getElementById('formDelete').action = `/administrasi/vra/${id}`;
    openModal('modalDelete');
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
</script>
@endpush
</x-admin-layout>
