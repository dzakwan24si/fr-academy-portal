<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="theme-color" content="#1e3a8a">
    <title>FR Academy Trainee</title>
    
    <!-- Portal Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-slate-800 flex h-screen overflow-hidden">
    
    <!-- Desktop Sidebar (Hidden on mobile) -->
    @if(!request()->routeIs('trainee.login'))
    <aside class="hidden lg:flex flex-col w-64 bg-[#064e3b] text-white shadow-xl flex-shrink-0 z-40 relative">
        <div class="p-6 border-b border-emerald-800/50 bg-[#064e3b]">
            <h1 class="text-2xl font-black text-white flex items-center gap-3">
                <img src="https://www.first-resources.com/wp-content/uploads/2024/06/fr-logo.png" alt="FR Logo" class="h-12 w-auto bg-white rounded p-1 shadow-lg shadow-white/10">
                FR Academy
            </h1>
            <p class="text-emerald-200 text-xs mt-2 font-medium">Trainee Portal</p>
        </div>
        <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto">
            <a href="{{ route('trainee.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('trainee.dashboard') ? 'bg-lime-500 text-[#064e3b] font-bold shadow-md' : 'text-emerald-100 hover:bg-emerald-800/50 font-medium' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="{{ route('trainee.attendance') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('trainee.attendance') ? 'bg-lime-500 text-[#064e3b] font-bold shadow-md' : 'text-emerald-100 hover:bg-emerald-800/50 font-medium' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Absensi
            </a>
            <a href="{{ route('trainee.tasks') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('trainee.tasks') ? 'bg-lime-500 text-[#064e3b] font-bold shadow-md' : 'text-emerald-100 hover:bg-emerald-800/50 font-medium' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                Tugas Training
            </a>
            <a href="{{ route('trainee.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('trainee.profile') ? 'bg-lime-500 text-[#064e3b] font-bold shadow-md' : 'text-emerald-100 hover:bg-emerald-800/50 font-medium' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profil Saya
            </a>
        </nav>
    </aside>
    @endif

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50 h-screen overflow-hidden relative">
        
        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden w-full {{ !request()->routeIs('trainee.login') ? 'pb-20 lg:pb-0' : '' }}">
            @yield('content')
        </main>

        <!-- Mobile Bottom Navigation (Hidden on desktop) -->
        @if(!request()->routeIs('trainee.login'))
        <nav class="lg:hidden fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 flex justify-around items-center z-50 pb-safe shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
            <a href="{{ route('trainee.dashboard') }}" class="flex flex-col items-center justify-center w-full py-2.5 transition-colors {{ request()->routeIs('trainee.dashboard') ? 'text-[#064e3b]' : 'text-slate-400' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[10px] font-bold">Dashboard</span>
            </a>
            <a href="{{ route('trainee.attendance') }}" class="flex flex-col items-center justify-center w-full py-2.5 transition-colors {{ request()->routeIs('trainee.attendance') ? 'text-[#064e3b]' : 'text-slate-400' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <span class="text-[10px] font-bold">Absen</span>
            </a>
            <a href="{{ route('trainee.tasks') }}" class="flex flex-col items-center justify-center w-full py-2.5 transition-colors {{ request()->routeIs('trainee.tasks') ? 'text-[#064e3b]' : 'text-slate-400' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="text-[10px] font-bold">Tugas</span>
            </a>
            <a href="{{ route('trainee.profile') }}" class="flex flex-col items-center justify-center w-full py-2.5 transition-colors {{ request()->routeIs('trainee.profile') ? 'text-[#064e3b]' : 'text-slate-400' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-[10px] font-bold">Profil</span>
            </a>
        </nav>
        @endif
    </div>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</body>
</html>
