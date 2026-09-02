@extends('Administrasi.Layouts.app')

@section('title', 'CAPEX | FR Academy Enterprise Portal')

@section('content')
{{-- ============================== PAGE HEADER ============================== --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">CAPEX</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Capital Expenditure Management</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('administrasi.capex.export', ['year' => $selectedYear]) }}"
           class="flex items-center gap-2 px-4 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-sm font-medium rounded-lg hover:bg-[#F9FAFB] transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export
        </a>
        <button onclick="openAddModal()"
                class="flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah CAPEX
        </button>
    </div>
</div>

{{-- ============================== YEAR NAVIGATION ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl p-4 mb-5">
    <div class="flex items-center gap-2 overflow-x-auto scrollbar-thin pb-1" id="year-tabs">
        @foreach($years as $year)
        <a href="{{ route('administrasi.capex.index', ['year' => $year]) }}"
           class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium transition-all
                  {{ $year == $selectedYear
                      ? 'bg-[#4B6A34] text-white shadow-sm'
                      : 'text-[#71717A] hover:bg-[#F4F4F5] hover:text-[#18181B]' }}">
            {{ $year }}
        </a>
        @endforeach

        <button onclick="document.getElementById('modalNewYear').classList.remove('hidden')"
                class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-[#4B6A34] border border-dashed border-[#4B6A34] hover:bg-[#F0F5EB] transition-colors ml-1">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            Tambah Tahun
        </button>
    </div>
</div>

{{-- ============================== SELECTED YEAR BADGE ============================== --}}
<div class="flex items-center gap-3 mb-5">
    <div class="flex items-center gap-2 bg-[#F0F5EB] border border-[#C6D9B3] text-[#4B6A34] text-sm font-semibold px-4 py-2 rounded-full">
        <i data-lucide="calendar-check" class="w-4 h-4"></i>
        CAPEX — {{ $selectedYear }}
    </div>
</div>

{{-- ============================== SUMMARY CARDS ============================== --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    {{-- Total RAB --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-[#71717A] uppercase tracking-wider">Total RAB</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i data-lucide="wallet" class="w-4 h-4 text-blue-500"></i>
            </div>
        </div>
        <p class="text-xl font-bold text-[#18181B]">
            Rp {{ number_format($summary['totalRab'], 0, ',', '.') }}
        </p>
        <p class="text-xs text-[#A1A1AA] mt-1">RAB Kantor + RAB Mess</p>
    </div>

    {{-- Total Realisasi --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-[#71717A] uppercase tracking-wider">Total Realisasi</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <i data-lucide="receipt" class="w-4 h-4 text-amber-500"></i>
            </div>
        </div>
        <p class="text-xl font-bold text-[#18181B]">
            Rp {{ number_format($summary['totalRealisasi'], 0, ',', '.') }}
        </p>
        <p class="text-xs text-[#A1A1AA] mt-1">Realisasi Kantor + Realisasi Mess</p>
    </div>

    {{-- Sisa RAB --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-[#71717A] uppercase tracking-wider">Sisa RAB</span>
            <div class="w-8 h-8 rounded-lg {{ $summary['sisaRab'] >= 0 ? 'bg-green-50' : 'bg-red-50' }} flex items-center justify-center">
                <i data-lucide="{{ $summary['sisaRab'] >= 0 ? 'trending-down' : 'alert-triangle' }}"
                   class="w-4 h-4 {{ $summary['sisaRab'] >= 0 ? 'text-green-500' : 'text-red-500' }}"></i>
            </div>
        </div>
        <p class="text-xl font-bold {{ $summary['sisaRab'] >= 0 ? 'text-green-600' : 'text-red-500' }}">
            Rp {{ number_format(abs($summary['sisaRab']), 0, ',', '.') }}
            @if($summary['sisaRab'] < 0)
                <span class="text-xs font-medium">(Over Budget)</span>
            @endif
        </p>
        <p class="text-xs text-[#A1A1AA] mt-1">Total RAB - Total Realisasi</p>
    </div>
</div>

{{-- ============================== SUCCESS MESSAGE ============================== --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg" id="flash-msg">
    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
    {{ session('success') }}
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

{{-- ============================== 2x2 GRID SECTIONS ============================== --}}

{{-- ROW 1: RAB Kantor | Realisasi Kantor --}}
<div class="grid grid-cols-2 gap-5 mb-5">

    {{-- ---- RAB KANTOR ---- --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded bg-blue-500"></div>
                <h3 class="font-bold text-sm text-[#18181B] uppercase tracking-wide">RAB Kantor</h3>
            </div>
            <button onclick="openAddModalPreset('kantor','rab')"
                    class="flex items-center gap-1 text-xs font-medium text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
            </button>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold w-8">No</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Kelompok Barang</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Type/Merk</th>
                        <th class="px-3 py-2.5 text-center text-[#71717A] font-semibold">Satuan</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Qty</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Harga Satuan</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Total RAB</th>
                        <th class="px-3 py-2.5 text-center text-[#71717A] font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5]">
                    @forelse($rabKantor as $i => $row)
                    <tr class="hover:bg-[#FAFAFA] transition-colors">
                        <td class="px-3 py-2.5 text-[#71717A]">{{ $rabKantor->firstItem() + $i }}</td>
                        <td class="px-3 py-2.5 font-medium text-[#18181B]">{{ $row->nama_barang }}</td>
                        <td class="px-3 py-2.5 text-[#52525B]">{{ $row->type_merk ?? '-' }}</td>
                        <td class="px-3 py-2.5 text-center text-[#52525B]">{{ $row->satuan ?? '-' }}</td>
                        <td class="px-3 py-2.5 text-right text-[#52525B]">{{ number_format($row->jumlah_unit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-right text-[#52525B]">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-right font-semibold text-[#18181B]">Rp {{ number_format($row->total_rab, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $row->capex_id }})"
                                        class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $row->capex_id }}, '{{ addslashes($row->nama_barang) }}')"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-[#A1A1AA]">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                            <p class="text-xs">Belum ada data RAB Kantor</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-blue-50 border-t-2 border-blue-200">
                        <td colspan="6" class="px-3 py-2.5 text-xs font-bold text-blue-700 uppercase">Total RAB Kantor</td>
                        <td class="px-3 py-2.5 text-right text-sm font-bold text-blue-700">Rp {{ number_format($summary['rabKantorTotal'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($rabKantor->hasPages())
        <div class="px-4 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB]">
            {{ $rabKantor->appends(request()->except('rab_kantor_page'))->links('pagination::simple-tailwind') }}
        </div>
        @endif
    </div>

    {{-- ---- REALISASI KANTOR ---- --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded bg-amber-500"></div>
                <h3 class="font-bold text-sm text-[#18181B] uppercase tracking-wide">Realisasi Kantor</h3>
            </div>
            <button onclick="openAddModalPreset('kantor','realisasi')"
                    class="flex items-center gap-1 text-xs font-medium text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
            </button>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold w-8">No</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Tanggal</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Kelompok Barang</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Nama Transaksi</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Debit</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Kredit</th>
                        <th class="px-3 py-2.5 text-center text-[#71717A] font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5]">
                    @forelse($realisasiKantor as $i => $row)
                    <tr class="hover:bg-[#FAFAFA] transition-colors">
                        <td class="px-3 py-2.5 text-[#71717A]">{{ $realisasiKantor->firstItem() + $i }}</td>
                        <td class="px-3 py-2.5 text-[#52525B] whitespace-nowrap">{{ $row->tanggal?->format('d M Y') ?? '-' }}</td>
                        <td class="px-3 py-2.5 font-medium text-[#18181B]">{{ $row->nama_barang }}</td>
                        <td class="px-3 py-2.5 text-[#52525B]">{{ $row->nama_transaksi ?? '-' }}</td>
                        <td class="px-3 py-2.5 text-right font-medium text-green-600">Rp {{ number_format($row->debit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-right font-medium text-red-500">Rp {{ number_format($row->kredit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $row->capex_id }})"
                                        class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $row->capex_id }}, '{{ addslashes($row->nama_barang) }}')"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-[#A1A1AA]">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                            <p class="text-xs">Belum ada data Realisasi Kantor</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-amber-50 border-t-2 border-amber-200">
                        <td colspan="4" class="px-3 py-2.5 text-xs font-bold text-amber-700 uppercase tracking-wide">Total Realisasi Kantor</td>
                        <td colspan="2" class="px-3 py-2.5 text-right text-sm font-bold text-amber-700">Rp {{ number_format($summary['realisasiKantorTotal'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($realisasiKantor->hasPages())
        <div class="px-4 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB]">
            {{ $realisasiKantor->appends(request()->except('rel_kantor_page'))->links('pagination::simple-tailwind') }}
        </div>
        @endif
    </div>
</div>

{{-- ROW 2: RAB Mess | Realisasi Mess --}}
<div class="grid grid-cols-2 gap-5">

    {{-- ---- RAB MESS ---- --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded bg-purple-500"></div>
                <h3 class="font-bold text-sm text-[#18181B] uppercase tracking-wide">RAB Mess</h3>
            </div>
            <button onclick="openAddModalPreset('mess','rab')"
                    class="flex items-center gap-1 text-xs font-medium text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
            </button>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold w-8">No</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Kelompok Barang</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Type/Merk</th>
                        <th class="px-3 py-2.5 text-center text-[#71717A] font-semibold">Satuan</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Qty</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Harga Satuan</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Total RAB</th>
                        <th class="px-3 py-2.5 text-center text-[#71717A] font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5]">
                    @forelse($rabMess as $i => $row)
                    <tr class="hover:bg-[#FAFAFA] transition-colors">
                        <td class="px-3 py-2.5 text-[#71717A]">{{ $rabMess->firstItem() + $i }}</td>
                        <td class="px-3 py-2.5 font-medium text-[#18181B]">{{ $row->nama_barang }}</td>
                        <td class="px-3 py-2.5 text-[#52525B]">{{ $row->type_merk ?? '-' }}</td>
                        <td class="px-3 py-2.5 text-center text-[#52525B]">{{ $row->satuan ?? '-' }}</td>
                        <td class="px-3 py-2.5 text-right text-[#52525B]">{{ number_format($row->jumlah_unit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-right text-[#52525B]">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-right font-semibold text-[#18181B]">Rp {{ number_format($row->total_rab, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $row->capex_id }})"
                                        class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $row->capex_id }}, '{{ addslashes($row->nama_barang) }}')"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-[#A1A1AA]">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                            <p class="text-xs">Belum ada data RAB Mess</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-purple-50 border-t-2 border-purple-200">
                        <td colspan="6" class="px-3 py-2.5 text-xs font-bold text-purple-700 uppercase">Total RAB Mess</td>
                        <td class="px-3 py-2.5 text-right text-sm font-bold text-purple-700">Rp {{ number_format($summary['rabMessTotal'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($rabMess->hasPages())
        <div class="px-4 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB]">
            {{ $rabMess->appends(request()->except('rab_mess_page'))->links('pagination::simple-tailwind') }}
        </div>
        @endif
    </div>

    {{-- ---- REALISASI MESS ---- --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB] bg-[#F9FAFB]">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 rounded bg-rose-500"></div>
                <h3 class="font-bold text-sm text-[#18181B] uppercase tracking-wide">Realisasi Mess</h3>
            </div>
            <button onclick="openAddModalPreset('mess','realisasi')"
                    class="flex items-center gap-1 text-xs font-medium text-[#4B6A34] hover:text-[#3d5829] transition-colors">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
            </button>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold w-8">No</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Tanggal</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Kelompok Barang</th>
                        <th class="px-3 py-2.5 text-left text-[#71717A] font-semibold">Nama Transaksi</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Debit</th>
                        <th class="px-3 py-2.5 text-right text-[#71717A] font-semibold">Kredit</th>
                        <th class="px-3 py-2.5 text-center text-[#71717A] font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5]">
                    @forelse($realisasiMess as $i => $row)
                    <tr class="hover:bg-[#FAFAFA] transition-colors">
                        <td class="px-3 py-2.5 text-[#71717A]">{{ $realisasiMess->firstItem() + $i }}</td>
                        <td class="px-3 py-2.5 text-[#52525B] whitespace-nowrap">{{ $row->tanggal?->format('d M Y') ?? '-' }}</td>
                        <td class="px-3 py-2.5 font-medium text-[#18181B]">{{ $row->nama_barang }}</td>
                        <td class="px-3 py-2.5 text-[#52525B]">{{ $row->nama_transaksi ?? '-' }}</td>
                        <td class="px-3 py-2.5 text-right font-medium text-green-600">Rp {{ number_format($row->debit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-right font-medium text-red-500">Rp {{ number_format($row->kredit, 0, ',', '.') }}</td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditModal({{ $row->capex_id }})"
                                        class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $row->capex_id }}, '{{ addslashes($row->nama_barang) }}')"
                                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-[#A1A1AA]">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                            <p class="text-xs">Belum ada data Realisasi Mess</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-rose-50 border-t-2 border-rose-200">
                        <td colspan="4" class="px-3 py-2.5 text-xs font-bold text-rose-700 uppercase">Total Realisasi Mess</td>
                        <td class="px-3 py-2.5 text-right text-sm font-bold text-rose-700">Rp {{ number_format($summary['realisasiMessTotal'], 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if($realisasiMess->hasPages())
        <div class="px-4 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB]">
            {{ $realisasiMess->appends(request()->except('rel_mess_page'))->links('pagination::simple-tailwind') }}
        </div>
        @endif
    </div>
</div>

{{-- ================================================================
     MODALS
================================================================ --}}

{{-- ---- MODAL: ADD CAPEX ---- --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Tambah Data CAPEX</h2>
            <button onclick="closeAddModal()" class="p-1.5 hover:bg-[#F4F4F5] rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i>
            </button>
        </div>

        <form id="formAdd" method="POST" action="{{ route('administrasi.capex.store') }}" class="px-6 py-5 space-y-5">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">

            {{-- Step 1: Jenis CAPEX --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-2">Jenis CAPEX</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis_capex" value="kantor" id="jenis_capex_kantor" class="peer sr-only" required>
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-[#4B6A34] peer-checked:bg-[#F0F5EB] rounded-xl p-4 text-center transition-all">
                            <i data-lucide="building-2" class="w-6 h-6 mx-auto mb-1.5 text-[#71717A] peer-checked:text-[#4B6A34]"></i>
                            <p class="text-sm font-semibold text-[#374151]">Kantor</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis_capex" value="mess" id="jenis_capex_mess" class="peer sr-only">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-[#4B6A34] peer-checked:bg-[#F0F5EB] rounded-xl p-4 text-center transition-all">
                            <i data-lucide="hotel" class="w-6 h-6 mx-auto mb-1.5 text-[#71717A]"></i>
                            <p class="text-sm font-semibold text-[#374151]">Mess</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Step 2: Jenis Catatan --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-2">Jenis Catatan</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis_catatan" value="rab" id="add_jenis_rab" class="peer sr-only" required onchange="toggleAddFields()">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl p-4 text-center transition-all">
                            <i data-lucide="clipboard-list" class="w-6 h-6 mx-auto mb-1.5 text-[#71717A]"></i>
                            <p class="text-sm font-semibold text-[#374151]">RAB</p>
                            <p class="text-xs text-[#A1A1AA] mt-0.5">Rencana Anggaran</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="jenis_catatan" value="realisasi" id="add_jenis_realisasi" class="peer sr-only" onchange="toggleAddFields()">
                        <div class="border-2 border-[#E5E7EB] peer-checked:border-amber-500 peer-checked:bg-amber-50 rounded-xl p-4 text-center transition-all">
                            <i data-lucide="receipt" class="w-6 h-6 mx-auto mb-1.5 text-[#71717A]"></i>
                            <p class="text-sm font-semibold text-[#374151]">Realisasi</p>
                            <p class="text-xs text-[#A1A1AA] mt-0.5">Transaksi Aktual</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Periode (Read-Only) --}}
            <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg px-4 py-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-[#71717A]">Periode</span>
                <span class="text-sm font-bold text-[#4B6A34]">{{ $selectedYear }}</span>
            </div>

            {{-- Common Fields --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kelompok Barang Kapital <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_barang" placeholder="Contoh: Laptop / Notebook, AC Split..." required
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Type / Merk</label>
                        <input type="text" name="type_merk" placeholder="Contoh: ASUS ExpertBook..."
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Satuan</label>
                        <select name="satuan"
                                class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all bg-white">
                            <option value="">— Pilih —</option>
                            <option value="Unit">Unit</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Set">Set</option>
                            <option value="Buah">Buah</option>
                            <option value="Liter">Liter</option>
                            <option value="Meter">Meter</option>
                            <option value="Paket">Paket</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- RAB-specific fields --}}
            <div id="add_rab_fields" class="hidden space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jumlah Unit</label>
                        <input type="number" name="jumlah_unit" id="add_jumlah_unit" placeholder="0" min="0" step="0.01"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all"
                               oninput="calcTotalRab()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" id="add_harga_satuan" placeholder="0" min="0" step="1"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all"
                               oninput="calcTotalRab()">
                    </div>
                </div>
                {{-- Auto-computed Total RAB --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-700">Total RAB (otomatis)</span>
                    <span id="add_total_rab_preview" class="text-sm font-bold text-blue-700">Rp 0</span>
                </div>
            </div>

            {{-- Realisasi-specific fields --}}
            <div id="add_realisasi_fields" class="hidden space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal Transaksi</label>
                    <input type="date" name="tanggal"
                           min="{{ $selectedYear }}-01-01" max="{{ $selectedYear }}-12-31"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Transaksi</label>
                    <input type="text" name="nama_transaksi" placeholder="Deskripsi transaksi..."
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Debit (Rp)</label>
                        <input type="number" name="debit" placeholder="0" min="0" step="1"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kredit (Rp)</label>
                        <input type="number" name="kredit" placeholder="0" min="0" step="1"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ---- MODAL: EDIT CAPEX ---- --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Edit Data CAPEX</h2>
            <button onclick="closeEditModal()" class="p-1.5 hover:bg-[#F4F4F5] rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i>
            </button>
        </div>

        <form id="formEdit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')

            {{-- Info Badge --}}
            <div id="edit_type_badge" class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg px-4 py-3 flex items-center justify-between">
                <span id="edit_type_label" class="text-xs font-semibold text-[#71717A]">Jenis</span>
                <span id="edit_type_value" class="text-sm font-bold text-[#4B6A34]">—</span>
            </div>

            {{-- Common Fields --}}
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kelompok Barang Kapital <span class="text-red-500">*</span></label>
                <input type="text" name="nama_barang" id="edit_nama_barang" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Type / Merk</label>
                    <input type="text" name="type_merk" id="edit_type_merk"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Satuan</label>
                    <select name="satuan" id="edit_satuan"
                            class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none bg-white">
                        <option value="">— Pilih —</option>
                        <option value="Unit">Unit</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Set">Set</option>
                        <option value="Buah">Buah</option>
                        <option value="Liter">Liter</option>
                        <option value="Meter">Meter</option>
                        <option value="Paket">Paket</option>
                    </select>
                </div>
            </div>

            {{-- RAB edit fields --}}
            <div id="edit_rab_fields" class="hidden space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jumlah Unit</label>
                        <input type="number" name="jumlah_unit" id="edit_jumlah_unit" min="0" step="0.01"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none"
                               oninput="calcEditTotalRab()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" id="edit_harga_satuan" min="0" step="1"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none"
                               oninput="calcEditTotalRab()">
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-700">Total RAB (otomatis)</span>
                    <span id="edit_total_rab_preview" class="text-sm font-bold text-blue-700">Rp 0</span>
                </div>
            </div>

            {{-- Realisasi edit fields --}}
            <div id="edit_realisasi_fields" class="hidden space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Transaksi</label>
                    <input type="text" name="nama_transaksi" id="edit_nama_transaksi"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Debit (Rp)</label>
                        <input type="number" name="debit" id="edit_debit" min="0" step="1"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kredit (Rp)</label>
                        <input type="number" name="kredit" id="edit_kredit" min="0" step="1"
                               class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
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
            <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Data CAPEX</h3>
            <p class="text-sm text-[#71717A] mb-1">Apakah Anda yakin ingin menghapus data CAPEX ini?</p>
            <p class="text-sm font-semibold text-[#18181B] mb-5" id="delete_name_label">—</p>
            <p class="text-xs text-red-500 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB] transition-colors">
                    Batal
                </button>
                <form id="formDelete" method="POST" action="" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ---- MODAL: NEW YEAR ---- --}}
<div id="modalNewYear" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalNewYear').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Tambah Tahun Baru</h2>
            <button onclick="document.getElementById('modalNewYear').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('administrasi.capex.year') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tahun Baru</label>
                <input type="number" name="new_year" min="2000" max="2099"
                       placeholder="Contoh: 2027"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>
            <div class="flex justify-end gap-3 pt-1">
                <button type="button" onclick="document.getElementById('modalNewYear').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const selectedYear = {{ $selectedYear }};

// ============================================================
// ADD MODAL
// ============================================================
function openAddModal(presetCapex = null, presetCatatan = null) {
    document.getElementById('modalAdd').classList.remove('hidden');
    document.getElementById('formAdd').reset();
    document.getElementById('add_rab_fields').classList.add('hidden');
    document.getElementById('add_realisasi_fields').classList.add('hidden');
    document.getElementById('add_total_rab_preview').textContent = 'Rp 0';

    if (presetCapex) {
        const radio = document.getElementById('jenis_capex_' + presetCapex);
        if (radio) radio.checked = true;
    }
    if (presetCatatan) {
        const radio = document.getElementById('add_jenis_' + presetCatatan);
        if (radio) {
            radio.checked = true;
            toggleAddFields();
        }
    }
}

function openAddModalPreset(capex, catatan) {
    openAddModal(capex, catatan);
}

function closeAddModal() {
    document.getElementById('modalAdd').classList.add('hidden');
}

function toggleAddFields() {
    const isRab = document.getElementById('add_jenis_rab')?.checked;
    document.getElementById('add_rab_fields').classList.toggle('hidden', !isRab);
    document.getElementById('add_realisasi_fields').classList.toggle('hidden', isRab);
}

function calcTotalRab() {
    const qty   = parseFloat(document.getElementById('add_jumlah_unit').value) || 0;
    const price = parseFloat(document.getElementById('add_harga_satuan').value) || 0;
    const total = qty * price;
    document.getElementById('add_total_rab_preview').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ============================================================
// EDIT MODAL
// ============================================================
function openEditModal(id) {
    fetch(`/administrasi/capex/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;

            // Set form action
            document.getElementById('formEdit').action = `/administrasi/capex/${id}`;

            // Type badge
            const capexLabel = d.jenis_capex === 'kantor' ? 'Kantor' : 'Mess';
            const catatanLabel = d.jenis_catatan === 'rab' ? 'RAB' : 'Realisasi';
            document.getElementById('edit_type_label').textContent = `${capexLabel} — ${catatanLabel}`;
            document.getElementById('edit_type_value').textContent = `${selectedYear}`;

            // Common fields
            document.getElementById('edit_nama_barang').value = d.nama_barang || '';
            document.getElementById('edit_type_merk').value   = d.type_merk || '';
            document.getElementById('edit_satuan').value      = d.satuan || '';

            // Toggle sections
            const isRab = d.jenis_catatan === 'rab';
            document.getElementById('edit_rab_fields').classList.toggle('hidden', !isRab);
            document.getElementById('edit_realisasi_fields').classList.toggle('hidden', isRab);

            if (isRab) {
                document.getElementById('edit_jumlah_unit').value  = d.jumlah_unit || '';
                document.getElementById('edit_harga_satuan').value = d.harga_satuan || '';
                calcEditTotalRab();
            } else {
                document.getElementById('edit_tanggal').value         = d.tanggal || '';
                document.getElementById('edit_nama_transaksi').value  = d.nama_transaksi || '';
                document.getElementById('edit_debit').value           = d.debit || 0;
                document.getElementById('edit_kredit').value          = d.kredit || 0;
            }

            document.getElementById('modalEdit').classList.remove('hidden');
        });
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function calcEditTotalRab() {
    const qty   = parseFloat(document.getElementById('edit_jumlah_unit').value) || 0;
    const price = parseFloat(document.getElementById('edit_harga_satuan').value) || 0;
    const total = qty * price;
    document.getElementById('edit_total_rab_preview').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ============================================================
// DELETE MODAL
// ============================================================
function openDeleteModal(id, name) {
    document.getElementById('delete_name_label').textContent = name;
    document.getElementById('formDelete').action = `/administrasi/capex/${id}`;
    document.getElementById('modalDelete').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('modalDelete').classList.add('hidden');
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // Scroll active year tab into view
    const activeTab = document.querySelector('#year-tabs a.bg-\\[\\#4B6A34\\]');
    if (activeTab) {
        activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    // Wire jenis_catatan radios
    ['add_jenis_rab', 'add_jenis_realisasi'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', toggleAddFields);
    });
});
</script>
@endpush
