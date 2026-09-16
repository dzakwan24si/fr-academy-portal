<x-admin-layout>

<x-slot name="title">Detail Stock Opname Mess | FR Academy</x-slot>
    <x-slot name="header">Detail Stock Opname Mess | FR Academy</x-slot>



{{-- ============================== BREADCRUMB ============================== --}}
<nav class="flex items-center gap-2 text-xs text-[#71717A] mb-3">
    <span>Inventaris</span>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span>Mess</span>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <a href="{{ route('administrasi.inventaris.mess.stock-opname') }}" class="hover:text-[#4B6A34] transition-colors">Stock Opname</a>
    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
    <span class="text-[#18181B] font-semibold">Detail</span>
</nav>

{{-- ============================== HEADER ============================== --}}
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Stock Opname Mess: {{ $formattedDate }}</h1>
        <p class="text-sm text-[#71717A] mt-0.5">
            {{ $sessionMeta->catatan_sesi ?: 'Stock Opname fasilitas mess (Read-Only)' }}
        </p>
    </div>

    <div class="flex items-center gap-2">
        <div class="text-xs text-[#71717A] flex items-center gap-1.5 bg-white px-3 py-2 rounded-xl border border-[#E5E7EB] shadow-sm">
            <i data-lucide="lock" class="w-3.5 h-3.5 text-[#A1A1AA]"></i>
            <span>Mode Lihat Saja (Read-Only)</span>
        </div>
    </div>
</div>

{{-- ============================== SUB-TABS ============================== --}}
<div class="flex items-center justify-start mb-6">
    <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
        <a href="{{ route('administrasi.inventaris.mess.barang') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'barang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
            Barang
        </a>
        <a href="{{ route('administrasi.inventaris.mess.pembelian') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'pembelian' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
            Pembelian
        </a>
        <a href="{{ route('administrasi.inventaris.mess.stock-opname') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'stock_opname' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
            Stock Opname
        </a>
    </div>
</div>

