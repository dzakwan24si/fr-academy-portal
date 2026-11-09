<x-admin-layout>
<x-slot name="title">Program | FR Academy</x-slot>
    <x-slot name="header">Program | FR Academy</x-slot>


<div class="space-y-5">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <span class="text-sm font-semibold">{{ session('error') ?? $errors->first() }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-[#E5E7EB] rounded-xl p-5 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-[#18181B]">Program Pelatihan</h1>
            <p class="mt-1 text-sm text-[#71717A]">Ringkasan program yang dihitung dari data Program Batch, Realisasi, dan Training Records.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Import CSV/Excel Button --}}
            <button type="button" onclick="document.getElementById('modalImportProgram').classList.remove('hidden')"
                    class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs cursor-pointer">
                <i data-lucide="upload" class="w-4 h-4 text-[#4B6A34]"></i>
                <span>Import Program</span>
            </button>

            {{-- Template Excel Button --}}
            <a href="{{ route('administrasi.program-batch.template', ['type' => 'reguler']) }}"
               class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-xs font-semibold rounded-lg hover:bg-[#F9FAFB] transition-colors shadow-2xs"
               title="Download Format Template Excel">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-blue-600"></i>
                <span>Template Excel</span>
            </a>

            <a href="{{ route('administrasi.program-batch.index') }}" class="inline-flex items-center gap-1.5 bg-[#4B6A34] hover:bg-[#3d572a] text-white px-3.5 py-2 rounded-lg text-xs font-semibold shadow-xs transition-all">
                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                <span>Kelola Program Batch</span>
            </a>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <form method="GET" action="{{ route('administrasi.program.index') }}" class="flex flex-wrap items-center gap-3 rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-xs">
        {{-- Filter Jenis Program --}}
        <div class="w-full sm:w-auto">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Jenis Program</label>
            <select name="type" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white font-medium text-slate-700 outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] cursor-pointer">
                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Jenis Program</option>
                <option value="reguler" {{ $type === 'reguler' ? 'selected' : '' }}>Reguler</option>
                <option value="non_reguler_soft_skill" {{ $type === 'non_reguler_soft_skill' ? 'selected' : '' }}>Non Reguler - Soft Skill</option>
                <option value="non_reguler_teknikal" {{ $type === 'non_reguler_teknikal' ? 'selected' : '' }}>Non Reguler - Teknikal</option>
                <option value="non_reguler" {{ $type === 'non_reguler' ? 'selected' : '' }}>Semua Non Reguler</option>
            </select>
        </div>

        {{-- Filter Tahun --}}
        <div class="w-full sm:w-auto">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Tahun</label>
            <select name="year" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white font-medium text-slate-700 outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] cursor-pointer">
                <option value="all" {{ $year === null ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Region --}}
        <div class="w-full sm:w-auto">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Region</label>
            <select name="region" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white font-medium text-slate-700 outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] cursor-pointer">
                <option value="">Semua Region</option>
                @foreach($regions as $item)
                    <option value="{{ $item }}" {{ strtolower($region) === strtolower($item) ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>

        {{-- Search Input --}}
        <div class="flex-1 min-w-[240px]">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Pencarian</label>
            <div class="relative">
                <input name="search" value="{{ $search }}" placeholder="Cari nama program, lokasi..." class="w-full rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34]">
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 self-end">
            <button type="submit" class="rounded-lg bg-[#4B6A34] hover:bg-[#3d572a] px-4 py-2 text-sm font-semibold text-white shadow-xs transition-colors cursor-pointer">
                Filter
            </button>
            <a href="{{ route('administrasi.program.index') }}" class="rounded-lg bg-slate-100 hover:bg-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition-colors">
                Reset
            </a>
        </div>
    </form>

    {{-- Metric Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        @php
            $cards = [
                ['Total Programs', $groups->count(), 'program'],
                ['Total Batch', $groups->sum('total_batch'), 'batch'],
                ['Target Peserta', number_format($groups->sum('target_participants'), 0, ',', '.'), 'orang'],
                ['Realisasi Peserta', number_format($groups->sum('realized_participants'), 0, ',', '.'), 'orang'],
                ['% Peserta', ($groups->sum('target_participants') ? round($groups->sum('realized_participants') / $groups->sum('target_participants') * 100, 1) : 0) . '%', 'realisasi']
            ];
        @endphp
        @foreach($cards as $card)
            <div class="rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-[#71717A]">{{ $card[0] }}</p>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-xl font-extrabold text-[#18181B]">{{ $card[1] }}</span>
                    <span class="text-xs font-semibold text-[#71717A]">{{ $card[2] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Data Table Card --}}
    <div class="overflow-hidden rounded-xl border border-[#E5E7EB] bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm text-left">
                <thead class="bg-[#F9FAFB] text-[10px] uppercase tracking-wider text-[#71717A] border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-4 py-3.5">Nama Program</th>
                        <th class="px-4 py-3.5">Jenis Program</th>
                        <th class="px-4 py-3.5">Region</th>
                        <th class="px-4 py-3.5 text-center">Total Batch</th>
                        <th class="px-4 py-3.5 text-center">Target Peserta</th>
                        <th class="px-4 py-3.5 text-center">Realisasi Batch</th>
                        <th class="px-4 py-3.5 text-center">Realisasi Peserta</th>
                        <th class="px-4 py-3.5 text-center">% Batch</th>
                        <th class="px-4 py-3.5 text-center">% Peserta</th>
                        <th class="px-4 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5]">
                    @forelse($groups as $group)
                        <tr class="hover:bg-[#FAFAFA] transition-colors">
                            <td class="px-4 py-3.5 font-semibold text-[#18181B]">
                                {{ $group['name'] }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($group['type'] === 'reguler')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        Reguler
                                    </span>
                                @elseif(($group['sub_type'] ?? '') === 'soft_skill')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        Non Reguler - Soft Skill
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                        Non Reguler - Teknikal
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">{{ $group['region'] ?: '-' }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ $group['total_batch'] }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ number_format($group['target_participants'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ $group['realized_batch'] }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ number_format($group['realized_participants'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="font-bold {{ $group['batch_percent'] >= 100 ? 'text-emerald-700' : 'text-slate-700' }}">
                                    {{ $group['batch_percent'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="font-bold {{ $group['participant_percent'] >= 100 ? 'text-emerald-700' : 'text-slate-700' }}">
                                    {{ $group['participant_percent'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a class="inline-flex items-center gap-1 font-semibold text-[#4B6A34] hover:text-[#3d572a] hover:underline" href="{{ route('administrasi.program.show', ['type' => $group['type'], 'name' => $group['name'], 'year' => $year]) }}">
                                    <span>Detail</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-sm text-[#71717A]">
                                Tidak ada data program yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#E5E7EB] bg-[#F9FAFB] px-4 py-3 text-xs font-medium text-[#71717A] flex justify-between items-center">
            <span>Menampilkan {{ $groups->count() }} Program</span>
            <span>Total Target: {{ number_format($groups->sum('target_participants'), 0, ',', '.') }} Peserta</span>
        </div>
    </div>
</div>

{{-- ============================== MODAL: IMPORT PROGRAM ============================== --}}
<div id="modalImportProgram" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" onclick="document.getElementById('modalImportProgram').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden z-10 animate-in fade-in duration-150">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#4B6A34] flex items-center justify-center">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#18181B]">Import Program / Batch</h2>
                    <p class="text-xs text-[#71717A]">Unggah file Excel (.xlsx) atau CSV (.csv)</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modalImportProgram').classList.add('hidden')" class="p-1 hover:bg-gray-200 rounded-lg text-[#71717A] cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('administrasi.program-batch.import') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf

            {{-- Template Download Options --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100/70 text-[#4B6A34] flex items-center justify-center shrink-0">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Template Excel Import</p>
                        <p class="text-[11px] text-slate-500">Unduh format resmi untuk Program Reguler / Non-Reguler</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto">
                    <a href="{{ route('administrasi.program-batch.template', ['type' => 'reguler']) }}"
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold text-[#4B6A34] bg-white hover:bg-emerald-50 border border-emerald-200 rounded-lg transition shadow-2xs">
                        <i data-lucide="download" class="w-3 h-3"></i>
                        <span>Template Reguler</span>
                    </a>
                    <a href="{{ route('administrasi.program-batch.template', ['type' => 'non_reguler']) }}"
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition shadow-2xs">
                        <i data-lucide="download" class="w-3 h-3"></i>
                        <span>Non-Reguler</span>
                    </a>
                </div>
            </div>

            {{-- File Upload Dropzone --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-2">Pilih File Excel / CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-[#CBD5E1] hover:border-[#4B6A34] rounded-xl p-6 text-center cursor-pointer transition-colors bg-[#F9FAFB]"
                     onclick="document.getElementById('progFileInput').click()">
                    <i data-lucide="file-spreadsheet" class="w-10 h-10 mx-auto text-[#4B6A34] mb-2"></i>
                    <p class="text-xs font-semibold text-[#18181B]" id="progFileNameDisplay">Klik atau seret file Excel/CSV ke sini</p>
                    <p class="text-[11px] text-[#71717A] mt-1">Format: .xlsx, .csv, .txt (Maks 20 MB)</p>
                    <input type="file" name="file" id="progFileInput" accept=".csv,.xlsx,.txt" required class="hidden"
                           onchange="handleProgFileSelected(this)">
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
                            <span class="text-[11px] text-[#71717A]">Update jika batch sama, tambah data baru</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-2.5 p-3 border border-[#E5E7EB] rounded-xl cursor-pointer hover:bg-[#F9FAFB] transition-colors has-[:checked]:border-red-500 has-[:checked]:bg-red-50/30">
                        <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="text-xs font-bold text-red-600 block">Timpa Seluruhnya</span>
                            <span class="text-[11px] text-[#71717A]">Hapus semua data program jenis ini lalu isi dari file</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Columns Supported Info -->
            <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-3 text-[11px] text-[#334155] space-y-1">
                <span class="font-bold text-blue-900 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-blue-600"></i> Deteksi Otomatis Format
                </span>
                <p class="text-[#64748B] text-[10px] leading-relaxed">
                    Sistem otomatis mendeteksi kolom Program Reguler (Training Program, Batch, Tanggal, Peserta) maupun Non-Reguler (Jenis Pelatihan, Total Batch, Trainer).
                </p>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalImportProgram').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB] transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm flex items-center gap-2 cursor-pointer">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                    <span>Mulai Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleProgFileSelected(input) {
    if (input.files && input.files[0]) {
        const display = document.getElementById('progFileNameDisplay');
        if (display) {
            display.innerText = input.files[0].name;
        }
    }
}
</script>
</x-admin-layout>
