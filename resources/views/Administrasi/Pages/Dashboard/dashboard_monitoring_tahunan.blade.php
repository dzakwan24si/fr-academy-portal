<x-admin-layout>

<x-slot name="title">Dashboard Monitoring Tahunan | FR Academy</x-slot>
    <x-slot name="header">Dashboard Monitoring Tahunan | FR Academy</x-slot>

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
    .fr-header-bg {
        background: linear-gradient(135deg, #1e3a17 0%, #2d5a27 50%, #3f7435 100%);
    }
    .fr-table-header {
        background-color: #2d5a27;
        color: #ffffff;
    }
    /* Custom Scrollbar for program buttons */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    @media print {
        aside, header, nav, .no-print, button, form, .filter-section {
            display: none !important;
        }
        body, main, #main-content {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .page-break {
            page-break-before: always;
        }
        .shadow-xs, .shadow-sm, .shadow-md, .shadow-lg {
            box-shadow: none !important;
        }
        .border {
            border-color: #cbd5e1 !important;
        }
    }
</style>
@endpush


<div class="space-y-6 pb-12">

    {{-- ============================== 1. HEADER BANNER (GAMBAR 1) ============================== --}}
    <div class="fr-header-bg relative overflow-hidden rounded-2xl p-6 text-white shadow-lg border border-emerald-800/40">
        {{-- Background organic deco --}}
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-10 w-40 h-40 rounded-full bg-lime-500/10 blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                    <img src="{{ asset('Logo First Resource.png') }}" alt="FR" class="w-9 h-9 object-contain drop-shadow-sm" onerror="this.src='{{ asset('favicon.ico') }}'">
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/30 text-emerald-100 border border-emerald-400/30 tracking-wider">
                            FIRST RESOURCES LEARNING CENTER (FRLC)
                        </span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <h1 class="text-xl md:text-2xl font-black tracking-wide uppercase drop-shadow-sm text-white">
                        {{ $headerTitle }}
                    </h1>
                    <p class="text-xs md:text-sm text-emerald-100/90 font-medium mt-1 flex items-center gap-2">
                        <span>Target vs realisasi program s.d. bulan ini</span>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                        <span class="text-emerald-200 font-bold">Tahun {{ $year }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                {{-- Year Filter Selector --}}
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/20">
                    <i data-lucide="calendar" class="w-4 h-4 text-emerald-200"></i>
                    <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Tahun:</span>
                    <form method="GET" action="{{ route('administrasi.dashboard-monitoring-tahunan.index') }}" class="inline-block">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="program" value="{{ $selectedProgramName }}">
                        <input type="hidden" name="month" value="{{ $selectedMonthCode }}">
                        <input type="hidden" name="region" value="{{ $selectedRegion }}">
                        <select name="year" onchange="this.form.submit()" class="bg-emerald-900/80 text-white text-xs font-bold rounded-lg px-2.5 py-1 border border-emerald-500/50 focus:outline-hidden focus:ring-2 focus:ring-emerald-400 cursor-pointer">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ (int)($year ?? 2026) === (int)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Region Filter Selector --}}
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/20">
                    <i data-lucide="map-pin" class="w-4 h-4 text-emerald-200"></i>
                    <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Region:</span>
                    <form method="GET" action="{{ route('administrasi.dashboard-monitoring-tahunan.index') }}" class="inline-block">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <input type="hidden" name="program" value="{{ $selectedProgramName }}">
                        <input type="hidden" name="month" value="{{ $selectedMonthCode }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <select name="region" onchange="this.form.submit()" class="bg-emerald-900/80 text-white text-xs font-bold rounded-lg px-2.5 py-1 border border-emerald-500/50 focus:outline-hidden focus:ring-2 focus:ring-emerald-400 cursor-pointer">
                            <option value="all" {{ ($selectedRegion === 'all' || !$selectedRegion) ? 'selected' : '' }}>Semua Region</option>
                            @foreach($regions as $reg)
                                <option value="{{ $reg }}" {{ (string)($selectedRegion ?? 'all') === (string)$reg ? 'selected' : '' }}>{{ $reg }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Print to PDF Button --}}
                <button type="button" 
                        onclick="exportToPDF()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Print ke PDF</span>
                </button>

                {{-- Reset Filter Button --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => $tab, 'year' => $year]) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-semibold backdrop-blur-sm transition-all text-white shadow-xs">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>Reset Filter</span>
                </a>

                {{-- Fullscreen Button --}}
                <button type="button" 
                        onclick="toggleDashboardFullscreen()" 
                        class="btn-toggle-fullscreen inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-700/80 hover:bg-emerald-600/90 border border-emerald-400/40 text-xs font-bold transition-all text-white shadow-xs cursor-pointer">
                    <i data-lucide="maximize-2" class="w-3.5 h-3.5 fullscreen-btn-icon"></i>
                    <span class="fullscreen-btn-text">Full Screen</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ============================== 2. FILTER BULAN (JAN - DES) REALISASI VS PROGRAM ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#1b4332] border border-emerald-100 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Filter Bulan:</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Progress Kumulatif (Januari s/d Bulan Terpilih)</p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 flex-wrap overflow-x-auto no-scrollbar py-0.5">
                {{-- Tombol Semua Bulan --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => $tab, 'category' => $category, 'program' => $selectedProgramName, 'month' => 'all', 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs {{ ($selectedMonthCode === 'all' || !$selectedMonthNum) ? 'bg-[#1b4332] text-white shadow-xs ring-2 ring-[#1b4332]/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                    Semua Bulan
                </a>

                @foreach($monthList as $mNum => $mCode)
                    @php
                        $isActiveMonth = ($selectedMonthNum === $mNum);
                    @endphp
                    <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => $tab, 'category' => $category, 'program' => $selectedProgramName, 'month' => $mCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs {{ $isActiveMonth ? 'bg-green-600 text-white shadow-xs ring-2 ring-green-600/30' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300' }}"
                       title="Bulan {{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$mNum] }}">
                        {{ $mCode }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============================== 3. FILTER TABS & KATEGORI (GAMBAR 2) ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-5">
        
        {{-- Main Tab Switcher (Program Reguler vs Program Non Reguler) --}}
        <div class="flex items-center gap-2 p-1.5 bg-slate-100/90 rounded-xl w-fit border border-slate-200 shadow-inner">
            <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'reguler', 'category' => 'all', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
               class="flex items-center gap-2.5 px-5 py-2 rounded-lg text-sm font-bold transition-all {{ $tab === 'reguler' ? 'bg-white text-green-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <i data-lucide="graduation-cap" class="w-4.5 h-4.5"></i>
                <span>Program Reguler</span>
                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $tab === 'reguler' ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">
                    {{ $regulerTotalCount }}
                </span>
            </a>

            <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'non_reguler', 'category' => 'all', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
               class="flex items-center gap-2.5 px-5 py-2 rounded-lg text-sm font-bold transition-all {{ $tab === 'non_reguler' ? 'bg-white text-green-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <i data-lucide="award" class="w-4.5 h-4.5"></i>
                <span>Program Non Reguler</span>
                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $tab === 'non_reguler' ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">
                    {{ $nonRegulerTotalCount }}
                </span>
            </a>
        </div>

        {{-- Category Cards Grid (Styled identically to Gambar 2) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @if($tab === 'reguler')
                {{-- Card: Semua Reguler --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'reguler', 'category' => 'all', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="text-left p-5 rounded-2xl border transition-all relative block {{ $category === 'all' ? 'border-green-600 bg-white shadow-md shadow-green-600/5 ring-1 ring-green-600' : 'border-slate-200 bg-white hover:border-slate-300 shadow-xs' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center {{ $category === 'all' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        @if($category === 'all')
                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        @endif
                    </div>
                    <h3 class="font-bold text-base leading-snug text-slate-800">Semua Reguler</h3>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-xs text-slate-400 font-normal">Kategori Program</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $category === 'all' ? 'bg-green-100 text-green-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                            {{ $regulerTotalCount }} Data
                        </span>
                    </div>
                </a>

                {{-- Card: Training Reguler - Staf --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'reguler', 'category' => 'staf', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="text-left p-5 rounded-2xl border transition-all relative block {{ $category === 'staf' ? 'border-green-600 bg-white shadow-md shadow-green-600/5 ring-1 ring-green-600' : 'border-slate-200 bg-white hover:border-slate-300 shadow-xs' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center {{ $category === 'staf' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                        </div>
                        @if($category === 'staf')
                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        @endif
                    </div>
                    <h3 class="font-bold text-base leading-snug text-slate-800">Training Reguler - Staf</h3>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-xs text-slate-400 font-normal">Kategori Program</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $category === 'staf' ? 'bg-green-100 text-green-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                            {{ $regulerStafCount }} Data
                        </span>
                    </div>
                </a>

                {{-- Card: Training Reguler - Mandor --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'reguler', 'category' => 'mandor', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="text-left p-5 rounded-2xl border transition-all relative block {{ $category === 'mandor' ? 'border-green-600 bg-white shadow-md shadow-green-600/5 ring-1 ring-green-600' : 'border-slate-200 bg-white hover:border-slate-300 shadow-xs' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center {{ $category === 'mandor' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        @if($category === 'mandor')
                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        @endif
                    </div>
                    <h3 class="font-bold text-base leading-snug text-slate-800">Training Reguler - Mandor</h3>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-xs text-slate-400 font-normal">Kategori Program</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $category === 'mandor' ? 'bg-green-100 text-green-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                            {{ $regulerMandorCount }} Data
                        </span>
                    </div>
                </a>
            @else
                {{-- Card: Semua Non Reguler --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'non_reguler', 'category' => 'all', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="text-left p-5 rounded-2xl border transition-all relative block {{ $category === 'all' ? 'border-green-600 bg-white shadow-md shadow-green-600/5 ring-1 ring-green-600' : 'border-slate-200 bg-white hover:border-slate-300 shadow-xs' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center {{ $category === 'all' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        @if($category === 'all')
                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        @endif
                    </div>
                    <h3 class="font-bold text-base leading-snug text-slate-800">Semua Non Reguler</h3>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-xs text-slate-400 font-normal">Kategori Program</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $category === 'all' ? 'bg-green-100 text-green-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                            {{ $nonRegulerTotalCount }} Data
                        </span>
                    </div>
                </a>

                {{-- Card: Managerial and Leadership --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'non_reguler', 'category' => 'managerial', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="text-left p-5 rounded-2xl border transition-all relative block {{ $category === 'managerial' ? 'border-green-600 bg-white shadow-md shadow-green-600/5 ring-1 ring-green-600' : 'border-slate-200 bg-white hover:border-slate-300 shadow-xs' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center {{ $category === 'managerial' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            <i data-lucide="award" class="w-5 h-5"></i>
                        </div>
                        @if($category === 'managerial')
                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        @endif
                    </div>
                    <h3 class="font-bold text-base leading-snug text-slate-800">Managerial and Leadership</h3>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-xs text-slate-400 font-normal">Kategori Program</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $category === 'managerial' ? 'bg-green-100 text-green-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                            {{ $nonRegulerManagerialCount }} Data
                        </span>
                    </div>
                </a>

                {{-- Card: Technical --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'non_reguler', 'category' => 'technical', 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="text-left p-5 rounded-2xl border transition-all relative block {{ $category === 'technical' ? 'border-green-600 bg-white shadow-md shadow-green-600/5 ring-1 ring-green-600' : 'border-slate-200 bg-white hover:border-slate-300 shadow-xs' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center {{ $category === 'technical' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        @if($category === 'technical')
                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                        @endif
                    </div>
                    <h3 class="font-bold text-base leading-snug text-slate-800">Technical</h3>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-xs text-slate-400 font-normal">Kategori Program</p>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $category === 'technical' ? 'bg-green-100 text-green-800 font-bold' : 'bg-slate-100 text-slate-600' }}">
                            {{ $nonRegulerTechnicalCount }} Data
                        </span>
                    </div>
                </a>
            @endif
        </div>

        {{-- Sub Kategori Pills for Non Reguler --}}
        @if($tab === 'non_reguler' && !empty($nonRegulerSubCategories))
            @php
                $displaySubCats = $nonRegulerSubCategories;
                if ($category === 'managerial' || strcasecmp($category, 'softskill') === 0) {
                    // Managerial cuman mempunyai 1 kategori: Softskill
                    $displaySubCats = array_values(array_filter($nonRegulerSubCategories, fn($c) => strcasecmp($c, 'softskill') === 0));
                    if (empty($displaySubCats)) $displaySubCats = ['Softskill'];
                } elseif ($category === 'technical') {
                    // Kategori Technical
                    $displaySubCats = array_values(array_filter($nonRegulerSubCategories, fn($c) => strcasecmp($c, 'softskill') !== 0));
                }
            @endphp
            <div class="flex items-center gap-2 flex-wrap pt-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Kategori:</span>
                @foreach($displaySubCats as $subCat)
                    @php
                        $isSubActive = (strcasecmp($category, $subCat) === 0 || ($category === 'managerial' && strcasecmp($subCat, 'softskill') === 0));
                        $subCount = $subCategoryCounts[$subCat] ?? ($subCat === 'Softskill' ? $nonRegulerManagerialCount : 0);
                    @endphp
                    <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => 'non_reguler', 'category' => $subCat, 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-all shadow-2xs {{ $isSubActive ? 'bg-green-700 text-white shadow-xs ring-2 ring-green-600/30' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                        <span>{{ $subCat }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full font-bold {{ $isSubActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ $subCount }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ============================== 3. FILTER TOMBOL NAMA PROGRAM LANGSUNG ============================== --}}
        <div class="pt-4 border-t border-slate-200/80">
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4 text-green-700"></i>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Pilih Program Secara Langsung:</span>
                </div>
                <span class="text-[11px] text-slate-400 font-medium">Klik tombol program di bawah untuk memfilter data spesifik</span>
            </div>

            {{-- Horizontal Wrap Button List --}}
            <div class="flex flex-wrap items-center gap-2 max-h-48 overflow-y-auto pr-1">
                {{-- Tombol Semua Program --}}
                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => $tab, 'category' => $category, 'program' => 'all', 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs {{ $selectedProgramName === 'all' || !$selectedProgramName ? 'bg-[#1b4332] text-white ring-2 ring-[#1b4332]/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                    Semua Program ({{ count($allProgramButtons) }})
                </a>

                @foreach($allProgramButtons as $pName)
                    @php
                        $isActiveBtn = ($selectedProgramName === $pName);
                    @endphp
                    <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => $tab, 'category' => $category, 'program' => $pName, 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs whitespace-nowrap {{ $isActiveBtn ? 'bg-green-600 text-white ring-2 ring-green-600/30' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 hover:border-slate-400' }}"
                       title="{{ $pName }}">
                        {{ $pName }}
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ============================== 4. 4 KPI METRIC CARDS (GAMBAR 1 & 3) ============================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        
        {{-- Card 1: Target Batch --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                    <i data-lucide="target" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Target batch</p>
                    <p class="text-3xl font-black text-slate-900 leading-tight mt-0.5">{{ $kpi['target_batch'] }}</p>
                    <p class="text-[11px] text-slate-400 font-medium">Rencana batch tahun ini</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Realisasi Batch (Dark Green Theme as in Gambar 1) --}}
        <div class="bg-[#0f3822] border border-[#1b4332] text-white rounded-2xl p-5 shadow-sm flex items-center justify-between relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-emerald-500/10 pointer-events-none"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-13 h-13 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-white">
                    <i data-lucide="clipboard-check" class="w-7 h-7 text-emerald-300"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-emerald-200 uppercase tracking-wider">Realisasi batch</p>
                    <p class="text-3xl font-black text-white leading-tight mt-0.5">{{ $kpi['realized_batch'] }}</p>
                    <p class="text-[11px] text-emerald-300/90 font-semibold mt-0.5">
                        {{ $kpi['batch_percentage'] }}% dari target
                    </p>
                </div>
            </div>
        </div>

        {{-- Card 3: Target Peserta --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Target peserta</p>
                    <p class="text-3xl font-black text-slate-900 leading-tight mt-0.5">{{ number_format($kpi['target_peserta'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-slate-400 font-medium">Kuota total peserta</p>
                </div>
            </div>
        </div>

        {{-- Card 4: Realisasi Peserta (Warm Gold/Amber Tint as in Gambar 1) --}}
        <div class="bg-[#fef9ee] border border-amber-200 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-amber-100 border border-amber-300 flex items-center justify-center text-amber-800">
                    <i data-lucide="user-check" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-amber-800/80 uppercase tracking-wider">Realisasi peserta</p>
                    <p class="text-3xl font-black text-[#92400e] leading-tight mt-0.5">{{ number_format($kpi['realized_peserta'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-[#b45309] font-bold mt-0.5">
                        {{ $kpi['peserta_percentage'] }}% dari target
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================== 5. CHART TARGET VS REALISASI (GAMBAR 1) ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 id="chartTitle" class="text-base md:text-lg font-black text-slate-900 tracking-tight">
                    Target vs realisasi peserta per program
                </h2>
                <p id="chartSubtitle" class="text-xs text-slate-500 mt-0.5">Perbandingan kuota target vs realisasi aktual peserta pelatihan</p>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                {{-- Mode Switcher (Peserta vs Batch) --}}
                <div class="flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200 shadow-2xs">
                    <button type="button" id="btnModePeserta" onclick="switchChartMode('peserta')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-green-700 shadow-xs">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        <span>Peserta</span>
                    </button>
                    <button type="button" id="btnModeBatch" onclick="switchChartMode('batch')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900">
                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        <span>Batch</span>
                    </button>
                </div>

                {{-- Legend Indicators --}}
                <div class="flex items-center gap-4 text-xs font-bold">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-3 rounded-xs bg-[#0284c7]"></span>
                        <span id="legendTargetLabel" class="text-slate-600">Target peserta</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-3 rounded-xs bg-[#059669]"></span>
                        <span id="legendRealLabel" class="text-slate-600">Realisasi peserta</span>
                    </div>
                    <div class="flex items-center gap-1.5 pl-3 border-l border-slate-300 text-slate-500">
                        <span>% capaian</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart Canvas Container --}}
        @php
            $chartHeight = max(280, count($chartData['labels']) * 42);
        @endphp
        <div class="relative w-full" style="height: {{ $chartHeight }}px;">
            <canvas id="targetRealisasiChart"></canvas>
        </div>
    </div>

    {{-- ============================== 6. TABEL RINCIAN PER PROGRAM (GAMBAR 1) ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/60">
            <div>
                <h3 class="text-base font-black text-slate-900">Rincian per program</h3>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan matriks target dan realisasi batch serta peserta</p>
            </div>
            <span class="text-xs font-bold text-slate-600 bg-white border border-slate-200 px-3 py-1 rounded-full shadow-2xs">
                Total: {{ count($activePrograms) }} Program
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="fr-table-header text-white text-xs font-extrabold uppercase tracking-wider">
                        <th class="py-3.5 px-5 text-left">Program</th>
                        <th class="py-3.5 px-3 text-center border-l border-emerald-700/60">Target Batch</th>
                        <th class="py-3.5 px-3 text-center border-l border-emerald-700/60">Realisasi Batch</th>
                        <th class="py-3.5 px-3 text-center border-l border-emerald-700/60">% Batch</th>
                        <th class="py-3.5 px-3 text-center border-l border-emerald-700/60">Target Peserta</th>
                        <th class="py-3.5 px-3 text-center border-l border-emerald-700/60">Realisasi Peserta</th>
                        <th class="py-3.5 px-4 text-center border-l border-emerald-700/60">% Peserta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($activePrograms as $idx => $prog)
                        @php
                            $rowBg = ($idx % 2 === 0) ? 'bg-white' : 'bg-slate-50/70';
                            $pctPeserta = $prog['participant_percent'];
                            $pctPesertaColor = 'text-red-600 font-bold';
                            if ($pctPeserta >= 100) {
                                $pctPesertaColor = 'text-green-700 font-extrabold';
                            } elseif ($pctPeserta > 0) {
                                $pctPesertaColor = 'text-amber-600 font-bold';
                            }
                        @endphp
                        <tr class="{{ $rowBg }} hover:bg-emerald-50/40 transition-colors">
                            <td class="py-3 px-5 font-semibold text-slate-800">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index', ['tab' => $tab, 'category' => $category, 'program' => $prog['name'], 'month' => $selectedMonthCode, 'year' => $year, 'region' => $selectedRegion]) }}" 
                                       class="hover:text-green-700 hover:underline">
                                        {{ $prog['name'] }}
                                    </a>
                                    @if(!empty($prog['is_unbudget']))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wider shrink-0">
                                            Unbudget
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center text-slate-700 border-l border-slate-100 font-medium">{{ $prog['target_batch'] }}</td>
                            <td class="py-3 px-3 text-center text-slate-700 border-l border-slate-100 font-medium">{{ $prog['realized_batch'] }}</td>
                            <td class="py-3 px-3 text-center border-l border-slate-100 font-bold {{ $prog['batch_percent'] >= 100 ? 'text-green-700' : ($prog['batch_percent'] > 0 ? 'text-amber-600' : 'text-slate-500') }}">
                                {{ $prog['batch_percent'] }}%
                            </td>
                            <td class="py-3 px-3 text-center text-slate-700 border-l border-slate-100 font-medium">{{ number_format($prog['target_participants'], 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center text-slate-700 border-l border-slate-100 font-semibold">{{ number_format($prog['realized_participants'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center border-l border-slate-100 {{ $pctPesertaColor }}">
                                {{ $prog['participant_percent'] }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 italic">Tidak ada data program yang sesuai dengan filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- Total Footer Row --}}
                @if(count($activePrograms) > 1)
                    <tfoot>
                        <tr class="bg-slate-100/90 font-extrabold text-xs text-slate-900 border-t-2 border-slate-300">
                            <td class="py-3 px-5 uppercase">TOTAL</td>
                            <td class="py-3 px-3 text-center border-l border-slate-200">{{ $kpi['target_batch'] }}</td>
                            <td class="py-3 px-3 text-center border-l border-slate-200">{{ $kpi['realized_batch'] }}</td>
                            <td class="py-3 px-3 text-center border-l border-slate-200 text-green-700">{{ $kpi['batch_percentage'] }}%</td>
                            <td class="py-3 px-3 text-center border-l border-slate-200">{{ number_format($kpi['target_peserta'], 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-center border-l border-slate-200">{{ number_format($kpi['realized_peserta'], 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center border-l border-slate-200 text-green-700">{{ $kpi['peserta_percentage'] }}%</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Note box at the bottom (as in Gambar 1) --}}
        <div class="p-4 bg-emerald-50/70 border-t border-emerald-100 flex items-center gap-2.5 text-xs text-emerald-800 font-medium">
            <div class="w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center shrink-0 text-emerald-800">
                <i data-lucide="leaf" class="w-3.5 h-3.5"></i>
            </div>
            <span><strong>Catatan:</strong> Program unbudget atau program dengan target 0 dan realisasi > 0 ditampilkan sebagai 100% capaian.</span>
        </div>
    </div>


</div>

@push('scripts')
<script>
let currentChartMode = 'peserta';
let myChart = null;

const chartLabels = @json($chartData['labels']);
const chartDataPeserta = @json($chartData['peserta']);
const chartDataBatch = @json($chartData['batch']);

window.switchChartMode = function(mode) {
    if (mode === currentChartMode) return;
    currentChartMode = mode;

    const btnPeserta = document.getElementById('btnModePeserta');
    const btnBatch = document.getElementById('btnModeBatch');
    const chartTitle = document.getElementById('chartTitle');
    const chartSubtitle = document.getElementById('chartSubtitle');
    const legendTarget = document.getElementById('legendTargetLabel');
    const legendReal = document.getElementById('legendRealLabel');

    const activeClasses = "flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-green-700 shadow-xs";
    const inactiveClasses = "flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900";

    if (mode === 'batch') {
        if (btnPeserta) btnPeserta.className = inactiveClasses;
        if (btnBatch) btnBatch.className = activeClasses;

        if (chartTitle) chartTitle.textContent = "Target vs realisasi batch per program";
        if (chartSubtitle) chartSubtitle.textContent = "Perbandingan target batch vs realisasi aktual batch pelatihan";
        if (legendTarget) legendTarget.textContent = "Target batch";
        if (legendReal) legendReal.textContent = "Realisasi batch";

        if (myChart) {
            myChart.data.datasets[0].label = 'Target batch';
            myChart.data.datasets[0].data = chartDataBatch.targets;
            myChart.data.datasets[1].label = 'Realisasi batch';
            myChart.data.datasets[1].data = chartDataBatch.realizations;
            if (myChart.options.scales.x.title) {
                myChart.options.scales.x.title.text = 'Jumlah batch';
            }
            myChart.update();
        }
    } else {
        if (btnPeserta) btnPeserta.className = activeClasses;
        if (btnBatch) btnBatch.className = inactiveClasses;

        if (chartTitle) chartTitle.textContent = "Target vs realisasi peserta per program";
        if (chartSubtitle) chartSubtitle.textContent = "Perbandingan kuota target vs realisasi aktual peserta pelatihan";
        if (legendTarget) legendTarget.textContent = "Target peserta";
        if (legendReal) legendReal.textContent = "Realisasi peserta";

        if (myChart) {
            myChart.data.datasets[0].label = 'Target peserta';
            myChart.data.datasets[0].data = chartDataPeserta.targets;
            myChart.data.datasets[1].label = 'Realisasi peserta';
            myChart.data.datasets[1].data = chartDataPeserta.realizations;
            if (myChart.options.scales.x.title) {
                myChart.options.scales.x.title.text = 'Jumlah peserta';
            }
            myChart.update();
        }
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Lucide Icons
    if (window.lucide) {
        window.lucide.createIcons();
    }

    const ctx = document.getElementById('targetRealisasiChart');
    if (ctx && chartLabels.length > 0) {
        
        // Plugin to render % Capaian at the far right of each bar group
        const rightLabelPlugin = {
            id: 'rightLabelPlugin',
            afterDatasetsDraw(chart, args, options) {
                const { ctx, chartArea: { right }, scales: { y } } = chart;
                ctx.save();
                ctx.font = 'bold 12px Inter, sans-serif';
                ctx.textAlign = 'right';
                ctx.textBaseline = 'middle';

                const activePcts = (currentChartMode === 'batch') ? chartDataBatch.percentages : chartDataPeserta.percentages;

                chartLabels.forEach((label, index) => {
                    const yPos = y.getPixelForTick(index);
                    const pct = activePcts[index] || 0;
                    
                    if (pct >= 100) {
                        ctx.fillStyle = '#15803d'; // green-700
                    } else if (pct > 0) {
                        ctx.fillStyle = '#d97706'; // amber-600
                    } else {
                        ctx.fillStyle = '#dc2626'; // red-600
                    }

                    ctx.fillText(pct + '%', right + 42, yPos);
                });
                ctx.restore();
            }
        };

        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Target peserta',
                        data: chartDataPeserta.targets,
                        backgroundColor: '#0284c7', // Vibrant Sky Blue
                        hoverBackgroundColor: '#0369a1',
                        borderRadius: 4,
                        barPercentage: 0.75,
                        categoryPercentage: 0.8,
                    },
                    {
                        label: 'Realisasi peserta',
                        data: chartDataPeserta.realizations,
                        backgroundColor: '#059669', // Vibrant Emerald Green
                        hoverBackgroundColor: '#047857',
                        borderRadius: 4,
                        barPercentage: 0.75,
                        categoryPercentage: 0.8,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        right: 55, // Give room for % capaian
                        top: 10,
                        bottom: 10,
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false,
                        },
                        ticks: {
                            font: { size: 11, weight: '500' },
                            color: '#64748b',
                            precision: 0,
                        },
                        title: {
                            display: true,
                            text: 'Jumlah peserta',
                            font: { size: 12, weight: 'bold' },
                            color: '#475569',
                            padding: { top: 10 }
                        }
                    },
                    y: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#1e293b',
                            autoSkip: false,
                            callback: function(val, index) {
                                const text = this.getLabelForValue(val);
                                return text.length > 32 ? text.substr(0, 30) + '...' : text;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false, // Custom legend in HTML
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            afterBody: function(context) {
                                const dataIndex = context[0].dataIndex;
                                const activePcts = (currentChartMode === 'batch') ? chartDataBatch.percentages : chartDataPeserta.percentages;
                                return '% Capaian: ' + (activePcts[dataIndex] || 0) + '%';
                            }
                        }
                    }
                }
            },
            plugins: [rightLabelPlugin]
        });
    }
});

// Export to PDF function
function exportToPDF() {
    // If html2pdf is available, download styled PDF; otherwise fallback to native print dialog
    if (typeof html2pdf !== 'undefined') {
        const element = document.querySelector('.space-y-6');
        if (!element) {
            window.print();
            return;
        }

        const opt = {
            margin:       [10, 10, 10, 10],
            filename:     'monitoring-tahunan-' + '{{ $tab }}' + '-{{ $selectedMonthCode }}-{{ $year }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Temporarily hide interactive filter buttons during export
        const filterPills = document.querySelector('.filter-section') || document.querySelector('.no-print');
        html2pdf().set(opt).from(element).save();
    } else {
        window.print();
    }
}
</script>
@endpush
</x-admin-layout>
