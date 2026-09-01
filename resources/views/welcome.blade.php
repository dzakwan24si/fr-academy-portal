<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-teal-primary min-h-[600px] flex items-center pt-32 pb-16">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/welcomefr.jpg') }}" alt="FR Academy Welcome" class="w-full h-full object-cover">
        </div>
        <!-- Dark overlay to ensure text readability -->
        <div class="absolute inset-0 bg-black/50 z-10"></div>
        
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-2xl">
                <span class="inline-block py-1 px-3 rounded bg-white/20 text-white text-xs font-semibold tracking-wider mb-4 border border-white/30 backdrop-blur-sm">FIRST RESOURCES LEARNING CENTER</span>
                <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight mb-6">
                    Membangun Masa Depan <br>
                    <span class="text-green-accent">Kelapa Sawit Berkelanjutan</span>
                </h1>
                <p class="text-lg text-gray-200 mb-8 max-w-xl">
                    Pusat pelatihan perusahaan terdepan untuk menciptakan sumber daya manusia yang unggul, profesional, dan berdedikasi.
                </p>
                
                <!-- Search Box -->
                <div class="bg-white p-2 rounded-full flex items-center shadow-lg max-w-xl">
                    <input type="text" placeholder="Cari artikel atau program pelatihan..." class="flex-grow px-4 py-2 text-gray-700 focus:outline-none bg-transparent">
                    <button class="bg-green-accent hover:bg-green-600 text-white px-6 py-3 rounded-full font-medium transition duration-300">
                        Cari
                    </button>
                </div>

                <div class="mt-8 flex items-center gap-4 text-white">
                    <div class="flex -space-x-3">
                        <img class="w-10 h-10 rounded-full border-2 border-teal-primary" src="https://ui-avatars.com/api/?name=A&background=random" alt="Avatar">
                        <img class="w-10 h-10 rounded-full border-2 border-teal-primary" src="https://ui-avatars.com/api/?name=B&background=random" alt="Avatar">
                        <img class="w-10 h-10 rounded-full border-2 border-teal-primary" src="https://ui-avatars.com/api/?name=C&background=random" alt="Avatar">
                    </div>
                    <div>
                        <div class="flex text-yellow-400 text-sm">
                            ★ ★ ★ ★ ★
                        </div>
                        <p class="text-xs text-gray-300">Dipercaya oleh ribuan profesional</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <!-- Image Grid -->
                <div class="relative">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-64 bg-gray-200 rounded-2xl overflow-hidden mt-8">
                            <img src="https://images.unsplash.com/photo-1592982537447-6f29402c40c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Training" class="w-full h-full object-cover">
                        </div>
                        <div class="h-64 bg-gray-200 rounded-2xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Meeting" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -z-10 -bottom-6 -left-6 w-32 h-32 bg-green-50 rounded-full"></div>
                </div>

                <!-- Text Content -->
                <div>
                    <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">TENTANG FR ACADEMY</span>
                    <h2 class="text-4xl font-bold text-teal-primary mt-4 mb-6 leading-tight">
                        Misi Kami: Pertanian yang Lebih Baik untuk <span class="text-green-accent">Masa Depan</span>
                    </h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Kami merancang program pelatihan komprehensif untuk meningkatkan keterampilan teknis dan manajerial para praktisi perkebunan. Didukung oleh instruktur berpengalaman dan fasilitas modern, kami berkomitmen untuk melahirkan agen perubahan yang dapat menerapkan praktik pertanian berkelanjutan dan mencapai efisiensi operasional yang optimal.
                    </p>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-accent mt-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-teal-primary">Instruktur Berpengalaman</h4>
                                <p class="text-sm text-gray-500">Belajar langsung dari praktisi ahli yang telah puluhan tahun berkecimpung di industri.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-accent mt-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-teal-primary">Fasilitas Modern</h4>
                                <p class="text-sm text-gray-500">Fasilitas simulasi dan praktik lapangan yang memadai untuk pembelajaran aplikatif.</p>
                            </div>
                        </li>
                    </ul>
                    
                    <a href="#" class="inline-flex items-center text-teal-primary font-semibold hover:text-green-accent transition">
                        Lebih Lanjut Tentang Kami 
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">PROGRAM PELATIHAN KAMI</span>
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary mt-4 mb-4">Solusi Pelatihan Komprehensif</h2>
                <p class="text-gray-600">FR Academy menawarkan berbagai spesialisasi untuk memenuhi kebutuhan industri kelapa sawit modern.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-orange-50/50 rounded-2xl p-8 transition-transform hover:-translate-y-2 duration-300 border border-orange-100">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-teal-primary mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary mb-3">Manajemen Agronomi</h3>
                    <p class="text-gray-600 text-sm mb-6">Pelatihan mendalam tentang pembibitan, perawatan tanaman, pemupukan, hingga teknik panen yang efisien dan ramah lingkungan.</p>
                    
                    <div class="rounded-xl overflow-hidden h-40 mb-4">
                        <img src="https://images.unsplash.com/photo-1592982537447-6f29402c40c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Agronomi" class="w-full h-full object-cover">
                    </div>
                    <a href="#" class="text-sm font-semibold text-teal-primary flex items-center hover:text-green-accent transition">
                        Pelajari lebih lanjut <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-blue-50/50 rounded-2xl p-8 transition-transform hover:-translate-y-2 duration-300 border border-blue-100">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-teal-primary mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary mb-3">Operasional Pabrik</h3>
                    <p class="text-gray-600 text-sm mb-6">Mempelajari standar operasional prosedur pengolahan kelapa sawit, manajemen K3, dan perawatan mesin pabrik.</p>
                    
                    <div class="rounded-xl overflow-hidden h-40 mb-4">
                        <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Pabrik" class="w-full h-full object-cover">
                    </div>
                    <a href="#" class="text-sm font-semibold text-teal-primary flex items-center hover:text-green-accent transition">
                        Pelajari lebih lanjut <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="bg-teal-50/50 rounded-2xl p-8 transition-transform hover:-translate-y-2 duration-300 border border-teal-100">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-teal-primary mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary mb-3">Kepemimpinan & Etika</h3>
                    <p class="text-gray-600 text-sm mb-6">Fokus pada pengembangan karakter, keterampilan kepemimpinan, dan penyelesaian masalah bagi calon manajer.</p>
                    
                    <div class="rounded-xl overflow-hidden h-40 mb-4">
                        <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Kepemimpinan" class="w-full h-full object-cover">
                    </div>
                    <a href="#" class="text-sm font-semibold text-teal-primary flex items-center hover:text-green-accent transition">
                        Pelajari lebih lanjut <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-teal-primary py-16 text-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 divide-y md:divide-y-0 md:divide-x divide-teal-700">
                <!-- Stat 1 -->
                <div class="text-center py-4">
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1">20<span class="text-green-accent text-4xl">k+</span></div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">SISWA LULUS</div>
                </div>
                <!-- Stat 2 -->
                <div class="text-center py-4">
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1">98<span class="text-green-accent text-4xl">%</span></div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">TINGKAT KEPUASAN</div>
                </div>
                <!-- Stat 3 -->
                <div class="text-center py-4">
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1">1,5<span class="text-green-accent text-4xl">k+</span></div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">FASILITATOR BERPENGALAMAN</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services / Testimonial Section -->
    <section class="py-24 bg-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-y-0 right-0 w-1/2 bg-teal-primary rounded-l-[100px] -z-10"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left: Accordion / Services -->
                <div>
                    <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">LAYANAN KAMI</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-teal-primary mt-4 mb-10 leading-tight">
                        Dari Bibit Hingga Panen — <br>Layanan Penuh Kami
                    </h2>
                    
                    <div class="space-y-4" x-data="{ selected: 1 }">
                        <!-- Accordion Item 1 -->
                        <div class="border-b border-gray-200 pb-4">
                            <button @click="selected = 1" class="flex justify-between items-center w-full text-left font-bold text-xl py-2 focus:outline-none" :class="selected === 1 ? 'text-green-accent' : 'text-gray-400 hover:text-teal-primary'">
                                <span>PROGRAM REGULER</span>
                                <svg class="w-6 h-6 transform transition-transform" :class="selected === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="selected === 1" x-collapse class="text-gray-600 mt-4 pr-8">
                                Program pelatihan dasar bagi karyawan baru maupun calon karyawan yang akan ditempatkan di berbagai kebun operasional.
                            </div>
                        </div>

                        <!-- Accordion Item 2 -->
                        <div class="border-b border-gray-200 pb-4">
                            <button @click="selected = 2" class="flex justify-between items-center w-full text-left font-bold text-xl py-2 focus:outline-none" :class="selected === 2 ? 'text-green-accent' : 'text-gray-400 hover:text-teal-primary'">
                                <span>PROGRAM NON-REGULER</span>
                                <svg class="w-6 h-6 transform transition-transform" :class="selected === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="selected === 2" x-collapse class="text-gray-600 mt-4 pr-8" style="display: none;">
                                Pelatihan khusus yang dirancang sesuai dengan kebutuhan spesifik divisi atau tantangan operasional terkini.
                            </div>
                        </div>

                        <!-- Accordion Item 3 -->
                        <div class="border-b border-gray-200 pb-4">
                            <button @click="selected = 3" class="flex justify-between items-center w-full text-left font-bold text-xl py-2 focus:outline-none" :class="selected === 3 ? 'text-green-accent' : 'text-gray-400 hover:text-teal-primary'">
                                <span>SERTIFIKASI & ASESMEN</span>
                                <svg class="w-6 h-6 transform transition-transform" :class="selected === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="selected === 3" x-collapse class="text-gray-600 mt-4 pr-8" style="display: none;">
                                Uji kompetensi dan pemberian sertifikasi standar industri perkebunan kelapa sawit nasional maupun internasional.
                            </div>
                        </div>

                        <!-- Accordion Item 4 -->
                        <div class="border-b border-gray-200 pb-4">
                            <button @click="selected = 4" class="flex justify-between items-center w-full text-left font-bold text-xl py-2 focus:outline-none" :class="selected === 4 ? 'text-green-accent' : 'text-gray-400 hover:text-teal-primary'">
                                <span>KLINIK KONSULTASI</span>
                                <svg class="w-6 h-6 transform transition-transform" :class="selected === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="selected === 4" x-collapse class="text-gray-600 mt-4 pr-8" style="display: none;">
                                Layanan pendampingan ahli bagi perkebunan swasta atau masyarakat untuk menyelesaikan masalah agronomi.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Testimonial & Image -->
                <div class="relative">
                    <div class="bg-white p-8 rounded-2xl shadow-xl relative z-10 lg:-ml-12 lg:mr-12 mb-8 md:mb-0">
                        <div class="text-green-accent mb-4">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"></path></svg>
                        </div>
                        <p class="text-xl font-medium text-teal-primary italic leading-relaxed mb-6">
                            "Pelatihan di FR Academy memberikan wawasan mendalam yang langsung dapat saya terapkan di lapangan. Dari teknik pemupukan hingga perlindungan tanaman, semuanya sangat aplikatif."
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=random" alt="Budi" class="w-12 h-12 rounded-full mr-4">
                                <div>
                                    <h4 class="font-bold text-teal-primary">Budi Santoso</h4>
                                    <p class="text-sm text-gray-500">Asisten Kepala, Divisi Agronomi</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 transition">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative rounded-2xl overflow-hidden h-96 w-full lg:absolute lg:top-12 lg:-right-4 lg:w-4/5 -z-0 hidden md:block">
                        <img src="https://images.unsplash.com/photo-1599839619722-39751411ea63?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Plantation House" class="w-full h-full object-cover">
                        <!-- Certified Badge -->
                        <div class="absolute bottom-6 left-6 bg-white px-4 py-2 rounded-lg flex items-center shadow-lg">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-accent mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Tersertifikasi</p>
                                <p class="text-sm font-bold text-teal-primary">ISO 9001:2015</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">ARTIKEL & PENGUMUMAN</span>
                    <h2 class="text-3xl font-bold text-teal-primary mt-2">Jelajahi Berita & Tips</h2>
                </div>
                <a href="/artikel" class="hidden md:inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    Lihat Semua Berita
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- News 1 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
                    <div class="relative h-48">
                        <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="News" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-bold text-teal-primary uppercase tracking-wider shadow">Berita</div>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            12 Sep 2026
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary mb-3 line-clamp-2 hover:text-green-accent transition">Pendaftaran Batch Terbaru Program Reguler Dibuka</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">FR Academy kembali membuka pendaftaran untuk program pelatihan reguler calon asisten agronomi dan pabrik kelapa sawit.</p>
                        <a href="#" class="text-green-accent font-semibold text-sm inline-flex items-center hover:text-green-600 transition">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- News 2 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
                    <div class="relative h-48">
                        <img src="https://images.unsplash.com/photo-1516937941344-00b4e0337589?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="News" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-bold text-teal-primary uppercase tracking-wider shadow">Riset</div>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            05 Sep 2026
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary mb-3 line-clamp-2 hover:text-green-accent transition">Cara Mencegah Hama Kumbang Tanduk</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">Panduan praktis terbaru dari tim peneliti FR Academy mengenai pencegahan dan pengendalian hama Oryctes rhinoceros.</p>
                        <a href="#" class="text-green-accent font-semibold text-sm inline-flex items-center hover:text-green-600 transition">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- News 3 -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
                    <div class="relative h-48">
                        <img src="https://images.unsplash.com/photo-1551836022-d5d88e9218df?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="News" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded text-xs font-bold text-teal-primary uppercase tracking-wider shadow">Prestasi</div>
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            28 Aug 2026
                        </div>
                        <h3 class="text-xl font-bold text-teal-primary mb-3 line-clamp-2 hover:text-green-accent transition">FR Academy Raih Penghargaan Lembaga Pelatihan Terbaik</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">Penganugerahan dari Kementerian Ketenagakerjaan atas komitmen nyata dalam mencetak SDM unggul kelapa sawit.</p>
                        <a href="#" class="text-green-accent font-semibold text-sm inline-flex items-center hover:text-green-600 transition">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="/artikel" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                    Lihat Semua Berita
                </a>
            </div>
        </div>
    </section>
</x-layout>
