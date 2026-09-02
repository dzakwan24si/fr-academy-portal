<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">Dashboard Overview</x-slot>
    <x-slot name="headerSub">Ringkasan statistik dan aktivitas Portal FR Academy.</x-slot>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats Card 1 -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex items-center shadow-sm">
            <div class="w-14 h-14 rounded-full bg-[#509c38]/10 flex items-center justify-center text-[#509c38] mr-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wide">Total Artikel</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $totalArticles }}</p>
            </div>
        </div>
        
        <!-- Stats Card 2 -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex items-center shadow-sm">
            <div class="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 mr-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wide">Tema Event</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $totalEvents }}</p>
            </div>
        </div>
        
        <!-- Stats Card 3 -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex items-center shadow-sm">
            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500 mr-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wide">TV Playlist</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $totalPlaylists }}</p>
            </div>
        </div>
        
        <!-- Stats Card 4 -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex items-center shadow-sm">
            <div class="w-14 h-14 rounded-full bg-purple-50 flex items-center justify-center text-purple-500 mr-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wide">Administrator</p>
                <p class="text-2xl font-extrabold text-gray-800">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions as a clean white section -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-8 shadow-sm">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Aksi Cepat</h3>
        </div>
        <div class="p-8">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.artikel.create') }}" class="px-6 py-3 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tulis Artikel Baru
                </a>
                <a href="{{ route('admin.tv.index') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-sm inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    Atur Playlist TV
                </a>
                <a href="{{ route('admin.events.create') }}" class="px-6 py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-sm inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Unggah Tema
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
