<!-- SIDEBAR PARTIAL -->
<aside class="w-64 bg-white border-r border-[#E5E7EB] shrink-0 hidden md:flex flex-col">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-center h-24 border-b border-[#E5E7EB] px-4 shrink-0">
        <img src="{{ asset('images/logofr.png') }}" alt="Logo" class="h-16 w-auto">
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-5">

        <!-- SECTION: Dashboard -->
        @canany(['view_dashboard', 'edit_dashboard'])
        <details class="group"
            {{ request()->routeIs('dashboard', 'administrasi.dashboard-realisasi.*', 'administrasi.dashboard-monitoring-tahunan.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Dashboard</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <a href="{{ route('administrasi.dashboard-realisasi.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.dashboard-realisasi.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="chart-column-increasing" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Utama</span>
                </a>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('dashboard') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layout-grid" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard MR</span>
                </a>

                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.dashboard-monitoring-tahunan.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar-check" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Monitoring Tahunan</span>
                </a>

                @hasanyrole(['Top Management', 'Superadmin'])
                <a href="{{ route('Top_Management.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->is('Top_Management*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="crown" class="w-4 h-4 shrink-0 text-amber-500"></i>
                    <span>Portal Eksekutif</span>
                </a>
                @endhasanyrole
            </div>
        </details>
        @endcanany

        <!-- SECTION: Program -->
        @canany(['view_program', 'edit_program'])
        <details class="group"
            {{ request()->routeIs('administrasi.program.*', 'administrasi.program-batch.*', 'administrasi.schedule.*', 'administrasi.realisasi-vs-batch.*', 'administrasi.training-record.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Program</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <a href="{{ route('administrasi.program.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.program.index', 'administrasi.program.show') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layers-3" class="w-4 h-4 shrink-0"></i><span>Program</span>
                </a>
                <a href="{{ route('administrasi.realisasi-vs-batch.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.realisasi-vs-batch.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="chart-no-axes-combined" class="w-4 h-4 shrink-0"></i><span>Realisasi Program Vs Target</span>
                </a>
                <a href="{{ route('administrasi.schedule.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.schedule.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar" class="w-4 h-4 shrink-0"></i><span>Schedule</span>
                </a>
                <a href="{{ route('administrasi.program-batch.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.program-batch.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0"></i><span>Program / Batch</span>
                </a>
                <a href="{{ route('administrasi.training-record.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.training-record.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="users-round" class="w-4 h-4 shrink-0"></i><span>Training Record</span>
                </a>
            </div>
        </details>
        @endcanany

        <!-- SECTION: Monitoring -->
        @canany(['view_monitoring', 'edit_monitoring'])
        <details class="group"
            {{ request()->routeIs('administrasi.monitoring-summarized.*', 'administrasi.monitoring-alumni.*', 'administrasi.monitoring-lulusan.*', 'administrasi.asisten.*', 'administrasi.monitoring.*', 'administrasi.monitoring-talent.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Monitoring</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">

                <!-- 1. Monitoring Summarized -->
                <a href="{{ route('administrasi.monitoring-summarized.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-summarized.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Summarized</span>
                </a>

                <!-- 2. Monitoring Alumni -->
                <a href="{{ route('administrasi.monitoring-alumni.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-alumni.*', 'administrasi.monitoring-lulusan.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Alumni</span>
                </a>

                <!-- 3. Database Asisten -->
                <a href="{{ route('administrasi.asisten.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.asisten.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="contact" class="w-4 h-4 shrink-0"></i>
                    <span>Database Asisten</span>
                </a>

                <!-- 4. Monitoring Detailed (ganti nama dari Monitoring) -->
                <a href="{{ route('administrasi.monitoring.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="line-chart" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Detailed</span>
                </a>
            </div>
        </details>
        @endcanany

        <!-- SECTION: Talent -->
        @canany(['view_talent', 'edit_talent'])
        <details class="group"
            {{ request()->routeIs('administrasi.monitoring-talent.*', 'administrasi.talent-pool.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Talent</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <!-- 1. Monitoring Talent -->
                <a href="{{ route('administrasi.monitoring-talent.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-talent.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="sparkles" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Talent</span>
                </a>

                <!-- 2. Talent Pool -->
                <a href="{{ route('administrasi.talent-pool.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.talent-pool.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="users-round" class="w-4 h-4 shrink-0"></i>
                    <span>Talent Pool</span>
                </a>
            </div>
        </details>
        @endcanany

        <!-- SECTION: Administration -->
        @canany(['view_administrasi', 'edit_administrasi', 'access_administrasi'])
        <details class="group"
            {{ request()->routeIs('administrasi.rekrutmen.*', 'administrasi.asisten.*', 'administrasi.cost.*', 'administrasi.vra.*', 'administrasi.biaya-rekruit.*', 'administrasi.capex.*', 'administrasi.cuti.*', 'administrasi.mpp.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Administration</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">

                <!-- Rekrutmen Karyawan -->
                <a href="{{ route('administrasi.rekrutmen.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.rekrutmen.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="user-plus" class="w-4 h-4 shrink-0"></i>
                    <span>Rekrutmen</span>
                </a>

                <!-- Sub-Modul Cost & Finansial (Hanya tampil jika memiliki permission view_cost) -->
                @canany(['view_cost', 'edit_cost'])
                <!-- Cost -->
                <a href="{{ route('administrasi.cost.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.cost.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="banknote" class="w-4 h-4 shrink-0"></i>
                    <span>Cost</span>
                </a>

                <!-- VRA -->
                <a href="{{ route('administrasi.vra.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.vra.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="truck" class="w-4 h-4 shrink-0"></i>
                    <span>VRA</span>
                </a>

                <!-- Biaya Rekruit -->
                <a href="{{ route('administrasi.biaya-rekruit.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.biaya-rekruit.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="user-plus" class="w-4 h-4 shrink-0"></i>
                    <span>Biaya Rekruit</span>
                </a>

                <!-- CAPEX -->
                <a href="{{ route('administrasi.capex.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.capex.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="landmark" class="w-4 h-4 shrink-0"></i>
                    <span>CAPEX</span>
                </a>
                @endcanany

                <!-- Cuti -->
                <a href="{{ route('administrasi.cuti.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.cuti.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar-check-2" class="w-4 h-4 shrink-0"></i>
                    <span>Cuti</span>
                </a>

                <!-- MPP -->
                <a href="{{ route('administrasi.mpp.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.mpp.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                    <span>MPP</span>
                </a>

            </div>
        </details>
        @endcanany

        <!-- SECTION: Data Aset -->
        @canany(['view_data_aset', 'edit_data_aset'])
        <details class="group" {{ request()->routeIs('administrasi.inventaris.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Data Aset</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <!-- Dashboard Inventaris -->
                <a href="{{ route('administrasi.inventaris.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.dashboard') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Inventaris</span>
                </a>

                <!-- Gudang & Kantor -->
                <a href="{{ route('administrasi.inventaris.barang') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.barang') || request()->routeIs('administrasi.inventaris.pembelian') || request()->routeIs('administrasi.inventaris.stock-opname*') || request()->routeIs('administrasi.inventaris.gudang.*') || request()->routeIs('administrasi.inventaris.kantor.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="boxes" class="w-4 h-4 shrink-0"></i>
                    <span>Gudang & Kantor</span>
                </a>
            </div>
        </details>
        @endcanany

        <!-- SECTION: Kompetensi -->
        @can('manage_competency')
        <details class="group" {{ request()->routeIs('admin.competency.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Kompetensi</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="space-y-0.5">
                <a href="{{ route('admin.competency.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('admin.competency.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="award" class="w-4 h-4 shrink-0"></i>
                    <span>Kelola Kamus Kompetensi</span>
                </a>
                <a href="{{ route('competency.public') }}" target="_blank"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium">
                    <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i>
                    <span>Lihat Halaman Publik</span>
                </a>
            </div>
        </details>
        @endcan

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

    <!-- Sidebar Footer: Settings + Logout -->
    <div class="px-3 pb-4 border-t border-[#E5E7EB] pt-3 space-y-0.5">
        <a href="#"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium transition-colors">
            <i data-lucide="settings" class="w-4 h-4 shrink-0"></i>
            <span>Settings</span>
        </a>
        <a href="#"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-500 hover:bg-red-50 hover:text-red-600 font-medium transition-colors">
            <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
            <span>Logout</span>
        </a>
        <p class="text-center text-[10px] text-[#A1A1AA] pt-2">v{{ app()->version() }} &copy; FR Academy
            {{ date('Y') }}</p>
    </div>
</aside>
