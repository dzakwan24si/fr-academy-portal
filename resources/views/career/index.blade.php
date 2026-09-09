<x-layout>
    <!-- Hero Section -->
    <section class="relative h-[350px] md:h-[400px] pt-20 overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/slide3.png') }}" alt="Karir FR Academy" class="w-full h-full object-cover object-center" data-aos="zoom-out" data-aos-duration="2000">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-green-accent font-bold tracking-widest uppercase text-sm mb-4 block">
                Bergabung Bersama Kami
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">
                Karir <span class="text-green-accent">First Resources</span>
            </h1>
            <p class="text-lg md:text-xl text-teal-100 max-w-2xl mx-auto font-light">
                Temukan peluang karir dan jadilah bagian dari perjalanan luar biasa kami dalam mencetak profesional andal di industri kelapa sawit.
            </p>
        </div>
    </section>

    <!-- Posisi Program Tersedia Section -->
    <section id="posisi" class="py-24 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">PILIHAN KARIR</span>
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-4">Posisi Program Tersedia</h2>
                <p class="text-gray-600 dark:text-gray-300">Pilih jalur karir yang sesuai dengan latar belakang dan minat Anda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- FAT Card -->
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-8 text-center transition-transform hover:-translate-y-2 duration-300 shadow-lg border border-gray-100 dark:border-gray-600 flex flex-col items-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-teal-50 dark:bg-gray-800 rounded-full flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3">Field Assistant Training (FAT)</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 flex-grow">Program pelatihan untuk menjadi asisten lapangan yang tangguh dan menguasai teknis agronomis perkebunan.</p>
                    <a href="#" class="w-full bg-white text-green-accent border border-green-accent hover:bg-green-accent hover:text-white px-6 py-2 rounded-full font-semibold transition duration-300">Daftar FAT</a>
                </div>

                <!-- TAT Card -->
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-8 text-center transition-transform hover:-translate-y-2 duration-300 shadow-lg border border-gray-100 dark:border-gray-600 flex flex-col items-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-teal-50 dark:bg-gray-800 rounded-full flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3">Traction Assistant Training (TAT)</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 flex-grow">Mendidik calon pemimpin di bidang transportasi dan alat berat untuk mendukung operasional kebun.</p>
                    <a href="#" class="w-full bg-white text-green-accent border border-green-accent hover:bg-green-accent hover:text-white px-6 py-2 rounded-full font-semibold transition duration-300">Daftar TAT</a>
                </div>

                <!-- MAT Card -->
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-8 text-center transition-transform hover:-translate-y-2 duration-300 shadow-lg border border-gray-100 dark:border-gray-600 flex flex-col items-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-teal-50 dark:bg-gray-800 rounded-full flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3">Mill Assistant Training (MAT)</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 flex-grow">Program persiapan menjadi asisten pabrik kelapa sawit dengan kemampuan operasional handal.</p>
                    <a href="#" class="w-full bg-white text-green-accent border border-green-accent hover:bg-green-accent hover:text-white px-6 py-2 rounded-full font-semibold transition duration-300">Daftar MAT</a>
                </div>

                <!-- AAT Card -->
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-8 text-center transition-transform hover:-translate-y-2 duration-300 shadow-lg border border-gray-100 dark:border-gray-600 flex flex-col items-center" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-16 h-16 bg-teal-50 dark:bg-gray-800 rounded-full flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-3">Administration Assistant (AAT)</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-6 flex-grow">Pelatihan khusus bidang tata usaha, keuangan, dan administrasi umum perkebunan serta pabrik.</p>
                    <a href="#" class="w-full bg-white text-green-accent border border-green-accent hover:bg-green-accent hover:text-white px-6 py-2 rounded-full font-semibold transition duration-300">Daftar AAT</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Informasi Penting Rekrutmen -->
    <section id="informasi" class="py-24 bg-teal-900 text-white relative overflow-hidden transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold mt-4 mb-4">Informasi Penting Rekrutmen</h2>
                <p class="text-teal-200">Catatan resmi dari Tim Rekrutmen First Resources Group.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Info 1 -->
                <div class="bg-teal-800/50 p-8 rounded-2xl border border-teal-700/50 text-center flex flex-col items-center" data-aos="zoom-in" data-aos-delay="100">
                    <div class="w-14 h-14 bg-green-accent/20 rounded-full flex items-center justify-center text-green-accent mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Gratis / Tanpa Biaya</h3>
                    <p class="text-teal-100 text-sm">Proses Rekrutmen First Resources Group Tidak Pernah memungut biaya apapun. Hati-hati terhadap penipuan.</p>
                </div>
                
                <!-- Info 2 -->
                <div class="bg-teal-800/50 p-8 rounded-2xl border border-teal-700/50 text-center flex flex-col items-center" data-aos="zoom-in" data-aos-delay="200">
                    <div class="w-14 h-14 bg-green-accent/20 rounded-full flex items-center justify-center text-green-accent mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Privasi Terjaga</h3>
                    <p class="text-teal-100 text-sm">Kerahasiaan data pelamar dijamin sepenuhnya dan hanya digunakan untuk keperluan rekrutmen internal.</p>
                </div>
                
                <!-- Info 3 -->
                <div class="bg-teal-800/50 p-8 rounded-2xl border border-teal-700/50 text-center flex flex-col items-center" data-aos="zoom-in" data-aos-delay="300">
                    <div class="w-14 h-14 bg-green-accent/20 rounded-full flex items-center justify-center text-green-accent mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Sistem Gugur</h3>
                    <p class="text-teal-100 text-sm">Hanya kandidat yang memenuhi kualifikasi terbaik yang akan diproses ke tahapan seleksi berikutnya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Persiapan Pendaftaran Section -->
    <section class="py-24 bg-white dark:bg-gray-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">PANDUAN</span>
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-4">Persiapan Pendaftaran</h2>
                <p class="text-gray-600 dark:text-gray-300">Siapkan data-data berikut sebelum Anda mulai mengisi formulir pendaftaran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- Preparation 1 -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-4">Data Akademik</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Nama Universitas / Institusi</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Program Studi / Jurusan</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Indeks Prestasi Kumulatif (IPK)</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Tahun Lulus & Lama Masa Studi</li>
                    </ul>
                </div>

                <!-- Preparation 2 -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-4">Data Fisik & Medis</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Tinggi dan Berat Badan Saat Ini</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Riwayat Penyakit Berat / Operasi</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Status Penggunaan Kacamata</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Riwayat Patah Tulang (Jika Ada)</li>
                    </ul>
                </div>

                <!-- Preparation 3 -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 shadow-sm" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center text-teal-primary dark:text-teal-400 mb-6 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-teal-primary dark:text-white mb-4">Demografi & Lokasi Tes</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Data Asal Daerah (Kota/Provinsi)</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Pekerjaan Orang Tua Saat Ini</li>
                        <li class="flex items-start"><span class="text-green-accent mr-2">•</span> Memilih Lokasi Tes offline (Pilih 1 dari 4): Pekanbaru, Medan, Jogjakarta, atau Jakarta</li>
                    </ul>
                </div>
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                <a href="#posisi" class="inline-block bg-green-accent hover:bg-green-600 text-white px-10 py-4 rounded-full font-bold transition duration-300 shadow-lg text-lg">
                    Mulai Pendaftaran
                </a>
            </div>
        </div>
    </section>
</x-layout>
