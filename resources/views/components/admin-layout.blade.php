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

    

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-100">
        
        <!-- Headerbar -->
        <header class="relative h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-[100] flex-shrink-0">
            <!-- Left side badge -->
            <div class="flex items-center gap-3">
                <!-- Hamburger Menu Dropdown -->
                <div x-data="{ menuOpen: false }" class="relative">
                    <button @click="menuOpen = !menuOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 rounded p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div x-show="menuOpen" @click.away="menuOpen = false" x-transition class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-slate-200 py-2 z-50 hidden" :class="{'hidden': !menuOpen}">
                        <div class="px-4 py-2 border-b border-slate-100 mb-1">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">FR Academy</span>
                        </div>
                        <a href="{{ route('training-non-reguler.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('training-non-reguler.dashboard') ? 'text-[#28844b] bg-green-50' : 'text-gray-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                        
                        <a href="{{ route('training-non-reguler.daily-classes.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition {{ request()->routeIs('training-non-reguler.daily-classes.*') || request()->routeIs('training-non-reguler.tasks.*') ? 'text-[#28844b] bg-green-50' : 'text-gray-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Materi Pelatihan
                        </a>
                    </div>
                </div>
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





