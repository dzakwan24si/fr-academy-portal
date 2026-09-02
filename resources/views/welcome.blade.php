<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-teal-primary min-h-screen flex items-center pt-32 pb-16">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/welcomefr.jpg') }}" alt="FR Academy Welcome" class="w-full h-full object-cover">
        </div>
        <!-- Dark overlay to ensure text readability -->
        <div class="absolute inset-0 bg-black/50 z-10"></div>
        
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col items-center text-center" data-aos="zoom-in" data-aos-duration="1000">
            <div class="max-w-3xl flex flex-col items-center">
                <span class="inline-block py-1 px-3 rounded bg-white/20 text-white text-xs font-semibold tracking-wider mb-4 border border-white/30 backdrop-blur-sm">FIRST RESOURCES LEARNING CENTER</span>
                <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight mb-6">
                    Membangun Masa Depan <br>
                    <span class="text-green-accent">Kelapa Sawit Berkelanjutan</span>
                </h1>
                <p class="text-lg text-gray-200 mb-10 max-w-2xl mx-auto">
                    Pusat pelatihan perusahaan terdepan untuk menciptakan sumber daya manusia yang unggul, profesional, dan berdedikasi.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
                    <a href="#program" class="w-full sm:w-auto bg-green-accent hover:bg-green-600 text-white px-8 py-4 rounded-full font-bold transition duration-300 shadow-lg text-lg">
                        Jelajahi Program
                    </a>
                    <a href="/artikel" class="w-full sm:w-auto bg-white/20 hover:bg-white/30 backdrop-blur-md text-white border border-white/30 px-8 py-4 rounded-full font-bold transition duration-300 shadow-lg text-lg">
                        Baca Artikel
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <!-- Image Grid -->
                <div class="relative" data-aos="fade-right">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="h-64 bg-gray-200 rounded-2xl overflow-hidden mt-8">
                            <img src="https://i.ibb.co.com/hFZ1CScS/slides-6.jpg" alt="Training" class="w-full h-full object-cover">
                        </div>
                        <div class="h-64 bg-gray-200 rounded-2xl overflow-hidden">
                            <img src="https://i.ibb.co.com/nsYWPxcw/image-5.png" alt="Meeting" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -z-10 -bottom-6 -left-6 w-32 h-32 bg-green-50 rounded-full"></div>
                </div>

                <!-- Text Content -->
                <div data-aos="fade-left" data-aos-delay="200">
                    <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">TENTANG FR ACADEMY</span>
                    <h2 class="text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-6 leading-tight">
                        Misi Kami: Pertanian yang Lebih Baik untuk <span class="text-green-accent">Masa Depan</span>
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        Kami merancang program pelatihan komprehensif untuk meningkatkan keterampilan teknis dan manajerial para praktisi perkebunan. Didukung oleh instruktur berpengalaman dan fasilitas modern, kami berkomitmen untuk melahirkan agen perubahan yang dapat menerapkan praktik pertanian berkelanjutan dan mencapai efisiensi operasional yang optimal.
                    </p>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-accent mt-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-teal-primary dark:text-white">Instruktur Berpengalaman</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belajar langsung dari praktisi ahli yang telah puluhan tahun berkecimpung di industri.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-accent mt-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-lg font-semibold text-teal-primary dark:text-white">Fasilitas Modern</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Fasilitas simulasi dan praktik lapangan yang memadai untuk pembelajaran aplikatif.</p>
                            </div>
                        </li>
                    </ul>
                    
                    <a href="#" class="inline-flex items-center text-teal-primary dark:text-teal-300 font-semibold hover:text-green-accent dark:hover:text-green-400 transition">
                        Lebih Lanjut Tentang Kami 
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">PROGRAM PELATIHAN KAMI</span>
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-4">Solusi Pelatihan Komprehensif</h2>
                <p class="text-gray-600 dark:text-gray-300">FR Academy menawarkan berbagai spesialisasi untuk memenuhi kebutuhan industri kelapa sawit modern.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-orange-50/50 dark:bg-gray-700 rounded-2xl p-8 transition-transform hover:-translate-y-2 duration-300 border border-orange-100 dark:border-gray-600 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3">Manajemen Agronomi</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6">Pelatihan mendalam tentang pembibitan, perawatan tanaman, pemupukan, hingga teknik panen yang efisien dan ramah lingkungan.</p>
                    
                    <div class="rounded-xl overflow-hidden h-40 mb-4">
                        <img src="https://i.ibb.co.com/hFZ1CScS/slides-6.jpg" alt="Agronomi" class="w-full h-full object-cover">
                    </div>
                    <a href="#" class="text-sm font-semibold text-teal-primary dark:text-teal-400 flex items-center hover:text-green-accent transition">
                        Pelajari lebih lanjut <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-blue-50/50 dark:bg-gray-700 rounded-2xl p-8 transition-transform hover:-translate-y-2 duration-300 border border-blue-100 dark:border-gray-600 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3">Operasional Pabrik</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6">Mempelajari standar operasional prosedur pengolahan kelapa sawit, manajemen K3, dan perawatan mesin pabrik.</p>
                    
                    <div class="rounded-xl overflow-hidden h-40 mb-4">
                        <img src="https://i.ibb.co.com/nsYWPxcw/image-5.png" alt="Pabrik" class="w-full h-full object-cover">
                    </div>
                    <a href="#" class="text-sm font-semibold text-teal-primary dark:text-teal-400 flex items-center hover:text-green-accent transition">
                        Pelajari lebih lanjut <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="bg-teal-50/50 dark:bg-gray-700 rounded-2xl p-8 transition-transform hover:-translate-y-2 duration-300 border border-teal-100 dark:border-gray-600 shadow-sm" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3">Kepemimpinan & Etika</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6">Fokus pada pengembangan karakter, keterampilan kepemimpinan, dan penyelesaian masalah bagi calon manajer.</p>
                    
                    <div class="rounded-xl overflow-hidden h-40 mb-4">
                        <img src="https://i.ibb.co.com/LH86Lkw/20260818-083322.jpg" alt="Kepemimpinan" class="w-full h-full object-cover">
                    </div>
                    <a href="#" class="text-sm font-semibold text-teal-primary dark:text-teal-400 flex items-center hover:text-green-accent transition">
                        Pelajari lebih lanjut <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats-section" class="bg-teal-primary py-16 text-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 divide-y md:divide-y-0 md:divide-x divide-teal-700">
                <!-- Stat 1 -->
                <div class="text-center py-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1"><span class="counter" data-target="20">0</span><span class="text-green-accent text-4xl">k+</span></div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">SISWA LULUS</div>
                </div>
                <!-- Stat 2 -->
                <div class="text-center py-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1"><span class="counter" data-target="98">0</span><span class="text-green-accent text-4xl">%</span></div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">TINGKAT KEPUASAN</div>
                </div>
                <!-- Stat 3 -->
                <div class="text-center py-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-5xl font-bold mb-2 flex justify-center items-baseline gap-1"><span class="counter" data-target="1.5">0</span><span class="text-green-accent text-4xl">k+</span></div>
                    <div class="text-teal-200 text-sm uppercase tracking-wider font-semibold">FASILITATOR BERPENGALAMAN</div>
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
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">AREA FOKUS KAMI</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-4 leading-tight">
                    Dari Bibit Hingga Panen — <br>Layanan Penuh Kami
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

                <!-- Accordion Item 1 -->
                <div class="border-b border-teal-700/50 group accordion-item transition-colors duration-300" :class="selected === 1 ? 'bg-teal-800/50' : 'hover:bg-teal-800/30'">
                    <button @click="selected = selected === 1 ? null : 1" class="flex items-center w-full text-left py-8 md:py-12 px-4 md:px-8 focus:outline-none">
                        <span class="text-green-accent font-mono text-xl mr-6 md:mr-10" :class="selected === 1 ? 'opacity-100' : 'opacity-70'">(01)</span>
                        <h3 class="text-4xl md:text-6xl lg:text-7xl font-bold uppercase text-outline tracking-wide" :class="selected === 1 ? 'text-outline-active' : ''">PROGRAM REGULER</h3>
                    </button>
                    <div x-show="selected === 1" x-collapse class="px-4 md:px-8 pb-10 md:pl-[6.5rem]">
                        <p class="mb-6 text-lg text-teal-100 max-w-3xl">Program pelatihan dasar bagi karyawan baru maupun calon karyawan yang akan ditempatkan di berbagai kebun operasional.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-4xl text-teal-50">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-accent mr-2 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong class="text-white">Field Assistant:</strong> Penjelasan singkat mengenai asisten kebun.</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-accent mr-2 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong class="text-white">Mill Assistant:</strong> Penjelasan mengenai asisten pabrik kelapa sawit.</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-accent mr-2 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong class="text-white">Downstream Assistant:</strong> Penjelasan mengenai asisten downstream.</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-accent mr-2 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong class="text-white">Traction Assistant:</strong> Penjelasan mengenai asisten traksi.</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-accent mr-2 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong class="text-white">Administration Assistant:</strong> Penjelasan mengenai asisten administrasi.</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-accent mr-2 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span><strong class="text-white">Management Associate:</strong> Jalur kepemimpinan masa depan.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accordion Item 2 -->
                <div class="border-b border-teal-700/50 group accordion-item transition-colors duration-300" :class="selected === 2 ? 'bg-teal-800/50' : 'hover:bg-teal-800/30'">
                    <button @click="selected = selected === 2 ? null : 2" class="flex items-center w-full text-left py-8 md:py-12 px-4 md:px-8 focus:outline-none">
                        <span class="text-green-accent font-mono text-xl mr-6 md:mr-10" :class="selected === 2 ? 'opacity-100' : 'opacity-70'">(02)</span>
                        <h3 class="text-4xl md:text-6xl lg:text-7xl font-bold uppercase text-outline tracking-wide" :class="selected === 2 ? 'text-outline-active' : ''">PROGRAM NON-REGULER</h3>
                    </button>
                    <div x-show="selected === 2" x-collapse class="px-4 md:px-8 pb-10 md:pl-[6.5rem]">
                        <p class="mb-6 text-lg text-teal-100 max-w-3xl">Pelatihan khusus yang dirancang sesuai dengan kebutuhan spesifik divisi atau tantangan operasional terkini.</p>
                        <div class="flex flex-wrap gap-3 max-w-4xl">
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Estate Program</span>
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Mill & Downstream Program</span>
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Soft Skill Program</span>
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Administration Program</span>
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Sustainability Program</span>
                        </div>
                    </div>
                </div>

                <!-- Accordion Item 3 -->
                <div class="border-b border-teal-700/50 group accordion-item transition-colors duration-300" :class="selected === 3 ? 'bg-teal-800/50' : 'hover:bg-teal-800/30'">
                    <button @click="selected = selected === 3 ? null : 3" class="flex items-center w-full text-left py-8 md:py-12 px-4 md:px-8 focus:outline-none">
                        <span class="text-green-accent font-mono text-xl mr-6 md:mr-10" :class="selected === 3 ? 'opacity-100' : 'opacity-70'">(03)</span>
                        <h3 class="text-4xl md:text-6xl lg:text-7xl font-bold uppercase text-outline tracking-wide" :class="selected === 3 ? 'text-outline-active' : ''">PROGRAM PENDUKUNG</h3>
                    </button>
                    <div x-show="selected === 3" x-collapse class="px-4 md:px-8 pb-10 md:pl-[6.5rem]">
                        <p class="mb-6 text-lg text-teal-100 max-w-3xl">Pelatihan untuk meningkatkan kompetensi pendukung yang esensial di lingkungan operasional perkebunan.</p>
                        <div class="flex flex-wrap gap-3 max-w-4xl">
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Soft Skill Program</span>
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Administration Program</span>
                            <span class="px-4 py-2 bg-teal-800 rounded-full text-sm text-teal-50 border border-teal-600">Sustainability Program</span>
                        </div>
                    </div>
                </div>

                <!-- Accordion Item 4 -->
                <div class="border-b border-teal-700/50 group accordion-item transition-colors duration-300" :class="selected === 4 ? 'bg-teal-800/50' : 'hover:bg-teal-800/30'">
                    <button @click="selected = selected === 4 ? null : 4" class="flex items-center w-full text-left py-8 md:py-12 px-4 md:px-8 focus:outline-none">
                        <span class="text-green-accent font-mono text-xl mr-6 md:mr-10" :class="selected === 4 ? 'opacity-100' : 'opacity-70'">(04)</span>
                        <h3 class="text-4xl md:text-6xl lg:text-7xl font-bold uppercase text-outline tracking-wide" :class="selected === 4 ? 'text-outline-active' : ''">PROSES BISNIS KAMI</h3>
                    </button>
                    <div x-show="selected === 4" x-collapse class="px-4 md:px-8 pb-10 md:pl-[6.5rem]">
                        <p class="mb-6 text-lg text-teal-100 max-w-3xl">Alur bisnis <i>resources</i> yang berurutan dalam operasional utama perusahaan:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-4 gap-x-6 max-w-5xl">
                            <div class="flex items-center text-teal-50"><span class="w-8 h-8 flex items-center justify-center bg-teal-800 border border-teal-600 rounded-full text-sm font-bold mr-3">1</span> Nursery Cultivation</div>
                            <div class="flex items-center text-teal-50"><span class="w-8 h-8 flex items-center justify-center bg-teal-800 border border-teal-600 rounded-full text-sm font-bold mr-3">2</span> Field Planting</div>
                            <div class="flex items-center text-teal-50"><span class="w-8 h-8 flex items-center justify-center bg-teal-800 border border-teal-600 rounded-full text-sm font-bold mr-3">3</span> Up Keep</div>
                            <div class="flex items-center text-teal-50"><span class="w-8 h-8 flex items-center justify-center bg-teal-800 border border-teal-600 rounded-full text-sm font-bold mr-3">4</span> Harvesting</div>
                            <div class="flex items-center text-teal-50"><span class="w-8 h-8 flex items-center justify-center bg-teal-800 border border-teal-600 rounded-full text-sm font-bold mr-3">5</span> Palm Oil Mill</div>
                            <div class="flex items-center text-teal-50"><span class="w-8 h-8 flex items-center justify-center bg-teal-800 border border-teal-600 rounded-full text-sm font-bold mr-3">6</span> Downstream Processing</div>
                            <div class="flex items-center text-teal-50"><span class="w-8 h-8 flex items-center justify-center bg-teal-800 border border-teal-600 rounded-full text-sm font-bold mr-3">7</span> Sales To Customers</div>
                        </div>
                    </div>
                </div>
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
