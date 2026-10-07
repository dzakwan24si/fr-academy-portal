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
