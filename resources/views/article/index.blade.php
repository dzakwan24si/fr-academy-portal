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
            <a href="#" class="px-6 py-2 rounded-full bg-green-accent text-white font-semibold text-sm shadow-md transition">Semua</a>
            <a href="#" class="px-6 py-2 rounded-full bg-white text-gray-600 font-medium text-sm hover:bg-gray-50 border border-gray-200 transition">Berita</a>
            <a href="#" class="px-6 py-2 rounded-full bg-white text-gray-600 font-medium text-sm hover:bg-gray-50 border border-gray-200 transition">Pengumuman</a>
            <a href="#" class="px-6 py-2 rounded-full bg-white text-gray-600 font-medium text-sm hover:bg-gray-50 border border-gray-200 transition">Penelitian</a>
            <a href="#" class="px-6 py-2 rounded-full bg-white text-gray-600 font-medium text-sm hover:bg-gray-50 border border-gray-200 transition">Prestasi</a>
            <a href="#" class="px-6 py-2 rounded-full bg-white text-gray-600 font-medium text-sm hover:bg-gray-50 border border-gray-200 transition">Riset Unggulan</a>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Item 1 -->
            <a href="/artikel/slug-artikel" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-bold text-teal-primary uppercase tracking-wider shadow">Berita</div>
                </div>
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            12 Sep 2026
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary mb-3 line-clamp-2 group-hover:text-green-accent transition">Pendaftaran Batch Terbaru Program Reguler Dibuka</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">FR Academy kembali membuka pendaftaran untuk program pelatihan reguler calon asisten agronomi dan pabrik kelapa sawit.</p>
                    </div>
                    <div class="text-green-accent font-semibold text-sm inline-flex items-center group-hover:text-green-600 transition mt-4">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>
            </a>

            <!-- Item 2 -->
            <a href="/artikel/slug-artikel" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1516937941344-00b4e0337589?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-bold text-teal-primary uppercase tracking-wider shadow">Riset</div>
                </div>
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            05 Sep 2026
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary mb-3 line-clamp-2 group-hover:text-green-accent transition">Cara Mencegah Hama Kumbang Tanduk</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">Panduan praktis terbaru dari tim peneliti FR Academy mengenai pencegahan dan pengendalian hama Oryctes rhinoceros.</p>
                    </div>
                    <div class="text-green-accent font-semibold text-sm inline-flex items-center group-hover:text-green-600 transition mt-4">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>
            </a>
            
            <!-- Item 3 -->
            <a href="/artikel/slug-artikel" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col">
                <div class="relative h-56 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="News" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-bold text-teal-primary uppercase tracking-wider shadow">Berita</div>
                </div>
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            03 Sep 2026
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary mb-3 line-clamp-2 group-hover:text-green-accent transition">FR Academy Dampingi Petani Lokal</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">Kegiatan pengabdian masyarakat di Bengkalis untuk penguatan daya saing produk kelapa sawit.</p>
                    </div>
                    <div class="text-green-accent font-semibold text-sm inline-flex items-center group-hover:text-green-600 transition mt-4">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Pagination (Dummy) -->
        <div class="mt-16 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                </a>
                <a href="#" aria-current="page" class="z-10 bg-teal-50 border-teal-500 text-teal-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">1</a>
                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">2</a>
                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">3</a>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">8</a>
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </a>
            </nav>
        </div>
    </div>
</x-layout>
