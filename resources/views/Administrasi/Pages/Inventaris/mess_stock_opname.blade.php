<x-admin-layout>

<x-slot name="title">Inventaris Mess: Stock Opname | FR Academy</x-slot>
    <x-slot name="header">Inventaris Mess: Stock Opname | FR Academy</x-slot>



{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris Mess</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Daftar sesi Stock Opname fasilitas mess (Read-Only).</p>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
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

        <div class="text-xs text-[#71717A] flex items-center gap-1.5 bg-white px-3 py-2 rounded-xl border border-[#E5E7EB] shadow-sm">
            <i data-lucide="lock" class="w-3.5 h-3.5 text-[#A1A1AA]"></i>
            <span>Read-Only</span>
        </div>
    </div>
</div>

{{-- ============================== SESSIONS GRID ============================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($sessions as $session)
    @php
        $rawDate = \Carbon\Carbon::parse($session->tanggal_stock_opname)->format('Y-m-d');
        $formattedDate = \Carbon\Carbon::parse($session->tanggal_stock_opname)->translatedFormat('d F Y');
        $isDraf = strtolower($session->status) === 'draf';
    @endphp
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-[#CBDDA8] transition-all">
        <div>
            {{-- Top Row: Date & Status Badge --}}
            <div class="flex items-center justify-between gap-3 mb-4">
                <h3 class="text-lg font-bold text-[#18181B] tracking-tight">{{ $formattedDate }}</h3>
                @if($isDraf)
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE]">
                        DRAF
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase bg-[#EAF5E9] text-[#2F6B27] border border-[#C6E6C1]">
                        SELESAI
                    </span>
                @endif
            </div>

            {{-- Summary / Description text --}}
            <p class="text-xs leading-relaxed text-[#71717A] mb-6">
                @if($session->catatan_sesi)
                    {{ $session->catatan_sesi }}
                @else
                    {{ $session->total_aset_diperiksa }} jenis fasilitas mess diperiksa.
                    @if($session->total_masalah > 0)
                        Catatan perbaikan pada {{ $session->total_masalah }} item.
                    @else
                        Audit mess selesai dan tercatat baik.
                    @endif
                @endif
            </p>
        </div>

        {{-- Action Button --}}
        <div>
            <a href="{{ route('administrasi.inventaris.mess.stock-opname.detail', ['date' => $rawDate]) }}"
               class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-[#E5E7EB] text-[#4B6A34] hover:bg-[#F0F5EB] hover:border-[#CBDDA8] font-semibold text-xs rounded-xl transition-all">
                <span>Lihat Detail</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white border border-[#E5E7EB] rounded-2xl p-12 text-center text-[#A1A1AA]">
        <div class="flex flex-col items-center justify-center gap-2">
            <i data-lucide="clipboard-check" class="w-10 h-10 text-[#A1A1AA]"></i>
            <p class="font-medium text-sm">Belum ada catatan sesi Stock Opname mess.</p>
        </div>
    </div>
    @endforelse
</div>

</x-admin-layout>
