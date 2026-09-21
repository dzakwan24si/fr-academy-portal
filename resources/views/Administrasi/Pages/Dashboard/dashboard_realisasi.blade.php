<x-admin-layout>

<x-slot name="title">Dashboard Realisasi Program | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">Dashboard Realisasi Program | FR Academy Enterprise Portal</x-slot>

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush



{{-- ============================== PAGE HEADER & FILTERS ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl p-4 mb-5 shadow-xs">
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        {{-- Title Section --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#1b4332] text-white rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="chart-column-increasing" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-extrabold text-[#18181B] tracking-tight uppercase">LEARNING & DEVELOPMENT</h1>
                    <span class="text-xs font-semibold italic text-[#d97706] bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                        — "Learning Today, Leading Tomorrow"
                    </span>
                </div>
                <p class="text-xs text-[#71717A] mt-0.5">Monitoring Realisasi Program Training & Talent Development</p>
            </div>
        </div>

        {{-- Filters Section --}}
        <form method="GET" action="{{ route('administrasi.dashboard-realisasi.index') }}" id="dashboardFilterForm" class="flex items-center gap-2.5 flex-wrap">
            <input type="hidden" name="month" value="{{ $selectedMonthCode }}">

            {{-- Filter Tahun --}}
            <div class="relative">
                <select
                    name="year"
                    onchange="this.form.submit()"
                    class="border border-emerald-600 bg-emerald-50 text-emerald-900 rounded-lg px-3 py-1.5 text-xs font-bold outline-none focus:border-emerald-700 shadow-2xs cursor-pointer hover:bg-emerald-100 transition-colors"
                >
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ (int)($year ?? 2026) === (int)$y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tipe Program --}}
            <div class="relative">
                <select
                    name="program_type"
                    id="filterProgramType"
                    onchange="onProgramTypeChange()"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 text-xs font-semibold outline-none focus:border-green-600 bg-white shadow-2xs cursor-pointer hover:border-slate-400 transition-colors"
                >
                    <option value="all" {{ ($programType === 'all' || !$programType) ? 'selected' : '' }}>Semua Tipe Program</option>
                    <option value="reguler" {{ $programType === 'reguler' ? 'selected' : '' }}>Program Reguler</option>
                    <option value="non_reguler" {{ $programType === 'non_reguler' ? 'selected' : '' }}>Program Non Reguler</option>
                </select>
            </div>

            {{-- Filter Sub-Kategori / Program Category (Tampil saat Reguler / Non Reguler dipilih) --}}
            <div class="relative" id="filterCategoryWrapper" style="{{ ($programType === 'all' || !$programType) ? 'display: none;' : '' }}">
                <select
                    name="category"
                    id="filterCategory"
                    onchange="this.form.submit()"
                    class="border border-green-600 bg-emerald-50/50 rounded-lg px-3 py-1.5 text-emerald-900 text-xs font-bold outline-none focus:border-green-700 shadow-2xs cursor-pointer hover:bg-emerald-50 transition-colors"
                >
                    @if($programType === 'reguler')
                        <option value="all" {{ ($category === 'all' || !$category) ? 'selected' : '' }}>Semua Reguler (Staff & Mandor)</option>
                        <option value="staf" {{ $category === 'staf' ? 'selected' : '' }}>Program Reguler Staff</option>
                        <option value="mandor" {{ $category === 'mandor' ? 'selected' : '' }}>Program Reguler Mandor</option>
                    @elseif($programType === 'non_reguler')
                        <option value="all" {{ ($category === 'all' || !$category) ? 'selected' : '' }}>Semua Kategori Non Reguler</option>
                        <option value="Managerial and Leadership" {{ $category === 'Managerial and Leadership' || $category === 'managerial' ? 'selected' : '' }}>Managerial and Leadership</option>
                        <option value="Technical" {{ $category === 'Technical' || $category === 'technical' ? 'selected' : '' }}>Technical</option>
                        @foreach($nonRegulerCategoryOptions as $catOpt)
                            @if(!in_array($catOpt, ['Managerial and Leadership', 'Technical']))
                                <option value="{{ $catOpt }}" {{ (strcasecmp($category, $catOpt) === 0) ? 'selected' : '' }}>{{ $catOpt }}</option>
                            @endif
                        @endforeach
                    @else
                        <option value="all">Semua Kategori</option>
                    @endif
                </select>
            </div>

            {{-- Filter Nama Program (Dinamis sesuai Tipe Reguler / Non Reguler) --}}
            <div class="relative">
                <select
                    name="program"
                    id="filterProgramName"
                    onchange="this.form.submit()"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 text-xs font-semibold outline-none focus:border-green-600 bg-white shadow-2xs cursor-pointer hover:border-slate-400 transition-colors max-w-[220px]"
                >
                    <option value="all" {{ ($selectedProgram === 'all' || !$selectedProgram) ? 'selected' : '' }}>Semua Nama Program</option>
                    @foreach($availablePrograms as $pOption)
                        <option value="{{ $pOption }}" {{ (strcasecmp($selectedProgram, $pOption) === 0) ? 'selected' : '' }}>{{ $pOption }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Region (Mirip dengan Program BATCH) --}}
            <div class="relative">
                <select
                    name="region"
                    onchange="this.form.submit()"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-slate-700 text-xs font-semibold outline-none focus:border-green-600 bg-white shadow-2xs cursor-pointer hover:border-slate-400 transition-colors"
                >
                    <option value="all" {{ ($region === 'all' || !$region) ? 'selected' : '' }}>Semua Region</option>
                    @foreach($allRegions as $reg)
                        <option value="{{ $reg }}" {{ (strcasecmp($region, $reg) === 0) ? 'selected' : '' }}>{{ $reg }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Refresh / Reset Button --}}
            <a href="{{ route('administrasi.dashboard-realisasi.index') }}" class="flex items-center gap-1.5 bg-[#1b4332] hover:bg-[#2d5a27] text-white rounded-lg px-3 py-1.5 text-xs font-bold transition-all shadow-xs active:scale-95" title="Reset semua filter">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>Reset</span>
            </a>
        </form>
    </div>
