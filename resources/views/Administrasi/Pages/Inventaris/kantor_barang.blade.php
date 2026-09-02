@extends('Administrasi.Layouts.app')

@section('title', 'Inventaris Kantor: Barang | FR Academy')

@section('content')

{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris Kantor: Barang</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Master data manajemen untuk asset inventaris kantor.</p>
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
        {{-- Search Input --}}
        <div class="w-full sm:w-96">
            <form method="GET" action="{{ route('administrasi.inventaris.kantor.barang') }}">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3.5 flex items-center text-[#A1A1AA]">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama asset..."
                           class="w-full pl-10 pr-4 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] transition-all">
                </div>
            </form>
        </div>

        {{-- Add Button --}}
        <button onclick="document.getElementById('modalAddBarang').classList.remove('hidden')"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Barang</span>
        </button>
    </div>
</div>

{{-- ============================== DATA TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Nama Asset</th>
                    <th class="px-6 py-4 text-center">Satuan</th>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($barangList as $index => $barang)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    <td class="px-6 py-4 text-center text-[#71717A] font-medium">
                        {{ $barangList->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4 font-bold text-[#18181B]">
                        {{ $barang->nama_asset }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#EBF3FF] text-[#2563EB]">
                            {{ $barang->satuan }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-[#52525B]">
                        {{ $barang->lokasi }}
                    </td>
                    <td class="px-6 py-4 text-[#71717A]">
                        {{ $barang->keterangan ?: '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openEditModal({{ json_encode($barang) }})"
                                    class="p-1.5 text-[#52525B] hover:text-[#4B6A34] hover:bg-[#F4F4F5] rounded-lg transition-colors"
                                    title="Edit Barang">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('administrasi.inventaris.kantor.barang.destroy', $barang->id) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset kantor ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-[#71717A] hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus Barang">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-[#A1A1AA]">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="package-search" class="w-8 h-8 text-[#A1A1AA]"></i>
                            <p class="font-medium text-sm">Belum ada data master aset barang untuk kantor.</p>
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
            Menampilkan {{ $barangList->firstItem() ?? 0 }}–{{ $barangList->lastItem() ?? 0 }} dari {{ $barangList->total() }} data
        </div>
        <div>
            {{ $barangList->links() }}
        </div>
    </div>
</div>

{{-- ============================== MODAL: TAMBAH BARANG ============================== --}}
<div id="modalAddBarang" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E5E7EB] mx-4 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Tambah Master Aset Kantor</h3>
            <button onclick="document.getElementById('modalAddBarang').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.inventaris.kantor.barang.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Nama Asset <span class="text-red-500">*</span></label>
                <input type="text" name="nama_asset" required placeholder="Contoh: PC All-in-One, Meja Kerja, AC Split..."
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Satuan <span class="text-red-500">*</span></label>
                <select name="satuan" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Unit">Unit</option>
                    <option value="Buah">Buah</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Set">Set</option>
                    <option value="Box">Box</option>
                    <option value="Meter">Meter</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Deskripsi pemakaian ruangan kantor atau spesifikasi singkat..."
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalAddBarang').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-sm font-semibold rounded-xl hover:bg-[#F4F4F5] transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-colors shadow-sm">
                    Simpan Aset
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================== MODAL: EDIT BARANG ============================== --}}
<div id="modalEditBarang" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E5E7EB] mx-4 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Edit Master Aset Kantor</h3>
            <button onclick="document.getElementById('modalEditBarang').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="formEditBarang" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Nama Asset <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama_asset" name="nama_asset" required
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Satuan <span class="text-red-500">*</span></label>
                <select id="edit_satuan" name="satuan" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Unit">Unit</option>
                    <option value="Buah">Buah</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Set">Set</option>
                    <option value="Box">Box</option>
                    <option value="Meter">Meter</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Keterangan</label>
                <textarea id="edit_keterangan" name="keterangan" rows="3"
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalEditBarang').classList.add('hidden')"
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

@endsection

@push('scripts')
<script>
    function openEditModal(barang) {
        const form = document.getElementById('formEditBarang');
        form.action = `/administrasi/inventaris/kantor/barang/${barang.id}`;
        document.getElementById('edit_nama_asset').value = barang.nama_asset;
        document.getElementById('edit_satuan').value = barang.satuan;
        document.getElementById('edit_keterangan').value = barang.keterangan || '';
        document.getElementById('modalEditBarang').classList.remove('hidden');
    }
</script>
@endpush
