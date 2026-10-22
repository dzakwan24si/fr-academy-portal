<x-admin-layout>
    <x-slot:title>
        Pengaturan Konten Halaman
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2.5">
                    <i data-lucide="layout-template" class="w-7 h-7 text-[#509c38]"></i>
                    Pengaturan Konten Halaman (CMS)
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola teks hero, foto slider, visi misi, dan narasi profil halaman publik secara dinamis.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 hover:text-[#509c38] transition">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Buka Beranda
                </a>
                <a href="/tentang-kami" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 hover:text-[#509c38] transition">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Buka Tentang Kami
                </a>
                <a href="/career" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 hover:text-[#509c38] transition">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Buka Karir
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-[#509c38] p-4 rounded-r-xl shadow-sm flex items-center gap-3" role="alert">
                <i data-lucide="check-circle" class="w-5 h-5 text-[#509c38] shrink-0"></i>
                <p class="text-emerald-900 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm" role="alert">
                <div class="flex items-center gap-2 text-rose-800 font-semibold text-sm mb-1">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-500"></i>
                    Terdapat kesalahan dalam pengisian formulir:
                </div>
                <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5 ml-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 mb-8 space-x-8 overflow-x-auto">
            <a href="{{ route('admin.page_settings.index', ['tab' => 'beranda']) }}" 
               class="pb-4 px-2 border-b-2 font-semibold text-sm flex items-center gap-2.5 transition-all whitespace-nowrap {{ $tab === 'beranda' ? 'border-[#509c38] text-[#509c38]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i data-lucide="home" class="w-4 h-4"></i>
                Halaman Beranda (Home)
            </a>
            <a href="{{ route('admin.page_settings.index', ['tab' => 'tentang']) }}" 
               class="pb-4 px-2 border-b-2 font-semibold text-sm flex items-center gap-2.5 transition-all whitespace-nowrap {{ $tab === 'tentang' ? 'border-[#509c38] text-[#509c38]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i data-lucide="info" class="w-4 h-4"></i>
                Halaman Tentang Kami (About Us)
            </a>
            <a href="{{ route('admin.page_settings.index', ['tab' => 'portal']) }}" 
               class="pb-4 px-2 border-b-2 font-semibold text-sm flex items-center gap-2.5 transition-all whitespace-nowrap {{ $tab === 'portal' ? 'border-[#509c38] text-[#509c38]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Menu Portal (Header & Dropdown)
            </a>
            <a href="{{ route('admin.page_settings.index', ['tab' => 'karir']) }}" 
               class="pb-4 px-2 border-b-2 font-semibold text-sm flex items-center gap-2.5 transition-all whitespace-nowrap {{ $tab === 'karir' ? 'border-[#509c38] text-[#509c38]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i data-lucide="briefcase" class="w-4 h-4"></i>
                Program Karir (Career)
            </a>
        </div>

        @if($tab === 'beranda')
            <!-- TAB 1: BERANDA FORM -->
            <form action="{{ route('admin.page_settings.update_beranda') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Card 1: Hero Section & Slider -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold">
                            <i data-lucide="tv" class="w-5 h-5 text-[#509c38]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Hero Section & Slider Foto</h2>
                            <p class="text-xs text-gray-500">Teks utama dan gambar latar belakang yang pertama kali dilihat pengunjung.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Badge Kecil</label>
                            <input type="text" name="home_hero_badge" value="{{ old('home_hero_badge', $beranda['home_hero_badge']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Slogan / Tagline Kutipan</label>
                            <input type="text" name="home_hero_tagline" value="{{ old('home_hero_tagline', $beranda['home_hero_tagline']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Judul Utama (Headline) <span class="text-rose-500">*</span></label>
                            <input type="text" name="home_hero_title" required value="{{ old('home_hero_title', $beranda['home_hero_title']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Deskripsi Singkat Hero <span class="text-rose-500">*</span></label>
                            <textarea name="home_hero_desc" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">{{ old('home_hero_desc', $beranda['home_hero_desc']) }}</textarea>
                        </div>

                        <!-- CTA 1 -->
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Tombol 1 - Teks</label>
                            <input type="text" name="home_hero_btn1_text" value="{{ old('home_hero_btn1_text', $beranda['home_hero_btn1_text']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Tombol 1 - Link URL</label>
                            <input type="text" name="home_hero_btn1_url" value="{{ old('home_hero_btn1_url', $beranda['home_hero_btn1_url']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <!-- CTA 2 -->
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Tombol 2 - Teks</label>
                            <input type="text" name="home_hero_btn2_text" value="{{ old('home_hero_btn2_text', $beranda['home_hero_btn2_text']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Tombol 2 - Link URL</label>
                            <input type="text" name="home_hero_btn2_url" value="{{ old('home_hero_btn2_url', $beranda['home_hero_btn2_url']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>
                    </div>

                    <!-- Slide Images Section -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <i data-lucide="images" class="w-4 h-4 text-[#509c38]"></i>
                                    Foto Slider Hero (Dinamis - Tanpa Batas Jumlah)
                                </h3>
                                <p class="text-xs text-gray-500">
                                    Total saat ini: <strong class="text-gray-900">{{ count($slides) }} Slide</strong>. Anda bisa menambah slide sebanyak mungkin, mengganti foto tertentu, atau menghapus slide.
                                </p>
                            </div>
                            <button type="submit" 
                                    formaction="{{ route('admin.page_settings.reset_slides') }}" 
                                    formmethod="POST" 
                                    onclick="return confirm('Kembalikan slider ke 4 foto standar bawaan sistem?')"
                                    class="text-xs text-gray-500 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1.5">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                Reset ke 4 Slide Bawaan
                            </button>
                        </div>

                        <!-- Current Slides Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            @foreach($slides as $slideItem)
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 flex flex-col relative group">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-gray-700">Slide #{{ $loop->iteration }}</span>
                                        @if(count($slides) > 1)
                                            <button type="submit" 
                                                    formaction="{{ route('admin.page_settings.delete_slide') }}" 
                                                    formmethod="POST" 
                                                    name="index" 
                                                    value="{{ $slideItem['index'] }}" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus Slide #{{ $loop->iteration }}?')"
                                                    class="text-rose-500 hover:text-rose-700 text-xs font-semibold flex items-center gap-1 bg-rose-50 hover:bg-rose-100 px-2 py-0.5 rounded-lg transition"
                                                    title="Hapus Slide">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                    <div class="w-full h-32 rounded-lg overflow-hidden bg-gray-200 mb-3 border border-gray-300">
                                        <img src="{{ $slideItem['url'] }}" alt="Slide {{ $loop->iteration }}" class="w-full h-full object-cover">
                                    </div>
                                    <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Foto Ini:</label>
                                    <input type="file" name="replace_slides[{{ $slideItem['index'] }}]" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-[#509c38] hover:file:text-white transition">
                                </div>
                            @endforeach
                        </div>

                        <!-- Dropzone Tambah Slide Baru -->
                        <div class="p-5 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50/40">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div>
                                    <h4 class="text-sm font-bold text-emerald-950 flex items-center gap-2">
                                        <i data-lucide="plus-circle" class="w-4 h-4 text-[#509c38]"></i>
                                        Tambah Slide Baru (Bisa Lebih Dari 1 Sekaligus)
                                    </h4>
                                    <p class="text-xs text-emerald-800/80 mt-0.5">
                                        Pilih 1 atau beberapa file foto (JPG/PNG/WEBP). Foto-foto baru akan otomatis ditambahkan ke daftar slide setelah tombol simpan ditekan.
                                    </p>
                                </div>
                                <div>
                                    <input type="file" name="new_slides[]" multiple accept="image/*" class="text-xs text-emerald-900 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#509c38] file:text-white hover:file:bg-green-600 transition shadow-sm cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Visi & Misi Section -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold">
                            <i data-lucide="target" class="w-5 h-5 text-[#509c38]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Visi & Keunggulan FR Academy</h2>
                            <p class="text-xs text-gray-500">Konten narasi visi, pilar keunggulan, serta 2 foto dokumentasi terkait.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Badge Kecil</label>
                            <input type="text" name="home_vision_badge" value="{{ old('home_vision_badge', $beranda['home_vision_badge']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Judul Visi <span class="text-rose-500">*</span></label>
                            <input type="text" name="home_vision_title" required value="{{ old('home_vision_title', $beranda['home_vision_title']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Deskripsi Lengkap Visi <span class="text-rose-500">*</span></label>
                            <textarea name="home_vision_desc" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">{{ old('home_vision_desc', $beranda['home_vision_desc']) }}</textarea>
                        </div>

                        <!-- Keunggulan 1 -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 md:col-span-2 space-y-3">
                            <h3 class="text-xs font-bold text-[#509c38] uppercase">Poin Keunggulan #1</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Poin 1</label>
                                    <input type="text" name="home_vision_p1_title" value="{{ old('home_vision_p1_title', $beranda['home_vision_p1_title']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan Poin 1</label>
                                    <input type="text" name="home_vision_p1_desc" value="{{ old('home_vision_p1_desc', $beranda['home_vision_p1_desc']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                            </div>
                        </div>

                        <!-- Keunggulan 2 -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 md:col-span-2 space-y-3">
                            <h3 class="text-xs font-bold text-[#509c38] uppercase">Poin Keunggulan #2</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Poin 2</label>
                                    <input type="text" name="home_vision_p2_title" value="{{ old('home_vision_p2_title', $beranda['home_vision_p2_title']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan Poin 2</label>
                                    <input type="text" name="home_vision_p2_desc" value="{{ old('home_vision_p2_desc', $beranda['home_vision_p2_desc']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                            </div>
                        </div>

                        <!-- 2 Foto Visi -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 flex flex-col">
                            <span class="text-xs font-bold text-gray-700 mb-2">Foto Dokumentasi Visi 1 (Kiri/Bawah)</span>
                            <div class="w-full h-36 rounded-lg overflow-hidden bg-gray-200 mb-3 border border-gray-300">
                                <img src="{{ $beranda['home_vision_img_1'] }}" alt="Vision 1" class="w-full h-full object-cover">
                            </div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Foto 1:</label>
                            <input type="file" name="home_vision_img_1" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#509c38] file:text-white hover:file:bg-green-600 transition">
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80 flex flex-col">
                            <span class="text-xs font-bold text-gray-700 mb-2">Foto Dokumentasi Visi 2 (Kanan/Atas)</span>
                            <div class="w-full h-36 rounded-lg overflow-hidden bg-gray-200 mb-3 border border-gray-300">
                                <img src="{{ $beranda['home_vision_img_2'] }}" alt="Vision 2" class="w-full h-full object-cover">
                            </div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Foto 2:</label>
                            <input type="file" name="home_vision_img_2" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#509c38] file:text-white hover:file:bg-green-600 transition">
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-end gap-4 sticky bottom-4 bg-white/95 backdrop-blur-md p-4 rounded-2xl border border-gray-200 shadow-lg z-30">
                    <button type="submit" class="bg-[#509c38] hover:bg-green-600 text-white font-bold px-6 py-3 rounded-xl shadow-md transition-all flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan Beranda
                    </button>
                </div>
            </form>

        @elseif($tab === 'tentang')
            <!-- TAB 2: TENTANG KAMI FORM -->
            <form action="{{ route('admin.page_settings.update_tentang') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Card 1: Hero Header -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold">
                            <i data-lucide="flag" class="w-5 h-5 text-[#509c38]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Hero Header & Latar Belakang</h2>
                            <p class="text-xs text-gray-500">Judul besar bagian atas halaman Tentang Kami beserta 2 paragraf pengantar dan foto latar.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Badge Kecil</label>
                            <input type="text" name="about_hero_badge" value="{{ old('about_hero_badge', $tentang['about_hero_badge']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Judul Besar Hero <span class="text-rose-500">*</span></label>
                            <input type="text" name="about_hero_title" required value="{{ old('about_hero_title', $tentang['about_hero_title']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Pengantar Kolom Kiri <span class="text-rose-500">*</span></label>
                            <textarea name="about_hero_col1" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">{{ old('about_hero_col1', $tentang['about_hero_col1']) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Pengantar Kolom Kanan <span class="text-rose-500">*</span></label>
                            <textarea name="about_hero_col2" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">{{ old('about_hero_col2', $tentang['about_hero_col2']) }}</textarea>
                        </div>

                        <!-- Foto Background Hero -->
                        <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200/80 flex flex-col sm:flex-row items-center gap-6">
                            <div class="w-full sm:w-48 h-28 rounded-lg overflow-hidden bg-gray-200 shrink-0 border border-gray-300">
                                <img src="{{ $tentang['about_hero_bg'] }}" alt="Hero Background" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow w-full">
                                <span class="text-xs font-bold text-gray-700 block mb-1">Foto Latar Belakang Hero (Background)</span>
                                <p class="text-xs text-gray-500 mb-3">Foto ini akan tampil dengan aksen warna dark teal di bagian atas halaman Tentang Kami.</p>
                                <input type="file" name="about_hero_bg" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#509c38] file:text-white hover:file:bg-green-600 transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Profil & Misi -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold">
                            <i data-lucide="book-open" class="w-5 h-5 text-[#509c38]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Profil & Misi ([ 01 / PROFIL & MISI ])</h2>
                            <p class="text-xs text-gray-500">Narasi komprehensif profil FR Academy serta foto dokumentasi kegiatan perkebunan lebar.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Badge Section</label>
                            <input type="text" name="about_profile_badge" value="{{ old('about_profile_badge', $tentang['about_profile_badge']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Judul Profil <span class="text-rose-500">*</span></label>
                            <input type="text" name="about_profile_title" required value="{{ old('about_profile_title', $tentang['about_profile_title']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Narasi Lengkap Profil <span class="text-rose-500">*</span></label>
                            <textarea name="about_profile_desc" rows="4" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">{{ old('about_profile_desc', $tentang['about_profile_desc']) }}</textarea>
                        </div>

                        <!-- Foto Dokumentasi Lebar -->
                        <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200/80">
                            <span class="text-xs font-bold text-gray-700 block mb-2">Foto Dokumentasi Lebar</span>
                            <div class="w-full h-48 rounded-lg overflow-hidden bg-gray-200 mb-4 border border-gray-300">
                                <img src="{{ $tentang['about_profile_img'] }}" alt="Profile Documentation" class="w-full h-full object-cover">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Keterangan / Caption Foto</label>
                                    <input type="text" name="about_profile_img_caption" value="{{ old('about_profile_img_caption', $tentang['about_profile_img_caption']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Teks Lokasi Foto</label>
                                    <input type="text" name="about_profile_img_location" value="{{ old('about_profile_img_location', $tentang['about_profile_img_location']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                            </div>

                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Foto Dokumentasi:</label>
                            <input type="file" name="about_profile_img" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#509c38] file:text-white hover:file:bg-green-600 transition">
                        </div>
                    </div>
                </div>

                <!-- Card 3: Filosofi & Skala -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold">
                            <i data-lucide="compass" class="w-5 h-5 text-[#509c38]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Filosofi & Stat Headline ([ 02 / FILOSOFI & SKALA ])</h2>
                            <p class="text-xs text-gray-500">Pernyataan komitmen filosofi pelatihan.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Badge Section</label>
                            <input type="text" name="about_philosophy_badge" value="{{ old('about_philosophy_badge', $tentang['about_philosophy_badge']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Pernyataan Filosofi (Center Headline) <span class="text-rose-500">*</span></label>
                            <textarea name="about_philosophy_title" rows="3" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">{{ old('about_philosophy_title', $tentang['about_philosophy_title']) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center justify-end gap-4 sticky bottom-4 bg-white/95 backdrop-blur-md p-4 rounded-2xl border border-gray-200 shadow-lg z-30">
                    <button type="submit" class="bg-[#509c38] hover:bg-green-600 text-white font-bold px-6 py-3 rounded-xl shadow-md transition-all flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan Tentang Kami
                    </button>
                </div>
            </form>
        @elseif($tab === 'portal')
            <!-- TAB 3: MENU PORTAL (HEADER DROPDOWN) -->
            <div x-data="{
                editModalOpen: false,
                currentLink: {
                    id: '',
                    name: '',
                    url: '',
                    target: '_blank',
                    order: 1,
                    is_active: true
                },
                openEdit(item) {
                    this.currentLink = {
                        id: item.id,
                        name: item.name,
                        url: item.url,
                        target: item.target || '_blank',
                        order: item.order || 1,
                        is_active: Boolean(item.is_active)
                    };
                    this.editModalOpen = true;
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                }
            }" class="space-y-8">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Left Column: Daftar Website Terhubung (Col 7) -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 flex items-center gap-2">
                                        <i data-lucide="list" class="w-5 h-5 text-[#509c38]"></i>
                                        Daftar Sistem &amp; Website di Portal
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Kelola link, ubah urutan, atau aktifkan/nonaktifkan sistem.</p>
                                </div>

                                <form action="{{ route('admin.page_settings.reset_portal_links') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset daftar menu portal kembali ke 3 sistem awal (E-Learning, Mess, dan Peta SDM)?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-rose-600 bg-gray-50 hover:bg-rose-50 px-3 py-1.5 rounded-lg border border-gray-200 transition">
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                        Reset Bawaan
                                    </button>
                                </form>
                            </div>

                            @if(empty($portalLinks))
                                <div class="text-center py-12 px-4 border-2 border-dashed border-gray-200 rounded-xl">
                                    <i data-lucide="globe" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                                    <h4 class="text-sm font-semibold text-gray-700">Belum Ada Tautan Menu</h4>
                                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Tambahkan sistem atau website baru melalui formulir di sebelah kanan.</p>
                                </div>
                            @else
                                <div class="divide-y divide-gray-100 space-y-3">
                                    @foreach($portalLinks as $idx => $link)
                                        <div class="pt-3 first:pt-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-gray-50/70 transition duration-200">
                                            <div class="flex items-start gap-3.5 min-w-0">
                                                <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-800 text-xs font-mono font-bold flex items-center justify-center shrink-0 mt-0.5">
                                                    #{{ $link['order'] ?? ($idx + 1) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="text-sm font-bold text-gray-900 truncate">
                                                            {{ $link['name'] }}
                                                        </h4>
                                                        @if(!empty($link['is_active']))
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                                Aktif
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                                Nonaktif
                                                            </span>
                                                        @endif
                                                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">
                                                            {{ ($link['target'] ?? '_blank') === '_blank' ? 'Tab Baru' : 'Tab Sama' }}
                                                        </span>
                                                    </div>

                                                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs text-teal-600 hover:text-green-600 hover:underline mt-1 truncate max-w-md">
                                                        <span class="truncate">{{ $link['url'] }}</span>
                                                        <i data-lucide="external-link" class="w-3 h-3 shrink-0"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                                <!-- Quick Toggle Status Button -->
                                                <form action="{{ route('admin.page_settings.toggle_portal_link', $link['id']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-2 rounded-lg text-xs font-semibold border transition {{ !empty($link['is_active']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' }}"
                                                            title="{{ !empty($link['is_active']) ? 'Klik untuk Nonaktifkan' : 'Klik untuk Aktifkan' }}">
                                                        <i data-lucide="{{ !empty($link['is_active']) ? 'eye' : 'eye-off' }}" class="w-4 h-4"></i>
                                                    </button>
                                                </form>

                                                <!-- Edit Button -->
                                                <button type="button" 
                                                        @click="openEdit({{ json_encode($link) }})"
                                                        class="p-2 rounded-lg text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:bg-gray-100 hover:text-teal-700 transition"
                                                        title="Edit Tautan">
                                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <form action="{{ route('admin.page_settings.delete_portal_link', $link['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tautan {{ $link['name'] }} dari menu portal?');">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-2 rounded-lg text-xs font-semibold bg-white text-rose-600 border border-rose-200 hover:bg-rose-50 transition"
                                                            title="Hapus Tautan">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Form Tambah Website Baru (Col 5) -->
                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 sticky top-6">
                            <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-green-50 text-[#509c38] flex items-center justify-center font-bold">
                                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Tambah Sistem / Website Baru</h3>
                                    <p class="text-xs text-gray-500">Tautan akan langsung tampil di menu PORTAL.</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.page_settings.store_portal_link') }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">
                                        Nama Sistem / Website <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" required placeholder="Contoh: HRIS First Resources" value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">
                                        URL / Tautan Website <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="url" required placeholder="https://sistem.domain.com" value="{{ old('url') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition font-mono">
                                    <p class="text-[11px] text-gray-400 mt-1">Bisa berupa tautan lengkap (https://...) atau rute internal.</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">
                                            Buka Di
                                        </label>
                                        <select name="target" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition bg-white">
                                            <option value="_blank" selected>Tab Baru (_blank)</option>
                                            <option value="_self">Tab Sama (_self)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">
                                            Urutan Tampilan
                                        </label>
                                        <input type="number" name="order" min="1" value="{{ old('order', count($portalLinks) + 1) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-[#509c38] focus:ring-[#509c38] w-4 h-4">
                                        <span class="text-xs font-semibold text-gray-700">Langsung Aktifkan Tautan</span>
                                    </label>
                                </div>

                                <div class="pt-4 border-t border-gray-100">
                                    <button type="submit" class="w-full bg-[#509c38] hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        Tambah ke Menu Portal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Alpine.js Edit Modal -->
                <div x-show="editModalOpen" 
                     x-transition.opacity 
                     class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" 
                     style="display: none;">
                    <div @click.away="editModalOpen = false" 
                         class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-200 transform transition-all">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-6">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i data-lucide="edit-3" class="w-5 h-5 text-[#509c38]"></i>
                                Edit Tautan Menu Portal
                            </h3>
                            <button @click="editModalOpen = false" type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <form :action="'{{ url('/admin/page-settings/portal-links') }}/' + currentLink.id + '/update'" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Nama Sistem / Website <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="currentLink.name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">URL / Tautan Website <span class="text-rose-500">*</span></label>
                                <input type="text" name="url" x-model="currentLink.url" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-mono focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Buka Di</label>
                                    <select name="target" x-model="currentLink.target" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition bg-white">
                                        <option value="_blank">Tab Baru (_blank)</option>
                                        <option value="_self">Tab Sama (_self)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Urutan Tampilan</label>
                                    <input type="number" name="order" min="1" x-model="currentLink.order" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                            </div>

                            <div class="pt-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" :checked="currentLink.is_active" class="rounded border-gray-300 text-[#509c38] focus:ring-[#509c38] w-4 h-4">
                                    <span class="text-xs font-semibold text-gray-700">Status Aktif (Tampil di Menu Header)</span>
                                </label>
                            </div>

                            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                                <button @click="editModalOpen = false" type="button" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                                    Batal
                                </button>
                                <button type="submit" class="bg-[#509c38] hover:bg-green-600 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 text-xs">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @elseif($tab === 'karir')
            <!-- TAB 4: PROGRAM KARIR (CAREER) -->
            <div class="space-y-6">
                <!-- Section Header / Top Action Bar -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-[#509c38] flex items-center justify-center font-bold shrink-0">
                            <i data-lucide="briefcase" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Daftar Program Karir & Lowongan</h2>
                            <p class="text-xs text-gray-500">Kelola daftar program pelatihan dan lowongan kerja FR Academy yang ditampilkan di halaman publik /career.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('admin.career_programs.create') }}" class="px-5 py-2.5 bg-[#509c38] hover:bg-green-700 text-white font-bold rounded-xl transition shadow-sm inline-flex items-center text-xs sm:text-sm">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Program Baru
                        </a>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase font-semibold text-gray-600 tracking-wider">
                                    <th class="px-6 py-4">Visual Card</th>
                                    <th class="px-6 py-4">Kode</th>
                                    <th class="px-6 py-4">Nama Program</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Link Formulir (GForm)</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($careerPrograms as $program)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="w-16 h-11 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 shrink-0 shadow-xs">
                                                @if($program->image)
                                                    <img src="{{ asset('storage/' . $program->image) }}" alt="{{ $program->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <img src="{{ asset('images/bg_card_' . strtolower($program->code) . '.jpg') }}" 
                                                         onerror="this.src='{{ asset('images/bg_card_fat.jpg') }}'"
                                                         alt="{{ $program->name }}" class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-mono">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $program->code }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-gray-900 leading-snug">{{ $program->name }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ Str::limit(strip_tags($program->description), 75) }}</p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($program->is_active)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($program->gform_url)
                                                <a href="{{ $program->gform_url }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                                    <span>Buka Link</span>
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Belum diset</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.career_programs.edit', $program) }}" 
                                                   class="p-2 rounded-lg text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:bg-gray-100 hover:text-teal-700 transition"
                                                   title="Edit Program">
                                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                </a>
                                                <form action="{{ route('admin.career_programs.destroy', $program) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program {{ $program->name }} secara permanen?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 rounded-lg text-xs font-semibold bg-white text-rose-600 border border-rose-200 hover:bg-rose-50 transition"
                                                            title="Hapus Program">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                            <div class="w-16 h-16 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-4">
                                                <i data-lucide="briefcase" class="w-8 h-8"></i>
                                            </div>
                                            <p class="text-base font-bold text-gray-800 mb-1">Belum Ada Program Karir</p>
                                            <p class="text-xs text-gray-500 max-w-sm mx-auto mb-5">Program karir dan lowongan yang Anda tambahkan di sini akan langsung tampil pada halaman publik Karir.</p>
                                            <a href="{{ route('admin.career_programs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#509c38] text-white text-xs font-bold hover:bg-green-700 shadow-sm transition">
                                                <i data-lucide="plus" class="w-4 h-4"></i>
                                                Tambah Program Pertama
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