</div>

{{-- ============================== FILTER BULAN (JAN - DES) KUMULATIF ============================== --}}
<div class="bg-white rounded-xl border border-slate-200/80 p-4 mb-5 shadow-xs">
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
            <a href="{{ route('administrasi.dashboard-realisasi.index', array_merge(request()->query(), ['month' => 'all'])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs {{ ($selectedMonthCode === 'all' || !$selectedMonthNum) ? 'bg-[#1b4332] text-white shadow-xs ring-2 ring-[#1b4332]/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                Semua Bulan
            </a>

            @foreach($monthList as $mNum => $mCode)
                @php
                    $isActiveMonth = ($selectedMonthNum === $mNum);
                @endphp
                <a href="{{ route('administrasi.dashboard-realisasi.index', array_merge(request()->query(), ['month' => $mCode])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs {{ $isActiveMonth ? 'bg-green-600 text-white shadow-xs ring-2 ring-green-600/30' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300' }}"
                   title="Bulan {{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$mNum] }}">
                    {{ $mCode }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<script>
    const regulerOptions = @json($regulerProgramOptions);
    const nonRegulerOptions = @json($nonRegulerProgramOptions);
    const nonRegulerCategories = @json($nonRegulerCategoryOptions);
    const allOptions = @json($availablePrograms);

    function onProgramTypeChange() {
        const type = document.getElementById('filterProgramType').value;
        const programSelect = document.getElementById('filterProgramName');
        const categoryWrapper = document.getElementById('filterCategoryWrapper');
        const categorySelect = document.getElementById('filterCategory');

        programSelect.innerHTML = '<option value="all">Semua Nama Program</option>';

        let options = allOptions;
        if (type === 'reguler') options = regulerOptions;
        else if (type === 'non_reguler') options = nonRegulerOptions;

        options.forEach(opt => {
            const el = document.createElement('option');
            el.value = opt;
            el.textContent = opt;
            programSelect.appendChild(el);
        });

        // Update Sub-category Dropdown
        categorySelect.innerHTML = '';
        if (type === 'reguler') {
            categoryWrapper.style.display = 'block';
            categorySelect.innerHTML = `
                <option value="all">Semua Reguler (Staff & Mandor)</option>
                <option value="staf">Program Reguler Staff</option>
                <option value="mandor">Program Reguler Mandor</option>
            `;
        } else if (type === 'non_reguler') {
            categoryWrapper.style.display = 'block';
            let catHtml = '<option value="all">Semua Kategori Non Reguler</option>';
            catHtml += '<option value="Managerial and Leadership">Managerial and Leadership</option>';
            catHtml += '<option value="Technical">Technical</option>';
            nonRegulerCategories.forEach(cat => {
                if (cat !== 'Managerial and Leadership' && cat !== 'Technical') {
                    catHtml += `<option value="${cat}">${cat}</option>`;
                }
            });
            categorySelect.innerHTML = catHtml;
        } else {
            categoryWrapper.style.display = 'none';
        }

        document.getElementById('dashboardFilterForm').submit();
    }
</script>

{{-- ============================== TOP ROW: 4 KPI PROGRESS CARDS ============================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

    {{-- CARD 1: PROGRESS REGULER STAFF ( MANAGEMENT TRAINEE ) --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-xs flex flex-col justify-between">
        {{-- Card Header Banner --}}
        <div class="bg-[#1b4332] text-white px-4 py-2.5 flex items-center gap-2">
            <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0"></i>
            <h3 class="text-xs font-bold uppercase tracking-wide truncate">PROGRESS REGULER STAFF ( MANAGEMENT TRAINEE )</h3>
        </div>

        <div class="p-4 flex-1 flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#71717A] uppercase text-center tracking-wider mb-1">REALISASI PROGRAM MT (PESERTA)</p>
                <div class="text-center">
                    <span class="text-4xl font-extrabold text-[#18181B] tracking-tight">{{ $regulerMt['percentage'] }}%</span>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-3">
                    <div class="w-full bg-[#E5E7EB] rounded-full h-3 overflow-hidden">
                        <div class="bg-[#1b4332] h-full rounded-full transition-all duration-500" style="width: {{ min(100, $regulerMt['percentage']) }}%"></div>
                    </div>
                    <p class="text-[10px] text-right font-bold text-[#71717A] mt-1">Target {{ $regulerMt['target'] }}%</p>
                </div>
            </div>

            {{-- Bottom Stats Grid --}}
            <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-[#F4F4F5] text-center">
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Jumlah Peserta</p>
                    <p class="text-xs font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="users" class="w-3 h-3 text-[#1b4332]"></i> {{ $regulerMt['jumlah_peserta'] }} / {{ $regulerMt['target_peserta'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">orang</span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Program Berjalan</p>
                    <p class="text-sm font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="book-open" class="w-3 h-3 text-amber-500"></i> {{ $regulerMt['program_berjalan'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">program</span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Program Selesai</p>
                    <p class="text-sm font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i> {{ $regulerMt['program_selesai'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">program</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 2: PROGRES TALENT PROGRAM --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-xs flex flex-col justify-between">
        {{-- Card Header Banner --}}
        <div class="bg-[#1b4332] text-white px-4 py-2.5 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 overflow-hidden">
                <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i>
                <h3 class="text-xs font-bold uppercase tracking-wide truncate">PROGRES TALENT PROGRAM</h3>
            </div>
            <button type="button" onclick="openTalentProgressModal()" class="p-1 hover:bg-[#2d5a27] rounded transition-colors text-white/80 hover:text-white" title="Edit Progres Materi">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        <div class="p-4 flex-1 flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#71717A] uppercase text-center tracking-wider mb-1">PROGRES MATERI TALENT</p>
                <div class="text-center">
                    <span class="text-4xl font-extrabold text-[#18181B] tracking-tight">{{ $talentProgram['percentage'] }}%</span>
                    <p class="text-[11px] font-semibold text-[#71717A] mt-0.5">{{ $talentProgram['sudah_penugasan'] }} / {{ $talentProgram['jumlah_talent'] }} orang</p>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-2.5">
                    <div class="w-full bg-[#E5E7EB] rounded-full h-3 overflow-hidden">
                        <div class="bg-[#1b4332] h-full rounded-full transition-all duration-500" style="width: {{ min(100, $talentProgram['percentage']) }}%"></div>
                    </div>
                    <p class="text-[10px] text-right font-bold text-[#71717A] mt-1">Target {{ $talentProgram['target'] }}%</p>
                </div>
            </div>

            {{-- Bottom Content: Talent Stats & Breakdown --}}
            <div class="grid grid-cols-12 gap-2 mt-3 pt-3 border-t border-[#F4F4F5] items-center">
                <div class="col-span-5 text-center border-r border-[#F4F4F5] pr-2">
                    <p class="text-[10px] font-semibold text-[#71717A] uppercase">TOTAL TALENT</p>
                    <p class="text-base font-extrabold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-[#1b4332]"></i> {{ $talentProgram['jumlah_talent'] }}
                    </p>
                    <span class="text-[9px] text-emerald-700 font-bold">{{ $talentProgram['sudah_penugasan'] }} Aktif Penugasan</span>
                </div>
                <div class="col-span-7 pl-1 text-[10px] text-[#3F3F46] space-y-0.5 font-medium leading-tight max-h-[85px] overflow-y-auto">
                    @foreach($talentProgram['breakdown'] as $item)
                        <div class="flex justify-between items-center">
                            <span class="truncate">- {{ $item['role'] }}:</span>
                            <span class="font-bold text-[#18181B] text-[9px] shrink-0 ml-1">{{ $item['count'] }} org</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 3: PROGRES SOFT SKILL --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-xs flex flex-col justify-between">
        {{-- Card Header Banner --}}
        <div class="bg-[#1b4332] text-white px-4 py-2.5 flex items-center gap-2">
            <i data-lucide="message-square" class="w-4 h-4 shrink-0"></i>
            <h3 class="text-xs font-bold uppercase tracking-wide truncate">PROGRES SOFT SKILL</h3>
        </div>

        <div class="p-4 flex-1 flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#71717A] uppercase text-center tracking-wider mb-1">REALISASI PROGRAM SOFT SKILL (PESERTA)</p>
                <div class="text-center">
                    <span class="text-4xl font-extrabold text-[#18181B] tracking-tight">{{ $softSkill['percentage'] }}%</span>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-3">
                    <div class="w-full bg-[#E5E7EB] rounded-full h-3 overflow-hidden">
                        <div class="bg-[#1b4332] h-full rounded-full transition-all duration-500" style="width: {{ min(100, $softSkill['percentage']) }}%"></div>
                    </div>
                    <p class="text-[10px] text-right font-bold text-[#71717A] mt-1">Target {{ $softSkill['target'] }}%</p>
                </div>
            </div>

            {{-- Bottom Stats Grid --}}
            <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-[#F4F4F5] text-center">
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Jumlah Peserta</p>
                    <p class="text-xs font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="user-check" class="w-3 h-3 text-[#1b4332]"></i> {{ $softSkill['peserta_realisasi'] }} / {{ $softSkill['peserta_target'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">orang</span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Jumlah Batch</p>
                    <p class="text-xs font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="layers" class="w-3 h-3 text-emerald-600"></i> {{ $softSkill['batch_realisasi'] }} / {{ $softSkill['batch_target'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">batch</span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Total Program</p>
                    <p class="text-xs font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="clipboard-list" class="w-3 h-3 text-blue-600"></i> {{ $softSkill['total_program'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">program</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 4: PROGRES TEKNIKAL --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-xs flex flex-col justify-between">
        {{-- Card Header Banner --}}
        <div class="bg-[#1b4332] text-white px-4 py-2.5 flex items-center gap-2">
            <i data-lucide="wrench" class="w-4 h-4 shrink-0"></i>
            <h3 class="text-xs font-bold uppercase tracking-wide truncate">PROGRES TEKNIKAL</h3>
        </div>

        <div class="p-4 flex-1 flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#71717A] uppercase text-center tracking-wider mb-1">REALISASI PROGRAM TEKNIKAL (PESERTA)</p>
                <div class="text-center">
                    <span class="text-4xl font-extrabold text-[#18181B] tracking-tight">{{ $teknikal['percentage'] }}%</span>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-3">
                    <div class="w-full bg-[#E5E7EB] rounded-full h-3 overflow-hidden">
                        <div class="bg-[#1b4332] h-full rounded-full transition-all duration-500" style="width: {{ min(100, $teknikal['percentage']) }}%"></div>
                    </div>
                    <p class="text-[10px] text-right font-bold text-[#71717A] mt-1">Target {{ $teknikal['target'] }}%</p>
                </div>
            </div>

            {{-- Bottom Stats Grid --}}
            <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-[#F4F4F5] text-center">
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Jumlah Peserta</p>
                    <p class="text-xs font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="user-check" class="w-3 h-3 text-[#1b4332]"></i> {{ number_format($teknikal['peserta_realisasi'], 0, ',', '.') }} / {{ number_format($teknikal['peserta_target'], 0, ',', '.') }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">orang</span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Jumlah Batch</p>
                    <p class="text-xs font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="layers" class="w-3 h-3 text-emerald-600"></i> {{ $teknikal['batch_realisasi'] }} / {{ $teknikal['batch_target'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">batch</span>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-[#71717A]">Total Program</p>
                    <p class="text-xs font-bold text-[#18181B] flex items-center justify-center gap-1 mt-0.5">
                        <i data-lucide="clipboard-list" class="w-3 h-3 text-blue-600"></i> {{ $teknikal['total_program'] }}
                    </p>
                    <span class="text-[9px] text-[#A1A1AA]">program</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ============================== BOTTOM ROW: 2 TABLES (STAF & MANDOR) & EXPANDED CHART ============================== --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

    {{-- LEFT: 2 TABLES (STAF & MANDOR) (5 Cols) --}}
    <div class="lg:col-span-5 space-y-4">

        {{-- TABLE 1: TRAINING REGULER - STAF --}}
        <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-xs flex flex-col justify-between">
            <div>
                {{-- Header Banner --}}
                <div class="bg-[#1b4332] text-white px-3 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" class="w-3.5 h-3.5 shrink-0"></i>
                        <h3 class="text-[11px] font-extrabold uppercase tracking-wide">TRAINING REGULER - STAF</h3>
                    </div>
                    <span class="text-[9px] bg-emerald-800/80 border border-emerald-600/50 text-emerald-100 font-bold px-1.5 py-0.5 rounded">
                        {{ count($mtStafTable) }} Program
                    </span>
                </div>

                {{-- Table (Compact) --}}
                <div class="overflow-x-auto max-h-56 overflow-y-auto">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-[#F9FAFB] text-[#71717A] font-bold border-b border-[#E5E7EB] text-[9px] uppercase sticky top-0 z-10">
                            <tr>
                                <th class="px-2.5 py-1.5">PROGRAM</th>
                                <th class="px-1.5 py-1.5 text-center">RENCANA</th>
                                <th class="px-1.5 py-1.5 text-center">KELAS</th>
                                <th class="px-1.5 py-1.5 text-center">RESIGN</th>
                                <th class="px-1.5 py-1.5 text-center">LULUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F4F4F5] text-[#18181B] font-medium text-[10px]">
                            @forelse($mtStafTable as $row)
                                <tr class="hover:bg-emerald-50/50 transition-colors">
                                    <td class="px-2.5 py-1.5 font-bold flex items-center gap-1 truncate max-w-[140px]" title="{{ $row['name'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#1b4332] shrink-0"></span>
                                        <span class="truncate">{{ $row['name'] }}</span>
                                    </td>
                                    <td class="px-1.5 py-1.5 text-center text-[#52525B]">{{ $row['rencana'] }}</td>
                                    <td class="px-1.5 py-1.5 text-center font-bold text-slate-800">{{ $row['kelas'] }}</td>
                                    <td class="px-1.5 py-1.5 text-center text-rose-600 font-semibold">{{ $row['resign'] }}</td>
                                    <td class="px-1.5 py-1.5 text-center text-emerald-600 font-bold">{{ $row['lulus'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-[10px] text-slate-400">Tidak ada data program staf</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Compact Footer Summary --}}
            <div class="bg-[#F9FAFB] border-t border-[#E5E7EB] px-3 py-1.5 flex items-center justify-between text-[10px]">
                <span class="font-extrabold uppercase text-[#18181B] text-[10px]">TOTAL STAF</span>
                <div class="flex items-center gap-2.5 font-bold">
                    <span class="text-slate-700">PESERTA: <span class="font-extrabold text-[#18181B]">{{ $mtStafSummary['peserta'] }} / {{ $mtStafSummary['rencana'] }}</span></span>
                    <span class="text-rose-600">RESIGN: {{ $mtStafSummary['resign'] }}</span>
                    <span class="text-emerald-600">LULUS: {{ $mtStafSummary['lulus'] }}</span>
                </div>
            </div>
        </div>

        {{-- TABLE 2: TRAINING REGULER - MANDOR --}}
        <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-xs flex flex-col justify-between">
            <div>
                {{-- Header Banner --}}
                <div class="bg-[#2d5a27] text-white px-3 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="users" class="w-3.5 h-3.5 shrink-0"></i>
                        <h3 class="text-[11px] font-extrabold uppercase tracking-wide">TRAINING REGULER - MANDOR</h3>
                    </div>
                    <span class="text-[9px] bg-emerald-900/80 border border-emerald-600/50 text-emerald-100 font-bold px-1.5 py-0.5 rounded">
                        {{ count($mtMandorTable) }} Program
                    </span>
                </div>

                {{-- Table (Compact) --}}
                <div class="overflow-x-auto max-h-56 overflow-y-auto">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-[#F9FAFB] text-[#71717A] font-bold border-b border-[#E5E7EB] text-[9px] uppercase sticky top-0 z-10">
                            <tr>
                                <th class="px-2.5 py-1.5">PROGRAM</th>
                                <th class="px-1.5 py-1.5 text-center">RENCANA</th>
                                <th class="px-1.5 py-1.5 text-center">KELAS</th>
                                <th class="px-1.5 py-1.5 text-center">RESIGN</th>
                                <th class="px-1.5 py-1.5 text-center">LULUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F4F4F5] text-[#18181B] font-medium text-[10px]">
                            @forelse($mtMandorTable as $row)
                                <tr class="hover:bg-emerald-50/50 transition-colors">
                                    <td class="px-2.5 py-1.5 font-bold flex items-center gap-1 truncate max-w-[140px]" title="{{ $row['name'] }} ({{ $row['region'] }})">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2d5a27] shrink-0"></span>
                                        <span class="truncate">{{ $row['name'] }}</span>
                                        <span class="text-[8px] font-semibold text-slate-400">({{ $row['region'] }})</span>
                                    </td>
                                    <td class="px-1.5 py-1.5 text-center text-[#52525B]">{{ $row['rencana'] }}</td>
                                    <td class="px-1.5 py-1.5 text-center font-bold text-slate-800">{{ $row['kelas'] }}</td>
                                    <td class="px-1.5 py-1.5 text-center text-rose-600 font-semibold">{{ $row['resign'] }}</td>
                                    <td class="px-1.5 py-1.5 text-center text-emerald-600 font-bold">{{ $row['lulus'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-[10px] text-slate-400">Tidak ada data program mandor</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Compact Footer Summary --}}
            <div class="bg-[#F9FAFB] border-t border-[#E5E7EB] px-3 py-1.5 flex items-center justify-between text-[10px]">
                <span class="font-extrabold uppercase text-[#18181B] text-[10px]">TOTAL MANDOR</span>
                <div class="flex items-center gap-2.5 font-bold">
                    <span class="text-slate-700">PESERTA: <span class="font-extrabold text-[#18181B]">{{ $mtMandorSummary['peserta'] }} / {{ $mtMandorSummary['rencana'] }}</span></span>
                    <span class="text-rose-600">RESIGN: {{ $mtMandorSummary['resign'] }}</span>
                    <span class="text-emerald-600">LULUS: {{ $mtMandorSummary['lulus'] }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT: EXPANDED DUAL AXIS MONTHLY CHART & YTD CARDS (7 Cols) --}}
    <div class="lg:col-span-7 bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-xs flex flex-col justify-between">
        <div>
            {{-- Header Banner --}}
            <div class="bg-[#1b4332] text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="chart-column-increasing" class="w-4 h-4 shrink-0"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wide">GRAFIK JLH PESERTA TRAINING & PROGRAM TRAINING</h3>
                </div>
                <span class="text-[10px] font-semibold bg-white/10 px-2.5 py-0.5 rounded-full">
                    Januari - Desember {{ $year }}
                </span>
            </div>

            {{-- Chart Container (Larger Height for spacious visibility) --}}
            <div class="p-5">
                <div class="h-80 sm:h-96">
                    <canvas id="monthlyTrainingChart"></canvas>
                </div>
            </div>
        </div>

        {{-- YTD Footer Cards --}}
        <div class="p-4 pt-0 grid grid-cols-2 gap-3">
            <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl p-3.5 flex items-center gap-3.5 shadow-2xs">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#1b4332] flex items-center justify-center shrink-0 shadow-2xs">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div class="text-left">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[#71717A]">TOTAL PESERTA (YTD)</p>
                    <p class="text-xl font-extrabold text-[#18181B] leading-tight mt-0.5">
                        {{ number_format($monthlyData['total_peserta_ytd'], 0, ',', '.') }} <span class="text-xs font-semibold text-[#71717A]">orang</span>
                    </p>
                </div>
            </div>

            <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl p-3.5 flex items-center gap-3.5 shadow-2xs">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-[#d97706] flex items-center justify-center shrink-0 shadow-2xs">
                    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                </div>
                <div class="text-left">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[#71717A]">TOTAL PROGRAM (YTD)</p>
                    <p class="text-xl font-extrabold text-[#18181B] leading-tight mt-0.5">
                        {{ $monthlyData['total_program_ytd'] }} <span class="text-xs font-semibold text-[#71717A]">program</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. Dual Axis Monthly Chart
        const pesertaData = @json($monthlyData['peserta']);
        const programData = @json($monthlyData['program']);

        const maxPeserta = Math.max(...pesertaData, 10);
        const maxProgram = Math.max(...programData, 1);

        // Cari rasio tertinggi program/peserta untuk memastikan garis selalu di dalam bar
        let maxRatio = 0.05;
        for (let i = 0; i < pesertaData.length; i++) {
            if (pesertaData[i] > 0 && programData[i] > 0) {
                const ratio = programData[i] / pesertaData[i];
                if (ratio > maxRatio) maxRatio = ratio;
            }
        }

        const yPesertaMax = Math.ceil((maxPeserta * 1.15) / 50) * 50;
        const yProgramMax = Math.max(20, Math.ceil((maxRatio * yPesertaMax * 1.35) / 5) * 5);

        const ctxMonthly = document.getElementById('monthlyTrainingChart').getContext('2d');
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: @json($monthlyData['labels']),
                datasets: [
                    {
                        label: 'Jumlah Peserta (Orang)',
                        data: pesertaData,
                        backgroundColor: '#1b4332',
                        hoverBackgroundColor: '#2d5a27',
                        borderRadius: 4,
                        yAxisID: 'yPeserta',
                        order: 2
                    },
                    {
                        label: 'Jumlah Program (Program)',
                        data: programData,
                        borderColor: '#ea580c',
                        backgroundColor: '#ea580c',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#ea580c',
                        pointBorderWidth: 2.5,
                        pointRadius: 4.5,
                        pointHoverRadius: 6.5,
                        type: 'line',
                        yAxisID: 'yProgram',
                        order: 1
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
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            usePointStyle: true,
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        padding: 10,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 10 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#71717A' }
                    },
                    yPeserta: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        max: yPesertaMax,
                        grid: { color: '#F4F4F5' },
                        ticks: { font: { size: 9 }, color: '#71717A' },
                        title: { display: false }
                    },
                    yProgram: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: yProgramMax,
                        grid: { drawOnChartArea: false },
                        ticks: { font: { size: 9 }, color: '#71717A' },
                        title: { display: false }
                    }
                }
            }
        });

        // 2. Evaluation Gauge/Donut Chart
        const gaugeEl = document.getElementById('evaluationGaugeChart');
        if (gaugeEl) {
            const ctxGauge = gaugeEl.getContext('2d');
            new Chart(ctxGauge, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [{{ $evaluasi['average_score'] }}, {{ $evaluasi['max_score'] - $evaluasi['average_score'] }}],
                        backgroundColor: ['#1b4332', '#E5E7EB'],
                        borderWidth: 0,
                        circumference: 240,
                        rotation: 240,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '78%',
                    plugins: {
                        tooltip: { enabled: false }
                    }
                }
            });
        }
    });
    function openTalentProgressModal() {
        const modal = document.getElementById('modalEditTalentProgress');
        if (modal) modal.classList.remove('hidden');
    }
    function closeTalentProgressModal() {
        const modal = document.getElementById('modalEditTalentProgress');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endpush

{{-- Modal Edit Progres Materi Talent --}}
<div id="modalEditTalentProgress" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base">Edit Progres Materi Talent</h3>
                    <p class="text-xs text-slate-500">Input persentase manual progres materi talent program</p>
                </div>
            </div>
            <button type="button" onclick="closeTalentProgressModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.monitoring-talent.update-progress-materi') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Persentase Progres Materi (%)</label>
                <div class="relative">
                    <input type="number" step="0.1" min="0" max="100" name="progress_materi" value="{{ $talentProgram['percentage'] }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400">%</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5">Nilai persentase ini akan disimpan dan ditampilkan pada KPI Progres Talent Program.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeTalentProgressModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

</x-admin-layout>
