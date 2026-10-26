<x-admin-layout>

<x-slot name="title">Inventaris — Summary Overview | FR Academy</x-slot>
    <x-slot name="header">Inventaris — Summary Overview | FR Academy</x-slot>



{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Inventaris Gudang & Kantor</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Rekapitulasi kondisi aset berdasarkan jenis lokasi & rincian ruangan kantor.</p>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
        {{-- Location Switcher Pills --}}
        <div class="inline-flex bg-[#F4F4F5] border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
            <a href="{{ route('administrasi.inventaris.summary', array_merge(request()->query(), ['jenis_lokasi' => ''])) }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ empty($jenisLokasiFilter) ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                Semua
            </a>
            <a href="{{ route('administrasi.inventaris.summary', array_merge(request()->query(), ['jenis_lokasi' => 'Gudang'])) }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $jenisLokasiFilter === 'Gudang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                Gudang
            </a>
            <a href="{{ route('administrasi.inventaris.summary', array_merge(request()->query(), ['jenis_lokasi' => 'Kantor'])) }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $jenisLokasiFilter === 'Kantor' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-white' }}">
                Kantor
            </a>
        </div>

        {{-- Feature Sub-tabs --}}
        <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm">
            <a href="{{ route('administrasi.inventaris.summary', request()->query()) }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'summary' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Summary
            </a>
            <a href="{{ route('administrasi.inventaris.barang') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'barang' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Barang
            </a>
            <a href="{{ route('administrasi.inventaris.pembelian') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'pembelian' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Pembelian
            </a>
            <a href="{{ route('administrasi.inventaris.stock-opname') }}"
               class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $activeTab === 'stock_opname' ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                Stock Opname
            </a>
        </div>
    </div>
</div>

{{-- ============================== LATEST STOCK OPNAME INFO & SESSION SELECTOR ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 mb-6 shadow-sm">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#4B6A34] text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="clipboard-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-[#18181B]">Data Stock Opname Sesi Terbaru</h3>
                <p class="text-xs text-[#71717A] mt-0.5">
                    Rekap kondisi aset murni bersumber dari hasil pelaksanaan Stock Opname terbaru (tanpa penggabungan data pembelian).
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if(!empty($latestDateKantor))
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-900 shadow-2xs">
                    <i data-lucide="building-2" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                    <span>Kantor: <strong>{{ \Carbon\Carbon::parse($latestDateKantor)->translatedFormat('d F Y') }}</strong></span>
                </div>
            @endif
            @if(!empty($latestDateGudang))
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-semibold text-emerald-900 shadow-2xs">
                    <i data-lucide="warehouse" class="w-3.5 h-3.5 text-[#4B6A34]"></i>
                    <span>Gudang: <strong>{{ \Carbon\Carbon::parse($latestDateGudang)->translatedFormat('d F Y') }}</strong></span>
                </div>
            @else
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-500 shadow-2xs">
                    <i data-lucide="warehouse" class="w-3.5 h-3.5 text-gray-400"></i>
                    <span>Gudang: <em>Belum ada sesi SO</em></span>
                </div>
            @endif
        </div>
    </div>

    @if(count($availableDates) > 1)
    <div class="mt-4 pt-3 border-t border-[#E5E7EB] flex items-center gap-2 overflow-x-auto scrollbar-thin">
        <span class="text-[11px] font-bold text-[#71717A] uppercase shrink-0 flex items-center gap-1">
            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
            Sesi Lainnya:
        </span>
        @foreach($availableDates as $dt)
            <a href="{{ route('administrasi.inventaris.summary', array_merge(request()->query(), ['date' => $dt])) }}"
               class="px-3.5 py-1 text-xs font-bold rounded-lg transition-all whitespace-nowrap {{ $selectedDate === $dt ? 'bg-[#4B6A34] text-white shadow-sm' : 'bg-[#F4F4F5] text-[#52525B] hover:bg-[#E4E4E7] hover:text-[#18181B]' }}">
                {{ \Carbon\Carbon::parse($dt)->translatedFormat('d F Y') }}
                @if($dt === $overallLatestDate)
                    <span class="ml-1 text-[10px] px-1.5 py-0.5 bg-emerald-700 text-white rounded-full font-medium">Terbaru</span>
                @endif
            </a>
        @endforeach
    </div>
    @endif
</div>

{{-- ============================== SECTION 1: GUDANG ============================== --}}
@if(isset($summaryData['Gudang']))
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
        <div class="flex items-center gap-2.5">
            <i data-lucide="warehouse" class="w-5 h-5 text-[#4B6A34]"></i>
            <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">REKAP ASET GUDANG</h2>
            @if(!empty($latestDateGudang))
                <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                    Sesi: {{ \Carbon\Carbon::parse($latestDateGudang)->translatedFormat('d F Y') }}
                </span>
            @endif
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
                    <td colspan="7" class="px-6 py-8 text-center text-[#A1A1AA] text-xs">
                        Belum ada data pelaksanaan stock opname untuk inventaris gudang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ============================== SECTION 2: KANTOR (AGREGAT & BREAKDOWN PER RUANGAN) ============================== --}}
@if(isset($summaryData['Kantor']))
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
        <div class="flex items-center gap-2.5">
            <i data-lucide="building-2" class="w-5 h-5 text-[#4B6A34]"></i>
            <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">REKAP ASET KANTOR (AGREGAT)</h2>
            @if(!empty($latestDateKantor))
                <span class="text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                    Sesi: {{ \Carbon\Carbon::parse($latestDateKantor)->translatedFormat('d F Y') }}
                </span>
            @endif
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
                    <td colspan="7" class="px-6 py-8 text-center text-[#A1A1AA] text-xs">
                        Belum ada data pelaksanaan stock opname untuk inventaris kantor.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ============================== SECTION 3: BREAKDOWN PER LOKASI SPESIFIK (KANTOR) ============================== --}}
@if(isset($summaryData['Kantor']) && isset($kantorRoomSummary) && count($kantorRoomSummary) > 0)
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm mb-6 overflow-hidden">
    {{-- Section Header --}}
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-amber-50/50">
        <div class="flex items-center gap-2.5">
            <i data-lucide="door-open" class="w-5 h-5 text-amber-700"></i>
            <h2 class="text-sm font-bold text-amber-800 tracking-wider uppercase">BREAKDOWN ASET PER RUANGAN KANTOR (LOKASI SPESIFIK)</h2>
            @if(!empty($latestDateKantor))
                <span class="text-[11px] font-semibold text-amber-800 bg-amber-100 border border-amber-200 px-2.5 py-0.5 rounded-full">
                    Sesi: {{ \Carbon\Carbon::parse($latestDateKantor)->translatedFormat('d F Y') }}
                </span>
            @endif
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

</x-admin-layout>
