<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Hero / Welcome Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-green-800 to-green-600 rounded-xl px-8 py-8 lg:py-10 text-white shadow-md mb-8 mt-2 flex items-center h-32 lg:h-40">
        <!-- Decorative Geometric Pattern -->
        <div class="absolute right-0 top-0 bottom-0 opacity-30 pointer-events-none flex items-center justify-end w-3/4 overflow-hidden h-full">
            <svg class="h-[150%] max-h-56 w-auto text-white transform translate-x-4 lg:translate-x-0" viewBox="0 0 200 100" fill="currentColor" preserveAspectRatio="xMaxYMid slice">
                <!-- Top Row -->
                <!-- Quarter Circle pointing bottom right -->
                <path d="M 0 0 A 50 50 0 0 0 50 50 L 0 50 Z" opacity="0.5"/>
                <!-- Concentric arcs from bottom right -->
                <path d="M 50 50 A 50 50 0 0 1 100 0" fill="none" stroke="currentColor" stroke-width="5" opacity="0.7"/>
                <path d="M 68 50 A 32 32 0 0 1 100 18" fill="none" stroke="currentColor" stroke-width="5" opacity="0.7"/>
                <path d="M 86 50 A 14 14 0 0 1 100 36" fill="none" stroke="currentColor" stroke-width="5" opacity="0.7"/>
                <!-- Hollow Circle -->
                <circle cx="125" cy="25" r="16" fill="none" stroke="currentColor" stroke-width="8" opacity="0.8"/>
                <!-- Quarter Circle pointing bottom left -->
                <path d="M 200 0 A 50 50 0 0 1 150 50 L 200 50 Z" opacity="0.6"/>

                <!-- Bottom Row -->
                <!-- Quarter Circle pointing top right -->
                <path d="M -20 100 A 50 50 0 0 1 30 50 L -20 50 Z" opacity="0.4"/>
                <path d="M 0 100 A 50 50 0 0 1 50 50 L 0 50 Z" opacity="0.4"/>
                <!-- Square -->
                <rect x="50" y="50" width="50" height="50" opacity="0.6"/>
                <!-- Thick Hollow circle -->
                <circle cx="125" cy="75" r="18" fill="none" stroke="currentColor" stroke-width="12" opacity="0.5"/>
                <!-- Semi Circle pointing left -->
                <path d="M 200 50 A 50 50 0 0 0 200 100 Z" opacity="0.7"/>
            </svg>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-2xl lg:text-3xl font-bold mb-1.5 tracking-tight">Selamat Datang di Dashboard!</h1>
            <p class="text-green-100 text-sm lg:text-base max-w-xl">Pusat manajemen operasional program pelatihan Learning Activity.</p>
        </div>
    </div>

    <!-- Quick Stats Cards with Gradients & Hover effects -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-10">
        <!-- Card 1 -->
        <div class="group relative overflow-hidden bg-white rounded-3xl p-4 lg:p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.1)] transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute -bottom-4 -right-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-24 h-24 lg:w-32 lg:h-32 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div class="flex items-center gap-3 lg:gap-5">
                <div class="w-12 h-12 lg:w-16 lg:h-16 shrink-0 rounded-2xl bg-gradient-to-br from-green-400 to-[#28844b] text-white flex items-center justify-center shadow-lg shadow-green-200 group-hover:rotate-6 transition-transform">
                    <svg class="w-6 h-6 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs lg:text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider truncate" title="Peserta Kelas Berjalan">Peserta Kelas Berjalan</p>
                    <p class="text-2xl lg:text-3xl font-black text-gray-800">{{ $totalTrainees }} <span class="text-xs lg:text-sm text-gray-400 font-medium lowercase">orang</span></p>
                </div>
            </div>
        </div>
        
        <!-- Card 2 -->
        <div class="group relative overflow-hidden bg-white rounded-3xl p-4 lg:p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.1)] transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute -bottom-4 -right-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-24 h-24 lg:w-32 lg:h-32 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div class="flex items-center gap-3 lg:gap-5">
                <div class="w-12 h-12 lg:w-16 lg:h-16 shrink-0 rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-200 group-hover:rotate-6 transition-transform">
                    <svg class="w-6 h-6 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs lg:text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider truncate" title="Materi Berjalan (Hari ini)">Materi Berjalan (Hari Ini)</p>
                    <p class="text-2xl lg:text-3xl font-black text-gray-800">{{ collect($recentSesi)->count() }} <span class="text-xs lg:text-sm text-gray-400 font-medium lowercase">materi</span></p>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="group relative overflow-hidden bg-white rounded-3xl p-4 lg:p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.1)] transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute -bottom-4 -right-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-24 h-24 lg:w-32 lg:h-32 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div class="flex items-center gap-3 lg:gap-5">
                <div class="w-12 h-12 lg:w-16 lg:h-16 shrink-0 rounded-2xl bg-gradient-to-br from-amber-300 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-200 group-hover:-rotate-6 transition-transform">
                    <svg class="w-6 h-6 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs lg:text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider truncate" title="Total Seluruh Materi">Total Seluruh Materi</p>
                    <p class="text-2xl lg:text-3xl font-black text-gray-800">{{ $totalSesi }} <span class="text-xs lg:text-sm text-gray-400 font-medium lowercase">materi</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-10">
        <!-- Recent Activities Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden flex flex-col">
            <div class="p-6 md:p-8 border-b border-gray-100 flex justify-between items-center bg-white">
                <div>
                    <h3 class="font-black text-gray-800 text-xl">Pelatihan Sedang Berlangsung</h3>
                    <p class="text-sm text-gray-500 mt-1">Materi yang sedang berjalan hari ini.</p>
                </div>
                <a href="{{ route('training-non-reguler.daily-classes.index') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 bg-gray-50 text-gray-600 rounded-full text-sm font-bold hover:bg-gray-100 transition-colors">
                    Lihat Semua
                </a>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Materi & Kategori</th>
                        <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Tanggal</th>
                        <th class="px-8 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Trainer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentSesi as $sesi)
                        <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer" onclick="window.location.href='{{ route('training-non-reguler.daily-classes.show', $sesi->id) }}'">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0">
                                        {{ substr($sesi->title, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 group-hover:text-[#28844b] transition-colors">{{ $sesi->title }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $sesi->trainingProgram->name ?? 'Tanpa Program' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-semibold text-slate-600 whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @if($sesi->end_date && \Carbon\Carbon::parse($sesi->end_date)->format('Y-m-d') !== \Carbon\Carbon::parse($sesi->date)->format('Y-m-d'))
                                        {{ \Carbon\Carbon::parse($sesi->date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sesi->end_date)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($sesi->date)->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-medium text-slate-700">{{ $sesi->trainer_name ?: '-' }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-medium text-gray-500">Belum ada pelatihan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 bg-gray-50 text-center sm:hidden">
                <a href="{{ route('training-non-reguler.daily-classes.index') }}" class="text-[#28844b] text-sm font-bold">
                    Lihat Semua Materi
                </a>
            </div>
        </div>

        <!-- Quick Actions Sidebar -->
        <div class="flex flex-col gap-6 h-full">
            <!-- Action 1 -->
            <a href="{{ route('training-non-reguler.history.index') }}" class="group block relative overflow-hidden bg-[#28844b] rounded-3xl p-8 shadow-md hover:shadow-lg hover:shadow-green-900/20 hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 -mr-6 -mt-6 w-32 h-32 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
                
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="font-black text-white text-xl mb-2 leading-tight">Riwayat Pelatihan</h4>
                    <p class="text-green-100 text-sm font-medium">Lihat seluruh riwayat materi pelatihan yang sudah selesai.</p>
                </div>
                
                <div class="absolute bottom-8 right-8 text-white opacity-0 transform translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </a>

            <!-- Action 2 -->
            <a href="{{ route('training-non-reguler.daily-classes.index') }}" class="group block relative overflow-hidden bg-blue-600 rounded-3xl p-8 shadow-md hover:shadow-lg hover:shadow-blue-900/20 hover:-translate-y-1 transition-all duration-300">
                <div class="absolute top-0 right-0 -mr-6 -mt-6 w-32 h-32 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
                
                <div class="relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="font-black text-white text-xl mb-2 leading-tight">Kelola Materi</h4>
                    <p class="text-blue-100 text-sm font-medium">Buat materi baru, import peserta, dan kelola absensi harian.</p>
                </div>
                
                <div class="absolute bottom-8 right-8 text-white opacity-0 transform translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </a>
            
            <!-- Quick Help -->
            <div class="bg-amber-50/50 rounded-3xl p-6 border border-amber-100/50 mt-auto">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-1">Tips</h4>
                        <p class="text-sm text-gray-600 leading-snug">Pastikan untuk selalu menyiapkan dan membagikan QR Code Absensi kepada peserta di setiap sesi kelas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>









