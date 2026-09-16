<x-admin-layout>

<x-slot name="title">Cuti (Employee Leave Management) | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">Cuti (Employee Leave Management) | FR Academy Enterprise Portal</x-slot>

@push('styles')
<style>
    /* Sticky table columns for smooth horizontal scrolling */
    .sticky-nik {
        position: sticky;
        left: 0;
        z-index: 20;
        background-color: inherit;
    }
    .sticky-nama {
        position: sticky;
        left: 110px;
        z-index: 20;
        background-color: inherit;
    }
    .sticky-jabatan {
        position: sticky;
        left: 270px;
        z-index: 20;
        background-color: inherit;
    }
</style>
@endpush


    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-xs">
            <span class="text-sm font-medium">{{ $errors->first('file') }}</span>
        </div>
    @endif

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- 1. Page Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-primary-900 tracking-tight">Cuti</h2>
            <p class="text-[#71717A] mt-1 text-sm">Pengelolaan hak cuti, penggunaan cuti, dan sisa cuti karyawan</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Export Report Button -->
            <a href="{{ route('administrasi.cuti.export', ['year' => $selectedYear]) }}"
               class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i> Export Excel
            </a>

            <!-- Download Template Button -->
            <a href="{{ route('administrasi.cuti.template') }}"
               class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#71717A]"></i> Download Template
            </a>

            <form action="{{ route('administrasi.cuti.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <label class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors cursor-pointer">
                    <i data-lucide="upload" class="w-4 h-4 text-[#71717A]"></i>
                    Import CSV
                    <input type="file" name="file" accept=".csv,.xlsx" required class="hidden" onchange="this.form.submit()">
                </label>
            </form>

            <!-- Add Data Button -->
            <button onclick="openAddModal()"
                    class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-sm transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i> + Tambah Data
            </button>
        </div>
    </div>

    <!-- 2. Year Navigation Tabs -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-3.5 shadow-2xs">
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
                <div id="yearTabsContainer" class="flex items-center gap-2 overflow-x-auto py-1 px-1 scroll-smooth" style="scrollbar-width: thin;">
                    @foreach ($years as $year)
                        <a href="{{ route('administrasi.cuti.index', ['year' => $year, 'search' => $search, 'jabatan' => $filterJabatan, 'status' => $filterStatus]) }}"
                           id="year-tab-{{ $year }}"
                           class="px-5 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ (int)$selectedYear === (int)$year ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
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
            <div class="shrink-0">
                <button type="button" onclick="openTambahTahunModal()"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#4B6A34] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 flex items-center gap-1.5 transition-colors shadow-2xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>+ Tambah Tahun</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Summary Stats Banner -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Karyawan</p>
            <h3 class="text-2xl font-bold text-[#18181B] mt-1">{{ $totals['total_karyawan'] }} <span class="text-xs font-normal text-[#71717A]">Orang</span></h3>
        </div>
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total {{ $hakCutiLabel }}</p>
            <h3 class="text-2xl font-bold text-primary-700 mt-1">{{ $totals['total_hak_cuti'] }} <span class="text-xs font-normal text-[#71717A]">Hari</span></h3>
        </div>
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Cuti Diambil</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $totals['total_cuti_diambil'] }} <span class="text-xs font-normal text-[#71717A]">Hari</span></h3>
        </div>
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Sisa Kuota Cuti</p>
            <h3 class="text-2xl font-bold {{ $totals['total_sisa_cuti'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }} mt-1">
                {{ $totals['total_sisa_cuti'] }} <span class="text-xs font-normal text-[#71717A]">Hari</span>
            </h3>
        </div>
    </div>

    <!-- 4. Search & Filter Bar -->
    <form action="{{ route('administrasi.cuti.index') }}" method="GET" class="bg-white border border-[#E5E7EB] rounded-xl p-3 shadow-2xs flex flex-col md:flex-row items-center gap-3">
        <input type="hidden" name="year" value="{{ $selectedYear }}">

        <!-- Search Input -->
        <div class="relative flex-1 w-full">
            <i data-lucide="search" class="w-4 h-4 text-[#A1A1AA] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="Search NIK / Nama / Jabatan..."
                   class="w-full pl-10 pr-4 py-2 text-sm bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-[#18181B] placeholder-[#A1A1AA]">
        </div>

        <!-- Filter Kategori / Jenis Jabatan -->
        <div class="w-full md:w-48">
            <select name="jenis_jabatan" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#374151] focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="">Semua Kategori</option>
                @foreach($jenisJabatanList as $jns)
                    <option value="{{ $jns }}" {{ $filterJenisJabatan == $jns ? 'selected' : '' }}>{{ $jns }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Jabatan -->
        <div class="w-full md:w-52">
            <select name="jabatan" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#374151] focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="">Semua Jabatan</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos }}" {{ $filterJabatan == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status Sisa Cuti -->
        <div class="w-full md:w-48">
            <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg text-[#374151] focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <option value="">Semua Status Sisa</option>
                <option value="positif" {{ $filterStatus == 'positif' ? 'selected' : '' }}>Sisa Cuti Tersedia (&gt; 0)</option>
                <option value="habis" {{ $filterStatus == 'habis' ? 'selected' : '' }}>Cuti Habis (= 0)</option>
                <option value="minus" {{ $filterStatus == 'minus' ? 'selected' : '' }}>Overlimit (&lt; 0)</option>
            </select>
        </div>

        <button type="submit" class="w-full md:w-auto px-4 py-2 bg-[#F4F4F5] hover:bg-[#E4E4E7] text-[#18181B] text-sm font-medium rounded-lg transition-colors">
            Cari
        </button>

        @if(!empty($search) || !empty($filterJabatan) || !empty($filterJenisJabatan) || !empty($filterStatus))
            <a href="{{ route('administrasi.cuti.index', ['year' => $selectedYear]) }}" class="text-xs text-rose-600 hover:underline px-2 whitespace-nowrap">
                Reset Filter
            </a>
        @endif
    </form>

    <!-- 5. Main Data Table with Horizontal Scroll -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1250px]">
                <thead>
                    <!-- Header Row 1 -->
                    <tr class="bg-[#F0FDF4] border-b border-[#E5E7EB] text-[11px] font-bold uppercase tracking-wider text-[#374151]">
                        <th rowspan="2" class="py-3 px-4 sticky-nik bg-[#F0FDF4] border-r border-[#E5E7EB]">NIK</th>
                        <th rowspan="2" class="py-3 px-4 sticky-nama bg-[#F0FDF4] border-r border-[#E5E7EB]">Nama</th>
                        <th rowspan="2" class="py-3 px-4 sticky-jabatan bg-[#F0FDF4] border-r border-[#E5E7EB]">Jabatan</th>
                        <th rowspan="2" class="py-3 px-3 text-center border-r border-[#E5E7EB]">TMK</th>
                        <th rowspan="2" class="py-3 px-3 text-center bg-blue-50/50 border-r border-[#E5E7EB]">
                            Periode Hak Cuti
                            <span class="block text-[9px] font-normal text-blue-700 capitalize">Otomatis ({{ $periodeHakCuti }})</span>
                        </th>
                        <th rowspan="2" class="py-3 px-3 text-center border-r border-[#E5E7EB]">
                            {{ $hakCutiLabel }}
                            <span class="block text-[9px] font-normal text-[#71717A] capitalize">Manual</span>
                        </th>
                        <th rowspan="2" class="py-3 px-3 text-center bg-amber-50/40 border-r border-[#E5E7EB]">
                            {{ $sisaCarryoverLabel }}
                            <span class="block text-[9px] font-normal text-amber-700 capitalize">Histori</span>
                        </th>
                        <th rowspan="2" class="py-3 px-3 text-center border-r border-[#E5E7EB]">
                            Cuti Diambil
                            <span class="block text-[9px] font-normal text-[#71717A] capitalize">Manual</span>
                        </th>
                        <th rowspan="2" class="py-3 px-3 text-center bg-emerald-100/60 border-r border-[#E5E7EB] text-emerald-900 font-extrabold">
                            Sisa Cuti
                            <span class="block text-[9px] font-normal text-emerald-700 capitalize">Kalkulasi</span>
                        </th>
                        <!-- Grouped Header for 12 Months -->
                        <th colspan="12" class="py-2.5 px-2 text-center bg-[#E5E7EB]/50 border-r border-[#E5E7EB] text-[#1F2937]">
                            {{ $periodePengambilanLabel }}
                        </th>
                        <th rowspan="2" class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                    <!-- Header Row 2 (Monthly Child Columns) -->
                    <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB] text-[10px] font-semibold text-[#4B5563] text-center">
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Jan</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Feb</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Mar</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Apr</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Mei</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Jun</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Jul</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Agu</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Sep</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Okt</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Nov</th>
                        <th class="py-2 px-1.5 border-r border-[#E5E7EB] w-9">Des</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] text-xs text-[#18181B]">
                    @php
                        $currentGroup = null;
                    @endphp
                    @forelse ($records as $row)
                        @php
                            $rawGroup = $row->jenis_jabatan ?? 'Lain-lain';
                            $cfg = str_contains($rawGroup, 'Staf') || str_contains($rawGroup, 'Staff')
                                ? ['title' => 'STAFF', 'bg' => 'bg-blue-50/80', 'text' => 'text-blue-900', 'badge' => 'bg-blue-600 text-white']
                                : (str_contains($rawGroup, 'PBT')
                                    ? ['title' => 'Karyawan PBT', 'bg' => 'bg-amber-50/80', 'text' => 'text-amber-900', 'badge' => 'bg-amber-600 text-white']
                                    : (str_contains($rawGroup, 'KHT')
                                        ? ['title' => 'Karyawan KHT', 'bg' => 'bg-emerald-50/80', 'text' => 'text-emerald-900', 'badge' => 'bg-[#4B6A34] text-white']
                                        : ['title' => $rawGroup, 'bg' => 'bg-gray-100', 'text' => 'text-gray-900', 'badge' => 'bg-gray-600 text-white']));
                        @endphp

                        {{-- Section Separator Row (Full-width baris penuh) --}}
                        @if ($currentGroup !== $cfg['title'])
                            @php
                                $currentGroup = $cfg['title'];
                            @endphp
                            <tr class="{{ $cfg['bg'] }} border-y-2 border-[#D1D5DB]">
                                <td colspan="22" class="py-2.5 px-4 font-bold {{ $cfg['text'] }} sticky left-0 z-10 bg-inherit shadow-2xs">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $cfg['badge'] }} shadow-2xs">Kategori</span>
                                        <span class="text-sm font-extrabold tracking-wide uppercase">{{ $cfg['title'] }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        <tr class="hover:bg-[#F9FAFB] transition-colors group">
                            <!-- NIK (Sticky) -->
                            <td class="py-3 px-4 font-semibold sticky-nik bg-white group-hover:bg-[#F9FAFB] border-r border-[#E5E7EB] whitespace-nowrap text-primary-900">
                                {{ $row->nik }}
                            </td>

                            <!-- Nama (Sticky & Clickable for Detail) -->
                            <td class="py-3 px-4 font-bold sticky-nama bg-white group-hover:bg-[#F9FAFB] border-r border-[#E5E7EB] whitespace-nowrap">
                                <button type="button" onclick="showEmployeeDetail({{ $row->cuti_id }})" class="hover:text-primary-700 hover:underline text-left">
                                    {{ $row->nama }}
                                </button>
                            </td>

                            <!-- Jabatan (Sticky) -->
                            <td class="py-3 px-4 sticky-jabatan bg-white group-hover:bg-[#F9FAFB] border-r border-[#E5E7EB] whitespace-nowrap text-[#52525B]">
                                {{ $row->jabatan ?? '-' }}
                            </td>

                            <!-- TMK -->
                            <td class="py-3 px-3 text-center border-r border-[#E5E7EB] whitespace-nowrap text-[#71717A]">
                                {{ $row->tmk ? $row->tmk->format('d M Y') : '-' }}
                            </td>

                            <!-- Periode Hak Cuti (Auto = Selected Year - 1) -->
                            <td class="py-3 px-3 text-center bg-blue-50/20 border-r border-[#E5E7EB] font-medium text-blue-900">
                                {{ $row->periode_hak_cuti }}
                            </td>

                            <!-- Hak Cuti (Manual) -->
                            <td class="py-3 px-3 text-center border-r border-[#E5E7EB]">
                                <span class="inline-block px-2.5 py-0.5 rounded-md border border-[#E5E7EB] bg-white font-semibold text-[#18181B]">
                                    {{ $row->hak_cuti }}
                                </span>
                            </td>

                            <!-- Sisa Hak Cuti Carryover (Histori) -->
                            <td class="py-3 px-3 text-center bg-amber-50/20 border-r border-[#E5E7EB] font-medium text-amber-900">
                                {{ $row->sisa_hak_cuti_carryover > 0 ? $row->sisa_hak_cuti_carryover : '-' }}
                            </td>

                            <!-- Cuti Diambil -->
                            <td class="py-3 px-3 text-center border-r border-[#E5E7EB] font-medium text-[#18181B]">
                                {{ $row->cuti_diambil }}
                            </td>

                            <!-- Sisa Cuti (Calculated & Color coded) -->
                            <td class="py-3 px-3 text-center border-r border-[#E5E7EB] {{ $row->sisa_cuti >= 0 ? 'bg-emerald-50/50' : 'bg-rose-50/70' }}">
                                @if($row->sisa_cuti > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold text-emerald-800 bg-emerald-100/80">
                                        {{ $row->sisa_cuti }}
                                    </span>
                                @elseif($row->sisa_cuti === 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold text-gray-700 bg-gray-100">
                                        0
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold text-rose-700 bg-rose-100" title="Melebihi Kuota Cuti">
                                        {{ $row->sisa_cuti }}
                                    </span>
                                @endif
                            </td>

                            <!-- 12 Monthly Usage Columns -->
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->jan > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->jan > 0 ? $row->jan : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->feb > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->feb > 0 ? $row->feb : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->mar > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->mar > 0 ? $row->mar : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->apr > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->apr > 0 ? $row->apr : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->mei > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->mei > 0 ? $row->mei : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->jun > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->jun > 0 ? $row->jun : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->jul > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->jul > 0 ? $row->jul : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->agu > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->agu > 0 ? $row->agu : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->sep > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->sep > 0 ? $row->sep : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->okt > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->okt > 0 ? $row->okt : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->nov > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->nov > 0 ? $row->nov : '-' }}
                            </td>
                            <td class="py-3 px-1.5 text-center border-r border-[#E5E7EB] {{ $row->des > 0 ? 'font-bold text-[#18181B] bg-emerald-50/30' : 'text-[#A1A1AA]' }}">
                                {{ $row->des > 0 ? $row->des : '-' }}
                            </td>

                            <!-- Action Column -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- View Detail -->
                                    <button type="button"
                                            onclick="showEmployeeDetail({{ $row->cuti_id }})"
                                            class="p-1.5 text-[#71717A] hover:text-[#4B6A34] hover:bg-emerald-50 rounded-lg transition-colors"
                                            title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Edit -->
                                    <button type="button"
                                            onclick="openEditModal({{ $row->cuti_id }})"
                                            class="p-1.5 text-[#71717A] hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Edit Data">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete -->
                                    <button type="button"
                                            onclick="confirmDelete({{ $row->cuti_id }}, '{{ $row->nama }}', '{{ $row->nik }}')"
                                            class="p-1.5 text-[#71717A] hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                            title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="22" class="py-12 text-center text-[#71717A]">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-[#F4F4F5] flex items-center justify-center text-[#A1A1AA]">
                                        <i data-lucide="clipboard-x" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-[#18181B]">Belum ada data cuti untuk tahun {{ $selectedYear }}</p>
                                        <p class="text-xs text-[#71717A] mt-0.5">Silakan klik tombol "+ Tambah Data" untuk mulai memasukkan data karyawan.</p>
                                    </div>
                                    <button onclick="openAddModal()" class="mt-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                        + Tambah Data Baru
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($records->hasPages())
            <div class="p-4 border-t border-[#E5E7EB] bg-white flex items-center justify-between">
                <span class="text-xs text-[#71717A]">
                    Menampilkan {{ $records->firstItem() ?? 0 }} - {{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} data
                </span>
                {{ $records->links() }}
            </div>
        @endif
    </div>

    <!-- ======================================================== -->
    <!-- 6. MODAL: TAMBAH DATA                                    -->
    <!-- ======================================================== -->
    <div id="addModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
                <div>
                    <h3 class="text-lg font-bold text-[#18181B]">Tambah Data Hak &amp; Penggunaan Cuti</h3>
                    <p class="text-xs text-[#71717A] mt-0.5">Periode Tahun Aktif: <strong class="text-primary-700">{{ $selectedYear }}</strong></p>
                </div>
                <button onclick="closeModal('addModal')" class="text-[#71717A] hover:text-[#18181B] p-1.5 rounded-lg hover:bg-[#F4F4F5]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form action="{{ route('administrasi.cuti.store') }}" method="POST" class="overflow-y-auto p-6 space-y-6 flex-1">
                @csrf
                <input type="hidden" name="tahun" value="{{ $selectedYear }}">

                <!-- SECTION 1: Informasi Karyawan -->
                <div>
                    <div class="flex items-center gap-2 border-b border-[#E5E7EB] pb-2 mb-4">
                        <i data-lucide="user" class="w-4 h-4 text-[#4B6A34]"></i>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#374151]">Informasi Karyawan</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">NIK (Employee ID) <span class="text-rose-500">*</span></label>
                            <input type="text" name="nik" required placeholder="Contoh: FRA-001" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">Nama Karyawan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama" required placeholder="Contoh: Budi Santoso" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">Jenis Jabatan / Kategori <span class="text-rose-500">*</span></label>
                            <select name="jenis_jabatan" required class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="Staf">Staf</option>
                                <option value="Karyawan PBT">Karyawan PBT</option>
                                <option value="Karyawan KHT">Karyawan KHT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">Jabatan / Posisi</label>
                            <input type="text" name="jabatan" placeholder="Contoh: Manager Ops" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-[#374151] mb-1">TMK (Tanggal Mulai Kerja)</label>
                            <input type="date" name="tmk" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Informasi Hak Cuti -->
                <div>
                    <div class="flex items-center gap-2 border-b border-[#E5E7EB] pb-2 mb-4">
                        <i data-lucide="award" class="w-4 h-4 text-[#4B6A34]"></i>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#374151]">Informasi Hak Cuti</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Periode Hak Cuti (Read-Only) -->
                        <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                            <label class="block text-xs font-medium text-blue-900 mb-1">
                                Periode Hak Cuti <span class="text-[10px] text-blue-600">(Otomatis)</span>
                            </label>
                            <input type="text" value="{{ $periodeHakCuti }}" readonly class="w-full px-3 py-2 text-sm bg-white/80 border border-blue-200 rounded-lg text-blue-950 font-bold cursor-not-allowed">
                            <p class="text-[10px] text-blue-600 mt-1">Tahun Aktif - 1</p>
                        </div>

                        <!-- Hak Cuti (Manual Input) -->
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">
                                {{ $hakCutiLabel }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="hak_cuti" id="add_hak_cuti" value="12" min="0" required oninput="calculateAddSisa()" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-semibold">
                            <p class="text-[10px] text-[#71717A] mt-1">Default 12 hari (dapat diedit)</p>
                        </div>

                        <!-- Sisa Hak Cuti Carryover (Manual / Histori) -->
                        <div class="bg-amber-50/40 p-3 rounded-xl border border-amber-100">
                            <label class="block text-xs font-medium text-amber-900 mb-1">
                                {{ $sisaCarryoverLabel }}
                            </label>
                            <input type="number" name="sisa_hak_cuti_carryover" id="add_carryover" value="0" min="0" oninput="calculateAddSisa()" class="w-full px-3 py-2 text-sm bg-white border border-amber-200 rounded-lg font-semibold">
                            <p class="text-[10px] text-amber-700 mt-1">Sisa 2 periode lalu</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Penggunaan Cuti -->
                <div>
                    <div class="flex items-center justify-between border-b border-[#E5E7EB] pb-2 mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#4B6A34]"></i>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#374151]">Penggunaan Cuti ({{ $periodePengambilanLabel }})</h4>
                        </div>
                        <div class="text-xs text-[#71717A]">
                            Total Bulan: <strong id="add_total_bulan_display" class="text-primary-700">0</strong> Hari
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-[#374151] mb-1">
                            Cuti yang Sudah Diambil <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="number" name="cuti_diambil" id="add_cuti_diambil" value="0" min="0" required oninput="calculateAddSisa()" class="w-full md:w-64 px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-bold text-[#18181B]">
                            <button type="button" onclick="syncAddMonthlyToTotal()" class="px-3 py-1.5 bg-[#F4F4F5] hover:bg-[#E4E4E7] text-xs font-medium text-[#374151] rounded-lg transition-colors" title="Salin jumlah dari rincian 12 bulan ke Cuti yang Diambil">
                                Samakan dengan Total Rincian Bulan
                            </button>
                        </div>
                    </div>

                    <p class="text-[11px] font-medium text-[#71717A] mb-2">Rincian Pengambilan Cuti per Bulan:</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5">
                        @foreach(['jan' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar', 'apr' => 'Apr', 'mei' => 'Mei', 'jun' => 'Jun', 'jul' => 'Jul', 'agu' => 'Agu', 'sep' => 'Sep', 'okt' => 'Okt', 'nov' => 'Nov', 'des' => 'Des'] as $field => $label)
                            <div>
                                <label class="block text-[11px] font-semibold text-center text-[#52525B] mb-1">{{ $label }}</label>
                                <input type="number" name="{{ $field }}" id="add_{{ $field }}" value="0" min="0" oninput="updateAddMonthlySum()" class="w-full text-center px-2 py-1.5 text-xs bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-medium">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SECTION 4: Hasil Perhitungan (Live Read-Only Preview) -->
                <div class="bg-[#F0FDF4] border border-emerald-200 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">Hasil Perhitungan Sisa Cuti</p>
                        <p class="text-[11px] text-emerald-700 mt-0.5">Rumus: {{ $hakCutiLabel }} + {{ $sisaCarryoverLabel }} − Cuti yang Diambil</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-xs text-[#52525B]">Sisa Cuti Tersedia:</span>
                        </div>
                        <div id="add_sisa_cuti_badge" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-extrabold text-xl shadow-xs">
                            12
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 text-sm font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Data Cuti
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 7. MODAL: EDIT DATA                                      -->
    <!-- ======================================================== -->
    <div id="editModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
                <div>
                    <h3 class="text-lg font-bold text-[#18181B]">Edit Data Cuti Karyawan</h3>
                    <p class="text-xs text-[#71717A] mt-0.5" id="edit_modal_subtitle">Memperbarui data cuti</p>
                </div>
                <button onclick="closeModal('editModal')" class="text-[#71717A] hover:text-[#18181B] p-1.5 rounded-lg hover:bg-[#F4F4F5]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form id="editForm" method="POST" class="overflow-y-auto p-6 space-y-6 flex-1">
                @csrf
                @method('PUT')

                <!-- SECTION 1: Informasi Karyawan -->
                <div>
                    <div class="flex items-center gap-2 border-b border-[#E5E7EB] pb-2 mb-4">
                        <i data-lucide="user" class="w-4 h-4 text-[#4B6A34]"></i>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#374151]">Informasi Karyawan</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">NIK <span class="text-rose-500">*</span></label>
                            <input type="text" name="nik" id="edit_nik" required class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">Nama Karyawan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama" id="edit_nama" required class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">Jenis Jabatan / Kategori <span class="text-rose-500">*</span></label>
                            <select name="jenis_jabatan" id="edit_jenis_jabatan" required class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                <option value="Staf">Staf</option>
                                <option value="Karyawan PBT">Karyawan PBT</option>
                                <option value="Karyawan KHT">Karyawan KHT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">Jabatan</label>
                            <input type="text" name="jabatan" id="edit_jabatan" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-[#374151] mb-1">TMK (Tanggal Mulai Kerja)</label>
                            <input type="date" name="tmk" id="edit_tmk" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Informasi Hak Cuti -->
                <div>
                    <div class="flex items-center gap-2 border-b border-[#E5E7EB] pb-2 mb-4">
                        <i data-lucide="award" class="w-4 h-4 text-[#4B6A34]"></i>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#374151]">Informasi Hak Cuti</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                            <label class="block text-xs font-medium text-blue-900 mb-1">
                                Periode Hak Cuti <span class="text-[10px] text-blue-600">(Otomatis)</span>
                            </label>
                            <input type="text" id="edit_periode_hak_cuti" readonly class="w-full px-3 py-2 text-sm bg-white/80 border border-blue-200 rounded-lg text-blue-950 font-bold cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#374151] mb-1">
                                {{ $hakCutiLabel }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="hak_cuti" id="edit_hak_cuti" min="0" required oninput="calculateEditSisa()" class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-semibold">
                        </div>
                        <div class="bg-amber-50/40 p-3 rounded-xl border border-amber-100">
                            <label class="block text-xs font-medium text-amber-900 mb-1">
                                {{ $sisaCarryoverLabel }}
                            </label>
                            <input type="number" name="sisa_hak_cuti_carryover" id="edit_carryover" min="0" oninput="calculateEditSisa()" class="w-full px-3 py-2 text-sm bg-white border border-amber-200 rounded-lg font-semibold">
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Penggunaan Cuti -->
                <div>
                    <div class="flex items-center justify-between border-b border-[#E5E7EB] pb-2 mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#4B6A34]"></i>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#374151]">Penggunaan Cuti ({{ $periodePengambilanLabel }})</h4>
                        </div>
                        <div class="text-xs text-[#71717A]">
                            Total Bulan: <strong id="edit_total_bulan_display" class="text-primary-700">0</strong> Hari
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-[#374151] mb-1">
                            Cuti yang Sudah Diambil <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="number" name="cuti_diambil" id="edit_cuti_diambil" min="0" required oninput="calculateEditSisa()" class="w-full md:w-64 px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-bold text-[#18181B]">
                            <button type="button" onclick="syncEditMonthlyToTotal()" class="px-3 py-1.5 bg-[#F4F4F5] hover:bg-[#E4E4E7] text-xs font-medium text-[#374151] rounded-lg transition-colors">
                                Samakan dengan Total Rincian Bulan
                            </button>
                        </div>
                    </div>

                    <p class="text-[11px] font-medium text-[#71717A] mb-2">Rincian Pengambilan Cuti per Bulan:</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5">
                        @foreach(['jan' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar', 'apr' => 'Apr', 'mei' => 'Mei', 'jun' => 'Jun', 'jul' => 'Jul', 'agu' => 'Agu', 'sep' => 'Sep', 'okt' => 'Okt', 'nov' => 'Nov', 'des' => 'Des'] as $field => $label)
                            <div>
                                <label class="block text-[11px] font-semibold text-center text-[#52525B] mb-1">{{ $label }}</label>
                                <input type="number" name="{{ $field }}" id="edit_{{ $field }}" min="0" oninput="updateEditMonthlySum()" class="w-full text-center px-2 py-1.5 text-xs bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-medium">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SECTION 4: Live Calculated Sisa Cuti -->
                <div class="bg-[#F0FDF4] border border-emerald-200 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-900">Hasil Perhitungan Sisa Cuti</p>
                        <p class="text-[11px] text-emerald-700 mt-0.5">Sisa Cuti = {{ $hakCutiLabel }} + {{ $sisaCarryoverLabel }} − Cuti Diambil</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-xs text-[#52525B]">Sisa Cuti:</span>
                        </div>
                        <div id="edit_sisa_cuti_badge" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-extrabold text-xl shadow-xs">
                            0
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-sm font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-medium bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-lg shadow-sm transition-colors flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 8. MODAL: DETAIL KARYAWAN                                -->
    <!-- ======================================================== -->
    <div id="detailModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#4B6A34] text-white flex items-center justify-center font-bold text-sm" id="detail_avatar">
                        NA
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#18181B]" id="detail_nama">-</h3>
                        <p class="text-xs text-[#71717A]"><span id="detail_nik">-</span> • <span id="detail_jabatan">-</span> • <span id="detail_jenis_jabatan" class="font-bold text-primary-800">-</span></p>
                    </div>
                </div>
                <button onclick="closeModal('detailModal')" class="text-[#71717A] hover:text-[#18181B] p-1.5 rounded-lg hover:bg-[#F4F4F5]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="overflow-y-auto p-6 space-y-5 flex-1">
                <!-- Visual Summary Cards (Top) -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#71717A]">Hak Cuti</p>
                        <p class="text-xl font-bold text-primary-900 mt-0.5" id="detail_hak_cuti">0</p>
                        <p class="text-[10px] text-[#71717A]">Periode <span id="detail_periode_hak">2025</span></p>
                    </div>
                    <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#71717A]">Cuti Terpakai</p>
                        <p class="text-xl font-bold text-amber-600 mt-0.5" id="detail_cuti_diambil">0</p>
                        <p class="text-[10px] text-[#71717A]">Tahun {{ $selectedYear }}</p>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center" id="detail_sisa_box">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Sisa Cuti</p>
                        <p class="text-xl font-extrabold text-emerald-700 mt-0.5" id="detail_sisa_cuti">0</p>
                        <p class="text-[10px] text-emerald-600">Hari Tersedia</p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 text-xs space-y-2.5">
                    <div class="flex justify-between py-1 border-b border-[#F4F4F5]">
                        <span class="text-[#71717A]">Kategori Karyawan:</span>
                        <span class="font-bold text-[#18181B]" id="detail_kategori">-</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#F4F4F5]">
                        <span class="text-[#71717A]">Tanggal Mulai Kerja (TMK):</span>
                        <span class="font-semibold text-[#18181B]" id="detail_tmk">-</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#F4F4F5]">
                        <span class="text-[#71717A]">Sisa Hak Cuti Carryover (<span id="detail_carryover_label">2023–2024</span>):</span>
                        <span class="font-semibold text-[#18181B]" id="detail_carryover">0 Hari</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-[#71717A]">Total Pengambilan dari Rincian Bulanan:</span>
                        <span class="font-semibold text-[#18181B]" id="detail_total_bulanan">0 Hari</span>
                    </div>
                </div>

                <!-- Monthly Breakdown Table -->
                <div>
                    <p class="text-xs font-bold text-[#374151] mb-2 uppercase tracking-wider">Rincian Pengambilan Cuti {{ $selectedYear }}</p>
                    <div class="grid grid-cols-6 gap-2 text-center text-xs">
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Jan</span><strong id="detail_m_jan">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Feb</span><strong id="detail_m_feb">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Mar</span><strong id="detail_m_mar">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Apr</span><strong id="detail_m_apr">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Mei</span><strong id="detail_m_mei">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Jun</span><strong id="detail_m_jun">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Jul</span><strong id="detail_m_jul">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Agu</span><strong id="detail_m_agu">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Sep</span><strong id="detail_m_sep">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Okt</span><strong id="detail_m_okt">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Nov</span><strong id="detail_m_nov">0</strong></div>
                        <div class="bg-[#F9FAFB] border border-[#E5E7EB] p-2 rounded-lg"><span class="text-[10px] text-[#71717A] block">Des</span><strong id="detail_m_des">0</strong></div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-[#F9FAFB] border-t border-[#E5E7EB] flex justify-end">
                <button onclick="closeModal('detailModal')" class="px-4 py-2 bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-xs font-semibold text-[#18181B] rounded-lg">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 9. MODAL: TAHUN BARU                                     -->
    <!-- ======================================================== -->
    <div id="newYearModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-200">
            <h3 class="text-base font-bold text-[#18181B]">Pilih / Tambah Tahun Baru</h3>
            <p class="text-xs text-[#71717A] mt-1">Masukkan tahun yang ingin Anda buka atau kelola.</p>

            <form action="{{ route('administrasi.cuti.year') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-[#374151] mb-1">Tahun (2000 - 2099)</label>
                    <input type="number" name="new_year" value="{{ $selectedYear + 1 }}" min="2000" max="2099" required class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('newYearModal')" class="px-3 py-2 text-xs font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-lg">
                        Buka Periode Tahun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 10. MODAL: DELETE CONFIRMATION                           -->
    <!-- ======================================================== -->
    <div id="deleteModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-200 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="trash-2" class="w-6 h-6"></i>
            </div>
            <h3 class="text-base font-bold text-[#18181B]">Hapus Data Cuti?</h3>
            <p class="text-xs text-[#71717A] mt-1">
                Apakah Anda yakin ingin menghapus data cuti untuk <strong id="delete_emp_name" class="text-[#18181B]">-</strong> (<span id="delete_emp_nik">-</span>)? Tindakan ini tidak dapat dibatalkan.
            </p>

            <form id="deleteForm" method="POST" class="mt-5 flex items-center justify-center gap-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-xs font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-lg">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
<script>
    const MONTHS = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];

    // Modal Helpers
    const openModal = (id) => document.getElementById(id)?.classList.remove('hidden');
    const closeModal = (id) => document.getElementById(id)?.classList.add('hidden');

    function openAddModal() {
        openModal('addModal');
        calculateSisa('add');
        updateMonthlySum('add');
    }

    function openNewYearModal() {
        openModal('newYearModal');
    }

    // Unified Calculations for Add & Edit
    function calculateSisa(prefix) {
        const hak = parseInt(document.getElementById(`${prefix}_hak_cuti`)?.value) || 0;
        const carryover = parseInt(document.getElementById(`${prefix}_carryover`)?.value) || 0;
        const taken = parseInt(document.getElementById(`${prefix}_cuti_diambil`)?.value) || 0;
        const sisa = hak + carryover - taken;

        const badge = document.getElementById(`${prefix}_sisa_cuti_badge`);
        if (badge) {
            badge.innerText = sisa;
            badge.className = 'px-4 py-2 rounded-xl text-white font-extrabold text-xl shadow-xs ' +
                (sisa > 0 ? 'bg-emerald-600' : (sisa === 0 ? 'bg-gray-600' : 'bg-rose-600'));
        }
    }

    function updateMonthlySum(prefix) {
        const total = MONTHS.reduce((sum, m) => sum + (parseInt(document.getElementById(`${prefix}_${m}`)?.value) || 0), 0);
        const disp = document.getElementById(`${prefix}_total_bulan_display`);
        if (disp) disp.innerText = total;
    }

    function syncMonthlyToTotal(prefix) {
        const total = MONTHS.reduce((sum, m) => sum + (parseInt(document.getElementById(`${prefix}_${m}`)?.value) || 0), 0);
        const takenInput = document.getElementById(`${prefix}_cuti_diambil`);
        if (takenInput) {
            takenInput.value = total;
            calculateSisa(prefix);
        }
    }

    // Aliases for HTML event handlers
    const calculateAddSisa = () => calculateSisa('add');
    const calculateEditSisa = () => calculateSisa('edit');
    const updateAddMonthlySum = () => updateMonthlySum('add');
    const updateEditMonthlySum = () => updateMonthlySum('edit');
    const syncAddMonthlyToTotal = () => syncMonthlyToTotal('add');
    const syncEditMonthlyToTotal = () => syncMonthlyToTotal('edit');

    // Edit Modal Ajax
    function openEditModal(id) {
        fetch(`/administrasi/cuti/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const data = res.data;
                    document.getElementById('editForm').action = `/administrasi/cuti/${id}`;
                    document.getElementById('edit_modal_subtitle').innerText = `${data.nama} (${data.nik})`;

                    document.getElementById('edit_nik').value = data.nik;
                    document.getElementById('edit_nama').value = data.nama;
                    document.getElementById('edit_jabatan').value = data.jabatan !== '-' ? data.jabatan : '';
                    if (document.getElementById('edit_jenis_jabatan')) {
                        document.getElementById('edit_jenis_jabatan').value = data.jenis_jabatan !== '-' ? data.jenis_jabatan : 'Staf';
                    }
                    document.getElementById('edit_tmk').value = data.tmk_raw || '';
                    document.getElementById('edit_periode_hak_cuti').value = data.periode_hak_cuti;
                    document.getElementById('edit_hak_cuti').value = data.hak_cuti;
                    document.getElementById('edit_carryover').value = data.sisa_hak_cuti_carryover;
                    document.getElementById('edit_cuti_diambil').value = data.cuti_diambil;

                    MONTHS.forEach(m => {
                        const input = document.getElementById(`edit_${m}`);
                        if (input) input.value = data.monthly[m] || 0;
                    });

                    updateMonthlySum('edit');
                    calculateSisa('edit');
                    openModal('editModal');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat data cuti untuk diedit.');
            });
    }

    // Detail Modal Ajax
    function showEmployeeDetail(id) {
        fetch(`/administrasi/cuti/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const data = res.data;
                    document.getElementById('detail_nama').innerText = data.nama;
                    document.getElementById('detail_nik').innerText = data.nik;
                    document.getElementById('detail_jabatan').innerText = data.jabatan;
                    if (document.getElementById('detail_jenis_jabatan')) {
                        document.getElementById('detail_jenis_jabatan').innerText = data.jenis_jabatan || '-';
                    }
                    if (document.getElementById('detail_kategori')) {
                        document.getElementById('detail_kategori').innerText = data.jenis_jabatan || '-';
                    }
                    document.getElementById('detail_tmk').innerText = data.tmk;

                    // Initials for avatar
                    const initials = data.nama.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
                    document.getElementById('detail_avatar').innerText = initials || 'EM';

                    document.getElementById('detail_hak_cuti').innerText = data.hak_cuti;
                    document.getElementById('detail_periode_hak').innerText = data.periode_hak_cuti;
                    document.getElementById('detail_cuti_diambil').innerText = data.cuti_diambil;
                    document.getElementById('detail_sisa_cuti').innerText = data.sisa_cuti;
                    document.getElementById('detail_carryover').innerText = data.sisa_hak_cuti_carryover + ' Hari';
                    document.getElementById('detail_carryover_label').innerText = data.periode_sisa_carryover_label;
                    document.getElementById('detail_total_bulanan').innerText = data.total_bulanan + ' Hari';

                    // Sisa box styling
                    const sisaBox = document.getElementById('detail_sisa_box');
                    const sisaText = document.getElementById('detail_sisa_cuti');
                    if (data.sisa_cuti < 0) {
                        sisaBox.className = 'bg-rose-50 border border-rose-200 rounded-xl p-3 text-center';
                        sisaText.className = 'text-xl font-extrabold text-rose-600 mt-0.5';
                    } else if (data.sisa_cuti === 0) {
                        sisaBox.className = 'bg-gray-50 border border-gray-200 rounded-xl p-3 text-center';
                        sisaText.className = 'text-xl font-extrabold text-gray-700 mt-0.5';
                    } else {
                        sisaBox.className = 'bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center';
                        sisaText.className = 'text-xl font-extrabold text-emerald-700 mt-0.5';
                    }

                    MONTHS.forEach(m => {
                        const el = document.getElementById(`detail_m_${m}`);
                        if (el) el.innerText = data.monthly[m] > 0 ? data.monthly[m] : '-';
                    });

                    openModal('detailModal');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat detail karyawan.');
            });
    }

    // Delete Confirmation
    function confirmDelete(id, name, nik) {
        document.getElementById('deleteForm').action = `/administrasi/cuti/${id}`;
        document.getElementById('delete_emp_name').innerText = name;
        document.getElementById('delete_emp_nik').innerText = nik;
        openModal('deleteModal');
    }

    // Year Navigation & Tambah Tahun
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

    function openNewYearModal() {
        openTambahTahunModal();
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
                    <p class="text-[11px] text-[#71717A]">Buka atau kelola data cuti periode tahun baru</p>
                </div>
            </div>
            <button type="button" onclick="closeTambahTahunModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formTambahTahun" onsubmit="handleTambahTahun(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1">Tahun Anggaran / Periode Cuti</label>
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
                        class="flex-1 px-4 py-2 text-xs font-bold text-white bg-[#4B6A34] rounded-xl hover:bg-[#3d562a] shadow-sm">
                    Buka Tahun
                </button>
            </div>
        </form>
    </div>
</div>
@endpush
