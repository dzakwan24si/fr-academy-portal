<x-layout>
    <x-slot name="title">Program Pelatihan & Pengembangan - FR Academy</x-slot>

    <!-- Hero Section -->
    <section class="relative h-[350px] md:h-[400px] pt-20 overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/slide2.png') }}" alt="FR Academy Programs" class="w-full h-full object-cover object-center" data-aos="zoom-out" data-aos-duration="2000">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-green-accent font-bold tracking-widest uppercase text-sm mb-4 block">
                PROGRAM KAMI
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">
                Program Pelatihan & <span class="text-green-accent">Pengembangan</span>
            </h1>
            <p class="text-lg md:text-xl text-teal-100 max-w-2xl mx-auto font-light">
                Pusat pengembangan kompetensi dan pembentukan karakter kerja unggul di industri kelapa sawit.
            </p>
        </div>
    </section>

    <!-- Sticky Tab Navigation (Alpine.js) -->
    <div x-data="{ activeTab: 'reguler' }" class="relative">
        <div class="sticky top-20 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center justify-center md:justify-start gap-2 py-3.5 overflow-x-auto no-scrollbar" aria-label="Tabs">
                    <button type="button"
                            @click="activeTab = 'reguler'; document.getElementById('reguler').scrollIntoView({behavior: 'smooth', block: 'start'})"
                            :class="activeTab === 'reguler' ? 'bg-teal-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2.5 whitespace-nowrap cursor-pointer">
                        <span>Program Reguler (Trainee)</span>
                        <span :class="activeTab === 'reguler' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                              class="text-xs px-2 py-0.5 rounded-full font-bold">5 Jalur Karir</span>
                    </button>
                    <button type="button"
                            @click="activeTab = 'non-reguler'; document.getElementById('non-reguler').scrollIntoView({behavior: 'smooth', block: 'start'})"
                            :class="activeTab === 'non-reguler' ? 'bg-teal-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2.5 whitespace-nowrap cursor-pointer">
                        <span>Program Non-Reguler (Spesialisasi)</span>
                        <span :class="activeTab === 'non-reguler' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                              class="text-xs px-2 py-0.5 rounded-full font-bold">6 Bidang Teknis</span>
                    </button>
                    <button type="button"
                            @click="activeTab = 'talent'; document.getElementById('talent').scrollIntoView({behavior: 'smooth', block: 'start'})"
                            :class="activeTab === 'talent' ? 'bg-teal-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center gap-2.5 whitespace-nowrap cursor-pointer">
                        <span>Inisiatif & Talent Management</span>
                        <span :class="activeTab === 'talent' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                              class="text-xs px-2 py-0.5 rounded-full font-bold">Akselerasi</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Scroll Spy Observer Setup -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('scrollSpy', () => ({
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    this.activeTab = entry.target.id;
                                }
                            });
                        }, { rootMargin: '-20% 0px -70% 0px' });
                        
                        document.querySelectorAll('.scroll-section').forEach((section) => {
                            observer.observe(section);
                        });
                    }
                }));
            });
        </script>

        <div x-data="scrollSpy">

            <!-- ========================================== -->
            <!-- SECTION 1: PROGRAM REGULER (EDITORIAL SHOWCASE) -->
            <!-- ========================================== -->
            <section id="reguler" class="scroll-section py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300 scroll-mt-20 border-b border-gray-200 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Section Header -->
                    <div class="max-w-3xl mb-12" data-aos="fade-up">
                        <span class="text-green-accent font-bold tracking-wider text-xs uppercase block mb-1">PROGRAM KADERISASI & ONBOARDING CALON ASISTEN</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight">
                            Program Reguler (Trainee Onboarding)
                        </h2>
                        <p class="mt-3 text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                            Program pengembangan intensif selama <strong>6 bulan</strong> dengan model *In-Class Training* dan *On-the-Job Training (OJT)* langsung di unit operasional. Membekali lulusan baru menjadi staf dan asisten yang siap memimpin di lini depan perkebunan dan pabrik.
                        </p>
                    </div>

                    <!-- Quick Anchor Filter Chips -->
                    <div class="flex flex-wrap items-center gap-2 mb-16" data-aos="fade-up">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">Lompat ke jalur:</span>
                        <a href="#prog-fat" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 text-teal-primary dark:text-teal-300 border border-gray-200 dark:border-gray-700 hover:border-green-accent hover:text-green-accent transition">
                            🌱 Kebun (FAT)
                        </a>
                        <a href="#prog-mat" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 text-teal-primary dark:text-teal-300 border border-gray-200 dark:border-gray-700 hover:border-green-accent hover:text-green-accent transition">
                            🏭 Pabrik PKS (MAT)
                        </a>
                        <a href="#prog-tat" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 text-teal-primary dark:text-teal-300 border border-gray-200 dark:border-gray-700 hover:border-green-accent hover:text-green-accent transition">
                            🚜 Traksi & Bengkel (TAT)
                        </a>
                        <a href="#prog-aat" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 text-teal-primary dark:text-teal-300 border border-gray-200 dark:border-gray-700 hover:border-green-accent hover:text-green-accent transition">
                            📑 Administrasi KTU (AAT)
                        </a>
                        <a href="#prog-dmt" class="text-xs font-medium px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 text-teal-primary dark:text-teal-300 border border-gray-200 dark:border-gray-700 hover:border-green-accent hover:text-green-accent transition">
                            🔬 Kilang Downstream (DMT)
                        </a>
                    </div>

                    <!-- Alternating Story Cards with Environmental Photographs -->
                    <div class="space-y-16 lg:space-y-24">

                        <!-- 1. FAT: Field Assistant Training (Kebun) -->
                        <div id="prog-fat" class="bg-white dark:bg-gray-800/90 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group">
                                    <img src="{{ asset('images/programs/fat_estate.png') }}" alt="Area Perkebunan Kelapa Sawit FAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent lg:hidden"></div>
                                    <!-- Floating Environment Badge -->
                                    <div class="absolute top-5 left-5 z-10">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-teal-primary/90 backdrop-blur-md text-white text-xs font-semibold border border-white/20 shadow-md">
                                            <span>🌱</span> Lingkungan: Perkebunan Kelapa Sawit & Afdeling
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white lg:hidden z-10">
                                        <p class="text-xs text-gray-200">Praktik langsung pengelolaan blok tanam, proteksi tanaman, dan supervisi pemanen di kebun inti.</p>
                                    </div>
                                </div>
                                <!-- Content Column -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-black tracking-wider bg-green-100 dark:bg-green-950/80 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800">
                                                KODE: FAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider">
                                                Agronomi & Budidaya Kebun
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Field Assistant Training (FAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6">
                                            Program kaderisasi calon Asisten Kebun yang bertanggung jawab langsung atas operasional afdeling perkebunan. Peserta dilatih menguasai siklus hidup kelapa sawit, manajemen pemeliharaan, efektivitas tenaga panen, serta konsistensi target produksi TBS bermutu tinggi.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2.5 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Manajemen Blok Panen & Mutu Buah (TPH)</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Teknik Pemupukan & Pengendalian Gulma</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Pengendalian Hama & Penyakit (HPT)</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Kepemimpinan Lapangan & Hubungan Kerja</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-700/80 flex flex-wrap items-center justify-between gap-4 text-xs">
                                        <div>
                                            <span class="text-gray-400 block">Jalur Posisi Lulusan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Asisten Kebun (Field Assistant)</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block">Metode Pembelajaran:</span>
                                            <span class="font-bold text-teal-primary dark:text-teal-300">6 Bulan (In-Class & OJT Lapangan)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. MAT: Mill Assistant Training (Pabrik Kelapa Sawit) -->
                        <div id="prog-mat" class="bg-white dark:bg-gray-800/90 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Content Column (Reversed on Desktop) -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between order-2 lg:order-1">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-black tracking-wider bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                KODE: MAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider">
                                                Teknik Pengolahan & Pabrik Kelapa Sawit
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Mill Assistant Training (MAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6">
                                            Mempersiapkan calon Asisten Pabrik yang andal mengelola mesin industri pengolahan Tandan Buah Segar (TBS) menjadi Crude Palm Oil (CPO) dan Palm Kernel. Menitikberatkan pada rasio ekstraksi tinggi (OER/KER), penekanan *oil losses*, keselamatan ketat (K3), dan keandalan stasiun mesin.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2.5 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Stasiun Loading Ramp, Sterilizer & Thresher</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Pengoperasian Kempa Press & Stasiun Klarifikasi</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Boiler Bertekanan Tinggi & Turbin Uap PKS</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Pengelolaan Limbah Industri & Kepatuhan AMDAL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-700/80 flex flex-wrap items-center justify-between gap-4 text-xs">
                                        <div>
                                            <span class="text-gray-400 block">Jalur Posisi Lulusan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Asisten Pabrik / Mill Assistant</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block">Metode Pembelajaran:</span>
                                            <span class="font-bold text-teal-primary dark:text-teal-300">6 Bulan (Teori PKS & OJT Stasiun Pabrik)</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group order-1 lg:order-2">
                                    <img src="{{ asset('images/programs/mat_mill.jpg') }}" alt="Pabrik Kelapa Sawit MAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent lg:hidden"></div>
                                    <!-- Floating Environment Badge -->
                                    <div class="absolute top-5 right-5 z-10">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-teal-primary/90 backdrop-blur-md text-white text-xs font-semibold border border-white/20 shadow-md">
                                            <span>🏭</span> Lingkungan: Pabrik Kelapa Sawit (PKS)
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white lg:hidden z-10">
                                        <p class="text-xs text-gray-200">Stasiun pengolahan buah, turbin tenaga uap, ketel boiler, dan laboratorium analisis mutu minyak kelapa sawit.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. TAT: Traction Assistant Training (Traksi & Alat Berat) -->
                        <div id="prog-tat" class="bg-white dark:bg-gray-800/90 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group">
                                    <img src="{{ asset('images/programs/tat_traction.jpg') }}" alt="Workshop Traksi dan Armada Alat Berat TAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent lg:hidden"></div>
                                    <!-- Floating Environment Badge -->
                                    <div class="absolute top-5 left-5 z-10">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-teal-primary/90 backdrop-blur-md text-white text-xs font-semibold border border-white/20 shadow-md">
                                            <span>🚜</span> Lingkungan: Workshop Bengkel, Dump Truck & Alat Berat
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white lg:hidden z-10">
                                        <p class="text-xs text-gray-200">Bengkel sentral perawatan dump truck pengangkut buah, motor grader perawatan jalan, dan genset operasional.</p>
                                    </div>
                                </div>
                                <!-- Content Column -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-black tracking-wider bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                KODE: TAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider">
                                                Traksi, Logistik & Heavy Equipment
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Traction Assistant Training (TAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6">
                                            Menyiapkan calon Asisten Traksi yang bertanggung jawab atas kelancaran evakuasi TBS dari kebun ke pabrik. Menguasai manajemen workshop, jadwal pemeliharaan armada truk, alat berat perawatan infrastruktur jalan kebun, serta efisiensi konsumsi bahan bakar (*Vehicle Running Account*).
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2.5 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Preventive Maintenance Dump Truck & Alat Berat</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Overhaul Mesin Diesel, Transmisi & Hidrolik</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Manajemen Efisiensi BBM & Rasio VRA</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Standardisasi Safety Bengkel & Defensive Driving</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-700/80 flex flex-wrap items-center justify-between gap-4 text-xs">
                                        <div>
                                            <span class="text-gray-400 block">Jalur Posisi Lulusan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Asisten Traksi & Workshop</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block">Metode Pembelajaran:</span>
                                            <span class="font-bold text-teal-primary dark:text-teal-300">6 Bulan (Workshop Teori & Praktik Lapangan)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. AAT: Administration Assistant Training (Kantor Tata Usaha) -->
                        <div id="prog-aat" class="bg-white dark:bg-gray-800/90 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Content Column (Reversed on Desktop) -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between order-2 lg:order-1">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-black tracking-wider bg-purple-100 dark:bg-purple-950/80 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                KODE: AAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider">
                                                Tata Kelola Keuangan & Administrasi Estate
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Administration Assistant Training (AAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6">
                                            Program pembentukan calon Kepala Tata Usaha (KTU) yang mengendalikan urat nadi administrasi, finansial, dan logistik perkebunan. Peserta dilatih menguasai rekonsiliasi buku kas, verifikasi penggajian ribuan tenaga kerja, pengelolaan gudang aset, serta pelaporan biaya produksi sesuai standar tata kelola First Resources.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2.5 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Sistem Akuntansi Biaya Perkebunan & Kas Bank</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Verifikasi Payroll, Premi Panen & Lembur Karyawan</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Manajemen Gudang Material & Stock Opname Berkala</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Penyusunan Anggaran (Budgeting) & Kesiapan Audit</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-700/80 flex flex-wrap items-center justify-between gap-4 text-xs">
                                        <div>
                                            <span class="text-gray-400 block">Jalur Posisi Lulusan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Kepala Tata Usaha (KTU)</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block">Metode Pembelajaran:</span>
                                            <span class="font-bold text-teal-primary dark:text-teal-300">6 Bulan (Simulasi Finansial & OJT Kantor KTU)</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group order-1 lg:order-2">
                                    <img src="{{ asset('images/programs/aat_admin.jpg') }}" alt="Sentra Administrasi Estate AAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent lg:hidden"></div>
                                    <!-- Floating Environment Badge -->
                                    <div class="absolute top-5 right-5 z-10">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-teal-primary/90 backdrop-blur-md text-white text-xs font-semibold border border-white/20 shadow-md">
                                            <span>📑</span> Lingkungan: Kantor Tata Usaha (KTU) & Sentra Finansial
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white lg:hidden z-10">
                                        <p class="text-xs text-gray-200">Pengelolaan pembukuan digital, rekonsiliasi biaya produksi unit, dan sistem logistik gudang kebun.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. DMT: Downstream Management Trainee (Kilang Hilir & Refinery) -->
                        <div id="prog-dmt" class="bg-white dark:bg-gray-800/90 rounded-3xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group">
                                    <img src="{{ asset('images/programs/dmt_downstream.jpg') }}" alt="Kilang Industri Hilir Refinery DMT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent lg:hidden"></div>
                                    <!-- Floating Environment Badge -->
                                    <div class="absolute top-5 left-5 z-10">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-teal-primary/90 backdrop-blur-md text-white text-xs font-semibold border border-white/20 shadow-md">
                                            <span>🔬</span> Lingkungan: Kompleks Kilang Refinery, Fraksinasi & Oleokimia
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white lg:hidden z-10">
                                        <p class="text-xs text-gray-200">Fasilitas kilang pemurnian CPO berteknologi tinggi, menara fraksinasi fraksi cair/padat, dan tangki penyimpanan ekspor.</p>
                                    </div>
                                </div>
                                <!-- Content Column -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-black tracking-wider bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                                KODE: DMT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider">
                                                Industri Pengolahan Hilir & Nilai Tambah
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Downstream Management Trainee (DMT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6">
                                            Program akselerasi khusus bagi calon insinyur dan supervisor di kawasan industri pengolahan hilir sawit. Berfokus pada proses konversi CPO menjadi produk bernilai tambah tinggi seperti minyak goreng (RBD Olein), margarin (RBD Stearin), biodiesel, hingga derivatif oleokimia dengan sistem otomasi industri modern.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2.5 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Proses Refining, Bleaching & Deodorizing (RBD)</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Sistem Fraksinasi Terkontrol & Produksi Oleokimia</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Otomasi Pabrik PLC, DCS & Pemipaan Kilang</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent shrink-0"></span>
                                                    <span>Standar Mutu Pangan Dunia (FSSC 22000, Halal, RSPO)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-700/80 flex flex-wrap items-center justify-between gap-4 text-xs">
                                        <div>
                                            <span class="text-gray-400 block">Jalur Posisi Lulusan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Supervisor / Engineer Kilang Hilir</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block">Metode Pembelajaran:</span>
                                            <span class="font-bold text-teal-primary dark:text-teal-300">6 Bulan (Laboratorium Proses & OJT Plant)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <!-- ========================================== -->
            <!-- SECTION 2: PROGRAM NON-REGULER (INTERACTIVE DOMAIN EXPLORER) -->
            <!-- ========================================== -->
            <section id="non-reguler" 
                     x-data="{ activeDomain: 0 }" 
                     class="scroll-section py-20 bg-white dark:bg-gray-950 transition-colors duration-300 scroll-mt-20 border-b border-gray-200 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Section Header -->
                    <div class="max-w-3xl mb-12" data-aos="fade-up">
                        <span class="text-green-accent font-bold tracking-wider text-xs uppercase block mb-1">CONTINUING PROFESSIONAL DEVELOPMENT</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight">
                            Program Non-Reguler (Pelatihan Spesialisasi)
                        </h2>
                        <p class="mt-3 text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                            Pelatihan teknis modular berdurasi spesifik untuk meningkatkan kapabilitas praktisi yang sedang bertugas, menjawab kendala teknis lapangan terkini, serta standarisasi metode kerja unggulan.
                        </p>
                    </div>

                    @php
                        $domains = [
                            [
                                'id' => 0,
                                'code' => 'ESTATE',
                                'title' => 'Teknikal Estate',
                                'badge' => 'Agronomi & Praktik Budidaya',
                                'desc' => 'Peningkatan standar teknis agronomi kebun sawit, mulai dari manajemen perawatan Tanaman Belum Menghasilkan (TBM), pemupukan berbasis analisa daun, hingga klinik penanganan hama penyakit.',
                                'image' => asset('images/programs/fat_estate.png'),
                                'target' => 'Mandor, Mandor 1, Asisten Kebun, Askep Estate',
                                'format' => 'Klinik Lapangan, Workshop Singkat, Mentoring Lapangan',
                                'modules' => [
                                    ['name' => 'Klinik Kinerja Mandor', 'highlight' => 'Optimasi supervisi regu kerja & mutu harian'],
                                    ['name' => 'Estate Coaching Program', 'highlight' => 'Pendampingan problem solving agronomi'],
                                    ['name' => 'Pengelolaan TBM', 'highlight' => 'Kastrasi, sanitasi, dan pembentukan tajuk optimal'],
                                    ['name' => 'Klinik HPT & Proteksi Tanaman', 'highlight' => 'Pengendalian ulat api, kumbang tanduk & ganoderma'],
                                    ['name' => 'Manajemen Pemupukan Efektif', 'highlight' => 'Aplikasi 4T (Tepat Jenis, Dosis, Waktu, Cara)'],
                                    ['name' => 'Standardisasi Panen & Mutu TPH', 'highlight' => 'Penekanan restan buah & eliminasi brondolan']
                                ]
                            ],
                            [
                                'id' => 1,
                                'code' => 'MILL',
                                'title' => 'Teknikal Mill',
                                'badge' => 'Keandalan & Efisiensi Pabrik PKS',
                                'desc' => 'Program penguatan kompetensi teknis bagi tim operasional pabrik kelapa sawit guna menjaga kontinuitas pengolahan TBS, memaksimalkan rendemen minyak (OER), dan mencegah downtime.',
                                'image' => asset('images/programs/mat_mill.jpg'),
                                'target' => 'Operator Stasiun, Mandor Pabrik, Asisten Pabrik, Masinis',
                                'format' => 'Praktik Stasiun Mesin, Bedah Kasus Losses, Kalibrasi',
                                'modules' => [
                                    ['name' => 'Basic Mechanic for Operators', 'highlight' => 'Dasar mekanikal & troubleshooting stasiun pabrik'],
                                    ['name' => 'Mill Mentoring Program', 'highlight' => 'Pendampingan langsung praktisi senior PKS'],
                                    ['name' => 'Manajemen Operasional Boiler', 'highlight' => 'Efisiensi pembakaran biomassa & keamanan uap'],
                                    ['name' => 'Water Treatment & Limbah WWTP', 'highlight' => 'Pemurnian air boiler & baku mutu limbah cair'],
                                    ['name' => 'Total Productive Maintenance (TPM)', 'highlight' => 'Pencegahan kerusakan mendadak mesin utama'],
                                    ['name' => 'Klinik Kinerja Pabrik', 'highlight' => 'Audit kehilangan minyak (oil losses reduction)']
                                ]
                            ],
                            [
                                'id' => 2,
                                'code' => 'TRACTION',
                                'title' => 'Teknikal Traksi & Alat Berat',
                                'badge' => 'Workshop, Keandalan Mesin & Armada',
                                'desc' => 'Standardisasi pemeliharaan preventif armada dump truck dan alat berat infrastruktur kebun, tata kelola bengkel unit, serta efisiensi penggunaan bahan bakar solar.',
                                'image' => asset('images/programs/tat_traction.jpg'),
                                'target' => 'Mekanik Bengkel, Operator Alat Berat, Driver DT, Asisten Traksi',
                                'format' => 'Hands-on Workshop, Uji Emisi & Sistem Hidrolik, Defensive Driving',
                                'modules' => [
                                    ['name' => 'Manajemen Workshop & Bengkel Unit', 'highlight' => 'Standardisasi 5R dan alur kerja bengkel perkebunan'],
                                    ['name' => 'Operation & Maintenance Dump Truck', 'highlight' => 'Pemeriksaan harian P2H dan perawatan berkala'],
                                    ['name' => 'Perawatan & Sinkronisasi Genset', 'highlight' => 'Stabilitas suplai kelistrikan perumahan & operasional'],
                                    ['name' => 'Defensive Driving untuk Driver Sawit', 'highlight' => 'Keselamatan berkendara di kontur jalan tanah berbukit'],
                                    ['name' => 'Overhaul Dump Truck Mesin Euro 4', 'highlight' => 'Diagnosis sistem injeksi common rail & emisi'],
                                    ['name' => 'Pengelolaan Suku Cadang & Ban', 'highlight' => 'Rotasi ban, vulkanisir, dan efisiensi konsumsi BBM']
                                ]
                            ],
                            [
                                'id' => 3,
                                'code' => 'DOWNSTREAM',
                                'title' => 'Teknikal Downstream',
                                'badge' => 'Kilang Hilir, Refinery & Oleokimia',
                                'desc' => 'Penguasaan teknologi fraksinasi, pemurnian minyak sawit, serta sistem otomatisasi instrumen terintegrasi untuk menghasilkan produk fraksi cair dan padat berstandar ekspor.',
                                'image' => asset('images/programs/dmt_downstream.jpg'),
                                'target' => 'Operator Kilang, Instrument Technician, Quality Control, Supervisor Plant',
                                'format' => 'Simulasi DCS/SCADA, Audit Mutu Produk, Kalibrasi PLC',
                                'modules' => [
                                    ['name' => 'Operation & Maintenance Pompa Industri', 'highlight' => 'Perawatan mechanical seal dan pompa sentrifugal'],
                                    ['name' => 'Refinery & Fractionation System', 'highlight' => 'Pengendalian titik kristalisasi dan kejernihan minyak'],
                                    ['name' => 'Sistem Kalibrasi & Otomasi PLC', 'highlight' => 'Troubleshooting sensor suhu, flow rate & pressure'],
                                    ['name' => 'Operasional Kilang Oleokimia', 'highlight' => 'Proses splitting, hidrogenasi, dan distilasi asam lemak'],
                                    ['name' => 'Quality Control & Food Safety', 'highlight' => 'Analisa parameter FFA, IV, DOBI, dan warna Lovibond'],
                                    ['name' => 'Standardisasi Sertifikasi Ekspor', 'highlight' => 'Kepatuhan standar FSSC 22000, Halal, Kosher, RSPO']
                                ]
                            ],
                            [
                                'id' => 4,
                                'code' => 'ADMIN_K3',
                                'title' => 'Administrasi & K3/Sustainability',
                                'badge' => 'Tata Kelola, K3 & Lingkungan Hidup',
                                'desc' => 'Program pelatihan yang memastikan kepatuhan regulasi lingkungan, tanggap darurat kebakaran hutan/lahan, keselamatan kerja seluruh karyawan, serta tata kelola administrasi legal.',
                                'image' => asset('images/programs/aat_admin.jpg'),
                                'target' => 'Staf Tata Usaha, Tim Tanggap Darurat, Officer K3/Sustain, Pamong TPA',
                                'format' => 'Simulasi Pemadaman Karhutla, Audit Lapangan, K3 First Aid',
                                'modules' => [
                                    ['name' => 'Warehouse & Inventory Management', 'highlight' => 'Pencatatan keluar-masuk pupuk, pestisida & material'],
                                    ['name' => 'Tanggap Darurat Karhutla', 'highlight' => 'Pola formasi regu pemadam, pompa air & embung kebun'],
                                    ['name' => 'K3 & First Aid Pertolongan Pertama', 'highlight' => 'Sertifikasi P3K dan penanganan darurat kecelakaan'],
                                    ['name' => 'Mentoring Administrasi KTU', 'highlight' => 'Penyusunan laporan bulanan unit dan akuntansi biaya'],
                                    ['name' => 'Pelatihan Pamong Penitipan Anak (TPA)', 'highlight' => 'Standar gizi, sanitasi, dan edukasi anak karyawan kebun'],
                                    ['name' => 'Kepatuhan Audit ISPO / RSPO', 'highlight' => 'Dokumentasi ketelusuran (traceability) dan konservasi']
                                ]
                            ],
                            [
                                'id' => 5,
                                'code' => 'LEADERSHIP',
                                'title' => 'Manajerial & Kepemimpinan',
                                'badge' => 'Soft Skills, Supervisi & Karakter Insan FR',
                                'desc' => 'Pengembangan kapasitas kepemimpinan bagi seluruh level hierarki staf perkebunan untuk membangun tim yang solid, adaptif, solutif terhadap problem operasional, dan berorientasi hasil.',
                                'image' => asset('images/slide4.png'),
                                'target' => 'Asisten, Askep, Manager, Koordinator Departemen',
                                'format' => 'Executive Workshop, Case Studies, Behavioral Coaching',
                                'modules' => [
                                    ['name' => '7 Habits of Highly Effective People', 'highlight' => 'Membangun karakter kerja berorientasi proaktif dan win-win'],
                                    ['name' => 'Leader As Coach untuk Supervisi', 'highlight' => 'Teknik menggali potensi bawahan tanpa instruksi searah'],
                                    ['name' => 'Supervisory Management untuk Asisten', 'highlight' => 'Keterampilan supervisi regu mandor dan manajemen waktu'],
                                    ['name' => 'Fundamental Leadership untuk Askep', 'highlight' => 'Pengelolaan unit afdeling majemuk dan koordinasi lintas sektor'],
                                    ['name' => 'Strategic Leadership untuk Manager', 'highlight' => 'Pengambilan keputusan strategis dan pengelolaan budget unit'],
                                    ['name' => 'Problem Solving & Decision Making (PSDM)', 'highlight' => 'Metode analisa akar masalah operasional berbasis data riil']
                                ]
                            ]
                        ];
                    @endphp

                    <!-- Dynamic Layout: Side Domain Selector + Active Detail Showcase -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        
                        <!-- Left Navigation: Interactive Domain List (5 Cols) -->
                        <div class="lg:col-span-4 space-y-3">
                            @foreach($domains as $index => $item)
                                <button type="button" 
                                        @click="activeDomain = {{ $index }}"
                                        :class="activeDomain === {{ $index }} 
                                            ? 'bg-teal-primary text-white shadow-md border-teal-primary' 
                                            : 'bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 border-gray-200 dark:border-gray-800'"
                                        class="w-full text-left p-4 rounded-2xl border transition-all duration-200 flex items-center justify-between group cursor-pointer">
                                    <div class="flex items-center gap-3.5">
                                        <div :class="activeDomain === {{ $index }} ? 'bg-white/20 text-white' : 'bg-teal-50 dark:bg-teal-950 text-teal-primary dark:text-teal-300'"
                                             class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ substr($item['code'], 0, 3) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm tracking-tight leading-snug">
                                                {{ $item['title'] }}
                                            </h4>
                                            <p :class="activeDomain === {{ $index }} ? 'text-teal-200' : 'text-gray-500 dark:text-gray-400'"
                                               class="text-xs mt-0.5">
                                                {{ $item['badge'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <svg :class="activeDomain === {{ $index }} ? 'translate-x-1 text-green-accent' : 'text-gray-400 group-hover:translate-x-1'"
                                         class="w-4 h-4 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @endforeach
                        </div>

                        <!-- Right Showcase Panel (7 Cols) -->
                        <div class="lg:col-span-8">
                            @foreach($domains as $index => $item)
                                <div x-show="activeDomain === {{ $index }}"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-3"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="bg-gray-50/80 dark:bg-gray-900/80 rounded-3xl border border-gray-200 dark:border-gray-800 p-6 sm:p-8 lg:p-10 shadow-sm"
                                     style="{{ $index !== 0 ? 'display: none;' : '' }}">
                                    
                                    <!-- Photo Header Banner -->
                                    <div class="relative h-48 sm:h-64 rounded-2xl overflow-hidden mb-8 border border-gray-200/80 dark:border-gray-700/80 shadow-inner">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover object-center">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                                        <div class="absolute bottom-5 left-5 right-5 text-white">
                                            <span class="inline-block px-2.5 py-1 rounded bg-teal-primary/90 text-green-accent text-xs font-bold uppercase tracking-wider mb-1.5 border border-teal-500/30">
                                                {{ $item['badge'] }}
                                            </span>
                                            <h3 class="text-xl sm:text-2xl font-black text-white">
                                                {{ $item['title'] }}
                                            </h3>
                                        </div>
                                    </div>

                                    <!-- Description & Target -->
                                    <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed mb-6">
                                        {{ $item['desc'] }}
                                    </p>

                                    <!-- Context Strip -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 text-xs mb-8">
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Target Peserta Program:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $item['target'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Format & Metode:</span>
                                            <span class="font-bold text-teal-primary dark:text-teal-300">{{ $item['format'] }}</span>
                                        </div>
                                    </div>

                                    <!-- Module / Workshop Cards Matrix -->
                                    <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">
                                        Modul Pelatihan & Workshop Terkait:
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($item['modules'] as $mod)
                                            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 flex flex-col justify-between">
                                                <h5 class="text-sm font-bold text-gray-900 dark:text-white mb-1">
                                                    {{ $mod['name'] }}
                                                </h5>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                                    {{ $mod['highlight'] }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </section>


            <!-- ========================================== -->
            <!-- SECTION 3: TALENT MANAGEMENT & INISIATIF PROGRAM -->
            <!-- ========================================== -->
            <section id="talent" class="scroll-section py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300 scroll-mt-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 p-8 sm:p-12 lg:p-16 shadow-sm">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                            
                            <!-- Left Column: Content -->
                            <div class="lg:col-span-7" data-aos="fade-right">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 dark:bg-green-950/60 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800 text-xs font-bold uppercase tracking-wider mb-4">
                                    <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                                    Kaderisasi & Akselerasi Pimpinan
                                </div>
                                <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight mb-5 leading-tight">
                                    Inisiatif Program & Talent Management
                                </h2>
                                <p class="text-gray-600 dark:text-gray-300 text-base leading-relaxed mb-8">
                                    Menjamin keberlanjutan kepemimpinan First Resources Group melalui pembinaan terarah bagi staf berpotensi tinggi (*high-potential talent*). Memadukan pendampingan klinis langsung di afdeling, transfer pengalaman dari para eksekutif senior, serta ajang tahunan apresiasi inovasi.
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Pillar 1 -->
                                    <div class="p-5 rounded-2xl border border-gray-100 dark:border-gray-700/80 bg-gray-50/70 dark:bg-gray-900/50">
                                        <div class="w-10 h-10 rounded-xl bg-teal-primary/10 text-teal-primary dark:text-teal-300 flex items-center justify-center mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0 1 18 0z"></path></svg>
                                        </div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1.5">Klinik Kinerja Mandor</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Pendampingan di lapangan guna membedah hambatan kerja harian, meningkatkan kedisiplinan mutu ancak panen, serta efektivitas supervisi tenaga kerja.</p>
                                    </div>
                                    
                                    <!-- Pillar 2 -->
                                    <div class="p-5 rounded-2xl border border-gray-100 dark:border-gray-700/80 bg-gray-50/70 dark:bg-gray-900/50">
                                        <div class="w-10 h-10 rounded-xl bg-teal-primary/10 text-teal-primary dark:text-teal-300 flex items-center justify-center mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1.5">Learning Festival</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Pekan tahunan kompetisi inovasi operasional, pertukaran *best practices* antar region kebun (Riau, Kalbar, Kaltim), dan penganugerahan insan berprestasi.</p>
                                    </div>

                                    <!-- Pillar 3 -->
                                    <div class="p-5 rounded-2xl border border-gray-100 dark:border-gray-700/80 bg-gray-50/70 dark:bg-gray-900/50">
                                        <div class="w-10 h-10 rounded-xl bg-teal-primary/10 text-teal-primary dark:text-teal-300 flex items-center justify-center mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 1 1 0 5.292M15 21H3v-1a6 6 0 0 1 12 0v1zm0 0h6v-1a6 6 0 0 0-9-5.197M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"></path></svg>
                                        </div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1.5">Executive Mentoring & SME</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Bimbingan intensif 1-on-1 bersama Subject Matter Experts (SME), Controller, hingga jajaran Direksi operasional First Resources Group.</p>
                                    </div>

                                    <!-- Pillar 4 -->
                                    <div class="p-5 rounded-2xl border border-gray-100 dark:border-gray-700/80 bg-gray-50/70 dark:bg-gray-900/50">
                                        <div class="w-10 h-10 rounded-xl bg-teal-primary/10 text-teal-primary dark:text-teal-300 flex items-center justify-center mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"></path></svg>
                                        </div>
                                        <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1.5">Metodologi 70:20:10</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Penguasaan kompetensi riil melalui 70% penugasan lapangan yang menantang, 20% pembinaan mentor, dan 10% modul formal akademi.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Visual Showcase Frame -->
                            <div class="lg:col-span-5" data-aos="fade-left">
                                <div class="relative rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-lg">
                                    <img src="{{ asset('images/slide4.png') }}" alt="Pembelajaran FR Academy" class="w-full h-96 sm:h-[460px] object-cover object-center">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                                    <div class="absolute bottom-6 left-6 right-6 text-white">
                                        <span class="inline-block px-3 py-1 rounded-lg bg-teal-primary/90 backdrop-blur-md text-xs font-bold text-green-accent mb-2 border border-teal-500/30 shadow">
                                            First Resources Center of Excellence
                                        </span>
                                        <h4 class="text-lg font-bold text-white mb-1">
                                            Transfer Pengetahuan dari Ahli Lapangan
                                        </h4>
                                        <p class="text-xs text-gray-200 leading-relaxed">
                                            Dibimbing langsung oleh praktisi internal kompeten dari level Manager, General Manager, hingga Direktur operasional.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
            
        </div>
    </div>

</x-layout>
