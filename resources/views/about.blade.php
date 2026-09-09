<x-layout>
    <!-- Hero Section -->
    <section class="relative h-[350px] md:h-[400px] pt-20 overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/welcomefr.jpg') }}" alt="Tentang FR Academy" class="w-full h-full object-cover object-center" data-aos="zoom-out" data-aos-duration="2000">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-green-accent font-bold tracking-widest uppercase text-sm mb-4 block">
                TENTANG KAMI
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">
                Sejarah & Profil <span class="text-green-accent">FR Academy</span>
            </h1>
            <p class="text-lg md:text-xl text-teal-100 max-w-2xl mx-auto font-light">
                Menjadi Center of Excellence dalam membentuk sumber daya manusia yang handal dan berkarakter unggul sejak tahun 1996.
            </p>
        </div>
    </section>

    <!-- Sejarah Timeline Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white">Perjalanan Kami</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">Dari pusat pelatihan awal hingga menjadi Center of Excellence yang terintegrasi.</p>
            </div>
            
            <div class="relative max-w-4xl mx-auto">
                <!-- Line -->
                <div class="absolute left-0 md:left-1/2 top-0 bottom-0 w-1 bg-green-accent/30 -ml-[0.1rem] md:-ml-0.5 z-0 rounded"></div>
                
                <div class="space-y-12 relative z-10">
                    <!-- 1996 -->
                    <div class="flex flex-col md:flex-row items-start md:items-center w-full" data-aos="fade-up">
                        <div class="md:w-1/2 md:pr-12 md:text-right mb-4 md:mb-0 ml-12 md:ml-0 order-2 md:order-1">
                            <h3 class="text-2xl font-bold text-teal-primary dark:text-white">Pusat Pendidikan & Latihan Perkebunan</h3>
                            <p class="text-gray-600 dark:text-gray-300 mt-2">Pusat pelatihan pertama didirikan di Kebun Meridian (PT. MSSP) dengan nama Pusat Pendidikan dan Latihan Perkebunan (PPLP).</p>
                        </div>
                        <div class="absolute left-0 md:left-1/2 transform md:-translate-x-1/2 w-8 h-8 rounded-full bg-green-accent border-4 border-white dark:border-gray-900 flex items-center justify-center shadow-lg -ml-4 md:ml-0 order-1 md:order-2"></div>
                        <div class="md:w-1/2 md:pl-12 ml-12 md:ml-0 order-3 flex items-center h-full">
                            <span class="text-4xl font-black text-teal-200 dark:text-teal-900/50">1996</span>
                        </div>
                    </div>
                    
                    <!-- 2002 -->
                    <div class="flex flex-col md:flex-row items-start md:items-center w-full" data-aos="fade-up" data-aos-delay="100">
                        <div class="md:w-1/2 md:pr-12 md:text-right ml-12 md:ml-0 flex items-center justify-end h-full md:order-1">
                            <span class="text-4xl font-black text-teal-200 dark:text-teal-900/50 hidden md:block">2002</span>
                        </div>
                        <div class="absolute left-0 md:left-1/2 transform md:-translate-x-1/2 w-8 h-8 rounded-full bg-teal-primary border-4 border-white dark:border-gray-900 flex items-center justify-center shadow-lg -ml-4 md:ml-0 order-1 md:order-2"></div>
                        <div class="md:w-1/2 md:pl-12 mb-4 md:mb-0 ml-12 md:ml-0 order-2 md:order-3">
                            <span class="text-4xl font-black text-teal-200 dark:text-teal-900/50 md:hidden mb-2 block">2002</span>
                            <h3 class="text-2xl font-bold text-teal-primary dark:text-white">Relokasi ke Kebun CLP</h3>
                            <p class="text-gray-600 dark:text-gray-300 mt-2">Memindahkan operasional ke Kebun CLP dengan bangunan dan fasilitas yang lebih memadai untuk mengakomodasi kebutuhan pelatihan yang meningkat.</p>
                        </div>
                    </div>
                    
                    <!-- 2010 -->
                    <div class="flex flex-col md:flex-row items-start md:items-center w-full" data-aos="fade-up" data-aos-delay="200">
                        <div class="md:w-1/2 md:pr-12 md:text-right mb-4 md:mb-0 ml-12 md:ml-0 order-2 md:order-1">
                            <h3 class="text-2xl font-bold text-teal-primary dark:text-white">FR Academy (Center of Excellence)</h3>
                            <p class="text-gray-600 dark:text-gray-300 mt-2">Berpindah ke Kebun Kubang (PT. PSG), Kampar, Riau. Secara resmi berganti nama menjadi FR Academy dan menjadi Center of Excellence untuk First Resources Group.</p>
                        </div>
                        <div class="absolute left-0 md:left-1/2 transform md:-translate-x-1/2 w-8 h-8 rounded-full bg-green-accent border-4 border-white dark:border-gray-900 flex items-center justify-center shadow-lg shadow-green-accent/40 -ml-4 md:ml-0 order-1 md:order-2">
                            <div class="w-3 h-3 bg-white rounded-full"></div>
                        </div>
                        <div class="md:w-1/2 md:pl-12 ml-12 md:ml-0 order-3 flex items-center h-full">
                            <span class="text-5xl font-black text-green-accent">2010</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tenaga Pengajar & Metode Belajar -->
    <section class="py-20 bg-white dark:bg-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2" data-aos="fade-right">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl aspect-video lg:aspect-square group">
                        <img src="{{ asset('images/slide4.png') }}" alt="Tenaga Pengajar" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-teal-900/80 to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="bg-white/20 backdrop-blur-md rounded-xl p-4 border border-white/30 text-white">
                                <p class="font-bold text-lg">Subject Matter Expert (SME)</p>
                                <p class="text-sm opacity-90">Praktisi andal dari internal perusahaan</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-1/2" data-aos="fade-left">
                    <span class="text-green-accent font-bold tracking-wider uppercase text-sm">KEUNGGULAN KAMI</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-6 leading-tight">Pengajar Praktisi & Metode 70:20:10</h2>
                    <p class="text-gray-600 dark:text-gray-300 text-lg mb-8 leading-relaxed">
                        Kami meyakini bahwa pembelajaran terbaik berasal dari mereka yang mengalaminya langsung. Pengajar di FR Academy didominasi oleh internal First Resources Group, mulai dari level <strong>Direktur, Controller, hingga Manager</strong> yang bertindak sebagai <em>Subject Matter Expert (SME)</em>.
                    </p>
                    
                    <div class="bg-teal-50 dark:bg-gray-700/50 rounded-2xl p-6 border border-teal-100 dark:border-gray-600">
                        <h4 class="font-bold text-teal-primary dark:text-white mb-4 flex items-center">
                            <svg class="w-6 h-6 text-green-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            Formula Pembelajaran Efektif
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm font-semibold mb-1">
                                    <span class="text-gray-700 dark:text-gray-200">Experiential Learning (Praktik Langsung)</span>
                                    <span class="text-green-accent">70%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-[#59A338] h-2 rounded-full" style="width: 70%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm font-semibold mb-1">
                                    <span class="text-gray-700 dark:text-gray-200">Social Learning (Mentoring & Coaching)</span>
                                    <span class="text-teal-500">20%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-[#78c649] h-2 rounded-full" style="width: 20%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm font-semibold mb-1">
                                    <span class="text-gray-700 dark:text-gray-200">Formal Learning (Kelas & Teori)</span>
                                    <span class="text-blue-500">10%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-[#114045] h-2 rounded-full" style="width: 10%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fasilitas Kampus -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-green-accent font-bold tracking-wider uppercase text-sm">INFRASTRUKTUR</span>
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white mt-4">Fasilitas Kampus Kubang</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">Kami menyediakan lingkungan yang dirancang khusus untuk mendukung proses belajar mengajar secara optimal.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200 dark:shadow-none hover:-translate-y-2 transition-transform duration-300" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-16 h-16 rounded-2xl bg-green-50 dark:bg-gray-700 flex items-center justify-center text-green-accent mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-2">300 Hektare</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Area praktik lapangan (Simulasi) untuk pembelajaran teknis agronomi secara langsung.</p>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200 dark:shadow-none hover:-translate-y-2 transition-transform duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 rounded-2xl bg-teal-50 dark:bg-gray-700 flex items-center justify-center text-teal-primary mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-2">6 Ruang Kelas</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Dilengkapi dengan Learning Management System (LMS) modern untuk interaksi maksimal.</p>
                </div>
                
                <!-- Card 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200 dark:shadow-none hover:-translate-y-2 transition-transform duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-gray-700 flex items-center justify-center text-blue-500 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-2">Asrama 100 Orang</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Memiliki 48 kamar berjenis twin-bed dengan fasilitas kenyamanan bagi peserta didik.</p>
                </div>
                
                <!-- Card 4 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-xl shadow-gray-200 dark:shadow-none hover:-translate-y-2 transition-transform duration-300" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 dark:bg-gray-700 flex items-center justify-center text-orange-500 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-2">Olahraga & Outbound</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Fasilitas pendukung mencakup sarana ibadah, area olahraga, dan lahan outbound khusus.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ekspansi Learning Station -->
    <section class="py-20 bg-teal-primary text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Ekspansi Learning Station</h2>
                <p class="text-teal-100 max-w-2xl mx-auto">Kami terus berkembang dengan mendirikan pos pembelajaran di lokasi operasional strategis yang berfokus pada persiapan kedisiplinan dan teknis tenaga supervisi seperti Mandor dan Kerani.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Kalbar -->
                <div class="bg-teal-900/50 border border-teal-700 rounded-2xl p-8 hover:bg-teal-800/80 transition duration-300" data-aos="fade-right">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-accent rounded-full flex items-center justify-center mt-1">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold mb-2 text-white">Learning Station Kalbar</h3>
                            <p class="text-teal-200">Berlokasi di Kebun Limpah Sejahtera, Provinsi Kalimantan Barat.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Kubar -->
                <div class="bg-teal-900/50 border border-teal-700 rounded-2xl p-8 hover:bg-teal-800/80 transition duration-300" data-aos="fade-left">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-accent rounded-full flex items-center justify-center mt-1">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold mb-2 text-white">Learning Station Kutai Barat</h3>
                            <p class="text-teal-200">Berlokasi di Kebun Cak Begai, Provinsi Kalimantan Timur.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
