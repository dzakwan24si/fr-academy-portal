@extends('Administrasi.Layouts.app')

@section('title', 'Training Record | FR Academy')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div><h1 class="text-2xl font-bold text-[#18181B]">Training Record</h1><p class="mt-1 text-sm text-[#71717A]">Kelola peserta berdasarkan training activity yang sudah tersedia.</p></div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('administrasi.training-record.template') }}" class="rounded-lg border border-[#E5E7EB] bg-white px-3 py-2 text-xs font-semibold text-[#374151]">Template Excel</a>
            <a href="{{ route('administrasi.training-record.export', request()->query()) }}" class="rounded-lg border border-[#E5E7EB] bg-white px-3 py-2 text-xs font-semibold text-[#374151]">Export Excel</a>
            <form method="POST" action="{{ route('administrasi.training-record.import') }}" enctype="multipart/form-data" class="flex items-center gap-2">@csrf<input type="file" name="file" required accept=".csv,.xlsx" class="max-w-52 text-xs"><button class="rounded-lg bg-[#4B6A34] px-3 py-2 text-xs font-semibold text-white">Import Data</button></form>
            <button type="button" onclick="openDeleteAllModal()" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 px-3 py-2 text-xs font-semibold transition-colors">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Hapus Semua Data</span>
            </button>
        </div>
    </div>
    @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    <form method="GET" class="space-y-3.5 rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-2xs">
        {{-- Filter Tahun --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-2 text-xs font-bold uppercase tracking-wider text-[#71717A] min-w-16">Tahun</span>
            <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['year' => 'all'])) }}" 
               class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition-all {{ ($year === 'all' || !$rawYear || $rawYear === 'all') ? 'border-[#4B6A34] bg-[#4B6A34] text-white shadow-xs' : 'border-[#E5E7EB] text-[#52525B] hover:bg-slate-50' }}">
                Semua Tahun
            </a>
            @foreach($years as $yOption)
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['year' => $yOption])) }}" 
                   class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition-all {{ ((string)$year === (string)$yOption && $rawYear !== 'all') ? 'border-[#4B6A34] bg-[#4B6A34] text-white shadow-xs' : 'border-[#E5E7EB] text-[#52525B] hover:bg-slate-50' }}">
                    {{ $yOption }}
                </a>
            @endforeach
        </div>

        {{-- Filter Jenis Tipe Program --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-2 text-xs font-bold uppercase tracking-wider text-[#71717A] min-w-16">Jenis</span>
            @foreach([
                'all' => 'Semua Program',
                'reguler' => 'Program Reguler',
                'non_reguler' => 'Program Non Reguler'
            ] as $value => $label)
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['type' => $value, 'category' => 'all'])) }}" 
                   class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition-all {{ $type === $value ? 'border-[#4B6A34] bg-[#4B6A34] text-white shadow-xs' : 'border-[#E5E7EB] text-[#52525B] hover:bg-slate-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Filter Sub-Kategori / Detail Kategori (Tampil Sesuai Jenis Program) --}}
        @if($type === 'reguler')
            <div class="flex flex-wrap items-center gap-2 bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100">
                <span class="mr-2 text-xs font-bold uppercase tracking-wider text-emerald-800 min-w-16">Kategori</span>
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['category' => 'all'])) }}"
                   class="rounded-lg border px-3 py-1 text-xs font-bold transition-all {{ ($category === 'all' || !$category) ? 'border-green-700 bg-green-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                    Semua Reguler (Staff & Mandor)
                </a>
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['category' => 'staf'])) }}"
                   class="rounded-lg border px-3 py-1 text-xs font-bold transition-all {{ $category === 'staf' ? 'border-green-700 bg-green-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                    Training Reguler - Staf
                </a>
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['category' => 'mandor'])) }}"
                   class="rounded-lg border px-3 py-1 text-xs font-bold transition-all {{ $category === 'mandor' ? 'border-green-700 bg-green-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                    Training Reguler - Mandor
                </a>
            </div>
        @elseif($type === 'non_reguler')
            <div class="flex flex-wrap items-center gap-2 bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100">
                <span class="mr-2 text-xs font-bold uppercase tracking-wider text-emerald-800 min-w-16">Kategori</span>
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['category' => 'all'])) }}"
                   class="rounded-lg border px-3 py-1 text-xs font-bold transition-all {{ ($category === 'all' || !$category) ? 'border-green-700 bg-green-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                    Semua Non Reguler
                </a>
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['category' => 'managerial'])) }}"
                   class="rounded-lg border px-3 py-1 text-xs font-bold transition-all {{ ($category === 'managerial' || strcasecmp($category, 'softskill') === 0) ? 'border-green-700 bg-green-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                    Managerial and Leadership (Softskill)
                </a>
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['category' => 'technical'])) }}"
                   class="rounded-lg border px-3 py-1 text-xs font-bold transition-all {{ $category === 'technical' ? 'border-green-700 bg-green-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                    Technical
                </a>

                {{-- Detail Technical Sub-categories --}}
                @foreach($nonRegulerSubCategories as $subCat)
                    @if(strcasecmp($subCat, 'softskill') !== 0)
                        <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['category' => $subCat])) }}"
                           class="rounded-lg border px-3 py-1 text-xs font-semibold transition-all {{ (strcasecmp($category, $subCat) === 0) ? 'border-emerald-600 bg-emerald-600 text-white font-bold' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                            {{ $subCat }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Filter Bulan --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-2 text-xs font-bold uppercase tracking-wider text-[#71717A] min-w-16">Bulan</span>
            <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['month' => 0])) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ !$month ? 'bg-[#4B6A34] text-white' : 'text-[#52525B]' }}">All</a>
            @foreach($months as $number => $label)
                <a href="{{ route('administrasi.training-record.index', array_merge(request()->query(), ['month' => $number])) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $month === $number ? 'bg-[#4B6A34] text-white' : 'text-[#52525B]' }}">{{ $label }}</a>
            @endforeach
        </div>

        {{-- Search Input --}}
        <div class="flex gap-2">
            @if(request('year')) <input type="hidden" name="year" value="{{ request('year') }}"> @endif
            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            @if(request('month')) <input type="hidden" name="month" value="{{ request('month') }}"> @endif
            <input name="search" value="{{ request('search') }}" placeholder="Cari NIK, nama, PT, lokasi, atau pelatihan..." class="min-w-0 flex-1 rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm outline-none focus:border-green-600">
            <button class="rounded-lg bg-[#27272A] px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors">Cari</button>
            <a href="{{ route('administrasi.training-record.index') }}" class="rounded-lg border border-slate-300 bg-slate-100 hover:bg-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition-colors flex items-center gap-1">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
            </a>
        </div>
    </form>

    <details class="rounded-xl border border-[#E5E7EB] bg-white p-4"><summary class="cursor-pointer text-sm font-bold text-[#18181B]">+ Tambah Training Record</summary><form method="POST" action="{{ route('administrasi.training-record.store') }}" class="mt-4 grid gap-3 md:grid-cols-4">@csrf
        <select name="program_type" id="training-record-program-type" required class="rounded-lg border px-3 py-2 text-sm"><option value="">Tipe activity</option><option value="reguler">Reguler</option><option value="non_reguler">Non Reguler</option></select>
        <select name="program_id" id="training-record-program-id" required class="rounded-lg border px-3 py-2 text-sm"><option value="">Pilih tipe activity terlebih dahulu</option>@foreach($activities as $activity)<option value="{{ $activity['id'] }}" data-type="{{ $activity['type'] }}">{{ ucfirst($activity['type']) }} #{{ $activity['id'] }} - {{ $activity['name'] }}</option>@endforeach</select>
        <input name="participant_identifier" required placeholder="NIK" class="rounded-lg border px-3 py-2 text-sm"><input name="participant_name" required placeholder="Nama" class="rounded-lg border px-3 py-2 text-sm"><input name="gender" placeholder="Jenis Kelamin" class="rounded-lg border px-3 py-2 text-sm"><input name="company" placeholder="Nama PT" class="rounded-lg border px-3 py-2 text-sm"><input name="work_location" placeholder="Lokasi Kerja" class="rounded-lg border px-3 py-2 text-sm"><input name="group_name" placeholder="Group" class="rounded-lg border px-3 py-2 text-sm"><input name="region" placeholder="REGION" class="rounded-lg border px-3 py-2 text-sm"><input name="gol" placeholder="GOL" class="rounded-lg border px-3 py-2 text-sm"><input name="position" placeholder="Jabatan" class="rounded-lg border px-3 py-2 text-sm"><input name="instructor" placeholder="Nama Instruktur" class="rounded-lg border px-3 py-2 text-sm"><input name="internal_external" placeholder="Internal/Eksternal" class="rounded-lg border px-3 py-2 text-sm"><input name="level" placeholder="Level" class="rounded-lg border px-3 py-2 text-sm"><input name="periode" placeholder="Periode" class="rounded-lg border px-3 py-2 text-sm"><input name="tanggal" placeholder="Tanggal (contoh: 01-31 atau 15)" class="rounded-lg border px-3 py-2 text-sm">
        <select name="bulan" class="rounded-lg border px-3 py-2 text-sm bg-white">
            <option value="">Pilih Bulan</option>
            <option value="JAN">Januari (JAN)</option>
            <option value="FEB">Februari (FEB)</option>
            <option value="MAR">Maret (MAR)</option>
            <option value="APR">April (APR)</option>
            <option value="MEI">Mei (MEI)</option>
            <option value="JUN">Juni (JUN)</option>
            <option value="JUL">Juli (JUL)</option>
            <option value="AGU">Agustus (AGU)</option>
            <option value="SEP">September (SEP)</option>
            <option value="OKT">Oktober (OKT)</option>
            <option value="NOV">November (NOV)</option>
            <option value="DES">Desember (DES)</option>
        </select>
        <input type="number" name="tahun" value="{{ date('Y') }}" placeholder="Tahun" min="2000" max="2100" class="rounded-lg border px-3 py-2 text-sm">
        <input name="notes" placeholder="Keterangan" class="rounded-lg border px-3 py-2 text-sm md:col-span-2"><button class="rounded-lg bg-[#4B6A34] px-4 py-2 text-sm font-semibold text-white md:col-span-4">Simpan Peserta</button>
    </form></details>

    <div class="overflow-hidden rounded-xl border border-[#E5E7EB] bg-white"><div class="overflow-x-auto"><table class="w-full min-w-375 text-xs"><thead class="bg-[#F9FAFB] text-left text-[10px] uppercase tracking-wider text-[#71717A]"><tr><th class="px-3 py-3">No</th><th class="px-3 py-3">NIK</th><th class="px-3 py-3">Nama</th><th class="px-3 py-3">Jenis Kelamin</th><th class="px-3 py-3">Nama PT</th><th class="px-3 py-3">Lokasi Kerja</th><th class="px-3 py-3">Group</th><th class="px-3 py-3">REGION</th><th class="px-3 py-3">GOL</th><th class="px-3 py-3">Jabatan</th><th class="px-3 py-3">Nama Pelatihan</th><th class="px-3 py-3">Instruktur</th><th class="px-3 py-3">Tempat</th><th class="px-3 py-3">Kategori</th><th class="px-3 py-3">Internal/Eksternal</th><th class="px-3 py-3">Level</th><th class="px-3 py-3">Periode</th><th class="px-3 py-3">Tanggal</th><th class="px-3 py-3">Bulan</th><th class="px-3 py-3">Tahun</th><th class="px-3 py-3">Keterangan</th><th class="px-3 py-3 text-right">Action</th></tr></thead><tbody class="divide-y divide-[#F4F4F5]">@forelse($records as $index => $record)@php($activity = $record->activity())@php($actInfo = $activities->get($record->program_type . ':' . $record->program_id))<tr class="hover:bg-[#FAFAFA] group"><td class="px-3 py-3">{{ $records->firstItem() + $index }}</td><td class="px-3 py-3 font-semibold">{{ str_starts_with($record->participant_identifier, 'NAME:') ? '-' : $record->participant_identifier }}</td><td class="px-3 py-3">{{ $record->participant_name }}</td><td class="px-3 py-3">{{ $record->gender }}</td><td class="px-3 py-3">{{ $record->company }}</td><td class="px-3 py-3">{{ $record->work_location }}</td><td class="px-3 py-3">{{ $record->group_name }}</td><td class="px-3 py-3">{{ $record->region }}</td><td class="px-3 py-3">{{ $record->gol }}</td><td class="px-3 py-3">{{ $record->position }}</td><td class="px-3 py-3">{{ $record->activityName() }}</td><td class="px-3 py-3">{{ $record->instructor ?: $record->activityTrainer() }}</td><td class="px-3 py-3">{{ $record->program_type === 'reguler' ? $activity?->Lokasi_training : $activity?->lokasi_training }}</td><td class="px-3 py-3">@if($record->program_type === 'reguler')<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Reguler</span>@elseif(($actInfo['sub_type'] ?? '') === 'soft_skill')<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Soft Skill</span>@else<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Teknikal</span>@endif</td><td class="px-3 py-3">{{ $record->internal_external }}</td><td class="px-3 py-3">{{ $record->level }}</td><td class="px-3 py-3">{{ $record->periode }}</td><td class="px-3 py-3">{{ $record->tanggal ?: '-' }}</td><td class="px-3 py-3 font-semibold text-[#18181B]">{{ $record->bulan ?: '-' }}</td><td class="px-3 py-3">{{ $record->tahun ?: '-' }}</td><td class="px-3 py-3">{{ $record->notes }}</td><td class="px-3 py-3 text-right"><div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity"><a href="{{ route('administrasi.training-record.show', $record) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Detail"><i data-lucide="eye" class="w-4 h-4"></i></a><button type="button" onclick='openEditModal(@json($record))' class="p-1.5 text-amber-600 hover:bg-amber-50 rounded transition-colors" title="Edit"><i data-lucide="edit-2" class="w-4 h-4"></i></button><form method="POST" action="{{ route('administrasi.training-record.destroy', $record) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline">@csrf @method('DELETE')<button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus"><i data-lucide="trash-2" class="w-4 h-4"></i></button></form></div></td></tr>@empty<tr><td colspan="22" class="px-4 py-10 text-center text-sm text-[#71717A]">Belum ada Training Record.</td></tr>@endforelse</tbody></table></div><div class="flex flex-col gap-3 border-t border-[#E5E7EB] px-4 py-3 text-xs text-[#71717A] sm:flex-row sm:items-center sm:justify-between"><span>Menampilkan {{ $records->firstItem() ?? 0 }}-{{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} peserta</span><div>{{ $records->onEachSide(1)->links() }}</div></div></div>
</div>

<!-- Modal Edit Training Record -->
<div id="modalEditTrainingRecord" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-150">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-[#fcfcf9]">
            <div><h2 class="text-lg font-bold text-slate-800">Edit Training Record</h2><p class="text-xs text-slate-500 mt-0.5">Perbarui data peserta dan training activity terkait</p></div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form id="formEditTrainingRecord" method="POST" action="" class="flex-1 overflow-y-auto p-5 space-y-4">
            @csrf @method('PUT')
            <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Tipe Activity <span class="text-red-500">*</span></label><select name="program_type" id="edit-program-type" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs bg-white focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"><option value="reguler">Reguler</option><option value="non_reguler">Non Reguler</option></select></div>
                <div class="sm:col-span-3"><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Training Activity <span class="text-red-500">*</span></label><select name="program_id" id="edit-program-id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs bg-white focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"><option value="">Pilih activity</option>@foreach($activities as $activity)<option value="{{ $activity['id'] }}" data-type="{{ $activity['type'] }}">{{ ucfirst($activity['type']) }} #{{ $activity['id'] }} - {{ $activity['name'] }}</option>@endforeach</select></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">NIK <span class="text-red-500">*</span></label><input name="participant_identifier" id="edit-participant_identifier" required placeholder="NIK" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label><input name="participant_name" id="edit-participant_name" required placeholder="Nama" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Jenis Kelamin</label><input name="gender" id="edit-gender" placeholder="Jenis Kelamin" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama PT</label><input name="company" id="edit-company" placeholder="Nama PT" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Lokasi Kerja</label><input name="work_location" id="edit-work_location" placeholder="Lokasi Kerja" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Group</label><input name="group_name" id="edit-group_name" placeholder="Group" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">REGION</label><input name="region" id="edit-region" placeholder="REGION" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">GOL</label><input name="gol" id="edit-gol" placeholder="GOL" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Jabatan</label><input name="position" id="edit-position" placeholder="Jabatan" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Nama Instruktur</label><input name="instructor" id="edit-instructor" placeholder="Nama Instruktur" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Internal / Eksternal</label><input name="internal_external" id="edit-internal_external" placeholder="Internal/Eksternal" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Level</label><input name="level" id="edit-level" placeholder="Level" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Periode</label><input name="periode" id="edit-periode" placeholder="Periode" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Tanggal</label><input name="tanggal" id="edit-tanggal" placeholder="Tanggal (contoh: 01-31 atau 15)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Bulan</label><select name="bulan" id="edit-bulan" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs bg-white focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"><option value="">Pilih Bulan</option><option value="JAN">Januari (JAN)</option><option value="FEB">Februari (FEB)</option><option value="MAR">Maret (MAR)</option><option value="APR">April (APR)</option><option value="MEI">Mei (MEI)</option><option value="JUN">Juni (JUN)</option><option value="JUL">Juli (JUL)</option><option value="AGU">Agustus (AGU)</option><option value="SEP">September (SEP)</option><option value="OKT">Oktober (OKT)</option><option value="NOV">November (NOV)</option><option value="DES">Desember (DES)</option></select></div>
                <div><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Tahun</label><input type="number" name="tahun" id="edit-tahun" placeholder="Tahun" min="2000" max="2100" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
                <div class="sm:col-span-2 lg:col-span-4"><label class="block text-[11px] font-bold uppercase text-slate-600 mb-1">Keterangan</label><input name="notes" id="edit-notes" placeholder="Keterangan" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-green-600 focus:ring-1 focus:ring-green-600 outline-none"></div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-bold rounded-lg transition-colors shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3-Step Confirmation Delete All Training Record -->
<div id="modalDeleteAllTrainingRecord" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-red-100 animate-in fade-in zoom-in duration-150">
        <!-- Modal Top Bar -->
        <div class="p-4 bg-red-50 border-b border-red-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <div>
                    <span id="deleteAllStepBadge" class="text-[10px] font-extrabold uppercase tracking-wider bg-red-200 text-red-800 px-2 py-0.5 rounded-full">
                        LANGKAH 1 / 3
                    </span>
                </div>
            </div>
            <button type="button" onclick="closeDeleteAllModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 space-y-4">
            <div class="flex items-center gap-1.5 justify-center py-1" id="deleteAllStepDots">
                <!-- Dots rendered dynamically -->
            </div>

            <div class="text-center space-y-2">
                <h3 id="deleteAllTitle" class="text-base font-extrabold text-slate-800"></h3>
                <p id="deleteAllDesc" class="text-xs text-slate-600 leading-relaxed max-w-md mx-auto"></p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2.5 text-xs text-amber-800">
                <i data-lucide="info" class="w-4 h-4 mt-0.5 shrink-0 text-amber-600"></i>
                <span>Sistem membutuhkan 3 kali konfirmasi beruntun demi keamanan data operasional.</span>
            </div>
        </div>

        <!-- Modal Actions -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button type="button" onclick="closeDeleteAllModal()" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                Batalkan
            </button>
            <button type="button" id="deleteAllConfirmBtn" onclick="proceedDeleteAll()" class="px-5 py-2.5 text-white text-xs font-bold rounded-lg transition-colors shadow-sm bg-red-600 hover:bg-red-700">
                Lanjutkan (1/3)
            </button>
        </div>

        <!-- Hidden Form for Submit Delete All -->
        <form id="formDeleteAllTrainingRecord" method="POST" action="{{ route('administrasi.training-record.destroy-all') }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const type = document.getElementById('training-record-program-type');
    const activity = document.getElementById('training-record-program-id');
    if (type && activity) {
        const options = [...activity.options].slice(1).map((option) => option.cloneNode(true));
        type.addEventListener('change', () => {
            activity.innerHTML = '<option value="">Pilih activity</option>';
            options.filter((option) => option.dataset.type === type.value).forEach((option) => activity.appendChild(option.cloneNode(true)));
        });
    }

    const editType = document.getElementById('edit-program-type');
    const editActivity = document.getElementById('edit-program-id');
    if (editType && editActivity) {
        window.allEditActivityOptions = [...editActivity.options].slice(1).map((opt) => opt.cloneNode(true));
        editType.addEventListener('change', () => {
            filterEditActivityOptions(editType.value, '');
        });
    }

    if (window.lucide) {
        lucide.createIcons();
    }
});

function filterEditActivityOptions(selectedType, selectedId) {
    const editActivity = document.getElementById('edit-program-id');
    if (!editActivity || !window.allEditActivityOptions) return;
    editActivity.innerHTML = '<option value="">Pilih activity</option>';
    window.allEditActivityOptions
        .filter((opt) => opt.dataset.type === selectedType)
        .forEach((opt) => {
            const clone = opt.cloneNode(true);
            if (String(clone.value) === String(selectedId)) {
                clone.selected = true;
            }
            editActivity.appendChild(clone);
        });
}

function openEditModal(record) {
    const modal = document.getElementById('modalEditTrainingRecord');
    const form = document.getElementById('formEditTrainingRecord');
    if (!modal || !form) return;

    form.action = "{{ url('administrasi/training-record') }}/" + record.id;

    const editType = document.getElementById('edit-program-type');
    if (editType) {
        editType.value = record.program_type || 'reguler';
        filterEditActivityOptions(editType.value, record.program_id);
    }

    const fields = [
        'participant_identifier', 'participant_name', 'gender', 'company',
        'work_location', 'group_name', 'region', 'gol', 'position',
        'instructor', 'internal_external', 'level', 'periode', 'notes',
        'tanggal', 'bulan', 'tahun'
    ];

    fields.forEach(field => {
        const input = document.getElementById('edit-' + field);
        if (input) {
            input.value = record[field] !== null && record[field] !== undefined ? record[field] : '';
        }
    });

    modal.classList.remove('hidden');
    if (window.lucide) {
        lucide.createIcons();
    }
}

function closeEditModal() {
    const modal = document.getElementById('modalEditTrainingRecord');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// 3-Step Confirmation for Delete All Training Record
let deleteAllStep = 1;
const totalDeleteSteps = 3;

const deleteStepWarnings = [
    {
        title: "Peringatan 1 dari 3: Konfirmasi Hapus",
        desc: "Apakah Anda yakin ingin menghapus SELURUH data Training Record? Semua data peserta pelatihan yang tersimpan akan dihapus dari sistem.",
        confirmBtn: "Lanjutkan (1/3)",
        btnColor: "bg-red-600 hover:bg-red-700"
    },
    {
        title: "Peringatan 2 dari 3: Data Tidak Dapat Dipulihkan",
        desc: "PERHATIAN! Tindakan ini TIDAK DAPAT DIBATALKAN. Seluruh riwayat record peserta akan hilang permanen dari database. Pastikan Anda telah mengekspor cadangan (Export CSV) jika diperlukan.",
        confirmBtn: "Saya Mengerti Risiko Ini (2/3)",
        btnColor: "bg-red-700 hover:bg-red-800"
    },
    {
        title: "Peringatan 3 dari 3: Konfirmasi Terakhir!",
        desc: "Ini adalah konfirmasi terakhir. Klik tombol di bawah ini untuk mengosongkan tabel Training Record secara permanen sekarang juga.",
        confirmBtn: "HAPUS SEMUA DATA SEKARANG (3/3)",
        btnColor: "bg-red-800 hover:bg-red-900"
    }
];

function openDeleteAllModal() {
    deleteAllStep = 1;
    renderDeleteStep();
    const modal = document.getElementById('modalDeleteAllTrainingRecord');
    if (modal) {
        modal.classList.remove('hidden');
        if (window.lucide) lucide.createIcons();
    }
}

function closeDeleteAllModal() {
    const modal = document.getElementById('modalDeleteAllTrainingRecord');
    if (modal) {
        modal.classList.add('hidden');
    }
    deleteAllStep = 1;
}

function renderDeleteStep() {
    const stepData = deleteStepWarnings[deleteAllStep - 1];
    document.getElementById('deleteAllStepBadge').innerText = `LANGKAH ${deleteAllStep} / 3`;
    document.getElementById('deleteAllTitle').innerText = stepData.title;
    document.getElementById('deleteAllDesc').innerText = stepData.desc;
    const btn = document.getElementById('deleteAllConfirmBtn');
    btn.innerText = stepData.confirmBtn;
    btn.className = `px-5 py-2.5 text-white text-xs font-bold rounded-lg transition-colors shadow-sm ${stepData.btnColor}`;

    // Update progress dots
    const dotsContainer = document.getElementById('deleteAllStepDots');
    dotsContainer.innerHTML = [1, 2, 3].map(step => `
        <div class="h-2 rounded-full transition-all ${
            step < deleteAllStep
                ? 'w-6 bg-red-400'
                : step === deleteAllStep
                ? 'w-8 bg-red-600'
                : 'w-2 bg-slate-200'
        }"></div>
    `).join('');
}

function proceedDeleteAll() {
    if (deleteAllStep < totalDeleteSteps) {
        deleteAllStep++;
        renderDeleteStep();
    } else {
        // Step 3 confirmed -> submit form
        document.getElementById('formDeleteAllTrainingRecord').submit();
    }
}
</script>
@endsection
