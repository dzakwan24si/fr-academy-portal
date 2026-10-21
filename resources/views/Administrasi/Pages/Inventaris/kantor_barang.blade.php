<x-admin-layout>

<x-slot name="title">Inventaris Kantor: Barang | FR Academy</x-slot>
    <x-slot name="header">Inventaris Kantor: Barang | FR Academy</x-slot>



{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris Gudang & Kantor</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Master data manajemen untuk asset inventaris kantor.</p>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
        {{-- Location Switcher Pills --}}
        <div class="inline-flex bg-[#F4F4F5] border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
            <a href="{{ route('administrasi.inventaris.gudang.barang') }}"
               class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ request()->routeIs('administrasi.inventaris.gudang.*') ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                Gudang
            </a>
            <a href="{{ route('administrasi.inventaris.kantor.barang') }}"
               class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ request()->routeIs('administrasi.inventaris.kantor.*') ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                Kantor
            </a>
        </div>

        {{-- Feature Sub-tabs --}}
        <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
            <a href="{{ route('administrasi.inventaris.kantor.barang') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'barang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Barang
            </a>
            <a href="{{ route('administrasi.inventaris.kantor.pembelian') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'pembelian' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Pembelian
            </a>
            <a href="{{ route('administrasi.inventaris.kantor.stock-opname') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'stock_opname' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Stock Opname
            </a>
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
    <form method="GET" action="{{ route('administrasi.inventaris.kantor.barang') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full sm:w-auto flex-1">
            {{-- Search Input --}}
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-[#A1A1AA]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama asset kantor..."
                       class="w-full pl-10 pr-4 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] transition-all">
            </div>

            {{-- Filter Jenis Lokasi --}}
            <select name="jenis_lokasi" onchange="this.form.submit()" class="px-3.5 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] font-medium focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                <option value="">Semua Lokasi</option>
                <option value="Kantor" {{ ($jenisLokasiFilter ?? '') === 'Kantor' ? 'selected' : '' }}>Kantor</option>
                <option value="Gudang" {{ ($jenisLokasiFilter ?? '') === 'Gudang' ? 'selected' : '' }}>Gudang</option>
            </select>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('administrasi.inventaris.kantor.barang.template') }}"
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-white border border-[#E5E7EB] text-[#52525B] hover:bg-[#F9FAFB] text-xs font-semibold rounded-xl transition-all shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Template Excel</span>
            </a>
            <button type="button" onclick="document.getElementById('modalImportBarang').classList.remove('hidden')"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-[#F0F5EB] border border-[#CBDDA8] text-[#4B6A34] hover:bg-[#CBDDA8] text-xs font-semibold rounded-xl transition-all shadow-sm">
                <i data-lucide="file-up" class="w-4 h-4"></i>
                <span>Import Excel/CSV</span>
            </button>
            <button type="button" onclick="document.getElementById('modalAddBarang').classList.remove('hidden')"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-[#4B6A34] text-white text-xs font-semibold rounded-xl hover:bg-[#3d562a] transition-all shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Barang</span>
            </button>
            <button type="button" onclick="document.getElementById('modalDeleteAllBarang').classList.remove('hidden')"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 text-xs font-semibold rounded-xl transition-all shadow-sm">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                <span>Hapus Semua Data</span>
            </button>
        </div>
    </form>
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
                    <th class="px-6 py-4 text-center">Jenis Lokasi</th>
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
                    <td class="px-6 py-4 text-center">
                        @if(($barang->jenis_lokasi ?: $barang->lokasi) === 'Kantor')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Kantor
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Gudang
                            </span>
                        @endif
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
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset ini?');" class="inline">
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
                <input type="text" name="nama_asset" required placeholder="Contoh: Kursi Merah, Laptop, TV..."
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Jenis Lokasi <span class="text-red-500">*</span></label>
                <select name="jenis_lokasi" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Kantor" selected>Kantor</option>
                    <option value="Gudang">Gudang</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Satuan <span class="text-red-500">*</span></label>
                <select name="satuan" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Unit">Unit</option>
                    <option value="Bh">Bh</option>
                    <option value="Buah">Buah</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Set">Set</option>
                    <option value="Box">Box</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Deskripsi pemakaian atau catatan khusus..."
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
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Jenis Lokasi <span class="text-red-500">*</span></label>
                <select id="edit_jenis_lokasi" name="jenis_lokasi" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Kantor">Kantor</option>
                    <option value="Gudang">Gudang</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Satuan <span class="text-red-500">*</span></label>
                <select id="edit_satuan" name="satuan" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Unit">Unit</option>
                    <option value="Bh">Bh</option>
                    <option value="Buah">Buah</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Set">Set</option>
                    <option value="Box">Box</option>
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

{{-- ============================== MODAL HAPUS SEMUA DATA BARANG KANTOR ============================== --}}
<div id="modalDeleteAllBarang" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl border border-[#E5E7EB] text-center">
        <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
        </div>
        <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Semua Data Barang Kantor?</h3>
        <p class="text-xs text-[#71717A] mb-4">
            Tindakan ini akan menghapus seluruh data barang inventaris lokasi <strong class="text-rose-600">Kantor</strong>.
        </p>
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-[11px] text-rose-800 text-left mb-5">
            <p class="font-bold flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i> Perhatian:</p>
            <p class="mt-0.5">Seluruh data barang Kantor beserta riwayat pembelian & stock opname terkait akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('modalDeleteAllBarang').classList.add('hidden')"
                    class="flex-1 px-4 py-2.5 text-xs font-semibold text-[#374151] bg-white border border-[#E5E7EB] rounded-xl hover:bg-[#F9FAFB]">
                Batal
            </button>
            <form method="POST" action="{{ route('administrasi.inventaris.kantor.barang.destroyAll') }}" class="flex-1">
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
    function openEditModal(barang) {
        const form = document.getElementById('formEditBarang');
        form.action = `/administrasi/inventaris/kantor/barang/${barang.id}`;
        document.getElementById('edit_nama_asset').value = barang.nama_asset;
        document.getElementById('edit_jenis_lokasi').value = barang.jenis_lokasi || barang.lokasi || 'Kantor';
        document.getElementById('edit_satuan').value = barang.satuan;
        document.getElementById('edit_keterangan').value = barang.keterangan || '';
        document.getElementById('modalEditBarang').classList.remove('hidden');
    }
</script>
@endpush

</x-admin-layout>
