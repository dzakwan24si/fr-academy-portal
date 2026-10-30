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
                    <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="hover:text-[#509c38] transition">CMS Halaman</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                    <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="hover:text-[#509c38] transition">Program Pelatihan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                    <span class="text-gray-800 font-semibold">Tambah Program Baru</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2.5">
                    <i data-lucide="plus-circle" class="w-6 h-6 text-[#509c38]"></i>
                    Tambah Program Pelatihan
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Buat halaman publik dan entri menu baru untuk program pelatihan.
                </p>
            </div>
            <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-xs" role="alert">
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

            <!-- 1. INFORMASI PROGRAM -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-7 space-y-5">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-[#509c38]"></i>
                        1. Informasi Program
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Nama program, kategori menu navigasi, dan narasi deskripsi program.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Judul Program -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Nama / Judul Program <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Training Damkarlabun atau FAT" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition font-semibold text-gray-900">
                    </div>

                    <!-- Subtitle / Tagline -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Tagline / Sub-Judul (Opsional)
                        </label>
                        <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Contoh: Pelatihan Penanggulangan Kebakaran Lahan" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-800">
                    </div>
                </div>

                <!-- Kategori / Induk Menu -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Kategori / Menu Navigasi Program <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="category_name" 
                           list="category_suggestions" 
                           required
                           value="{{ old('category_name', $prefilledCategory ?? '') }}" 
                           placeholder="Pilih kategori yang sudah ada atau ketik nama menu baru..." 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-900 font-medium">
                    <datalist id="category_suggestions">
                        @if(!empty($categories))
                            @foreach($categories as $catName)
                                <option value="{{ $catName }}">
                            @endforeach
                        @else
                            <option value="Program Reguler">
                            <option value="Managerial & Leadership">
                            <option value="Estate">
                            <option value="Mill">
                            <option value="Traksi">
                            <option value="Downstream">
                            <option value="Administrasi">
                            <option value="Talent Management">
                        @endif
                    </datalist>
                    <p class="text-[11px] text-gray-400 mt-1.5">
                        Pilih menu yang ada agar otomatis masuk sebagai sub-menu, atau ketik nama baru untuk membuat menu baru di navbar.
                    </p>
                </div>

                <!-- Deskripsi Program (Overview) -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Deskripsi Lengkap Program <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="overview" rows="4" required placeholder="Tuliskan latar belakang, urgensi, atau narasi umum mengenai program pelatihan ini..." class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed text-gray-800">{{ old('overview') }}</textarea>
                </div>

                <!-- Upload Foto / Gambar Program -->
                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Foto / Poster / Gambar Program
                    </label>
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/60">
                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                        <p class="text-[11px] text-gray-400 mt-1.5">
                            Unggah foto poster kegiatan atau flyer program (JPG, PNG, WEBP maks 5MB).
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. KURIKULUM & BENEFIT PROGRAM -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-7 space-y-5">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4 text-[#509c38]"></i>
                        2. Kurikulum & Manfaat Program
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Tuliskan 1 baris per materi atau per poin manfaat yang diperoleh peserta.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Kurikulum -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5 flex items-center justify-between">
                            <span>Kurikulum / Materi Pelatihan</span>
                            <span class="text-[11px] font-normal text-gray-400 lowercase">(1 materi per baris)</span>
                        </label>
                        <textarea name="curriculum_pillars_text" rows="6" placeholder="Contoh:&#10;Landasan Hukum & Regulasi&#10;Pengenalan Peralatan Kerja&#10;Teknik Operasional & Simulasi" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed">{{ old('curriculum_pillars_text') }}</textarea>
                    </div>

                    <!-- Benefit -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5 flex items-center justify-between">
                            <span>Manfaat / Benefit Program</span>
                            <span class="text-[11px] font-normal text-gray-400 lowercase">(1 poin per baris)</span>
                        </label>
                        <textarea name="objectives_text" rows="6" placeholder="Contoh:&#10;Meningkatkan kompetensi teknis tim&#10;Mencegah risiko insiden operasional&#10;Mendapatkan sertifikat kompetensi" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed">{{ old('objectives_text') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. METODE BELAJAR 70:20:10 & ATRIBUT TAMBAHAN (OPSIONAL) -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-7 space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-[#509c38]"></i>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900">
                            3. Metode Belajar & Atribut Tambahan (Opsional)
                        </h2>
                    </div>
                </div>

                <!-- Checkbox Gunakan Metode Belajar -->
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white transition">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="is_reguler" 
                               value="1" 
                               x-model="isReguler" 
                               class="w-4 h-4 mt-0.5 text-[#509c38] rounded border-gray-300 focus:ring-[#509c38]">
                        <div>
                            <span class="text-sm font-bold text-gray-900 block">Tampilkan Kotak Metode Belajar (70 : 20 : 10)</span>
                            <span class="text-xs text-gray-500 mt-0.5 block leading-relaxed">
                                Centang jika program ini menerapkan metode belajar 70:20:10 (seperti pada program Trainee).
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Input Metode Belajar & Atribut (Hanya Tampil Jika Checkbox Dicentang) -->
                <div x-show="isReguler" x-transition.opacity class="space-y-4 pt-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Rasio Metode Belajar
                            </label>
                            <input type="text" name="method_ratio" value="{{ old('method_ratio', '70 : 20 : 10') }}" placeholder="70 : 20 : 10" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-teal-800 focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Keterangan Metode Belajar
                            </label>
                            <input type="text" name="method_desc" value="{{ old('method_desc', '70% experiential learning, 20% social learning, dan 10% formal learning') }}" placeholder="70% experiential learning, 20% social learning, dan 10% formal learning" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Badge Khusus (Opsional)
                            </label>
                            <input type="text" name="badge" value="{{ old('badge') }}" placeholder="Contoh: Agronomi & Kebun" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Target Posisi / Peran (Opsional)
                            </label>
                            <input type="text" name="target_role" value="{{ old('target_role') }}" placeholder="Contoh: Calon Asisten Kebun" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Durasi Program (Opsional)
                            </label>
                            <input type="text" name="duration" value="{{ old('duration') }}" placeholder="Contoh: 6 Bulan Terjadwal" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex items-center justify-between gap-3 pt-2">
                <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
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
