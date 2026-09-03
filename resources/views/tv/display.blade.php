<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Academy - TV Display</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        /* Hide scrollbar for TV Display */
        body { overflow: hidden; background-color: #114045; }
        
        [x-cloak] { display: none !important; }
        
        .fade-enter { opacity: 0; }
        .fade-enter-active { opacity: 1; transition: opacity 1s ease-in-out; }
        .fade-leave { opacity: 1; }
        .fade-leave-active { opacity: 0; transition: opacity 1s ease-in-out; }
        
        .progress-bar {
            transition: width linear;
        }
    </style>
</head>
<body class="text-white h-screen w-screen flex flex-col font-sans">
    
    @if(count($playlists) > 0)
        <!-- Alpine Component for Carousel -->
        <div x-data="tvDisplay()" x-init="init()" class="relative flex-1 flex flex-col h-full w-full">
            
            <!-- Progress Bar at Top -->
            <div class="h-2 w-full bg-[#0a2629] fixed top-0 left-0 z-50">
                <div class="h-full bg-[#59A338] progress-bar" :style="`width: ${progress}%; transition-duration: ${tickInterval}ms`"></div>
            </div>

            <!-- Slides Container -->
            <div class="relative flex-1 overflow-hidden">
                <template x-for="(item, index) in playlists" :key="item.id">
                    <div x-show="currentIndex === index" 
                         x-transition:enter="transition ease-out duration-1000"
                         x-transition:enter-start="opacity-0 scale-105"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-1000 absolute inset-0"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0 w-full h-full flex flex-col">
                         
                        <!-- Split Screen Layout -->
                        <div class="flex h-full w-full">
                            <!-- Left: Image -->
                            <div class="w-1/2 h-full relative bg-gray-900 border-r-8 border-[#59A338]">
                                <template x-if="item.article.featured_image">
                                    <img :src="'/storage/' + item.article.featured_image" class="absolute inset-0 w-full h-full object-cover">
                                </template>
                                <template x-if="!item.article.featured_image">
                                    <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-teal-primary">
                                        <img src="{{ asset('images/logofr.png') }}" class="w-48 opacity-20">
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Right: Content -->
                            <div class="w-1/2 h-full flex flex-col justify-between py-16 px-20 bg-[#114045] relative">
                                <!-- Watermark Logo -->
                                <img src="{{ asset('images/logofr.png') }}" class="absolute top-12 right-12 w-32 opacity-20">
                                
                                <div class="relative z-10 flex flex-col h-full">
                                    <!-- Header -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-block px-4 py-1.5 rounded-full bg-[#59A338] text-white font-bold tracking-wider uppercase text-sm mb-6" x-text="item.article.category?.name || 'Informasi'"></span>
                                        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-8 text-white line-clamp-3" x-text="item.article.title"></h1>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 overflow-hidden relative mb-8">
                                        <div class="prose prose-invert prose-lg text-gray-300 max-w-none" x-html="item.article.content"></div>
                                        <!-- Gradient Fade Out -->
                                        <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-t from-[#114045] to-transparent pointer-events-none"></div>
                                    </div>
                                    
                                    <!-- Footer (Author & QR) -->
                                    <div class="flex-shrink-0 flex items-center justify-between mt-auto">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-[#59A338]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-400 font-medium uppercase tracking-wide">Penulis</p>
                                                <p class="text-xl font-bold text-white" x-text="item.article.author?.name || 'FR Academy'"></p>
                                            </div>
                                        </div>
                                        
                                        <!-- QR Code -->
                                        <div class="flex items-center bg-white/10 p-3 rounded-2xl backdrop-blur-sm border border-white/20">
                                            <div class="bg-white p-2 rounded-xl flex-shrink-0" x-html="item.qr_code"></div>
                                            <div class="ml-4 mr-2">
                                                <p class="text-sm text-white font-bold mb-1">Scan untuk Membaca</p>
                                                <p class="text-xs text-teal-200">Buka selengkapnya di HP Anda</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Bottom Ticker / Footer -->
            <div class="h-24 bg-white flex items-center px-12 z-20 shadow-2xl">
                <img src="{{ asset('images/logofr.png') }}" class="h-12 border-r-2 border-gray-200 pr-8 mr-8">
                
                <!-- Running Text -->
                <div class="flex-1 overflow-hidden relative h-full flex items-center">
                    <div class="whitespace-nowrap animate-[marquee_20s_linear_infinite] text-2xl font-bold text-teal-primary">
                        <span class="mx-4">SELAMAT DATANG DI FR ACADEMY</span>
                        <span class="mx-4 text-green-accent">•</span>
                        <span class="mx-4">PUSAT INFORMASI, EDUKASI, DAN OPERASIONAL TERPADU FIRST RESOURCES</span>
                        <span class="mx-4 text-green-accent">•</span>
                        <span class="mx-4" x-text="currentDate"></span>
                    </div>
                </div>
                
                <div class="pl-8 border-l-2 border-gray-200 ml-8 text-right flex-shrink-0">
                    <p class="text-4xl font-extrabold text-[#59A338]" x-text="currentTime"></p>
                </div>
            </div>
            
            <style>
                @keyframes marquee {
                    0% { transform: translateX(100%); }
                    100% { transform: translateX(-100%); }
                }
            </style>
        </div>
    @else
        <!-- Empty State -->
        <div x-data="tvDisplay()" x-init="init()" class="flex-1 flex flex-col items-center justify-center bg-[#114045]">
            <img src="{{ asset('images/logofr.png') }}" class="h-24 mb-8 opacity-80">
            <h1 class="text-4xl font-bold text-white mb-4">FR Academy TV Display</h1>
            <p class="text-xl text-teal-100">Belum ada playlist yang ditambahkan.</p>
            <p class="text-md text-teal-200 mt-2">Silakan login ke Admin Panel untuk mengatur daftar tayang.</p>
        </div>
    @endif
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tvDisplay', () => ({
                playlists: @json($playlists ?? []),
                currentIndex: 0,
                progress: 0,
                tickInterval: 100, // Update progress every 100ms
                timer: null,
                clockTimer: null,
                currentTime: '',
                currentDate: '',
                
                currentHash: '{{ $currentHash ?? "" }}',
                updatePending: false,
                
                init() {
                    this.startClock();
                    this.pollForUpdates();
                    
                    if(this.playlists.length === 0) return;
                    this.startSlide();
                },
                
                startSlide() {
                    this.progress = 0;
                    let durationMs = this.playlists[this.currentIndex].duration_seconds * 1000;
                    let elapsed = 0;
                    
                    clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        elapsed += this.tickInterval;
                        this.progress = (elapsed / durationMs) * 100;
                        
                        if (elapsed >= durationMs) {
                            this.nextSlide();
                        }
                    }, this.tickInterval);
                },
                
                nextSlide() {
                    // Jika ada update tertunda, tunggu sampai slide terakhir selesai diputar, baru refresh
                    if (this.updatePending && this.currentIndex === this.playlists.length - 1) {
                        clearInterval(this.timer); // Hentikan timer agar tidak spam reload()
                        window.location.reload();
                        return;
                    }
                    
                    this.currentIndex = (this.currentIndex + 1) % this.playlists.length;
                    this.startSlide();
                },
                
                startClock() {
                    const updateClock = () => {
                        const now = new Date();
                        this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    };
                    updateClock();
                    this.clockTimer = setInterval(updateClock, 1000);
                },
                
                pollForUpdates() {
                    // Cek update setiap 15 detik menggunakan API
                    setInterval(async () => {
                        try {
                            const response = await fetch('{{ route("tv.check") }}?t=' + new Date().getTime());
                            const data = await response.json();
                            if (data.hash && data.hash !== this.currentHash) {
                                console.log("Ada update! Menunggu siklus saat ini selesai untuk refresh...");
                                this.updatePending = true;
                                
                                // Jika layar dalam keadaan kosong, langsung refresh saja tanpa menunggu
                                if (this.playlists.length === 0) {
                                    window.location.reload();
                                }
                            }
                        } catch (e) {
                            console.error("Gagal mengecek update:", e);
                        }
                    }, 15000);
                }
            }));
        });
    </script>

</body>
</html>
