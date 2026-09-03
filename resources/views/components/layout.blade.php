<!DOCTYPE html>
<html lang="id" class="scroll-smooth" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'FR Academy' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/faviconfr.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 antialiased flex flex-col min-h-screen transition-colors duration-300">
    @php
        $active_event = \App\Models\HomepageEvent::where('is_active', true)->first();
    @endphp

    <!-- Event Overlay -->
    @if(isset($active_event) && $active_event && $active_event->image_path)
        <div class="fixed inset-0 z-[100] pointer-events-none flex items-center justify-center">
            <!-- Asumsi gambar frame beresolusi tinggi dengan tengah bolong -->
            <img src="{{ asset($active_event->image_path) }}" alt="{{ $active_event->name }}" class="w-full h-full object-cover">
        </div>
    @endif
    
    <!-- Navigation -->
    <header x-data="{ scrolled: false, mobileMenuOpen: false }" 
            @scroll.window="scrolled = (window.pageYOffset > 20)"
            :class="scrolled ? 'bg-teal-primary shadow-lg' : 'bg-teal-primary lg:bg-transparent'" 
            class="fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="flex items-center gap-3">
                        <img src="{{ asset('images/logofr.png') }}" alt="FR Academy Logo" class="h-10 w-auto rounded">
                        <span class="text-white font-bold text-xl tracking-tight hidden sm:block">FR ACADEMY</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="text-white hover:text-green-accent px-3 py-2 text-sm font-medium transition">BERANDA</a>
                    <a href="/tentang-kami" class="text-white hover:text-green-accent px-3 py-2 text-sm font-medium transition">TENTANG</a>
                    <a href="#" class="text-white hover:text-green-accent px-3 py-2 text-sm font-medium transition">PROGRAM</a>
                    <a href="/artikel" class="text-white hover:text-green-accent px-3 py-2 text-sm font-medium transition">ARTIKEL</a>
                </nav>

                <!-- Right Actions -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode" class="text-white hover:text-green-accent p-2 transition rounded-full focus:outline-none">
                        <!-- Sun Icon (shows when dark mode is active) -->
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon Icon (shows when light mode is active) -->
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <!-- Search Icon -->
                    <button class="text-white hover:text-green-accent p-2 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    @auth
                        <a href="/admin" class="bg-teal-primary hover:bg-teal-700 text-white px-5 py-2 rounded-md text-sm font-medium transition duration-300 shadow">
                            Dashboard
                        </a>
                    @else
                        <a href="/login" class="bg-green-accent hover:bg-green-600 text-white px-5 py-2 rounded-md text-sm font-medium transition duration-300 shadow">
                            Masuk Portal
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden space-x-2">
                    <button @click="darkMode = !darkMode" class="text-white hover:text-green-accent p-2 transition rounded-full focus:outline-none">
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-white hover:text-green-accent focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden bg-teal-primary border-t border-teal-700 shadow-xl" 
             style="display: none;">
            <div class="px-4 pt-2 pb-6 space-y-1">
                <a href="/" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">BERANDA</a>
                <a href="/tentang-kami" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">TENTANG</a>
                <a href="#" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">PROGRAM</a>
                <a href="/artikel" class="block px-3 py-3 rounded-md text-base font-medium text-white hover:bg-teal-700 hover:text-green-accent transition">ARTIKEL</a>
                <div class="mt-4 pt-4 border-t border-teal-700">
                    @auth
                        <a href="/admin" class="block text-center w-full bg-green-accent hover:bg-green-600 text-white px-5 py-3 rounded-md text-base font-bold transition">Dashboard Admin</a>
                    @else
                        <a href="/login" class="block text-center w-full bg-green-accent hover:bg-green-600 text-white px-5 py-3 rounded-md text-base font-bold transition">Masuk Portal</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-teal-primary text-white pt-16 pb-8 border-t-[16px] border-green-accent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <!-- Brand Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-start mb-6 gap-4">
                        <img src="{{ asset('images/logofr.png') }}" alt="First Resources" class="h-16 object-contain bg-white p-1.5 rounded">
                        <p class="text-sm text-gray-300 leading-relaxed">
                            Didorong oleh semangat. Berakar pada tujuan.<br>Fokus pada hasil untuk kemajuan industri perkebunan kelapa sawit.
                        </p>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium text-lg">yogie.hizkia@first-resources.co.id</p>
                        <p class="font-bold text-green-accent text-xl">+62 852 7855 4025</p>
                    </div>
                    <!-- Social Icons -->
                    <div class="flex space-x-3 mt-6">
                        <a href="#" class="w-8 h-8 rounded-full border border-gray-500 flex items-center justify-center hover:bg-green-accent hover:border-transparent transition"><span class="sr-only">Facebook</span>f</a>
                        <a href="#" class="w-8 h-8 rounded-full border border-gray-500 flex items-center justify-center hover:bg-green-accent hover:border-transparent transition"><span class="sr-only">Twitter</span>t</a>
                        <a href="#" class="w-8 h-8 rounded-full border border-gray-500 flex items-center justify-center hover:bg-green-accent hover:border-transparent transition"><span class="sr-only">Instagram</span>i</a>
                        <a href="#" class="w-8 h-8 rounded-full border border-gray-500 flex items-center justify-center hover:bg-green-accent hover:border-transparent transition"><span class="sr-only">LinkedIn</span>in</a>
                    </div>
                </div>

                <!-- Useful Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Pintasan Portal</h3>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li><a href="/" class="hover:text-green-accent transition">Beranda Utama</a></li>
                        <li><a href="/artikel" class="hover:text-green-accent transition">Semua Artikel & Berita</a></li>
                        <li><a href="/login" class="hover:text-green-accent transition">Login Admin / Staf</a></li>
                        <li><a href="/tv-display" class="hover:text-green-accent transition">Mode Layar TV (TV Display)</a></li>
                    </ul>
                </div>

                <!-- Location -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Lokasi Kami</h3>
                    <a href="https://maps.google.com/maps?q=First+Resources+Learning+Centre" target="_blank" rel="noopener noreferrer" class="block relative w-full h-40 bg-teal-900 rounded-lg overflow-hidden border border-teal-800 shadow-inner group">
                        <!-- Peta Google Asli -->
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15958.629399435649!2d101.41169300600109!3d0.4705599827050588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5a86a6058e579%3A0xc3f1a26c4f03cb71!2sFirst%20Resources%20Learning%20Centre!5e0!3m2!1sen!2sid!4v1709618143491!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="pointer-events-none w-full h-full"></iframe>
                        <!-- Overlay Link -->
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <span class="bg-green-accent text-white text-sm font-bold px-4 py-2 rounded-lg shadow-lg flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Buka di Maps
                            </span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-12 pt-8 border-t border-teal-800 flex justify-center items-center text-sm text-gray-400">
                <p>&copy; 2026 First Resources Academy. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Scroll to Top Button -->
    <div x-data="{ show: false }" 
         @scroll.window="show = window.pageYOffset > 300"
         class="fixed bottom-8 right-8 z-50">
        <button x-show="show" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                class="w-12 h-12 bg-green-accent rounded-full flex items-center justify-center text-white hover:bg-green-600 hover:shadow-xl transition-all shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                style="display: none;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
        </button>
    </div>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });
    </script>
</body>
</html>
