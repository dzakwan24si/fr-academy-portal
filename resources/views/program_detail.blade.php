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
    <section class="py-12 lg:py-16 bg-white dark:bg-gray-950 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @php
                $hasPosterDetails = (!empty($program['curriculum_pillars']) && count($program['curriculum_pillars']) > 0) || 
                                    (!empty($program['objectives']) && count($program['objectives']) > 0);
            @endphp

            @if($hasPosterDetails)
                <!-- ================================================================= -->
                <!-- PROGRAM REGULER: STRICTLY FROM OFFICIAL POSTER                   -->
                <!-- ================================================================= -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    
                    <!-- Left Column: Deskripsi, Kurikulum, Benefit (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6" data-aos="fade-up">
                        
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
                        <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                                <h3 class="text-base sm:text-lg font-bold text-teal-primary dark:text-green-accent">
                                    KURIKULUM
                                </h3>
                            </div>
                            <ol class="space-y-2 text-sm sm:text-base text-gray-800 dark:text-gray-200 pl-1">
                                @foreach($program['curriculum_pillars'] ?? [] as $cIndex => $pillar)
                                    <li class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700">
                                        <span class="w-6 h-6 rounded-full bg-teal-primary text-white text-xs font-bold flex items-center justify-center shrink-0">
                                            {{ $cIndex + 1 }}
                                        </span>
                                        <span class="font-medium">{{ $pillar }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <!-- 3. Benefit -->
                        <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-2 h-2 rounded-full bg-green-accent"></span>
                                <h3 class="text-base sm:text-lg font-bold text-teal-primary dark:text-green-accent">
                                    BENEFIT
                                </h3>
                            </div>
                            <ul class="space-y-2.5 text-sm sm:text-base text-gray-800 dark:text-gray-200 pl-1">
                                @foreach($program['objectives'] ?? [] as $oIdx => $objective)
                                    <li class="flex items-start gap-3 p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700">
                                        <span class="w-6 h-6 rounded-full bg-green-accent/20 text-teal-primary dark:text-green-accent text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                                            {{ $oIdx + 1 }}
                                        </span>
                                        <span class="leading-relaxed font-normal">{{ $objective }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>

                    <!-- Right Column: Poster Image & Metode Belajar (5 Cols) -->
                    <div class="lg:col-span-5 space-y-6" data-aos="fade-up" data-aos-delay="100">
                        <!-- Poster Image -->
                        <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-md bg-gray-50 dark:bg-gray-900 p-2">
                            <img src="{{ asset($program['image']) }}" 
                                 alt="Poster Resmi {{ $program['title'] }}" 
                                 class="w-full h-auto object-contain rounded-xl shadow-inner">
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
                <!-- PROGRAM NON-REGULER: HANYA JUDUL, GAMBAR, DAN DESKRIPSI           -->
                <!-- ================================================================= -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    
                    <!-- Left Column: Judul & Deskripsi Program (7 Cols) -->
                    <div class="lg:col-span-7" data-aos="fade-up">
                        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm">
                            <span class="text-xs font-bold tracking-widest uppercase text-green-accent block mb-2">
                                DESKRIPSI PROGRAM
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-teal-primary dark:text-white tracking-tight mb-4">
                                {{ $program['title'] }}
                            </h2>
                            <p class="text-base sm:text-lg text-gray-700 dark:text-gray-300 leading-relaxed font-light">
                                {{ $program['overview'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Gambar Program (5 Cols) -->
                    <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="100">
                        <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-md bg-gray-50 dark:bg-gray-900 p-2">
                            <img src="{{ asset($program['image'] ?? $program['hero_image']) }}" 
                                 alt="{{ $program['title'] }}" 
                                 class="w-full h-auto object-cover rounded-xl shadow-inner">
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </section>

</x-layout>
