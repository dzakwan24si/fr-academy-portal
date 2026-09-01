<x-layout>
    <!-- Article Header -->
    <div class="relative bg-teal-primary min-h-80 flex flex-col justify-end pb-12 pt-32">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Header Background" class="w-full h-full object-cover opacity-30">
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-gray-300 mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="/" class="hover:text-white transition">BERANDA</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            <a href="/artikel" class="hover:text-white transition">ARTIKEL</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            <span class="text-white font-medium truncate max-w-[200px] md:max-w-md">FR ACADEMY DAMPINGI PETANI LOKAL KEMBANGKAN...</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight max-w-4xl">
                FR Academy Dampingi Petani Lokal Kembangkan Praktik Pertanian Berkelanjutan
            </h1>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Left Sidebar (30%) -->
            <div class="w-full lg:w-1/3 space-y-10 order-2 lg:order-1">
                
                <!-- Kategori Lainnya -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="text-xl font-bold text-teal-primary mb-6 border-b pb-4">Kategori Lainnya</h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="flex items-center text-gray-600 hover:text-green-accent transition">
                            <svg class="w-4 h-4 mr-3 text-green-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Berita
                        </a></li>
                        <li><a href="#" class="flex items-center text-gray-600 hover:text-green-accent transition">
                            <svg class="w-4 h-4 mr-3 text-green-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Pengumuman
                        </a></li>
                        <li><a href="#" class="flex items-center text-gray-600 hover:text-green-accent transition">
                            <svg class="w-4 h-4 mr-3 text-green-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Penelitian
                        </a></li>
                        <li><a href="#" class="flex items-center text-gray-600 hover:text-green-accent transition">
                            <svg class="w-4 h-4 mr-3 text-green-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Prestasi
                        </a></li>
                        <li><a href="#" class="flex items-center text-gray-600 hover:text-green-accent transition">
                            <svg class="w-4 h-4 mr-3 text-green-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            Riset Unggulan
                        </a></li>
                    </ul>
                </div>

                <!-- Berita Terbaru -->
                <div>
                    <h3 class="text-xl font-bold text-teal-primary mb-6">Berita Terbaru</h3>
                    <div class="space-y-6">
                        <!-- News Item -->
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition cursor-pointer">
                            <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="News" class="w-full h-32 object-cover">
                            <div class="p-4">
                                <h4 class="font-bold text-teal-primary text-sm leading-tight mb-2">FR Academy Gelar Workshop Manajemen Operasional Pertanian</h4>
                                <div class="text-xs text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    2 hari yang lalu
                                </div>
                            </div>
                        </div>

                        <!-- News Item -->
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition cursor-pointer">
                            <img src="https://images.unsplash.com/photo-1592982537447-6f29402c40c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="News" class="w-full h-32 object-cover">
                            <div class="p-4">
                                <h4 class="font-bold text-teal-primary text-sm leading-tight mb-2">Peneliti FR Academy Temukan Metode Baru Perawatan Bibit</h4>
                                <div class="text-xs text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    1 minggu yang lalu
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="/artikel" class="inline-flex items-center text-teal-primary font-semibold hover:text-green-accent transition mt-6 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        Lihat Semua Berita
                    </a>
                </div>
            </div>

            <!-- Right Main Content (70%) -->
            <div class="w-full lg:w-2/3 order-1 lg:order-2">
                <article class="bg-white p-8 md:p-10 rounded-2xl border border-gray-100 shadow-sm">
                    <!-- Article Body -->
                    <div class="prose prose-teal max-w-none text-gray-700 space-y-6">
                        <p class="font-bold text-gray-900">
                            Bengkalis (5/8) - FR Academy kembali melaksanakan kegiatan Pengabdian kepada Masyarakat (PkM) di wilayah perkebunan kelapa sawit rakyat di Kecamatan Pinggir, Kabupaten Bengkalis, Rabu (5/8/2026). Kegiatan kali ini difokuskan pada penguatan daya saing produk turunan kelapa sawit yang dikembangkan oleh kelompok tani lokal.
                        </p>
                        
                        <p>
                            Pendampingan tersebut menggabungkan pelatihan manajemen operasional panen dengan fasilitasi sertifikasi keberlanjutan untuk produk unggulan desa. Kegiatan diikuti lebih dari 40 petani dan penyuluh lapangan dari berbagai area perkebunan, difasilitasi oleh dosen ahli dari program studi Agribisnis dan Teknologi Hasil Pertanian FR Academy.
                        </p>
                        
                        <p>
                            Kegiatan ini merupakan kelanjutan dari program pendampingan yang dilakukan tim PkM FR Academy di Kecamatan Pinggir pada awal 2026 melalui program bertajuk "Optimalisasi Rantai Pasok dan Strategi Pemasaran Digital yang Legal untuk Produk Pertanian Lokal".
                        </p>
                        
                        <p>
                            Pada pendampingan sebelumnya, petani diperkenalkan pada proses menemukan ide inovasi produk berbasis komoditas turunan sawit menggunakan pendekatan design thinking. Petani juga mendapatkan pemahaman mengenai legalitas usaha, termasuk simulasi pendaftaran Nomor Induk Berusaha (NIB) melalui OSS RBA serta pentingnya sertifikasi keamanan pangan sebagai salah satu nilai tambah produk.
                        </p>
                        
                        <p>
                            "Pada tahap ini, pendampingan kami arahkan pada penguatan efisiensi produksi dan pemasaran. Harapannya, produk yang sudah mulai dibangun dari sisi legalitas dan kualitas ini dapat semakin dikenal dan menjangkau pasar yang lebih luas," ujar Ketua Pengusul PkM, Dr. Budi Santoso, M.Si.
                        </p>
                        
                        <p>
                            Kegiatan dibuka oleh Kepala Desa setempat, yang menyampaikan apresiasi atas keberlanjutan kerja sama antara komunitas petani dan FR Academy. Menurutnya, pendampingan yang diberikan menjadi kesempatan bagi masyarakat untuk semakin siap dalam mengembangkan usaha pertanian produktif dan berkelanjutan.
                        </p>
                    </div>

                    <!-- Article Meta -->
                    <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <div class="w-8 h-8 rounded-full bg-teal-primary text-white flex items-center justify-center mr-3 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            1 minggu yang lalu
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-500">
                            <div class="w-8 h-8 rounded-full bg-teal-primary text-white flex items-center justify-center mr-3 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            Administrator
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="mt-8 flex flex-wrap gap-2">
                        <a href="#" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-xs font-semibold text-gray-600 hover:border-green-accent hover:text-green-accent transition">Kehidupan Kampus</a>
                        <a href="#" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-xs font-semibold text-gray-600 hover:border-green-accent hover:text-green-accent transition">Sekilas FR</a>
                        <a href="#" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-xs font-semibold text-gray-600 hover:border-green-accent hover:text-green-accent transition">Pengabdian kepada Masyarakat</a>
                    </div>
                </article>
            </div>
            
        </div>
    </div>
</x-layout>
