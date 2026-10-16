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
    <section id="posisi" x-data="{ modalOpen: false, selectedProgram: null }" class="py-24 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-green-accent font-semibold tracking-wider text-sm uppercase">PILIHAN KARIR</span>
                <h2 class="text-3xl md:text-4xl font-bold text-teal-primary dark:text-white mt-4 mb-4">Posisi Program Tersedia</h2>
                <p class="text-gray-600 dark:text-gray-300">Pilih jalur karir yang sesuai dengan latar belakang dan minat Anda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-7">
                @foreach($programs as $program)
                @php
                    $code = strtoupper(trim($program->code ?? ''));
                    // Theme config per program
                    $theme = match($code) {
                        'FAT' => [
                            'tag' => 'Kebun & Agronomi',
                            'sub' => 'Field Operations',
                            'badge_bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
                            'icon_bg' => 'from-emerald-500 to-green-600',
                            'glow' => 'group-hover:shadow-emerald-500/20 group-hover:border-emerald-300',
                            'btn' => 'group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 text-emerald-700 border-emerald-200 bg-emerald-50/50',
                            'top_bar' => 'from-emerald-500 to-teal-500',
                            'illustration' => asset('images/bg_card_fat.jpg'),
                            'accent_badge' => 'Kebun Kelapa Sawit',
                        ],
                        'MAT' => [
                            'tag' => 'Pabrik Minyak Kelapa Sawit (PMKS)',
                            'sub' => 'Mill Processing',
                            'badge_bg' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
                            'icon_bg' => 'from-amber-500 to-orange-600',
                            'glow' => 'group-hover:shadow-amber-500/20 group-hover:border-amber-300',
                            'btn' => 'group-hover:bg-amber-600 group-hover:text-white group-hover:border-amber-600 text-amber-700 border-amber-200 bg-amber-50/50',
                            'top_bar' => 'from-amber-500 to-orange-500',
                            'illustration' => asset('images/bg_card_mat.jpg'),
                            'accent_badge' => 'Truk Tangki CPO & Mill',
                        ],
                        'TAT' => [
                            'tag' => 'Traksi, Bengkel & Alat Berat',
                            'sub' => 'Traction & Heavy Equipment',
                            'badge_bg' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
                            'icon_bg' => 'from-sky-500 to-blue-600',
                            'glow' => 'group-hover:shadow-sky-500/20 group-hover:border-sky-300',
                            'btn' => 'group-hover:bg-sky-600 group-hover:text-white group-hover:border-sky-600 text-sky-700 border-sky-200 bg-sky-50/50',
                            'top_bar' => 'from-sky-500 to-blue-600',
                            'illustration' => asset('images/bg_card_tat.jpg'),
                            'accent_badge' => 'Ekskavator & Alat Berat',
                        ],
                        'AAT' => [
                            'tag' => 'Administrasi, Keuangan & KTU',
                            'sub' => 'Administration & Finance',
                            'badge_bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
                            'icon_bg' => 'from-indigo-500 to-purple-600',
                            'glow' => 'group-hover:shadow-indigo-500/20 group-hover:border-indigo-300',
                            'btn' => 'group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 text-indigo-700 border-indigo-200 bg-indigo-50/50',
                            'top_bar' => 'from-indigo-500 to-purple-600',
                            'illustration' => asset('images/bg_card_aat.jpg'),
                            'accent_badge' => 'Staf Administrasi & Kantor',
                        ],
                        'DMT' => [
                            'tag' => 'Data Management & Digitalization',
                            'sub' => 'Data & Information Technology',
                            'badge_bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800',
                            'icon_bg' => 'from-cyan-500 to-teal-600',
                            'glow' => 'group-hover:shadow-cyan-500/20 group-hover:border-cyan-300',
                            'btn' => 'group-hover:bg-cyan-600 group-hover:text-white group-hover:border-cyan-600 text-cyan-700 border-cyan-200 bg-cyan-50/50',
                            'top_bar' => 'from-cyan-500 to-teal-600',
                            'illustration' => asset('images/bg_card_dmt.jpg'),
                            'accent_badge' => 'Data Center & Analytics',
                        ],
                        'PMT' => [
                            'tag' => 'Plantation Management & Agronomy',
                            'sub' => 'Estate Field Leadership',
                            'badge_bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
                            'icon_bg' => 'from-emerald-600 to-teal-700',
                            'glow' => 'group-hover:shadow-emerald-500/20 group-hover:border-emerald-300',
                            'btn' => 'group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 text-emerald-700 border-emerald-200 bg-emerald-50/50',
                            'top_bar' => 'from-emerald-600 to-teal-600',
                            'illustration' => asset('images/bg_card_pmt.jpg'),
                            'accent_badge' => 'Plantation Management',
                        ],
                        'MA' => [
                            'tag' => 'Management Associate & Leadership',
                            'sub' => 'Future Executive Leaders',
                            'badge_bg' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
                            'icon_bg' => 'from-purple-600 to-indigo-700',
                            'glow' => 'group-hover:shadow-purple-500/20 group-hover:border-purple-300',
                            'btn' => 'group-hover:bg-purple-600 group-hover:text-white group-hover:border-purple-600 text-purple-700 border-purple-200 bg-purple-50/50',
                            'top_bar' => 'from-purple-600 to-indigo-600',
                            'illustration' => asset('images/bg_card_ma.jpg'),
                            'accent_badge' => 'Executive Trainee',
                        ],
                        default => [
                            'tag' => 'Program Pelatihan Reguler',
                            'sub' => 'Professional Development',
                            'badge_bg' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800',
                            'icon_bg' => 'from-teal-600 to-green-600',
                            'glow' => 'group-hover:shadow-green-500/20 group-hover:border-green-300',
                            'btn' => 'group-hover:bg-green-600 group-hover:text-white group-hover:border-green-600 text-teal-700 border-gray-200 bg-gray-50',
                            'top_bar' => 'from-teal-600 to-green-600',
                            'illustration' => asset('images/bg_card_fat.jpg'),
                            'accent_badge' => 'Pelatihan Terpadu',
                        ]
                    };
                @endphp
                @php
                    $imageUrl = $program->image ? asset('storage/' . $program->image) : ($theme['illustration'] ?? asset('images/bg_card_fat.jpg'));
                @endphp
                <!-- Modern Premium Dynamic Card -->
                <div data-program="{{ json_encode([
                        'id' => $program->id,
                        'name' => $program->name,
                        'code' => $program->code,
                        'description' => $program->description,
                        'qualifications' => $program->qualifications,
                        'gform_url' => $program->gform_url,
                        'tag' => $theme['tag'],
                        'sub' => $theme['sub'],
                    ]) }}" @click.prevent.stop="selectedProgram = JSON.parse($el.dataset.program); modalOpen = true" 
                     class="group relative bg-white dark:bg-gray-800 rounded-3xl transition-all duration-500 hover:-translate-y-2.5 shadow-[0_8px_30px_rgb(0,0,0,0.06)] hover:shadow-[0_20px_40px_rgb(0,0,0,0.12)] border border-gray-100/80 dark:border-gray-700/80 flex flex-col justify-between cursor-pointer overflow-hidden {{ $theme['glow'] }}" 
                     data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    
                    <!-- Decorative Top Highlight Bar -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r {{ $theme['top_bar'] }} z-20"></div>

                    <div>
                        <!-- Program Visual Banner (Curved modern photo header) -->
                        <div class="relative h-48 w-full bg-slate-900 overflow-hidden flex items-center justify-center">
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $program->name }}" 
                                 class="w-full h-full object-cover object-center group-hover:scale-108 transition-transform duration-700 ease-out">
                            <!-- Gradient overlay for cinematic depth -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/15 to-transparent pointer-events-none"></div>

                            <!-- Program Code Badge Pill -->
                            <div class="absolute top-4 left-4 z-10">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-md bg-black/45 text-white border border-white/25 shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    {{ $code ?: 'PROGRAM' }}
                                </span>
                            </div>

                            <!-- Subtle Pill Indicator in Banner Bottom -->
                            <div class="absolute bottom-3 left-4 z-10">
                                <span class="text-[11px] font-semibold text-white/90 backdrop-blur-sm bg-black/30 px-2.5 py-0.5 rounded-md border border-white/10">
                                    {{ $theme['sub'] ?? 'Academy Program' }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Content Body -->
                        <div class="p-6 pt-5">
                            <!-- Title (Clean without category sub-label) -->
                            <div class="mb-3 text-left">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug group-hover:text-green-600 transition-colors">
                                    {{ $program->name }}
                                </h3>
                            </div>

                            <!-- Description Snippet -->
                            <p class="text-gray-600 dark:text-gray-300 text-xs leading-relaxed text-justify line-clamp-3 mb-2">
                                {{ $program->description }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer Action Button -->
                    <div class="px-6 pb-6 pt-0 w-full">
                        <div class="flex items-center justify-between text-xs font-bold py-2.5 px-4 rounded-xl border transition-all duration-300 {{ $theme['btn'] }}">
                            <span>Lihat Detail Program</span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Alpine Modal -->
        <div x-show="modalOpen" 
             style="display: none;"
             x-transition:enter="ease-out duration-100" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-75" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" 
                     @click="modalOpen = false"
                     aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.15)] transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-10" @click.stop>
                    
                    <!-- Professional Header with Subtle Pattern -->
                    <div class="bg-[#0f4d4a] px-6 py-6 border-b border-[#0f4d4a] relative overflow-hidden">
                        <!-- Subtle Geometric Pattern -->
                        <div class="absolute right-0 top-0 opacity-10">
                            <svg width="200" height="200" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="80" cy="20" r="40" fill="white"/>
                                <rect x="30" y="60" width="50" height="50" transform="rotate(-45 30 60)" fill="white"/>
                            </svg>
                        </div>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                    <span class="text-teal-200 text-xs font-bold tracking-widest uppercase" x-text="selectedProgram?.tag || 'Detail Program'"></span>
                                    <span class="text-teal-300/60 text-xs font-mono" x-show="selectedProgram?.code" x-text="'(' + selectedProgram?.code + ')'"></span>
                                </div>
                                <h3 class="text-2xl font-bold text-white leading-tight" id="modal-title" x-text="selectedProgram?.name"></h3>
                            </div>
                            <button @click="modalOpen = false" type="button" class="text-teal-200 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition-colors focus:outline-none ml-4">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 md:p-8 bg-white">
                        <div class="flex flex-col md:flex-row gap-8">
                            
                            <!-- Left Column (Details) -->
                            <div class="w-full md:w-3/5 space-y-6">
                                <!-- Description Box -->
                                <div class="bg-[#f0f7f6] rounded-xl p-5 border-l-4 border-[#147a73]">
                                    <h4 class="text-[#0f4d4a] font-bold text-sm mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Gambaran Umum
                                    </h4>
                                    <p class="text-gray-700 text-sm leading-relaxed text-justify" x-text="selectedProgram?.description"></p>
                                </div>

                                <!-- Qualifications -->
                                <div>
                                    <h4 class="text-[#0f4d4a] font-bold text-sm mb-4 border-b border-gray-100 pb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Persyaratan & Kualifikasi
                                    </h4>
                                    <div class="text-gray-700 text-sm [&_ol]:list-decimal [&_ul]:list-disc [&_ol]:pl-5 [&_ul]:pl-5 [&_li]:mb-1.5 [&_li]:pl-1 space-y-2" x-html="selectedProgram?.qualifications"></div>
                                </div>
                            </div>

                            <!-- Right Column (Action / QR) -->
                            <div class="w-full md:w-2/5">
                                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-[0_4px_20px_rgba(0,0,0,0.03)] h-full flex flex-col items-center justify-center relative overflow-hidden group hover:border-[#147a73] transition-colors duration-300">
                                    
                                    <h4 class="font-bold text-[#0f4d4a] mb-2 text-center text-lg">Pendaftaran</h4>
                                    <p class="text-xs text-gray-500 mb-6 text-center">Scan QR Code ini dengan kamera HP Anda</p>
                                    
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 group-hover:shadow-md transition-shadow duration-300">
                                        <template x-if="selectedProgram">
                                            <img :src="'/career-qr?url=' + encodeURIComponent(selectedProgram.gform_url)" alt="QR Code" class="w-36 h-36 object-contain">
                                        </template>
                                    </div>
                                    
                                    <div class="w-full border-t border-gray-100 relative mb-6">
                                        <span class="absolute left-1/2 -top-2.5 -translate-x-1/2 bg-white px-3 text-[10px] font-bold text-gray-400 tracking-widest">ATAU KLIK</span>
                                    </div>

                                    <a :href="selectedProgram?.gform_url" target="_blank" class="w-full flex justify-center items-center rounded-lg px-4 py-3 bg-[#509c38] hover:bg-[#438a2e] text-sm font-bold text-white transition-all duration-200 hover:-translate-y-0.5 shadow-sm hover:shadow-md">
                                        Buka Formulir
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </div>
                            
                        </div>
                    </div>
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
