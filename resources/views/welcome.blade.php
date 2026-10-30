<x-layout>
    @php
        $heroSlides = \App\Models\AppSetting::getHeroSlides();
        $heroBadge = \App\Models\AppSetting::get('home_hero_badge', 'FIRST RESOURCES ACADEMY');
        $heroTitle = \App\Models\AppSetting::get('home_hero_title', "Membangun Value, Menumbuhkan Karakter & <br><span class=\"text-green-accent\">Menyiapkan Masa Depan</span>");
        $heroTagline = \App\Models\AppSetting::get('home_hero_tagline', '"Kebun Itu Sebaik Manusianya."');
        $heroDesc = \App\Models\AppSetting::get('home_hero_desc', 'FR Academy hadir sebagai pusat pengembangan kompetensi dan pembentukan karakter kerja untuk menciptakan human capital perusahaan yang unggul, adaptif, dan siap menghadapi tantangan bisnis.');
        $heroBtn1Text = \App\Models\AppSetting::get('home_hero_btn1_text', 'Jelajahi Program');
        $heroBtn1Url = \App\Models\AppSetting::get('home_hero_btn1_url', '#program');
        $heroBtn2Text = \App\Models\AppSetting::get('home_hero_btn2_text', 'Baca Artikel');
        $heroBtn2Url = \App\Models\AppSetting::get('home_hero_btn2_url', '/artikel');

        $visionBadge = \App\Models\AppSetting::get('home_vision_badge', 'TENTANG FR ACADEMY');
        $visionTitle = \App\Models\AppSetting::get('home_vision_title', 'Visi FR Academy');
        $visionDesc = \App\Models\AppSetting::get('home_vision_desc', 'Meningkatkan nilai perusahaan melalui pembelajaran berkelanjutan yang berkualitas dengan menjadi mitra strategis dalam pengembangan SDM. Kami merancang lingkungan belajar yang mengintegrasikan pengetahuan teknis lapangan dengan nilai-nilai kepemimpinan.');
        $visionP1Title = \App\Models\AppSetting::get('home_vision_p1_title', 'Instruktur Internal Ahli');
        $visionP1Desc = \App\Models\AppSetting::get('home_vision_p1_desc', 'Dibimbing langsung oleh praktisi internal kompeten mulai dari level Manager hingga Direktur, serta External Expert sesuai bidangnya.');
        $visionP2Title = \App\Models\AppSetting::get('home_vision_p2_title', 'Fasilitas Terintegrasi');
        $visionP2Desc = \App\Models\AppSetting::get('home_vision_p2_desc', 'Terintegrasi dengan kebun 300 Ha dan R&D, dilengkapi ruang kelas, mess 48 kamar, sarana olahraga, hingga area praktik langsung.');
        $visionImg1 = \App\Models\AppSetting::getImageUrl('home_vision_img_1', 'https://i.ibb.co.com/hFZ1CScS/slides-6.jpg');
        $visionImg2 = \App\Models\AppSetting::getImageUrl('home_vision_img_2', 'https://i.ibb.co.com/Rp1kWtfV/image-12.png');

        // Pilar Program Utama
        $pillarsBadge = \App\Models\AppSetting::get('home_pillars_badge', 'PILAR PROGRAM UTAMA');
        $pillarsTitle = \App\Models\AppSetting::get('home_pillars_title', 'Tiga Pilar Program Utama');
        $pillarsDesc = \App\Models\AppSetting::get('home_pillars_desc', 'FR Academy berfokus pada pengembangan SDM melalui pilar pembelajaran utama.');
        $pillarCards = \App\Models\AppSetting::getPillarCards();

        // Statistik Capaian
        $stat1Value = \App\Models\AppSetting::get('home_stat1_value', '300');
        $stat1Unit = \App\Models\AppSetting::get('home_stat1_unit', 'Ha');
        $stat1Label = \App\Models\AppSetting::get('home_stat1_label', 'Area Praktik & Simulasi');
        $stat1Icon = \App\Models\AppSetting::getImageUrl('home_stat1_icon', 'images/300h.png');

        $stat2Value = \App\Models\AppSetting::get('home_stat2_value', '70:20:10');
        $stat2Unit = \App\Models\AppSetting::get('home_stat2_unit', '');
        $stat2Label = \App\Models\AppSetting::get('home_stat2_label', 'Metode Pembelajaran (Experiential, Social, Formal)');
        $stat2Icon = \App\Models\AppSetting::getImageUrl('home_stat2_icon', 'images/learningmethod.png');

        $stat3Value = \App\Models\AppSetting::get('home_stat3_value', '3');
        $stat3Unit = \App\Models\AppSetting::get('home_stat3_unit', '');
        $stat3Label = \App\Models\AppSetting::get('home_stat3_label', 'Pusat Pelatihan & Learning Station');
        $stat3Icon = \App\Models\AppSetting::getImageUrl('home_stat3_icon', 'images/3pillar.png');

        // Accordion Section
        $programBadge = \App\Models\AppSetting::get('home_program_badge', 'MENCETAK PLANTER & ENGINEER UNGGUL');
        $programTitle = \App\Models\AppSetting::get('home_program_title', "Membangun Value, Menumbuhkan Karakter & <br> Menyiapkan Masa Depan");
        $accordions = \App\Models\AppSetting::getAccordions();
    @endphp

    <!-- Hero Section -->
    <section class="relative bg-teal-primary min-h-screen flex items-center pt-32 pb-16 overflow-hidden"
             x-data="{ 
                currentSlide: 0, 
                slides: {{ \Illuminate\Support\Js::from($heroSlides) }},
                nextSlide() { this.currentSlide = (this.currentSlide === this.slides.length - 1) ? 0 : this.currentSlide + 1 },
                prevSlide() { this.currentSlide = (this.currentSlide === 0) ? this.slides.length - 1 : this.currentSlide - 1 }
             }" 
             x-init="setInterval(() => nextSlide(), 5000)">
             
        <!-- Background Slider -->
        <div class="absolute inset-0 z-0 bg-black">
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="currentSlide === index"
                     x-transition:enter="transition ease-in-out duration-1000"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in-out duration-1000"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-105"
                     class="absolute inset-0">
                    <img :src="slide" alt="FR Academy Slide" class="w-full h-full object-cover">
                </div>
            </template>
        </div>
        
        <!-- Dark overlay to ensure text readability -->
        <div class="absolute inset-0 bg-black/60 z-10 transition-opacity duration-1000"></div>

        <!-- Slider Navigation Controls -->
        <div class="absolute inset-y-0 left-0 z-30 flex items-center px-4 md:px-8">
            <button @click="prevSlide()" class="p-3 rounded-full bg-black/20 text-white hover:bg-black/60 hover:scale-110 transition-all backdrop-blur-sm border border-white/10 hidden sm:block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
        </div>
        <div class="absolute inset-y-0 right-0 z-30 flex items-center px-4 md:px-8">
            <button @click="nextSlide()" class="p-3 rounded-full bg-black/20 text-white hover:bg-black/60 hover:scale-110 transition-all backdrop-blur-sm border border-white/10 hidden sm:block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <!-- Slider Indicators -->
        <div class="absolute bottom-10 left-0 right-0 z-30 flex justify-center space-x-3">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="currentSlide = index" 
                        class="h-2 rounded-full transition-all duration-500 ease-out"
                        :class="currentSlide === index ? 'bg-green-accent w-10 shadow-[0_0_10px_rgba(89,163,56,0.8)]' : 'bg-white/40 hover:bg-white/80 w-2'"></button>
            </template>
        </div>
        
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col items-center text-center" data-aos="zoom-in" data-aos-duration="1000">
            <div class="max-w-3xl flex flex-col items-center">
                <span class="inline-block py-1 px-3 rounded bg-white/20 text-white text-xs font-semibold tracking-wider mb-4 border border-white/30 backdrop-blur-sm">{{ $heroBadge }}</span>
                <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">
                    {!! $heroTitle !!}
                </h1>
                @if($heroTagline)
                    <p class="text-xl md:text-2xl text-green-accent font-semibold mb-6">
                        {{ $heroTagline }}
                    </p>
                @endif
                <p class="text-lg text-gray-200 mb-10 max-w-3xl mx-auto leading-relaxed">
                    {{ $heroDesc }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
                    @if($heroBtn1Text)
                        <a href="{{ $heroBtn1Url ?: '#program' }}" class="w-full sm:w-auto bg-green-accent hover:bg-green-600 text-white px-8 py-4 rounded-full font-bold transition duration-300 shadow-lg text-lg">
                            {{ $heroBtn1Text }}
                        </a>
                    @endif
                    @if($heroBtn2Text)
                        <a href="{{ $heroBtn2Url ?: '/artikel' }}" class="w-full sm:w-auto bg-white/20 hover:bg-white/30 backdrop-blur-md text-white border border-white/30 px-8 py-4 rounded-full font-bold transition duration-300 shadow-lg text-lg">
                            {{ $heroBtn2Text }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="relative py-24 bg-white dark:bg-gray-900 transition-colors duration-300 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <!-- Image Grid -->
                <div class="relative" data-aos="fade-right">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-64 bg-gray-200 rounded-2xl overflow-hidden mt-8 shadow-md">
                            <img src="{{ $visionImg1 }}" alt="Training" class="w-full h-full object-cover">
                        </div>
                        <div class="h-64 bg-gray-200 rounded-2xl overflow-hidden shadow-md">
                            <img src="{{ $visionImg2 }}" alt="Meeting" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -z-10 -bottom-6 -left-6 w-32 h-32 bg-green-50 dark:bg-green-950/20 rounded-full"></div>
                </div>

                <!-- Text Content -->
                <div data-aos="fade-left" data-aos-delay="200">
                    <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">{{ $visionBadge }}</span>
                    <h2 class="text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-6 leading-tight">
                        {{ $visionTitle }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        {{ $visionDesc }}
                    </p>
                    
                    <ul class="space-y-4 mb-8">
                        @if($visionP1Title)
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-accent mt-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-teal-primary dark:text-white">{{ $visionP1Title }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $visionP1Desc }}</p>
                            </div>
                        </li>
                        @endif
                        @if($visionP2Title)
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-accent mt-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-teal-primary dark:text-white">{{ $visionP2Title }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $visionP2Desc }}</p>
                            </div>
                        </li>
                        @endif
                    </ul>
                    
                    <a href="/tentang-kami" class="inline-flex items-center text-teal-primary dark:text-teal-300 font-semibold hover:text-green-accent dark:hover:text-green-400 transition">
                        Lebih Lanjut Tentang Kami 
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section class="relative py-24 bg-gray-50 dark:bg-gray-800 transition-colors duration-300 overflow-hidden">
        
        <!-- Floating Ornament Sawit di Kanan Atas (Area Kosong) -->
        <div class="absolute top-10 right-0 z-0 opacity-40 pointer-events-none transform translate-x-1/3 hover:scale-105 transition-transform duration-700 animate-[bounce_5s_infinite]">
            <img src="{{ asset('images/sawit.png') }}" alt="Ornamen Sawit" class="w-64 h-64 md:w-96 md:h-96 object-contain drop-shadow-2xl">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">{{ $pillarsBadge }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-4">{{ $pillarsTitle }}</h2>
                <p class="text-gray-600 dark:text-gray-300">{{ $pillarsDesc }}</p>
            </div>

            @php
                $pillarCount = count($pillarCards);
                $gridClass = match(true) {
                    $pillarCount === 1 => 'grid grid-cols-1 max-w-xl mx-auto gap-8',
                    $pillarCount === 2 => 'grid grid-cols-1 md:grid-cols-2 max-w-4xl mx-auto gap-8',
                    $pillarCount === 4 => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8',
                    default => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
                };
            @endphp
            <div class="{{ $gridClass }}">
                @foreach($pillarCards as $idx => $card)
                <div class="group bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col" data-aos="fade-up" data-aos-delay="{{ (($idx % 3) + 1) * 100 }}">
                    <div class="relative h-56 overflow-hidden">
                        <img src="{{ $card['image_url'] ?? $card['image'] }}" alt="{{ $card['title'] }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        @if(!empty($card['icon_url']) || !empty($card['icon']))
                        <div class="absolute -bottom-8 right-8">
                            <div class="w-24 h-24 bg-white dark:bg-gray-800 rounded-2xl p-1.5 shadow-xl flex items-center justify-center border border-gray-100 dark:border-gray-700 transform group-hover:-translate-y-2 transition-transform duration-500 z-20">
                                <img src="{{ $card['icon_url'] ?? $card['icon'] }}" alt="{{ $card['title'] }} Icon" class="w-full h-full object-contain drop-shadow-sm">
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="p-8 pt-10 flex flex-col flex-grow">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $card['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 flex-grow">{{ $card['desc'] ?? '' }}</p>
                        @if(!empty($card['btn_text']))
                            <a href="{{ !empty($card['btn_url']) ? $card['btn_url'] : '#program' }}" class="inline-flex items-center text-sm font-bold text-teal-600 dark:text-teal-400 group-hover:text-green-accent transition-colors">
                                {{ $card['btn_text'] }} 
                                <svg class="ml-2 w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats-section" class="relative py-12 text-white bg-teal-900 overflow-hidden">
        <!-- Background Image Palm Tree -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/PALM TREE_Panorama1.png') }}" alt="Palm Tree Background" class="w-full h-full object-cover opacity-30 mix-blend-luminosity">
            <div class="absolute inset-0 bg-teal-primary/60"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 divide-y md:divide-y-0 md:divide-x divide-teal-700/50">
                <!-- Stat 1 -->
                <div class="text-center py-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="flex justify-center mb-6">
                        <img src="{{ $stat1Icon }}" alt="Area Icon" class="w-24 h-24 md:w-32 md:h-32 object-contain drop-shadow-lg">
                    </div>
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1">
                        @if(is_numeric($stat1Value))
                            <span class="counter" data-target="{{ $stat1Value }}">0</span>
                        @else
                            <span>{{ $stat1Value }}</span>
                        @endif
                        @if($stat1Unit)
                            <span class="text-green-accent text-4xl"> {{ $stat1Unit }}</span>
                        @endif
                    </div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">{{ $stat1Label }}</div>
                </div>
                <!-- Stat 2 -->
                <div class="text-center py-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="flex justify-center mb-6">
                        <img src="{{ $stat2Icon }}" alt="Method Icon" class="w-24 h-24 md:w-32 md:h-32 object-contain drop-shadow-lg">
                    </div>
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1">
                        @if(is_numeric($stat2Value))
                            <span class="counter" data-target="{{ $stat2Value }}">0</span>
                        @else
                            <span>{{ $stat2Value }}</span>
                        @endif
                        @if($stat2Unit)
                            <span class="text-green-accent text-4xl"> {{ $stat2Unit }}</span>
                        @endif
                    </div>
                    <div class="text-teal-200 text-xs md:text-sm uppercase tracking-wider font-semibold px-2">{{ $stat2Label }}</div>
                </div>
                <!-- Stat 3 -->
                <div class="text-center py-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="flex justify-center mb-6">
                        <img src="{{ $stat3Icon }}" alt="Pillars Icon" class="w-24 h-24 md:w-32 md:h-32 object-contain drop-shadow-lg">
                    </div>
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1">
                        @if(is_numeric($stat3Value))
                            <span class="counter" data-target="{{ $stat3Value }}">0</span>
                        @else
                            <span>{{ $stat3Value }}</span>
                        @endif
                        @if($stat3Unit)
                            <span class="text-green-accent text-4xl"> {{ $stat3Unit }}</span>
                        @endif
                    </div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">{{ $stat3Label }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Script for Counter Animation -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.counter');
        const speed = 40; 
    
        const animateCounters = () => {
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target');
                let currentCount = 0;
                const inc = target / speed;
                
                const updateCount = () => {
                    if (currentCount < target) {
                        currentCount += inc;
                        if(currentCount > target) currentCount = target;
                        
                        let isDecimal = target % 1 !== 0;
                        counter.innerText = isDecimal ? currentCount.toFixed(1).replace('.', ',') : Math.ceil(currentCount);
                        
                        setTimeout(updateCount, 30);
                    } else {
                        counter.innerText = target.toString().replace('.', ',');
                    }
                };
                updateCount();
            });
        }
    
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
    
        const statsSection = document.getElementById('stats-section');
        if(statsSection) {
            observer.observe(statsSection);
        }
    });
    </script>

    <!-- Services Section (Modern Accordion) -->
    <section id="program" class="py-24 bg-teal-900 dark:bg-gray-950 relative overflow-hidden text-white transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" data-aos="fade-up">
            <div class="mb-16">
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">{{ $programBadge }}</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-4 leading-tight">
                    {!! $programTitle !!}
                </h2>
            </div>
            
            <div class="space-y-0 border-t border-teal-700/50" x-data="{ selected: null }">
                <style>
                    .text-outline {
                        color: transparent;
                        -webkit-text-stroke: 1px rgba(255, 255, 255, 0.4);
                        transition: all 0.3s ease;
                    }
                    .text-outline-active {
                        color: #509c38;
                        -webkit-text-stroke: 0px transparent;
                    }
                    .accordion-item:hover .text-outline:not(.text-outline-active) {
                        -webkit-text-stroke: 1px rgba(255, 255, 255, 0.8);
                    }
                </style>

                <!-- Dynamic Accordions -->
                @foreach($accordions as $index => $acc)
                @php
                    $accNum = $index + 1;
                    $accPad = str_pad($accNum, 2, '0', STR_PAD_LEFT);
                    $accItems = $acc['items'] ?? [];
                @endphp
                <div class="border-b border-teal-700/50 group accordion-item transition-colors duration-300" :class="selected === {{ $accNum }} ? 'bg-teal-800/50' : 'hover:bg-teal-800/30'">
                    <button @click="selected = selected === {{ $accNum }} ? null : {{ $accNum }}" class="flex items-center w-full text-left py-8 md:py-12 px-4 md:px-8 focus:outline-none">
                        <span class="text-green-accent font-semibold text-xl mr-6 md:mr-10" :class="selected === {{ $accNum }} ? 'opacity-100' : 'opacity-70'">({{ $accPad }})</span>
                        <h3 class="text-4xl md:text-6xl lg:text-7xl font-bold uppercase text-outline tracking-wide" :class="selected === {{ $accNum }} ? 'text-outline-active' : ''">{{ $acc['title'] ?? '' }}</h3>
                    </button>
                    <div x-show="selected === {{ $accNum }}" x-collapse class="px-4 md:px-8 pb-10 md:pl-[6.5rem]">
                        @if(!empty($acc['desc']))
                            <p class="mb-6 text-lg text-teal-100 max-w-3xl">{{ $acc['desc'] }}</p>
                        @endif
                        @if(!empty($accItems))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-5xl text-teal-50">
                                @foreach($accItems as $item)
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-green-accent mr-2.5 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div>
                                            <strong class="text-white font-semibold">{{ $item['title'] ?? '' }}</strong>
                                            @if(!empty($item['desc']))
                                                <p class="text-sm text-teal-100/90 mt-0.5">{{ $item['desc'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>



    <!-- News Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12" data-aos="fade-up">
                <div>
                    <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">ARTIKEL & PENGUMUMAN</span>
                    <h2 class="text-3xl font-bold text-teal-primary dark:text-white mt-2">Jelajahi Berita & Tips</h2>
                </div>
                <a href="/artikel" class="hidden md:inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    Lihat Semua Berita
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($latest_articles as $article)
                <!-- News Item -->
                <div class="bg-white dark:bg-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 flex flex-col border border-transparent dark:border-gray-600">
                    <div class="relative h-48 overflow-hidden">
                        @if($article->featured_image)
                            <img src="{{ Storage::url($article->featured_image) }}" alt="Cover" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-[#114045] flex items-center justify-center opacity-90">
                                <img src="{{ asset('images/logofr.png') }}" class="w-32 opacity-30">
                            </div>
                        @endif
                        <div class="absolute top-4 left-4 bg-white dark:bg-gray-800 px-3 py-1 rounded text-xs font-bold text-teal-primary dark:text-teal-400 uppercase tracking-wider shadow">
                            {{ $article->category->name ?? 'Informasi' }}
                        </div>
                    </div>
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('d M Y') : $article->created_at->format('d M Y') }}
                            </div>
                            <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3 line-clamp-2 hover:text-green-accent dark:hover:text-green-400 transition">{{ $article->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">{{ Str::limit(strip_tags($article->content), 120) }}</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('artikel.show', $article->slug) }}" class="text-green-accent dark:text-green-400 font-semibold text-sm inline-flex items-center hover:text-green-600 dark:hover:text-green-300 transition">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="col-span-1 md:col-span-3 text-center py-12">
                        <p class="text-gray-500 text-lg">Belum ada artikel terbaru.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-8 text-center md:hidden">
                <a href="/artikel" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    Lihat Semua Berita
                </a>
            </div>
        </div>
    </section>
</x-layout>
