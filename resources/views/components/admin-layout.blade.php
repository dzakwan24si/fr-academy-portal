<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} - FR Academy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-slate-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 md:hidden" @click="sidebarOpen = false" x-transition.opacity style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="w-64 bg-white border-r border-slate-200 flex flex-col flex-shrink-0 z-30 fixed inset-y-0 left-0 transition-transform duration-300 md:relative md:translate-x-0">
<!-- Logo Area -->
<div class="h-16 flex items-center px-6 border-b border-gray-200 gap-3">
    <div class="w-8 h-8 bg-green-700 rounded flex items-center justify-center overflow-hidden">
        <!-- Logo image -->
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6IXFsyDzrrVfRnMqVKjbCCQMpoDcnpq7oqulBgm5nQQ&s=10"
             alt="FR Academy Logo"
             class="w-full h-full object-cover"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

        <!-- Fallback logo jika gambar gagal dimuat -->
        <svg class="w-5 h-5 text-white hidden" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
    </div>
    <span class="font-bold text-lg text-gray-800">FR Academy</span>
</div>
        <!-- Sidebar Content -->
        <div class="flex-1 overflow-y-auto p-4">
            <p class="text-xs font-semibold text-gray-400 mb-4 px-2 uppercase tracking-wider">Menu Utama</p>
            
            <nav class="space-y-1">
                <a href="{{ route('training-reguler.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-reguler.dashboard') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <a href="{{ route('training-reguler.training-programs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-reguler.training-programs.*') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Program Training
                </a>

                <a href="{{ route('training-reguler.trainees.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-reguler.trainees.*') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Trainee
                </a>

                <a href="{{ route('training-reguler.trainers.index') }}" class="hidden items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-reguler.trainers.*') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Trainer
                </a>

                <a href="{{ route('training-reguler.daily-classes.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-reguler.daily-classes.*') || request()->routeIs('training-reguler.tasks.*') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Sesi Pelatihan
                </a>

                <a href="{{ route('training-reguler.weekly-reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-reguler.weekly-reports.*') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan Mingguan
                </a>

                <a href="{{ route('training-reguler.moas.index') }}" class="hidden flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-reguler.moas.*') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15M9 11l3 3L22 4"></path></svg>
                    MOA
                </a>

                <div class="pt-4 mt-4 border-t border-gray-100"></div>
                <p class="text-xs font-semibold text-gray-400 mb-4 px-2 uppercase tracking-wider">Non Reguler Training</p>
                
                <a href="{{ route('training-non-reguler.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-non-reguler.dashboard') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                
                <a href="{{ route('training-non-reguler.daily-classes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('training-non-reguler.daily-classes.*') || request()->routeIs('training-non-reguler.tasks.*') ? 'bg-[#28844b] text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Materi Pelatihan
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-100">
        
        <!-- Headerbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-10 flex-shrink-0">
            <!-- Left side badge -->
            <div class="flex items-center gap-3">
                <!-- Hamburger Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 rounded p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">
                    {{ $title ?? 'Dashboard' }}
                </h1>
            </div>

            <!-- Right side info -->
            <div class="flex items-center gap-6">
                <!-- Clock -->
                <div x-data="realtimeClock()" x-init="startClock()" class="text-right hidden sm:block">
                    <div class="flex items-center justify-end text-green-700 font-bold gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span x-text="time"></span>
                    </div>
                    <div class="text-[11px] text-gray-400 font-medium" x-text="date"></div>
                </div>

                <!-- User Profile -->
                <div class="flex items-center gap-3 border-l pl-6 border-gray-200 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                    <div class="w-9 h-9 rounded-full bg-green-100 text-green-700 font-bold flex items-center justify-center text-sm border border-green-200">
                        {{ substr(auth()->user()?->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-bold text-gray-800 leading-tight">{{ auth()->user()?->name ?? 'Admin FR' }}</p>
                        <p class="text-xs text-gray-500">Superadmin</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-auto p-6 lg:p-8">
            <!-- Header Section -->
            @if(isset($header))
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-slate-800">{{ $header }}</h1>
                    @if(isset($headerSub))
                        <p class="text-sm text-gray-500 mt-1">{{ $headerSub }}</p>
                    @endif
                </div>
            @endif

            <!-- Global Toast Alert -->
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-6 flex items-center justify-between gap-3 p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl shadow-sm" x-transition.opacity>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold">Berhasil!</p>
                        <p class="text-sm text-emerald-600">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 bg-emerald-100 hover:bg-emerald-200 p-1.5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Clock Script -->
    <script>
        function realtimeClock() {
            return {
                time: '',
                date: '',
                days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                startClock() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                },
                updateClock() {
                    const now = new Date();
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');
                    this.time = `${h}.${m}.${s}`;
                    
                    const dayName = this.days[now.getDay()];
                    const d = now.getDate();
                    const monthName = this.months[now.getMonth()];
                    const y = now.getFullYear();
                    this.date = `${dayName}, ${d} ${monthName} ${y}`;
                }
            }
        }
    </script>
</body>
</html>
