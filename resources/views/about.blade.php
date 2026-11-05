<x-layout>
    @php
        $aboutHeroBadge = \App\Models\AppSetting::get('about_hero_badge', 'TENTANG KAMI');
        $aboutHeroTitle = \App\Models\AppSetting::get('about_hero_title', 'Center of Excellence dalam Membentuk Insan Agribisnis Berkelas Dunia');
        $aboutHeroCol1 = \App\Models\AppSetting::get('about_hero_col1', 'Sejak 1996, First Resources Academy menjadi kawah candradimuka dalam membina kompetensi teknis, disiplin, dan kepemimpinan operasional perkebunan.');
        $aboutHeroCol2 = \App\Models\AppSetting::get('about_hero_col2', 'Mengintegrasikan kurikulum berstandar industri, pengajar praktisi internal, dan 300 hektare sarana simulasi kebun terpadu di Kampar, Riau.');
        $aboutHeroBg = \App\Models\AppSetting::getImageUrl('about_hero_bg', 'images/welcomefr.jpg');

        $aboutProfileBadge = \App\Models\AppSetting::get('about_profile_badge', '[ 01 / PROFIL & MISI ]');
        $aboutProfileTitle = \App\Models\AppSetting::get('about_profile_title', 'Membentuk Karakter & Ketangguhan Generasi Pemimpin Agribisnis');
        $aboutProfileDesc = \App\Models\AppSetting::get('about_profile_desc', 'First Resources Academy didirikan sebagai komitmen strategis First Resources Group dalam memastikan regenerasi sumber daya manusia yang handal, adaptif, dan berintegritas tinggi. Kami tidak hanya mentransfer keahlian teknis budidaya agronomi dan pengolahan pabrik kelapa sawit, tetapi menanamkan etos kerja unggul, kepatuhan SOP, dan kepemimpinan operasional berwawasan masa depan.');
        $aboutProfileImg = \App\Models\AppSetting::getImageUrl('about_profile_img', 'images/slide4.png');
        $aboutProfileImgCaption = \App\Models\AppSetting::get('about_profile_img_caption', 'Pelatihan Kelas & Pembekalan K3 Staf Operasional Perkebunan');
        $aboutProfileImgLocation = \App\Models\AppSetting::get('about_profile_img_location', 'Pusat Pelatihan Kebun Kubang, Kampar');

        $aboutPhilosophyBadge = \App\Models\AppSetting::get('about_philosophy_badge', '[ 02 / FILOSOFI & SKALA ]');
        $aboutPhilosophyTitle = \App\Models\AppSetting::get('about_philosophy_title', 'Lebih dari Sekadar Pusat Pelatihan — Kami Membentuk Sikap Kerja, Ketajaman Analisis, dan Kepemimpinan Nyata di Lapangan.');
    @endphp

    <!-- HERO SECTION (Inspired by Reference Top Section) -->
    <section class="relative min-h-[500px] md:min-h-[560px] pt-28 pb-14 overflow-hidden flex flex-col justify-between bg-teal-primary text-white">
        <!-- Background Image with atmospheric dark teal treatment -->
        <div class="absolute inset-0 z-0">
            <img src="{{ $aboutHeroBg }}" alt="Tentang FR Academy" class="w-full h-full object-cover object-center scale-105" data-aos="zoom-out" data-aos-duration="2500">
            <div class="absolute inset-0 bg-gradient-to-t from-teal-primary via-teal-primary/85 to-[#0b292c]/90"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.06)_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>
        </div>

        <!-- Top Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-4 md:pt-8">
            <div class="max-w-4xl" data-aos="fade-up">
                <span class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-green-accent mb-4 font-mono">
                    <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                    {{ $aboutHeroBadge }}
                </span>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.15] mb-6">
                    {{ $aboutHeroTitle }}
                </h1>
            </div>
        </div>

        <!-- Bottom Hero 3-Column Bar (Direct Reference Match) -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-8 border-t border-white/15">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 items-end text-sm text-teal-100/90 font-light">
                <div class="md:col-span-5 leading-relaxed">
                    {{ $aboutHeroCol1 }}
                </div>
                <div class="md:col-span-5 leading-relaxed">
                    {{ $aboutHeroCol2 }}
                </div>
                <div class="md:col-span-2 md:text-right">
                    <a href="#profile" class="inline-flex items-center gap-2 text-green-accent hover:text-white font-semibold transition-colors group">
                        <span>Jelajahi Profil</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: PROFIL & MISI (Who We Are - Reference Layout) -->
    <section id="profile" class="py-20 lg:py-24 bg-white dark:bg-gray-950 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Split Header -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start mb-12 lg:mb-16">
                <div class="lg:col-span-5" data-aos="fade-up">
                    <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                        {{ $aboutProfileBadge }}
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight">
                        {{ $aboutProfileTitle }}
                    </h2>
                </div>
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light">
                        {{ $aboutProfileDesc }}
                    </p>
                </div>
            </div>

            <!-- Wide Corporate Photo (Matches Reference) -->
            <div class="relative overflow-hidden rounded-xl border border-gray-200/90 dark:border-gray-800 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                <img src="{{ $aboutProfileImg }}" alt="{{ $aboutProfileImgCaption }}" class="w-full h-[320px] sm:h-[440px] lg:h-[500px] object-cover object-center">
                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 text-white">
                    <div>
                        <span class="text-xs font-semibold text-green-accent tracking-widest uppercase block mb-1">DOKUMENTASI INTERNAL</span>
                        <h3 class="text-lg sm:text-xl font-bold">{{ $aboutProfileImgCaption }}</h3>
                    </div>
                    <span class="text-xs text-gray-300 font-mono">{{ $aboutProfileImgLocation }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: STATS & STATEMENT HEADLINE (Technical Dot Grid Accent - Reference Layout) -->
    <section 
        x-data="{
            hasTriggered: false,
            stat1: 0,
            stat2a: 0,
            stat2b: 0,
            stat2c: 0,
            stat3: 0,
            init() {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.hasTriggered) {
                            this.hasTriggered = true;
                            this.animateNumbers();
                        }
                    });
                }, { threshold: 0.25 });
                observer.observe(this.$el);
            },
            animateNumbers() {
                this.stat1 = 0;
                this.stat2a = 0;
                this.stat2b = 0;
                this.stat2c = 0;
                this.stat3 = 0;

                const duration = 1800;
                const startTime = performance.now();
                const easeOutQuart = (x) => 1 - Math.pow(1 - x, 4);

                const step = (now) => {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = easeOutQuart(progress);

                    this.stat1 = Math.round(eased * 300);
                    this.stat2a = Math.round(eased * 70);
                    this.stat2b = Math.round(eased * 20);
                    this.stat2c = Math.round(eased * 10);
                    this.stat3 = Math.round(eased * 100);

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    } else {
                        this.stat1 = 300;
                        this.stat2a = 70;
                        this.stat2b = 20;
                        this.stat2c = 10;
                        this.stat3 = 100;
                    }
                };

                requestAnimationFrame(step);
            }
        }"
        class="py-20 lg:py-28 bg-gray-50/70 dark:bg-gray-900/60 bg-tech-grid border-y border-gray-200/80 dark:border-gray-800 transition-colors duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Center Statement Headline -->
            <div class="max-w-4xl mx-auto text-center mb-16 lg:mb-20" data-aos="fade-up">
                <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-4 font-mono">
                    {{ $aboutPhilosophyBadge }}
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-snug">
                    {{ $aboutPhilosophyTitle }}
                </h2>
            </div>

            <!-- 3 Stat Blocks with Thin Dividers (Direct Reference Match) -->
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200 dark:divide-gray-800 text-center" data-aos="fade-up" data-aos-delay="100">
                <!-- Stat 1 -->
                <div class="py-8 md:py-4 px-6">
                    <div class="text-5xl sm:text-6xl font-black text-teal-primary dark:text-green-accent tracking-tight mb-3 tabular-nums">
                        <span x-text="stat1">300</span><span class="text-green-accent dark:text-teal-400 text-4xl sm:text-5xl">+</span> <span class="text-2xl font-bold">Ha</span>
                    </div>
                    <div class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-1">
                        Pusat Pelatihan & Lahan Praktik
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-light max-w-xs mx-auto">
                        Fasilitas simulasi mandiri agronomi, pembibitan, dan kultur teknis lapangan.
                    </p>
                </div>

                <!-- Stat 2 -->
                <div class="py-8 md:py-4 px-6">
                    <div class="text-5xl sm:text-6xl font-black text-teal-primary dark:text-green-accent tracking-tight mb-3 font-mono tabular-nums">
                        <span x-text="stat2a">70</span><span class="text-gray-400 dark:text-gray-600">:</span><span x-text="stat2b">20</span><span class="text-gray-400 dark:text-gray-600">:</span><span x-text="stat2c">10</span>
                    </div>
                    <div class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-1">
                        Formula Pembelajaran Terpadu
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-light max-w-xs mx-auto">
                        Integrasi 70% praktik langsung, 20% mentoring praktisi, dan 10% teori kelas.
                    </p>
                </div>

                <!-- Stat 3 -->
                <div class="py-8 md:py-4 px-6">
                    <div class="text-5xl sm:text-6xl font-black text-teal-primary dark:text-green-accent tracking-tight mb-3 tabular-nums">
                        <span x-text="stat3">100</span><span class="text-green-accent dark:text-teal-400 text-4xl sm:text-5xl">%</span>
                    </div>
                    <div class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-1">
                        Pengajar Praktisi Internal
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-light max-w-xs mx-auto">
                        Dibimbing langsung oleh Direktur, Controller, dan Manager operasional aktif.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: METODOLOGI (4 DARK TEAL BLOCKS 01 - 04 - Reference Layout) -->
    <section class="py-20 lg:py-24 bg-white dark:bg-gray-950 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Split Header -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start mb-12 lg:mb-16">
                <div class="lg:col-span-5" data-aos="fade-up">
                    <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                        [ 03 / METODOLOGI PEMBINAAN ]
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight">
                        Bagaimana Kami Membina Talenta
                    </h2>
                </div>
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light">
                        Kurikulum First Resources Academy dirancang secara komprehensif untuk menyeimbangkan penguasaan keahlian teknis operasional dengan kematangan kepemimpinan, kepatuhan keselamatan kerja, dan integritas tinggi.
                    </p>
                </div>
            </div>

            <!-- 4 Dark Teal Blocks Grid (Matches Reference Blocks 01-04) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="200">
                <!-- Block 01 -->
                <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group">
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                            Experiential Learning
                        </h3>
                        <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                            70% porsi pembelajaran difokuskan pada praktik langsung di kebun simulasi 300 hektare dan pabrik kelapa sawit (PKS) untuk menguasai realitas kerja nyata.
                        </p>
                    </div>
                    <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                        <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Porsi 70%</span>
                        <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">01</span>
                    </div>
                </div>

                <!-- Block 02 -->
                <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group">
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                            Social Mentoring & Coaching
                        </h3>
                        <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                            20% melalui pendampingan intensif dari jajaran Direktur, Controller, dan Manager yang bertindak sebagai Subject Matter Expert (SME).
                        </p>
                    </div>
                    <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                        <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Porsi 20%</span>
                        <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">02</span>
                    </div>
                </div>

                <!-- Block 03 -->
                <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group">
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                            Formal Learning & SOP
                        </h3>
                        <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                            10% pembekalan konsep agronomi, engineering, administrasi tata usaha, serta standar keselamatan kerja (K3) berbasis modul LMS modern.
                        </p>
                    </div>
                    <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                        <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Porsi 10%</span>
                        <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">03</span>
                    </div>
                </div>

                <!-- Block 04 -->
                <div class="bg-teal-primary text-white p-8 flex flex-col justify-between min-h-[340px] border border-teal-800/80 hover:border-green-accent transition-colors duration-300 group">
                    <div>
                        <h3 class="text-xl font-bold mb-3 text-white group-hover:text-green-accent transition-colors">
                            Discipline & Character
                        </h3>
                        <p class="text-sm text-teal-100/85 leading-relaxed font-light">
                            Pembinaan fisik, mental, dan etika kepemimpinan asrama untuk membentuk staf operasional yang berdaya tahan tinggi, jujur, dan berintegritas.
                        </p>
                    </div>
                    <div class="pt-8 border-t border-white/10 flex justify-between items-end">
                        <span class="text-xs font-mono uppercase tracking-wider text-teal-300">Integritas</span>
                        <span class="text-4xl sm:text-5xl font-black text-white/90 group-hover:text-green-accent transition-colors font-mono">04</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5: TIMELINE / HISTORI PERJALANAN (Matches Reference Section "From Risk Expertise To Integrated Solutions") -->
    <section class="py-20 lg:py-24 bg-gray-50/70 dark:bg-gray-900/60 transition-colors duration-300 border-y border-gray-200/80 dark:border-gray-800"
             x-data="{
                 activeYear: '1996',
                 years: ['1996', '2002', '2010'],
                 currentIndex: 0,
                 autoplay: true,
                 timer: null,
                 startAutoplay() {
                     this.stopAutoplay();
                     this.timer = setInterval(() => {
                         if (this.autoplay) {
                             this.nextYear();
                         }
                     }, 5000);
                 },
                 stopAutoplay() {
                     if (this.timer) clearInterval(this.timer);
                 },
                 selectYear(yr) {
                     this.activeYear = yr;
                     this.currentIndex = this.years.indexOf(yr);
                     this.startAutoplay();
                 },
                 nextYear() {
                     this.currentIndex = (this.currentIndex + 1) % this.years.length;
                     this.activeYear = this.years[this.currentIndex];
                 },
                 isPassed(yr) {
                     return this.years.indexOf(yr) < this.currentIndex;
                 },
                 get progressWidth() {
                     if (this.activeYear === '1996') return '0%';
                     if (this.activeYear === '2002') return '50%';
                     if (this.activeYear === '2010') return '100%';
                     return '0%';
                 },
                 timeline: {
                     '1996': {
                         stage: 'Fase 01 — Pondasi Awal',
                         title: 'Pusat Pendidikan & Latihan Perkebunan (PPLP)',
                         location: 'Kebun Meridian (PT. MSSP)',
                         desc: 'Pusat pelatihan pertama didirikan di Kebun Meridian dengan nama Pusat Pendidikan dan Latihan Perkebunan (PPLP) untuk meletakkan pondasi kompetensi agronomi dan pembinaan disiplin insan perkebunan grup.',
                         image: '{{ asset('images/programs/fat_estate.png') }}',
                         caption: 'Inspeksi mutu agronomi dan pembibitan awal di areal perkebunan.'
                     },
                     '2002': {
                         stage: 'Fase 02 — Ekspansi Fasilitas',
                         title: 'Relokasi ke Kebun CLP',
                         location: 'Kebun CLP',
                         desc: 'Memindahkan operasional pelatihan ke Kebun CLP dengan bangunan gedung, asrama, dan fasilitas workshop yang lebih memadai guna mengakomodasi kebutuhan pelatihan yang meningkat.',
                         image: '{{ asset('images/slide5.png') }}',
                         caption: 'Pembekalan teori kelas dan penguatan etos kedisiplinan kerja.'
                     },
                     '2010': {
                         stage: 'Fase 03 — Center of Excellence',
                         title: 'Transformasi Menjadi FR Academy Terpadu',
                         location: 'Kebun Kubang (PT. PSG), Kampar, Riau',
                         desc: 'Berpindah ke Kebun Kubang (PT. PSG), Kampar, Riau. Secara resmi berganti nama menjadi FR Academy dan dikukuhkan sebagai Center of Excellence terpadu dengan lahan praktik seluas 300 hektare.',
                         image: '{{ asset('images/welcomefr.jpg') }}',
                         caption: 'Pusat Pelatihan Terpadu FR Academy di Kebun Kubang, Kampar, Riau.'
                     }
                 }
             }"
             x-init="startAutoplay()"
             @mouseenter="autoplay = false"
             @mouseleave="autoplay = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                <!-- Left Column: Editorial & Scrubber (Reference Matching) -->
                <div class="lg:col-span-6" data-aos="fade-right">
                    <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                        [ 04 / HISTORI PERJALANAN ]
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight mb-6">
                        Dari Pusat Pelatihan Hingga Center of Excellence
                    </h2>

                    <!-- Dynamic Milestone Content with Smooth Transitions -->
                    <div class="min-h-[220px] relative">
                        <template x-for="yr in years" :key="yr">
                            <div x-show="activeYear === yr"
                                 x-transition:enter="transition ease-out duration-500 transform"
                                 x-transition:enter-start="opacity-0 translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200 transform absolute inset-0 pointer-events-none"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2">
                                <div class="flex items-center gap-2 text-xs font-semibold text-green-accent uppercase tracking-wider mb-2">
                                    <span class="w-6 h-[2px] bg-green-accent"></span>
                                    <span x-text="timeline[yr].stage"></span>
                                </div>
                                <h3 class="text-2xl sm:text-3xl font-bold text-teal-primary dark:text-white mb-2 leading-tight" x-text="timeline[yr].title"></h3>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-3 font-medium">
                                    <svg class="w-4 h-4 text-green-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span x-text="timeline[yr].location"></span>
                                </div>
                                <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed font-light" x-text="timeline[yr].desc"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Horizontal Scrubber Line (Animated connecting track and glowing aura) -->
                    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-800">
                        <div class="relative flex items-center justify-between">
                            <!-- Background Track Line -->
                            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-gray-200 dark:bg-gray-700/80 rounded-full"></div>
                            
                            <!-- Active Animated Progress Bar -->
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-gradient-to-r from-teal-primary via-green-accent to-emerald-400 rounded-full transition-all duration-700 ease-out shadow-[0_0_8px_rgba(89,163,56,0.5)]"
                                 :style="'width: ' + progressWidth"></div>
                            
                            <!-- 1996 -->
                            <button type="button" 
                                    @click="selectYear('1996')"
                                    class="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none">
                                <div class="relative flex items-center justify-center">
                                    <!-- Glowing Aura Rings (Matches Reference screenshot) -->
                                    <span x-show="activeYear === '1996'" 
                                          x-transition:enter="transition ease-out duration-300"
                                          x-transition:enter-start="opacity-0 scale-50"
                                          x-transition:enter-end="opacity-100 scale-100"
                                          class="absolute -inset-3 rounded-full bg-green-500/25 blur-[2px] animate-pulse"></span>
                                    <span x-show="activeYear === '1996'" 
                                          class="absolute -inset-1.5 rounded-full bg-green-accent/30 animate-ping opacity-60"></span>
                                    <div class="w-5 h-5 rounded-full border-2 transition-all duration-300 relative z-10 flex items-center justify-center"
                                         :class="activeYear === '1996' 
                                             ? 'bg-teal-primary border-green-accent scale-125 shadow-[0_0_12px_rgba(89,163,56,0.8)] ring-4 ring-green-accent/25' 
                                             : (isPassed('1996') 
                                                 ? 'bg-green-accent border-green-accent text-white scale-105 shadow-sm' 
                                                 : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 group-hover:border-teal-primary group-hover:scale-110')">
                                        <span x-show="isPassed('1996')" class="w-1.5 h-1.5 rounded-full bg-white block"></span>
                                    </div>
                                </div>
                                <span class="mt-3 text-xs font-mono transition-all duration-300"
                                      :class="activeYear === '1996' 
                                          ? 'text-teal-primary dark:text-green-accent font-black scale-110' 
                                          : (isPassed('1996') 
                                              ? 'text-teal-primary dark:text-green-accent font-bold' 
                                              : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300')">1996</span>
                            </button>

                            <!-- 2002 -->
                            <button type="button" 
                                    @click="selectYear('2002')"
                                    class="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none">
                                <div class="relative flex items-center justify-center">
                                    <!-- Glowing Aura Rings (Matches Reference screenshot) -->
                                    <span x-show="activeYear === '2002'" 
                                          x-transition:enter="transition ease-out duration-300"
                                          x-transition:enter-start="opacity-0 scale-50"
                                          x-transition:enter-end="opacity-100 scale-100"
                                          class="absolute -inset-3 rounded-full bg-green-500/25 blur-[2px] animate-pulse"></span>
                                    <span x-show="activeYear === '2002'" 
                                          class="absolute -inset-1.5 rounded-full bg-green-accent/30 animate-ping opacity-60"></span>
                                    <div class="w-5 h-5 rounded-full border-2 transition-all duration-300 relative z-10 flex items-center justify-center"
                                         :class="activeYear === '2002' 
                                             ? 'bg-teal-primary border-green-accent scale-125 shadow-[0_0_12px_rgba(89,163,56,0.8)] ring-4 ring-green-accent/25' 
                                             : (isPassed('2002') 
                                                 ? 'bg-green-accent border-green-accent text-white scale-105 shadow-sm' 
                                                 : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 group-hover:border-teal-primary group-hover:scale-110')">
                                        <span x-show="isPassed('2002')" class="w-1.5 h-1.5 rounded-full bg-white block"></span>
                                    </div>
                                </div>
                                <span class="mt-3 text-xs font-mono transition-all duration-300"
                                      :class="activeYear === '2002' 
                                          ? 'text-teal-primary dark:text-green-accent font-black scale-110' 
                                          : (isPassed('2002') 
                                              ? 'text-teal-primary dark:text-green-accent font-bold' 
                                              : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300')">2002</span>
                            </button>

                            <!-- 2010 -->
                            <button type="button" 
                                    @click="selectYear('2010')"
                                    class="relative z-10 flex flex-col items-center group cursor-pointer focus:outline-none">
                                <div class="relative flex items-center justify-center">
                                    <!-- Glowing Aura Rings (Matches Reference screenshot) -->
                                    <span x-show="activeYear === '2010'" 
                                          x-transition:enter="transition ease-out duration-300"
                                          x-transition:enter-start="opacity-0 scale-50"
                                          x-transition:enter-end="opacity-100 scale-100"
                                          class="absolute -inset-3 rounded-full bg-green-500/25 blur-[2px] animate-pulse"></span>
                                    <span x-show="activeYear === '2010'" 
                                          class="absolute -inset-1.5 rounded-full bg-green-accent/30 animate-ping opacity-60"></span>
                                    <div class="w-5 h-5 rounded-full border-2 transition-all duration-300 relative z-10 flex items-center justify-center"
                                         :class="activeYear === '2010' 
                                             ? 'bg-teal-primary border-green-accent scale-125 shadow-[0_0_12px_rgba(89,163,56,0.8)] ring-4 ring-green-accent/25' 
                                             : (isPassed('2010') 
                                                 ? 'bg-green-accent border-green-accent text-white scale-105 shadow-sm' 
                                                 : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 group-hover:border-teal-primary group-hover:scale-110')">
                                        <span x-show="isPassed('2010')" class="w-1.5 h-1.5 rounded-full bg-white block"></span>
                                    </div>
                                </div>
                                <span class="mt-3 text-xs font-mono transition-all duration-300"
                                      :class="activeYear === '2010' 
                                          ? 'text-teal-primary dark:text-green-accent font-black scale-110' 
                                          : (isPassed('2010') 
                                              ? 'text-teal-primary dark:text-green-accent font-bold' 
                                              : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300')">2010</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Frame with Smooth Cross-Fade -->
                <div class="lg:col-span-6" data-aos="fade-left">
                    <div class="relative overflow-hidden rounded-2xl border border-gray-200/90 dark:border-gray-800 bg-gray-100 dark:bg-gray-900 shadow-lg min-h-[380px] sm:min-h-[460px]">
                        <!-- Floating Active Year Badge -->
                        <div class="absolute top-4 right-4 z-30 pointer-events-none">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/65 backdrop-blur-md text-white font-mono text-xs font-bold border border-white/20 shadow-md">
                                <span class="w-2 h-2 rounded-full bg-green-accent animate-pulse"></span>
                                <span x-text="activeYear"></span>
                            </span>
                        </div>

                        <template x-for="yr in years" :key="yr">
                            <div x-show="activeYear === yr"
                                 x-transition:enter="transition ease-out duration-700 transform"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-300 transform absolute inset-0 pointer-events-none"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-105"
                                 class="w-full h-full flex flex-col justify-between">
                                <div class="w-full h-[320px] sm:h-[400px] overflow-hidden bg-gray-900">
                                    <img :src="timeline[yr].image" 
                                         :alt="timeline[yr].title" 
                                         class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700">
                                </div>
                                <div class="p-4 bg-white/95 dark:bg-gray-900/95 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-600 dark:text-gray-400 italic flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span x-text="timeline[yr].caption"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5: THE LEADERSHIP (Real Leaders Provided by User) -->
    <section class="py-20 lg:py-24 bg-white dark:bg-gray-950 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Split Header -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start mb-12 lg:mb-16">
                <div class="lg:col-span-5" data-aos="fade-up">
                    <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                        [ 05 / THE LEADERSHIP ]
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight">
                        The Leadership & Learning Development Team
                    </h2>
                </div>
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light">
                        Dipimpin oleh praktisi perkebunan berpengalaman, jajaran pimpinan First Resources Academy berdedikasi penuh dalam merancang kurikulum, membina karakter kedisiplinan, dan memastikan setiap lulusan memiliki kompetensi unggul di garis terdepan operasional.
                    </p>
                </div>
            </div>

            <!-- 3-Column Grid Matching Reference Layout (Aspect 3:4 Studio Portraits) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="200">
                <!-- Leader 1: Benyamin Basuki -->
                <div class="group">
                    <div class="aspect-[3/4] overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200/90 dark:border-gray-800 mb-4 shadow-xs">
                        <img src="{{ asset('images/leaders/benyamin_basuki_studio.jpg') }}" alt="Benyamin Basuki" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-teal-primary dark:text-white">Benyamin Basuki</h3>
                    <p class="text-sm font-semibold text-green-accent mt-0.5">Academy Director at Plantation</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">First Resources Academy</p>
                </div>

                <!-- Leader 2: Pinondang Lindarto Simanungkalit -->
                <div class="group">
                    <div class="aspect-[3/4] overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200/90 dark:border-gray-800 mb-4 shadow-xs">
                        <img src="{{ asset('images/leaders/pinondang_simanungkalit_studio.jpg') }}" alt="Pinondang Lindarto Simanungkalit" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-teal-primary dark:text-white">Pinondang Lindarto Simanungkalit</h3>
                    <p class="text-sm font-semibold text-green-accent mt-0.5">L&D Sr. Manager</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Learning & Development</p>
                </div>

                <!-- Leader 3: Henritoga Pana Hutajulu -->
                <div class="group">
                    <div class="aspect-[3/4] overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200/90 dark:border-gray-800 mb-4 shadow-xs">
                        <img src="{{ asset('images/leaders/henritoga_hutajulu_studio.jpg') }}" alt="Henritoga Pana Hutajulu" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-teal-primary dark:text-white">Henritoga Pana Hutajulu</h3>
                    <p class="text-sm font-semibold text-green-accent mt-0.5">L&D Sr. Manager</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Learning & Development</p>
                </div>

                <!-- Leader 4: Sandos Simatupang -->
                <div class="group">
                    <div class="aspect-[3/4] overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200/90 dark:border-gray-800 mb-4 shadow-xs">
                        <img src="{{ asset('images/leaders/sandos_simatupang_studio.jpg') }}" alt="Sandos Simatupang" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-teal-primary dark:text-white">Sandos Simatupang</h3>
                    <p class="text-sm font-semibold text-green-accent mt-0.5">L&D Manager</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Learning & Development</p>
                </div>

                <!-- Block 5 & 6: Solid Dark Teal Statement Card (Spans 2 columns on desktop) -->
                <div class="lg:col-span-2 rounded-lg bg-teal-primary text-white p-8 md:p-10 flex flex-col justify-between border border-teal-800/80 shadow-sm min-h-[300px]">
                    <div>
                        <span class="text-xs font-mono uppercase tracking-widest text-green-accent block mb-2">[ KOMITMEN LEADERSHIP ]</span>
                        <h3 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mt-1">Dedikasi Membina Pemimpin Masa Depan Agribisnis</h3>
                        <p class="text-sm sm:text-base text-teal-100/90 leading-relaxed font-light mt-4 max-w-2xl">
                            Di bawah arahan jajaran kepemimpinan dan tim Learning & Development, First Resources Academy secara konsisten mengintegrasikan keahlian teknis terdepan dengan kedisiplinan berkarakter unggul di seluruh unit operasional perkebunan.
                        </p>
                    </div>
                    <div class="pt-6 border-t border-white/10 mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <span class="text-xs font-mono text-teal-300">First Resources Group • Center of Excellence</span>
                        <a href="/program" class="inline-flex items-center gap-2 text-xs font-semibold text-green-accent hover:text-white transition-colors group font-mono uppercase tracking-wider">
                            <span>Jelajahi Kurikulum Program</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 7: FASILITAS PELATIHAN & PETA LEARNING STATION -->
    <section class="py-20 lg:py-24 bg-gray-50/70 dark:bg-gray-900/60 bg-tech-grid border-y border-gray-200/80 dark:border-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Split Header -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start mb-14">
                <div class="lg:col-span-5" data-aos="fade-up">
                    <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-3 font-mono">
                        [ 06 / INFRASTRUKTUR & FASILITAS ]
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-teal-primary dark:text-white tracking-tight leading-tight">
                        Fasilitas Pelatihan Terpadu & Jaringan Operasional
                    </h2>
                </div>
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 leading-relaxed font-light">
                        Seluruh sarana dan prasarana dirancang khusus untuk mendukung proses belajar mengajar, kedisiplinan pembinaan asrama, serta simulasi praktik agronomi secara komprehensif.
                    </p>
                </div>
            </div>

            <!-- 4 Clean Architectural Facility Grid (No Cluttered Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16" data-aos="fade-up" data-aos-delay="150">
                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <div class="text-3xl font-black text-teal-primary dark:text-green-accent mb-2">300 Ha</div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Lahan Praktik Lapangan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-light leading-relaxed">Area simulasi teknis agronomi dan pembibitan secara langsung.</p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <div class="text-3xl font-black text-teal-primary dark:text-green-accent mb-2">6 Kelas</div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Ruang Kelas Modern</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-light leading-relaxed">Dilengkapi Learning Management System (LMS) interaktif.</p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <div class="text-3xl font-black text-teal-primary dark:text-green-accent mb-2">100 Orang</div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Kapasitas Asrama</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-light leading-relaxed">48 kamar berjenis twin-bed dengan fasilitas kenyamanan optimal.</p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg">
                    <div class="text-3xl font-black text-teal-primary dark:text-green-accent mb-2">Fasilitas</div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Sarana Penunjang</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-light leading-relaxed">Tempat ibadah, lapangan olahraga, dan wahana outbound.</p>
                </div>
            </div>

            <!-- Learning Station Map Banner -->
            <div class="p-8 lg:p-10 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl" data-aos="fade-up">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-5">
                        <span class="text-xs font-mono uppercase tracking-widest text-green-accent block mb-2">JARINGAN OPERASIONAL</span>
                        <h3 class="text-2xl font-bold text-teal-primary dark:text-white mb-3">Ekspansi Learning Station di Wilayah Strategis</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-light leading-relaxed mb-4">
                            FR Academy terus memperluas pos pembelajaran di titik-titik operasional strategis (Riau, Kalimantan Barat, dan Kalimantan Timur) untuk percepatan kompetensi Mandor dan Kerani.
                        </p>
                        <div class="flex flex-wrap gap-2 text-xs font-mono">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded">Riau</span>
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded">Kalimantan Barat</span>
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded">Kalimantan Timur</span>
                        </div>
                    </div>
                    <div class="lg:col-span-7 flex justify-center">
                        <img src="{{ asset('images/peta.png') }}" alt="Peta Persebaran Learning Station" class="max-h-64 w-auto object-contain">
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>

