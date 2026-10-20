<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Dashboard' }} - FR Academy</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/faviconfr.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('styles')
    <style>
        /* Fullscreen Mode Layout Rules */
        :fullscreen aside,
        :fullscreen header,
        body.is-fullscreen-mode aside,
        body.is-fullscreen-mode header,
        html.is-fullscreen-mode aside,
        html.is-fullscreen-mode header {
            display: none !important;
        }

        :fullscreen main,
        body.is-fullscreen-mode main,
        html.is-fullscreen-mode main {
            padding: 1.25rem 2rem !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: 100% !important;
            overflow-y: auto !important;
            background-color: #f8fafc !important;
        }

        /* Floating Exit Fullscreen Button */
        .floating-exit-fullscreen-btn {
            display: none;
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 99999;
        }

        :fullscreen .floating-exit-fullscreen-btn,
        body.is-fullscreen-mode .floating-exit-fullscreen-btn,
        html.is-fullscreen-mode .floating-exit-fullscreen-btn {
            display: flex !important;
        }
    </style>
    <script>
        if (sessionStorage.getItem('dashboard_fullscreen') === 'true') {
            document.documentElement.classList.add('is-fullscreen-mode');
        }
    </script>
</head>
<body class="bg-white font-sans antialiased text-gray-900">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-white">
        
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden" style="display: none;">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-50 border-r border-gray-200 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-center h-24 border-b border-gray-200 px-4">
                <img src="{{ asset('images/logofr.png') }}" alt="Logo" class="h-16 w-auto">
            </div>
            
            <!-- Sidebar Menu -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
                
