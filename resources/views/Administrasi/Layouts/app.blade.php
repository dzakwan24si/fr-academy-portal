<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('Logo First Resource.png') }}">
    <title>@yield('title', 'FR Academy Enterprise Portal')</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f4f7f1',
                            100: '#e5ecd9',
                            200: '#cbdba8',
                            300: '#aac577',
                            400: '#87aa4e',
                            500: '#509c38', // Brand forest green
                            600: '#3d562a',
                            700: '#2f4321',
                            800: '#222f17',
                            900: '#141c0e',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        /* Sticky first columns helper */
        .sticky-col-1 {
            position: sticky;
            left: 0;
            z-index: 10;
        }
        .sticky-col-2 {
            position: sticky;
            left: 130px;
            z-index: 10;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8F9FA] text-[#1A1A1A] min-h-screen flex">

    <!-- 1. SIDEBAR (Dynamic based on Role / Session / Query) -->
    @if(session('user_role') === 'top_management' || request()->query('role') === 'top_management' || request()->is('Top_Management*') || (auth()->check() && auth()->user()?->role === 'top_management'))
        @include('Administrasi.Layouts.sidebar_top_management')
    @else
        @include('Administrasi.Layouts.sidebar')
    @endif

    <!-- 2. MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOP NAVBAR (Included from Layouts/header.blade.php) -->
        @include('Administrasi.Layouts.header')

        <!-- MAIN BODY -->
        <main class="flex-1 overflow-y-auto p-8 space-y-6">
            @yield('content')
        </main>
    </div>

    <!-- Script initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
