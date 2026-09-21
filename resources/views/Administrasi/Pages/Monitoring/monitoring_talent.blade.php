<x-admin-layout>

<x-slot name="title">Monitoring Talent | FR Academy</x-slot>
    <x-slot name="header">Monitoring Talent | FR Academy</x-slot>


<div class="space-y-6">

    {{-- ============================== 1. HERO & ACTION BAR ============================== --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-green-700 transition-colors">Dashboard</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-400">Monitoring</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-green-700 font-bold">Monitoring Talent</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-green-100 text-green-800 flex items-center justify-center shrink-0">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </span>
                Monitoring Talent
            </h1>
            <p class="mt-1 text-xs text-slate-500 font-medium">Update posisi, penugasan, dan perkembangan peserta talent pool First Resources Academy.</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('administrasi.monitoring-talent.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                <i data-lucide="file-down" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>Template Excel</span>
            </a>
            
            <a href="{{ route('administrasi.monitoring-talent.export', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>Export Excel</span>
            </a>

            <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                <i data-lucide="upload" class="w-3.5 h-3.5 text-green-700"></i>
                <span>Import CSV</span>
            </button>

            <button type="button" onclick="openProgressMateriModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 px-3.5 py-2 text-xs font-bold transition-all shadow-2xs">
                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-700"></i>
                <span>Progres Materi: {{ $progressMateri }}%</span>
            </button>

            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 rounded-xl bg-[#4B6A34] hover:bg-[#3d562a] text-white px-4 py-2 text-xs font-bold transition-all shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Talent</span>
            </button>

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

    {{-- ============================== 2. SUMMARY KPI CARDS ============================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        {{-- Total Peserta --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Talent</span>
                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900">{{ $totalAll }}</span>
                <span class="text-[11px] font-medium text-slate-400">Orang</span>
            </div>
            <div class="mt-2 text-[10px] font-bold text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md w-fit">
                Semua Pool
            </div>
        </div>

        {{-- Sudah Penugasan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Sudah Penugasan</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-emerald-700">{{ $countSudah }}</span>
                <span class="text-[11px] font-medium text-slate-400">Orang</span>
            </div>
            <div class="mt-2 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md w-fit">
                {{ $totalAll > 0 ? round(($countSudah / $totalAll) * 100) : 0 }}% Dari Total
            </div>
        </div>

        {{-- Akan Penugasan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Akan Penugasan</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-amber-700">{{ $countAkan }}</span>
                <span class="text-[11px] font-medium text-slate-400">Orang</span>
            </div>
            <div class="mt-2 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md w-fit">
                Pipeline Siap
            </div>
        </div>

        {{-- Belum Penugasan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700">Belum Penugasan</span>
                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                    <i data-lucide="user-x" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-700">{{ $countBelum }}</span>
                <span class="text-[11px] font-medium text-slate-400">Orang</span>
            </div>
            <div class="mt-2 text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md w-fit">
                Standby
            </div>
        </div>

        {{-- Sudah Confirm --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs relative overflow-hidden flex flex-col justify-between col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800">Sudah Confirm</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                    <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-blue-700">{{ $countConfirm }}</span>
                <span class="text-[11px] font-medium text-slate-400">Orang</span>
            </div>
            <div class="mt-2 text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md w-fit">
                Confirmed Role
            </div>
        </div>
    </div>

    {{-- ============================== 3. FILTER TABS KATEGORI & CONTROLS ============================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        
        {{-- Category Pills --}}
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2.5 flex items-center gap-1.5">
                <i data-lucide="tag" class="w-3.5 h-3.5 text-green-700"></i>
                <span>Filter Kategori Talent:</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Semua Kategori --}}
                <a href="{{ route('administrasi.monitoring-talent.index', array_merge(request()->query(), ['kategori' => 'all'])) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs {{ ($kategori === 'all' || !$kategori) ? 'bg-[#4B6A34] text-white shadow-xs ring-2 ring-[#4B6A34]/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                    <span>Semua Kategori</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold {{ ($kategori === 'all' || !$kategori) ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $totalAll }}
                    </span>
                </a>

                {{-- Dynamic Categories --}}
                @foreach($categories as $cat)
                    <a href="{{ route('administrasi.monitoring-talent.index', array_merge(request()->query(), ['kategori' => $cat])) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs {{ $kategori === $cat ? 'bg-[#4B6A34] text-white shadow-xs ring-2 ring-[#4B6A34]/20' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200' }}">
                        <span>{{ $cat }}</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold {{ $kategori === $cat ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $categoryCounts[$cat] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Dropdown Filters & Search --}}
        <form method="GET" action="{{ route('administrasi.monitoring-talent.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2 border-t border-slate-100">
            <input type="hidden" name="kategori" value="{{ $kategori }}">

            {{-- Filter Status --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Penugasan</label>
                <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Region --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Region</label>
                <select name="region" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                    <option value="all" {{ $region === 'all' ? 'selected' : '' }}>Semua Region</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg }}" {{ $region === $reg ? 'selected' : '' }}>{{ $reg }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Talent To (Posisi Target) --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Target Posisi (Talent To)</label>
                <select name="talent_to" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-700 focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                    <option value="all" {{ $talentTo === 'all' ? 'selected' : '' }}>Semua Posisi Target</option>
                    @foreach($talentToList as $tTo)
                        <option value="{{ $tTo }}" {{ $talentTo === $tTo ? 'selected' : '' }}>{{ $tTo }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Search Bar --}}
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Cari Data Talent</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari ID, Nama, Unit, Posisi, Mentor..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                        Cari
                    </button>
                    @if($search || $kategori !== 'all' || $status !== 'all' || $region !== 'all' || $talentTo !== 'all')
                        <a href="{{ route('administrasi.monitoring-talent.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors flex items-center justify-center" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
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
                        <th class="px-3.5 py-3.5">Talent To</th>
                        <th class="px-3.5 py-3.5">Unit & Region</th>
                        <th class="px-3.5 py-3.5">Mentor</th>
                        <th class="px-3.5 py-3.5">Status Penugasan</th>
                        <th class="px-3.5 py-3.5">Keterangan / Plt</th>
                        <th class="px-3.5 py-3.5">Periode & Lokasi Baru</th>
                        <th class="px-3.5 py-3.5">Kategori</th>
                        <th class="px-3.5 py-3.5 text-right w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($talents as $index => $item)
                        @php
                            $stLower = strtolower($item->status ?? '');
                            $statusClass = 'bg-slate-100 text-slate-700 border-slate-200';
                            if (str_contains($stLower, 'sudah penugasan')) {
                                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif (str_contains($stLower, 'akan penugasan')) {
                                $statusClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            } elseif (str_contains($stLower, 'confirm')) {
                                $statusClass = 'bg-blue-50 text-blue-700 border-blue-200';
                            } elseif (str_contains($stLower, 'belum penugasan')) {
                                $statusClass = 'bg-rose-50 text-rose-700 border-rose-200';
                            }

                            $katBadge = 'bg-slate-100 text-slate-700 border-slate-200';
                            if ($item->kategori === 'TALENT ESTATE TAHUN 2026') {
                                $katBadge = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                            } elseif ($item->kategori === 'TALENT ESTATE REDEVELOP TAHUN 2025') {
                                $katBadge = 'bg-amber-50 text-amber-800 border-amber-200';
                            } elseif ($item->kategori === 'TALENT MILL TAHUN 2026') {
                                $katBadge = 'bg-blue-50 text-blue-800 border-blue-200';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            {{-- No --}}
                            <td class="px-3.5 py-3.5 text-center font-bold text-slate-400">
                                {{ $talents->firstItem() + $index }}
                            </td>

                            {{-- Employee --}}
                            <td class="px-3.5 py-3.5">
                                <div class="font-bold text-slate-900 leading-snug">{{ $item->employee_name }}</div>
                                <div class="text-[11px] font-mono text-slate-500 mt-0.5">ID: {{ $item->employee_id ?: '-' }}</div>
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

                            {{-- Mentor --}}
                            <td class="px-3.5 py-3.5 text-[11px]">
                                <div class="text-slate-800 font-semibold truncate max-w-40" title="{{ $item->mentor_lapangan }}">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold">Lap:</span> {{ $item->mentor_lapangan ?: '-' }}
                                </div>
                                <div class="text-slate-600 mt-0.5 truncate max-w-40" title="{{ $item->mentor_frlc }}">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold">FRLC:</span> {{ $item->mentor_frlc ?: '-' }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-3.5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $item->status ?: '-' }}
                                </span>
                            </td>

                            {{-- Keterangan / Plt --}}
                            <td class="px-3.5 py-3.5 font-medium text-slate-800 max-w-48">
                                <div class="truncate" title="{{ $item->keterangan }}">{{ $item->keterangan ?: '-' }}</div>
                                @if($item->alasan)
                                    <div class="text-[10px] text-slate-400 mt-0.5 italic truncate" title="{{ $item->alasan }}">
                                        {{ $item->alasan }}
                                    </div>
                                @endif
                            </td>

                            {{-- Periode & Lokasi Baru --}}
                            <td class="px-3.5 py-3.5 text-[11px] max-w-48">
                                <div class="font-bold text-slate-800 truncate" title="{{ $item->lokasi_penugasan_baru }}">
                                    {{ $item->lokasi_penugasan_baru ?: '-' }}
                                </div>
                                <div class="text-slate-500 text-[10px] mt-0.5 truncate" title="{{ $item->periode_penugasan_baru }}">
                                    {{ $item->periode_penugasan_baru ?: ($item->periode_penugasan_lama ?: '-') }}
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-3.5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $katBadge }} whitespace-nowrap">
                                    {{ str_replace('TALENT ', '', $item->kategori) }}
                                </span>
                            </td>

                            {{-- Action --}}
                            <td class="px-3.5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick='openEditModal(@json($item))' class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Data">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <form method="POST" action="{{ route('administrasi.monitoring-talent.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta talent {{ $item->employee_name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Data">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-300"></i>
                                    <span class="text-sm font-semibold">Tidak ada data peserta talent yang sesuai filter.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100 px-5 py-3.5 text-xs text-slate-500">
            <span>Menampilkan {{ $talents->firstItem() ?? 0 }} - {{ $talents->lastItem() ?? 0 }} dari {{ $talents->total() }} peserta</span>
            <div>{{ $talents->links() }}</div>
        </div>
    </div>
</div>

{{-- ============================== MODAL TAMBAH TALENT ============================== --}}
<div id="modalAddTalent" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-[#fcfcf9]">
            <div>
                <h2 class="text-base font-bold text-slate-900">Tambah Peserta Talent</h2>
                <p class="text-xs text-slate-500 mt-0.5">Input data peserta baru ke dalam talent pool</p>
            </div>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('administrasi.monitoring-talent.store') }}" class="flex-1 overflow-y-auto p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Employee ID / NIK</label>
                    <input name="employee_id" placeholder="Contoh: 2023120481" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input name="employee_name" required placeholder="Nama lengkap karyawan" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Posisi Asal</label>
                    <input name="posisi_asal" placeholder="Contoh: Asisten Kebun" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Talent To (Target)</label>
                    <input name="talent_to" placeholder="Contoh: Askep" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Unit</label>
                    <input name="unit" placeholder="Contoh: ATS1" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Region</label>
                    <input name="region" placeholder="Contoh: Riau, Kalbar, Kaltim" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Mentor Lapangan</label>
                    <input name="mentor_lapangan" placeholder="Nama mentor lapangan" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Mentor FRLC</label>
                    <input name="mentor_frlc" placeholder="Nama mentor FRLC" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Status Penugasan</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                        <option value="Sudah Penugasan">Sudah Penugasan</option>
                        <option value="Akan Penugasan">Akan Penugasan</option>
                        <option value="Belum Penugasan">Belum Penugasan</option>
                        <option value="Sudah Confirm Askep">Sudah Confirm Askep</option>
                        <option value="Sudah Confirm Mgr. Mill">Sudah Confirm Mgr. Mill</option>
                        <option value="Sudah Confirm Mgr. Traksi">Sudah Confirm Mgr. Traksi</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Kategori Talent <span class="text-red-500">*</span></label>
                    <input name="kategori" list="kategori_talent_list" required placeholder="Ketik atau pilih kategori talent..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                    <datalist id="kategori_talent_list">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Keterangan / Posisi Plt</label>
                    <input name="keterangan" placeholder="Contoh: Plt. Askep di ATS 1" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Periode Lama</label>
                    <input name="periode_penugasan_lama" placeholder="Contoh: Periode 1 April - 30 Juli 2026" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Periode Baru</label>
                    <input name="periode_penugasan_baru" placeholder="Contoh: Periode 1 Agustus - 30 November 2026" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Lokasi Penugasan Baru</label>
                    <input name="lokasi_penugasan_baru" placeholder="Contoh: Tetap / Plt. Askep ANJ Binanga" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Alasan Penugasan</label>
                    <input name="alasan" placeholder="Alasan penugasan / catatan rotasi" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-bold rounded-xl transition-colors shadow-xs">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL EDIT TALENT ============================== --}}
<div id="modalEditTalent" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-[#fcfcf9]">
            <div>
                <h2 class="text-base font-bold text-slate-900">Edit Data Peserta Talent</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi posisi dan penugasan talent</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="formEditTalent" method="POST" action="" class="flex-1 overflow-y-auto p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Employee ID / NIK</label>
                    <input name="employee_id" id="edit_employee_id" placeholder="Contoh: 2023120481" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input name="employee_name" id="edit_employee_name" required placeholder="Nama lengkap karyawan" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Posisi Asal</label>
                    <input name="posisi_asal" id="edit_posisi_asal" placeholder="Contoh: Asisten Kebun" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Talent To (Target)</label>
                    <input name="talent_to" id="edit_talent_to" placeholder="Contoh: Askep" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Unit</label>
                    <input name="unit" id="edit_unit" placeholder="Contoh: ATS1" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Region</label>
                    <input name="region" id="edit_region" placeholder="Contoh: Riau, Kalbar, Kaltim" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Mentor Lapangan</label>
                    <input name="mentor_lapangan" id="edit_mentor_lapangan" placeholder="Nama mentor lapangan" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Mentor FRLC</label>
                    <input name="mentor_frlc" id="edit_mentor_frlc" placeholder="Nama mentor FRLC" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Status Penugasan</label>
                    <select name="status" id="edit_status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                        <option value="Sudah Penugasan">Sudah Penugasan</option>
                        <option value="Akan Penugasan">Akan Penugasan</option>
                        <option value="Belum Penugasan">Belum Penugasan</option>
                        <option value="Sudah Confirm Askep">Sudah Confirm Askep</option>
                        <option value="Sudah Confirm Mgr. Mill">Sudah Confirm Mgr. Mill</option>
                        <option value="Sudah Confirm Mgr. Traksi">Sudah Confirm Mgr. Traksi</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Kategori Talent <span class="text-red-500">*</span></label>
                    <input name="kategori" id="edit_kategori" list="kategori_talent_list" required placeholder="Ketik atau pilih kategori talent..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Keterangan / Posisi Plt</label>
                    <input name="keterangan" id="edit_keterangan" placeholder="Contoh: Plt. Askep di ATS 1" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Periode Lama</label>
                    <input name="periode_penugasan_lama" id="edit_periode_penugasan_lama" placeholder="Contoh: Periode 1 April - 30 Juli 2026" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Periode Baru</label>
                    <input name="periode_penugasan_baru" id="edit_periode_penugasan_baru" placeholder="Contoh: Periode 1 Agustus - 30 November 2026" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Lokasi Penugasan Baru</label>
                    <input name="lokasi_penugasan_baru" id="edit_lokasi_penugasan_baru" placeholder="Contoh: Tetap / Plt. Askep ANJ Binanga" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Alasan Penugasan</label>
                    <input name="alasan" id="edit_alasan" placeholder="Alasan penugasan / catatan rotasi" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none">
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-bold rounded-xl transition-colors shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL IMPORT CSV ============================== --}}
<div id="modalImportCsv" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-[#fcfcf9]">
            <div>
                <h2 class="text-base font-bold text-slate-900">Import CSV Talent</h2>
                <p class="text-xs text-slate-500 mt-0.5">Unggah file CSV data monitoring talent</p>
            </div>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('administrasi.monitoring-talent.import') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Pilih File CSV / Excel</label>
                <input type="file" name="file" required accept=".csv,.txt" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer border border-slate-200 rounded-xl p-2">
                <p class="text-[11px] text-slate-400 mt-1.5">Gunakan format titik koma (;) atau koma (,).</p>
            </div>
            <div class="pt-3 flex items-center justify-end gap-2">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-bold rounded-xl transition-colors shadow-xs">Import Sekarang</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL DELETE ALL ============================== --}}
<div id="modalDeleteAll" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="p-5 border-b border-rose-100 bg-rose-50/50 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-rose-900">Hapus Semua Data Talent?</h2>
                <p class="text-xs text-rose-600 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="p-6 space-y-3">
            <p class="text-xs text-slate-600">Seluruh data peserta talent yang ada di dalam database akan dihapus secara permanen.</p>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
            <button type="button" onclick="closeDeleteAllModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
            <form method="POST" action="{{ route('administrasi.monitoring-talent.destroyAll') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs">Ya, Hapus Semua</button>
            </form>
        </div>
    </div>
</div>

{{-- ============================== JAVASCRIPT MODAL HANDLERS ============================== --}}
<script>
    function openAddModal() {
        document.getElementById('modalAddTalent').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('modalAddTalent').classList.add('hidden');
    }

    function openEditModal(talent) {
        const form = document.getElementById('formEditTalent');
        form.action = `/administrasi/monitoring-talent/${talent.id}`;
        
        document.getElementById('edit_employee_id').value = talent.employee_id || '';
        document.getElementById('edit_employee_name').value = talent.employee_name || '';
        document.getElementById('edit_posisi_asal').value = talent.posisi_asal || '';
        document.getElementById('edit_talent_to').value = talent.talent_to || '';
        document.getElementById('edit_unit').value = talent.unit || '';
        document.getElementById('edit_region').value = talent.region || '';
        document.getElementById('edit_mentor_lapangan').value = talent.mentor_lapangan || '';
        document.getElementById('edit_mentor_frlc').value = talent.mentor_frlc || '';
        document.getElementById('edit_status').value = talent.status || 'Sudah Penugasan';
        document.getElementById('edit_kategori').value = talent.kategori || 'TALENT ESTATE TAHUN 2026';
        document.getElementById('edit_keterangan').value = talent.keterangan || '';
        document.getElementById('edit_periode_penugasan_lama').value = talent.periode_penugasan_lama || '';
        document.getElementById('edit_periode_penugasan_baru').value = talent.periode_penugasan_baru || '';
        document.getElementById('edit_lokasi_penugasan_baru').value = talent.lokasi_penugasan_baru || '';
        document.getElementById('edit_alasan').value = talent.alasan || '';

        document.getElementById('modalEditTalent').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('modalEditTalent').classList.add('hidden');
    }

    function openImportModal() {
        document.getElementById('modalImportCsv').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('modalImportCsv').classList.add('hidden');
    }

    function openDeleteAllModal() {
        document.getElementById('modalDeleteAll').classList.remove('hidden');
    }
    function closeDeleteAllModal() {
        document.getElementById('modalDeleteAll').classList.add('hidden');
    }

    function openProgressMateriModal() {
        document.getElementById('modalProgressMateri').classList.remove('hidden');
    }
    function closeProgressMateriModal() {
        document.getElementById('modalProgressMateri').classList.add('hidden');
    }
</script>

{{-- Modal Edit Progres Materi Talent --}}
<div id="modalProgressMateri" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
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
            <button type="button" onclick="closeProgressMateriModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.monitoring-talent.update-progress-materi') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Persentase Progres Materi (%)</label>
                <div class="relative">
                    <input type="number" step="0.1" min="0" max="100" name="progress_materi" value="{{ $progressMateri }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400">%</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5">Nilai persentase ini akan disimpan dan ditampilkan pada Dashboard Realisasi & Monitoring Talent.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeProgressMateriModal()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
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
