@extends('Administrasi.Layouts.app')

@section('title', 'Inventaris Gudang: Stock Opname | FR Academy')

@section('content')

{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col gap-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris Gudang</h1>
        </div>

        {{-- Sub-tabs --}}
        <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm overflow-x-auto max-w-full">
            <a href="{{ route('administrasi.inventaris.summary') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $activeTab === 'summary' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Summary
            </a>
            <a href="{{ route('administrasi.inventaris.gudang.barang') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $activeTab === 'barang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Barang
            </a>
            <a href="{{ route('administrasi.inventaris.gudang.pembelian') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $activeTab === 'pembelian' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Pembelian
            </a>
            <a href="{{ route('administrasi.inventaris.gudang.stock-opname') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $activeTab === 'stock_opname' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Stock Opname
            </a>
        </div>
    </div>

    {{-- Add Stock Opname Button Row --}}
    <div class="flex items-center justify-start sm:justify-end gap-3 flex-wrap">
        <button onclick="document.getElementById('modalAddSession').classList.remove('hidden')"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-all shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Stock Opname</span>
        </button>
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

@if(session('info'))
<div class="mb-5 flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-800 text-sm px-4 py-3 rounded-xl shadow-sm">
    <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0"></i>
    <span class="font-medium">{{ session('info') }}</span>
</div>
@endif

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
                    {{ $session->total_aset_diperiksa }} jenis aset diperiksa.
                    @if($session->total_masalah > 0)
                        Selisih/kerusakan tercatat pada {{ $session->total_masalah }} item.
                    @else
                        Audit berjalan lancar tanpa selisih major.
                    @endif
                @endif
            </p>
        </div>

        {{-- Action Button --}}
        <div>
            @if($isDraf)
                <a href="{{ route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $rawDate]) }}"
                   class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-[#E5E7EB] text-[#2563EB] hover:bg-[#EFF6FF] hover:border-[#BFDBFE] font-semibold text-xs rounded-xl transition-all">
                    <span>Lanjutkan Draf</span>
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                </a>
            @else
                <a href="{{ route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $rawDate]) }}"
                   class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-[#E5E7EB] text-[#4B6A34] hover:bg-[#F0F5EB] hover:border-[#CBDDA8] font-semibold text-xs rounded-xl transition-all">
                    <span>Lihat Detail</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white border border-[#E5E7EB] rounded-2xl p-12 text-center text-[#A1A1AA]">
        <div class="flex flex-col items-center justify-center gap-2">
            <i data-lucide="clipboard-check" class="w-10 h-10 text-[#A1A1AA]"></i>
            <p class="font-medium text-sm">Belum ada sesi Stock Opname yang tercatat.</p>
            <p class="text-xs text-[#71717A]">Klik tombol "+ Tambah Stock Opname" di atas untuk memulai sesi audit inventaris baru.</p>
        </div>
    </div>
    @endforelse
</div>

{{-- ============================== MODAL: TAMBAH SESI STOCK OPNAME ============================== --}}
<div id="modalAddSession" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[#E5E7EB] mx-4 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#18181B]">Mulai Stock Opname Baru</h3>
            <button onclick="document.getElementById('modalAddSession').classList.add('hidden')" class="text-[#A1A1AA] hover:text-[#18181B]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('administrasi.inventaris.gudang.stock-opname.session.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Tanggal Audit <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_stock_opname" value="{{ date('Y-m-d') }}" required
                       class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Status Sesi <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]">
                    <option value="Selesai">Selesai</option>
                    <option value="Draf">Draf</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#374151] mb-1.5 uppercase tracking-wider">Catatan / Ringkasan Audit</label>
                <textarea name="catatan_sesi" rows="3" placeholder="Contoh: Audit kuartal Q3 berjalan lancar tanpa selisih major..."
                          class="w-full px-3.5 py-2.5 border border-[#E5E7EB] rounded-xl text-sm text-[#18181B] focus:outline-none focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalAddSession').classList.add('hidden')"
                        class="px-4 py-2 border border-[#E5E7EB] text-[#52525B] text-sm font-semibold rounded-xl hover:bg-[#F4F4F5] transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-xl hover:bg-[#3d562a] transition-colors shadow-sm">
                    Buat Sesi & Isi Detail
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
