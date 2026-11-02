<x-admin-layout>
    <x-slot:title>
        Edit Program {{ $portal_program->title }} - CMS
    </x-slot>

    @php
        $rawCurriculum = is_array($portal_program->curriculum_pillars) && count($portal_program->curriculum_pillars) > 0 
            ? $portal_program->curriculum_pillars 
            : (old('curriculum_pillars_text') ? array_filter(array_map('trim', explode("\n", old('curriculum_pillars_text')))) : []);
        $curriculumArray = count($rawCurriculum) > 0 ? array_values($rawCurriculum) : [''];

        $rawObjectives = is_array($portal_program->objectives) && count($portal_program->objectives) > 0 
            ? $portal_program->objectives 
            : (old('objectives_text') ? array_filter(array_map('trim', explode("\n", old('objectives_text')))) : []);
        $objectivesArray = count($rawObjectives) > 0 ? array_values($rawObjectives) : [''];

        $methodRatio = old('method_ratio', $portal_program->learning_method['ratio'] ?? '70 : 20 : 10');
        $methodDesc = old('method_desc', $portal_program->learning_method['desc'] ?? '70% experiential learning, 20% social learning, dan 10% formal learning');
        $isRegulerDefault = old('is_reguler', $portal_program->is_reguler ? '1' : '0') == '1';
    @endphp

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto"
         x-data="{ 
            isReguler: {{ $isRegulerDefault ? 'true' : 'false' }},
            curriculumList: {{ json_encode($curriculumArray) }},
            benefitList: {{ json_encode($objectivesArray) }},
            addCurriculum() {
                this.curriculumList.push('');
            },
            removeCurriculum(idx) {
                if (this.curriculumList.length > 1) {
                    this.curriculumList.splice(idx, 1);
                } else {
                    this.curriculumList = [''];
                }
            },
            addBenefit() {
                this.benefitList.push('');
            },
            removeBenefit(idx) {
                if (this.benefitList.length > 1) {
                    this.benefitList.splice(idx, 1);
                } else {
                    this.benefitList = [''];
                }
            }
         }">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="hover:text-[#509c38] transition">CMS Halaman</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                    <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="hover:text-[#509c38] transition">Program Pelatihan</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                    <span class="text-gray-800 font-semibold">{{ $portal_program->title }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2.5">
                    <i data-lucide="edit-3" class="w-6 h-6 text-[#509c38]"></i>
                    Edit Program: {{ $portal_program->title }}
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Sesuaikan informasi judul, kategori menu, kurikulum, manfaat, dan foto program pelatihan.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ url('/program/' . $portal_program->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:text-[#509c38] hover:bg-gray-50 shadow-xs transition">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Lihat Web
                </a>
                <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-xs transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
            </div>
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

        <form action="{{ route('admin.portal_programs.update', $portal_program) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="slug" value="{{ $portal_program->slug }}">

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
                        <input type="text" name="title" value="{{ old('title', $portal_program->title) }}" required placeholder="Contoh: Field Assistant Training (FAT)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition font-semibold text-gray-900">
                    </div>

                    <!-- Subtitle / Tagline -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Tagline / Sub-Judul (Opsional)
                        </label>
                        <input type="text" name="subtitle" value="{{ old('subtitle', $portal_program->subtitle) }}" placeholder="Contoh: Agronomi & Budidaya Perkebunan Kelapa Sawit" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition text-gray-800">
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
                           value="{{ old('category_name', $portal_program->category_name ?? '') }}" 
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
                    <textarea name="overview" rows="4" required placeholder="Tuliskan latar belakang, urgensi, atau narasi umum mengenai program pelatihan ini..." class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition leading-relaxed text-gray-800">{{ old('overview', $portal_program->overview) }}</textarea>
                </div>

                <!-- Upload Foto / Poster & Gambar Background Hero -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <!-- 1. Foto / Poster / Gambar Program -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Foto / Poster / Gambar Program
                        </label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-gray-200 bg-gray-50/60 h-full">
                            @if($portal_program->image)
                                <img src="{{ $portal_program->getResolvedImageUrl() }}" alt="{{ $portal_program->title }}" class="w-20 h-20 object-cover rounded-xl bg-white border border-gray-200 shadow-xs shrink-0">
                            @endif
                            <div class="flex-1 w-full">
                                <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                <p class="text-[11px] text-gray-400 mt-1.5">
                                    @if($portal_program->image)
                                        Pilih file baru jika ingin mengganti poster saat ini (JPG, PNG, WEBP maks 5MB).
                                    @else
                                        Unggah foto poster kegiatan atau flyer program (JPG, PNG, WEBP maks 5MB).
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Gambar Background Hero (Banner Atas) -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                            Gambar Background Hero (Banner Atas)
                        </label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-gray-200 bg-gray-50/60 h-full">
                            @if($portal_program->hero_image)
                                <img src="{{ $portal_program->getResolvedHeroImageUrl() }}" alt="{{ $portal_program->title }} Hero" class="w-28 h-20 object-cover rounded-xl bg-white border border-gray-200 shadow-xs shrink-0">
                            @endif
                            <div class="flex-1 w-full">
                                <input type="file" name="hero_image_file" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                <p class="text-[11px] text-gray-400 mt-1.5">
                                    @if($portal_program->hero_image)
                                        Pilih file baru jika ingin mengganti background banner hero atas (JPG, PNG, WEBP maks 5MB).
                                    @else
                                        Unggah gambar latar banner hero di bagian atas halaman program (JPG, PNG, WEBP maks 5MB).
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. KURIKULUM & BENEFIT PROGRAM -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-7 space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4 text-[#509c38]"></i>
                        2. Kurikulum & Manfaat Program
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Kelola pembagian poin materi kurikulum dan poin manfaat program secara terstruktur.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <!-- Kurikulum -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase text-gray-700">
                                Kurikulum / Materi Pelatihan
                            </label>
                            <span class="text-[11px] font-semibold text-[#114045] bg-teal-50 px-2 py-0.5 rounded-md" x-text="curriculumList.filter(i => i.trim() !== '').length + ' Poin'"></span>
                        </div>
                        
                        <div class="space-y-2.5">
                            <template x-for="(item, idx) in curriculumList" :key="'curr_' + idx">
                                <div class="flex items-start gap-2 p-2 rounded-xl bg-gray-50/80 border border-gray-200 focus-within:border-[#509c38] focus-within:bg-white transition-all">
                                    <span class="w-6 h-6 rounded-lg bg-[#114045] text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1" x-text="idx + 1"></span>
                                    <textarea x-model="curriculumList[idx]" rows="2" placeholder="Tuliskan poin materi kurikulum..." class="flex-1 bg-transparent border-0 p-1 text-xs text-gray-800 focus:ring-0 resize-none leading-relaxed outline-none"></textarea>
                                    <button type="button" @click="removeCurriculum(idx)" class="p-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition shrink-0 mt-1" title="Hapus poin">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            
                            <button type="button" @click="addCurriculum()" class="w-full py-2.5 px-3 border border-dashed border-[#509c38]/40 hover:border-[#509c38] bg-green-50/40 hover:bg-green-50 text-[#1d5732] hover:text-[#509c38] rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                + Tambah Poin Kurikulum
                            </button>
                        </div>

                        <!-- Hidden textarea synced with curriculumList for standard form submission -->
                        <textarea name="curriculum_pillars_text" :value="curriculumList.filter(i => i.trim() !== '').join('\n')" class="hidden"></textarea>
                    </div>

                    <!-- Benefit / Objectives -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase text-gray-700">
                                Manfaat / Benefit Program
                            </label>
                            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md" x-text="benefitList.filter(i => i.trim() !== '').length + ' Poin'"></span>
                        </div>

                        <div class="space-y-2.5">
                            <template x-for="(item, idx) in benefitList" :key="'ben_' + idx">
                                <div class="flex items-start gap-2 p-2 rounded-xl bg-gray-50/80 border border-gray-200 focus-within:border-[#509c38] focus-within:bg-white transition-all">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white text-xs font-bold flex items-center justify-center shrink-0 mt-1" x-text="idx + 1"></span>
                                    <textarea x-model="benefitList[idx]" rows="2" placeholder="Tuliskan poin manfaat / benefit..." class="flex-1 bg-transparent border-0 p-1 text-xs text-gray-800 focus:ring-0 resize-none leading-relaxed outline-none"></textarea>
                                    <button type="button" @click="removeBenefit(idx)" class="p-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition shrink-0 mt-1" title="Hapus poin">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            
                            <button type="button" @click="addBenefit()" class="w-full py-2.5 px-3 border border-dashed border-[#509c38]/40 hover:border-[#509c38] bg-green-50/40 hover:bg-green-50 text-[#1d5732] hover:text-[#509c38] rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                + Tambah Poin Manfaat
                            </button>
                        </div>

                        <!-- Hidden textarea synced with benefitList for standard form submission -->
                        <textarea name="objectives_text" :value="benefitList.filter(i => i.trim() !== '').join('\n')" class="hidden"></textarea>
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
                            <input type="text" name="method_ratio" value="{{ $methodRatio }}" placeholder="70 : 20 : 10" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-bold text-teal-800 focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Keterangan Metode Belajar
                            </label>
                            <input type="text" name="method_desc" value="{{ $methodDesc }}" placeholder="70% experiential learning, 20% social learning, dan 10% formal learning" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Badge Khusus (Opsional)
                            </label>
                            <input type="text" name="badge" value="{{ old('badge', $portal_program->badge) }}" placeholder="Contoh: Agronomi & Kebun" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Target Posisi / Peran (Opsional)
                            </label>
                            <input type="text" name="target_role" value="{{ old('target_role', $portal_program->target_role) }}" placeholder="Contoh: Calon Asisten Kebun" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                                Durasi Program (Opsional)
                            </label>
                            <input type="text" name="duration" value="{{ old('duration', $portal_program->duration) }}" placeholder="Contoh: 6 Bulan Terjadwal" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="flex items-center justify-between gap-3 pt-2">
                <a href="{{ route('admin.page_settings.index', ['tab' => 'menu_program']) }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-[#509c38] hover:bg-green-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 text-xs sm:text-sm">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

    </div>
</x-admin-layout>
