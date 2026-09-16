<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} - FR Academy</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/faviconfr.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
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
                
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-[#509c38] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <!-- SECTION: Konten & Media -->
                <details class="group" {{ request()->routeIs('admin.artikel.*', 'admin.tv.*', 'admin.events.*') ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between rounded-lg px-4 py-3 mt-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:bg-gray-100 [&::-webkit-details-marker]:hidden transition-colors">
                        <span>Konten & Media</span>
                        <svg class="h-4 w-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="space-y-1 mt-1">
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
                    </div>
                </details>
                
                <!-- SECTION: Sistem & Pengaturan -->
                <details class="group" {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.profile.*') ? 'open' : '' }}>
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

                    <!-- Notification -->
                    <button class="relative p-2 text-gray-400 hover:text-gray-600 transition bg-gray-50 rounded-full border border-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

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
    
    @livewireScripts
</body>
</html>
