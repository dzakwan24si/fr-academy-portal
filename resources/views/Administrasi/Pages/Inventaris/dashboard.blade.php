<x-admin-layout>

<x-slot name="title">Dashboard Visualisasi Aset & Inventaris | FR Academy</x-slot>
    <x-slot name="header">Dashboard Visualisasi Aset & Inventaris | FR Academy</x-slot>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush



{{-- ============================== HEADER & SUB-TABS ============================== --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Dashboard Aset & Inventaris</h1>
        <p class="text-xs text-[#71717A] mt-0.5">Visualisasi kesehatan aset, sebaran ruangan, serta tren pengadaan inventaris academy.</p>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
        {{-- Year Filter Pills --}}
        <div class="inline-flex bg-white border border-[#E5E7EB] rounded-xl p-1 shadow-sm shrink-0">
            @foreach($years as $yr)
            <a href="{{ route('administrasi.inventaris.dashboard', ['year' => $yr]) }}"
               class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all {{ $selectedYear === $yr ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#52525B] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                {{ $yr }}
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ============================== TOP STAT CARDS (KPIs) ============================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card 1: Total Jenis Barang --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[#71717A] uppercase tracking-wider">Total Jenis Barang</span>
            <div class="w-9 h-9 bg-emerald-50 text-[#4B6A34] rounded-xl flex items-center justify-center">
                <i data-lucide="boxes" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-[#18181B]">{{ number_format($totalJenisBarang) }}</span>
            <span class="text-xs font-medium text-[#71717A]">Jenis Aset</span>
        </div>
        <div class="mt-3 pt-3 border-t border-[#F4F4F5] flex items-center justify-between text-[11px] text-[#71717A]">
            <span>Gudang: <strong class="text-[#18181B]">{{ $totalBarangGudang }}</strong></span>
            <span>Kantor: <strong class="text-[#18181B]">{{ $totalBarangKantor }}</strong></span>
        </div>
    </div>

    {{-- Card 2: Total Unit Aset --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[#71717A] uppercase tracking-wider">Total Volume Stok</span>
            <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i data-lucide="package-search" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-[#18181B]">{{ number_format($totalAsetUnit) }}</span>
            <span class="text-xs font-medium text-[#71717A]">Total Unit</span>
        </div>
        <div class="mt-3 pt-3 border-t border-[#F4F4F5] flex items-center justify-between text-[11px] text-[#71717A]">
            <span>Pengadaan {{ $selectedYear }}:</span>
            <span class="font-bold text-blue-600">+{{ number_format($totalPembelianYtd) }} Unit</span>
        </div>
    </div>

    {{-- Card 3: Kondisi Baik (Asset Health) --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[#71717A] uppercase tracking-wider">Aset Kondisi Baik</span>
            <div class="w-9 h-9 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-emerald-700">{{ number_format($totalKondisiBaik) }}</span>
            <span class="text-xs font-medium text-[#71717A]">Unit</span>
        </div>
        <div class="mt-3 pt-3 border-t border-[#F4F4F5] flex items-center justify-between text-[11px]">
            <span class="text-[#71717A]">Rasio Layak Pakai:</span>
            <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">{{ $healthRatio }}%</span>
        </div>
    </div>

    {{-- Card 4: Kondisi Rusak --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[#71717A] uppercase tracking-wider">Aset Perlu Maintenance</span>
            <div class="w-9 h-9 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold text-rose-600">{{ number_format($totalKondisiRusak) }}</span>
            <span class="text-xs font-medium text-[#71717A]">Unit Rusak</span>
        </div>
        <div class="mt-3 pt-3 border-t border-[#F4F4F5] flex items-center justify-between text-[11px]">
            <span class="text-[#71717A]">Persentase Rusak:</span>
            <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md">{{ $damageRatio }}%</span>
        </div>
    </div>
</div>

{{-- ============================== SECTION 1: CHARTS (LOCATION CONDITION & PURCHASE TREND) ============================== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Chart 1: Kondisi Aset per Jenis Lokasi --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-[#18181B]">Kondisi Aset: Gudang vs Kantor</h2>
                <p class="text-xs text-[#71717A]">Perbandingan jumlah barang kondisi baik & rusak per lokasi</p>
            </div>
            <span class="text-xs font-bold text-[#71717A] bg-[#F4F4F5] px-2.5 py-1 rounded-lg">By Stock Opname</span>
        </div>
        <div class="h-64 relative">
            <canvas id="locationConditionChart"></canvas>
        </div>
    </div>

    {{-- Chart 2: Tren Pembelian Aset --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-[#18181B]">Tren Pengadaan Barang (YTD)</h2>
                <p class="text-xs text-[#71717A]">Jumlah volume pembelian aset baru dari tahun ke tahun</p>
            </div>
            <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">2020 – {{ $selectedYear }}</span>
        </div>
        <div class="h-64 relative">
            <canvas id="purchaseTrendChart"></canvas>
        </div>
    </div>
</div>

{{-- ============================== SECTION 2: CHARTS (ROOM SEBARAN & TOP DAMAGED ITEMS) ============================== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Chart 3: Sebaran Aset Kantor per Ruangan --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-[#18181B]">Sebaran Aset Kantor per Ruangan</h2>
                <p class="text-xs text-[#71717A]">Volume barang di lokasi/ruangan spesifik</p>
            </div>
            <span class="text-xs font-bold text-[#71717A] bg-[#F4F4F5] px-2.5 py-1 rounded-lg">{{ count($chartRoom['rooms']) }} Ruangan</span>
        </div>
        <div class="h-72 relative">
            <canvas id="roomDistributionChart"></canvas>
        </div>
    </div>

    {{-- Damaged Items Table / Maintenance Alert --}}
    <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
            <div>
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                    <h2 class="text-base font-bold text-[#18181B]">Daftar Aset Perlu Maintenance / Replacement</h2>
                </div>
                <p class="text-xs text-[#71717A] mt-0.5">Seluruh daftar barang yang mengalami kerusakan dan memerlukan pemeliharaan atau penggantian</p>
            </div>
            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-3 py-1 border border-rose-200 rounded-full">
                {{ count($topDamagedItems) }} Barang Perlu Tindakan
            </span>
        </div>

        <div class="overflow-x-auto flex-1 max-h-[500px]">
            <table class="w-full text-sm text-left">
                <thead class="sticky top-0 bg-[#F9FAFB] z-10 shadow-xs">
                    <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-5 py-3">Nama Asset</th>
                        <th class="px-3 py-3 text-center">Lokasi</th>
                        <th class="px-3 py-3 text-center text-emerald-700">Baik</th>
                        <th class="px-3 py-3 text-center text-rose-600">Rusak</th>
                        <th class="px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                    @forelse($topDamagedItems as $item)
                    <tr class="hover:bg-[#F9FAFB] transition-colors">
                        <td class="px-5 py-3 font-bold text-[#18181B]">
                            {{ $item['nama_asset'] }}
                            <span class="block text-[11px] text-[#71717A] font-normal">{{ $item['satuan'] }}</span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded-md {{ $item['jenis_lokasi'] === 'Gudang' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $item['jenis_lokasi'] }}
                                @if($item['lokasi_spesifik'] && $item['lokasi_spesifik'] !== '-')
                                    ({{ $item['lokasi_spesifik'] }})
                                @endif
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center font-bold text-emerald-700">
                            {{ number_format($item['jumlah_baik']) }}
                        </td>
                        <td class="px-3 py-3 text-center font-bold text-rose-600">
                            <span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded-md">{{ number_format($item['jumlah_rusak']) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-[#52525B]">
                            {{ $item['keterangan'] }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-[#A1A1AA] text-xs">
                            🎉 Tidak ada barang yang rusak terdeteksi pada Stock Opname {{ $selectedYear }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================== SECTION 3: ROOM BREAKDOWN SUMMARY TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
        <div class="flex items-center gap-2.5">
            <i data-lucide="building-2" class="w-5 h-5 text-[#4B6A34]"></i>
            <h2 class="text-sm font-bold text-[#4B6A34] tracking-wider uppercase">REKAPITULASI ASET PER RUANGAN KANTOR</h2>
        </div>
        <span class="text-xs font-bold text-[#71717A] bg-white px-3 py-1 border border-[#E5E7EB] rounded-full">
            {{ count($roomSummary) }} Area Terdata
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-[#E5E7EB] bg-[#F9FAFB] text-[#71717A] text-xs font-bold uppercase tracking-wider">
                    <th class="px-6 py-3 w-16 text-center border-r border-[#E5E7EB]">No</th>
                    <th class="px-6 py-3 border-r border-[#E5E7EB]">Ruangan / Lokasi Spesifik</th>
                    <th class="px-6 py-3 text-center border-r border-[#E5E7EB] w-36">Kondisi Baik</th>
                    <th class="px-6 py-3 text-center border-r border-[#E5E7EB] w-36 text-rose-600">Kondisi Rusak</th>
                    <th class="px-6 py-3 text-center w-36 font-extrabold text-[#18181B]">Total Aset</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E5E7EB] text-[#18181B]">
                @forelse($roomSummary as $index => $room)
                <tr class="hover:bg-[#F9FAFB] transition-colors">
                    <td class="px-6 py-3.5 text-center text-[#71717A] font-medium border-r border-[#E5E7EB]">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-3.5 font-bold text-[#18181B] border-r border-[#E5E7EB]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="door-open" class="w-4 h-4 text-[#4B6A34]"></i>
                            <span>{{ $room->lokasi_spesifik }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-center font-bold text-emerald-700 border-r border-[#E5E7EB]">
                        {{ number_format($room->total_baik) }} Unit
                    </td>
                    <td class="px-6 py-3.5 text-center font-bold text-rose-600 border-r border-[#E5E7EB]">
                        {{ number_format($room->total_rusak) }} Unit
                    </td>
                    <td class="px-6 py-3.5 text-center font-extrabold text-[#18181B]">
                        {{ number_format($room->total_aset) }} Unit
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-[#A1A1AA] text-xs">
                        Belum ada rincian lokasi ruangan spesifik terdaftar pada Stock Opname.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================== CHART INITIALIZATION SCRIPT ============================== --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Chart: Location Condition (Gudang vs Kantor)
    const ctxLoc = document.getElementById('locationConditionChart')?.getContext('2d');
    if (ctxLoc) {
        new Chart(ctxLoc, {
            type: 'bar',
            data: {
                labels: @json($chartLocationCondition['categories']),
                datasets: [
                    {
                        label: 'Kondisi Baik',
                        data: @json($chartLocationCondition['baik']),
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                    },
                    {
                        label: 'Kondisi Rusak',
                        data: @json($chartLocationCondition['rusak']),
                        backgroundColor: '#F43F5E',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Instrument Sans', size: 12, weight: 'bold' } } },
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#F3F4F6' } }
                }
            }
        });
    }

    // 2. Chart: Purchase Trend (Line Chart)
    const ctxTrend = document.getElementById('purchaseTrendChart')?.getContext('2d');
    if (ctxTrend) {
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($chartPurchaseTrend['years']),
                datasets: [{
                    label: 'Unit Barang Dibeli',
                    data: @json($chartPurchaseTrend['quantities']),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#2563EB',
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#F3F4F6' } }
                }
            }
        });
    }

    // 3. Chart: Room Distribution (Horizontal Bar)
    const ctxRoom = document.getElementById('roomDistributionChart')?.getContext('2d');
    if (ctxRoom) {
        new Chart(ctxRoom, {
            type: 'bar',
            data: {
                labels: @json($chartRoom['rooms']),
                datasets: [
                    {
                        label: 'Kondisi Baik',
                        data: @json($chartRoom['baik']),
                        backgroundColor: '#059669',
                        borderRadius: 4,
                    },
                    {
                        label: 'Kondisi Rusak',
                        data: @json($chartRoom['rusak']),
                        backgroundColor: '#E11D48',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Instrument Sans', size: 11, weight: 'bold' } } }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush

</x-admin-layout>
