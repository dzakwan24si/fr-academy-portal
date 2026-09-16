<!-- 2. TOP HEADER NAVBAR PARTIAL (Synced with admin-layout.blade.php) -->
<header class="bg-white border-b border-gray-200 h-24 flex items-center justify-between px-8 z-10 shrink-0">
    <div class="flex items-center flex-1">
        <!-- Breadcrumbs -->
        <div class="hidden md:flex items-center text-sm text-gray-500">
            <span class="hover:text-gray-800 cursor-pointer">Home</span>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="hover:text-gray-800 cursor-pointer">Administrasi</span>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-[#509c38] font-medium">@yield('title', 'Dashboard')</span>
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
                    <p class="text-xs text-gray-500 leading-tight">{{ auth()->check() && auth()->user()->roles->count() > 0 ? auth()->user()->roles->pluck('name')->join(', ') : 'Administrator' }}</p>
                </div>
                <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-1 border border-gray-100 z-50" style="display: none;">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 font-medium">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</header>
