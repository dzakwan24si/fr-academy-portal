<x-admin-layout>

<x-slot name="title">Talent Pool | FR Academy Enterprise Portal</x-slot>
<x-slot name="header">Talent Pool | FR Academy Enterprise Portal</x-slot>

<div class="space-y-6">

    {{-- ============================== 1. HERO & ACTION BAR ============================== --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-400">Talent</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#4B6A34] font-bold">Talent Pool</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-[#4B6A34] flex items-center justify-center shrink-0">
                    <i data-lucide="users-round" class="w-5 h-5"></i>
                </span>
                Talent Pool
            </h1>
            <p class="mt-1 text-xs text-slate-500 font-medium">Database pemetaan kandidat talent, kesiapan suksesi, dan status penempatan First Resources Academy.</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- Template Excel --}}
            <a href="{{ route('administrasi.talent-pool.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                <i data-lucide="file-down" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>Template Excel</span>
            </a>
            
            {{-- Export Excel --}}
            <a href="{{ route('administrasi.talent-pool.export', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>Export Excel</span>
            </a>

            {{-- Import CSV/Excel --}}
            <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                <i data-lucide="upload" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                <span>Import Excel/CSV</span>
            </button>

            {{-- Tambah Kandidat --}}
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 rounded-xl bg-[#4B6A34] hover:bg-[#3d562a] text-white px-4 py-2 text-xs font-bold transition-all shadow-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah Kandidat</span>
            </button>

            {{-- Hapus Semua --}}
            <button type="button" onclick="openDeleteAllModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 px-3.5 py-2 text-xs font-bold transition-all">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Hapus Semua</span>
            </button>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/90 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-3 shadow-2xs">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-xs font-semibold text-rose-800 flex items-center gap-3 shadow-2xs">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- ============================== 2. FILTER CARD ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <form method="GET" action="{{ route('administrasi.talent-pool.index') }}" class="space-y-4">
            {{-- Baris 1: Filter Utama (Tahun & Jenis Kategori) berbentuk Dropdown --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-4 border-b border-slate-100">
                {{-- Dropdown Tahun --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                        <span>Tahun</span>
                    </label>
                    <select name="year" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-[#4B6A34] focus:ring-2 focus:ring-[#4B6A34]/20 outline-none transition-all cursor-pointer">
                        <option value="all" {{ ($year === 'all' || !$rawYear || $rawYear === 'all') ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach($years as $yOption)
                            <option value="{{ $yOption }}" {{ ((string)$year === (string)$yOption && $rawYear !== 'all') ? 'selected' : '' }}>
                                Tahun {{ $yOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown Jenis Kategori --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                        <span>Jenis Kategori</span>
                    </label>
                    <select name="jenis_kategori" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-[#4B6A34] focus:ring-2 focus:ring-[#4B6A34]/20 outline-none transition-all cursor-pointer">
                        <option value="all" {{ ($jenisKategori === 'all' || !$jenisKategori) ? 'selected' : '' }}>Semua Jenis ({{ $totalAllJenis }})</option>
                        @foreach($jenisKategoriList as $jk)
                            <option value="{{ $jk }}" {{ strcasecmp($jenisKategori, $jk) === 0 ? 'selected' : '' }}>
                                {{ $jk }} ({{ $jenisCounts[$jk] ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Baris 2: Status Penempatan, Region, Target Posisi, Cari Kandidat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                {{-- Filter Status Penempatan --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Penempatan</label>
                    <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] outline-none cursor-pointer">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                        @foreach($statusList as $st)
                            <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Region --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Region</label>
                    <select name="region" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] outline-none cursor-pointer">
                        <option value="all" {{ $region === 'all' ? 'selected' : '' }}>Semua Region</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg }}" {{ $region === $reg ? 'selected' : '' }}>{{ $reg }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Target Posisi (Talent To) --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Target Posisi (Talent To)</label>
                    <select name="talent_to" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] outline-none cursor-pointer">
                        <option value="all" {{ $talentTo === 'all' ? 'selected' : '' }}>Semua Target Posisi</option>
                        @foreach($talentToList as $tTo)
                            <option value="{{ $tTo }}" {{ $talentTo === $tTo ? 'selected' : '' }}>{{ $tTo }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Bar --}}
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Cari Kandidat</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK, Nama, Posisi, Unit, Lokasi..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] outline-none">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                            Cari
                        </button>
                        @if($search || $status !== 'all' || $region !== 'all' || $talentTo !== 'all' || $year !== 'all' || $jenisKategori !== 'all')
                            <a href="{{ route('administrasi.talent-pool.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors flex items-center justify-center" title="Reset Filter">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ============================== 3. SUMMARY KPI CARDS ============================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Card 1: Total Talent Pool (Menghitung status Belum Penempatan) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700">Total Talent Pool</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900">{{ $countBelum }}</span>
                <span class="text-xs font-medium text-slate-400">Kandidat</span>
            </div>
            <div class="mt-3 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200/60 px-3 py-1 rounded-lg w-fit flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span>Status: Belum Penempatan (Aktif Dalam Pool)</span>
            </div>
        </div>

        {{-- Card 2: Sudah Penempatan --}}
        <div class="bg-white rounded-2xl border border-emerald-200/80 p-5 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Sudah Penempatan</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-700">{{ $countSudah }}</span>
                <span class="text-xs font-medium text-slate-400">Orang</span>
            </div>
            <div class="mt-3 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-3 py-1 rounded-lg w-fit flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>{{ ($countBelum + $countSudah) > 0 ? round(($countSudah / ($countBelum + $countSudah)) * 100, 1) : 0 }}% Telah Ditempatkan</span>
            </div>
        </div>
    </div>

    {{-- ============================== 3.5 GRAFIK TARGET POSISI (TALENT TO) ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-50 text-[#4B6A34] flex items-center justify-center">
                        <i data-lucide="bar-chart-2" class="w-3.5 h-3.5"></i>
                    </span>
                    <span>Distribusi Target Posisi</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Grafik jumlah kandidat talent pool berdasarkan target posisi (Talent To)</p>
            </div>
            <div class="text-xs font-semibold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/60 w-fit flex items-center gap-2">
                <span>Total Target Posisi:</span>
                <span class="text-slate-900 font-bold bg-white px-2 py-0.5 rounded-md border border-slate-200">{{ count($chartLabels) }}</span>
            </div>
        </div>

        @if(count($chartLabels) > 0)
            <div class="h-64 sm:h-72 w-full">
                <canvas id="chartTargetPosisi"></canvas>
            </div>
        @else
            <div class="h-44 flex flex-col items-center justify-center text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                <i data-lucide="bar-chart" class="w-8 h-8 text-slate-300 mb-2"></i>
                <span class="text-xs font-semibold text-slate-500">Tidak ada data target posisi untuk filter yang dipilih</span>
            </div>
        @endif
    </div>

    {{-- ============================== 4. DATA TABLE ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-3.5 py-3.5 text-center w-12">No</th>
                        <th class="px-3.5 py-3.5">Karyawan</th>
                        <th class="px-3.5 py-3.5">Posisi Asal</th>
                        <th class="px-3.5 py-3.5">Target Posisi (Talent To)</th>
                        <th class="px-3.5 py-3.5">Unit & Region</th>
                        <th class="px-3.5 py-3.5">Status Penempatan</th>
                        <th class="px-3.5 py-3.5">Lokasi / Tgl Penempatan</th>
                        <th class="px-3.5 py-3.5">Keterangan</th>
                        <th class="px-3.5 py-3.5">Jenis Kategori</th>
                        <th class="px-3.5 py-3.5 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($talents as $index => $item)
                        @php
                            $isSudah = strcasecmp(trim($item->status ?? ''), 'Sudah penempatan') === 0;
                            $statusBadge = $isSudah
                                ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
                                : 'bg-amber-50 text-amber-800 border-amber-200';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            {{-- No --}}
                            <td class="px-3.5 py-3.5 text-center font-bold text-slate-400">
                                {{ $talents->firstItem() + $index }}
                            </td>

                            {{-- Employee --}}
                            <td class="px-3.5 py-3.5">
                                <div class="font-bold text-slate-900 leading-snug">{{ $item->employee_name }}</div>
                                <div class="text-[11px] font-mono text-slate-500 mt-0.5">NIK: {{ $item->employee_id ?: '-' }}</div>
                            </td>

                            {{-- Posisi Asal --}}
                            <td class="px-3.5 py-3.5 font-medium text-slate-700">
                                {{ $item->posisi_asal ?: '-' }}
                            </td>

                            {{-- Talent To --}}
                            <td class="px-3.5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                    {{ $item->talent_to ?: '-' }}
                                </span>
                            </td>

                            {{-- Unit & Region --}}
                            <td class="px-3.5 py-3.5">
                                <div class="font-bold text-slate-800">{{ $item->unit ?: '-' }}</div>
                                <div class="text-[11px] text-slate-500 font-medium">{{ $item->region ?: '-' }}</div>
                            </td>

                            {{-- Status Penempatan --}}
                            <td class="px-3.5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isSudah ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    <span>{{ $item->status }}</span>
                                </span>
                            </td>

                            {{-- Lokasi / Tanggal Penempatan --}}
                            <td class="px-3.5 py-3.5">
                                <div class="font-bold text-slate-800">{{ $item->lokasi_penempatan ?: '-' }}</div>
                                @if($item->tanggal_penempatan)
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span>{{ $item->tanggal_penempatan->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Keterangan --}}
                            <td class="px-3.5 py-3.5 font-medium text-slate-700 max-w-44">
                                <div class="truncate" title="{{ $item->keterangan }}">{{ $item->keterangan ?: '-' }}</div>
                            </td>

                            {{-- Jenis Kategori --}}
                            <td class="px-3.5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $item->jenis_kategori ?: '-' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3.5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5 opacity-90 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick="openEditModal({{ json_encode($item) }})" class="p-1.5 text-slate-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <button type="button" onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->employee_name) }}')" class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 mb-3 border border-slate-100">
                                        <i data-lucide="users-round" class="w-6 h-6"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600">Belum ada data kandidat Talent Pool</span>
                                    <span class="text-xs text-slate-400 mt-1 max-w-md">Klik tombol "Tambah Kandidat" atau "Import Excel/CSV" untuk mulai memasukkan data peserta.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($talents->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $talents->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ============================== 5. MODALS ============================== --}}

{{-- Modal Tambah --}}
<div id="modalAdd" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4 text-[#4B6A34]"></i>
                Tambah Kandidat Talent Pool
            </h3>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('administrasi.talent-pool.store') }}" method="POST" class="p-5 space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIK / ID Karyawan</label>
                    <input type="text" name="employee_id" placeholder="Contoh: EMP001" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="employee_name" required placeholder="Nama kandidat" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Posisi Asal</label>
                    <input type="text" name="posisi_asal" placeholder="Contoh: Asisten Agronomi" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Target Posisi (Talent To)</label>
                    <input type="text" name="talent_to" placeholder="Contoh: Askep Agronomi" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Unit / Kebun / PKS</label>
                    <input type="text" name="unit" placeholder="Contoh: Kebun Sukamaju" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Region</label>
                    <input type="text" name="region" placeholder="Contoh: RIAU, KALBAR, KALTENG..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Penempatan <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium bg-white">
                        <option value="Belum Penempatan">Belum Penempatan</option>
                        <option value="Sudah penempatan">Sudah penempatan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lokasi Penempatan</label>
                    <input type="text" name="lokasi_penempatan" placeholder="Unit / Kebun tujuan penempatan" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Penempatan</label>
                    <input type="date" name="tanggal_penempatan" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium bg-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" value="{{ date('Y') }}" min="2000" max="2100" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Kategori <span class="text-red-500">*</span></label>
                    <select name="jenis_kategori" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium bg-white">
                        <option value="Estate">Estate</option>
                        <option value="Mill">Mill</option>
                        <option value="Traksi">Traksi</option>
                        <option value="Administrasi">Administrasi</option>
                        <option value="Downstream">Downstream</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan mengenai kandidat atau penempatan..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-xl font-bold transition-colors">
                    Simpan Kandidat
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                <i data-lucide="edit" class="w-4 h-4 text-[#4B6A34]"></i>
                Edit Kandidat Talent Pool
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="formEdit" method="POST" class="p-5 space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIK / ID Karyawan</label>
                    <input type="text" name="employee_id" id="edit_employee_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="employee_name" id="edit_employee_name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Posisi Asal</label>
                    <input type="text" name="posisi_asal" id="edit_posisi_asal" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Target Posisi (Talent To)</label>
                    <input type="text" name="talent_to" id="edit_talent_to" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Unit / Kebun / PKS</label>
                    <input type="text" name="unit" id="edit_unit" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Region</label>
                    <input type="text" name="region" id="edit_region" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Penempatan <span class="text-red-500">*</span></label>
                    <select name="status" id="edit_status" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium bg-white">
                        <option value="Belum Penempatan">Belum Penempatan</option>
                        <option value="Sudah penempatan">Sudah penempatan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lokasi Penempatan</label>
                    <input type="text" name="lokasi_penempatan" id="edit_lokasi_penempatan" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Penempatan</label>
                    <input type="date" name="tanggal_penempatan" id="edit_tanggal_penempatan" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium bg-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" id="edit_tahun" min="2000" max="2100" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Kategori <span class="text-red-500">*</span></label>
                    <select name="jenis_kategori" id="edit_jenis_kategori" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium bg-white">
                        <option value="Estate">Estate</option>
                        <option value="Mill">Mill</option>
                        <option value="Traksi">Traksi</option>
                        <option value="Administrasi">Administrasi</option>
                        <option value="Downstream">Downstream</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-green-600 font-medium"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-xl font-bold transition-colors">
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Import --}}
<div id="modalImport" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                <i data-lucide="upload" class="w-4 h-4 text-[#4B6A34]"></i>
                Import Data Talent Pool
            </h3>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('administrasi.talent-pool.import') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih File Excel / CSV (.xlsx, .csv)</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full border border-slate-200 rounded-xl p-2 font-medium file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#4B6A34] hover:file:bg-emerald-100">
                <p class="text-[11px] text-slate-400 mt-1">Gunakan template resmi untuk format kolom yang sesuai.</p>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-xl font-bold transition-colors">
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Hapus Single --}}
<div id="modalDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
        <div class="p-5 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <h3 class="font-bold text-base text-slate-900">Hapus Data Kandidat?</h3>
            <p class="text-xs text-slate-500">Anda yakin ingin menghapus data kandidat <strong id="delete_name_label" class="text-slate-800"></strong> dari Talent Pool?</p>
        </div>
        <form id="formDelete" method="POST" class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-slate-200 bg-white text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition-colors">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

{{-- Modal Hapus Semua --}}
<div id="modalDeleteAll" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
        <div class="p-5 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <h3 class="font-bold text-base text-slate-900">Hapus SEMUA Data?</h3>
            <p class="text-xs text-slate-500">Tindakan ini akan mengosongkan <strong>seluruh data Talent Pool</strong> secara permanen.</p>
        </div>
        <form action="{{ route('administrasi.talent-pool.destroyAll') }}" method="POST" class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteAllModal()" class="px-4 py-2 border border-slate-200 bg-white text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition-colors">
                Batal
            </button>
            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors">
                Hapus Semua Data
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chartTargetPosisi');
        if (ctx) {
            const chartLabels = {!! json_encode($chartLabels) !!};
            const chartData = {!! json_encode($chartTotals) !!};

            if (chartLabels.length > 0) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Jumlah Kandidat',
                            data: chartData,
                            backgroundColor: '#4B6A34',
                            hoverBackgroundColor: '#3d562a',
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 45
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.parsed.y + ' Kandidat';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: { size: 11, weight: '500' },
                                    color: '#64748b'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 11, weight: '600' },
                                    color: '#334155',
                                    maxRotation: 30,
                                    minRotation: 0,
                                    autoSkip: false
                                }
                            }
                        }
                    }
                });
            }
        }
    });

    function openAddModal() {
        document.getElementById('modalAdd').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('modalAdd').classList.add('hidden');
    }

    function openEditModal(item) {
        document.getElementById('formEdit').action = `/administrasi/talent-pool/${item.id}`;
        document.getElementById('edit_employee_id').value = item.employee_id || '';
        document.getElementById('edit_employee_name').value = item.employee_name || '';
        document.getElementById('edit_posisi_asal').value = item.posisi_asal || '';
        document.getElementById('edit_talent_to').value = item.talent_to || '';
        document.getElementById('edit_unit').value = item.unit || '';
        document.getElementById('edit_region').value = item.region || '';
        document.getElementById('edit_status').value = item.status || 'Belum Penempatan';
        document.getElementById('edit_lokasi_penempatan').value = item.lokasi_penempatan || '';
        document.getElementById('edit_tanggal_penempatan').value = item.tanggal_penempatan ? item.tanggal_penempatan.substring(0, 10) : '';
        document.getElementById('edit_tahun').value = item.tahun || '';
        document.getElementById('edit_jenis_kategori').value = item.jenis_kategori || 'Estate';
        document.getElementById('edit_keterangan').value = item.keterangan || '';
        document.getElementById('modalEdit').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('modalEdit').classList.add('hidden');
    }

    function openImportModal() {
        document.getElementById('modalImport').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('modalImport').classList.add('hidden');
    }

    function openDeleteModal(id, name) {
        document.getElementById('formDelete').action = `/administrasi/talent-pool/${id}`;
        document.getElementById('delete_name_label').textContent = name;
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
</script>
@endpush

</x-admin-layout>
