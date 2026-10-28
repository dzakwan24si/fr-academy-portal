<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal resmi First Resources Academy. Pusat informasi pendaftaran, pengumuman, dan lowongan kerja program MAT dan FAT.">
    <title>First Resources Academy - Portal Resmi</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/faviconfr.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 antialiased flex flex-col min-h-screen transition-colors duration-300 overflow-x-hidden">
    @php
        $active_event = \App\Models\HomepageEvent::where('is_active', true)->first();
    @endphp

    <!-- Event Overlay -->
    @if(isset($active_event) && $active_event && $active_event->image_path)
        <div class="fixed inset-0 z-[100] pointer-events-none flex items-center justify-center">
            <!-- Asumsi gambar frame beresolusi tinggi dengan tengah bolong -->
            <img src="{{ asset($active_event->image_path) }}" alt="{{ $active_event->name }}" class="w-full h-full object-cover">
        </div>
    @endif
    
    <!-- Navigation -->
    <header x-data="{ scrolled: false, mobileMenuOpen: false }" 
            @scroll.window="scrolled = (window.pageYOffset > 20)"
            :class="scrolled ? 'bg-teal-primary shadow-lg' : 'bg-teal-primary lg:bg-transparent'" 
            class="fixed w-full z-50 transition-all duration-300">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-6 xl:px-8">
            <div class="flex items-center justify-between h-20 gap-2 xl:gap-4 w-full">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="flex items-center gap-2 xl:gap-2.5 group">
                        <img src="{{ asset('images/logofr.png') }}" alt="FR Academy Logo" class="h-8 xl:h-10 w-auto border border-white/50 rounded-none shrink-0 group-hover:scale-105 transition-transform">
                        <span class="text-white font-bold text-sm sm:text-base xl:text-xl tracking-tight hidden sm:block whitespace-nowrap">FR ACADEMY</span>
                    </a>
                </div>

                <!-- Desktop Menu (Spacious, Elegant, Well-balanced) -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-2 2xl:gap-4 mx-auto px-1 xl:px-2 shrink-0">
                    <a href="/" class="text-white/90 hover:text-green-accent px-2 xl:px-2.5 2xl:px-3 py-1.5 text-xs 2xl:text-sm font-semibold tracking-normal 2xl:tracking-wider transition whitespace-nowrap rounded-xl hover:bg-white/10">BERANDA</a>
                    <a href="/tentang-kami" class="text-white/90 hover:text-green-accent px-2 xl:px-2.5 2xl:px-3 py-1.5 text-xs 2xl:text-sm font-semibold tracking-normal 2xl:tracking-wider transition whitespace-nowrap rounded-xl hover:bg-white/10">TENTANG</a>
                    <!-- PROGRAM Dropdown with Flyout Sub-menus (Reguler & Non-Reguler) -->
                    <div x-data="{ open: false, subOpen: null }" class="relative flex items-center" @mouseenter="open = true" @mouseleave="open = false; subOpen = null">
                        <button type="button" class="text-white/90 hover:text-green-accent px-2 xl:px-2.5 2xl:px-3 py-1.5 text-xs 2xl:text-sm font-semibold tracking-normal 2xl:tracking-wider transition flex items-center whitespace-nowrap rounded-xl hover:bg-white/10 group focus:outline-none cursor-default">
                            PROGRAM
                            <svg class="w-3.5 h-3.5 ml-1 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <!-- Main Dropdown -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute top-full left-0 mt-1.5 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl py-2 border border-gray-100 dark:border-gray-700 z-50 text-left" 
                             style="display: none;">

                            @php
                                $headerProgramMenus = \App\Models\AppSetting::getProgramNavMenus(true);
                            @endphp
                            @foreach($headerProgramMenus as $pMenu)
                                @if(!empty($pMenu['has_submenu']) && !empty($pMenu['submenus']))
                                    <div class="relative" @mouseenter="subOpen = '{{ $pMenu['key'] }}'" @mouseleave="if(subOpen === '{{ $pMenu['key'] }}') subOpen = null">
                                        <div class="flex items-center justify-between px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-black dark:hover:text-white transition cursor-pointer mx-1.5 rounded-lg"
                                             :class="{ 'bg-gray-100 dark:bg-gray-700 text-black dark:text-white': subOpen === '{{ $pMenu['key'] }}' }">
                                            <span>{{ $pMenu['name'] }}</span>
                                            <span class="text-[10px] text-gray-600 dark:text-gray-400 ml-2">▶</span>
                                        </div>
                                        <!-- Flyout Submenu -->
                                        <div x-show="subOpen === '{{ $pMenu['key'] }}'" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-x-1"
                                             x-transition:enter-end="opacity-100 translate-x-0"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-x-0"
                                             x-transition:leave-end="opacity-0 translate-x-1"
                                             class="absolute top-0 left-full -ml-1 pl-2 w-72 z-50"
                                             style="display: none;">
                                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl py-2 border border-gray-100 dark:border-gray-700 max-h-[85vh] overflow-y-auto">
                                                @foreach($pMenu['submenus'] as $sub)
                                                    <a href="{{ $sub['url'] }}" class="block px-4 py-2 text-sm text-gray-900 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-black dark:hover:text-white transition mx-1 rounded-md">
                                                        {{ $sub['name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $pMenu['url'] ?? '#' }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-black dark:hover:text-white transition mx-1.5 rounded-lg">
                                        <span>{{ $pMenu['name'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('competency.public') }}" class="text-white/90 hover:text-green-accent px-2 xl:px-2.5 2xl:px-3 py-1.5 text-xs 2xl:text-sm font-semibold tracking-normal 2xl:tracking-wider transition whitespace-nowrap rounded-xl hover:bg-white/10">KOMPETENSI</a>
                    <a href="/career" class="text-white/90 hover:text-green-accent px-2 xl:px-2.5 2xl:px-3 py-1.5 text-xs 2xl:text-sm font-semibold tracking-normal 2xl:tracking-wider transition whitespace-nowrap rounded-xl hover:bg-white/10">CAREER</a>
                    <a href="/artikel" class="text-white/90 hover:text-green-accent px-2 xl:px-2.5 2xl:px-3 py-1.5 text-xs 2xl:text-sm font-semibold tracking-normal 2xl:tracking-wider transition whitespace-nowrap rounded-xl hover:bg-white/10">ARTIKEL</a>
                    <div x-data="{ open: false }" class="relative flex items-center" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="text-white/90 hover:text-green-accent px-2 xl:px-2.5 2xl:px-3 py-1.5 text-xs 2xl:text-sm font-semibold tracking-normal 2xl:tracking-wider transition flex items-center focus:outline-none whitespace-nowrap rounded-xl hover:bg-white/10">
                            PORTAL
                            <svg class="w-3.5 h-3.5 ml-1.5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition.opacity class="absolute top-full left-0 mt-1.5 w-60 bg-white dark:bg-gray-800 rounded-xl shadow-xl py-1.5 border border-gray-100 dark:border-gray-700 z-50" style="display: none;">
                            @foreach(\App\Models\AppSetting::getPortalLinks(true) as $portalLink)
                                <a href="{{ $portalLink['url'] }}" 
                                   target="{{ $portalLink['target'] ?? '_blank' }}" 
                                   @if(($portalLink['target'] ?? '_blank') === '_blank') rel="noopener noreferrer" @endif
                                   class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-teal-50 dark:hover:bg-gray-700 hover:text-teal-primary dark:hover:text-green-accent transition group">
                                    <span class="truncate font-medium">{{ $portalLink['name'] }}</span>
                                    @if(($portalLink['target'] ?? '_blank') === '_blank')
                                        <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-green-accent transition shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </nav>

                <!-- Right Actions -->
                <div class="hidden lg:flex items-center gap-1.5 xl:gap-2.5 2xl:gap-3 shrink-0 z-10">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode" class="text-white hover:text-green-accent w-7 h-7 xl:w-8 2xl:w-9 rounded-full flex items-center justify-center hover:bg-white/10 transition focus:outline-none shrink-0" title="Ubah Tema">
                        <!-- Sun Icon (shows when dark mode is active) -->
                        <svg x-show="darkMode" class="w-4 h-4 xl:w-4.5 xl:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon Icon (shows when light mode is active) -->
                        <svg x-show="!darkMode" class="w-4 h-4 xl:w-4.5 xl:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <!-- Search Icon -->
                    <button class="text-white hover:text-green-accent w-7 h-7 xl:w-8 2xl:w-9 rounded-full flex items-center justify-center hover:bg-white/10 transition shrink-0" title="Cari">
                        <svg class="w-4 h-4 xl:w-4.5 xl:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    <!-- Button Aplikasi Learning Activity -->
                    <a href="{{ route('download.app') }}" 
                       class="inline-flex items-center gap-1.5 bg-[#59A338] hover:bg-[#48872c] text-white px-2.5 xl:px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-300 shadow-sm hover:shadow-green-500/20 active:scale-95 whitespace-nowrap shrink-0"
                       title="Download Aplikasi Learning Activity (APK)">
                        <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span class="hidden 2xl:inline">Aplikasi </span><span>Learning Activity</span>
                    </a>

                    @auth('web')
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 bg-teal-600 hover:bg-teal-700 text-white px-2.5 xl:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-300 shadow-sm hover:shadow-teal-500/20 active:scale-95 whitespace-nowrap shrink-0">
                            <svg class="w-3.5 h-3.5 text-teal-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 bg-green-accent hover:bg-green-600 text-white px-2.5 xl:px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-300 shadow-sm hover:shadow-green-500/20 active:scale-95 whitespace-nowrap shrink-0">
                            <span>Masuk Portal</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden space-x-2">
                    <button @click="darkMode = !darkMode" class="text-white hover:text-green-accent p-2 transition rounded-full focus:outline-none">
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-white hover:text-green-accent focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden bg-teal-primary border-t border-teal-700 shadow-xl" 
             style="display: none;">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="/" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">BERANDA</a>
                <a href="/tentang-kami" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">TENTANG</a>
                <!-- Mobile Program Accordion with 8 Categories -->
                <div x-data="{ mobileProgramOpen: false, activeCat: null }" class="space-y-1">
                    <button @click.prevent="mobileProgramOpen = !mobileProgramOpen" type="button" class="w-full flex items-center justify-between px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition text-left">
                        <span>PROGRAM</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': mobileProgramOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="mobileProgramOpen" class="pl-3 pr-2 py-2 space-y-1 bg-black/20 rounded-lg text-sm" style="display: none;">
                        
                        @foreach($headerProgramMenus as $pMenu)
                            @if(!empty($pMenu['has_submenu']) && !empty($pMenu['submenus']))
                                <div class="pt-1">
                                    <button @click.prevent="activeCat = (activeCat === '{{ $pMenu['key'] }}' ? null : '{{ $pMenu['key'] }}')" class="w-full flex items-center justify-between px-3 py-1.5 font-semibold text-white/90">
                                        <span>{{ $pMenu['name'] }}</span>
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="{'rotate-180': activeCat === '{{ $pMenu['key'] }}'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="activeCat === '{{ $pMenu['key'] }}'" class="pl-5 space-y-1 py-1" style="display: none;">
                                        @foreach($pMenu['submenus'] as $sub)
                                            <a href="{{ $sub['url'] }}" class="block py-1 text-xs text-teal-100 hover:text-green-accent">
                                                {{ $sub['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="pt-1">
                                    <a href="{{ $pMenu['url'] ?? '#' }}" 
                                       class="w-full flex items-center justify-between px-3 py-1.5 font-semibold text-white/90 hover:text-green-accent transition">
                                        <span>{{ $pMenu['name'] }}</span>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('competency.public') }}" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">KOMPETENSI</a>
                <a href="/career" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">CAREER</a>
                <a href="/artikel" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">ARTIKEL</a>
                @foreach(\App\Models\AppSetting::getPortalLinks(true) as $portalLink)
                    <a href="{{ $portalLink['url'] }}" 
                       target="{{ $portalLink['target'] ?? '_blank' }}" 
                       @if(($portalLink['target'] ?? '_blank') === '_blank') rel="noopener noreferrer" @endif
                       class="flex items-center justify-between px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">
                        <span>{{ strtoupper($portalLink['name']) }}</span>
                        @if(($portalLink['target'] ?? '_blank') === '_blank')
                            <svg class="w-4 h-4 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        @endif
                    </a>
                @endforeach
                <a href="{{ route('download.app') }}" class="flex items-center gap-2 px-3 py-3 rounded-md text-base font-semibold text-green-300 hover:bg-teal-700 hover:text-white transition">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>APLIKASI LEARNING ACTIVITY</span>
                </a>
                <div class="mt-4 pt-4 border-t border-teal-700">
                    @auth
                        <a href="/admin" class="block text-center w-full bg-green-accent hover:bg-green-600 text-white px-5 py-3 rounded-md text-base font-bold transition">Dashboard Admin</a>
                    @else
                        <a href="/login" class="block text-center w-full bg-green-accent hover:bg-green-600 text-white px-5 py-3 rounded-md text-base font-bold transition">Masuk Portal</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow w-full overflow-x-hidden">
        {{ $slot }}
    </main>

    <!-- Footer (Corporate Agency Layout with First Resources Signature Deep Teal Palette) -->
    @php
        $footer = \App\Models\AppSetting::getFooterSettings();
        $footerPrograms = \Illuminate\Support\Facades\Schema::hasTable('portal_programs')
            ? \App\Models\PortalProgram::where('is_active', true)->orderBy('order')->orderBy('id')->take(6)->get()
            : collect();
    @endphp
    <footer class="bg-teal-primary text-white pt-16 pb-8 border-t border-teal-800/80 transition-colors duration-300 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-12 pb-12">
                <!-- Brand Info (Col 1-4) -->
                <div class="md:col-span-4 flex flex-col justify-between">
                    <div>
                        <!-- Logo & Brand Header -->
                        <div class="flex items-center gap-3 mb-4">
                            <img src="{{ asset('images/logofr.png') }}" alt="First Resources" class="h-10 w-auto border border-white/20 p-1 rounded bg-white">
                            <span class="text-white font-extrabold text-lg tracking-tight">{{ $footer['footer_brand_name'] ?? 'FR ACADEMY' }}</span>
                        </div>
                        <p class="text-xs sm:text-sm text-teal-100/80 font-light leading-relaxed mb-6 max-w-sm">
                            {{ $footer['footer_tagline'] ?? 'Pusat pengembangan kompetensi teknis, riset agronomi, kepemimpinan operasional, dan pembentukan karakter insan perkebunan kelapa sawit First Resources Group.' }}
                        </p>
                    </div>

                    <!-- Direct Contacts -->
                    <div class="space-y-2 text-xs text-teal-100/85">
                        @if(!empty($footer['footer_email']))
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <a href="mailto:{{ $footer['footer_email'] }}" class="hover:text-green-accent transition-colors">{{ $footer['footer_email'] }}</a>
                        </div>
                        @endif

                        @if(!empty($footer['footer_phone']))
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <a href="{{ !empty($footer['footer_wa_url']) ? $footer['footer_wa_url'] : 'tel:' . preg_replace('/[^0-9+]/', '', $footer['footer_phone']) }}" @if(!empty($footer['footer_wa_url'])) target="_blank" rel="noopener noreferrer" @endif class="hover:text-green-accent transition-colors">{{ $footer['footer_phone'] }}</a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Program Pathways (Col 5-6) -->
                <div class="md:col-span-2">
                    <h3 class="text-xs font-bold tracking-widest uppercase text-green-accent mb-4">
                        Program Kami
                    </h3>
                    <ul class="space-y-2.5 text-xs text-teal-100/80 font-light">
                        @if($footerPrograms->isNotEmpty())
                            @foreach($footerPrograms as $fProg)
                                <li>
                                    <a href="{{ url('/program/' . $fProg->slug) }}" class="hover:text-white hover:text-green-accent transition-colors">
                                        {{ !empty($fProg->code) ? $fProg->code . ' (' . Str::limit($fProg->title, 18) . ')' : Str::limit($fProg->title, 24) }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><a href="/program/fat" class="hover:text-white hover:text-green-accent transition-colors">FAT (Kebun)</a></li>
                            <li><a href="/program/mat" class="hover:text-white hover:text-green-accent transition-colors">MAT (Pabrik PKS)</a></li>
                            <li><a href="/program/tat" class="hover:text-white hover:text-green-accent transition-colors">TAT (Traksi & Bengkel)</a></li>
                            <li><a href="/program/aat" class="hover:text-white hover:text-green-accent transition-colors">AAT (Tata Usaha KTU)</a></li>
                            <li><a href="/program/dmt" class="hover:text-white hover:text-green-accent transition-colors">DMT (Kilang Hilir)</a></li>
                            <li><a href="/program/managerial-leadership" class="hover:text-white hover:text-green-accent transition-colors">Managerial & Leadership</a></li>
                            <li><a href="/program/talent-management" class="hover:text-white hover:text-green-accent transition-colors">Talent Management</a></li>
                        @endif
                        <li><a href="/career" class="text-green-accent font-semibold hover:text-white transition-colors">Peluang Karir &rarr;</a></li>
                    </ul>
                </div>

                <!-- Useful Links (Col 7-8) -->
                <div class="md:col-span-2">
                    <h3 class="text-xs font-bold tracking-widest uppercase text-green-accent mb-4">
                        Pintasan Portal
                    </h3>
                    <ul class="space-y-2.5 text-xs text-teal-100/80 font-light">
                        <li><a href="/" class="hover:text-white hover:text-green-accent transition-colors">Beranda Utama</a></li>
                        <li><a href="/tentang-kami" class="hover:text-white hover:text-green-accent transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ route('competency.public') }}" class="hover:text-white hover:text-green-accent transition-colors">Kompetensi</a></li>
                        <li><a href="https://petasdm-academy.first-resources.com/" target="_blank" class="hover:text-white hover:text-green-accent transition-colors">Peta SDM Operasional</a></li>
                        <li><a href="/artikel" class="hover:text-white hover:text-green-accent transition-colors">Artikel & Berita</a></li>
                        <li><a href="/tv-display" class="hover:text-white hover:text-green-accent transition-colors">TV Display Mode</a></li>
                        <li><a href="/login" class="hover:text-white hover:text-green-accent transition-colors">Login Admin / Staf</a></li>
                        <li><a href="{{ route('download.app') }}" class="inline-flex items-center gap-1.5 text-green-accent hover:text-white transition-colors font-medium"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Aplikasi Learning Activity</a></li>
                    </ul>
                </div>

                <!-- Location & Map (Col 9-12) -->
                <div class="md:col-span-4">
                    <h3 class="text-xs font-bold tracking-widest uppercase text-green-accent mb-4">
                        {{ $footer['footer_location_title'] ?? 'Lokasi Pusat Pelatihan' }}
                    </h3>
                    <a href="{{ $footer['footer_maps_view_url'] ?? 'https://maps.google.com/maps?q=First+Resources+Learning+Centre' }}" target="_blank" rel="noopener noreferrer" class="block relative w-full h-36 bg-teal-950 rounded-xl overflow-hidden border border-teal-700/70 shadow-inner group">
                        <!-- Peta Google Asli -->
                        <iframe src="{{ $footer['footer_maps_embed_url'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15958.629399435649!2d101.41169300600109!3d0.4705599827050588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5a86a6058e579%3A0xc3f1a26c4f03cb71!2sFirst%20Resources%20Learning%20Centre!5e0!3m2!1sen!2sid!4v1709618143491!5m2!1sen!2sid' }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="pointer-events-none w-full h-full"></iframe>
                        <!-- Overlay Link -->
                        <div class="absolute inset-0 bg-teal-950/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <span class="bg-green-accent text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                {{ $footer['footer_maps_button_text'] ?? 'Buka di Google Maps' }}
                            </span>
                        </div>
                    </a>
                    @if(!empty($footer['footer_location_address']))
                    <p class="text-[11px] text-teal-200/70 mt-2.5 leading-relaxed">
                        {{ $footer['footer_location_address'] }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Bottom Sub-bar: Copyright on Left, Social Media on Right -->
            <div class="pt-6 border-t border-teal-800/80 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-teal-200/70">
                <p>{{ $footer['footer_copyright'] ?? '© 2026 First Resources Academy. All rights reserved.' }}</p>

                <!-- Social Media Icons (Hanya tampil jika diisi) -->
                <div class="flex items-center space-x-5">
                    @if(!empty($footer['footer_social_linkedin']))
                    <a href="{{ $footer['footer_social_linkedin'] }}" target="_blank" rel="noopener noreferrer" class="text-teal-200 hover:text-green-accent transition-colors" aria-label="LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                    @endif

                    @if(!empty($footer['footer_social_facebook']))
                    <a href="{{ $footer['footer_social_facebook'] }}" target="_blank" rel="noopener noreferrer" class="text-teal-200 hover:text-green-accent transition-colors" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
                        </svg>
                    </a>
                    @endif

                    @if(!empty($footer['footer_social_instagram']))
                    <a href="{{ $footer['footer_social_instagram'] }}" target="_blank" rel="noopener noreferrer" class="text-teal-200 hover:text-green-accent transition-colors" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    @endif

                    @if(!empty($footer['footer_social_youtube']))
                    <a href="{{ $footer['footer_social_youtube'] }}" target="_blank" rel="noopener noreferrer" class="text-teal-200 hover:text-green-accent transition-colors" aria-label="YouTube">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </footer>
        </div>
    </footer>

    <!-- Floating Scroll to Top Button -->
    <div x-data="{ show: false }" 
         @scroll.window="show = window.pageYOffset > 300"
         class="fixed bottom-8 right-8 z-50">
        <button x-show="show" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                class="w-12 h-12 bg-green-accent rounded-full flex items-center justify-center text-white hover:bg-green-600 hover:shadow-xl transition-all shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                style="display: none;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
        </button>
    </div>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });
    </script>
</body>
</html>
