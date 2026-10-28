<x-admin-layout>
    <x-slot:title>
        Tambah Program Baru - CMS
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto" 
         x-data="{ 
            isReguler: {{ old('is_reguler', '0') == '1' ? 'true' : 'false' }}
         }">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('admin.page_settings.index', ['tab' => 'program']) }}" class="hover:text-[#509c38] transition">CMS Halaman</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                    <a href="{{ route('admin.page_settings.index', ['tab' => 'program']) }}" class="hover:text-[#509c38] transition">Program Pelatihan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                    <span class="text-gray-800 font-semibold">Tambah Program Baru</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2.5">
                    <i data-lucide="plus-circle" class="w-6 h-6 text-[#509c38]"></i>
                    Tambah Program Pelatihan
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Buat halaman publik baru untuk program pelatihan (Reguler Trainee atau Standar Non-Reguler).
                </p>
            </div>
            <a href="{{ route('admin.page_settings.index', ['tab' => 'program']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm" role="alert">
                <div class="flex items-center gap-2 text-rose-800 font-semibold text-sm mb-1">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-500"></i>
                    Terdapat kesalahan input:
                </div>
                <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5 ml-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.portal_programs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- 1. PILIH FORMAT TAMPILAN HALAMAN -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 flex items-center gap-2">
                        <i data-lucide="layout-template" class="w-4 h-4 text-[#509c38]"></i>
                        1. Format Tampilan Halaman Program
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Pilih tata letak yang sesuai dengan jenis program pelatihan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Format Standar (Non-Reguler) -->
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                           :class="!isReguler ? 'border-[#509c38] bg-green-50/30 ring-1 ring-[#509c38]' : 'border-gray-200 hover:border-gray-300 bg-white'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold text-gray-800 bg-gray-100">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-teal-800"></i>
                                Format Standar (Non-Reguler)
                            </span>
                            <input type="radio" name="is_reguler" value="0" @change="isReguler = false" :checked="!isReguler" class="w-4 h-4 text-[#509c38] focus:ring-[#509c38]">
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Format ringkas & modern: menampilkan <strong>Deskripsi (Overview)</strong>, <strong>Kurikulum</strong>, <strong>Benefit Program</strong>, dan <strong>Foto/Gambar Kegiatan</strong> (tanpa metode belajar 70:20:10).
                        </p>
                    </label>

                    <!-- Format Reguler (Trainee) -->
                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                           :class="isReguler ? 'border-[#509c38] bg-green-50/30 ring-1 ring-[#509c38]' : 'border-gray-200 hover:border-gray-300 bg-white'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold text-teal-900 bg-teal-100">
                                <i data-lucide="award" class="w-3.5 h-3.5 text-teal-800"></i>
                                Program Reguler (Trainee)
                            </span>
                            <input type="radio" name="is_reguler" value="1" @change="isReguler = true" :checked="isReguler" class="w-4 h-4 text-[#509c38] focus:ring-[#509c38]">
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Format komprehensif untuk calon asisten / pimpinan (FAT, MAT, TAT, dsb.): dilengkapi <strong>Poster Flyer Resmi</strong>, <strong>Badge Trainee</strong>, dan kotak <strong>Metode Belajar 70:20:10</strong>.
                        </p>
                    </label>
                </div>
            </div>

            <!-- 2. INFORMASI UTAMA PROGRAM -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-[#509c38]"></i>
                        2. Informasi Pokok & Deskripsi
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Judul, subjudul, kategori pengelompokan menu, dan narasi deskripsi program.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Judul Program -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Nama / Judul Program <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Training Damkarlabun atau Field Assistant Training" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition font-semibold text-gray-900">
                    </div>

                    <!-- Kode Program (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Kode Singkat (Opsional)
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: DAMKAR, FAT, K3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-800 uppercase">
                    </div>
                </div>

                <!-- Subtitle / Tagline Banner Atas -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Subtitle / Tagline (Teks Banner Atas)
                    </label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Contoh: Pelatihan Penanggulangan Kebakaran Lahan Perkebunan" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-800">
                </div>

                <!-- Kategori / Induk Menu -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Kategori / Induk Menu Program
                    </label>
                    <input type="text" 
                           name="category_name" 
                           list="category_suggestions" 
                           value="{{ old('category_name') }}" 
                           placeholder="Pilih atau ketik nama menu (Contoh: Administrasi, Estate, Mill, Traksi, Managerial, dsb.)" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-900">
                    <datalist id="category_suggestions">
                        <option value="Program Reguler">
                        <option value="Managerial & Leadership">
                        <option value="Estate">
                        <option value="Mill">
                        <option value="Traksi">
                        <option value="Downstream">
                        <option value="Administrasi">
                        <option value="Talent Management">
                        <option value="Kejuruan Teknikal">
                        <option value="Sertifikasi & K3">
                    </datalist>
                    <p class="text-[11px] text-gray-400 mt-1">Digunakan untuk pengelompokan program di katalog dan menu portal.</p>
                </div>

                <!-- Deskripsi Program (Overview) -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Deskripsi Lengkap Program (Overview) <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="overview" rows="5" required placeholder="Tuliskan latar belakang, urgensi, atau narasi umum mengenai program pelatihan ini..." class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed text-gray-800">{{ old('overview') }}</textarea>
                </div>

                <!-- Gambar & Background Hero -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <!-- Upload Gambar / Foto Kegiatan -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Upload Foto Kegiatan / Poster Program
                        </label>
                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 5MB). Foto dokumentasi kegiatan atau poster.</p>
                    </div>

                    <!-- Upload Background Banner Judul Atas -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Upload Background Banner Judul Atas (Opsional)
                        </label>
                        <input type="file" name="hero_image_file" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        <p class="text-[11px] text-gray-400 mt-1">Gambar latar belakang banner di atas halaman detail.</p>
                    </div>
                </div>
            </div>

            <!-- 3. KURIKULUM & BENEFIT PROGRAM -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4 text-[#509c38]"></i>
                        3. Kurikulum & Benefit Program
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Tuliskan 1 baris per poin materi kurikulum dan benefit yang didapatkan peserta.</p>
                </div>

                <!-- Kurikulum -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5 flex items-center justify-between">
                        <span>Kurikulum / Materi Pelatihan</span>
                        <span class="text-[11px] font-normal text-gray-400 lowercase">(1 baris per materi)</span>
                    </label>
                    <textarea name="curriculum_pillars_text" rows="5" placeholder="Landasan hukum dan sanksi&#10;Peraturan presiden, peraturan menteri dan peraturan daerah&#10;Dampak kebakaran&#10;Pengenalan peralatan Damkar&#10;Strategi pemadaman&#10;Administrasi pelaporan titik api (Hotspot)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed">{{ old('curriculum_pillars_text') }}</textarea>
                </div>

                <!-- Benefit -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5 flex items-center justify-between">
                        <span>Benefit / Manfaat Program</span>
                        <span class="text-[11px] font-normal text-gray-400 lowercase">(1 baris per poin benefit)</span>
                    </label>
                    <textarea name="objectives_text" rows="5" placeholder="Menambah knowledge dan skill peserta training dalam penanggulangan kebakaran&#10;Mampu meningkatkan kesadaran akan bahaya kebakaran&#10;Tetap menjaga koordinasi tim damkar yang sudah dibentuk&#10;Meningkatkan kepedulian terhadap maintenance peralatan damkar&#10;Mencegah terjadinya kebakaran lahan perkebunan" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed">{{ old('objectives_text') }}</textarea>
                </div>
            </div>

            <!-- 4. KHUSUS PROGRAM REGULER (TRAINEE) -->
            <div x-show="isReguler" x-transition.opacity class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-teal-900">
                            4. Atribut Khusus Program Reguler (Trainee)
                        </h2>
                        <p class="text-xs text-gray-500">Bagian ini hanya ditampilkan pada halaman detail Program Reguler Trainee.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Rasio Metode Belajar
                        </label>
                        <input type="text" name="method_ratio" value="{{ old('method_ratio', '70 : 20 : 10') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-teal-800 focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Keterangan Metode Belajar
                        </label>
                        <input type="text" name="method_desc" value="{{ old('method_desc', '70% experiential learning, 20% social learning, dan 10% formal learning') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Badge Khusus
                        </label>
                        <input type="text" name="badge" value="{{ old('badge') }}" placeholder="Contoh: Agronomi & Lapangan Kebun" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Target Posisi / Peran
                        </label>
                        <input type="text" name="target_role" value="{{ old('target_role') }}" placeholder="Contoh: Calon Asisten Kebun" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Durasi Program
                        </label>
                        <input type="text" name="duration" value="{{ old('duration') }}" placeholder="Contoh: 6 Bulan Terjadwal" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.page_settings.index', ['tab' => 'program']) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="bg-[#509c38] hover:bg-green-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 text-xs sm:text-sm">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan Program Baru
                </button>
            </div>
        </form>

    </div>
</x-admin-layout>