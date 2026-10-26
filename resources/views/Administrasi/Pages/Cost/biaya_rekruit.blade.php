<x-admin-layout>

<x-slot name="title">Biaya Rekruit | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">Biaya Rekruit | FR Academy Enterprise Portal</x-slot>



{{-- ============================== PAGE HEADER ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight flex items-center gap-2">
            <i data-lucide="user-plus" class="w-6 h-6 text-[#4B6A34]"></i>
            Biaya Rekruit
        </h1>
        <p class="text-sm text-[#71717A] mt-0.5">Manage and monitor recruitment-related expenses, accommodation, travel, and MCU.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
            @can('edit_cost')
        <!-- Import CSV Button -->
        <button onclick="document.getElementById('modalImport').classList.remove('hidden')"
                class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs">
            <i data-lucide="upload" class="w-4 h-4 text-[#4B6A34]"></i>
            Import CSV
        </button>
        @endcan

            <!-- Template CSV Button -->
            <a href="{{ route('administrasi.biaya-rekruit.template') }}"
            class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs"
            title="Download Format Template Excel">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-blue-600"></i>
                Template Excel
        </a>

        <!-- Export Report Button -->
        <a href="{{ route('administrasi.biaya-rekruit.export', ['year' => $selectedYear, 'month' => $selectedMonth]) }}"
           class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs">
            <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i>
            Export Excel
        </a>

        @can('edit_cost')
        <!-- Delete All Button -->
        <button onclick="document.getElementById('modalDeleteAll').classList.remove('hidden')"
                class="flex items-center gap-2 px-3.5 py-2 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-100 hover:border-red-300 transition-colors shadow-2xs"
                title="Hapus semua data Biaya Rekruit">
            <i data-lucide="trash" class="w-4 h-4 text-red-600"></i>
            Hapus Semua
        </button>

        <!-- Add Biaya Button -->
        <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-xs font-semibold rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Biaya
        </button>
        @endcan
    </div>
</div>

{{-- ============================== YEAR FILTER NAVIGATION ============================== --}}
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
                <a href="{{ route('administrasi.biaya-rekruit.index', ['year' => 'all', 'month' => $selectedMonth, 'search' => $search]) }}"
                   class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $selectedYear === 'all' ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
                    Semua Tahun
                </a>
                @foreach ($years as $year)
                    <a href="{{ route('administrasi.biaya-rekruit.index', ['year' => $year, 'month' => $selectedMonth, 'search' => $search]) }}"
                       id="year-tab-{{ $year }}"
                       class="px-4 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ (string)$selectedYear === (string)$year ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
                        {{ $year }}
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

{{-- ============================== MONTH FILTER NAVIGATION ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-3.5 mb-5 shadow-2xs">
    <div class="flex items-center gap-1.5 overflow-x-auto py-0.5 px-1 scroll-smooth" style="scrollbar-width: thin;">
        <div class="flex items-center gap-1.5 px-2 text-[#71717A] shrink-0">
            <i data-lucide="filter" class="w-4 h-4 text-[#4B6A34]"></i>
            <span class="text-xs font-bold text-[#18181B] uppercase tracking-wider">Bulan:</span>
        </div>
        <a href="{{ route('administrasi.biaya-rekruit.index', array_merge(request()->query(), ['month' => 'all'])) }}"
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $selectedMonth === 'all' ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
            Semua Bulan
        </a>
        @foreach($months as $num => $label)
            <a href="{{ route('administrasi.biaya-rekruit.index', array_merge(request()->query(), ['month' => $num])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ (string)$selectedMonth === (string)$num ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- ============================== SUMMARY CARDS + SEARCH ============================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    {{-- Card 1: Total Biaya --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs relative overflow-hidden flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Total Biaya Rekruit</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-3.5 h-3.5 text-emerald-600"></i>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-[#18181B] font-mono">Rp {{ number_format($summary['total_biaya'], 0, ',', '.') }}</p>
        </div>
        <div class="mt-2 text-[10px] text-[#71717A] flex items-center gap-1">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>{{ $selectedMonth !== 'all' ? 'Bulan ' . ($months[$selectedMonth] ?? $selectedMonth) : 'Tahun ' . $summary['target_year'] }}</span>
        </div>
    </div>

    {{-- Card 2: Jumlah Transaksi --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs relative overflow-hidden flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Jumlah Transaksi</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-3.5 h-3.5 text-blue-600"></i>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-[#18181B] font-mono">{{ number_format($summary['total_transaksi']) }}</p>
        </div>
        <div class="mt-2 text-[10px] text-[#71717A]">
            <span>Item Transaksi Tercatat</span>
        </div>
    </div>

    {{-- Card 3: Peserta Rekrut (Reguler) --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs relative overflow-hidden flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-800">Peserta Rekrut (Reguler)</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-indigo-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-xl sm:text-2xl font-bold text-indigo-700 font-mono">{{ number_format($summary['peserta_join']) }}</p>
                <span class="text-xs font-semibold text-slate-500">Orang</span>
            </div>
        </div>
        <div class="mt-2 flex items-center justify-between pt-1 border-t border-slate-100">
            <span class="text-[10px] text-slate-500 font-medium">Tahun {{ $summary['target_year'] }} (Non-Carry Over)</span>
            @can('edit_cost')
            <button type="button" onclick="openEditPesertaModal()"
                    class="text-[10px] font-bold text-[#4B6A34] hover:text-[#3d5829] flex items-center gap-1 hover:underline">
                <i data-lucide="edit-3" class="w-3 h-3"></i>
                <span>Ubah</span>
            </button>
            @endcan
        </div>
    </div>

    {{-- Card 4: Biaya Rekruit / Orang (Tahunan) --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs relative overflow-hidden flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-amber-800">Biaya Rekruit / Orang</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i data-lucide="badge-percent" class="w-3.5 h-3.5 text-amber-600"></i>
                </div>
            </div>
            @if($summary['peserta_join'] > 0)
                <p class="text-xl sm:text-2xl font-bold text-amber-700 font-mono">Rp {{ number_format($summary['biaya_per_orang'], 0, ',', '.') }}</p>
            @else
                <p class="text-base sm:text-lg font-bold text-slate-400">Belum Diset</p>
            @endif
        </div>
        <div class="mt-2 text-[10px] text-slate-500 truncate">
            @if($summary['peserta_join'] > 0)
                <span>Rp {{ number_format($summary['total_biaya_tahunan'], 0, ',', '.') }} ÷ {{ $summary['peserta_join'] }} org</span>
            @else
                <span>Klik Ubah pada kartu Peserta</span>
            @endif
        </div>
    </div>
</div>

{{-- Search & Periode Bar --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-3 mb-5 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
    <div class="flex items-center gap-2 text-xs font-semibold text-[#71717A]">
        <i data-lucide="info" class="w-4 h-4 text-[#4B6A34]"></i>
        <span>Menampilkan Biaya Rekrutmen: <strong class="text-[#18181B]">{{ $selectedYear !== 'all' ? 'Tahun ' . $selectedYear : 'Semua Tahun' }}</strong>{{ $selectedMonth !== 'all' ? ' (Bulan ' . ($months[$selectedMonth] ?? $selectedMonth) . ')' : '' }}</span>
    </div>
    <form method="GET" action="{{ route('administrasi.biaya-rekruit.index') }}" class="w-full sm:w-auto sm:min-w-[320px]">
        <input type="hidden" name="year" value="{{ $selectedYear }}">
        <input type="hidden" name="month" value="{{ $selectedMonth }}">
        <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA]">
                <i data-lucide="search" class="w-4 h-4"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search nama / referensi / PIC..."
                   class="w-full pl-10 pr-8 py-2 border border-[#E5E7EB] rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] bg-[#F9FAFB] text-[#18181B]">
            @if($search)
                <a href="{{ route('administrasi.biaya-rekruit.index', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" class="absolute inset-y-0 right-3 flex items-center text-xs text-red-500 hover:text-red-700">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- ============================== FLASH MESSAGE ============================== --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs px-4 py-3 rounded-lg shadow-2xs" id="flash-msg">
    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
    <span>{{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

{{-- ============================== GROUPED TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-2xs">
    <table class="w-full text-xs">
        <thead>
            <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB]">
                <th class="px-5 py-3.5 text-left text-xs font-bold text-[#71717A] uppercase tracking-wider w-32">Tanggal</th>
                <th class="px-5 py-3.5 text-left text-xs font-bold text-[#71717A] uppercase tracking-wider">Nama / Referensi</th>
                <th class="px-5 py-3.5 text-right text-xs font-bold text-[#71717A] uppercase tracking-wider w-44">Total Biaya</th>
                <th class="px-5 py-3.5 text-center text-xs font-bold text-[#71717A] uppercase tracking-wider w-24">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grouped as $jenis => $pjGroups)

                {{-- LEVEL 1: Jenis Rekruit — GREEN row --}}
                <tr class="bg-[#4B6A34]">
                    <td colspan="4" class="px-5 py-2.5 text-xs font-bold text-white tracking-wide">
                        {{ $jenis }}
                    </td>
                </tr>

                @foreach($pjGroups as $pj => $items)
                    {{-- LEVEL 2: Penanggung Jawab — AMBER row --}}
                    <tr class="bg-amber-400">
                        <td colspan="4" class="px-5 py-2 text-xs font-semibold text-white">
                            {{ $pj }}
                        </td>
                    </tr>

                    {{-- LEVEL 3: Item rows --}}
                    @foreach($items as $row)
                    <tr class="border-b border-[#F4F4F5] hover:bg-[#FAFAFA] transition-colors">
                        <td class="px-5 py-3 text-xs text-[#52525B] whitespace-nowrap font-mono">
                            {{ $row->tanggal?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-xs text-[#18181B] font-medium leading-relaxed">
                            {{ $row->nama }}
                        </td>
                        <td class="px-5 py-3 text-right text-xs font-semibold text-[#18181B] font-mono whitespace-nowrap">
                            Rp {{ number_format($row->total_biaya, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @can('edit_cost')
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ $row->id_biaya_rekruit }})"
                                        class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $row->id_biaya_rekruit }}, '{{ addslashes($row->nama) }}')"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                @endforeach

            @empty
            <tr>
                <td colspan="4" class="px-5 py-16 text-center text-[#A1A1AA]">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                    <p class="text-sm font-semibold text-[#18181B]">Belum ada data biaya rekruit</p>
                    @can('edit_cost')
                    <p class="text-xs mt-1 text-[#71717A]">Klik "+ Tambah Biaya" atau "Import CSV" untuk menambahkan data</p>
                    @endcan
                </td>
            </tr>
            @endforelse
        </tbody>

        {{-- GRAND TOTAL FOOTER --}}
        @if(count($grouped) > 0)
        <tfoot>
            <tr class="bg-blue-50 border-t-2 border-blue-200">
                <td colspan="2" class="px-5 py-3.5 text-xs font-bold text-blue-800 uppercase tracking-wide text-right">
                    Total Biaya Rekruit
                </td>
                <td class="px-5 py-3.5 text-right text-sm font-bold text-blue-700 font-mono">
                    Rp {{ number_format($summary['total_biaya'], 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- ================================================================
     MODALS
================================================================ --}}

@can('edit_cost')
{{-- ---- 1. MODAL: IMPORT CSV ---- --}}
<div id="modalImport" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="document.getElementById('modalImport').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden z-10 animate-in fade-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#18181B]">Import CSV Biaya Rekruit</h2>
                    <p class="text-xs text-[#71717A]">Unggah file CSV pengeluaran rekrutmen</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalImport').classList.add('hidden')" class="p-1 hover:bg-gray-200 rounded-lg text-[#71717A]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.biaya-rekruit.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

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
                            <span class="text-[11px] text-[#71717A]">Update data jika ID/nama sama</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-2.5 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:bg-[#F9FAFB] transition-colors has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30">
                        <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="text-xs font-bold text-red-600 block">Timpa Seluruhnya</span>
                            <span class="text-[11px] text-[#71717A]">Hapus semua data biaya rekruit lalu isi dari CSV</span>
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
                    <a href="{{ route('administrasi.biaya-rekruit.template') }}" class="text-blue-700 hover:underline font-semibold flex items-center gap-1">
                        Download Template <i data-lucide="arrow-down-to-line" class="w-3 h-3"></i>
                    </a>
                </div>
                <p class="text-[#64748B] font-mono text-[10px] break-all leading-relaxed">
                    cost_rekruit_id, tanggal, nama, pic_rekruiter, total_biaya
                </p>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
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

{{-- ---- 2. MODAL: ADD ---- --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalAdd').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 z-10 animate-in fade-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <h2 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-[#4B6A34]"></i>
                Tambah Biaya Rekruit
            </h2>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form method="POST" action="{{ route('administrasi.biaya-rekruit.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear !== 'all' ? $selectedYear : date('Y') }}">

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama / Referensi <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Ticket Pekanbaru – Jakarta"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Total Biaya (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="total_biaya" required min="0" step="1" placeholder="0"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
            </div>

            <div class="border-t border-[#E5E7EB] pt-4 space-y-3">
                <p class="text-[11px] text-[#A1A1AA] font-medium">Informasi Klasifikasi &amp; PIC</p>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis Rekruit</label>
                    <input type="text" name="jenis_rekruit" placeholder="Kosongkan untuk deteksi otomatis"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">PIC / Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" placeholder="Contoh: Josefhine E / Benyamin Basuki Yulianto"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-4 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---- 3. MODAL: EDIT ---- --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalEdit').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 z-10 animate-in fade-in duration-150">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <h2 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                <i data-lucide="pencil" class="w-5 h-5 text-blue-600"></i>
                Edit Biaya Rekruit
            </h2>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form id="formEdit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="edit_tanggal" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama / Referensi <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="edit_nama" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Total Biaya (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="total_biaya" id="edit_total_biaya" required min="0" step="1"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
            </div>

            <div class="border-t border-[#E5E7EB] pt-4 space-y-3">
                <p class="text-[11px] text-[#A1A1AA] font-medium">Informasi Klasifikasi &amp; PIC</p>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis Rekruit</label>
                    <input type="text" name="jenis_rekruit" id="edit_jenis_rekruit"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">PIC / Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" id="edit_penanggung_jawab"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-xs focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-4 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---- 4. MODAL: DELETE ---- --}}
<div id="modalDelete" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalDelete').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center z-10 animate-in fade-in duration-150">
        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="trash-2" class="w-7 h-7 text-red-500"></i>
        </div>
        <h3 class="text-base font-bold text-[#18181B] mb-1">Hapus Biaya Rekruit</h3>
        <p class="text-xs text-[#71717A] mb-1">Apakah Anda yakin ingin menghapus:</p>
        <p class="text-xs font-semibold text-[#18181B] mb-5 leading-relaxed" id="delete_name_label">—</p>
        <div class="flex gap-3">
            <button onclick="document.getElementById('modalDelete').classList.add('hidden')"
                    class="flex-1 px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
            <form id="formDelete" method="POST" action="" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>
</div>

{{-- ---- 5. MODAL: DELETE ALL ---- --}}
<div id="modalDeleteAll" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalDeleteAll').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center z-10 animate-in fade-in duration-150">
        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600">
            <i data-lucide="alert-octagon" class="w-7 h-7"></i>
        </div>
        <h3 class="text-base font-bold text-[#18181B] mb-1">Hapus Semua Data Biaya Rekruit?</h3>
        <p class="text-xs text-[#71717A] mb-4">
            Tindakan ini akan menghapus <strong>seluruh data Biaya Rekruit</strong> yang tersimpan di sistem.
        </p>
        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-[11px] text-red-800 text-left mb-5">
            <p class="font-bold flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i> Perhatian:</p>
            <p class="mt-0.5">Data yang telah dihapus tidak dapat dikembalikan kecuali Anda melakukan import ulang file CSV.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="document.getElementById('modalDeleteAll').classList.add('hidden')"
                    class="flex-1 px-4 py-2 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">
                Batal
            </button>
            <form method="POST" action="{{ route('administrasi.biaya-rekruit.destroyAll') }}" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                    Ya, Hapus Semua
                </button>
            </form>
        </div>
    </div>
</div>

    {{-- ============================== MODAL: TAMBAH TAHUN ============================== --}}
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

    {{-- Modal Edit Jumlah Peserta Reguler --}}
    <div id="modalEditPeserta" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-[#18181B]">Set Peserta Reguler (Tahun {{ $summary['target_year'] }})</h3>
                        <p class="text-[11px] text-[#71717A]">Angka dasar untuk kalkulasi Biaya Rekruit / Orang</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditPesertaModal()" class="text-[#71717A] hover:text-[#18181B] p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('administrasi.biaya-rekruit.update-peserta') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="year" value="{{ $summary['target_year'] }}">

                <div>
                    <label class="block text-xs font-bold text-[#374151] uppercase tracking-wider mb-1">
                        Jumlah Peserta Reguler (Orang)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" step="1" name="jumlah_peserta" value="{{ $summary['peserta_join'] }}" required
                               class="w-full rounded-xl border border-[#E5E7EB] px-4 py-2.5 text-base font-bold text-[#18181B] outline-none focus:border-[#4B6A34] focus:ring-2 focus:ring-[#4B6A34]/20 transition-all">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-[#71717A]">Orang</span>
                    </div>
                    <p class="text-[11px] text-[#71717A] mt-1.5 leading-relaxed">
                        Masukkan total peserta reguler yang berhasil join di tahun <strong>{{ $summary['target_year'] }}</strong> (tidak termasuk peserta carry-over).
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeEditPesertaModal()"
                            class="px-4 py-2 text-xs font-semibold text-[#52525B] bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] rounded-xl transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-[#4B6A34] hover:bg-[#3d5829] rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endcan

@push('scripts')
<script>
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

function openEditPesertaModal() {
    const modal = document.getElementById('modalEditPeserta');
    if (modal) {
        modal.classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

function closeEditPesertaModal() {
    const modal = document.getElementById('modalEditPeserta');
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

function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileNameDisplay').innerText = input.files[0].name;
    }
}

function openEditModal(id) {
    fetch(`/administrasi/biaya-rekruit/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;
            document.getElementById('formEdit').action = `/administrasi/biaya-rekruit/${id}`;
            document.getElementById('edit_tanggal').value          = d.tanggal ? d.tanggal.substring(0, 10) : '';
            document.getElementById('edit_nama').value             = d.nama             || '';
            document.getElementById('edit_total_biaya').value      = d.total_biaya      || 0;
            document.getElementById('edit_jenis_rekruit').value    = d.jenis_rekruit    || '';
            document.getElementById('edit_penanggung_jawab').value = d.penanggung_jawab || '';
            document.getElementById('modalEdit').classList.remove('hidden');
        });
}

function openDeleteModal(id, name) {
    document.getElementById('delete_name_label').textContent = name;
    document.getElementById('formDelete').action = `/administrasi/biaya-rekruit/${id}`;
    document.getElementById('modalDelete').classList.remove('hidden');
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