<!-- SECTION: Dashboard -->
        <details class="group"
            {{ request()->routeIs('dashboard', 'administrasi.dashboard-realisasi.*', 'administrasi.dashboard-monitoring-tahunan.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Dashboard</span>
                <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="space-y-0.5">
                <a href="{{ route('administrasi.dashboard-realisasi.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.dashboard-realisasi.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="chart-column-increasing" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Utama</span>
                </a>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('dashboard') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layout-grid" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard MR</span>
                </a>

                <a href="{{ route('administrasi.dashboard-monitoring-tahunan.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.dashboard-monitoring-tahunan.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar-check" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Progress Tahunan</span>
                </a>
            </div>
        </details>

<!-- SECTION: Program -->
        <details class="group"
            {{ request()->routeIs('administrasi.program-batch.*', 'administrasi.schedule.*', 'administrasi.realisasi-vs-batch.*', 'administrasi.training-record.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Program</span>
                <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="space-y-0.5">
                <a href="{{ route('administrasi.program-batch.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.program-batch.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layers-3" class="w-4 h-4 shrink-0"></i><span>Program</span>
                </a>
                <a href="{{ route('administrasi.schedule.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.schedule.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar" class="w-4 h-4 shrink-0"></i><span>Kalender</span>
                </a>
                <a href="{{ route('administrasi.training-record.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.training-record.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="award" class="w-4 h-4 shrink-0"></i><span>Training Record</span>
                </a>
                <a href="{{ route('administrasi.realisasi-vs-batch.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors {{ request()->routeIs('administrasi.realisasi-vs-batch.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="chart-no-axes-combined" class="w-4 h-4 shrink-0"></i><span>Realisasi Batch</span>
                </a>
            </div>
        </details>

<!-- SECTION: Monitoring -->
        <details class="group"
            {{ request()->routeIs('administrasi.monitoring-summarized.*', 'administrasi.monitoring-alumni.*', 'administrasi.monitoring-lulusan.*', 'administrasi.asisten.*', 'administrasi.monitoring.*', 'administrasi.monitoring-talent.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Monitoring</span>
                <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="space-y-0.5">

                <!-- 1. Monitoring Summarized -->
                <a href="{{ route('administrasi.monitoring-summarized.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-summarized.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Summarized</span>
                </a>

                <!-- 2. Monitoring Alumni -->
                <a href="{{ route('administrasi.monitoring-alumni.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-alumni.*', 'administrasi.monitoring-lulusan.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="graduation-cap" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Alumni</span>
                </a>

                <!-- 3. Database Asisten -->
                <a href="{{ route('administrasi.asisten.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.asisten.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="contact" class="w-4 h-4 shrink-0"></i>
                    <span>Database Asisten</span>
                </a>

                <!-- 4. Monitoring Talent -->
                <a href="{{ route('administrasi.monitoring-talent.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring-talent.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="sparkles" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Talent</span>
                </a>

                <!-- 5. Monitoring Employee -->
                <a href="{{ route('administrasi.monitoring.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.monitoring.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="line-chart" class="w-4 h-4 shrink-0"></i>
                    <span>Monitoring Employee</span>
                </a>
                
                <!-- 6. Staf Resign -->
                <a href="{{ route('administrasi.staf-resign.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.staf-resign.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="user-x" class="w-4 h-4 shrink-0"></i>
                    <span>Staf Resign</span>
                </a>
            </div>
        </details>

<!-- SECTION: Administration -->
        <details class="group"
            {{ request()->routeIs('administrasi.rekrutmen.*', 'administrasi.asisten.*', 'administrasi.cost.*', 'administrasi.vra.*', 'administrasi.biaya-rekruit.*', 'administrasi.capex.*', 'administrasi.cuti.*', 'administrasi.mpp.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Administration</span>
                <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="space-y-0.5">

                <!-- 1. Rekrutmen -->
                <a href="{{ route('administrasi.rekrutmen.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.rekrutmen.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="user-plus" class="w-4 h-4 shrink-0"></i>
                    <span>Rekrutmen</span>
                </a>

                <!-- 2. Biaya Rekrut -->
                <a href="{{ route('administrasi.biaya-rekruit.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.biaya-rekruit.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="user-plus" class="w-4 h-4 shrink-0"></i>
                    <span>Biaya Rekrut</span>
                </a>

                <!-- 3. CAPEX -->
                <a href="{{ route('administrasi.capex.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.capex.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="landmark" class="w-4 h-4 shrink-0"></i>
                    <span>CAPEX</span>
                </a>

                <!-- 4. VRA -->
                <a href="{{ route('administrasi.vra.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.vra.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="truck" class="w-4 h-4 shrink-0"></i>
                    <span>VRA</span>
                </a>

                <!-- 5. Cost -->
                <a href="{{ route('administrasi.cost.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.cost.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="banknote" class="w-4 h-4 shrink-0"></i>
                    <span>Cost</span>
                </a>

                <!-- 6. Cuti -->
                <a href="{{ route('administrasi.cuti.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.cuti.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="calendar-check-2" class="w-4 h-4 shrink-0"></i>
                    <span>Cuti</span>
                </a>

                <!-- 7. MPP -->
                <a href="{{ route('administrasi.mpp.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.mpp.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                    <span>MPP</span>
                </a>

            </div>
        </details>

<!-- SECTION: Data Aset -->
        <details class="group" {{ request()->routeIs('administrasi.inventaris.*') ? 'open' : '' }}>
            <summary
                class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-widest text-[#A1A1AA] hover:bg-[#F9FAFB] [&::-webkit-details-marker]:hidden">
                <span>Data Aset</span>
                <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-180 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="space-y-0.5">
                <!-- Dashboard Inventaris -->
                <a href="{{ route('administrasi.inventaris.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.dashboard') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                    <span>Dashboard Inventaris</span>
                </a>

                <!-- Gudang & Kantor -->
                <a href="{{ route('administrasi.inventaris.barang') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm transition-colors
                          {{ request()->routeIs('administrasi.inventaris.barang') || request()->routeIs('administrasi.inventaris.pembelian') || request()->routeIs('administrasi.inventaris.stock-opname*') || request()->routeIs('administrasi.inventaris.gudang.*') || request()->routeIs('administrasi.inventaris.kantor.*') ? 'bg-[#509c38] text-white font-semibold shadow-sm' : 'text-[#52525B] hover:bg-[#F4F4F5] hover:text-[#18181B] font-medium' }}">
                    <i data-lucide="boxes" class="w-4 h-4 shrink-0"></i>
                    <span>Gudang & Kantor</span>
                </a>
            </div>
        </details>



                @canany(['manage_articles', 'manage_tv_display', 'manage_events', 'manage_career'])
                <!-- SECTION: Konten & Media -->
                <details class="group" {{ request()->routeIs('admin.artikel.*', 'admin.tv.*', 'admin.events.*', 'admin.career_programs.*', 'admin.page_settings.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:bg-gray-100 [&::-webkit-details-marker]:hidden transition-colors">
                        <span>Konten & Media</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="space-y-1 mt-1">
                        <a href="{{ route('admin.page_settings.index') }}" class="{{ request()->routeIs('admin.page_settings.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <i data-lucide="layout-template" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.page_settings.*') ? 'text-white' : 'text-gray-400' }}"></i>
                            Konten Halaman
                        </a>

                        @can('manage_articles')
                        <a href="{{ route('admin.artikel.index') }}" class="{{ request()->routeIs('admin.artikel.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.artikel.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            Manajemen Artikel
                        </a>
                        @endcan
                        
                        @can('manage_tv_display')
                        <a href="{{ route('admin.tv.index') }}" class="{{ request()->routeIs('admin.tv.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.tv.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Playlist TV
                        </a>
                        @endcan
                        
                        @can('manage_events')
                        <a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.events.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Manajemen Tema
                        </a>
                        @endcan

                        @can('manage_career')
                        <a href="{{ route('admin.career_programs.index') }}" class="{{ request()->routeIs('admin.career_programs.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <i data-lucide="briefcase" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.career_programs.*') ? 'text-white' : 'text-gray-400' }}"></i>
                            Manajemen Karir
                        </a>
                        @endcan
                    </div>
                </details>
                @endcanany
                
                <!-- SECTION: Learning Activity (Non Reguler) -->
                @if(auth()->user()->can('access_learning_activity') || auth()->user()->hasRole(['Superadmin', 'Trainer']))
                <details class="group" {{ request()->routeIs('training-non-reguler.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:bg-gray-100 [&::-webkit-details-marker]:hidden transition-colors">
                        <span>Learning Activity</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="space-y-1 mt-1">
                        <a href="{{ route('training-non-reguler.dashboard') }}" class="{{ request()->routeIs('training-non-reguler.dashboard') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3 {{ request()->routeIs('training-non-reguler.dashboard') ? 'text-white' : 'text-gray-400' }}"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('training-non-reguler.daily-classes.index') }}" class="{{ request()->routeIs('training-non-reguler.daily-classes.*') || request()->routeIs('training-non-reguler.tasks.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <i data-lucide="book-open" class="w-5 h-5 mr-3 {{ request()->routeIs('training-non-reguler.daily-classes.*') || request()->routeIs('training-non-reguler.tasks.*') ? 'text-white' : 'text-gray-400' }}"></i>
                            Materi Pelatihan
                        </a>
                    </div>
                </details>
                @endif

                <!-- SECTION: Standar Kompetensi -->
                @can('manage_competency')
                <details class="group" {{ request()->routeIs('admin.competency.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:bg-gray-100 [&::-webkit-details-marker]:hidden transition-colors">
                        <span>Standar Kompetensi</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="space-y-1 mt-1">
                        <a href="{{ route('admin.competency.index') }}" class="{{ request()->routeIs('admin.competency.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <i data-lucide="award" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.competency.*') ? 'text-white' : 'text-gray-400' }}"></i>
                            Kelola Kamus Kompetensi
                        </a>
                        <a href="{{ route('competency.public') }}" target="_blank" class="text-gray-600 hover:bg-gray-100 flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <i data-lucide="external-link" class="w-5 h-5 mr-3 text-gray-400"></i>
                            Lihat Halaman Publik
                        </a>
                    </div>
                </details>
                @endcan

                <!-- SECTION: Sistem & Pengaturan -->
                <details class="group" {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.profile.*', 'administrasi.activity-logs.*', 'admin.dashboard') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:bg-gray-100 [&::-webkit-details-marker]:hidden transition-colors">
                        <span>Sistem & Pengaturan</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="space-y-1 mt-1">


                        @can('manage_users')
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Manajemen User
                        </a>
                        @endcan

                        @can('manage_roles')
                        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.roles.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Manajemen Role
                        </a>
                        @endcan

                        <a href="{{ route('administrasi.activity-logs.index') }}" class="{{ request()->routeIs('administrasi.activity-logs.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <i data-lucide="history" class="w-5 h-5 mr-3 {{ request()->routeIs('administrasi.activity-logs.*') ? 'text-white' : 'text-gray-400' }}"></i>
                            Histori Aktivitas
                        </a>

                        <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.profile.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Akun Saya
                        </a>
                    </div>
                </details>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 h-24 flex items-center justify-between px-8 z-10">
                <div class="flex items-center flex-1">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    
                    <!-- Breadcrumbs -->
                    <div class="hidden md:flex items-center text-sm text-gray-500">
                        <span class="hover:text-gray-800 cursor-pointer">Home</span>
                        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="hover:text-gray-800 cursor-pointer">Admin</span>
                        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-[#509c38] font-medium">{{ $title ?? 'Dashboard' }}</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-6">
                    <!-- Live Time -->
                    <div x-data="{ 
                            time: '',
                            updateTime() {
                                const now = new Date();
                                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                                const dateStr = now.toLocaleDateString('id-ID', options);
                                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                                this.time = `${dateStr} • ${timeStr}`;
                            }
                        }" 
                        x-init="updateTime(); setInterval(() => updateTime(), 1000);" 
                        class="hidden lg:flex items-center bg-gray-50 rounded-full px-5 py-2 border border-gray-200">
                        <svg class="w-4 h-4 text-[#509c38] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="time" class="text-sm font-semibold text-gray-700 whitespace-nowrap">Memuat Waktu...</span>
                    </div>

                    <!-- Notification Dropdown (Activity History) -->
                    @php
                        $recentActivityLogs = \App\Models\ActivityLog::latest()->take(5)->get();
                        $todayActivityCount = \App\Models\ActivityLog::whereDate('created_at', \Carbon\Carbon::today())->count();
                    @endphp
                    <div x-data="{ 
                            notifOpen: false,
                            isRead: localStorage.getItem('notif_read_date') === new Date().toDateString(),
                            markAsRead() {
                                this.isRead = true;
                                localStorage.setItem('notif_read_date', new Date().toDateString());
                            }
                        }" 
                        class="relative">
                        <button @click="notifOpen = !notifOpen" 
                                title="Histori Aktivitas"
                                class="relative p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition bg-gray-50 rounded-full border border-gray-200 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if($todayActivityCount > 0)
                                <span x-show="!isRead" 
                                      x-transition:leave="transition ease-in duration-150"
                                      x-transition:leave-start="opacity-100 scale-100"
                                      x-transition:leave-end="opacity-0 scale-50"
                                      class="absolute -top-0.5 -right-0.5 flex h-4.5 w-4.5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white">
                                    {{ $todayActivityCount > 99 ? '99+' : $todayActivityCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div x-show="notifOpen" 
                             @click.away="notifOpen = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 transform -translate-y-2"
                             class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50 overflow-hidden" 
                             style="display: none;">
                            
                            <!-- Dropdown Header -->
                            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/80">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-gray-900">Histori Aktivitas</h3>
                                    @if($todayActivityCount > 0)
                                        <span x-show="!isRead" 
                                              x-transition
                                              class="px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">
                                            {{ $todayActivityCount }} hari ini
                                        </span>
                                    @endif
                                </div>
                                <button type="button" 
                                        @click="markAsRead()" 
                                        x-show="!isRead" 
                                        class="text-xs font-semibold text-[#509c38] hover:text-[#3d792a] hover:underline transition-colors focus:outline-none">
                                    Tandai telah dibaca
                                </button>
                                <span x-show="isRead" class="text-xs font-medium text-gray-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Telah dibaca
                                </span>
                            </div>

                            <!-- Dropdown Body / Activity List -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                @forelse($recentActivityLogs as $log)
                                    <a href="{{ route('administrasi.activity-logs.index') }}" class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50/80 transition group">
                                        <div class="shrink-0 mt-0.5">
                                            @if($log->action === 'Tambah')
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </span>
                                            @elseif($log->action === 'Edit')
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </span>
                                            @elseif($log->action === 'Hapus')
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 text-rose-600 ring-1 ring-rose-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </span>
                                            @elseif($log->action === 'Import')
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-50 text-purple-600 ring-1 ring-purple-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-600 ring-1 ring-gray-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs font-bold text-gray-900 truncate group-hover:text-[#509c38] transition-colors">
                                                    {{ $log->module ?? 'Sistem' }}
                                                </p>
                                                <span class="text-[10px] font-medium text-gray-400 whitespace-nowrap">
                                                    {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-2 leading-snug">
                                                {{ $log->description }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 mt-1">
                                                Oleh: <span class="font-medium text-gray-600">{{ $log->user_name ?? 'Sistem' }}</span>
                                            </p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-5 py-8 text-center text-gray-400">
                                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-xs font-medium">Belum ada histori aktivitas.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Dropdown Footer -->
                            <a href="{{ route('administrasi.activity-logs.index') }}" 
                               class="block text-center py-2.5 bg-gray-50 hover:bg-gray-100 text-xs font-bold text-[#509c38] transition-colors border-t border-gray-100">
                                Lihat Selengkapnya &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-3 focus:outline-none rounded-full py-1 pl-1 pr-3 hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                            <img class="w-9 h-9 rounded-full object-cover border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=f3f4f6&color=374151" alt="Avatar">
                            <div class="text-left hidden md:block">
                                <p class="text-sm font-bold text-gray-800 leading-tight">{{ auth()->user()->name ?? 'Admin User' }}</p>
                                <p class="text-xs text-gray-500 leading-tight">{{ auth()->check() && auth()->user()->roles->count() > 0 ? auth()->user()->roles->pluck('name')->join(', ') : 'User Biasa' }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-1 border border-gray-100 z-50">
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 font-medium">Log Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-white p-8">
                <!-- Page Title Area (Matches concept) -->
                @if(isset($header) || isset($headerAction))
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $header ?? '' }}</h2>
                        @if(isset($headerSub))
                        <p class="text-sm text-gray-500 mt-1">{{ $headerSub }}</p>
                        @endif
                    </div>
                    @if(isset($headerAction))
                    <div>
                        {{ $headerAction }}
                    </div>
                    @endif
                </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
    
    <!-- Floating Exit Fullscreen Button -->
    <button type="button" onclick="toggleDashboardFullscreen()" class="floating-exit-fullscreen-btn items-center gap-2 px-4 py-2.5 bg-gray-900/90 hover:bg-gray-900 text-white rounded-full shadow-2xl text-xs font-bold transition-all border border-gray-700 backdrop-blur-md cursor-pointer">
        <i data-lucide="minimize-2" class="w-4 h-4"></i>
        <span>Keluar Fullscreen</span>
    </button>
    
    @livewireScripts
    <script>
        function toggleDashboardFullscreen() {
            const isCurrentlyFS = !!document.fullscreenElement || document.body.classList.contains('is-fullscreen-mode') || document.documentElement.classList.contains('is-fullscreen-mode');

            if (!isCurrentlyFS) {
                const docEl = document.documentElement;
                if (docEl.requestFullscreen) {
                    docEl.requestFullscreen().catch(() => {});
                } else if (docEl.webkitRequestFullscreen) {
                    docEl.webkitRequestFullscreen();
                } else if (docEl.msRequestFullscreen) {
                    docEl.msRequestFullscreen();
                }
                document.body.classList.add('is-fullscreen-mode');
                document.documentElement.classList.add('is-fullscreen-mode');
                sessionStorage.setItem('dashboard_fullscreen', 'true');
                updateFullscreenButtons(true);
            } else {
                if (document.fullscreenElement) {
                    if (document.exitFullscreen) {
                        document.exitFullscreen().catch(() => {});
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                }
                document.body.classList.remove('is-fullscreen-mode');
                document.documentElement.classList.remove('is-fullscreen-mode');
                sessionStorage.setItem('dashboard_fullscreen', 'false');
                updateFullscreenButtons(false);
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const isFS = !!document.fullscreenElement;
            if (isFS) {
                document.body.classList.add('is-fullscreen-mode');
                document.documentElement.classList.add('is-fullscreen-mode');
                sessionStorage.setItem('dashboard_fullscreen', 'true');
            } else {
                // If user exits native fullscreen (e.g. Esc key)
                document.body.classList.remove('is-fullscreen-mode');
                document.documentElement.classList.remove('is-fullscreen-mode');
                sessionStorage.setItem('dashboard_fullscreen', 'false');
            }
            updateFullscreenButtons(isFS);
        });

        function updateFullscreenButtons(isFS) {
            document.querySelectorAll('.btn-toggle-fullscreen').forEach(btn => {
                const textEl = btn.querySelector('.fullscreen-btn-text');
                const iconEl = btn.querySelector('.fullscreen-btn-icon');
                if (isFS) {
                    if (textEl) textEl.textContent = 'Keluar Fullscreen';
                    if (iconEl) iconEl.setAttribute('data-lucide', 'minimize-2');
                } else {
                    if (textEl) textEl.textContent = 'Full Screen';
                    if (iconEl) iconEl.setAttribute('data-lucide', 'maximize-2');
                }
            });
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isFS = sessionStorage.getItem('dashboard_fullscreen') === 'true';
            if (isFS) {
                document.body.classList.add('is-fullscreen-mode');
                document.documentElement.classList.add('is-fullscreen-mode');
                updateFullscreenButtons(true);
                if (document.documentElement.requestFullscreen && !document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                }
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
