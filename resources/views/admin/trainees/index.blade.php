@php
    $programsJson = \App\Models\TrainingProgram::all()->toJson();
@endphp
<x-admin-layout>
    <x-slot name="title">Trainee</x-slot>
    <x-slot name="header">Daftar Trainee</x-slot>
    <x-slot name="headerSub">Kelola data peserta training (Trainee).</x-slot>

    <div x-data="crudModalTrainee('/training-reguler/trainees', {{ $programsJson }})">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 mt-4">
            <!-- Total Trainee Card -->
            <div class="bg-emerald-600 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-xl p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-emerald-600/30 cursor-pointer">
                <div class="relative z-10 text-white">
                    <p class="text-[11px] font-bold mb-1 uppercase tracking-widest opacity-80">Total Trainee</p>
                    <p class="text-3xl font-black">{{ $total }}</p>
                </div>
                <div class="absolute -bottom-4 -right-4 opacity-10">
                    <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full opacity-5 -translate-y-1/2 translate-x-1/3"></div>
            </div>

            <!-- Aktif Card -->
            <div class="bg-white shadow-sm rounded-xl border border-slate-100 border-l-4 border-l-emerald-500 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold mb-1 uppercase tracking-widest">Aktif</p>
                    <p class="text-3xl font-black text-slate-800">{{ $aktif }}</p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- On Training Card -->
            <div class="bg-white shadow-sm rounded-xl border border-slate-100 border-l-4 border-l-amber-500 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold mb-1 uppercase tracking-widest">On Training</p>
                    <p class="text-3xl font-black text-slate-800">{{ $pasif }}</p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Resign Card -->
            <div class="bg-white shadow-sm rounded-xl border border-slate-100 border-l-4 border-l-rose-600 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold mb-1 uppercase tracking-widest">Resign</p>
                    <p class="text-3xl font-black text-slate-800">{{ $resign }}</p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 mb-5 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="font-semibold text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 mb-5 rounded-xl">
                <p class="font-semibold text-sm">{{ session('error') }}</p>
                @if(session('import_errors'))
                    <ul class="list-disc list-inside mt-2 text-xs space-y-1">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="font-bold text-slate-800 text-lg">Data Trainee</h3>
                <div class="flex items-center gap-2">
                    <!-- Import Button -->
                    <button @click="importOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200 ease-in-out hover:scale-105 active:scale-95 hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import
                    </button>
                    <!-- Add Button -->
                    <button @click="openModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out hover:scale-105 active:scale-95 hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Manual
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                        <tr>
                            <th rowspan="2" class="px-5 py-4 w-12 text-center border-b border-slate-200 align-middle">#</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 align-middle">NIK</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 align-middle">Nama Trainee</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 align-middle">Program</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 align-middle">Asal Daerah</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 align-middle">Universitas / Jurusan</th>
                            <th colspan="2" class="px-5 py-2 text-center border-b border-slate-200 bg-slate-100">OJT</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 align-middle">Status</th>
                            <th rowspan="2" class="px-5 py-4 text-right border-b border-slate-200 align-middle">Aksi</th>
                        </tr>
                        <tr>
                            <th class="px-5 py-2 text-center border-b border-slate-200 bg-slate-50">Region</th>
                            <th class="px-5 py-2 text-center border-b border-slate-200 bg-slate-50">Kode Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($trainees as $item)
                        <tr class="transition-colors duration-200 ease-in hover:bg-slate-50">
                            <td class="px-5 py-3.5 text-xs text-slate-400 font-medium text-center">{{ $trainees->firstItem() + $loop->index }}</td>
                            <td class="px-5 py-3.5 text-sm font-mono text-slate-600 font-semibold">{{ $item->nik ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-bold text-sm text-slate-800">{{ $item->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm font-medium text-slate-600">{{ $item->trainingProgram->name ?? '-' }} {{ isset($item->trainingProgram->year) ? '(' . $item->trainingProgram->year . ')' : '' }}</td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">
                                {{ $item->origin ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500">
                                <span>{{ $item->university ?? '-' }}</span>
                                @if($item->major)
                                    <br><span class="text-xs text-slate-400">{{ $item->major }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 text-center border-l border-slate-100">
                                @if($item->ojt_region)
                                    <span class="font-semibold text-slate-700">{{ $item->ojt_region }}</span>
                                @else
                                    <span class="text-slate-300 italic">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-500 text-center border-r border-slate-100">
                                @if($item->ojt_location_code)
                                    <span class="font-semibold text-slate-700">{{ $item->ojt_location_code }}</span>
                                @else
                                    <span class="text-slate-300 italic">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wide rounded-full
                                    {{ $item->status == 'aktif' ? 'bg-emerald-100 text-emerald-700' : (in_array($item->status, ['on_training', 'pasif']) ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ ucwords(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openModal({{ $item->toJson() }})"
                                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-semibold text-blue-600 rounded-md hover:bg-blue-50 transition-all duration-200 hover:-translate-y-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    <button type="button" @click="confirmDelete('{{ route('training-reguler.trainees.destroy', $item->id) }}', '{{ addslashes($item->name) }}')" class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-semibold text-red-600 rounded-md hover:bg-red-50 transition-all duration-200 hover:-translate-y-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-400">Belum ada data trainee</p>
                                    <p class="text-xs text-gray-300">Tambahkan trainee secara manual atau import dari Excel/CSV</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
            </table>
        </div>
        @if($trainees->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $trainees->links() }}
        </div>
        @endif
    </div>

      {{-- ===== IMPORT MODAL ===== --}}
<x-admin-modal show="importOpen" maxWidth="4xl" title="Import Trainee dari Excel/CSV" subtitle="Upload file lalu sistem akan otomatis membaca datanya">


        <form action="{{ route('training-reguler.trainees.import') }}"
              method="POST"
              enctype="multipart/form-data"
              class="p-7 space-y-5">

            @csrf

            <!-- Program Selection -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Program Training <span class="text-red-500">*</span>
                </label>

                <select name="training_program_id"
                        required
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none bg-white">

                    <option value="">— Pilih Program Training —</option>

                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}">
                            {{ $prog->name }} ({{ $prog->year }})
                        </option>
                    @endforeach

                </select>

                <p class="text-xs text-gray-400 mt-1">
                    Semua trainee di file akan dimasukkan ke program ini.
                </p>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    File Excel / CSV <span class="text-red-500">*</span>
                </label>

                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-green-200 rounded-xl cursor-pointer bg-green-50 hover:bg-green-100 hover:border-green-300 transition">

                    <div class="flex flex-col items-center gap-1.5">

                        <svg class="w-8 h-8 text-green-500"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>

                        <span class="text-sm text-gray-500 font-medium" x-text="importFile ? importFile.name : 'Klik untuk pilih file'">
                        </span>

                        <span class="text-xs text-gray-400">
                            Format: .xlsx, .xls, .csv — Maks 5MB
                        </span>

                    </div>

                    <input type="file"
                           name="file"
                           accept=".xlsx,.xls,.csv"
                           required
                           @change="handleImportFile($event)"
                           class="hidden">

                </label>
            </div>

            <!-- Preview Data -->
            <template x-if="importPreviewHeaders.length > 0">
                <div class="bg-white border border-gray-200 rounded-xl p-4 overflow-hidden shadow-sm">
                    <p class="text-xs font-bold text-gray-700 mb-2">Preview Data (3 baris pertama):</p>
                    <div class="overflow-x-auto rounded border border-gray-100">
                        <table class="w-full text-left text-xs whitespace-nowrap">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <template x-for="header in importPreviewHeaders">
                                        <th class="px-3 py-2 font-semibold text-gray-600" x-text="header || '-'"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="row in importPreviewData">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <template x-for="(cell, cellIndex) in importPreviewHeaders">
                                            <td class="px-3 py-2 text-gray-500 truncate max-w-[150px]" x-text="row[cellIndex] !== undefined && row[cellIndex] !== '' ? row[cellIndex] : '-'"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 italic">* Pastikan judul kolom di atas mengandung kata: nama, tempat_lahir, tgl_lahir, dll.</p>
                </div>
            </template>

            <!-- Kolom yang dipakai -->
            <div class="bg-green-50 border border-green-100 rounded-xl p-4">

                <p class="text-xs font-bold text-green-700 mb-2">
                    Kolom yang dibaca dari file:
                </p>

                <div class="flex flex-wrap gap-1.5">

                    @foreach(['nik', 'nama', 'tempat_lahir', 'tgl_lahir', 'asal_daerah', 'universitas', 'jurusan', 'status'] as $col)
                        <span class="px-2 py-0.5 bg-white border border-green-200 rounded-md text-xs font-mono text-gray-600">
                            {{ $col }}
                        </span>
                    @endforeach

                </div>

                <p class="text-xs text-gray-400 mt-2">
                    Kolom <span class="font-mono font-bold">nama</span> wajib diisi.
                    Kolom <span class="font-mono font-bold">status</span>:
                    aktif/pasif/resign (default: aktif).
                </p>

            </div>

            <div class="flex justify-end gap-3 pt-2">

                <button type="button"
                        @click="importOpen = false"
                        class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                    Batal
                </button>

                <button type="submit"
                        class="px-5 py-2.5 text-sm font-bold text-white bg-[#28844b] rounded-xl hover:bg-green-700 transition inline-flex items-center gap-2">

                    <svg class="w-4 h-4"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>

                    Import Sekarang

                </button>

            </div>

        </form>
</x-admin-modal>
        {{-- ===== ADD/EDIT MANUAL MODAL ===== --}}
        <x-admin-modal show="isOpen" maxWidth="3xl">
            <x-slot name="titleHtml"><span x-text="isEdit ? 'Edit Data Trainee' : 'Tambah Trainee Baru'"></span></x-slot>
            <x-slot name="subtitleHtml">Lengkapi formulir di bawah ini</x-slot>

                <form :action="formAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-6">

                        <!-- Section 1: Data Utama -->
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-[#28844b] flex items-center justify-center text-white text-xs font-black">1</div>
                                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Data Utama</h4>
                                <div class="flex-1 h-px bg-gray-100"></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">NIK (Nomor Induk) <span class="text-red-500">*</span></label>
                                    <input type="text" name="nik" x-model="formData.nik" required
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#28844b]/30 focus:border-[#28844b] outline-none transition font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="formData.name" required
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#28844b]/30 focus:border-[#28844b] outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Status Trainee <span class="text-red-500">*</span></label>
                                    <select name="status" x-model="formData.status" required
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#28844b]/30 focus:border-[#28844b] outline-none bg-white transition">
                                        <option value="aktif">Aktif</option>
                                        <option value="pasif">Pasif</option>
                                        <option value="resign">Resign</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Program Training <span class="text-red-500">*</span></label>
                                    <select name="training_program_id" x-model="formData.training_program_id" required
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#28844b]/30 focus:border-[#28844b] outline-none bg-white transition">
                                        <option value="">— Pilih Program —</option>
                                        <template x-for="p in programs" :key="p.id">
                                            <option :value="p.id" x-text="p.name + ' (' + p.year + ')'" :selected="formData.training_program_id == p.id"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Data Pendidikan -->
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-white text-xs font-black">2</div>
                                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Data Asal & Pendidikan</h4>
                                <div class="flex-1 h-px bg-gray-100"></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Asal Daerah</label>
                                    <input type="text" name="origin" x-model="formData.origin"
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Alumni (Universitas)</label>
                                    <input type="text" name="university" x-model="formData.university"
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jurusan (Major)</label>
                                    <input type="text" name="major" x-model="formData.major"
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition">
                                </div>
                            </div>
                        </div>

                                                <!-- Section 2.5: Data OJT -->
                        <div class="mt-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-orange-500 flex items-center justify-center text-white text-xs font-black">OJT</div>
                                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Data OJT (Opsional)</h4>
                                <div class="flex-1 h-px bg-gray-100"></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Region OJT</label>
                                    <input type="text" name="ojt_region" x-model="formData.ojt_region" placeholder="Misal: Kalimantan Barat"
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nama PT (Perusahaan)</label>
                                    <input type="text" name="ojt_company_name" x-model="formData.ojt_company_name" placeholder="Misal: PT. Astra Agro"
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500 outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Kode Lokasi OJT</label>
                                    <input type="text" name="ojt_location_code" x-model="formData.ojt_location_code" placeholder="Misal: LOK-01"
                                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500/30 focus:border-orange-500 outline-none transition">
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Resignation Detail (conditional) -->
                        <div x-show="formData.status === 'resign'" style="display:none;">
                            <div class="bg-red-50 border border-red-100 rounded-xl p-5 mt-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-7 h-7 rounded-lg bg-red-500 flex items-center justify-center text-white text-xs font-black">!</div>
                                    <h4 class="font-bold text-red-700 text-sm uppercase tracking-wider">Data Pengunduran Diri</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Tempat Lahir</label>
                                        <input type="text" name="birth_place" x-model="formData.birth_place"
                                            class="w-full px-4 py-2.5 text-sm border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500/30 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Tanggal Lahir</label>
                                        <input type="date" name="birth_date" x-model="formData.birth_date"
                                            class="w-full px-4 py-2.5 text-sm border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500/30 outline-none bg-white transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Foto Profil</label>
                                        <input type="file" name="photo" accept="image/*" class="w-full text-xs text-red-700">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Nilai Rata-rata Inclass</label>
                                        <input type="number" step="0.01" name="avg_inclass_score" x-model="formData.avg_inclass_score"
                                            class="w-full px-4 py-2.5 text-sm border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500/30 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Ranking Inclass</label>
                                        <input type="number" name="inclass_rank" x-model="formData.inclass_rank"
                                            class="w-full px-4 py-2.5 text-sm border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500/30 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Nilai Rata-rata Praktek</label>
                                        <input type="number" step="0.01" name="avg_practice_score" x-model="formData.avg_practice_score"
                                            class="w-full px-4 py-2.5 text-sm border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500/30 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Ranking Praktek</label>
                                        <input type="number" name="practice_rank" x-model="formData.practice_rank"
                                            class="w-full px-4 py-2.5 text-sm border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500/30 outline-none transition">
                                    </div>
                                </div>
                                <label class="block text-xs font-semibold text-red-700 mb-1.5">Alasan Resign</label>
                                <textarea name="resign_reason" x-model="formData.resign_reason" rows="2"
                                    placeholder="Jelaskan alasan trainee mengundurkan diri..."
                                    class="w-full px-4 py-2.5 text-sm border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500/30 outline-none resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="px-7 py-5 border-t border-gray-100 bg-gray-50/80 flex justify-end gap-3">
                        <button type="button" @click="isOpen = false"
                            class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 text-sm font-bold text-white bg-[#28844b] rounded-xl hover:bg-green-700 transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Data
                        </button>
                    </div>
                </form>
        </x-admin-modal>

    
        <!-- Modal Hapus -->
        <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                <div x-show="showDeleteModal" x-transition.opacity.duration.300ms class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false" aria-hidden="true"></div>
                <div x-show="showDeleteModal" x-transition.scale.origin.bottom.duration.300ms class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-left align-middle bg-white shadow-2xl rounded-2xl transform transition-all">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex items-center justify-center w-16 h-16 mb-4 bg-red-100 rounded-full">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Trainee?</h3>
                        <p class="text-sm text-gray-500 mb-6">Tindakan ini tidak dapat dibatalkan. Data Trainee <span class="font-bold text-gray-800" x-text="deleteTitle"></span> akan dihapus permanen.</p>
                        <div class="flex gap-3 justify-center w-full">
                            <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</button>
                            <button type="button" @click="executeDelete()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('crudModalTrainee', (baseUrl, programs) => ({
                showDeleteModal: false,
                deleteUrl: '',
                deleteTitle: '',
                confirmDelete(url, title) {
                    this.deleteUrl = url;
                    this.deleteTitle = title;
                    this.showDeleteModal = true;
                },
                executeDelete() {
                    if (this.deleteUrl) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = this.deleteUrl;
                        form.innerHTML = '@csrf @method("DELETE")';
                        document.body.appendChild(form);
                        form.submit();
                    }
                },
                isOpen: false,
                importOpen: false,
                importFile: null,
                importPreviewData: [],
                importPreviewHeaders: [],
                handleImportFile(event) {
                    const file = event.target.files[0];
                    this.importFile = file;
                    this.importPreviewData = [];
                    this.importPreviewHeaders = [];
                    if (!file) return;

                    if (typeof XLSX === 'undefined') {
                        console.warn('SheetJS library not loaded.');
                        return;
                    }

                    if(file.name.endsWith('.csv') || file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const data = new Uint8Array(e.target.result);
                            const workbook = XLSX.read(data, {type: 'array'});
                            const firstSheetName = workbook.SheetNames[0];
                            const worksheet = workbook.Sheets[firstSheetName];
                            const json = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
                            
                            let headerIdx = 0;
                            while(headerIdx < json.length && json[headerIdx].length === 0) {
                                headerIdx++;
                            }
                            if(headerIdx < json.length) {
                                this.importPreviewHeaders = json[headerIdx];
                                this.importPreviewData = json.slice(headerIdx + 1, headerIdx + 4);
                            }
                        };
                        reader.readAsArrayBuffer(file);
                    }
                },
                isEdit: false,
                baseUrl: baseUrl,
                programs: programs,
                formAction: '',
                formData: {
                    nik: '', name: '', training_program_id: '', origin: '',
                    university: '', major: '', status: 'aktif',
                    birth_place: '', birth_date: '', resign_reason: '',
                    avg_inclass_score: '', inclass_rank: '',
                    avg_practice_score: '', practice_rank: ''
                },
                openModal(data = null) {
                    this.isOpen = true;
                    if (data) {
                        this.isEdit = true;
                        this.formAction = this.baseUrl + '/' + data.id;
                        this.formData = {
                            nik: data.nik || '',
                            name: data.name || '',
                            training_program_id: data.training_program_id || '',
                            origin: data.origin || '',
                            university: data.university || '',
                            major: data.major || '',
                            status: data.status || 'aktif',
                            birth_place: data.resignation?.birth_place || '',
                            birth_date: data.resignation?.birth_date || '',
                            resign_reason: data.resignation?.reason || '',
                            avg_inclass_score: data.resignation?.avg_inclass_score || '',
                            inclass_rank: data.resignation?.inclass_rank || '',
                            avg_practice_score: data.resignation?.avg_practice_score || '',
                            practice_rank: data.resignation?.practice_rank || ''
                        };
                    } else {
                        this.isEdit = false;
                        this.formAction = this.baseUrl;
                        this.formData = {
                            nik: '', name: '', training_program_id: '', origin: '',
                            university: '', major: '', status: 'aktif',
                            birth_place: '', birth_date: '', resign_reason: '',
                            avg_inclass_score: '', inclass_rank: '',
                            avg_practice_score: '', practice_rank: ''
                        };
                    }
                }
            }));
        });
    </script>
</x-admin-layout>