{{-- ============================== SEARCH & EXPORT TOOLBAR ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Search Input --}}
        <div class="w-full sm:w-80">
            <form method="GET" action="{{ route('administrasi.inventaris.mess.stock-opname.detail', $date) }}">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3.5 flex items-center text-[#A1A1AA]">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama asset..."
                           class="w-full pl-10 pr-4 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] transition-all">
                </div>
            </form>
        </div>

        {{-- Export CSV button --}}
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <a href="{{ route('administrasi.inventaris.mess.stock-opname.export', $date) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-[#E5E7EB] text-[#52525B] hover:text-[#18181B] hover:bg-[#F9FAFB] text-xs font-semibold rounded-xl transition-colors shadow-sm" title="Download / Export Excel">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>
</div>

{{-- ============================== DETAILED TABLE (NO CRUD ACTIONS) ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th class="px-5 py-4 w-14 text-center">No</th>
                    <th class="px-5 py-4">Nama Asset</th>
                    <th class="px-4 py-4 text-center">Satuan</th>
                    <th class="px-4 py-4 text-center">Baik</th>
                    <th class="px-4 py-4 text-center">Rusak Ringan</th>
                    <th class="px-4 py-4 text-center">Rusak Berat</th>
                    <th class="px-4 py-4 text-center">Hilang</th>
                    <th class="px-5 py-4 text-center bg-[#EEF2F6] text-[#1E293B]">Grand Total</th>
                    <th class="px-5 py-4">Keterangan</th>
                    <th class="px-4 py-4 text-center w-20">Foto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($items as $index => $item)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    {{-- No --}}
                    <td class="px-5 py-4 text-center text-[#71717A] font-medium">
                        {{ $index + 1 }}
                    </td>

                    {{-- Nama Asset --}}
                    <td class="px-5 py-4 font-bold text-[#18181B]">
                        {{ $item->barang->nama_asset ?? '-' }}
                    </td>

                    {{-- Satuan --}}
                    <td class="px-4 py-4 text-center text-[#71717A]">
                        {{ $item->barang->satuan ?? 'Unit' }}
                    </td>

                    {{-- Baik --}}
                    <td class="px-4 py-4 text-center font-bold text-[#18181B]">
                        {{ $item->jumlah_baik }}
                    </td>

                    {{-- Rusak Ringan --}}
                    <td class="px-4 py-4 text-center font-bold {{ $item->jumlah_rusak_ringan > 0 ? 'text-[#C2410C]' : 'text-[#71717A]' }}">
                        {{ $item->jumlah_rusak_ringan }}
                    </td>

                    {{-- Rusak Berat --}}
                    <td class="px-4 py-4 text-center font-bold {{ $item->jumlah_rusak_berat > 0 ? 'text-[#DC2626]' : 'text-[#71717A]' }}">
                        {{ $item->jumlah_rusak_berat }}
                    </td>

                    {{-- Hilang --}}
                    <td class="px-4 py-4 text-center font-bold {{ $item->jumlah_hilang > 0 ? 'text-[#DC2626]' : 'text-[#71717A]' }}">
                        {{ $item->jumlah_hilang }}
                    </td>

                    {{-- Grand Total --}}
                    <td class="px-5 py-4 text-center font-extrabold text-[#1E293B] bg-[#F0F4F8]">
                        {{ $item->grand_total }}
                    </td>

                    {{-- Keterangan --}}
                    <td class="px-5 py-4 text-xs text-[#52525B] max-w-xs leading-relaxed">
                        {{ $item->keterangan ?: '-' }}
                    </td>

                    {{-- Foto --}}
                    <td class="px-4 py-4 text-center">
                        @if($item->foto_kerusakan)
                            <button onclick="openPhotoModal('{{ asset('storage/' . $item->foto_kerusakan) }}', '{{ $item->barang->nama_asset ?? 'Asset' }}')"
                                    class="p-2 text-[#4B6A34] hover:bg-[#F0F5EB] rounded-lg transition-colors"
                                    title="Lihat Foto Kerusakan">
                                <i data-lucide="image" class="w-4 h-4 text-[#4B6A34]"></i>
                            </button>
                        @else
                            <span class="text-[#D1D5DB]" title="Tidak ada foto">
                                <i data-lucide="image-off" class="w-4 h-4 inline"></i>
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center text-[#A1A1AA]">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="clipboard-x" class="w-8 h-8 text-[#A1A1AA]"></i>
                            <p class="font-medium text-sm">Belum ada rincian data aset mess yang dimasukkan pada sesi ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================== MODAL: PREVIEW FOTO KERUSAKAN ============================== --}}
<div id="modalPhotoPreview" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-md hidden" onclick="closePhotoModal(event)">
    <div class="bg-white rounded-2xl p-4 w-full max-w-2xl shadow-2xl border border-[#E5E7EB] mx-4 animate-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB] mb-3">
            <h3 class="text-sm font-bold text-[#18181B]" id="photoPreviewTitle">Foto Kerusakan Fasilitas Mess</h3>
            <button onclick="document.getElementById('modalPhotoPreview').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="flex items-center justify-center bg-[#F9FAFB] rounded-xl overflow-hidden max-h-[70vh]">
            <img id="photoPreviewImg" src="" alt="Foto Kerusakan" class="max-w-full max-h-[65vh] object-contain rounded-lg">
        </div>
    </div>
</div>

</x-admin-layout>

@push('scripts')
<script>
    function openPhotoModal(imgSrc, assetName) {
        document.getElementById('photoPreviewImg').src = imgSrc;
        document.getElementById('photoPreviewTitle').innerText = `Foto Kerusakan: ${assetName}`;
        document.getElementById('modalPhotoPreview').classList.remove('hidden');
    }

    function closePhotoModal(e) {
        document.getElementById('modalPhotoPreview').classList.add('hidden');
    }
</script>
@endpush
