<x-layout>
    <x-slot name="title">Program Kami - FR Academy</x-slot>

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
                Bentuk Masa Depan Anda Bersama <span class="text-green-accent">FR Academy</span>
            </h1>
            <p class="text-lg md:text-xl text-teal-100 max-w-2xl mx-auto font-light">
                Pusat pengembangan kompetensi dan pembentukan karakter kerja untuk menciptakan human capital unggul, adaptif, dan siap menghadapi tantangan bisnis First Resources Group.
            </p>
        </div>
    </section>

    <!-- Sticky Tab Navigation (Alpine.js) -->
    <div x-data="{ activeTab: 'reguler' }" class="relative">
        <div class="sticky top-20 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 shadow-sm transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex justify-center md:justify-start space-x-2 md:space-x-8 overflow-x-auto no-scrollbar py-4" aria-label="Tabs">
                    <button @click="activeTab = 'reguler'; document.getElementById('reguler').scrollIntoView({behavior: 'smooth', block: 'start', inline: 'nearest'})"
                            :class="activeTab === 'reguler' ? 'bg-teal-primary text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-teal-primary dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-gray-800'"
                            class="px-4 py-2.5 rounded-full font-bold text-sm transition-all duration-300 whitespace-nowrap">
                        Program Reguler
                    </button>
                    <button @click="activeTab = 'non-reguler'; document.getElementById('non-reguler').scrollIntoView({behavior: 'smooth', block: 'start', inline: 'nearest'})"
                            :class="activeTab === 'non-reguler' ? 'bg-teal-primary text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-teal-primary dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-gray-800'"
                            class="px-4 py-2.5 rounded-full font-bold text-sm transition-all duration-300 whitespace-nowrap">
                        Program Non-Reguler
                    </button>
                    <button @click="activeTab = 'talent'; document.getElementById('talent').scrollIntoView({behavior: 'smooth', block: 'start', inline: 'nearest'})"
                            :class="activeTab === 'talent' ? 'bg-teal-primary text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-teal-primary dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-gray-800'"
                            class="px-4 py-2.5 rounded-full font-bold text-sm transition-all duration-300 whitespace-nowrap">
                        Inisiatif Program
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
                                if(entry.isIntersecting) {
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
            <!-- SECTION 1: PROGRAM REGULER -->
            <section id="reguler" class="scroll-section pt-24 pb-16 bg-gray-50 dark:bg-gray-900 transition-colors duration-300 scroll-mt-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16" data-aos="fade-up">
                        <h2 class="text-3xl md:text-4xl font-extrabold text-teal-primary dark:text-white mb-4">Program Reguler</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                            Program pelatihan dasar dan komprehensif bagi karyawan baru maupun calon karyawan yang akan ditempatkan di berbagai unit operasional.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @php
                            $regulerPrograms = [
                                [
                                    'title' => 'Field Assistant Training (FAT)',
                                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>',
                                    'desc' => 'Program yang dirancang untuk menyiapkan calon Asisten Kebun agar memiliki pengetahuan, keterampilan, dan sikap kerja sesuai budaya First Resources.',
                                    'delay' => 100
                                ],
                                [
                                    'title' => 'Mill Assistant Training (MAT)',
                                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
                                    'desc' => 'Program yang dirancang untuk menyiapkan calon Asisten Pabrik dengan kompetensi teknis dan kepemimpinan yang sesuai kebutuhan operasional pabrik.',
                                    'delay' => 200
                                ],
                                [
                                    'title' => 'Traction Assistant Training (TAT)',
                                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>',
                                    'desc' => 'Program untuk menyiapkan calon Asisten Traksi yang memiliki kemampuan teknis dan manajerial sesuai standar perusahaan.',
                                    'delay' => 300
                                ],
                                [
                                    'title' => 'Administration Assistant Training (AAT)',
                                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                                    'desc' => 'Program untuk mempersiapkan calon Asisten Administrasi yang memiliki kompetensi administrasi, analisis, dan pengelolaan kerja sesuai budaya perusahaan.',
                                    'delay' => 400
                                ],
                                [
                                    'title' => 'Downstream Management Trainee (DMT)',
                                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>',
                                    'desc' => 'Program yang dirancang untuk menyiapkan calon supervisor di unit downstream agar memiliki kompetensi teknis dan perilaku kerja yang sesuai dengan kebutuhan operasional.',
                                    'delay' => 500
                                ]
                            ];
                        @endphp

                        @foreach($regulerPrograms as $prog)
                            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 flex flex-col h-full" data-aos="fade-up" data-aos-delay="{{ $prog['delay'] }}">
                                <!-- Hover Glow Effect -->
                                <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-teal-400 to-green-accent opacity-0 group-hover:opacity-10 transition-opacity duration-300 pointer-events-none"></div>
                                
                                <div class="w-16 h-16 bg-teal-50 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 group-hover:scale-110 transition-transform duration-300">
                                    {!! $prog['icon'] !!}
                                </div>
                                <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-4 group-hover:text-teal-primary dark:group-hover:text-teal-400 transition-colors">
                                    {{ $prog['title'] }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-6 flex-grow">
                                    {{ $prog['desc'] }}
                                </p>
                                
                                <!-- Duration Badge -->
                                <div class="mt-auto">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-[#ecfdf5] text-[#059669] border border-[#a7f3d0] dark:bg-green-900/30 dark:text-green-400 dark:border-green-800">
                                        ⏳ Durasi: 6 Bulan + Mentoring
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- SECTION 2: PROGRAM NON-REGULER -->
            <section id="non-reguler" class="scroll-section py-20 bg-white dark:bg-gray-950 transition-colors duration-300 scroll-mt-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row gap-16 items-start">
                        <div class="lg:w-1/3" data-aos="fade-right">
                            <h2 class="text-3xl md:text-4xl font-extrabold text-teal-primary dark:text-white mb-6">Program Non-Reguler</h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                                Pelatihan berdurasi singkat yang disusun secara dinamis untuk meningkatkan kompetensi teknis spesifik dan memecahkan tantangan operasional terkini di berbagai divisi.
                            </p>
                            <div class="hidden lg:block relative rounded-2xl overflow-hidden shadow-lg h-64 w-full">
                                <img src="https://i.ibb.co.com/3mg5bHQN/image-14.png" alt="Non Reguler Training" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-teal-900/30 mix-blend-multiply"></div>
                            </div>
                        </div>

                        <!-- Accordion Alpine.js -->
                        <div class="lg:w-2/3 w-full" x-data="{ activeAccordion: null }" data-aos="fade-left">
                            @php
                                $nonReguler = [
                                    ['id' => 1, 'title' => 'Teknikal Estate', 'desc' => 'Klinik Kinerja Mandor, Estate Coaching Program, Pengelolaan TBM, Klinik HPT, Manajemen Pemupukan dll.'],
                                    ['id' => 2, 'title' => 'Teknikal Mill', 'desc' => 'Basic Mechanic, Mill Mentoring, Manajemen Operasional Boiler, Pengelolaan Water Treatment, Total Productive Maintenance, Klinik Kinerja Pabrik, dll.'],
                                    ['id' => 3, 'title' => 'Teknikal Traksi', 'desc' => 'Manajemen Workshop, Operation and Maintenance DT, Perawatan Genset, Defensive Driving, Overhaul DT Euro 4, dll.'],
                                    ['id' => 4, 'title' => 'Teknikal Downstream', 'desc' => 'Operation and Maintenance Pump, Ref and Frac Operation for Operator, Sistem Kalibrasi PLC, Operasional Oleochemical for Operator, dll.'],
                                    ['id' => 5, 'title' => 'Teknikal Administrasi & Sustain', 'desc' => 'Warehouse Management, Pemadaman Kebakaran Hutan dan Lahan, K3-First Aid, Mentoring Asisten Administrasi, Pelatihan Pamong TPA, dll.'],
                                    ['id' => 6, 'title' => 'Managerial & Softskill', 'desc' => '7 Habits, Leader As Coach, Supervisory Management untuk Asisten, Fundamental Leadership untuk Askep, Strategic Leadership untuk Manager, Problem Solving and Decision Making, dll.']
                                ];
                            @endphp

                            <div class="space-y-4">
                                @foreach($nonReguler as $item)
                                    <div class="border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden transition-all duration-300 bg-gray-50 dark:bg-gray-900/50"
                                         :class="activeAccordion === {{ $item['id'] }} ? 'ring-2 ring-teal-primary/50 shadow-md' : 'hover:border-teal-300 dark:hover:border-teal-700'">
                                        <button @click="activeAccordion = activeAccordion === {{ $item['id'] }} ? null : {{ $item['id'] }}" 
                                                class="w-full flex items-center justify-between p-5 text-left focus:outline-none">
                                            <span class="font-bold text-lg text-gray-900 dark:text-gray-100" :class="activeAccordion === {{ $item['id'] }} ? 'text-teal-primary dark:text-teal-400' : ''">
                                                {{ $item['title'] }}
                                            </span>
                                            <span class="ml-6 flex items-center justify-center w-8 h-8 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 text-teal-600 transition-transform duration-300"
                                                  :class="activeAccordion === {{ $item['id'] }} ? 'rotate-180' : ''">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </span>
                                        </button>
                                        <div x-show="activeAccordion === {{ $item['id'] }}" x-collapse>
                                            <div class="px-5 pb-5 pt-2 text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-800 border-dashed mt-2">
                                                <div class="flex items-start mt-3">
                                                    <svg class="w-5 h-5 text-green-accent mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <p class="leading-relaxed text-sm">Contoh Program: <br> <span class="font-medium text-gray-800 dark:text-gray-300">{{ $item['desc'] }}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 3: TALENT MANAGEMENT -->
            <section id="talent" class="scroll-section py-20 bg-teal-950 text-white relative overflow-hidden scroll-mt-20">
                <!-- Background Glow -->
                <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-1/4 -left-1/4 w-1/2 h-full bg-teal-800/40 blur-[120px] rounded-full mix-blend-screen"></div>
                    <div class="absolute bottom-0 -right-1/4 w-1/2 h-1/2 bg-green-900/30 blur-[100px] rounded-full mix-blend-screen"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="bg-teal-900/60 backdrop-blur-lg border border-teal-700 rounded-3xl p-8 md:p-12 lg:p-16 flex flex-col lg:flex-row items-center gap-12" data-aos="zoom-in">
                        <div class="lg:w-1/2">
                            <span class="inline-block py-1 px-3 rounded-full bg-teal-800 border border-teal-600 text-teal-200 text-sm font-bold tracking-wider uppercase mb-6">
                                Inovasi & Kolaborasi
                            </span>
                            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight">
                                Inisiatif <span class="text-green-accent">Program</span>
                            </h2>
                            <p class="text-lg text-teal-100 mb-8 leading-relaxed font-light">
                                Beragam inisiatif strategis dan kegiatan komprehensif yang dirancang khusus untuk mendorong budaya inovasi, peningkatan kompetensi, serta kolaborasi antar divisi.
                            </p>
                            
                            <ul class="space-y-4 mb-6">
                                <li class="flex items-center text-teal-50 bg-teal-800/50 p-3 rounded-xl border border-teal-700">
                                    <span class="w-8 h-8 flex shrink-0 items-center justify-center bg-green-accent text-teal-900 rounded-full text-sm font-bold mr-4">✓</span> 
                                    <span class="font-semibold text-lg">Klinik Kinerja Mandor</span>
                                </li>
                                <li class="flex items-center text-teal-50 bg-teal-800/50 p-3 rounded-xl border border-teal-700">
                                    <span class="w-8 h-8 flex shrink-0 items-center justify-center bg-green-accent text-teal-900 rounded-full text-sm font-bold mr-4">✓</span> 
                                    <span class="font-semibold text-lg">Learning Festival</span>
                                </li>
                            </ul>
                        </div>
                        <div class="lg:w-1/2 w-full">
                            <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-teal-800/50 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                                <img src="https://i.ibb.co.com/XrLSNbzC/image-13.png" alt="Talent Management" class="w-full h-auto object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-teal-950 via-transparent to-transparent"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- SECTION 4: LEARNING STATIONS -->
            <section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16" data-aos="fade-up">
                        <span class="text-teal-primary dark:text-teal-400 font-bold tracking-wider text-sm uppercase">Fasilitas Kami</span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mt-2 mb-4">Learning Stations Terintegrasi</h2>
                        <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                            Selain pusat akademi utama, FR Academy juga membekali proses pembelajarannya melalui fasiltias Learning Station yang tersebar langsung di wilayah operasional utama.
                        </p>
                    </div>

                    <div class="max-w-4xl mx-auto flex justify-center" data-aos="zoom-in">
                        <img src="{{ asset('images/peta.png') }}" alt="Peta Persebaran Learning Station" class="w-full h-auto object-contain drop-shadow-xl">
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- CTA Section -->
    <section class="py-24 bg-white dark:bg-gray-950 transition-colors duration-300">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-teal-primary to-teal-800 rounded-3xl p-10 md:p-16 text-center text-white shadow-2xl relative overflow-hidden" data-aos="zoom-in">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-green-accent opacity-10 rounded-full blur-2xl"></div>
                
                <h2 class="text-3xl md:text-5xl font-extrabold mb-6 relative z-10">Siap Untuk Berkembang Bersama Kami?</h2>
                <p class="text-teal-100 text-lg mb-10 max-w-2xl mx-auto relative z-10">
                    Jadilah bagian dari perjalanan luar biasa kami dalam mencetak pemimpin dan profesional andal di industri kelapa sawit.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 relative z-10">
                    <a href="/career" class="bg-green-accent hover:bg-green-500 text-white font-bold py-4 px-8 rounded-xl transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Lihat Lowongan / Pendaftaran
                    </a>
                    <a href="/tentang-kami" class="bg-teal-900/50 hover:bg-teal-900 border border-teal-500/30 text-white font-bold py-4 px-8 rounded-xl transition duration-300">
                        Lebih Dekat Dengan Kami
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layout>
