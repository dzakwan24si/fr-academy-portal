<x-admin-layout>

<x-slot name="title">Inventaris Kantor: Pembelian | FR Academy</x-slot>
    <x-slot name="header">Inventaris Kantor: Pembelian | FR Academy</x-slot>



{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris Kantor</h1>
    </div>

    {{-- Sub-tabs --}}
    <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm shrink-0">
        <a href="{{ route('administrasi.inventaris.kantor.barang') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'barang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
            Barang
        </a>
        <a href="{{ route('administrasi.inventaris.kantor.pembelian') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'pembelian' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
            Pembelian
        </a>
        <a href="{{ route('administrasi.inventaris.kantor.stock-opname') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'stock_opname' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
            Stock Opname
        </a>
    </div>
</div>

{{-- ============================== YEAR FILTER TABS ============================== --}}
<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
    @foreach($years as $yr)
    <a href="{{ route('administrasi.inventaris.kantor.pembelian', ['year' => $yr]) }}"
       class="px-4 py-1.5 rounded-full text-xs font-bold transition-all {{ (string)$selectedYear === (string)$yr ? 'bg-[#4B6A34] text-white shadow-sm' : 'bg-white border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]' }}">
        {{ $yr }}
    </a>
    @endforeach
    <a href="{{ route('administrasi.inventaris.kantor.pembelian', ['year' => 'all']) }}"
       class="px-4 py-1.5 rounded-full text-xs font-bold transition-all {{ (string)$selectedYear === 'all' ? 'bg-[#4B6A34] text-white shadow-sm' : 'bg-white border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]' }}">
        Semua Tahun
    </a>
</div>

{{-- ============================== KPI CARDS ============================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    {{-- Card 1: Total Jenis Aset --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 shadow-sm flex flex-col justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#F0F5EB] flex items-center justify-center text-[#4B6A34]">
                <i data-lucide="boxes" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-bold text-[#71717A]">Total Jenis Aset</span>
        </div>
        <div class="mt-4">
            <p class="text-3xl font-extrabold text-[#18181B]">{{ $totalJenisAset }}</p>
        </div>
    </div>

    {{-- Card 2: Total Barang Dibeli --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 shadow-sm flex flex-col justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            </div>
            <span class="text-xs font-bold text-[#71717A]">Total Barang Dibeli</span>
        </div>
        <div class="mt-4 flex items-baseline gap-2">
            <p class="text-3xl font-extrabold text-[#18181B]">{{ $totalBarangDibeli }}</p>
            <span class="text-xs font-semibold text-[#71717A]">Unit</span>
        </div>
    </div>
</div>

{{-- ============================== FLASH ALERTS ============================== --}}
@if(session('success'))
<div class="mb-5 flex items-center gap-2 bg-[#F0F5EB] border border-[#CBDDA8] text-[#2F4321] text-sm px-4 py-3 rounded-xl shadow-sm" id="flash-msg">
    <i data-lucide="check-circle-2" class="w-4 h-4 text-[#4B6A34] shrink-0"></i>
    <span class="font-medium">{{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto text-[#71717A] hover:text-[#18181B]">
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>
</div>
@endif

@if($errors->any())
<div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl shadow-sm">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ============================== SEARCH & ACTION BAR ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Search Input & Filter --}}
        <form method="GET" action="{{ route('administrasi.inventaris.kantor.pembelian') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto flex-1">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-[#A1A1AA]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama asset..."
                       class="w-full pl-10 pr-4 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] transition-all">
            </div>

            {{-- Filter Lokasi Spesifik / Ruangan --}}
            <select name="lokasi_spesifik" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] font-medium focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                <option value="">Semua Ruangan Kantor</option>
                @foreach($availableRooms ?? [] as $room)
                    <option value="{{ $room }}" {{ ($lokasiSpesifikFilter ?? '') === $room ? 'selected' : '' }}>{{ $room }}</option>
                @endforeach
            </select>
        </form>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('administrasi.inventaris.kantor.pembelian.template') }}"
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-white border border-[#E5E7EB] text-[#52525B] hover:bg-[#F9FAFB] text-xs font-semibold rounded-xl transition-all shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Template Excel</span>
            </a>
            <button type="button" onclick="document.getElementById('modalImportPembelian').classList.remove('hidden')"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-[#F0F5EB] border border-[#CBDDA8] text-[#4B6A34] hover:bg-[#CBDDA8] text-xs font-semibold rounded-xl transition-all shadow-sm">
                <i data-lucide="file-up" class="w-4 h-4"></i>
                <span>Import CSV</span>
            </button>
            <button onclick="document.getElementById('modalAddPembelian').classList.remove('hidden')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-all shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Pembelian</span>
            </button>
            <button type="button" onclick="document.getElementById('modalDeleteAllPembelian').classList.remove('hidden')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 text-xs font-semibold rounded-xl transition-all shadow-sm">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Hapus Semua Data</span>
            </button>
        </div>
    </div>
</div>

{{-- ============================== DATA TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Nama Aset</th>
                    <th class="px-6 py-4">Tanggal Pembelian</th>
                    <th class="px-6 py-4 text-center">Tahun</th>
                    <th class="px-6 py-4 text-center">Jumlah</th>
                    <th class="px-6 py-4 text-center">Satuan</th>
                    <th class="px-6 py-4">Lokasi Spesifik</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($pembelianList as $index => $item)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    <td class="px-6 py-4 text-center text-[#71717A] font-medium">
                        {{ $pembelianList->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4 font-bold text-[#18181B]">
                        {{ $item->barang->nama_asset ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-[#52525B]">
                        {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-[#EBF3FF] text-[#2563EB]">
                            {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('Y') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-[#18181B]">
                        {{ $item->jumlah }}
                    </td>
                    <td class="px-6 py-4 text-center text-[#71717A]">
                        {{ $item->barang->satuan ?? 'Unit' }}
                    </td>
                    <td class="px-6 py-4 font-semibold text-[#374151]">
                        @if($item->lokasi_spesifik)
                            <span class="inline-flex items-center gap-1 bg-[#F9FAFB] px-2.5 py-1 rounded-lg border border-[#E5E7EB] text-xs">
                                <i data-lucide="map-pin" class="w-3 h-3 text-amber-600"></i>
                                {{ $item->lokasi_spesifik }}
                            </span>
                        @else
                            <span class="text-[#A1A1AA] text-xs italic">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-[#71717A]">
                        {{ $item->keterangan ?: '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openEditModal({{ json_encode($item) }})"
                                     class="p-1.5 text-[#52525B] hover:text-[#4B6A34] hover:bg-[#F4F4F5] rounded-lg transition-colors"
                                     title="Edit Pembelian">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('administrasi.inventaris.kantor.pembelian.destroy', $item->id) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pembelian ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-[#71717A] hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus Pembelian">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-[#A1A1AA]">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="receipt" class="w-8 h-8 text-[#A1A1AA]"></i>
                            <p class="font-medium text-sm">Tidak ada catatan data pembelian untuk periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination & Summary --}}
    <div class="px-6 py-4 border-t border-[#E5E7EB] bg-[#F9FAFB] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#71717A]">
        <div>
            Menampilkan {{ $pembelianList->firstItem() ?? 0 }}–{{ $pembelianList->lastItem() ?? 0 }} dari {{ $pembelianList->total() }} data
            @if($selectedYear !== 'all')
                (Tahun {{ $selectedYear }})
            @endif
        </div>
        <div>
            {{ $pembelianList->links() }}
        </div>
    </div>
</div>

{{-- Datalist for Room Suggestions --}}
<datalist id="roomSuggestions">
    <option value="Planter">
    <option value="Meeting">
    <option value="Administrasi">
    <option value="Loyality & Integrity">
    <option value="Diligance">
    <option value="Persistance & Care">
    <option value="Pantry & Solat">
    <option value="Academy Director">
    <option value="Office Staf">
    <option value="E-Learning">
    <option value="Assasmenet Mill">
    <option value="Assasmenet Estate">
    <option value="Mentoring">
    <option value="Loby">
    <option value="Server">
</datalist>

{{-- ============================== MODAL: TAMBAH PEMBELIAN ============================== --}}
<div id="modalAddPembelian" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E5E7EB] mx-4 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Tambah Pembelian Aset Kantor</h3>
            <button onclick="document.getElementById('modalAddPembelian').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.inventaris.kantor.pembelian.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Pilih Aset Barang <span class="text-red-500">*</span></label>
                <select name="barang_id" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="">-- Pilih Barang Kantor --</option>
                    @foreach($allBarang as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_asset }} ({{ $b->satuan }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Lokasi Spesifik / Ruangan Kantor</label>
                <input type="text" name="lokasi_spesifik" list="roomSuggestions" placeholder="Contoh: Planter, Meeting, Administrasi..."
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                <p class="text-[11px] text-[#71717A] mt-1">Kosongkan jika tidak merinci ruangan tertentu.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Tanggal Beli <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pembelian" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" min="1" value="1" required
                           class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Contoh: Pengadaan meja staff, Penambahan AC ruangan..."
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalAddPembelian').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-sm font-semibold rounded-xl hover:bg-[#F4F4F5] transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-colors shadow-sm">
                    Simpan Pembelian
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL: EDIT PEMBELIAN ============================== --}}
<div id="modalEditPembelian" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E5E7EB] mx-4 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Edit Pembelian Aset Kantor</h3>
            <button onclick="document.getElementById('modalEditPembelian').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditPembelian" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Pilih Aset Barang <span class="text-red-500">*</span></label>
                <select id="edit_barang_id" name="barang_id" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    @foreach($allBarang as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_asset }} ({{ $b->satuan }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Lokasi Spesifik / Ruangan Kantor</label>
                <input type="text" id="edit_lokasi_spesifik" name="lokasi_spesifik" list="roomSuggestions" placeholder="Contoh: Planter, Meeting, Administrasi..."
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Tanggal Beli <span class="text-red-500">*</span></label>
                    <input type="date" id="edit_tanggal_pembelian" name="tanggal_pembelian" required
                           class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" id="edit_jumlah" name="jumlah" min="1" required
                           class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Keterangan</label>
                <textarea id="edit_keterangan" name="keterangan" rows="3"
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalEditPembelian').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-sm font-semibold rounded-xl hover:bg-[#F4F4F5] transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL IMPORT PEMBELIAN KANTOR ============================== --}}
<div id="modalImportPembelian" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl border border-[#E5E7EB]">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-base font-bold text-[#18181B]">Import Data Pembelian Kantor</h3>
            <button onclick="document.getElementById('modalImportPembelian').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('administrasi.inventaris.kantor.pembelian.import') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">File CSV / Excel</label>
                <input type="file" name="file" accept=".csv,.txt,.xlsx" required
                       class="w-full text-xs text-[#52525B] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#F0F5EB] file:text-[#4B6A34] hover:file:bg-[#CBDDA8]">
                <p class="text-[11px] text-[#71717A] mt-1">Format file .csv dengan separator koma (,) atau titik koma (;).</p>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-[#E5E7EB]">
                <a href="{{ route('administrasi.inventaris.kantor.pembelian.template') }}" class="text-xs font-semibold text-[#4B6A34] hover:underline flex items-center gap-1">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Download Template</span>
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalImportPembelian').classList.add('hidden')"
                            class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-xs font-semibold rounded-xl hover:bg-[#F4F4F5]">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#4B6A34] text-white text-xs font-semibold rounded-xl hover:bg-[#3d562a] shadow-sm">
                        Import Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL HAPUS SEMUA DATA PEMBELIAN KANTOR ============================== --}}
<div id="modalDeleteAllPembelian" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl border border-[#E5E7EB] text-center">
        <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
        </div>
        <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Semua Data Pembelian Kantor?</h3>
        <p class="text-xs text-[#71717A] mb-4">
            Tindakan ini akan menghapus seluruh data riwayat pembelian inventaris lokasi <strong class="text-rose-600">Kantor</strong>.
        </p>
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-[11px] text-rose-800 text-left mb-5">
            <p class="font-bold flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i> Perhatian:</p>
            <p class="mt-0.5">Seluruh riwayat pembelian Kantor akan dihapus permanen. Data master barang tidak akan terhapus. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('modalDeleteAllPembelian').classList.add('hidden')"
                    class="flex-1 px-4 py-2.5 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-xl hover:bg-[#F9FAFB]">
                Batal
            </button>
            <form method="POST" action="{{ route('administrasi.inventaris.kantor.pembelian.destroyAll') }}" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 shadow-sm">
                    Ya, Hapus Semua
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(item) {
        const form = document.getElementById('formEditPembelian');
        form.action = `/administrasi/inventaris/kantor/pembelian/${item.id}`;
        document.getElementById('edit_barang_id').value = item.barang_id;
        document.getElementById('edit_lokasi_spesifik').value = item.lokasi_spesifik || '';
        document.getElementById('edit_tanggal_pembelian').value = item.tanggal_pembelian ? item.tanggal_pembelian.split('T')[0] : '';
        document.getElementById('edit_jumlah').value = item.jumlah;
        document.getElementById('edit_keterangan').value = item.keterangan || '';
        document.getElementById('modalEditPembelian').classList.remove('hidden');
    }
</script>
@endpush

</x-admin-layout>
