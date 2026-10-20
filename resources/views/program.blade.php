<x-layout>
    <x-slot name="title">Program Pelatihan & Pengembangan - FR Academy</x-slot>

    <!-- ===================================================================== -->
    <!-- HERO SECTION (Atmospheric Dark Teal - Corporate Reference Match)    -->
    <!-- ===================================================================== -->
    <section class="relative min-h-[500px] md:min-h-[560px] pt-28 pb-14 overflow-hidden flex flex-col justify-between bg-teal-primary text-white">
        <!-- Background Image with atmospheric dark teal treatment -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/slide2.png') }}" alt="Program Pelatihan FR Academy" class="w-full h-full object-cover object-center scale-105" data-aos="zoom-out" data-aos-duration="2500">
            <div class="absolute inset-0 bg-gradient-to-t from-teal-primary via-teal-primary/85 to-[#0b292c]/90"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.06)_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>
        </div>

        <!-- Top Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-4 md:pt-8">
            <div class="max-w-4xl" data-aos="fade-up">
                <span class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-green-accent mb-4 font-mono">
                    <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                    PROGRAM & KURIKULUM
                </span>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.15] mb-6">
                    Sistem Pembinaan & Pengembangan Talenta Operasional Terpadu
                </h1>
                <p class="text-base sm:text-lg text-teal-100/90 max-w-3xl font-light leading-relaxed">
                    Mempersiapkan kader pemimpin perkebunan dan staf spesialis teknik melalui perpaduan intensif pembelajaran teori, simulasi fasilitas terpadu, dan praktik langsung di unit operasional lini depan.
                </p>
            </div>
        </div>

        <!-- Bottom Hero 3-Column Bar (Direct Reference Match) -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-8 border-t border-white/15">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 items-end text-sm text-teal-100/90 font-light">
                <div class="md:col-span-5 leading-relaxed">
                    <strong class="text-white font-semibold block mb-0.5">01. Program Reguler (Trainee)</strong>
                    Kaderisasi calon Asisten selama 6 bulan melalui jalur FAT Kebun, MAT Pabrik, TAT Traksi, AAT Administrasi, dan DMT Kilang Hilir.
                </div>
                <div class="md:col-span-5 leading-relaxed">
                    <strong class="text-white font-semibold block mb-0.5">02. Spesialisasi & Talent Management</strong>
                    Continuing Professional Development di 6 domain kejuruan teknikal serta akselerasi kepemimpinan berbasis metode 70:20:10.
                </div>
                <div class="md:col-span-2 md:text-right">
                    <a href="#reguler" class="inline-flex items-center gap-2 text-green-accent hover:text-white font-semibold transition-colors group">
                        <span>Jelajahi Jalur</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- STICKY TAB NAVIGATION (Corporate Minimalist)                         -->
    <!-- ===================================================================== -->
    <div x-data="{ activeTab: 'reguler' }" class="relative">
        <div class="sticky top-20 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center justify-center md:justify-start gap-2 py-3 overflow-x-auto no-scrollbar font-mono text-xs" aria-label="Program Tabs">
                    <button type="button"
                            @click="activeTab = 'reguler'; document.getElementById('reguler').scrollIntoView({behavior: 'smooth', block: 'start'})"
                            :class="activeTab === 'reguler' ? 'bg-teal-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>[ 01 ] Program Reguler (Trainee)</span>
                        <span :class="activeTab === 'reguler' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                              class="text-[10px] px-2 py-0.5 rounded-full font-bold">5 Jalur Karir</span>
                    </button>
                    <button type="button"
                            @click="activeTab = 'non-reguler'; document.getElementById('non-reguler').scrollIntoView({behavior: 'smooth', block: 'start'})"
                            :class="activeTab === 'non-reguler' ? 'bg-teal-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>[ 02 ] Program Non-Reguler (Spesialisasi)</span>
                        <span :class="activeTab === 'non-reguler' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                              class="text-[10px] px-2 py-0.5 rounded-full font-bold">6 Bidang Teknis</span>
                    </button>
                    <button type="button"
                            @click="activeTab = 'talent'; document.getElementById('talent').scrollIntoView({behavior: 'smooth', block: 'start'})"
                            :class="activeTab === 'talent' ? 'bg-teal-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>[ 03 ] Inisiatif & Talent Management</span>
                        <span :class="activeTab === 'talent' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300'"
                              class="text-[10px] px-2 py-0.5 rounded-full font-bold">Akselerasi</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Scroll Spy Observer -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('programScrollSpy', () => ({
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    this.activeTab = entry.target.id;
                                }
                            });
                        }, { rootMargin: '-20% 0px -65% 0px' });
                        
                        document.querySelectorAll('.scroll-section').forEach((section) => {
                            observer.observe(section);
                        });
                    }
                }));
            });
        </script>

        <div x-data="programScrollSpy">

            <!-- ===================================================================== -->
            <!-- SECTION 01: PROGRAM REGULER (EDITORIAL SHOWCASE)                    -->
            <!-- ===================================================================== -->
            <section id="reguler" class="scroll-section py-20 lg:py-24 bg-white dark:bg-gray-950 transition-colors duration-300 scroll-mt-20 border-b border-gray-200/80 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Split Header (Matches Reference) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start mb-12 lg:mb-16">
                        <div class="lg:col-span-5" data-aos="fade-up">
                            <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                                [ 01 / PROGRAM REGULER ]
                            </span>
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight">
                                Kaderisasi Calon Asisten & Onboarding Trainee
                            </h2>
                        </div>
                        <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light mb-6">
                                Program intensif berdurasi <strong>6 bulan</strong> dengan model ganda: pembekalan terstruktur (*In-Class Training*) di Pusat Pelatihan Terpadu Kebun Kubang, dilanjutkan penugasan lapangan intensif (*On-the-Job Training*) di unit operasional perkebunan, pabrik kelapa sawit, traksi, administrasi, dan kilang industri hilir.
                            </p>
                            <!-- Fast Jump Chips -->
                            <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
                                <span class="text-gray-400 dark:text-gray-500 mr-1">Jalur Posisi:</span>
                                <a href="#prog-fat" class="px-2.5 py-1 rounded bg-gray-100 dark:bg-gray-800 text-teal-primary dark:text-teal-300 hover:bg-teal-primary hover:text-white transition">FAT Kebun</a>
                                <a href="#prog-mat" class="px-2.5 py-1 rounded bg-gray-100 dark:bg-gray-800 text-teal-primary dark:text-teal-300 hover:bg-teal-primary hover:text-white transition">MAT Pabrik</a>
                                <a href="#prog-tat" class="px-2.5 py-1 rounded bg-gray-100 dark:bg-gray-800 text-teal-primary dark:text-teal-300 hover:bg-teal-primary hover:text-white transition">TAT Traksi</a>
                                <a href="#prog-aat" class="px-2.5 py-1 rounded bg-gray-100 dark:bg-gray-800 text-teal-primary dark:text-teal-300 hover:bg-teal-primary hover:text-white transition">AAT Administrasi</a>
                                <a href="#prog-dmt" class="px-2.5 py-1 rounded bg-gray-100 dark:bg-gray-800 text-teal-primary dark:text-teal-300 hover:bg-teal-primary hover:text-white transition">DMT Hilir</a>
                            </div>
                        </div>
                    </div>

                    <!-- 5 Trainee Pathways (Clean Editorial Blocks) -->
                    <div class="space-y-16 lg:space-y-24">

                        <!-- 1. FAT: Field Assistant Training (Kebun) -->
                        <div id="prog-fat" class="border border-gray-200/90 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-900/60 scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group">
                                    <img src="{{ asset('images/programs/fat_estate.jpg') }}" alt="Area Perkebunan Kelapa Sawit FAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                                    <div class="absolute top-5 left-5 z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-primary/95 text-white text-xs font-mono border border-teal-400/30">
                                            Lingkungan: Perkebunan Kelapa Sawit & Afdeling
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white z-10">
                                        <span class="text-xs font-mono text-green-accent uppercase tracking-wider block mb-1">DOKUMENTASI OPERASIONAL</span>
                                        <p class="text-xs text-gray-200 font-light">Praktik langsung supervisi ancak panen, sensus hama penyakit, dan kultur teknis agronomi kebun inti.</p>
                                    </div>
                                </div>

                                <!-- Content Column -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-primary dark:text-green-accent border border-teal-200 dark:border-teal-800">
                                                KODE: FAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider font-mono">
                                                Agronomi & Budidaya Perkebunan
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Field Assistant Training (FAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6 font-light">
                                            Program kaderisasi calon Asisten Kebun yang bertanggung jawab langsung atas operasional afdeling perkebunan. Peserta dilatih menguasai siklus hidup kelapa sawit, manajemen pemeliharaan, efektivitas tenaga panen, serta konsistensi target produksi TBS bermutu tinggi.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider font-mono">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Manajemen Blok Panen & Mutu Buah (TPH)</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Teknik Pemupukan & Pengendalian Gulma</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Pengendalian Hama & Penyakit (HPT)</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Kepemimpinan Lapangan & Hubungan Kerja</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 text-xs font-mono">
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Jalur Penempatan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Asisten Kebun (Field Assistant)</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block mb-0.5">Durasi Pembinaan:</span>
                                            <span class="font-bold text-teal-primary dark:text-green-accent">6 Bulan (In-Class & OJT)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. MAT: Mill Assistant Training (Pabrik PKS) -->
                        <div id="prog-mat" class="border border-gray-200/90 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-900/60 scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Content Column (Reversed on Desktop) -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between order-2 lg:order-1">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-primary dark:text-green-accent border border-teal-200 dark:border-teal-800">
                                                KODE: MAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider font-mono">
                                                Teknik Pengolahan & Pabrik Kelapa Sawit
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Mill Assistant Training (MAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6 font-light">
                                            Mempersiapkan calon Asisten Pabrik yang andal mengelola mesin industri pengolahan Tandan Buah Segar (TBS) menjadi Crude Palm Oil (CPO) dan Palm Kernel. Menitikberatkan pada rasio ekstraksi tinggi (OER/KER), penekanan *oil losses*, keselamatan ketat (K3), dan keandalan stasiun mesin.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider font-mono">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Stasiun Loading Ramp, Sterilizer & Thresher</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Pengoperasian Kempa Press & Stasiun Klarifikasi</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Boiler Bertekanan Tinggi & Turbin Uap PKS</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Pengelolaan Limbah Industri & Kepatuhan AMDAL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 text-xs font-mono">
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Jalur Penempatan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Asisten Pabrik / Mill Assistant</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block mb-0.5">Durasi Pembinaan:</span>
                                            <span class="font-bold text-teal-primary dark:text-green-accent">6 Bulan (In-Class & OJT)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group order-1 lg:order-2">
                                    <img src="{{ asset('images/programs/mat_mill.jpg') }}" alt="Pabrik Kelapa Sawit MAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                                    <div class="absolute top-5 right-5 z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-primary/95 text-white text-xs font-mono border border-teal-400/30">
                                            Lingkungan: Pabrik Kelapa Sawit (PKS)
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white z-10">
                                        <span class="text-xs font-mono text-green-accent uppercase tracking-wider block mb-1">DOKUMENTASI OPERASIONAL</span>
                                        <p class="text-xs text-gray-200 font-light">Stasiun pemrosesan TBS, ketel boiler biomassa, pembangkit listrik turbin uap, dan laboratorium mutu CPO.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. TAT: Traction Assistant Training (Traksi & Alat Berat) -->
                        <div id="prog-tat" class="border border-gray-200/90 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-900/60 scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group">
                                    <img src="{{ asset('images/programs/tat_traction.jpg') }}" alt="Workshop Traksi dan Bengkel TAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                                    <div class="absolute top-5 left-5 z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-primary/95 text-white text-xs font-mono border border-teal-400/30">
                                            Lingkungan: Workshop Traksi & Bengkel Alat Berat
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white z-10">
                                        <span class="text-xs font-mono text-green-accent uppercase tracking-wider block mb-1">DOKUMENTASI OPERASIONAL</span>
                                        <p class="text-xs text-gray-200 font-light">Bengkel sentral pemeliharaan dump truck armada buah, motor grader perawatan jalan, dan genset operasional.</p>
                                    </div>
                                </div>

                                <!-- Content Column -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-primary dark:text-green-accent border border-teal-200 dark:border-teal-800">
                                                KODE: TAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider font-mono">
                                                Traksi, Logistik & Heavy Equipment
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Traction Assistant Training (TAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6 font-light">
                                            Menyiapkan calon Asisten Traksi yang bertanggung jawab atas kelancaran evakuasi TBS dari kebun ke pabrik. Menguasai manajemen workshop, jadwal pemeliharaan armada truk, alat berat perawatan infrastruktur jalan kebun, serta efisiensi konsumsi bahan bakar (*Vehicle Running Account*).
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider font-mono">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Preventive Maintenance Dump Truck & Alat Berat</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Overhaul Mesin Diesel, Transmisi & Hidrolik</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Manajemen Efisiensi BBM & Rasio VRA</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Standardisasi Safety Bengkel & Defensive Driving</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 text-xs font-mono">
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Jalur Penempatan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Asisten Traksi & Workshop</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block mb-0.5">Durasi Pembinaan:</span>
                                            <span class="font-bold text-teal-primary dark:text-green-accent">6 Bulan (In-Class & OJT)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. AAT: Administration Assistant Training (Kantor Tata Usaha) -->
                        <div id="prog-aat" class="border border-gray-200/90 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-900/60 scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Content Column (Reversed on Desktop) -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between order-2 lg:order-1">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-primary dark:text-green-accent border border-teal-200 dark:border-teal-800">
                                                KODE: AAT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider font-mono">
                                                Tata Kelola Keuangan & Administrasi Estate
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Administration Assistant Training (AAT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6 font-light">
                                            Program pembentukan calon Kepala Tata Usaha (KTU) yang mengendalikan urat nadi administrasi, finansial, dan logistik perkebunan. Peserta dilatih menguasai rekonsiliasi buku kas, verifikasi penggajian ribuan tenaga kerja, pengelolaan gudang aset, serta pelaporan biaya produksi sesuai standar tata kelola First Resources.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider font-mono">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Sistem Akuntansi Biaya Perkebunan & Kas Bank</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Verifikasi Payroll, Premi Panen & Lembur Karyawan</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Manajemen Gudang Material & Stock Opname Berkala</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Penyusunan Anggaran (Budgeting) & Kesiapan Audit</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 text-xs font-mono">
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Jalur Penempatan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Kepala Tata Usaha (KTU)</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block mb-0.5">Durasi Pembinaan:</span>
                                            <span class="font-bold text-teal-primary dark:text-green-accent">6 Bulan (In-Class & OJT)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group order-1 lg:order-2">
                                    <img src="{{ asset('images/programs/aat_admin.jpg') }}" alt="Sentra Administrasi Estate AAT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                                    <div class="absolute top-5 right-5 z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-primary/95 text-white text-xs font-mono border border-teal-400/30">
                                            Lingkungan: Kantor Tata Usaha (KTU) & Sentra Finansial
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white z-10">
                                        <span class="text-xs font-mono text-green-accent uppercase tracking-wider block mb-1">DOKUMENTASI OPERASIONAL</span>
                                        <p class="text-xs text-gray-200 font-light">Pengelolaan pembukuan digital perkebunan, rekonsiliasi biaya produksi unit, dan sistem logistik pergudangan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. DMT: Downstream Management Trainee (Kilang Hilir & Refinery) -->
                        <div id="prog-dmt" class="border border-gray-200/90 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-900/60 scroll-mt-28" data-aos="fade-up">
                            <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                                <!-- Photo Column -->
                                <div class="lg:col-span-6 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group">
                                    <img src="{{ asset('images/programs/dmt_downstream.jpg') }}" alt="Kilang Industri Hilir Refinery DMT" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                                    <div class="absolute top-5 left-5 z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-teal-primary/95 text-white text-xs font-mono border border-teal-400/30">
                                            Lingkungan: Kompleks Kilang Refinery, Fraksinasi & Oleokimia
                                        </span>
                                    </div>
                                    <div class="absolute bottom-5 left-5 right-5 text-white z-10">
                                        <span class="text-xs font-mono text-green-accent uppercase tracking-wider block mb-1">DOKUMENTASI OPERASIONAL</span>
                                        <p class="text-xs text-gray-200 font-light">Fasilitas pemurnian CPO berteknologi tinggi, menara fraksinasi, dan tangki penyimpanan ekspor.</p>
                                    </div>
                                </div>

                                <!-- Content Column -->
                                <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2.5 py-1 rounded text-xs font-mono font-bold bg-teal-50 dark:bg-teal-950 text-teal-primary dark:text-green-accent border border-teal-200 dark:border-teal-800">
                                                KODE: DMT
                                            </span>
                                            <span class="text-xs font-bold text-green-accent uppercase tracking-wider font-mono">
                                                Industri Pengolahan Hilir & Nilai Tambah
                                            </span>
                                        </div>
                                        <h3 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white mb-4 leading-tight">
                                            Downstream Management Trainee (DMT)
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base leading-relaxed mb-6 font-light">
                                            Program akselerasi khusus bagi calon insinyur dan supervisor di kawasan industri pengolahan hilir sawit. Berfokus pada proses konversi CPO menjadi produk bernilai tambah tinggi seperti minyak goreng (RBD Olein), margarin (RBD Stearin), biodiesel, hingga derivatif oleokimia dengan sistem otomasi industri modern.
                                        </p>

                                        <!-- Key Competencies List -->
                                        <div class="space-y-2 mb-8">
                                            <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider font-mono">Kompetensi Inti yang Dibina:</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-gray-700 dark:text-gray-300">
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Proses Refining, Bleaching & Deodorizing (RBD)</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Sistem Fraksinasi Terkontrol & Produksi Oleokimia</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Otomasi Pabrik PLC, DCS & Pemipaan Kilang</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-accent mt-1 shrink-0"></span>
                                                    <span>Standar Mutu Pangan Dunia (FSSC 22000, Halal, RSPO)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Metadata Strip -->
                                    <div class="pt-5 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 text-xs font-mono">
                                        <div>
                                            <span class="text-gray-400 block mb-0.5">Jalur Penempatan:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">Calon Supervisor / Engineer Kilang Hilir</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-gray-400 block mb-0.5">Durasi Pembinaan:</span>
                                            <span class="font-bold text-teal-primary dark:text-green-accent">6 Bulan (In-Class & OJT)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>


            <!-- ===================================================================== -->
            <!-- SECTION 02: PROGRAM NON-REGULER (INTERACTIVE TECHNICAL MATRIX)       -->
            <!-- ===================================================================== -->
            <section id="non-reguler" 
                     x-data="{ activeDomain: 0 }" 
                     class="scroll-section py-20 lg:py-24 bg-gray-50/70 dark:bg-gray-900/60 bg-tech-grid border-b border-gray-200/80 dark:border-gray-800 transition-colors duration-300 scroll-mt-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Split Header (Matches Reference) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start mb-12 lg:mb-16">
                        <div class="lg:col-span-5" data-aos="fade-up">
                            <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                                [ 02 / PROGRAM SPESIALISASI ]
                            </span>
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight">
                                Continuing Professional Development & Technical Mastery
                            </h2>
                        </div>
                        <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light">
                                Pelatihan teknis modular berdurasi spesifik untuk meningkatkan kapabilitas praktisi aktif (Mandor, Asisten, Operator, dan Mekanik), menjawab kendala teknis lapangan terkini, serta menstandardisasi metode kerja unggulan di seluruh wilayah operasional First Resources Group.
                            </p>
                        </div>
                    </div>

                    @php
                        $domains = [
                            [
                                'id' => 0,
                                'code' => 'ESTATE',
                                'title' => 'Teknikal Estate',
                                'badge' => 'Agronomi & Praktik Budidaya',
                                'desc' => 'Peningkatan standar teknis agronomi kebun sawit, mulai dari manajemen perawatan Tanaman Belum Menghasilkan (TBM), pemupukan berbasis analisa daun, hingga klinik penanganan hama penyakit.',
                                'image' => asset('images/programs/fat_estate.jpg'),
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
                        
                        <!-- Left Navigation: Interactive Domain List (4 Cols) -->
                        <div class="lg:col-span-4 space-y-2.5">
                            @foreach($domains as $index => $item)
                                <button type="button" 
                                        @click="activeDomain = {{ $index }}"
                                        :class="activeDomain === {{ $index }} 
                                            ? 'bg-teal-primary text-white shadow-md border-teal-primary' 
                                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700/80'"
                                        class="w-full text-left p-4 rounded-xl border transition-all duration-200 flex items-center justify-between group cursor-pointer">
                                    <div class="flex items-center gap-3.5">
                                        <div :class="activeDomain === {{ $index }} ? 'bg-white/20 text-white' : 'bg-teal-50 dark:bg-teal-950 text-teal-primary dark:text-green-accent'"
                                             class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-xs font-mono shrink-0">
                                            0{{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm tracking-tight leading-snug">
                                                {{ $item['title'] }}
                                            </h4>
                                            <p :class="activeDomain === {{ $index }} ? 'text-teal-200' : 'text-gray-500 dark:text-gray-400'"
                                               class="text-xs mt-0.5 font-light">
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

                        <!-- Right Showcase Panel (8 Cols) -->
                        <div class="lg:col-span-8">
                            @foreach($domains as $index => $item)
                                <div x-show="activeDomain === {{ $index }}"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 sm:p-8 lg:p-10 shadow-sm"
                                     style="{{ $index !== 0 ? 'display: none;' : '' }}">
                                    
                                    <!-- Photo Header Banner -->
                                    <div class="relative h-48 sm:h-60 rounded-xl overflow-hidden mb-8 border border-gray-200/80 dark:border-gray-800 shadow-inner">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover object-center">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                                        <div class="absolute bottom-5 left-5 right-5 text-white">
                                            <span class="inline-block px-2.5 py-1 rounded bg-teal-primary/95 text-green-accent text-xs font-mono font-bold uppercase tracking-wider mb-2 border border-teal-500/30">
                                                {{ $item['badge'] }}
                                            </span>
                                            <h3 class="text-xl sm:text-2xl font-black text-white">
                                                {{ $item['title'] }}
                                            </h3>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed mb-6 font-light">
                                        {{ $item['desc'] }}
                                    </p>

                                    <!-- Metadata Strip -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200/80 dark:border-gray-700/80 text-xs mb-8 font-mono">
                                        <div>
                                            <span class="text-gray-400 dark:text-gray-500 block mb-0.5">Target Peserta Program:</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $item['target'] }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-gray-500 block mb-0.5">Format & Metode:</span>
                                            <span class="font-bold text-teal-primary dark:text-green-accent">{{ $item['format'] }}</span>
                                        </div>
                                    </div>

                                    <!-- Module / Workshop Cards Matrix -->
                                    <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4 font-mono">
                                        Modul Pelatihan & Workshop Terkait:
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($item['modules'] as $mod)
                                            <div class="p-4 rounded-lg bg-gray-50/70 dark:bg-gray-800/50 border border-gray-200/80 dark:border-gray-800 flex flex-col justify-between hover:border-green-accent/50 transition-colors">
                                                <h5 class="text-sm font-bold text-gray-900 dark:text-white mb-1">
                                                    {{ $mod['name'] }}
                                                </h5>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-light">
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


            <!-- ===================================================================== -->
            <!-- SECTION 03: TALENT MANAGEMENT & INISIATIF STRATEGIS                   -->
            <!-- ===================================================================== -->
            <section id="talent" class="scroll-section py-20 lg:py-24 bg-white dark:bg-gray-950 transition-colors duration-300 scroll-mt-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Split Header (Matches Reference) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start mb-12 lg:mb-16">
                        <div class="lg:col-span-5" data-aos="fade-up">
                            <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                                [ 03 / KADERISASI & TALENT ]
                            </span>
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight">
                                Akselerasi Kepemimpinan & Manajemen Talenta
                            </h2>
                        </div>
                        <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light">
                                Menjamin keberlanjutan regenerasi kepemimpinan operasional First Resources Group melalui pembinaan terarah bagi staf berpotensi tinggi (*high-potential talent*). Memadukan pendampingan klinis langsung di afdeling, transfer pengalaman eksekutif senior, serta ajang tahunan inovasi operasional.
                            </p>
                        </div>
                    </div>

                    <!-- 4 Solid Dark Teal Strategic Pillars (01 to 04 - Direct Reference Match) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16" data-aos="fade-up">
                        <!-- Block 01 -->
                        <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group rounded-xl">
                            <div>
                                <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                                    Klinik Kinerja Mandor
                                </h3>
                                <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                                    Pendampingan intensif di ancak panen guna membedah hambatan kerja harian, meningkatkan mutu buah di TPH, dan memperkuat wibawa supervisi mandor.
                                </p>
                            </div>
                            <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                                <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Supervisi Lapangan</span>
                                <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">01</span>
                            </div>
                        </div>

                        <!-- Block 02 -->
                        <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group rounded-xl">
                            <div>
                                <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                                    Executive Mentoring & SME
                                </h3>
                                <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                                    Bimbingan intensif 1-on-1 bersama Subject Matter Experts (SME), Controller, hingga jajaran Direksi operasional First Resources Group.
                                </p>
                            </div>
                            <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                                <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Transfer Ahli</span>
                                <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">02</span>
                            </div>
                        </div>

                        <!-- Block 03 -->
                        <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group rounded-xl">
                            <div>
                                <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                                    Formula Pembelajaran 70:20:10
                                </h3>
                                <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                                    Penguasaan kompetensi riil melalui 70% penugasan lapangan yang menantang, 20% pembinaan mentor praktisi, dan 10% modul formal akademi.
                                </p>
                            </div>
                            <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                                <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Ekosistem Belajar</span>
                                <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">03</span>
                            </div>
                        </div>

                        <!-- Block 04 -->
                        <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group rounded-xl">
                            <div>
                                <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                                    Learning Festival Tahunan
                                </h3>
                                <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                                    Ajang tahunan kompetisi inovasi operasional, pertukaran *best practices* antar region kebun (Riau, Kalbar, Kaltim), dan penganugerahan insan berprestasi.
                                </p>
                            </div>
                            <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                                <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Budaya Inovasi</span>
                                <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">04</span>
                            </div>
                        </div>
                    </div>

                    <!-- Documentary Showcase Banner (Direct Reference Match) -->
                    <div class="relative overflow-hidden rounded-xl border border-gray-200/90 dark:border-gray-800 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                        <img src="{{ asset('images/slide4.png') }}" alt="Pembinaan Karakter & Kompetensi Insan Perkebunan" class="w-full h-[320px] sm:h-[400px] object-cover object-center">
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 text-white">
                            <div>
                                <span class="text-xs font-semibold text-green-accent tracking-widest uppercase block mb-1 font-mono">DOKUMENTASI INTERNAL</span>
                                <h3 class="text-lg sm:text-xl font-bold">Transfer Pengalaman Langsung dari Praktisi & Pimpinan Operasional Aktif</h3>
                            </div>
                            <a href="/career" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-accent hover:bg-green-600 text-white text-xs font-bold font-mono transition-colors shadow">
                                <span>Lihat Peluang Karir</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </section>
            
        </div>
    </div>

</x-layout>
