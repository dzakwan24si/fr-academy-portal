<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin FR Academy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Script Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-white min-h-screen font-sans text-gray-900">
    <div class="flex min-h-screen">
        
        <!-- Left Side - Image Background -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-teal-primary flex-col justify-end p-16 overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/welcomefr.jpg') }}" alt="FR Academy Campus" class="w-full h-full object-cover">
            </div>
            <!-- Dark Teal Overlay -->
            <div class="absolute inset-0 bg-teal-primary/80 mix-blend-multiply z-10"></div>
            <div class="absolute inset-0 bg-teal-900/40 z-10"></div>
            
            <!-- Text Content -->
            <div class="relative z-20 mb-8">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-4">FR ACADEMY</h1>
                <p class="text-lg text-teal-50 max-w-md leading-relaxed">
                    Pusat Informasi, Edukasi, dan Operasional Terpadu<br>
                    First Resources
                </p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 lg:p-24 relative">
            
            <div class="w-full max-w-md">
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-teal-primary mb-3">Selamat Datang</h2>
                    <p class="text-gray-500 text-sm">Silakan masuk menggunakan kredensial FR Academy Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded text-sm">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/login" class="space-y-6">
                    @csrf
                    
                    <!-- Identifier Input -->
                    <div>
                        <label for="identifier" class="block text-sm font-bold text-gray-700 mb-2">Email atau NIK Pegawai</label>
                        <div class="relative">
                            <input type="text" name="identifier" id="identifier" value="{{ old('identifier') }}" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-primary focus:border-teal-primary transition text-gray-700 pr-10" 
                                placeholder="Masukkan NIK atau Email">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-primary focus:border-teal-primary transition text-gray-700 pr-10" 
                                placeholder="Masukkan kata sandi">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Widget Cloudflare Turnstile -->
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAAEzPGJLBNyo86KAl"></div>

                    <!-- Options -->
                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-green-accent focus:ring-green-accent border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-600">Ingat Saya</label>
                        </div>
                        <a href="#" class="text-sm font-bold text-green-800 hover:text-green-600 transition">Lupa Kata Sandi?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-green-700 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-800 transition duration-300 flex justify-center items-center shadow-md">
                        Masuk ke Sistem 
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>

            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-8 text-center w-full max-w-md">
                <p class="text-xs text-gray-400">&copy; 2026 FR ACADEMY - Internal System Portal</p>
            </div>
            
        </div>
    </div>
</body>
</html>