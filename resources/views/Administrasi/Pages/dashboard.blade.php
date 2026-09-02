@extends('Administrasi.Layouts.app')

@section('title', 'Administration Dashboard | FR Academy Enterprise Portal')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')

{{-- ============================== PAGE HEADER ============================== --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Administration Dashboard</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Ringkasan aktivitas administrasi & keuangan — {{ date('F Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <div class="bg-white border border-[#E5E7EB] rounded-full px-4 py-1.5 flex items-center gap-2 shadow-xs">
            <span class="w-2 h-2 bg-[#4B6A34] rounded-full animate-pulse"></span>
            <span class="text-xs font-bold text-[#18181B]">Periode Aktif: {{ date('Y') }}</span>
        </div>
    </div>
</div>

{{-- ============================== KPI SUMMARY CARDS ============================== --}}
<div class="grid grid-cols-5 gap-4 mb-6">

    {{-- Budget This Month --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 col-span-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Budget Bulan Ini</span>
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                <i data-lucide="wallet" class="w-3.5 h-3.5 text-blue-500"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] leading-tight">Rp 850.000.000</p>
        <p class="text-[11px] text-[#A1A1AA] mt-1">Rencana Anggaran {{ date('M Y') }}</p>
    </div>

    {{-- Realisasi S/D Bulan --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 col-span-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Realisasi S/D Bulan</span>
            <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-amber-500"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] leading-tight">Rp 790.000.000</p>
        <p class="text-[11px] text-[#A1A1AA] mt-1">Aktual bulan berjalan</p>
    </div>

    {{-- Budget Utilization --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 col-span-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Utilisasi Budget</span>
            <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                <i data-lucide="percent" class="w-3.5 h-3.5 text-green-500"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-green-600 leading-tight">92,9%</p>
        <div class="w-full h-1.5 bg-[#F4F4F5] rounded-full mt-2 overflow-hidden">
            <div class="h-full bg-green-500 rounded-full" style="width: 92.9%"></div>
        </div>
    </div>

    {{-- CAPEX --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 col-span-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">CAPEX {{ date('Y') }}</span>
            <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                <i data-lucide="landmark" class="w-3.5 h-3.5 text-purple-500"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] leading-tight">Rp 150.000.000</p>
        <p class="text-[11px] text-[#A1A1AA] mt-1">Realisasi vs RAB</p>
    </div>

    {{-- VRA --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 col-span-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">VRA {{ date('M') }}</span>
            <div class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center">
                <i data-lucide="truck" class="w-3.5 h-3.5 text-orange-500"></i>
            </div>
        </div>
        <p class="text-lg font-bold text-[#18181B] leading-tight">Rp 28.750.000</p>
        <p class="text-[11px] text-[#A1A1AA] mt-1">Biaya operasional kendaraan</p>
    </div>
</div>

{{-- ============================== MAIN 2-COLUMN LAYOUT ============================== --}}
<div class="grid grid-cols-3 gap-5 mb-5">

    {{-- LEFT: Budget vs Realisasi Chart (2/3 width) --}}
    <div class="col-span-2 bg-white border border-[#E5E7EB] rounded-xl p-5">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-sm text-[#18181B]">Budget vs Realisasi — {{ date('Y') }}</h3>
                <p class="text-xs text-[#71717A] mt-0.5">Perbandingan anggaran dan realisasi per bulan</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span> Budget</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#4B6A34] inline-block"></span> Realisasi</span>
            </div>
        </div>
        <div class="h-56">
            <canvas id="budgetRealisasiChart"></canvas>
        </div>
    </div>

    {{-- RIGHT: Recruitment Cost + Quick Stats (1/3 width) --}}
    <div class="col-span-1 space-y-4">

        {{-- Biaya Rekruit Card --}}
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Biaya Rekruitmen</span>
                <i data-lucide="user-plus" class="w-4 h-4 text-[#A1A1AA]"></i>
            </div>
            <p class="text-xl font-bold text-[#18181B]">Rp 47.500.000</p>
            <p class="text-xs text-[#71717A] mt-1">Tahun {{ date('Y') }}</p>
            <div class="mt-3 pt-3 border-t border-[#E5E7EB] grid grid-cols-2 gap-2 text-xs">
                <div>
                    <p class="text-[#A1A1AA]">Kandidat</p>
                    <p class="font-bold text-[#18181B]">12 orang</p>
                </div>
                <div>
                    <p class="text-[#A1A1AA]">Avg. Cost</p>
                    <p class="font-bold text-[#18181B]">Rp 3,9 jt</p>
                </div>
            </div>
        </div>

        {{-- Cost Categories Breakdown --}}
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#71717A]">Distribusi Cost</span>
                <i data-lucide="pie-chart" class="w-4 h-4 text-[#A1A1AA]"></i>
            </div>
            <div class="h-36">
                <canvas id="costDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ============================== MONTHLY ADMIN SUMMARY TABLE ============================== --}}
<div class="grid grid-cols-3 gap-5 mb-5">

    {{-- Summary Table (2/3) --}}
    <div class="col-span-2 bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB]">
            <h3 class="font-bold text-sm text-[#18181B]">Ringkasan Administrasi Bulanan — {{ date('Y') }}</h3>
            <a href="{{ route('administrasi.cost.index') }}"
               class="text-xs font-semibold text-[#4B6A34] hover:underline flex items-center gap-1">
                Lihat Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-[#F9FAFB] border-b border-[#E5E7EB]">
                        <th class="px-4 py-3 text-left font-semibold text-[#71717A]">Bulan</th>
                        <th class="px-4 py-3 text-right font-semibold text-[#71717A]">Budget</th>
                        <th class="px-4 py-3 text-right font-semibold text-[#71717A]">Realisasi</th>
                        <th class="px-4 py-3 text-right font-semibold text-[#71717A]">Variance</th>
                        <th class="px-4 py-3 text-right font-semibold text-[#71717A]">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5]">
                    @php
                    $monthlySummary = [
                        ['bulan' => 'Januari',   'budget' => 820000000,  'realisasi' => 798000000],
                        ['bulan' => 'Februari',  'budget' => 810000000,  'realisasi' => 824000000],
                        ['bulan' => 'Maret',     'budget' => 830000000,  'realisasi' => 805000000],
                        ['bulan' => 'April',     'budget' => 845000000,  'realisasi' => 791000000],
                        ['bulan' => 'Mei',       'budget' => 855000000,  'realisasi' => 843000000],
                        ['bulan' => 'Juni',      'budget' => 840000000,  'realisasi' => 812000000],
                        ['bulan' => 'Juli',      'budget' => 850000000,  'realisasi' => 790000000],
                        ['bulan' => 'Agustus',   'budget' => 850000000,  'realisasi' => null],
                    ];
                    @endphp
                    @foreach($monthlySummary as $row)
                    @php
                        $variance  = $row['realisasi'] !== null ? ($row['budget'] - $row['realisasi']) : null;
                        $pct       = $row['realisasi'] !== null ? ($row['realisasi'] / $row['budget'] * 100) : null;
                        $isOver    = $variance !== null && $variance < 0;
                        $isCurrent = $row['bulan'] === date('F', mktime(0,0,0,date('n'),1,date('Y')));
                    @endphp
                    <tr class="hover:bg-[#FAFAFA] {{ $isCurrent ? 'bg-[#F0F5EB]' : '' }}">
                        <td class="px-4 py-2.5 font-medium text-[#18181B]">
                            {{ $row['bulan'] }}
                            @if($isCurrent)
                            <span class="ml-1.5 text-[10px] bg-[#4B6A34] text-white px-1.5 py-0.5 rounded-full font-semibold">Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right text-[#52525B]">Rp {{ number_format($row['budget'], 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right {{ $row['realisasi'] !== null ? 'text-[#52525B]' : 'text-[#A1A1AA]' }}">
                            {{ $row['realisasi'] !== null ? 'Rp ' . number_format($row['realisasi'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-right font-semibold {{ $isOver ? 'text-red-500' : ($variance !== null ? 'text-green-600' : 'text-[#A1A1AA]') }}">
                            {{ $variance !== null ? ($isOver ? '-' : '+') . 'Rp ' . number_format(abs($variance), 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-right font-bold {{ $pct !== null ? ($pct > 100 ? 'text-red-500' : 'text-[#18181B]') : 'text-[#A1A1AA]' }}">
                            {{ $pct !== null ? number_format($pct, 1) . '%' : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-[#F0F5EB] border-t-2 border-[#C6D9B3]">
                        <td class="px-4 py-3 text-xs font-bold text-[#4B6A34] uppercase">Total YTD</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-[#4B6A34]">Rp 5.900.000.000</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-[#4B6A34]">Rp 5.663.000.000</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-green-600">+Rp 237.000.000</td>
                        <td class="px-4 py-3 text-right text-xs font-bold text-[#4B6A34]">95,98%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Recent Activities (1/3) --}}
    <div class="col-span-1 bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-[#E5E7EB]">
            <h3 class="font-bold text-sm text-[#18181B]">Aktivitas Terbaru</h3>
            <p class="text-xs text-[#71717A] mt-0.5">Input & pembaruan data administrasi</p>
        </div>
        <div class="divide-y divide-[#F4F4F5]">
            @php
            $activities = [
                ['icon' => 'banknote',   'color' => 'blue',   'title' => 'Cost input ditambahkan',        'desc' => 'ATK Kantor — Rp 2.450.000',         'time' => '2 jam lalu'],
                ['icon' => 'landmark',   'color' => 'purple', 'title' => 'Transaksi CAPEX dicatat',        'desc' => 'Pembelian Laptop — Rp 37.500.000',   'time' => '4 jam lalu'],
                ['icon' => 'user-plus',  'color' => 'green',  'title' => 'Biaya Rekruit diperbarui',       'desc' => 'Assessment Kandidat Batch 3',        'time' => 'Kemarin'],
                ['icon' => 'truck',      'color' => 'orange', 'title' => 'VRA Juli diperbarui',            'desc' => 'B 1234 XY — 1.240 km / Rp 1.860.000','time' => 'Kemarin'],
                ['icon' => 'clipboard-list','color' => 'gray','title' => 'MPP & Cuti diperbarui',          'desc' => 'Pengajuan cuti 3 karyawan disetujui', 'time' => '3 hari lalu'],
            ];
            $colorMap = [
                'blue'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-500'],
                'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-500'],
                'green'  => ['bg' => 'bg-green-50',  'text' => 'text-green-500'],
                'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-500'],
                'gray'   => ['bg' => 'bg-gray-100',  'text' => 'text-gray-500'],
            ];
            @endphp
            @foreach($activities as $act)
            @php $clr = $colorMap[$act['color']]; @endphp
            <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-[#FAFAFA] transition-colors">
                <div class="w-7 h-7 rounded-full {{ $clr['bg'] }} flex items-center justify-center shrink-0 mt-0.5">
                    <i data-lucide="{{ $act['icon'] }}" class="w-3.5 h-3.5 {{ $clr['text'] }}"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-[#18181B] leading-tight">{{ $act['title'] }}</p>
                    <p class="text-[11px] text-[#71717A] mt-0.5 truncate">{{ $act['desc'] }}</p>
                </div>
                <span class="text-[10px] text-[#A1A1AA] whitespace-nowrap shrink-0 mt-0.5">{{ $act['time'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB]">
            <a href="#" class="text-xs font-semibold text-[#4B6A34] hover:underline flex items-center gap-1">
                Lihat semua aktivitas <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
    </div>
</div>

{{-- ============================== BOTTOM ROW: CAPEX + VRA SNAPSHOT ============================== --}}
<div class="grid grid-cols-2 gap-5">

    {{-- CAPEX Snapshot --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB]">
            <h3 class="font-bold text-sm text-[#18181B]">Snapshot CAPEX {{ date('Y') }}</h3>
            <a href="{{ route('administrasi.capex.index') }}"
               class="text-xs font-semibold text-[#4B6A34] hover:underline flex items-center gap-1">
                Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
        <div class="p-5 grid grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] mb-1">Total RAB</p>
                <p class="text-base font-bold text-[#18181B]">Rp 205 jt</p>
            </div>
            <div class="text-center border-x border-[#E5E7EB]">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] mb-1">Realisasi</p>
                <p class="text-base font-bold text-amber-600">Rp 54 jt</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] mb-1">Sisa RAB</p>
                <p class="text-base font-bold text-green-600">Rp 151 jt</p>
            </div>
        </div>
        <div class="px-5 pb-4">
            <div class="flex items-center justify-between text-[11px] text-[#71717A] mb-1.5">
                <span>Utilisasi CAPEX</span>
                <span class="font-semibold text-[#18181B]">26,3%</span>
            </div>
            <div class="w-full h-2 bg-[#F4F4F5] rounded-full overflow-hidden">
                <div class="h-full bg-amber-400 rounded-full" style="width: 26.3%"></div>
            </div>
        </div>
    </div>

    {{-- VRA Snapshot --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB]">
            <h3 class="font-bold text-sm text-[#18181B]">Snapshot VRA — {{ date('M Y') }}</h3>
            <a href="{{ route('administrasi.vra.index') }}"
               class="text-xs font-semibold text-[#4B6A34] hover:underline flex items-center gap-1">
                Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
        <div class="p-5 grid grid-cols-3 gap-4 mb-2">
            <div class="text-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] mb-1">Total KM</p>
                <p class="text-base font-bold text-[#18181B]">8.420 km</p>
            </div>
            <div class="text-center border-x border-[#E5E7EB]">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] mb-1">Total Biaya</p>
                <p class="text-base font-bold text-orange-600">Rp 28,7 jt</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] mb-1">Unit Aktif</p>
                <p class="text-base font-bold text-[#18181B]">6 unit</p>
            </div>
        </div>
        <div class="px-5 pb-4">
            <div class="h-24">
                <canvas id="vraMonthlyChart"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ============================================================
// CHART 1: Budget vs Realisasi Bar Chart
// ============================================================
(function () {
    const ctx = document.getElementById('budgetRealisasiChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags'],
            datasets: [
                {
                    label: 'Budget',
                    data: [820, 810, 830, 845, 855, 840, 850, 850],
                    backgroundColor: 'rgba(59,130,246,0.15)',
                    borderColor: '#3b82f6',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    borderSkipped: false,
                },
                {
                    label: 'Realisasi',
                    data: [798, 824, 805, 791, 843, 812, 790, null],
                    backgroundColor: 'rgba(75,106,52,0.75)',
                    borderColor: '#4B6A34',
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + (ctx.raw || 0).toLocaleString('id-ID') + ' jt'
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    beginAtZero: false,
                    min: 750,
                    grid: { color: '#F4F4F5' },
                    ticks: {
                        font: { size: 10 },
                        callback: v => 'Rp ' + v + ' jt'
                    }
                }
            }
        }
    });
})();

// ============================================================
// CHART 2: Cost Distribution Doughnut
// ============================================================
(function () {
    const ctx = document.getElementById('costDistributionChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Operasional', 'VRA', 'Rekruit', 'CAPEX', 'Lainnya'],
            datasets: [{
                data: [55, 12, 8, 18, 7],
                backgroundColor: ['#3b82f6','#f97316','#10b981','#8b5cf6','#94a3b8'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 10 }, padding: 8, boxWidth: 10 }
                }
            }
        }
    });
})();

// ============================================================
// CHART 3: VRA Monthly Trend Line
// ============================================================
(function () {
    const ctx = document.getElementById('vraMonthlyChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul'],
            datasets: [{
                label: 'Biaya VRA (jt)',
                data: [24.2, 26.8, 25.5, 28.1, 27.3, 29.4, 28.7],
                borderColor: '#f97316',
                backgroundColor: 'rgba(249,115,22,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#f97316',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: {
                    grid: { color: '#F4F4F5' },
                    ticks: { font: { size: 10 }, callback: v => 'Rp ' + v + ' jt' }
                }
            }
        }
    });
})();
</script>
@endpush
