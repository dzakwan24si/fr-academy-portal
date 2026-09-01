<x-layout>
    <!-- Header -->
    <div class="bg-teal-primary pt-32 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Artikel & Pengumuman</h1>
            <p class="text-teal-100 text-lg max-w-2xl mx-auto">Kumpulan berita terbaru, penelitian, dan pencapaian dari FR Academy.</p>
        </div>
    </div>

    <!-- Category Tabs & Articles -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <!-- Tabs -->
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <a href="/artikel" class="px-6 py-2 rounded-full {{ request('category') ? 'bg-white text-gray-600 border border-gray-200' : 'bg-green-accent text-white shadow-md' }} font-semibold text-sm transition">Semua</a>
            @foreach($categories as $cat)
                <a href="/artikel?category={{ $cat->id }}" class="px-6 py-2 rounded-full {{ request('category') == $cat->id ? 'bg-green-accent text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }} font-medium text-sm transition">{{ $cat->name }}</a>
            @endforeach
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($articles as $article)
            <a href="{{ route('artikel.show', $article->slug) }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col">
                <div class="relative h-56 overflow-hidden">
                    @if($article->featured_image)
                        <img src="{{ Storage::url($article->featured_image) }}" alt="Cover" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full bg-[#114045] flex items-center justify-center opacity-90 group-hover:scale-105 transition duration-500">
                            <img src="{{ asset('images/logofr.png') }}" class="w-32 opacity-30">
                        </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-bold text-teal-primary uppercase tracking-wider shadow">
                        {{ $article->category->name ?? 'Informasi' }}
                    </div>
                </div>
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : $article->created_at->format('d M Y') }}
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary mb-3 line-clamp-2 group-hover:text-green-accent transition">{{ $article->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit(strip_tags($article->content), 120) }}</p>
                    </div>
                    <div class="text-green-accent font-semibold text-sm inline-flex items-center group-hover:text-green-600 transition mt-4">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>
            </a>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5L18.5 7H20"></path></svg>
                        <p class="text-gray-500 text-lg font-medium">Belum ada artikel di kategori ini.</p>
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
