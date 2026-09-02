@extends('Administrasi.Layouts.app')

@section('title', 'Biaya Rekruit | FR Academy Enterprise Portal')

@section('content')

{{-- ============================== PAGE HEADER ============================== --}}
<div class="flex items-start justify-between mb-5">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Biaya Rekruit</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Manage and monitor recruitment-related expenses.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('administrasi.biaya-rekruit.export', ['year' => $selectedYear]) }}"
           class="flex items-center gap-2 px-4 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-sm font-medium rounded-lg hover:bg-[#F9FAFB] transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i> Export
        </a>
        <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Biaya
        </button>
    </div>
</div>

{{-- ============================== YEAR NAVIGATION ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl p-3 mb-5">
    <div class="flex items-center gap-1.5 overflow-x-auto" id="year-tabs">
        @php
            $showYears = array_filter($years, fn($y) => $y >= date('Y') - 3 && $y <= date('Y') + 2);
            $showYears = array_values($showYears);
        @endphp
        @foreach($showYears as $yr)
        <a href="{{ route('administrasi.biaya-rekruit.index', ['year' => $yr]) }}"
           class="flex-shrink-0 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all
                  {{ $yr == $selectedYear ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#71717A] hover:bg-[#F4F4F5]' }}">
            {{ $yr }}
        </a>
        @endforeach
        <button onclick="document.getElementById('modalNewYear').classList.remove('hidden')"
                class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-[#4B6A34] border border-dashed border-[#4B6A34] hover:bg-[#F0F5EB] transition-colors ml-1">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Tahun
        </button>
    </div>
</div>

{{-- ============================== PERIOD BADGE ============================== --}}
<h2 class="text-lg font-bold text-[#18181B] mb-4">Biaya Rekruit — {{ $selectedYear }}</h2>

{{-- ============================== SUMMARY CARDS + SEARCH ============================== --}}
<div class="flex items-stretch gap-4 mb-5">
    {{-- Card 1: Total Biaya --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 min-w-[260px]">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#71717A] mb-2">Total Biaya Rekruit</p>
        <p class="text-2xl font-bold text-[#18181B]">Rp {{ number_format($summary['total_biaya'], 0, ',', '.') }}</p>
    </div>

    {{-- Card 2: Jumlah Transaksi --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5 min-w-[200px]">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#71717A] mb-2">Jumlah Transaksi</p>
        <p class="text-2xl font-bold text-[#18181B]">{{ $summary['total_transaksi'] }}</p>
    </div>

    {{-- Search --}}
    <div class="flex-1 flex items-center">
        <form method="GET" action="{{ route('administrasi.biaya-rekruit.index') }}" class="w-full max-w-sm ml-auto">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search nama / referensi..."
                       class="w-full pl-10 pr-4 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] bg-white">
            </div>
        </form>
    </div>
</div>

{{-- ============================== FLASH MESSAGE ============================== --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg" id="flash-msg">
    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
    {{ session('success') }}
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

{{-- ============================== GROUPED TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-[#E5E7EB]">
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
                    <td colspan="4" class="px-5 py-2.5 text-sm font-bold text-white">
                        {{ $jenis }}
                    </td>
                </tr>

                @foreach($pjGroups as $pj => $items)
                    {{-- LEVEL 2: Penanggung Jawab — AMBER row --}}
                    <tr class="bg-amber-400">
                        <td colspan="4" class="px-5 py-2 text-sm font-semibold text-white">
                            {{ $pj }}
                        </td>
                    </tr>

                    {{-- LEVEL 3: Item rows --}}
                    @foreach($items as $row)
                    <tr class="border-b border-[#F4F4F5] hover:bg-[#FAFAFA] transition-colors">
                        <td class="px-5 py-3 text-sm text-[#52525B] whitespace-nowrap">
                            {{ $row->tanggal?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-sm text-[#18181B]">
                            {{ $row->nama }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm font-medium text-[#18181B]">
                            Rp {{ number_format($row->total_biaya, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ $row->id_biaya_rekruit }})"
                                        class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $row->id_biaya_rekruit }}, '{{ addslashes($row->nama) }}')"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endforeach

            @empty
            <tr>
                <td colspan="4" class="px-5 py-16 text-center text-[#A1A1AA]">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                    <p class="text-sm font-medium">Belum ada data biaya rekruit untuk {{ $selectedYear }}</p>
                    <p class="text-xs mt-1">Klik "+ Tambah Biaya" untuk menambahkan data</p>
                </td>
            </tr>
            @endforelse
        </tbody>

        {{-- GRAND TOTAL FOOTER --}}
        @if(count($grouped) > 0)
        <tfoot>
            <tr class="bg-blue-50 border-t-2 border-blue-200">
                <td colspan="2" class="px-5 py-3.5 text-sm font-bold text-blue-800 uppercase tracking-wide text-right">
                    Total Biaya Rekruit
                </td>
                <td class="px-5 py-3.5 text-right text-base font-bold text-blue-700">
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

{{-- ---- MODAL: ADD ---- --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalAdd').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Tambah Biaya Rekruit</h2>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form method="POST" action="{{ route('administrasi.biaya-rekruit.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama / Referensi <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Ticket Pekanbaru – Jakarta"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Total Biaya (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="total_biaya" required min="0" step="1" placeholder="0"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div class="border-t border-[#E5E7EB] pt-4 space-y-4">
                <p class="text-xs text-[#A1A1AA] font-medium">Informasi Internal (tidak tampil di tabel)</p>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis Rekruit</label>
                    <input type="text" name="jenis_rekruit" placeholder="Contoh: Ticket Pesawat Recruiter"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" placeholder="Contoh: Benyamin Basuki Yulianto"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---- MODAL: EDIT ---- --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalEdit').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Edit Biaya Rekruit</h2>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form id="formEdit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" id="edit_tanggal" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama / Referensi <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="edit_nama" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Total Biaya (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="total_biaya" id="edit_total_biaya" required min="0" step="1"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div class="border-t border-[#E5E7EB] pt-4 space-y-4">
                <p class="text-xs text-[#A1A1AA] font-medium">Informasi Internal</p>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis Rekruit</label>
                    <input type="text" name="jenis_rekruit" id="edit_jenis_rekruit"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Penanggung Jawab</label>
                    <input type="text" name="penanggung_jawab" id="edit_penanggung_jawab"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---- MODAL: DELETE ---- --}}
<div id="modalDelete" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="px-6 py-6 text-center">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="trash-2" class="w-7 h-7 text-red-500"></i>
            </div>
            <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Biaya Rekruit</h3>
            <p class="text-sm text-[#71717A] mb-1">Apakah Anda yakin ingin menghapus:</p>
            <p class="text-sm font-semibold text-[#18181B] mb-5" id="delete_name_label">—</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('modalDelete').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <form id="formDelete" method="POST" action="" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ---- MODAL: TAMBAH TAHUN ---- --}}
<div id="modalNewYear" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalNewYear').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xs mx-4">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Tambah Tahun</h2>
            <button onclick="document.getElementById('modalNewYear').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form method="POST" action="{{ route('administrasi.biaya-rekruit.year') }}" class="px-5 py-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tahun Baru</label>
                <input type="number" name="new_year" min="2000" max="2099" placeholder="{{ date('Y') + 1 }}"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalNewYear').classList.add('hidden')"
                        class="flex-1 px-4 py-2 text-sm text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829]">Pilih</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditModal(id) {
    fetch(`/administrasi/biaya-rekruit/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;
            document.getElementById('formEdit').action = `/administrasi/biaya-rekruit/${id}`;
            document.getElementById('edit_tanggal').value          = d.tanggal          || '';
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
</script>
@endpush
