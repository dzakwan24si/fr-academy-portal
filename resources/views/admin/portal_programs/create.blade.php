<x-admin-layout>
    <x-slot:title>
        Tambah Program Baru - CMS
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto" 
         x-data="{ 
            hasExtra: {{ old('has_extra') ? 'true' : 'false' }}
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
                    Tambah Program Baru
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Buat halaman publik baru untuk program pelatihan apa saja di FR Academy.
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
                    Terdapat kesalahan:
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

            <!-- 1. KONTEN UTAMA PROGRAM -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <!-- Judul Program -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Nama / Judul Program <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Field Assistant Training (FAT) atau Sertifikasi Mandor" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition font-semibold text-gray-900">
                </div>

                <!-- Subtitle / Tagline Banner Atas -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Subtitle / Tagline (Teks Banner Atas)
                    </label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="Contoh: Program Pengembangan Keahlian & Karakter Insan First Resources" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-800">
                </div>

                <!-- Kategori / Tipe Program (Bebas / Fleksibel) -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Kategori / Tipe Program
                    </label>
                    <input type="text" 
                           name="category_name" 
                           list="category_suggestions" 
                           value="{{ old('category_name') }}" 
                           placeholder="Pilih atau ketik tipe program baru (contoh: Program Trainee, Kejuruan, Sertifikasi, dll.)" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-900">
                    <datalist id="category_suggestions">
                        <option value="Program Trainee">
                        <option value="Kejuruan Teknikal">
                        <option value="Managerial & Leadership">
                        <option value="Talent Management">
                        <option value="Sertifikasi & K3">
                        <option value="Workshop & Seminar">
                    </datalist>
                    <p class="text-[11px] text-gray-400 mt-1">Bebas memilih dari daftar atau mengetik tipe baru apa saja sesuai kebutuhan.</p>
                </div>

                <!-- Deskripsi Program -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Deskripsi Program <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="overview" rows="5" required placeholder="Tuliskan teks narasi deskripsi program..." class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed text-gray-800">{{ old('overview') }}</textarea>
                </div>

                <!-- Gambar Program -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Upload Gambar / Poster Program
                    </label>
                    <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 5MB).</p>
                </div>

                <!-- Background Banner Judul Atas -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                        Upload Background Banner Judul Atas (Opsional)
                    </label>
                    <input type="file" name="hero_image_file" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    <p class="text-[11px] text-gray-400 mt-1">Gambar latar belakang di belakang judul atas. Kosongkan untuk menggunakan background default.</p>
                </div>
            </div>

            <!-- 2. RINCIAN TAMBAHAN (OPSIONAL — KURIKULUM & OBJECTIVE) -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Rincian Kurikulum & Benefit (Opsional)</h3>
                        <p class="text-xs text-gray-500">Aktifkan hanya jika program ini memiliki rincian poin kurikulum, benefit program, atau poster khusus.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_reguler" value="1" x-model="hasExtra" class="rounded border-gray-300 text-[#509c38] focus:ring-[#509c38] w-4 h-4">
                        <span class="text-xs font-semibold text-gray-700">Sertakan Rincian</span>
                    </label>
                </div>

                <div x-show="hasExtra" x-transition.opacity class="space-y-5 pt-4">
                    <!-- Kurikulum -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            [KURIKULUM] (1 baris per materi)
                        </label>
                        <textarea name="curriculum_pillars_text" rows="3" placeholder="Materi Kompetensi Umum&#10;Materi Kompetensi Teknis&#10;Materi Kompetensi Pendukung" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed">{{ old('curriculum_pillars_text', "Materi Kompetensi Umum\nMateri Kompetensi Teknis\nMateri Kompetensi Pendukung") }}</textarea>
                    </div>

                    <!-- Benefit -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            [BENEFIT] (1 baris per poin benefit)
                        </label>
                        <textarea name="objectives_text" rows="4" placeholder="Tuliskan poin benefit (1 baris per poin)..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed">{{ old('objectives_text') }}</textarea>
                    </div>

                    <!-- Metode Belajar -->
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
