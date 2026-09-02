@extends('Administrasi.Layouts.app')

@section('title', 'Inventaris — Summary Overview | FR Academy')

@section('content')

{{-- ============================== TOP SUB-TABS (SUMMARY, GUDANG, KANTOR) ============================== --}}
<div class="flex items-center justify-start mb-6">
    <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
        <a href="{{ route('administrasi.inventaris.summary') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all bg-[#4B6A34] text-white shadow-sm">
            Summary
        </a>
        <a href="{{ route('administrasi.inventaris.gudang.barang') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]">
            Gudang
        </a>
        <a href="{{ route('administrasi.inventaris.kantor.barang') }}"
           class="px-5 py-2 text-xs font-bold rounded-lg transition-all text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]">
            Kantor
        </a>
    </div>
</div>

{{-- ============================== PAGE TITLE & YEAR SELECTOR ============================== --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris — Summary Overview</h1>
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
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center gap-2.5">
        <i data-lucide="warehouse" class="w-5 h-5 text-[#4B6A34]"></i>
        <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">GUDANG</h2>
    </div>

    {{-- Summary Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th rowspan="2" class="px-6 py-3 w-16 text-center border-r border-[#E5E7EB]">No</th>
                    <th rowspan="2" class="px-6 py-3 border-r border-[#E5E7EB]">Nama Asset</th>
                    <th rowspan="2" class="px-6 py-3 text-center border-r border-[#E5E7EB] w-28">Stok</th>
                    <th colspan="3" class="px-6 py-2 text-center border-b border-r border-[#E5E7EB]">Jumlah Saat Ini</th>
                    <th rowspan="2" class="px-6 py-3">Keterangan</th>
                </tr>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-[11px] font-bold uppercase tracking-wider">
                    <th class="px-4 py-2 text-center text-[#2F6B27] border-r border-[#E5E7EB] w-28">Baik</th>
                    <th class="px-4 py-2 text-center text-[#C2410C] border-r border-[#E5E7EB] w-28">Rusak Ringan</th>
                    <th class="px-4 py-2 text-center text-[#DC2626] border-r border-[#E5E7EB] w-28">Rusak Berat</th>
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
                    <td class="px-6 py-3.5 text-center font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ $item['stok'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#2F6B27] border-r border-[#E5E7EB]">
                        {{ $item['baik'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#C2410C] border-r border-[#E5E7EB]">
                        {{ $item['rusak_ringan'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#DC2626] border-r border-[#E5E7EB]">
                        {{ $item['rusak_berat'] }}
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

{{-- ============================== SECTION 2: KANTOR ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center gap-2.5">
        <i data-lucide="briefcase" class="w-5 h-5 text-[#4B6A34]"></i>
        <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">KANTOR</h2>
    </div>

    {{-- Summary Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th rowspan="2" class="px-6 py-3 w-16 text-center border-r border-[#E5E7EB]">No</th>
                    <th rowspan="2" class="px-6 py-3 border-r border-[#E5E7EB]">Nama Asset</th>
                    <th rowspan="2" class="px-6 py-3 text-center border-r border-[#E5E7EB] w-28">Stok</th>
                    <th colspan="3" class="px-6 py-2 text-center border-b border-r border-[#E5E7EB]">Jumlah Saat Ini</th>
                    <th rowspan="2" class="px-6 py-3">Keterangan</th>
                </tr>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-[11px] font-bold uppercase tracking-wider">
                    <th class="px-4 py-2 text-center text-[#2F6B27] border-r border-[#E5E7EB] w-28">Baik</th>
                    <th class="px-4 py-2 text-center text-[#C2410C] border-r border-[#E5E7EB] w-28">Rusak Ringan</th>
                    <th class="px-4 py-2 text-center text-[#DC2626] border-r border-[#E5E7EB] w-28">Rusak Berat</th>
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
                    <td class="px-6 py-3.5 text-center font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ $item['stok'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#2F6B27] border-r border-[#E5E7EB]">
                        {{ $item['baik'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#C2410C] border-r border-[#E5E7EB]">
                        {{ $item['rusak_ringan'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#DC2626] border-r border-[#E5E7EB]">
                        {{ $item['rusak_berat'] }}
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

{{-- ============================== SECTION 3: MESS ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center gap-2.5">
        <i data-lucide="hotel" class="w-5 h-5 text-[#4B6A34]"></i>
        <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">MESS</h2>
    </div>

    {{-- Summary Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th rowspan="2" class="px-6 py-3 w-16 text-center border-r border-[#E5E7EB]">No</th>
                    <th rowspan="2" class="px-6 py-3 border-r border-[#E5E7EB]">Nama Asset</th>
                    <th rowspan="2" class="px-6 py-3 text-center border-r border-[#E5E7EB] w-28">Stok</th>
                    <th colspan="3" class="px-6 py-2 text-center border-b border-r border-[#E5E7EB]">Jumlah Saat Ini</th>
                    <th rowspan="2" class="px-6 py-3">Keterangan</th>
                </tr>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-[11px] font-bold uppercase tracking-wider">
                    <th class="px-4 py-2 text-center text-[#2F6B27] border-r border-[#E5E7EB] w-28">Baik</th>
                    <th class="px-4 py-2 text-center text-[#C2410C] border-r border-[#E5E7EB] w-28">Rusak Ringan</th>
                    <th class="px-4 py-2 text-center text-[#DC2626] border-r border-[#E5E7EB] w-28">Rusak Berat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($summaryData['Mess'] ?? [] as $index => $item)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    <td class="px-6 py-3.5 text-center text-[#71717A] font-medium border-r border-[#E5E7EB]">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-3.5 font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ $item['nama_asset'] }}
                    </td>
                    <td class="px-6 py-3.5 text-center font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        {{ $item['stok'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#2F6B27] border-r border-[#E5E7EB]">
                        {{ $item['baik'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#C2410C] border-r border-[#E5E7EB]">
                        {{ $item['rusak_ringan'] }}
                    </td>
                    <td class="px-4 py-3.5 text-center font-bold text-[#DC2626] border-r border-[#E5E7EB]">
                        {{ $item['rusak_berat'] }}
                    </td>
                    <td class="px-6 py-3.5 text-xs text-[#52525B]">
                        {{ $item['keterangan'] }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-6 text-center text-[#A1A1AA] text-xs">
                        Tidak ada data inventaris mess untuk tahun {{ $selectedYear }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
