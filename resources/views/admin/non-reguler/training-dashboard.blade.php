<x-admin-layout>
    <x-slot name="title">Dashboard Learning Activity</x-slot>

    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Hero / Welcome Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-[#143d23] via-[#1d5732] to-[#28844b] rounded-2xl px-6 py-6 lg:py-8 text-white shadow-lg mb-8 mt-2 flex items-center min-h-[140px]">
        <!-- Decorative Geometric Pattern -->
        <div class="absolute right-0 top-0 bottom-0 opacity-20 pointer-events-none flex items-center justify-end w-3/4 overflow-hidden h-full">
            <svg class="h-[150%] max-h-56 w-auto text-white transform translate-x-4 lg:translate-x-0" viewBox="0 0 200 100" fill="currentColor" preserveAspectRatio="xMaxYMid slice">
                <path d="M 0 0 A 50 50 0 0 0 50 50 L 0 50 Z" opacity="0.5"/>
                <path d="M 50 50 A 50 50 0 0 1 100 0" fill="none" stroke="currentColor" stroke-width="5" opacity="0.7"/>
                <path d="M 68 50 A 32 32 0 0 1 100 18" fill="none" stroke="currentColor" stroke-width="5" opacity="0.7"/>
                <path d="M 86 50 A 14 14 0 0 1 100 36" fill="none" stroke="currentColor" stroke-width="5" opacity="0.7"/>
                <circle cx="125" cy="25" r="16" fill="none" stroke="currentColor" stroke-width="8" opacity="0.8"/>
                <path d="M 200 0 A 50 50 0 0 1 150 50 L 200 50 Z" opacity="0.6"/>
                <path d="M -20 100 A 50 50 0 0 1 30 50 L -20 50 Z" opacity="0.4"/>
                <path d="M 0 100 A 50 50 0 0 1 50 50 L 0 50 Z" opacity="0.4"/>
                <rect x="50" y="50" width="50" height="50" opacity="0.6"/>
                <circle cx="125" cy="75" r="18" fill="none" stroke="currentColor" stroke-width="12" opacity="0.5"/>
                <path d="M 200 50 A 50 50 0 0 0 200 100 Z" opacity="0.7"/>
            </svg>
        </div>
        
        <div class="relative z-10 w-full flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-emerald-200 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Non-Reguler & Pengembangan Kompetensi Karyawan
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">Dashboard Learning Activity</h1>
                <p class="text-green-100 text-sm lg:text-base max-w-2xl mt-1">
                    Pusat monitoring operasional materi pelatihan non-reguler, distribusi kompetensi, dan realisasi pembelajaran.
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards (2 KPI Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <!-- Card 1: Total Materi -->
        <div class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Materi</span>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-md shadow-orange-200 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-800">{{ number_format($totalSesi) }}</span>
                <span class="text-xs font-semibold text-slate-400 lowercase">materi</span>
            </div>
            <div class="mt-2 text-xs text-amber-600 font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Siap Dijadwalkan
            </div>
        </div>

        <!-- Card 2: Kelas Hari Ini -->
        <div class="group relative overflow-hidden bg-white rounded-2xl p-5 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kelas Aktif Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white flex items-center justify-center shadow-md shadow-purple-200 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black {{ ($todaySesiCount ?? 0) > 0 ? 'text-violet-600' : 'text-slate-700' }}">{{ $todaySesiCount ?? 0 }}</span>
                <span class="text-xs font-semibold text-slate-400 lowercase">sesi aktif</span>
            </div>
            <div class="mt-2 text-xs {{ ($todaySesiCount ?? 0) > 0 ? 'text-violet-600 font-bold' : 'text-slate-400 font-medium' }} flex items-center gap-1">
                <span class="w-2 h-2 rounded-full {{ ($todaySesiCount ?? 0) > 0 ? 'bg-violet-500 animate-ping' : 'bg-slate-300' }}"></span>
                {{ ($todaySesiCount ?? 0) > 0 ? 'Sedang berlangsung hari ini' : 'Tidak ada kelas hari ini' }}
            </div>
        </div>
    </div>

    <!-- Visual Charts Section (Distribusi Materi per Sub Kategori) -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black text-slate-800">Distribusi Materi per Sub Kategori</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Proporsi materi pelatihan non-reguler berdasarkan sub kategori</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                    {{ $totalSesi }} Total Materi
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                <div class="md:col-span-5 lg:col-span-4 relative flex items-center justify-center min-h-[220px]">
                    <canvas id="programDonutChart" class="max-h-[220px]"></canvas>
                </div>
                <div class="md:col-span-7 lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                        $colors = ['#10b981', '#059669', '#3b82f6', '#f59e0b', '#8b5cf6', '#64748b'];
                    @endphp
                    @foreach($programStats as $idx => $prog)
                        @php
                            $pct = $totalSesi > 0 ? round(($prog['count'] / $totalSesi) * 100) : 0;
                            $c = $colors[$idx % count($colors)];
                        @endphp
                        <div class="flex items-center justify-between text-xs p-3 rounded-xl bg-slate-50/70 border border-slate-100 hover:bg-slate-100/70 transition-colors">
                            <div class="flex items-center gap-2.5 truncate pr-2">
                                <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-sm" style="background-color: {{ $c }}"></span>
                                <span class="font-bold text-slate-700 truncate" title="{{ $prog['name'] }}">{{ $prog['name'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="font-black text-slate-900">{{ $prog['count'] }} materi</span>
                                <span class="text-[11px] text-slate-400 font-mono font-semibold">({{ $pct }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Akses Cepat & Menu Operasional -->
    <div class="mb-10">
        <h3 class="font-extrabold text-slate-800 text-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Akses Cepat & Menu Operasional
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Shortcut 1: Kelola Materi & Sesi -->
            <a href="{{ route('training-non-reguler.daily-classes.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#1d5732] to-[#28844b] rounded-2xl p-6 text-white shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="font-black text-lg mb-1">Kelola Materi & Absensi</h4>
                    <p class="text-green-100 text-xs leading-relaxed">Atur tanggal kelas, bagikan QR code absensi peserta, dan kelola materi pembelajaran.</p>
                </div>
                <div class="mt-6 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-200 group-hover:translate-x-1 transition-transform">
                    Buka Manajemen Materi &rarr;
                </div>
            </a>

            <!-- Shortcut 2: Riwayat Pelatihan -->
            <a href="{{ route('training-non-reguler.history.index') }}" class="group relative overflow-hidden bg-white border border-slate-200 rounded-2xl p-6 hover:border-emerald-300 hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center mb-4 group-hover:bg-emerald-100 group-hover:text-emerald-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="font-black text-slate-800 text-lg mb-1 group-hover:text-emerald-700 transition-colors">Riwayat Pelatihan</h4>
                    <p class="text-slate-400 text-xs leading-relaxed">Arsip dokumentasi kelas pelatihan dan rekapitulasi yang sudah selesai diselenggarakan.</p>
                </div>
                <div class="mt-6 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition-transform">
                    Buka Arsip Riwayat &rarr;
                </div>
            </a>
        </div>
    </div>

    <!-- Script Chart.js Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Donut Chart (Program Distribution)
            const progData = @json($programStats);
            const progLabels = progData.map(p => p.name);
            const progCounts = progData.map(p => p.count);
            const progColors = ['#10b981', '#059669', '#3b82f6', '#f59e0b', '#8b5cf6', '#64748b'];

            const donutCtx = document.getElementById('programDonutChart')?.getContext('2d');
            if (donutCtx) {
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: progLabels,
                        datasets: [{
                            data: progCounts,
                            backgroundColor: progColors.slice(0, progLabels.length),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (ctx) {
                                        const val = ctx.raw || 0;
                                        const sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = sum > 0 ? Math.round((val / sum) * 100) : 0;
                                        return ` ${val} Materi (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-admin-layout>
