<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'FR Academy Portal' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased flex flex-col min-h-screen">
    
    <!-- Navigation -->
    <header x-data="{ scrolled: false }" 
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
                    <a href="#" class="text-white hover:text-green-accent px-3 py-2 text-sm font-medium transition">TENTANG</a>
                    <a href="#" class="text-white hover:text-green-accent px-3 py-2 text-sm font-medium transition">PROGRAM</a>
                    <a href="/artikel" class="text-white hover:text-green-accent px-3 py-2 text-sm font-medium transition">ARTIKEL</a>
                </nav>

                <!-- Right Actions -->
                <div class="hidden md:flex items-center space-x-4">
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
                <div class="flex items-center md:hidden">
                    <button type="button" class="text-white hover:text-green-accent focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
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
                    <h2 class="text-3xl font-bold mb-4">FirstResources</h2>
                    <p class="text-sm text-gray-300 mb-6">
                        Didorong oleh semangat, Berakar pada tujuan. Fokus pada hasil untuk kemajuan industri perkebunan kelapa sawit.
                    </p>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium text-lg">academy@first-resources.com</p>
                        <p class="font-bold text-green-accent text-xl">+62 800 123 46 57</p>
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
                    <h3 class="text-lg font-semibold mb-6">Tautan Berguna</h3>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li><a href="#" class="hover:text-green-accent transition">Perusahaan</a></li>
                        <li><a href="#" class="hover:text-green-accent transition">Layanan</a></li>
                        <li><a href="#" class="hover:text-green-accent transition">Fitur</a></li>
                        <li><a href="#" class="hover:text-green-accent transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-green-accent transition">Mulai</a></li>
                        <li><a href="/tv-display" class="hover:text-green-accent transition">Mode Layar TV (TV Display)</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Berlangganan</h3>
                    <p class="text-sm text-gray-300 mb-4">Dapatkan berita & pembaruan terbaru.</p>
                    <form class="flex border-b border-gray-500 pb-2">
                        <input type="email" placeholder="Alamat Email" class="bg-transparent w-full focus:outline-none text-sm text-white placeholder-gray-400">
                        <button type="submit" class="text-green-accent hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 mt-4">Kami tidak akan pernah membagikan atau menjual email Anda kepada siapa pun.</p>
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
</body>
</html>
