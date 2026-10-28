<x-admin-layout>
    <x-slot name="title">Profil Trainee | FR Academy</x-slot>
    <x-slot name="header">Profil Trainee | FR Academy</x-slot>

    @push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F4F4F5;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }
    </style>
    @endpush

    <div class="space-y-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-xs">
                <div class="flex items-center gap-2 font-medium text-sm mb-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                    <span>Terdapat beberapa kesalahan pengisian formulir:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-2">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl lg:text-3xl font-bold text-[#18181B] tracking-tight flex items-center gap-2.5">
                    <span class="p-2 bg-[#509c38]/10 text-[#509c38] rounded-xl inline-flex">
                        <i data-lucide="contact" class="w-6 h-6"></i>
                    </span>
                    Profil Trainee
                </h2>
                <p class="text-[#71717A] mt-1 text-sm">
                    Database riwayat, profil lengkap, latar belakang pendidikan, dan rekam jejak calon & peserta trainee.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Export Excel -->
                <a href="{{ route('administrasi.profil-trainee.export', request()->all()) }}"
                   class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-xs sm:text-sm rounded-lg px-3.5 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                    <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i>
                    <span>Export Excel</span>
                </a>

                <!-- Download Template -->
                <a href="{{ route('administrasi.profil-trainee.template') }}"
                   class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-xs sm:text-sm rounded-lg px-3.5 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#71717A]"></i>
                    <span>Template</span>
                </a>

                <!-- Import Button Trigger -->
                <button type="button" onclick="openImportModal()"
                        class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-xs sm:text-sm rounded-lg px-3.5 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                    <i data-lucide="upload" class="w-4 h-4 text-[#71717A]"></i>
                    <span>Import Excel</span>
                </button>

                <!-- Tambah Data Button -->
                <button type="button" onclick="openAddModal()"
                        class="bg-[#509c38] hover:bg-[#43832f] text-white font-medium text-xs sm:text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>+ Tambah Trainee</span>
                </button>

                @if($trainees->total() > 0)
                <!-- Hapus Semua Trigger -->
                <button type="button" onclick="openDeleteAllModal()"
                        class="bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-700 font-medium text-xs sm:text-sm rounded-lg px-3 py-2 flex items-center gap-1.5 transition-colors shadow-2xs"
                        title="Hapus Seluruh Data">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Hapus Semua</span>
                </button>
                @endif
            </div>
        </div>

        <!-- 2. Filter & Search Bar -->
        <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs">
            <form action="{{ route('administrasi.profil-trainee.index') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <i data-lucide="search" class="w-4 h-4 text-[#A1A1AA] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari ID, Nama, Jabatan, Kampus, Jurusan..."
                               class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38] transition-all text-[#18181B] placeholder-[#A1A1AA]">
                    </div>

                    <!-- Filter Nama Jabatan -->
                    <div>
                        <select name="nama_jabatan"
                                onchange="this.form.submit()"
                                class="w-full px-3 py-2 text-xs sm:text-sm bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38] text-[#18181B] transition-all cursor-pointer">
                            <option value="">Semua Jabatan</option>
                            @foreach($jabatanList as $j)
                                <option value="{{ $j }}" {{ $filterJabatan === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun Lulusan -->
                    <div>
                        <select name="tahun_lulusan"
                                onchange="this.form.submit()"
                                class="w-full px-3 py-2 text-xs sm:text-sm bg-[#F9FAFB] border border-[#E5E7EB] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38] text-[#18181B] transition-all cursor-pointer">
                            <option value="">Semua Tahun Lulusan</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ ((string)$filterTahunLulus === (string)$y) ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action: Cari & Reset -->
                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 bg-[#18181B] hover:bg-black text-white text-xs sm:text-sm font-medium py-2 px-3 rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow-2xs">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            <span>Cari</span>
                        </button>
                        @if($search !== '' || $filterGender !== '' || $filterUniversitas !== '' || $filterTahunLulus !== '' || $filterJabatan !== '')
                            <a href="{{ route('administrasi.profil-trainee.index') }}"
                               class="p-2 border border-[#E5E7EB] rounded-xl text-[#71717A] hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-2xs"
                               title="Reset Semua Filter">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- 3. Ringkasan Statistik (Stats Cards PHP) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
            <!-- Card 1: Total Trainee -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#71717A]">Total Trainee</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-[#509c38]">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-2xl font-bold text-[#18181B]">{{ number_format($stats['total_trainee']) }}</h3>
                    <span class="text-xs text-[#71717A]">Orang</span>
                </div>
            </div>

            <!-- Card 2: Rata-rata IPK -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#71717A]">Rata-rata IPK</span>
                    <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                        <i data-lucide="award" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-2xl font-bold text-amber-600">{{ $stats['avg_ipk'] }}</h3>
                    <span class="text-xs text-[#71717A]">/ 4.00</span>
                </div>
            </div>

            <!-- Card 3: Rata-rata Usia -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#71717A]">Rata-rata Usia</span>
                    <span class="p-1.5 rounded-lg bg-sky-50 text-sky-600">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <h3 class="text-2xl font-bold text-sky-700">{{ $stats['avg_usia'] }}</h3>
                    <span class="text-xs text-[#71717A]">Tahun</span>
                </div>
            </div>
        </div>

        <!-- 2.5 Ringkasan Grafik Demografi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5 sm:gap-4">
            <!-- Chart 1: Asal Universitas (Horizontal Bar) -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-[#F4F4F5]">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-[#509c38]"></i>
                        <span>Asal Universitas</span>
                    </h4>
                    <span class="text-[10px] font-bold text-[#509c38] bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-md">{{ count($chartUniversitas['labels']) }} Kampus</span>
                </div>
                @if(count($chartUniversitas['labels']) > 0)
                    <div class="h-56 w-full overflow-y-auto pr-1">
                        <div style="height: {{ max(220, count($chartUniversitas['labels']) * 28) }}px;" class="w-full relative">
                            <canvas id="chartUniversitas"></canvas>
                        </div>
                    </div>
                @else
                    <div class="h-56 flex flex-col items-center justify-center text-[#A1A1AA] bg-[#F9FAFB] rounded-xl border border-dashed border-[#E5E7EB]">
                        <i data-lucide="bar-chart-2" class="w-6 h-6 text-[#D4D4D8] mb-1"></i>
                        <span class="text-xs">Belum ada data universitas</span>
                    </div>
                @endif
            </div>

            <!-- Chart 2: Pekerjaan Orang Tua (Doughnut) -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-[#F4F4F5]">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                        <i data-lucide="briefcase" class="w-4 h-4 text-amber-600"></i>
                        <span>Pekerjaan Orang Tua</span>
                    </h4>
                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-md">{{ count($chartPekerjaanOrtu['labels']) }} Kategori</span>
                </div>
                @if(count($chartPekerjaanOrtu['labels']) > 0)
                    <div class="h-56 w-full relative">
                        <canvas id="chartPekerjaanOrtu"></canvas>
                    </div>
                @else
                    <div class="h-56 flex flex-col items-center justify-center text-[#A1A1AA] bg-[#F9FAFB] rounded-xl border border-dashed border-[#E5E7EB]">
                        <i data-lucide="pie-chart" class="w-6 h-6 text-[#D4D4D8] mb-1"></i>
                        <span class="text-xs">Belum ada data pekerjaan ortu</span>
                    </div>
                @endif
            </div>

            <!-- Chart 3: Distribusi Suku (Doughnut) -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-[#F4F4F5]">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                        <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                        <span>Distribusi Suku</span>
                    </h4>
                    <span class="text-[10px] font-bold text-purple-700 bg-purple-50 border border-purple-200/60 px-2 py-0.5 rounded-md">{{ count($chartSuku['labels']) }} Suku</span>
                </div>
                @if(count($chartSuku['labels']) > 0)
                    <div class="h-56 w-full relative">
                        <canvas id="chartSuku"></canvas>
                    </div>
                @else
                    <div class="h-56 flex flex-col items-center justify-center text-[#A1A1AA] bg-[#F9FAFB] rounded-xl border border-dashed border-[#E5E7EB]">
                        <i data-lucide="pie-chart" class="w-6 h-6 text-[#D4D4D8] mb-1"></i>
                        <span class="text-xs">Belum ada data suku</span>
                    </div>
                @endif
            </div>

            <!-- Chart 4: Kota / Provinsi Asal (Horizontal Bar) -->
            <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2 pb-2 border-b border-[#F4F4F5]">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#18181B] flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-4 h-4 text-sky-600"></i>
                        <span>Kota / Provinsi Asal</span>
                    </h4>
                    <span class="text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-200/60 px-2 py-0.5 rounded-md">{{ count($chartProvinsi['labels']) }} Daerah</span>
                </div>
                @if(count($chartProvinsi['labels']) > 0)
                    <div class="h-56 w-full overflow-y-auto pr-1">
                        <div style="height: {{ max(220, count($chartProvinsi['labels']) * 28) }}px;" class="w-full relative">
                            <canvas id="chartProvinsi"></canvas>
                        </div>
                    </div>
                @else
                    <div class="h-56 flex flex-col items-center justify-center text-[#A1A1AA] bg-[#F9FAFB] rounded-xl border border-dashed border-[#E5E7EB]">
                        <i data-lucide="bar-chart-2" class="w-6 h-6 text-[#D4D4D8] mb-1"></i>
                        <span class="text-xs">Belum ada data kota/provinsi</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- 4. Table Section -->
        <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-2xs overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs text-[#18181B] border-collapse min-w-[1300px]">
                    <thead class="bg-[#F9FAFB] text-[#71717A] text-[11px] uppercase font-bold tracking-wider border-b border-[#E5E7EB]">
                        <tr>
                            <th class="py-3 px-3 text-center w-12 bg-[#F9FAFB]">No</th>
                            <th class="py-3 px-3 w-32 bg-[#F9FAFB]">Trainee ID</th>
                            <th class="py-3 px-4 min-w-[180px] bg-[#F9FAFB]">Nama Trainee</th>
                            <th class="py-3 px-4 min-w-[160px]">Nama Jabatan</th>
                            <th class="py-3 px-4 min-w-[180px]">Asal Universitas</th>
                            <th class="py-3 px-4 min-w-[160px]">Jurusan</th>
                            <th class="py-3 px-3 text-center">Gender</th>
                            <th class="py-3 px-3 text-center">Usia</th>
                            <th class="py-3 px-3 text-center">Pendidikan</th>
                            <th class="py-3 px-3 text-center">IPK</th>
                            <th class="py-3 px-4 min-w-[160px]">Domisili Asal</th>
                            <th class="py-3 px-3 text-center">TB / BB</th>
                            <th class="py-3 px-4 text-center min-w-[110px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($trainees as $index => $t)
                            <tr class="hover:bg-[#F4F4F5]/60 transition-colors group">
                                <td class="py-3.5 px-3 text-center text-[#71717A]">
                                    {{ $trainees->firstItem() + $index }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-[#509c38]">
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-[11px]">
                                        {{ $t->trainee_id ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-[#18181B]">
                                    <button type="button" onclick="openDetailModal({{ $t->id }})" class="hover:text-[#509c38] text-left">
                                        {{ $t->nama_trainee }}
                                    </button>
                                </td>
                                <td class="py-3.5 px-4 text-[#52525B]">
                                    {{ $t->nama_jabatan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-[#52525B]">
                                    {{ $t->asal_universitas ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-[#52525B]">
                                    {{ $t->jurusan ?? '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    @if(str_contains(strtolower($t->jenis_kelamin ?? ''), 'laki'))
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Laki-laki</span>
                                    @elseif(str_contains(strtolower($t->jenis_kelamin ?? ''), 'perempuan'))
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-pink-50 text-pink-700 border border-pink-200">Perempuan</span>
                                    @else
                                        <span class="text-[#A1A1AA]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-center text-[#52525B]">
                                    {{ $t->usia ? $t->usia . ' Th' : '-' }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    @if($t->pendidikan_terakhir)
                                        <span class="px-2 py-0.5 rounded-md bg-gray-100 text-[#52525B] font-medium text-[11px]">
                                            {{ $t->pendidikan_terakhir }}
                                        </span>
                                    @else
                                        <span class="text-[#A1A1AA]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-center font-bold">
                                    @if($t->ipk !== null)
                                        <span class="{{ $t->ipk >= 3.5 ? 'text-emerald-700 bg-emerald-50' : ($t->ipk >= 3.0 ? 'text-amber-700 bg-amber-50' : 'text-zinc-700 bg-zinc-100') }} px-2 py-0.5 rounded-md text-[11px] border border-current/20">
                                            {{ number_format($t->ipk, 2) }}
                                        </span>
                                    @else
                                        <span class="text-[#A1A1AA]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[#52525B]">
                                    {{ $t->daerah_kabupaten_asal ? $t->daerah_kabupaten_asal : '' }}{{ ($t->daerah_kabupaten_asal && $t->kota_provinsi_asal) ? ', ' : '' }}{{ $t->kota_provinsi_asal ? $t->kota_provinsi_asal : ($t->daerah_kabupaten_asal ? '' : '-') }}
                                </td>
                                <td class="py-3.5 px-3 text-center text-[#52525B] whitespace-nowrap">
                                    {{ $t->tinggi_badan ? $t->tinggi_badan . 'cm' : '-' }} / {{ $t->berat_badan ? $t->berat_badan . 'kg' : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Detail Button -->
                                        <button type="button" onclick="openDetailModal({{ $t->id }})"
                                                class="p-1.5 rounded-lg border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#52525B] hover:text-[#18181B] transition-colors"
                                                title="Lihat Detail Profil">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <!-- Edit Button -->
                                        <button type="button" onclick="openEditModal({{ $t->id }})"
                                                class="p-1.5 rounded-lg border border-[#E5E7EB] hover:bg-emerald-50 text-[#52525B] hover:text-[#509c38] hover:border-emerald-200 transition-colors"
                                                title="Edit Profil">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" onclick="openDeleteModal({{ $t->id }}, '{{ addslashes($t->nama_trainee) }}')"
                                                class="p-1.5 rounded-lg border border-[#E5E7EB] hover:bg-rose-50 text-[#52525B] hover:text-rose-600 hover:border-rose-200 transition-colors"
                                                title="Hapus Data">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="py-12 text-center text-[#71717A]">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="inbox" class="w-10 h-10 text-[#D4D4D8]"></i>
                                        <span class="text-sm font-medium">Belum ada data Profil Trainee.</span>
                                        <span class="text-xs text-[#A1A1AA]">Gunakan tombol "+ Tambah Trainee" atau "Import Excel" untuk menambahkan data.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($trainees->hasPages())
                <div class="p-4 border-t border-[#E5E7EB] bg-[#F9FAFB] flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-[#71717A]">
                        Menampilkan <span class="font-semibold text-[#18181B]">{{ $trainees->firstItem() }}</span> sampai
                        <span class="font-semibold text-[#18181B]">{{ $trainees->lastItem() }}</span> dari
                        <span class="font-semibold text-[#18181B]">{{ $trainees->total() }}</span> total data
                    </div>
                    <div>
                        {{ $trainees->links() }}
                    </div>
                </div>
            @endif
        </div>

    </div>

    <!-- ==================== MODAL TAMBAH TRAINEE ==================== -->
    <div id="addModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl border border-[#E5E7EB] max-h-[90vh] flex flex-col my-auto">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 bg-emerald-50 text-[#509c38] rounded-xl">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-[#18181B]">Tambah Profil Trainee</h3>
                        <p class="text-xs text-[#71717A]">Isi data identitas, pendidikan, fisik, dan rekam jejak peserta trainee</p>
                    </div>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('administrasi.profil-trainee.store') }}" method="POST" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                @csrf

                <!-- Section A: Identitas Utama & Jabatan -->
                <div>
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="user" class="w-4 h-4"></i> Identitas & Jabatan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Trainee ID (Opsional)</label>
                            <input type="text" name="trainee_id" placeholder="Auto-generate jika kosong (e.g. TRN-0001)"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38] bg-[#F9FAFB]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Nama Trainee <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_trainee" required placeholder="Nama lengkap trainee"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Nama Jabatan</label>
                            <input type="text" name="nama_jabatan" placeholder="e.g. Asisten Agronomi"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                                <option value="">Pilih Gender</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Usia (Tahun)</label>
                            <input type="number" name="usia" min="15" max="90" placeholder="e.g. 23"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Suku</label>
                            <input type="text" name="suku" placeholder="e.g. Jawa, Batak, Sunda"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Agama</label>
                            <input type="text" name="agama" placeholder="e.g. Islam, Kristen, Katolik"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" placeholder="e.g. 172"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" placeholder="e.g. 65"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <!-- Section B: Pendidikan & Akademik -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i> Latar Belakang Pendidikan & Akademik
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Asal Universitas</label>
                            <input type="text" name="asal_universitas" placeholder="e.g. Universitas Gadjah Mada"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Jurusan / Program Studi</label>
                            <input type="text" name="jurusan" placeholder="e.g. Agronomi & Hortikultura"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Tahun Lulus</label>
                            <input type="number" name="tahun_lulus" placeholder="e.g. 2025"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Masa Studi</label>
                            <input type="text" name="masa_studi" placeholder="e.g. 4 Tahun / 3.5 Tahun"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">IPK (Skala 4.00)</label>
                            <input type="number" step="0.01" min="0" max="4" name="ipk" placeholder="e.g. 3.75"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <!-- Section C: Domisili & Keluarga -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-4 h-4"></i> Asal Daerah & Keluarga
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Daerah / Kabupaten Asal</label>
                            <input type="text" name="daerah_kabupaten_asal" placeholder="e.g. Sleman / Kampar"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Kota / Provinsi Asal</label>
                            <input type="text" name="kota_provinsi_asal" placeholder="e.g. D.I. Yogyakarta / Riau"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pekerjaan Orangtua</label>
                            <input type="text" name="pekerjaan_orangtua" placeholder="e.g. Wiraswasta / Petani / PNS"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <!-- Section D: Rekam Jejak, Organisasi & Prestasi -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="award" class="w-4 h-4"></i> Pengalaman, Prestasi & Hobi
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pengalaman Organisasi</label>
                            <textarea name="pengalaman_organisasi" rows="2" placeholder="Sebutkan jabatan, nama organisasi, dan kurun waktu"
                                      class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pengalaman Praktik Kerja / Magang</label>
                            <textarea name="pengalaman_praktek_kerja_magang" rows="2" placeholder="Nama instansi/perusahaan, posisi, deskripsi singkat"
                                      class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#18181B] mb-1">Prestasi Diri</label>
                                <textarea name="prestasi_diri" rows="2" placeholder="Penghargaan atau kejuaraan yang pernah diraih"
                                          class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#18181B] mb-1">Hobi</label>
                                <input type="text" name="hobi" placeholder="e.g. Olahraga, Membaca, Fotografi"
                                       class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section E: Riwayat Kesehatan -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="heart-pulse" class="w-4 h-4"></i> Riwayat Kesehatan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Status Riwayat Kesehatan</label>
                            <select name="riwayat_kesehatan" class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                                <option value="Tidak Ada">Tidak Ada (Sehat)</option>
                                <option value="Ada">Ada Riwayat Penyakit</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Catatan Riwayat Kesehatan</label>
                            <input type="text" name="catatan_riwayat_kesehatan" placeholder="Rincian alergi, riwayat operasi, atau penyakit tertentu (jika ada)"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-[#509c38] hover:bg-[#43832f] text-white shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan Data Trainee
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL EDIT TRAINEE ==================== -->
    <div id="editModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl border border-[#E5E7EB] max-h-[90vh] flex flex-col my-auto">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB]">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 bg-emerald-50 text-[#509c38] rounded-xl">
                        <i data-lucide="edit-3" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-[#18181B]">Edit Profil Trainee</h3>
                        <p class="text-xs text-[#71717A]">Perbarui data identitas dan kualifikasi peserta trainee</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="editForm" method="POST" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                @csrf
                @method('PUT')

                <!-- Section A: Identitas Utama & Jabatan -->
                <div>
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="user" class="w-4 h-4"></i> Identitas & Jabatan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Trainee ID</label>
                            <input type="text" id="edit_trainee_id" name="trainee_id"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38] bg-[#F9FAFB]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Nama Trainee <span class="text-rose-500">*</span></label>
                            <input type="text" id="edit_nama_trainee" name="nama_trainee" required
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Nama Jabatan</label>
                            <input type="text" id="edit_nama_jabatan" name="nama_jabatan"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Jenis Kelamin</label>
                            <select id="edit_jenis_kelamin" name="jenis_kelamin" class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                                <option value="">Pilih Gender</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Usia (Tahun)</label>
                            <input type="number" id="edit_usia" name="usia" min="15" max="90"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Suku</label>
                            <input type="text" id="edit_suku" name="suku"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Agama</label>
                            <input type="text" id="edit_agama" name="agama"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" id="edit_tinggi_badan" name="tinggi_badan"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Berat Badan (kg)</label>
                            <input type="number" step="0.1" id="edit_berat_badan" name="berat_badan"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <!-- Section B: Pendidikan & Akademik -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i> Latar Belakang Pendidikan & Akademik
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Asal Universitas</label>
                            <input type="text" id="edit_asal_universitas" name="asal_universitas"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Jurusan / Program Studi</label>
                            <input type="text" id="edit_jurusan" name="jurusan"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pendidikan Terakhir</label>
                            <select id="edit_pendidikan_terakhir" name="pendidikan_terakhir" class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Tahun Lulus</label>
                            <input type="number" id="edit_tahun_lulus" name="tahun_lulus"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Masa Studi</label>
                            <input type="text" id="edit_masa_studi" name="masa_studi"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">IPK (Skala 4.00)</label>
                            <input type="number" step="0.01" min="0" max="4" id="edit_ipk" name="ipk"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <!-- Section C: Domisili & Keluarga -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-4 h-4"></i> Asal Daerah & Keluarga
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Daerah / Kabupaten Asal</label>
                            <input type="text" id="edit_daerah_kabupaten_asal" name="daerah_kabupaten_asal"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Kota / Provinsi Asal</label>
                            <input type="text" id="edit_kota_provinsi_asal" name="kota_provinsi_asal"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pekerjaan Orangtua</label>
                            <input type="text" id="edit_pekerjaan_orangtua" name="pekerjaan_orangtua"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <!-- Section D: Rekam Jejak, Organisasi & Prestasi -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="award" class="w-4 h-4"></i> Pengalaman, Prestasi & Hobi
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pengalaman Organisasi</label>
                            <textarea id="edit_pengalaman_organisasi" name="pengalaman_organisasi" rows="2"
                                      class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Pengalaman Praktik Kerja / Magang</label>
                            <textarea id="edit_pengalaman_praktek_kerja_magang" name="pengalaman_praktek_kerja_magang" rows="2"
                                      class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#18181B] mb-1">Prestasi Diri</label>
                                <textarea id="edit_prestasi_diri" name="prestasi_diri" rows="2"
                                          class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-[#18181B] mb-1">Hobi</label>
                                <input type="text" id="edit_hobi" name="hobi"
                                       class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section E: Riwayat Kesehatan -->
                <div class="border-t border-[#E5E7EB] pt-5">
                    <h4 class="text-xs font-bold text-[#509c38] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="heart-pulse" class="w-4 h-4"></i> Riwayat Kesehatan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Status Riwayat Kesehatan</label>
                            <select id="edit_riwayat_kesehatan" name="riwayat_kesehatan" class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                                <option value="Tidak Ada">Tidak Ada (Sehat)</option>
                                <option value="Ada">Ada Riwayat Penyakit</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-[#18181B] mb-1">Catatan Riwayat Kesehatan</label>
                            <input type="text" id="edit_catatan_riwayat_kesehatan" name="catatan_riwayat_kesehatan"
                                   class="w-full text-xs sm:text-sm px-3 py-2 border border-[#E5E7EB] rounded-xl focus:ring-2 focus:ring-[#509c38]/20 focus:border-[#509c38]">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-[#509c38] hover:bg-[#43832f] text-white shadow-sm flex items-center gap-1.5">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL DETAIL TRAINEE ==================== -->
    <div id="detailModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl border border-[#E5E7EB] max-h-[90vh] flex flex-col my-auto">
            <div class="flex items-center justify-between p-5 border-b border-[#E5E7EB] bg-[#F9FAFB] rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-white border border-[#E5E7EB] text-[#509c38] rounded-xl shadow-2xs">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="detail_nama" class="text-lg font-bold text-[#18181B]">-</h3>
                            <span id="detail_badge_id" class="px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-[#509c38] text-[11px] font-semibold">-</span>
                        </div>
                        <p id="detail_jabatan" class="text-xs text-[#71717A] mt-0.5">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-[#71717A] hover:text-[#18181B] p-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar text-xs sm:text-sm">
                <!-- Grid Info Cepat -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-[#F9FAFB] rounded-xl border border-[#E5E7EB]">
                    <div>
                        <span class="text-[11px] text-[#71717A] block font-medium">Jenis Kelamin</span>
                        <span id="detail_gender" class="font-semibold text-[#18181B]">-</span>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#71717A] block font-medium">Usia</span>
                        <span id="detail_usia" class="font-semibold text-[#18181B]">-</span>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#71717A] block font-medium">Tinggi / Berat</span>
                        <span id="detail_tb_bb" class="font-semibold text-[#18181B]">-</span>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#71717A] block font-medium">Agama / Suku</span>
                        <span id="detail_agama_suku" class="font-semibold text-[#18181B]">-</span>
                    </div>
                </div>

                <!-- Akademik & Pendidikan -->
                <div class="space-y-3">
                    <h4 class="font-bold text-[#18181B] text-xs uppercase tracking-wider flex items-center gap-1.5 pb-2 border-b border-[#E5E7EB]">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-[#509c38]"></i> Pendidikan & Akademik
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-4">
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Asal Universitas:</span>
                            <span id="detail_universitas" class="font-semibold text-[#18181B]">-</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Jurusan:</span>
                            <span id="detail_jurusan" class="font-semibold text-[#18181B]">-</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Jenjang Pendidikan Terakhir:</span>
                            <span id="detail_pendidikan" class="font-semibold text-[#18181B]">-</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#71717A] block">IPK & Masa Studi:</span>
                            <span id="detail_ipk_studi" class="font-semibold text-[#18181B]">-</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Tahun Kelulusan:</span>
                            <span id="detail_tahun_lulus" class="font-semibold text-[#18181B]">-</span>
                        </div>
                    </div>
                </div>

                <!-- Domisili & Keluarga -->
                <div class="space-y-3">
                    <h4 class="font-bold text-[#18181B] text-xs uppercase tracking-wider flex items-center gap-1.5 pb-2 border-b border-[#E5E7EB]">
                        <i data-lucide="map-pin" class="w-4 h-4 text-[#509c38]"></i> Domisili & Orang Tua
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-4">
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Daerah / Kabupaten Asal:</span>
                            <span id="detail_kabupaten" class="font-semibold text-[#18181B]">-</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Kota / Provinsi Asal:</span>
                            <span id="detail_provinsi" class="font-semibold text-[#18181B]">-</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-[11px] text-[#71717A] block">Pekerjaan Orang Tua:</span>
                            <span id="detail_pekerjaan_ortu" class="font-semibold text-[#18181B]">-</span>
                        </div>
                    </div>
                </div>

                <!-- Pengalaman & Prestasi -->
                <div class="space-y-3">
                    <h4 class="font-bold text-[#18181B] text-xs uppercase tracking-wider flex items-center gap-1.5 pb-2 border-b border-[#E5E7EB]">
                        <i data-lucide="briefcase" class="w-4 h-4 text-[#509c38]"></i> Pengalaman, Prestasi & Minat
                    </h4>
                    <div class="space-y-2.5">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-[11px] font-bold text-[#71717A] block uppercase mb-1">Pengalaman Organisasi</span>
                            <p id="detail_organisasi" class="text-xs text-[#18181B] leading-relaxed whitespace-pre-line">-</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-[11px] font-bold text-[#71717A] block uppercase mb-1">Pengalaman Praktik Kerja / Magang</span>
                            <p id="detail_magang" class="text-xs text-[#18181B] leading-relaxed whitespace-pre-line">-</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="text-[11px] font-bold text-[#71717A] block uppercase mb-1">Prestasi Diri</span>
                            <p id="detail_prestasi" class="text-xs text-[#18181B] leading-relaxed whitespace-pre-line">-</p>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Hobi / Kegemaran:</span>
                            <span id="detail_hobi" class="font-semibold text-[#18181B]">-</span>
                        </div>
                    </div>
                </div>

                <!-- Kesehatan -->
                <div class="space-y-3">
                    <h4 class="font-bold text-[#18181B] text-xs uppercase tracking-wider flex items-center gap-1.5 pb-2 border-b border-[#E5E7EB]">
                        <i data-lucide="heart-pulse" class="w-4 h-4 text-[#509c38]"></i> Riwayat Kesehatan
                    </h4>
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[11px] text-[#71717A]">Status:</span>
                            <span id="detail_riwayat_kesehatan" class="font-semibold">-</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#71717A] block">Catatan Medis:</span>
                            <p id="detail_catatan_kesehatan" class="text-xs text-[#18181B] mt-0.5 leading-relaxed whitespace-pre-line">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-[#E5E7EB] bg-[#F9FAFB] rounded-b-2xl flex justify-end">
                <button type="button" onclick="closeDetailModal()"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-[#18181B] text-white hover:bg-black">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL IMPORT EXCEL/CSV ==================== -->
    <div id="importModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-[#E5E7EB] p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#E5E7EB] mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 bg-emerald-50 text-[#509c38] rounded-xl">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-[#18181B]">Import Profil Trainee</h3>
                        <p class="text-xs text-[#71717A]">Unggah berkas Excel (.xlsx) atau CSV (.csv)</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('administrasi.profil-trainee.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-[#E5E7EB] hover:border-[#509c38] rounded-2xl p-6 text-center cursor-pointer transition-colors bg-[#F9FAFB]">
                    <input type="file" id="fileImport" name="file" accept=".xlsx,.csv,.txt" required class="hidden" onchange="updateFileLabel(this)">
                    <label for="fileImport" class="cursor-pointer block">
                        <i data-lucide="cloud-upload" class="w-10 h-10 text-[#509c38] mx-auto mb-2"></i>
                        <span id="importFileLabel" class="text-sm font-semibold text-[#18181B] block">Pilih berkas Excel / CSV</span>
                        <span class="text-xs text-[#71717A] mt-1 block">Maksimal ukuran berkas 20MB (.xlsx, .csv)</span>
                    </label>
                </div>

                <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs text-emerald-800 space-y-1">
                    <p class="font-semibold flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4"></i> Petunjuk Import:
                    </p>
                    <p>Pastikan nama kolom pada berkas sesuai template. Jika kolom Trainee ID kosong, sistem akan otomatis menghasilkan kode TRN.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('administrasi.profil-trainee.template') }}" class="text-xs text-[#509c38] hover:underline font-semibold flex items-center gap-1">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh Format Template
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeImportModal()" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-[#509c38] hover:bg-[#43832f] text-white shadow-sm flex items-center gap-1.5">
                            <i data-lucide="upload" class="w-3.5 h-3.5"></i> Mulai Import
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL DELETE SATUAN ==================== -->
    <div id="deleteModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-[#E5E7EB] p-5">
            <div class="flex items-center gap-3 text-rose-600 mb-3">
                <span class="p-2.5 bg-rose-50 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </span>
                <h3 class="text-base font-bold text-[#18181B]">Konfirmasi Hapus Data</h3>
            </div>
            <p class="text-xs sm:text-sm text-[#71717A] mb-4">
                Apakah Anda yakin ingin menghapus data profil trainee <strong id="delete_target_name" class="text-[#18181B]">-</strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form id="deleteForm" method="POST" class="flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-semibold rounded-xl border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL DELETE SEMUA ==================== -->
    <div id="deleteAllModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-[#E5E7EB] p-5">
            <div class="flex items-center gap-3 text-rose-600 mb-3">
                <span class="p-2.5 bg-rose-50 rounded-xl">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                </span>
                <h3 class="text-base font-bold text-[#18181B]">Hapus Seluruh Data</h3>
            </div>
            <p class="text-xs sm:text-sm text-[#71717A] mb-4">
                Apakah Anda yakin ingin menghapus <strong class="text-rose-600">SEMUA DATA ({{ $stats['total_trainee'] }} data)</strong> Profil Trainee di sistem? Tindakan ini permanen.
            </p>
            <form action="{{ route('administrasi.profil-trainee.destroyAll') }}" method="POST" class="flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <input type="hidden" name="all" value="1">
                <button type="button" onclick="closeDeleteAllModal()" class="px-4 py-2 text-xs font-semibold rounded-xl border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm">
                    Ya, Kosongkan Semua Data
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function refreshIcons() {
            setTimeout(function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 50);
        }

        // Modal Add
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addModal').classList.add('flex');
            refreshIcons();
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.remove('flex');
            document.getElementById('addModal').classList.add('hidden');
        }

        // Modal Import
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            document.getElementById('importModal').classList.add('flex');
            refreshIcons();
        }
        function closeImportModal() {
            document.getElementById('importModal').classList.remove('flex');
            document.getElementById('importModal').classList.add('hidden');
        }
        function updateFileLabel(input) {
            if (input.files && input.files[0]) {
                document.getElementById('importFileLabel').innerText = input.files[0].name;
            }
        }

        // Modal Delete
        function openDeleteModal(id, name) {
            document.getElementById('delete_target_name').innerText = name;
            document.getElementById('deleteForm').action = `{{ url('administrasi/profil-trainee') }}/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
            refreshIcons();
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('flex');
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Modal Delete All
        function openDeleteAllModal() {
            document.getElementById('deleteAllModal').classList.remove('hidden');
            document.getElementById('deleteAllModal').classList.add('flex');
            refreshIcons();
        }
        function closeDeleteAllModal() {
            document.getElementById('deleteAllModal').classList.remove('flex');
            document.getElementById('deleteAllModal').classList.add('hidden');
        }

        // Modal Edit (Fetch Data via AJAX)
        function openEditModal(id) {
            fetch(`{{ url('administrasi/profil-trainee') }}/${id}`)
                .then(response => response.json())
                .then(res => {
                    if (res.success && res.data) {
                        const d = res.data;
                        document.getElementById('editForm').action = `{{ url('administrasi/profil-trainee') }}/${id}`;
                        document.getElementById('edit_trainee_id').value = d.trainee_id || '';
                        document.getElementById('edit_nama_trainee').value = d.nama_trainee || '';
                        document.getElementById('edit_nama_jabatan').value = d.nama_jabatan || '';
                        document.getElementById('edit_jenis_kelamin').value = d.jenis_kelamin || '';
                        document.getElementById('edit_usia').value = d.usia || '';
                        document.getElementById('edit_suku').value = d.suku || '';
                        document.getElementById('edit_agama').value = d.agama || '';
                        document.getElementById('edit_tinggi_badan').value = d.tinggi_badan || '';
                        document.getElementById('edit_berat_badan').value = d.berat_badan || '';
                        document.getElementById('edit_asal_universitas').value = d.asal_universitas || '';
                        document.getElementById('edit_jurusan').value = d.jurusan || '';
                        document.getElementById('edit_pendidikan_terakhir').value = d.pendidikan_terakhir || '';
                        document.getElementById('edit_tahun_lulus').value = d.tahun_lulus || '';
                        document.getElementById('edit_masa_studi').value = d.masa_studi || '';
                        document.getElementById('edit_ipk').value = d.ipk !== null ? d.ipk : '';
                        document.getElementById('edit_daerah_kabupaten_asal').value = d.daerah_kabupaten_asal || '';
                        document.getElementById('edit_kota_provinsi_asal').value = d.kota_provinsi_asal || '';
                        document.getElementById('edit_pekerjaan_orangtua').value = d.pekerjaan_orangtua || '';
                        document.getElementById('edit_pengalaman_organisasi').value = d.pengalaman_organisasi || '';
                        document.getElementById('edit_pengalaman_praktek_kerja_magang').value = d.pengalaman_praktek_kerja_magang || '';
                        document.getElementById('edit_prestasi_diri').value = d.prestasi_diri || '';
                        document.getElementById('edit_hobi').value = d.hobi || '';
                        document.getElementById('edit_riwayat_kesehatan').value = d.riwayat_kesehatan || 'Tidak Ada';
                        document.getElementById('edit_catatan_riwayat_kesehatan').value = d.catatan_riwayat_kesehatan || '';

                        document.getElementById('editModal').classList.remove('hidden');
                        document.getElementById('editModal').classList.add('flex');
                        refreshIcons();
                    }
                })
                .catch(err => {
                    console.error('Gagal mengambil data:', err);
                    alert('Gagal mengambil detail profil trainee.');
                });
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }

        // Modal Detail (Fetch Data via AJAX)
        function openDetailModal(id) {
            fetch(`{{ url('administrasi/profil-trainee') }}/${id}`)
                .then(response => response.json())
                .then(res => {
                    if (res.success && res.data) {
                        const d = res.data;
                        document.getElementById('detail_nama').innerText = d.nama_trainee || '-';
                        document.getElementById('detail_badge_id').innerText = d.trainee_id || '-';
                        document.getElementById('detail_jabatan').innerText = d.nama_jabatan ? `Posisi: ${d.nama_jabatan}` : 'Posisi: Belum Diatur';
                        document.getElementById('detail_gender').innerText = d.jenis_kelamin || '-';
                        document.getElementById('detail_usia').innerText = d.usia ? `${d.usia} Tahun` : '-';
                        document.getElementById('detail_tb_bb').innerText = (d.tinggi_badan || '-') + ' cm / ' + (d.berat_badan || '-') + ' kg';
                        document.getElementById('detail_agama_suku').innerText = (d.agama || '-') + ' / ' + (d.suku || '-');

                        document.getElementById('detail_universitas').innerText = d.asal_universitas || '-';
                        document.getElementById('detail_jurusan').innerText = d.jurusan || '-';
                        document.getElementById('detail_pendidikan').innerText = d.pendidikan_terakhir || '-';
                        document.getElementById('detail_ipk_studi').innerText = (d.ipk !== null ? `IPK: ${d.ipk}` : 'IPK: -') + (d.masa_studi ? ` (${d.masa_studi})` : '');
                        document.getElementById('detail_tahun_lulus').innerText = d.tahun_lulus ? `Tahun ${d.tahun_lulus}` : '-';

                        document.getElementById('detail_kabupaten').innerText = d.daerah_kabupaten_asal || '-';
                        document.getElementById('detail_provinsi').innerText = d.kota_provinsi_asal || '-';
                        document.getElementById('detail_pekerjaan_ortu').innerText = d.pekerjaan_orangtua || '-';

                        document.getElementById('detail_organisasi').innerText = d.pengalaman_organisasi || 'Tidak ada catatan pengalaman organisasi.';
                        document.getElementById('detail_magang').innerText = d.pengalaman_praktek_kerja_magang || 'Tidak ada catatan magang / PKL.';
                        document.getElementById('detail_prestasi').innerText = d.prestasi_diri || 'Tidak ada catatan prestasi diri.';
                        document.getElementById('detail_hobi').innerText = d.hobi || '-';

                        document.getElementById('detail_riwayat_kesehatan').innerText = d.riwayat_kesehatan || 'Tidak Ada (Sehat)';
                        document.getElementById('detail_catatan_kesehatan').innerText = d.catatan_riwayat_kesehatan || 'Kondisi kesehatan baik tanpa keluhan khusus.';

                        document.getElementById('detailModal').classList.remove('hidden');
                        document.getElementById('detailModal').classList.add('flex');
                        refreshIcons();
                    }
                })
                .catch(err => {
                    console.error('Gagal mengambil data:', err);
                    alert('Gagal mengambil data detail.');
                });
        }
        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('flex');
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close on background click
        window.addEventListener('click', function(e) {
            ['addModal', 'editModal', 'detailModal', 'importModal', 'deleteModal', 'deleteAllModal'].forEach(function(modalId) {
                const el = document.getElementById(modalId);
                if (el && e.target === el) {
                    el.classList.remove('flex');
                    el.classList.add('hidden');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const extendedPalette1 = [
                '#4B6A34', '#0284c7', '#f59e0b', '#ec4899', '#8b5cf6',
                '#10b981', '#6366f1', '#14b8a6', '#f97316', '#06b6d4',
                '#84cc16', '#a855f7', '#e11d48', '#3b82f6', '#d97706',
                '#059669', '#0d9488', '#475569', '#64748b', '#78716c'
            ];
            const extendedPalette2 = [
                '#059669', '#3b82f6', '#d97706', '#7c3aed', '#db2777',
                '#06b6d4', '#4B6A34', '#10b981', '#f59e0b', '#8b5cf6',
                '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#84cc16',
                '#a855f7', '#e11d48', '#475569', '#64748b', '#78716c'
            ];
            function getColors(palette, count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    colors.push(palette[i % palette.length]);
                }
                return colors;
            }

            // 1. Chart Asal Universitas (Horizontal Bar)
            const ctxUniv = document.getElementById('chartUniversitas');
            if (ctxUniv) {
                const labelsUniv = {!! json_encode($chartUniversitas['labels']) !!};
                const dataUniv = {!! json_encode($chartUniversitas['data']) !!};
                if (labelsUniv.length > 0) {
                    new Chart(ctxUniv, {
                        type: 'bar',
                        data: {
                            labels: labelsUniv,
                            datasets: [{
                                label: 'Jumlah Trainee',
                                data: dataUniv,
                                backgroundColor: '#509c38',
                                hoverBackgroundColor: '#43832f',
                                borderRadius: 6,
                                maxBarThickness: 22
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#18181B',
                                    padding: 8,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(ctx) { return ' ' + ctx.parsed.x + ' Trainee'; }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { precision: 0, font: { size: 10 } },
                                    grid: { color: '#F4F4F5' }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { font: { size: 10, weight: '600' }, color: '#3f3f46' }
                                }
                            }
                        }
                    });
                }
            }

            // 2. Chart Pekerjaan Orang Tua (Doughnut)
            const ctxOrtu = document.getElementById('chartPekerjaanOrtu');
            if (ctxOrtu) {
                const labelsOrtu = {!! json_encode($chartPekerjaanOrtu['labels']) !!};
                const dataOrtu = {!! json_encode($chartPekerjaanOrtu['data']) !!};
                if (labelsOrtu.length > 0) {
                    new Chart(ctxOrtu, {
                        type: 'doughnut',
                        data: {
                            labels: labelsOrtu,
                            datasets: [{
                                data: dataOrtu,
                                backgroundColor: getColors(extendedPalette1, labelsOrtu.length),
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '62%',
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 8,
                                        padding: 6,
                                        font: { size: 9.5, weight: '500' },
                                        color: '#3f3f46'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#18181B',
                                    padding: 8,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.parsed + ' Orang'; }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // 3. Chart Suku (Doughnut)
            const ctxSuku = document.getElementById('chartSuku');
            if (ctxSuku) {
                const labelsSuku = {!! json_encode($chartSuku['labels']) !!};
                const dataSuku = {!! json_encode($chartSuku['data']) !!};
                if (labelsSuku.length > 0) {
                    new Chart(ctxSuku, {
                        type: 'doughnut',
                        data: {
                            labels: labelsSuku,
                            datasets: [{
                                data: dataSuku,
                                backgroundColor: getColors(extendedPalette2, labelsSuku.length),
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '62%',
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 8,
                                        padding: 6,
                                        font: { size: 9.5, weight: '500' },
                                        color: '#3f3f46'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#18181B',
                                    padding: 8,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.parsed + ' Orang'; }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // 4. Chart Kota / Provinsi Asal (Horizontal Bar)
            const ctxProv = document.getElementById('chartProvinsi');
            if (ctxProv) {
                const labelsProv = {!! json_encode($chartProvinsi['labels']) !!};
                const dataProv = {!! json_encode($chartProvinsi['data']) !!};
                if (labelsProv.length > 0) {
                    new Chart(ctxProv, {
                        type: 'bar',
                        data: {
                            labels: labelsProv,
                            datasets: [{
                                label: 'Jumlah Trainee',
                                data: dataProv,
                                backgroundColor: '#0284c7',
                                hoverBackgroundColor: '#0369a1',
                                borderRadius: 6,
                                maxBarThickness: 22
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#18181B',
                                    padding: 8,
                                    cornerRadius: 6,
                                    callbacks: {
                                        label: function(ctx) { return ' ' + ctx.parsed.x + ' Trainee'; }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { precision: 0, font: { size: 10 } },
                                    grid: { color: '#F4F4F5' }
                                },
                                y: {
                                    grid: { display: false },
                                    ticks: { font: { size: 10, weight: '600' }, color: '#3f3f46' }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
    @endpush
</x-admin-layout>
