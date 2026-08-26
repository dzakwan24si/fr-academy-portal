<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kalender Training | FR Academy Enterprise Portal</title>

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
                            500: '#4B6A34', // Brand forest green
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
    </style>
</head>
<body class="bg-[#F8F9FA] text-[#1A1A1A] min-h-screen flex">

    <!-- 1. SIDEBAR -->
    <aside class="w-64 bg-white border-r border-[#E5E7EB] shrink-0 hidden md:flex flex-col">
        <!-- Logo Header -->
        <div class="p-6 border-b border-[#E5E7EB] flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary-500">
                <i data-lucide="sprout" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg text-primary-900 leading-tight">FR Academy</h1>
                <p class="text-xs text-[#71717A]">Enterprise Portal</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            <!-- Active Kalender Training -->
            <a href="{{ route('kalender-training.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-[#4B6A34]/10 text-primary-500 transition-colors">
                <i data-lucide="calendar" class="w-5 h-5 text-primary-500"></i>
                <span class="font-semibold text-sm">Kalender Training</span>
            </a>
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-[#71717A] hover:bg-[#F4F4F5] hover:text-[#18181B] transition-colors">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="font-medium text-sm">Dashboard</span>
            </a>
        </nav>

        <!-- Sidebar Footer Info -->
        <div class="p-4 border-t border-[#E5E7EB] text-center text-xs text-[#A1A1AA]">
            v{{ app()->version() }} &copy; FR Academy 2026
        </div>
    </aside>

    <!-- 2. MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navigation Navbar -->
        <header class="h-16 bg-white border-b border-[#E5E7EB] flex items-center justify-between px-8 z-10 shrink-0">
            <!-- Search bar placeholder -->
            <div class="relative w-72">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" placeholder="Cari..." class="w-full bg-[#F4F4F5] rounded-full py-1.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 text-[#18181B]">
            </div>

            <!-- Profile and Settings Actions -->
            <div class="flex items-center gap-4">
                <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
                <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                </button>
                <button class="w-9 h-9 rounded-full hover:bg-[#F4F4F5] flex items-center justify-center text-[#71717A]">
                    <i data-lucide="help-circle" class="w-5 h-5"></i>
                </button>

                <div class="h-6 w-px bg-[#E5E7EB]"></div>

                <!-- Admin Profile Card -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-[#18181B] leading-none">Admin Name</p>
                        <p class="text-xs text-[#71717A]">Administrator</p>
                    </div>
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-[#E5E7EB]">
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-8 space-y-6">

            <!-- Welcome Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-primary-900 tracking-tight">Kalender Training</h2>
                    <p class="text-[#71717A] mt-1">Kelola master data program pelatihan.</p>
                </div>

                <!-- Statically Locked Year indicator -->
                <div class="bg-white border border-[#E5E7EB] rounded-full px-4 py-1.5 flex items-center gap-2 shadow-2xs">
                    <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
                    <span class="text-sm font-bold text-[#18181B]">Tahun Aktif: 2026</span>
                </div>
            </div>

            <!-- 3. SUB-CATEGORY SELECTIONS -->
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                @php
                    $subIcons = [
                        'Training Reguler - Staf' => 'graduation-cap',
                        'Training Reguler - Mandor' => 'users-round',
                        'Training Riau' => 'map-pin',
                        'Training Kalbar' => 'map-pin',
                        'Training Kaltim' => 'map-pin',
                        'Training Corporate' => 'building'
                    ];
                @endphp
                @foreach ($categories as $cat)
                    <a href="?category={{ urlencode($cat) }}"
                       class="bg-white border rounded-xl p-4 flex flex-col justify-between transition-all hover:shadow-md hover:border-primary-300 {{ $activeCategory === $cat ? 'border-primary-500 ring-2 ring-primary-100 shadow-sm' : 'border-[#E5E7EB]' }}">
                        <div class="flex items-center justify-between">
                            <div class="p-2 rounded-lg {{ $activeCategory === $cat ? 'bg-primary-100 text-primary-600' : 'bg-[#F4F4F5] text-[#71717A]' }}">
                                <i data-lucide="{{ $subIcons[$cat] ?? 'book-open' }}" class="w-5 h-5"></i>
                            </div>
                            @if ($activeCategory === $cat)
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                                </span>
                            @endif
                        </div>
                        <div class="mt-4">
                            <h3 class="font-bold text-sm tracking-tight {{ $activeCategory === $cat ? 'text-primary-900' : 'text-[#18181B]' }}">{{ $cat }}</h3>
                            <p class="text-xs text-[#71717A] mt-1">Kategori Program</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- 4. DASHBOARD METRICS FOR ACTIVE CATEGORY -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Total Programs -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 flex items-center gap-5 shadow-sm">
                    <div class="p-4 bg-emerald-50 text-emerald-600 rounded-full">
                        <i data-lucide="book-open" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Program</p>
                        <h4 class="text-2xl font-bold text-[#18181B] mt-1">{{ number_format($stats['total_programs']) }}</h4>
                        <p class="text-xs text-emerald-600 mt-0.5 font-medium flex items-center gap-1">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i> Kalender aktif (2026)
                        </p>
                    </div>
                </div>

                <!-- Card 2: Total Batches -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 flex items-center gap-5 shadow-sm">
                    <div class="p-4 bg-blue-50 text-blue-600 rounded-full">
                        <i data-lucide="layers" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Total Batch / Tahun</p>
                        <h4 class="text-2xl font-bold text-[#18181B] mt-1">{{ number_format($stats['total_batches']) }}</h4>
                        <p class="text-xs text-blue-600 mt-0.5 font-medium flex items-center gap-1">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Total rencana realisasi
                        </p>
                    </div>
                </div>

                <!-- Card 3: Total Target Participants -->
                <div class="bg-white border border-[#E5E7EB] rounded-xl p-6 flex items-center gap-5 shadow-sm">
                    <div class="p-4 bg-purple-50 text-purple-600 rounded-full">
                        <i data-lucide="users" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#71717A]">Target Peserta / Tahun</p>
                        <h4 class="text-2xl font-bold text-[#18181B] mt-1">{{ number_format($stats['total_participants']) }} <span class="text-sm font-normal text-[#71717A]">org</span></h4>
                        <p class="text-xs text-purple-600 mt-0.5 font-medium flex items-center gap-1">
                            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Jumlah kuota peserta
                        </p>
                    </div>
                </div>
            </div>

            <!-- 5. FILTER BAR AND MAIN DATA TABLE -->
            <div class="bg-white border border-[#E5E7EB] rounded-xl shadow-sm overflow-hidden">
                <!-- Filter Section -->
                <div class="p-6 border-b border-[#E5E7EB] flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <form action="" method="GET" class="w-full flex flex-col md:flex-row items-stretch md:items-center gap-4">
                        <!-- Retain active states -->
                        <input type="hidden" name="category" value="{{ $activeCategory }}">

                        <!-- Location Dropdown Filter -->
                        <div class="relative w-full md:w-56">
                            <select name="location" onchange="this.form.submit()" class="w-full appearance-none bg-white border border-[#E5E7EB] rounded-lg py-2 pl-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary-100 focus:border-primary-500 text-[#18181B] font-medium">
                                <option value="">Semua Lokasi</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc }}" {{ $selectedLocation === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-3 flex items-center text-[#71717A] pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </span>
                        </div>

                        <!-- Live Search Bar -->
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA]">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </span>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari program..." class="w-full bg-white border border-[#E5E7EB] rounded-lg py-2 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-primary-100 focus:border-primary-500 text-[#18181B]">
                            @if ($search !== '' || $selectedLocation !== '')
                                <a href="?category={{ urlencode($activeCategory) }}" class="absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-[#EF4444] hover:text-[#DC2626]">
                                    Reset Filter
                                </a>
                            @endif
                        </div>
                    </form>

                    <!-- Add Program button -->
                    <button class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-sm whitespace-nowrap transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Program
                    </button>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8F9FA] text-[#71717A] font-semibold text-xs border-b border-[#E5E7EB]">
                                <th class="py-4 px-6 uppercase tracking-wider">Nama Program</th>
                                <th class="py-4 px-6 uppercase tracking-wider">Jenis Program</th>
                                <th class="py-4 px-6 uppercase tracking-wider">Region</th>
                                <th class="py-4 px-6 uppercase tracking-wider text-center">Target Batch</th>
                                <th class="py-4 px-6 uppercase tracking-wider text-center">Target Peserta</th>
                                <th class="py-4 px-6 uppercase tracking-wider">Lokasi</th>
                                <th class="py-4 px-6 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E5E7EB] text-sm text-[#18181B]">
                            @forelse ($programs as $index => $prog)
                                <tr class="hover:bg-[#F8F9FA] transition-colors cursor-pointer" onclick="openScheduleModal({{ json_encode($prog) }})">
                                    <!-- Program Name -->
                                    <td class="py-4 px-6 font-semibold max-w-sm">
                                        <div class="truncate" title="{{ $prog['name'] }}">{{ $prog['name'] }}</div>
                                        @if (!empty($prog['section']) && $prog['section'] !== $prog['name'])
                                            <span class="text-xs font-normal text-[#71717A] mt-0.5 block truncate">{{ $prog['section'] }}</span>
                                        @endif
                                    </td>

                                    <!-- Program Category Badge -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @php
                                            $badgeClasses = 'bg-gray-100 text-gray-800';
                                            $sub = $prog['subsection'] ?? '';
                                            if (stripos($prog['category'], 'Reguler') !== false) {
                                                $badgeClasses = 'bg-blue-50 text-blue-700 border border-blue-200';
                                            } elseif ($sub === 'ESTATE') {
                                                $badgeClasses = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                            } elseif ($sub === 'MILL') {
                                                $badgeClasses = 'bg-amber-50 text-amber-700 border border-amber-200';
                                            } elseif ($sub === 'TRAKSI') {
                                                $badgeClasses = 'bg-purple-50 text-purple-700 border border-purple-200';
                                            } elseif ($sub === 'DOWNSTREAM') {
                                                $badgeClasses = 'bg-cyan-50 text-cyan-700 border border-cyan-200';
                                            } elseif ($sub === 'ADMINISTRASI') {
                                                $badgeClasses = 'bg-rose-50 text-rose-700 border border-rose-200';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeClasses }}">
                                            {{ !empty($sub) ? $sub : $prog['sheet'] }}
                                        </span>
                                    </td>

                                    <!-- Region -->
                                    <td class="py-4 px-6 whitespace-nowrap text-[#71717A]">
                                        {{ $prog['region'] === '-' ? 'Nasional' : $prog['region'] }}
                                    </td>

                                    <!-- Target Batch -->
                                    <td class="py-4 px-6 whitespace-nowrap text-center font-medium">
                                        {{ $prog['total_batches'] }}
                                    </td>

                                    <!-- Target Participants -->
                                    <td class="py-4 px-6 whitespace-nowrap text-center font-bold text-primary-700">
                                        {{ number_format($prog['target_participants']['total']) }} <span class="text-xs font-normal text-[#71717A]">org</span>
                                    </td>

                                    <!-- Location -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-[#18181B] bg-white border border-[#E5E7EB] px-2.5 py-1 rounded-md shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                                            {{ $prog['location'] }}
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="py-4 px-6 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                        <button onclick="openScheduleModal({{ json_encode($prog) }})" class="text-primary-500 hover:text-primary-700 font-semibold text-xs flex items-center gap-1 mx-auto bg-primary-50 hover:bg-primary-100 px-3 py-1.5 rounded-lg transition-colors">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Lihat Jadwal
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#71717A]">
                                        <div class="flex flex-col items-center gap-2">
                                            <i data-lucide="folder-open" class="w-8 h-8 text-[#A1A1AA]"></i>
                                            <p class="font-medium text-sm">Tidak ada program training yang cocok.</p>
                                            <p class="text-xs text-[#A1A1AA]">Silakan ubah filter pencarian atau lokasi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary info -->
                <div class="p-6 bg-[#F8F9FA] border-t border-[#E5E7EB] flex items-center justify-between text-xs text-[#71717A] font-semibold uppercase tracking-wider">
                    <div>
                        Menampilkan {{ count($programs) }} dari {{ count($programs) }} Program Training
                    </div>
                    <div class="flex gap-2">
                        <button class="w-8 h-8 rounded-lg border border-[#E5E7EB] bg-white text-[#71717A] flex items-center justify-center cursor-not-allowed hover:bg-gray-50 transition-colors">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button class="w-8 h-8 rounded-lg border border-[#E5E7EB] bg-white text-[#71717A] flex items-center justify-center cursor-not-allowed hover:bg-gray-50 transition-colors">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 6. DETAILED SCHEDULE MODAL POPUP -->
    <div id="scheduleModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col shadow-2xl scale-95 transition-transform duration-300" id="modalContainer">
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between bg-primary-50">
                <div>
                    <span id="modalCategoryBadge" class="px-2.5 py-1 text-xs font-bold rounded-full bg-primary-100 text-primary-700">Category</span>
                    <h3 id="modalProgramName" class="text-xl font-bold text-primary-900 mt-2 tracking-tight">Nama Program</h3>
                    <p id="modalSectionName" class="text-sm text-primary-700 mt-0.5">Section Name</p>
                </div>
                <button onclick="closeScheduleModal()" class="w-10 h-10 rounded-full hover:bg-primary-100 text-[#71717A] flex items-center justify-center transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Section: Basic info -->
                    <div class="bg-[#F8F9FA] rounded-xl p-5 space-y-4 border border-[#E5E7EB]">
                        <h4 class="font-bold text-sm text-[#18181B] border-b border-[#E5E7EB] pb-2 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-primary-500"></i> Informasi Umum
                        </h4>
                        <div class="grid grid-cols-2 gap-y-3 text-sm">
                            <div class="text-[#71717A] font-medium">Region:</div>
                            <div id="modalRegion" class="font-semibold text-[#18181B]">-</div>

                            <div class="text-[#71717A] font-medium">Trainer:</div>
                            <div id="modalTrainer" class="font-semibold text-[#18181B]">-</div>

                            <div class="text-[#71717A] font-medium">Tipe Training:</div>
                            <div id="modalType" class="font-semibold text-[#18181B]">-</div>

                            <div class="text-[#71717A] font-medium">Total Jam / Hari:</div>
                            <div id="modalHours" class="font-semibold text-[#18181B]">-</div>

                            <div class="text-[#71717A] font-medium">Rencana Batch:</div>
                            <div id="modalBatches" class="font-semibold text-[#18181B]">-</div>

                            <div class="text-[#71717A] font-medium">Lokasi Training:</div>
                            <div id="modalLocation" class="font-semibold text-[#18181B]">-</div>
                        </div>
                    </div>

                    <!-- Right Section: Participants Breakdown -->
                    <div class="bg-[#F8F9FA] rounded-xl p-5 space-y-4 border border-[#E5E7EB]">
                        <h4 class="font-bold text-sm text-[#18181B] border-b border-[#E5E7EB] pb-2 flex items-center gap-2">
                            <i data-lucide="users" class="w-4 h-4 text-primary-500"></i> Target & Breakdown Peserta
                        </h4>

                        <!-- Breakdown Grid -->
                        <div class="grid grid-cols-4 gap-2 text-center text-xs">
                            <div class="bg-white border rounded-lg p-2">
                                <p class="text-[#71717A]">Non Staff</p>
                                <p id="breakdownNonStaff" class="font-bold text-sm mt-1 text-[#18181B]">0</p>
                            </div>
                            <div class="bg-white border rounded-lg p-2">
                                <p class="text-[#71717A]">Assistant</p>
                                <p id="breakdownAst" class="font-bold text-sm mt-1 text-[#18181B]">0</p>
                            </div>
                            <div class="bg-white border rounded-lg p-2">
                                <p class="text-[#71717A]">Askep</p>
                                <p id="breakdownAskep" class="font-bold text-sm mt-1 text-[#18181B]">0</p>
                            </div>
                            <div class="bg-white border rounded-lg p-2">
                                <p class="text-[#71717A]">Manager</p>
                                <p id="breakdownMgr" class="font-bold text-sm mt-1 text-[#18181B]">0</p>
                            </div>
                            <div class="bg-white border rounded-lg p-2">
                                <p class="text-[#71717A]">GM</p>
                                <p id="breakdownGM" class="font-bold text-sm mt-1 text-[#18181B]">0</p>
                            </div>
                            <div class="bg-white border rounded-lg p-2">
                                <p class="text-[#71717A]">Head</p>
                                <p id="breakdownHead" class="font-bold text-sm mt-1 text-[#18181B]">0</p>
                            </div>
                            <div class="bg-white border rounded-lg p-2">
                                <p class="text-[#71717A]">Staff</p>
                                <p id="breakdownStaf" class="font-bold text-sm mt-1 text-[#18181B]">0</p>
                            </div>
                            <div class="bg-primary-50 border border-primary-200 rounded-lg p-2">
                                <p class="text-primary-700 font-semibold">Total Target</p>
                                <p id="breakdownTotal" class="font-bold text-sm mt-1 text-primary-800">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Calendar Schedule (12 months layout) -->
                <div>
                    <h4 class="font-bold text-sm text-[#18181B] mb-3 flex items-center gap-2">
                        <i data-lucide="calendar-range" class="w-4 h-4 text-primary-500"></i> Kalender Rencana Pelaksanaan (Jan - Des 2026)
                    </h4>

                    <div class="grid grid-cols-3 md:grid-cols-6 gap-3" id="calendarGrid">
                        @php
                            $monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sept', 'Okt', 'Nov', 'Des'];
                        @endphp
                        @foreach ($monthsList as $m)
                            <div id="m-card-{{ $m }}" class="border rounded-xl p-3 text-center flex flex-col justify-between h-20 transition-all">
                                <p class="text-xs font-semibold text-[#71717A] uppercase tracking-wider">{{ $m }}</p>
                                <p id="m-val-{{ $m }}" class="text-sm font-bold text-[#18181B] mt-1">-</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-[#E5E7EB] flex justify-end bg-[#F8F9FA]">
                <button onclick="closeScheduleModal()" class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-semibold text-sm rounded-lg px-6 py-2.5 shadow-sm transition-colors">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript to control the detailed popup modal -->
    <script>
        // Initialize lucide icons
        lucide.createIcons();

        function openScheduleModal(program) {
            document.getElementById('modalProgramName').textContent = program.name;
            document.getElementById('modalSectionName').textContent = program.section;
            document.getElementById('modalRegion').textContent = program.region === '-' ? 'Nasional' : program.region;
            document.getElementById('modalTrainer').textContent = program.trainer;
            document.getElementById('modalType').textContent = program.type_training;
            document.getElementById('modalHours').textContent = program.total_hours > 0 ? (program.total_hours + " Jam (" + program.days + " Hari)") : "-";
            document.getElementById('modalBatches').textContent = program.total_batches + " Batch";
            document.getElementById('modalLocation').textContent = program.location;

            document.getElementById('breakdownNonStaff').textContent = program.participants.non_staff || '0';
            document.getElementById('breakdownAst').textContent = program.participants.ast || '0';
            document.getElementById('breakdownAskep').textContent = program.participants.askep || '0';
            document.getElementById('breakdownMgr').textContent = program.participants.mgr || '0';
            document.getElementById('breakdownGM').textContent = program.participants.gm || '0';
            document.getElementById('breakdownHead').textContent = program.participants.head || '0';
            document.getElementById('breakdownStaf').textContent = program.target_participants.staf || '0';
            document.getElementById('breakdownTotal').textContent = program.target_participants.total || '0';

            const badge = document.getElementById('modalCategoryBadge');
            badge.textContent = program.subsection ? program.subsection : program.sheet;
            badge.className = "px-2.5 py-1 text-xs font-bold rounded-full ";

            const sub = program.subsection;
            if (program.sheet === 'Reguler') {
                badge.className += "bg-blue-100 text-blue-800";
            } else if (sub === 'ESTATE') {
                badge.className += "bg-emerald-100 text-emerald-800";
            } else if (sub === 'MILL') {
                badge.className += "bg-amber-100 text-amber-800";
            } else if (sub === 'TRAKSI') {
                badge.className += "bg-purple-100 text-purple-800";
            } else if (sub === 'DOWNSTREAM') {
                badge.className += "bg-cyan-100 text-cyan-800";
            } else {
                badge.className += "bg-gray-100 text-gray-800";
            }

            const monthsList = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sept', 'Okt', 'Nov', 'Des'];

            monthsList.forEach(m => {
                const card = document.getElementById('m-card-' + m);
                const valNode = document.getElementById('m-val-' + m);

                if (program.calendar && program.calendar[m]) {
                    const scheduleVal = program.calendar[m];
                    valNode.textContent = program.sheet === 'Reguler' ? (scheduleVal + " org") : ("Batch " + scheduleVal);
                    valNode.className = "text-sm font-bold text-primary-800 mt-1";
                    card.className = "border border-primary-300 bg-primary-50 rounded-xl p-3 text-center flex flex-col justify-between h-20 shadow-xs ring-1 ring-primary-200";
                } else {
                    valNode.textContent = "-";
                    valNode.className = "text-sm font-bold text-[#A1A1AA] mt-1";
                    card.className = "border border-[#E5E7EB] bg-white rounded-xl p-3 text-center flex flex-col justify-between h-20 opacity-60";
                }
            });

            const modal = document.getElementById('scheduleModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                document.getElementById('modalContainer').classList.remove('scale-95');
                document.getElementById('modalContainer').classList.add('scale-100');
            }, 50);
        }

        function closeScheduleModal() {
            const modal = document.getElementById('scheduleModal');
            document.getElementById('modalContainer').classList.remove('scale-100');
            document.getElementById('modalContainer').classList.add('scale-95');
            modal.classList.remove('opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeScheduleModal();
        });
        document.getElementById('scheduleModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('scheduleModal')) closeScheduleModal();
        });
    </script>
    TEST
</body>
</html>
