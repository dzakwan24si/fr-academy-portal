<x-admin-layout>

<x-slot name="title">Inventaris Mess: Barang | FR Academy</x-slot>
    <x-slot name="header">Inventaris Mess: Barang | FR Academy</x-slot>



{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris Mess: Barang</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Daftar master aset inventaris mess (Read-Only).</p>
    </div>

    {{-- Sub-tabs --}}
    <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm shrink-0">
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

{{-- ============================== SEARCH BAR ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Search Input --}}
        <div class="w-full sm:w-96">
            <form method="GET" action="{{ route('administrasi.inventaris.mess.barang') }}">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3.5 flex items-center text-[#A1A1AA]">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama asset..."
                           class="w-full pl-10 pr-4 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] placeholder-[#A1A1AA] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] transition-all">
                </div>
            </form>
        </div>

        <div class="text-xs text-[#71717A] flex items-center gap-1.5 bg-[#F9FAFB] px-3 py-1.5 rounded-lg border border-[#E5E7EB]">
            <i data-lucide="lock" class="w-3.5 h-3.5 text-[#A1A1AA]"></i>
            <span>Mode Lihat Saja (Read-Only)</span>
        </div>
    </div>
</div>

{{-- ============================== DATA TABLE (NO CRUD) ============================== --}}
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
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-[#A1A1AA]">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="package-search" class="w-8 h-8 text-[#A1A1AA]"></i>
                            <p class="font-medium text-sm">Belum ada data master aset barang untuk mess.</p>
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

</x-admin-layout>
