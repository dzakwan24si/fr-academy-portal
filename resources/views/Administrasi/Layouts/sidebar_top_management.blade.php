@php
    $tmUrl = function($routeName, $params = []) {
        $params['role'] = 'top_management';
        return route($routeName, $params);
    };
@endphp

<!-- SIDEBAR TOP MANAGEMENT PARTIAL -->
<aside class="w-64 bg-white border-r border-[#E5E7EB] shrink-0 hidden md:flex flex-col">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-center h-24 border-b border-[#E5E7EB] px-4 shrink-0">
        <img src="{{ asset('images/logofr.png') }}" alt="Logo" class="h-16 w-auto">
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-5">

        <!-- SECTION: Dashboard -->
        <details class="group"
            {{ request()->routeIs('dashboard', 'administrasi.dashboard-realisasi.*', 'administrasi.dashboard-monitoring-tahunan.*', 'Top_Management.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Dashboard</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <a href="{{ $tmUrl('administrasi.dashboard-realisasi.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.dashboard-realisasi.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="chart-column-increasing" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Utama</span>
                </a>
                <a href="{{ route('Top_Management.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->is('Top_Management*') || request()->routeIs('dashboard') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layout-grid" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard MR</span>
                </a>

                <a href="{{ $tmUrl('administrasi.dashboard-monitoring-tahunan.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.dashboard-monitoring-tahunan.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar-check" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Progress per Tahun</span>
                </a>
            </div>
        </details>

        <!-- SECTION: Program -->
        <details class="group"
            {{ request()->routeIs('administrasi.program.*', 'administrasi.program-batch.*', 'administrasi.schedule.*', 'administrasi.realisasi-vs-batch.*', 'administrasi.training-record.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Program</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <a href="{{ $tmUrl('administrasi.program.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.program.index', 'administrasi.program.show') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layers-3" class="w-4 h-4 shrink-0"></i><span>Program</span>
                </a>
                <a href="{{ $tmUrl('administrasi.realisasi-vs-batch.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.realisasi-vs-batch.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="chart-no-axes-combined" class="w-4 h-4 shrink-0"></i><span>Realisasi Program Vs Target</span>
                </a>
                <a href="{{ $tmUrl('administrasi.schedule.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.schedule.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar" class="w-4 h-4 shrink-0"></i><span>Schedule</span>
                </a>
                <a href="{{ $tmUrl('administrasi.program-batch.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.program-batch.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0"></i><span>Program / Batch</span>
                </a>
                <a href="{{ $tmUrl('administrasi.training-record.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.training-record.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="users-round" class="w-4 h-4 shrink-0"></i><span>Training Record</span>
                </a>
            </div>
        </details>

        <!-- SECTION: Monitoring -->
        <details class="group"
            {{ request()->routeIs('administrasi.monitoring-summarized.*', 'administrasi.monitoring-alumni.*', 'administrasi.monitoring-lulusan.*', 'administrasi.asisten.*', 'administrasi.monitoring.*', 'administrasi.monitoring-talent.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Monitoring</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">

                <!-- 1. Monitoring Summarized -->
                <a href="{{ $tmUrl('administrasi.monitoring-summarized.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-summarized.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Summarized</span>
                </a>

                <!-- 2. Monitoring Alumni -->
                <a href="{{ $tmUrl('administrasi.monitoring-alumni.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-alumni.*', 'administrasi.monitoring-lulusan.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Alumni</span>
                </a>

                <!-- 3. Database Asisten -->
                <a href="{{ $tmUrl('administrasi.asisten.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.asisten.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="contact" class="w-4 h-4 shrink-0"></i>
                    <span>Database Asisten</span>
                </a>

                <!-- 4. Monitoring Talent -->
                <a href="{{ $tmUrl('administrasi.monitoring-talent.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-talent.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="sparkles" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Talent</span>
                </a>

                <!-- 5. Monitoring Detailed -->
                <a href="{{ $tmUrl('administrasi.monitoring.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="line-chart" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Detailed</span>
                </a>
            </div>
        </details>

        <!-- SECTION: Data Aset -->
        <details class="group" {{ request()->routeIs('administrasi.inventaris.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Data Aset</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <!-- Dashboard Inventaris Saja -->
                <a href="{{ $tmUrl('administrasi.inventaris.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="boxes" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Inventaris</span>
                </a>
            </div>
        </details>

        <!-- SECTION: Sistem & Pengaturan -->
        <details class="group" {{ request()->routeIs('administrasi.activity-logs.*', 'admin.dashboard') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Sistem & Pengaturan</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                @canany(['manage_articles', 'manage_users', 'manage_tv_display', 'manage_events'])
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('admin.dashboard') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Overview</span>
                </a>
                @endcanany
                <a href="{{ route('administrasi.activity-logs.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.activity-logs.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="history" class="w-4 h-4 shrink-0"></i>
                    <span>Histori Aktivitas</span>
                </a>
            </div>
        </details>

    </nav>

    <!-- Sidebar Footer -->
    <div class="px-3 pb-4 border-t border-[#E5E7EB] pt-3 space-y-0.5">
        <a href="#"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-500 hover:bg-red-50 hover:text-red-600 font-medium transition-colors">
            <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
            <span>Logout</span>
        </a>
        <p class="text-center text-[10px] text-[#A1A1AA] pt-2">&copy; FR Academy Top Management {{ date('Y') }}</p>
    </div>
</aside>
