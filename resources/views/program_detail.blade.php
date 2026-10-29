<x-layout>
    <x-slot name="title">{{ $program['title'] }} - FR Academy</x-slot>

    <!-- ===================================================================== -->
    <!-- HERO SECTION: Top Dark Teal Banner                                    -->
    <!-- ===================================================================== -->
    <section class="relative overflow-hidden flex flex-col justify-center text-white" style="background-color: #114045; min-height: 280px; padding-top: 110px; padding-bottom: 40px;">
        <!-- Background Image with genuine green shadow overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset($program['hero_image']) }}" alt="{{ $program['title'] }}" class="w-full h-full object-cover object-center scale-105" data-aos="zoom-out" data-aos-duration="2500">
            <!-- Dark Teal Overlay #114045 -->
            <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(17, 64, 69, 0.88) 0%, rgba(17, 64, 69, 0.70) 50%, rgba(11, 44, 48, 0.95) 100%);"></div>
            <div class="absolute inset-0" style="box-shadow: inset 0 0 140px rgba(17, 64, 69, 0.95);"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-4xl" data-aos="fade-up">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.2] mb-3">
                    {{ $program['title'] }}
                </h1>
                
                <p class="text-base sm:text-lg text-teal-100/90 font-light leading-relaxed max-w-3xl">
                    {{ $program['subtitle'] }}
                </p>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- CONTENT SECTION                                                       -->
    <!-- ===================================================================== -->
    <section class="py-12 lg:py-16 bg-white dark:bg-gray-950 transition-colors duration-300" x-data="{ zoomModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @php
                $isReguler = !empty($program['is_reguler']) || in_array($slug, ['fat', 'mat', 'tat', 'aat', 'dmt', 'pmt', 'management-associate']);
            @endphp

            @if($isReguler)
                <!-- ================================================================= -->
                <!-- PROGRAM REGULER: STRICTLY FROM OFFICIAL POSTER                   -->
                <!-- ================================================================= -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                    
                    <!-- Left Column: Deskripsi, Kurikulum, Objective (6 Cols) -->
                    <div class="lg:col-span-6 space-y-6" data-aos="fade-up">
                        
                        <!-- 1. Deskripsi Program -->
                        <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                            <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-2">
                                DESKRIPSI PROGRAM
                            </span>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-teal-primary dark:text-white tracking-tight mb-3">
                                {{ $program['title'] }}
                            </h2>
                            <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed font-light">
                                {{ $program['overview'] }}
                            </p>
                        </div>

                        <!-- 2. Kurikulum -->
                        @if(!empty($program['curriculum_pillars']) && count($program['curriculum_pillars']) > 0)
                            <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                                    <h3 class="text-base sm:text-lg font-bold text-teal-primary dark:text-green-accent">
                                        KURIKULUM
                                    </h3>
                                </div>
                                <ol class="space-y-2 text-sm sm:text-base text-gray-800 dark:text-gray-200 pl-1">
                                    @foreach($program['curriculum_pillars'] as $cIndex => $pillar)
                                        <li class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700">
                                            <span class="w-6 h-6 rounded-full bg-teal-primary text-white text-xs font-bold flex items-center justify-center shrink-0">
                                                {{ $cIndex + 1 }}
                                            </span>
                                            <span class="font-medium">{{ $pillar }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif

                        <!-- 3. Objective -->
                        @if(!empty($program['objectives']) && count($program['objectives']) > 0)
                            <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                                    <h3 class="text-base sm:text-lg font-bold text-teal-primary dark:text-green-accent">
                                        OBJECTIVE
                                    </h3>
                                </div>
                                <ul class="space-y-2.5 text-sm sm:text-base text-gray-800 dark:text-gray-200 pl-1">
                                    @foreach($program['objectives'] as $oIdx => $objective)
                                        <li class="flex items-start gap-3 p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700">
                                            <span class="w-6 h-6 rounded-full bg-green-accent/20 text-teal-primary dark:text-green-accent text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                                                {{ $oIdx + 1 }}
                                            </span>
                                            <span class="leading-relaxed font-normal">{{ $objective }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>

                    <!-- Right Column: Poster Image & Metode Belajar (6 Cols) -->
                    <div class="lg:col-span-6 space-y-6 lg:sticky lg:top-24" data-aos="fade-up" data-aos-delay="100">
                        <!-- Poster Image (Enlarged Card & Click to Zoom) -->
                        <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-xl bg-white dark:bg-gray-900 p-3 sm:p-4">
                            <div class="relative group cursor-pointer rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-800 flex items-center justify-center" @click="zoomModal = true">
                                <img src="{{ asset($program['image']) }}" 
                                     alt="Foto Program {{ $program['title'] }}" 
                                     class="w-full h-auto min-h-[480px] sm:min-h-[560px] lg:min-h-[640px] max-h-[820px] object-contain rounded-xl shadow-inner transition duration-500 group-hover:scale-[1.02]">
                                <div class="absolute top-3 right-3 bg-black/60 hover:bg-black/80 text-white px-3 py-1.5 rounded-xl text-xs font-semibold backdrop-blur-sm transition flex items-center gap-1.5 shadow-md">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                    Perbesar
                                </div>
                            </div>
                        </div>

                        <!-- 4. Metode Belajar (di bawah poster) -->
                        <div class="p-6 sm:p-7 rounded-2xl bg-teal-50/70 dark:bg-gray-900 border border-teal-100 dark:border-gray-800 shadow-sm">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                                <h3 class="text-base sm:text-lg font-bold text-teal-primary dark:text-green-accent">
                                    METODE BELAJAR
                                </h3>
                            </div>
                            <p class="text-xl sm:text-2xl font-extrabold text-teal-primary dark:text-white tracking-wide mb-1">
                                {{ $program['learning_method']['ratio'] ?? '70 : 20 : 10' }}
                            </p>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 font-light">
                                ({{ $program['learning_method']['desc'] ?? '70% experiential learning, 20% social learning, dan 10% formal learning' }})
                            </p>
                        </div>
                    </div>

                </div>
            @else
                <!-- ================================================================= -->
                <!-- PROGRAM NON-REGULER: DESKRIPSI, KURIKULUM, BENEFIT & GAMBAR       -->
                <!-- ================================================================= -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                    
                    <!-- Left Column: Deskripsi, Kurikulum, Benefit (6 Cols) -->
                    <div class="lg:col-span-6 space-y-6" data-aos="fade-up">
                        
                        <!-- 1. Deskripsi Program (Overview) -->
                        <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                            <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-[#114045] dark:bg-teal-900 text-white text-xs font-bold tracking-wide uppercase mb-4 shadow-sm">
                                Overview
                            </div>
                            <h2 class="text-xl sm:text-2xl font-extrabold text-teal-primary dark:text-white tracking-tight mb-3">
                                {{ $program['title'] }}
                            </h2>
                            <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed font-normal">
                                {{ $program['overview'] }}
                            </p>
                        </div>

                        <!-- 2. Kurikulum -->
                        @if(!empty($program['curriculum_pillars']) && count($program['curriculum_pillars']) > 0)
                            <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                                <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-[#114045] dark:bg-teal-900 text-white text-xs font-bold tracking-wide uppercase mb-4 shadow-sm">
                                    Kurikulum
                                </div>
                                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-800 dark:text-gray-200">
                                    @foreach($program['curriculum_pillars'] as $pillar)
                                        <li class="flex items-start gap-2.5 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/80">
                                            <span class="w-2 h-2 rounded-full bg-[#114045] dark:bg-green-accent mt-2 shrink-0"></span>
                                            <span class="leading-relaxed font-normal">{{ $pillar }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- 3. Benefit -->
                        @if((!empty($program['objectives']) && count($program['objectives']) > 0) || (!empty($program['benefits']) && count($program['benefits']) > 0))
                            <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                                <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-[#114045] dark:bg-teal-900 text-white text-xs font-bold tracking-wide uppercase mb-4 shadow-sm">
                                    Benefit
                                </div>
                                <ul class="space-y-2.5 text-sm sm:text-base text-gray-800 dark:text-gray-200">
                                    @foreach($program['objectives'] ?? $program['benefits'] ?? [] as $benefit)
                                        <li class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/80">
                                            <span class="w-2 h-2 rounded-full bg-green-accent mt-2 shrink-0"></span>
                                            <span class="leading-relaxed font-normal">{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>

                    <!-- Right Column: Gambar Program Saja (Diperbesar 6 Cols & Tampilan Eksklusif) -->
                    <div class="lg:col-span-6 space-y-6 lg:sticky lg:top-24" data-aos="fade-up" data-aos-delay="100">
                        <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-xl bg-white dark:bg-gray-900 p-3 sm:p-4">
                            <div class="relative group cursor-pointer rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center" @click="zoomModal = true">
                                <img src="{{ asset($program['image'] ?? $program['hero_image']) }}" 
                                     alt="{{ $program['title'] }}" 
                                     class="w-full h-auto min-h-[380px] sm:min-h-[460px] lg:min-h-[540px] xl:min-h-[580px] max-h-[660px] object-cover rounded-xl shadow-inner border border-gray-100 dark:border-gray-800 transition duration-500 group-hover:scale-[1.02]">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-between p-4">
                                    <span class="text-white text-xs font-semibold flex items-center gap-1.5 bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-lg shadow-sm">
                                        <svg class="w-3.5 h-3.5 text-green-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                        Klik untuk Perbesar
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif

        </div>

        <!-- Lightbox Modal untuk Zoom Gambar Layar Penuh -->
        <div x-show="zoomModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" 
             style="display: none;"
             @keydown.escape.window="zoomModal = false">
            <div @click.away="zoomModal = false" class="relative max-w-5xl w-full flex flex-col items-center">
                <button @click="zoomModal = false" 
                        type="button" 
                        class="absolute -top-12 right-0 text-white/80 hover:text-white p-2 rounded-full bg-white/10 hover:bg-white/20 transition"
                        title="Tutup">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <img src="{{ asset($program['image'] ?? $program['hero_image']) }}" 
                     alt="{{ $program['title'] }}" 
                     class="max-w-full max-h-[85vh] w-auto h-auto object-contain rounded-2xl shadow-2xl border border-white/10 bg-black/40">
                <p class="text-white/90 text-sm mt-3 font-semibold">{{ $program['title'] }}</p>
            </div>
        </div>
    </section>

</x-layout>
