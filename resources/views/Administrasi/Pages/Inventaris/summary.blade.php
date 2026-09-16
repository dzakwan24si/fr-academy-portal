@extends('Administrasi.Layouts.app')

@section('title', 'Inventaris — Summary Overview | FR Academy')

@section('content')

{{-- ============================== TOP SUB-TABS ============================== --}}
<div class="flex items-center justify-start mb-6">
    <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
        <a href="{{ route('administrasi.inventaris.summary') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all bg-[#4B6A34] text-white shadow-sm">
            Summary
        </a>
        <a href="{{ route('administrasi.inventaris.barang') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]">
            Barang
        </a>
        <a href="{{ route('administrasi.inventaris.pembelian') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]">
            Pembelian
        </a>
        <a href="{{ route('administrasi.inventaris.stock-opname') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]">
            Stock Opname
        </a>
    </div>
</div>

{{-- ============================== PAGE TITLE & YEAR SELECTOR ============================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris — Summary Overview</h1>
        <p class="text-xs text-[#71717A] mt-0.5">Rekapitulasi kondisi aset berdasarkan jenis lokasi & rincian ruangan kantor</p>
    </div>

    {{-- Year Filter Pills --}}
    <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm shrink-0">
        @foreach($years as $yr)
        <a href="{{ route('administrasi.inventaris.summary', ['year' => $yr]) }}"
           class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all {{ $selectedYear === $yr ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
            {{ $yr }}
        </a>
        @endforeach
    </div>
</div>

{{-- ============================== SECTION 1: GUDANG ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
        <div class="flex items-center gap-2.5">
            <i data-lucide="warehouse" class="w-5 h-5 text-[#4B6A34]"></i>
            <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">REKAP ASET GUDANG</h2>
        </div>
        <span class="text-xs font-bold text-[#71717A] bg-white px-3 py-1 border border-[#E5E7EB] rounded-full">
            {{ count($summaryData['Gudang'] ?? []) }} Jenis Aset
        </span>
    </div>

    {{-- Summary Table Gudang --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th rowspan="2" class="px-6 py-3 w-16 text-center border-r border-[#E5E7EB]">No</th>
                    <th rowspan="2" class="px-6 py-3 border-r border-[#E5E7EB]">Nama Asset</th>
                    <th rowspan="2" class="px-4 py-3 text-center border-r border-[#E5E7EB] w-24">Satuan</th>
                    <th rowspan="2" class="px-6 py-3 text-center border-r border-[#E5E7EB] w-28">Total Stok</th>
                    <th colspan="2" class="px-6 py-2 text-center border-b border-r border-[#E5E7EB]">Kondisi Saat Ini</th>
                    <th rowspan="2" class="px-6 py-3">Keterangan</th>
                </tr>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-[11px] font-bold uppercase tracking-wider">
                    <th class="px-4 py-2 text-center text-emerald-700 border-r border-[#E5E7EB] w-28">Baik</th>
                    <th class="px-4 py-2 text-center text-rose-600 border-r border-[#E5E7EB] w-28">Rusak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($summaryData['Gudang'] ?? [] as $index => $item)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    <td class="px-6 py-3.5 text-center text-[#71717A] font-medium border-r border-[#E5E7EB]">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-3.5 font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ $item['nama_asset'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center text-xs text-[#71717A] border-r border-[#E5E7EB]">
                        {{ $item['satuan'] }}
                    </td>
                    <td class="px-6 py-3.5 text-center font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ number_format($item['stok']) }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-emerald-700 border-r border-[#E5E7EB]">
                        {{ number_format($item['baik']) }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-rose-600 border-r border-[#E5E7EB]">
                        {{ number_format($item['rusak']) }}
                    </td>
                    <td class="px-6 py-3.5 text-xs text-[#52525B]">
                        {{ $item['keterangan'] }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-6 text-center text-[#A1A1AA] text-xs">
                        Tidak ada data inventaris gudang untuk tahun {{ $selectedYear }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================== SECTION 2: KANTOR (AGREGAT & BREAKDOWN PER RUANGAN) ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
        <div class="flex items-center gap-2.5">
            <i data-lucide="building-2" class="w-5 h-5 text-[#4B6A34]"></i>
            <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">REKAP ASET KANTOR (AGREGAT)</h2>
        </div>
        <span class="text-xs font-bold text-[#71717A] bg-white px-3 py-1 border border-[#E5E7EB] rounded-full">
            {{ count($summaryData['Kantor'] ?? []) }} Jenis Aset
        </span>
    </div>

    {{-- Summary Table Kantor --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th rowspan="2" class="px-6 py-3 w-16 text-center border-r border-[#E5E7EB]">No</th>
                    <th rowspan="2" class="px-6 py-3 border-r border-[#E5E7EB]">Nama Asset</th>
                    <th rowspan="2" class="px-4 py-3 text-center border-r border-[#E5E7EB] w-24">Satuan</th>
                    <th rowspan="2" class="px-6 py-3 text-center border-r border-[#E5E7EB] w-28">Total Stok</th>
                    <th colspan="2" class="px-6 py-2 text-center border-b border-r border-[#E5E7EB]">Kondisi Saat Ini</th>
                    <th rowspan="2" class="px-6 py-3">Keterangan</th>
                </tr>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-[11px] font-bold uppercase tracking-wider">
                    <th class="px-4 py-2 text-center text-emerald-700 border-r border-[#E5E7EB] w-28">Baik</th>
                    <th class="px-4 py-2 text-center text-rose-600 border-r border-[#E5E7EB] w-28">Rusak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($summaryData['Kantor'] ?? [] as $index => $item)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    <td class="px-6 py-3.5 text-center text-[#71717A] font-medium border-r border-[#E5E7EB]">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-3.5 font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ $item['nama_asset'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center text-xs text-[#71717A] border-r border-[#E5E7EB]">
                        {{ $item['satuan'] }}
                    </td>
                    <td class="px-6 py-3.5 text-center font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ number_format($item['stok']) }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-emerald-700 border-r border-[#E5E7EB]">
                        {{ number_format($item['baik']) }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-rose-600 border-r border-[#E5E7EB]">
                        {{ number_format($item['rusak']) }}
                    </td>
                    <td class="px-6 py-3.5 text-xs text-[#52525B]">
                        {{ $item['keterangan'] }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-6 text-center text-[#A1A1AA] text-xs">
                        Tidak ada data inventaris kantor untuk tahun {{ $selectedYear }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================== SECTION 3: BREAKDOWN PER LOKASI SPESIFIK (KANTOR) ============================== --}}
@if(isset($kantorRoomSummary) && count($kantorRoomSummary) > 0)
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-amber-50/50">
        <div class="flex items-center gap-2.5">
            <i data-lucide="door-open" class="w-5 h-5 text-amber-700"></i>
            <h2 class="text-sm font-bold text-amber-800 tracking-wider uppercase">BREAKDOWN ASET PER RUANGAN KANTOR (LOKASI SPESIFIK)</h2>
        </div>
        <span class="text-xs font-bold text-amber-800 bg-amber-100 px-3 py-1 rounded-full border border-amber-200">
            {{ count($kantorRoomSummary) }} Ruangan Terdaftar
        </span>
    </div>

    {{-- Room Summary Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th class="px-6 py-3 w-16 text-center border-r border-[#E5E7EB]">No</th>
                    <th class="px-6 py-3 border-r border-[#E5E7EB]">Lokasi Spesifik / Ruangan</th>
                    <th class="px-6 py-3 text-center border-r border-[#E5E7EB] w-36">Total Jenis Barang</th>
                    <th class="px-6 py-3 text-center border-r border-[#E5E7EB] w-32 text-emerald-700">Kondisi Baik</th>
                    <th class="px-6 py-3 text-center border-r border-[#E5E7EB] w-32 text-rose-600">Kondisi Rusak</th>
                    <th class="px-6 py-3 text-center w-36 font-extrabold text-[#18181B]">Total Aset</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @foreach($kantorRoomSummary as $idx => $room)
                <tr class="hover:bg-amber-50/30 transition-colors">
                    <td class="px-6 py-3 text-center text-[#71717A] font-medium border-r border-[#E5E7EB]">
                        {{ $idx + 1 }}
                    </td>
                    <td class="px-6 py-3 font-bold text-[#18181B] border-r border-[#E5E7EB] flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
                        <span>{{ $room->lokasi_spesifik }}</span>
                    </td>
                    <td class="px-6 py-3 text-center font-semibold text-[#52525B] border-r border-[#E5E7EB]">
                        {{ number_format($room->total_jenis_barang) }} Jenis
                    </td>
                    <td class="px-6 py-3 text-center font-bold text-emerald-700 border-r border-[#E5E7EB]">
                        {{ number_format($room->total_baik) }}
                    </td>
                    <td class="px-6 py-3 text-center font-bold text-rose-600 border-r border-[#E5E7EB]">
                        {{ number_format($room->total_rusak) }}
                    </td>
                    <td class="px-6 py-3 text-center font-extrabold text-[#18181B]">
                        {{ number_format($room->total_aset) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
