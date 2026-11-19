<x-layout>
    <!-- Hero Section -->
    <section class="relative h-[350px] md:h-[400px] pt-20 overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/slide5.png') }}" alt="Artikel FR Academy" class="w-full h-full object-cover object-center" data-aos="zoom-out" data-aos-duration="2000">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-green-accent font-bold tracking-widest uppercase text-sm mb-4 block">
                Pusat Informasi
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">
                Artikel & <span class="text-green-accent">Pengumuman</span>
            </h1>
            <p class="text-lg md:text-xl text-teal-100 max-w-2xl mx-auto font-light">
                Kumpulan berita terbaru, penelitian, dan pencapaian dari FR Academy.
            </p>
        </div>
    </section>

    <!-- Category Tabs & Articles -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 transition-colors duration-300">
        
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto mb-10" data-aos="fade-up">
            <form action="/artikel" method="GET" class="relative">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                        <svg class="w-5 h-5 text-green-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari artikel, topik, atau kata kunci..." 
                        aria-label="Cari artikel"
                        style="padding-left: 3rem; padding-right: 6.5rem;"
                        class="w-full pl-12 pr-28 py-3.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-sm text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-accent focus:border-transparent transition-all duration-200"
                    >
                    @if(request('search'))
                        <a href="{{ request('category') ? '/artikel?category='.request('category') : '/artikel' }}" 
                           class="absolute inset-y-0 right-20 flex items-center pr-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition" 
                           title="Hapus pencarian">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                    <button 
                        type="submit" 
                        class="absolute right-1.5 top-1.5 bottom-1.5 px-5 bg-teal-primary hover:bg-teal-900 text-white rounded-full text-xs font-semibold tracking-wide transition-all duration-200 shadow flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>Cari</span>
                    </button>
                </div>
            </form>

            <!-- Status Pencarian Aktif -->
            @if(request('search'))
                <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 px-2">
                    <div>
                        Hasil pencarian untuk: <span class="font-bold text-teal-primary dark:text-green-accent">"{{ request('search') }}"</span>
                        @if(request('category') && $activeCategory = $categories->firstWhere('id', request('category')))
                            di kategori <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $activeCategory->name }}</span>
                        @endif
                        <span class="ml-1 text-gray-400">({{ $articles->total() }} artikel ditemukan)</span>
                    </div>
                    <a href="{{ request('category') ? '/artikel?category='.request('category') : '/artikel' }}" class="text-red-500 hover:text-red-600 dark:text-red-400 underline font-medium">
                        Reset Pencarian
                    </a>
                </div>
            @endif
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-12">
            <a href="/artikel{{ request('search') ? '?search='.urlencode(request('search')) : '' }}" class="px-5 py-2 rounded-full {{ request('category') ? 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-green-accent' : 'bg-green-accent text-white shadow-md' }} font-semibold text-xs sm:text-sm transition">Semua</a>
            @foreach($categories as $cat)
                <a href="/artikel?category={{ $cat->id }}{{ request('search') ? '&search='.urlencode(request('search')) : '' }}" class="px-5 py-2 rounded-full {{ request('category') == $cat->id ? 'bg-green-accent text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 hover:border-green-accent' }} font-medium text-xs sm:text-sm transition">{{ $cat->name }}</a>
            @endforeach
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($articles as $article)
            <a href="{{ route('artikel.show', $article->slug) }}" class="group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 dark:border-gray-700 flex flex-col">
                <div class="relative h-56 overflow-hidden">
                    @if($article->featured_image)
                        <img src="{{ Storage::url($article->featured_image) }}" alt="Cover" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.onerror=null; this.src='{{ asset('images/welcomefr.jpg') }}';">
                    @else
                        <div class="w-full h-full bg-[#114045] flex items-center justify-center opacity-90 group-hover:scale-105 transition duration-500">
                            <img src="{{ asset('images/logofr.png') }}" class="w-32 opacity-30">
                        </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-white dark:bg-gray-900 px-3 py-1 rounded text-xs font-bold text-teal-primary dark:text-teal-400 uppercase tracking-wider shadow">
                        {{ $article->category->name ?? 'Informasi' }}
                    </div>
                </div>
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : $article->created_at->format('d M Y') }}
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3 line-clamp-2 group-hover:text-green-accent dark:group-hover:text-green-400 transition">{{ $article->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">{{ $article->excerpt(120) }}</p>
                    </div>
                    <div class="text-green-accent dark:text-green-400 font-semibold text-sm inline-flex items-center group-hover:text-green-600 dark:group-hover:text-green-300 transition mt-4">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>
            </a>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20"></path>
                        </svg>
                        @if(request('search'))
                            <h4 class="text-base font-bold text-gray-800 dark:text-gray-200 mb-1">Artikel Tidak Ditemukan</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Tidak ada artikel yang cocok dengan kata kunci "{{ request('search') }}".</p>
                            <a href="{{ request('category') ? '/artikel?category='.request('category') : '/artikel' }}" class="inline-flex items-center px-4 py-2 bg-green-accent hover:bg-green-600 text-white text-xs font-semibold rounded-full shadow transition">
                                Reset Pencarian
                            </a>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Belum ada artikel di kategori ini.</p>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-12">
            {{ $articles->links() }}
        </div>
    </div>
</x-layout>
