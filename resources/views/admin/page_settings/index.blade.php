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
            <a href="{{ route('admin.page_settings.index', ['tab' => 'program']) }}" 
               class="pb-4 px-2 border-b-2 font-semibold text-sm flex items-center gap-2.5 transition-all whitespace-nowrap {{ $tab === 'program' ? 'border-[#509c38] text-[#509c38]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                Halaman Program (Katalog & Detail)
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

                <!-- Card 3: Pilar Program Utama -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8"
                     x-data="{
                         pillars: {{ \Illuminate\Support\Js::from($pillarCards) }},
                         addPillar() {
                             const newId = 'p_' + Date.now();
                             this.pillars.push({
                                 id: newId,
                                 title: 'Pilar Baru',
                                 desc: '',
                                 btn_text: 'Pelajari lebih lanjut',
                                 btn_url: '',
                                 image: 'images/slide3.png',
                                 icon: 'images/reguler.png',
                                 image_url: '{{ asset('images/slide3.png') }}',
                                 icon_url: '{{ asset('images/reguler.png') }}',
                                 previewImage: null,
                                 previewIcon: null
                             });
                         },
                         removePillar(idx) {
                             if (this.pillars.length <= 1) {
                                 alert('Minimal harus ada 1 pilar program.');
                                 return;
                             }
                             if (confirm('Apakah Anda yakin ingin menghapus pilar ini?')) {
                                 this.pillars.splice(idx, 1);
                             }
                         },
                         onImageChange(event, item) {
                             const file = event.target.files[0];
                             if (file) {
                                 item.previewImage = URL.createObjectURL(file);
                             }
                         },
                         onIconChange(event, item) {
                             const file = event.target.files[0];
                             if (file) {
                                 item.previewIcon = URL.createObjectURL(file);
                             }
                         }
                     }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold">
                                <i data-lucide="layers" class="w-5 h-5 text-[#509c38]"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Pilar Program Utama</h2>
                                <p class="text-xs text-gray-500">Kelola daftar pilar program secara dinamis (bebas tambah atau hapus pilar: 1, 2, 3, 5, 7, atau lebih).</p>
                            </div>
                        </div>
                        <button type="button" @click="addPillar()" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#509c38] hover:bg-green-700 px-3.5 py-2 rounded-xl transition shadow-sm self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Pilar Baru
                        </button>
                    </div>

                    <!-- Header Bagian Pilar -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Badge Kecil</label>
                            <input type="text" name="home_pillars_badge" value="{{ old('home_pillars_badge', $beranda['home_pillars_badge']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Judul Bagian Pilar <span class="text-rose-500">*</span></label>
                            <input type="text" name="home_pillars_title" required value="{{ old('home_pillars_title', $beranda['home_pillars_title']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Deskripsi Bagian Pilar <span class="text-rose-500">*</span></label>
                            <textarea name="home_pillars_desc" rows="2" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">{{ old('home_pillars_desc', $beranda['home_pillars_desc']) }}</textarea>
                        </div>
                    </div>

                    <!-- Dynamic Repeater Pilar -->
                    <div class="space-y-6">
                        <template x-for="(item, idx) in pillars" :key="item.id || idx">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200/80 transition hover:border-gray-300">
                                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200/60">
                                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-[#509c38] text-white flex items-center justify-center text-xs font-bold" x-text="idx + 1"></span>
                                        <span>Pilar #<span x-text="idx + 1"></span>: <span x-text="item.title || 'Tanpa Judul'"></span></span>
                                    </h3>
                                    <button type="button" @click="removePillar(idx)" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-lg transition" title="Hapus pilar ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus Pilar
                                    </button>
                                </div>

                                <!-- Hidden fields for preserving state & mapping files -->
                                <input type="hidden" :name="'pillars[' + idx + '][id]'" :value="item.id">
                                <input type="hidden" :name="'pillars[' + idx + '][image]'" :value="item.image">
                                <input type="hidden" :name="'pillars[' + idx + '][icon]'" :value="item.icon">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Pilar <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="'pillars[' + idx + '][title]'" x-model="item.title" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Teks Tombol Aksi</label>
                                        <input type="text" :name="'pillars[' + idx + '][btn_text]'" x-model="item.btn_text" placeholder="Pelajari lebih lanjut" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Pilar <span class="text-rose-500">*</span></label>
                                        <textarea :name="'pillars[' + idx + '][desc]'" x-model="item.desc" rows="2" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition"></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Link URL Tombol</label>
                                        <input type="text" :name="'pillars[' + idx + '][btn_url]'" x-model="item.btn_url" placeholder="/program#reguler" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>

                                    <!-- Foto Cover Pilar -->
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 flex flex-col">
                                        <span class="text-xs font-bold text-gray-700 mb-2">Foto Cover Pilar</span>
                                        <div class="w-full h-32 rounded-lg overflow-hidden bg-gray-200 mb-3 border border-gray-300">
                                            <img :src="item.previewImage || item.image_url" alt="Cover Pilar" class="w-full h-full object-cover">
                                        </div>
                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Foto Cover:</label>
                                        <input type="file" :name="'pillar_images[' + item.id + ']'" @change="onImageChange($event, item)" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-[#509c38] hover:file:text-white transition">
                                    </div>

                                    <!-- Ikon Badge Pilar -->
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 flex flex-col">
                                        <span class="text-xs font-bold text-gray-700 mb-2">Ikon Badge Pilar</span>
                                        <div class="w-full h-32 rounded-lg overflow-hidden bg-gray-100 mb-3 border border-gray-300 flex items-center justify-center p-3">
                                            <img :src="item.previewIcon || item.icon_url" alt="Icon Pilar" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Ikon Badge:</label>
                                        <input type="file" :name="'pillar_icons[' + item.id + ']'" @change="onIconChange($event, item)" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-[#509c38] hover:file:text-white transition">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-500">Total: <strong x-text="pillars.length" class="text-gray-900"></strong> Pilar Aktif</span>
                        <button type="button" @click="addPillar()" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#509c38] hover:text-green-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Pilar Baru
                        </button>
                    </div>
                </div>

                <!-- Card 4: Statistik Capaian Perusahaan -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold">
                            <i data-lucide="bar-chart-2" class="w-5 h-5 text-[#509c38]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Statistik & Angka Capaian</h2>
                            <p class="text-xs text-gray-500">Kelola 3 angka capaian utama (misal: luas area kebun simulasi, rasio metode pelatihan, dan jumlah stasiun belajar).</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Stat 1 -->
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200/80 flex flex-col justify-between space-y-4">
                            <div>
                                <span class="text-xs font-bold text-[#509c38] uppercase block mb-3">Statistik #1</span>
                                <div class="w-full h-24 rounded-xl bg-teal-900/90 mb-3 p-3 flex items-center justify-center border border-teal-800">
                                    <img src="{{ $beranda['home_stat1_icon'] }}" alt="Stat 1 Icon" class="max-h-full max-w-full object-contain">
                                </div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Ikon/Ilustrasi:</label>
                                <input type="file" name="home_stat1_icon" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-[#509c38] hover:file:text-white transition mb-3">
                                
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nilai/Angka <span class="text-rose-500">*</span></label>
                                        <input type="text" name="home_stat1_value" required value="{{ old('home_stat1_value', $beranda['home_stat1_value']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan (Unit)</label>
                                        <input type="text" name="home_stat1_unit" value="{{ old('home_stat1_unit', $beranda['home_stat1_unit']) }}" placeholder="Ha" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan Label <span class="text-rose-500">*</span></label>
                                    <input type="text" name="home_stat1_label" required value="{{ old('home_stat1_label', $beranda['home_stat1_label']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                            </div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200/80 flex flex-col justify-between space-y-4">
                            <div>
                                <span class="text-xs font-bold text-[#509c38] uppercase block mb-3">Statistik #2</span>
                                <div class="w-full h-24 rounded-xl bg-teal-900/90 mb-3 p-3 flex items-center justify-center border border-teal-800">
                                    <img src="{{ $beranda['home_stat2_icon'] }}" alt="Stat 2 Icon" class="max-h-full max-w-full object-contain">
                                </div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Ikon/Ilustrasi:</label>
                                <input type="file" name="home_stat2_icon" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-[#509c38] hover:file:text-white transition mb-3">
                                
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nilai/Angka <span class="text-rose-500">*</span></label>
                                        <input type="text" name="home_stat2_value" required value="{{ old('home_stat2_value', $beranda['home_stat2_value']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan (Unit)</label>
                                        <input type="text" name="home_stat2_unit" value="{{ old('home_stat2_unit', $beranda['home_stat2_unit']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan Label <span class="text-rose-500">*</span></label>
                                    <input type="text" name="home_stat2_label" required value="{{ old('home_stat2_label', $beranda['home_stat2_label']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                            </div>
                        </div>

                        <!-- Stat 3 -->
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200/80 flex flex-col justify-between space-y-4">
                            <div>
                                <span class="text-xs font-bold text-[#509c38] uppercase block mb-3">Statistik #3</span>
                                <div class="w-full h-24 rounded-xl bg-teal-900/90 mb-3 p-3 flex items-center justify-center border border-teal-800">
                                    <img src="{{ $beranda['home_stat3_icon'] }}" alt="Stat 3 Icon" class="max-h-full max-w-full object-contain">
                                </div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Ganti Ikon/Ilustrasi:</label>
                                <input type="file" name="home_stat3_icon" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-[#509c38] hover:file:text-white transition mb-3">
                                
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nilai/Angka <span class="text-rose-500">*</span></label>
                                        <input type="text" name="home_stat3_value" required value="{{ old('home_stat3_value', $beranda['home_stat3_value']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan (Unit)</label>
                                        <input type="text" name="home_stat3_unit" value="{{ old('home_stat3_unit', $beranda['home_stat3_unit']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan Label <span class="text-rose-500">*</span></label>
                                    <input type="text" name="home_stat3_label" required value="{{ old('home_stat3_label', $beranda['home_stat3_label']) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Layanan & Program Unggul (Accordion) -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8"
                     x-data="{
                         accordions: {{ \Illuminate\Support\Js::from($accordions) }},
                         addAccordion() {
                             const num = this.accordions.length + 1;
                             const pad = num.toString().padStart(2, '0');
                             this.accordions.push({
                                 id: 'acc_' + Date.now(),
                                 title: 'ITEM ACCORDION ' + pad,
                                 desc: '',
                                 items: [
                                     { title: '', desc: '' }
                                 ]
                             });
                         },
                         removeAccordion(accIdx) {
                             if (this.accordions.length <= 1) {
                                 alert('Minimal harus ada 1 item accordion.');
                                 return;
                             }
                             if (confirm('Apakah Anda yakin ingin menghapus seluruh item accordion ini beserta poin-poinnya?')) {
                                 this.accordions.splice(accIdx, 1);
                             }
                         },
                         addPoint(accIdx) {
                             if (!this.accordions[accIdx].items) {
                                 this.accordions[accIdx].items = [];
                             }
                             this.accordions[accIdx].items.push({ title: '', desc: '' });
                         },
                         removePoint(accIdx, itemIdx) {
                             if (this.accordions[accIdx].items.length <= 1) {
                                 alert('Minimal harus ada 1 poin di dalam accordion ini.');
                                 return;
                             }
                             this.accordions[accIdx].items.splice(itemIdx, 1);
                         }
                     }">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold">
                                <i data-lucide="folder-kanban" class="w-5 h-5 text-[#509c38]"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Layanan & Program Unggul (Accordion)</h2>
                                <p class="text-xs text-gray-500">Kelola judul headline dan item accordion yang fleksibel beserta daftar poin seragam di dalamnya.</p>
                            </div>
                        </div>
                        <button type="button" @click="addAccordion()" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-[#509c38] hover:bg-green-700 px-3.5 py-2 rounded-xl transition shadow-sm self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Item Accordion
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Teks Badge Kecil</label>
                            <input type="text" name="home_program_badge" value="{{ old('home_program_badge', $beranda['home_program_badge']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1.5">Judul Besar Headline <span class="text-rose-500">*</span></label>
                            <input type="text" name="home_program_title" required value="{{ old('home_program_title', $beranda['home_program_title']) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                            <p class="text-[11px] text-gray-400 mt-1">Gunakan tag &lt;br&gt; jika ingin membuat baris baru.</p>
                        </div>
                    </div>

                    <!-- Accordion List Repeater -->
                    <div class="space-y-6">
                        <template x-for="(acc, accIdx) in accordions" :key="acc.id || accIdx">
                            <div class="bg-gray-50 p-5 sm:p-6 rounded-2xl border border-gray-200/80 space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-gray-200/60">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 text-xs font-mono font-bold" x-text="'(' + (accIdx + 1).toString().padStart(2, '0') + ')'"></span>
                                        <h3 class="text-sm font-bold text-gray-900 uppercase">
                                            <span>Item Accordion <span x-text="(accIdx + 1).toString().padStart(2, '0')"></span></span>: <span x-text="acc.title || 'Tanpa Judul'"></span>
                                        </h3>
                                    </div>
                                    <button type="button" @click="removeAccordion(accIdx)" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-lg transition" title="Hapus accordion ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus Item
                                    </button>
                                </div>

                                <input type="hidden" :name="'accordions[' + accIdx + '][id]'" :value="acc.id || ('acc_' + accIdx)">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Accordion <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="'accordions[' + accIdx + '][title]'" x-model="acc.title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition" placeholder="Cth: PROGRAM REGULER">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Singkat / Pengantar <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="'accordions[' + accIdx + '][desc]'" x-model="acc.desc" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition" placeholder="Deskripsi pengantar singkat saat accordion dibuka...">
                                    </div>
                                </div>

                                <!-- Dynamic Points Repeater (Uniform across ALL accordions!) -->
                                <div class="mt-4 pt-4 border-t border-gray-200/70">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-800 uppercase flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-[#509c38]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                <span>Daftar Poin</span>
                                            </h4>
                                            <p class="text-[11px] text-gray-500">Kelola judul poin dan keterangan rincian di dalamnya.</p>
                                        </div>
                                        <button type="button" @click="addPoint(accIdx)" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#509c38] hover:text-green-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Tambah Poin
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(item, itemIdx) in (acc.items || [])" :key="itemIdx">
                                            <div class="bg-white p-4 rounded-xl border border-gray-200/90 shadow-sm flex flex-col md:flex-row gap-3 items-start relative group hover:border-emerald-300 transition">
                                                <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-800 flex items-center justify-center text-xs font-bold shrink-0 mt-1 border border-teal-100">
                                                    <span x-text="itemIdx + 1"></span>
                                                </div>
                                                <div class="flex-grow w-full grid grid-cols-1 md:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Judul Poin <span class="text-rose-500">*</span></label>
                                                        <input type="text" :name="'accordions[' + accIdx + '][items][' + itemIdx + '][title]'" x-model="item.title" placeholder="Cth: Judul modul, topik, atau inisiatif" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-medium focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Deskripsi / Keterangan (Opsional)</label>
                                                        <textarea :name="'accordions[' + accIdx + '][items][' + itemIdx + '][desc]'" x-model="item.desc" rows="2" placeholder="Penjelasan atau rincian poin (opsional jika hanya berupa list singkat)..." class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition"></textarea>
                                                    </div>
                                                </div>
                                                <button type="button" @click="removePoint(accIdx, itemIdx)" class="shrink-0 text-gray-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-50 transition mt-1" title="Hapus Poin Ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-500">Total: <strong x-text="accordions.length" class="text-gray-900"></strong> Item Accordion</span>
                        <button type="button" @click="addAccordion()" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#509c38] hover:text-green-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-xl transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Item Accordion
                        </button>
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
        @elseif($tab === 'program')
            <!-- TAB 5: PROGRAM PELATIHAN (KATALOG & DETAIL PROGRAM) -->
            <div class="space-y-6" x-data="{ 
                filter: 'all', 
                search: '',
                matches(item) {
                    const matchesFilter = this.filter === 'all' || 
                        (this.filter === 'reguler' && item.is_reguler) || 
                        (this.filter === 'non_reguler' && !item.is_reguler);
                    const query = this.search.toLowerCase().trim();
                    const matchesSearch = !query || 
                        item.title.toLowerCase().includes(query) || 
                        item.code.toLowerCase().includes(query) || 
                        item.slug.toLowerCase().includes(query) ||
                        (item.category && item.category.toLowerCase().includes(query));
                    return matchesFilter && matchesSearch;
                }
            }">
                <!-- Section Header / Top Action Bar -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-800 flex items-center justify-center font-bold shrink-0">
                            <i data-lucide="book-open" class="w-6 h-6 text-[#509c38]"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Kelola Halaman Program Pelatihan</h2>
                            <p class="text-xs text-gray-500">Kelola katalog dan rincian halaman program publik, baik Program Reguler (Poster) maupun Non-Reguler (Minimalis).</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ url('/program/fat') }}" target="_blank" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:text-[#509c38] hover:bg-gray-50 shadow-sm transition inline-flex items-center gap-2">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            Pratinjau Web
                        </a>
                        <a href="{{ route('admin.portal_programs.create') }}" class="px-5 py-2.5 bg-[#509c38] hover:bg-green-700 text-white font-bold rounded-xl transition shadow-sm inline-flex items-center text-xs sm:text-sm">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Program Baru
                        </a>
                    </div>
                </div>

                <!-- Stats Overview Cards -->
                @php
                    $totalPrograms = $portalPrograms->count();
                    $regulerCount = $portalPrograms->where('is_reguler', true)->count();
                    $nonRegulerCount = $portalPrograms->where('is_reguler', false)->count();
                    $activeCount = $portalPrograms->where('is_active', true)->count();
                @endphp
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Program</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalPrograms }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Semua program di portal</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Dengan Kurikulum</p>
                        <p class="text-2xl font-black text-emerald-700 mt-1">{{ $regulerCount }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Memiliki rincian kurikulum</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
                        <p class="text-xs font-semibold text-teal-600 uppercase tracking-wider">Format Standar</p>
                        <p class="text-2xl font-black text-teal-700 mt-1">{{ $nonRegulerCount }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Judul, deskripsi & gambar</p>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Status Aktif</p>
                        <p class="text-2xl font-black text-blue-700 mt-1">{{ $activeCount }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Tayang di web publik</p>
                    </div>
                </div>

                <!-- Filters & Search Bar -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl">
                        <button type="button" @click="filter = 'all'" 
                                :class="filter === 'all' ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-900 font-medium'"
                                class="px-3.5 py-1.5 rounded-lg text-xs transition">
                            Semua ({{ $totalPrograms }})
                        </button>
                        <button type="button" @click="filter = 'reguler'" 
                                :class="filter === 'reguler' ? 'bg-white text-emerald-700 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-900 font-medium'"
                                class="px-3.5 py-1.5 rounded-lg text-xs transition">
                            Dengan Kurikulum ({{ $regulerCount }})
                        </button>
                        <button type="button" @click="filter = 'non_reguler'" 
                                :class="filter === 'non_reguler' ? 'bg-white text-teal-700 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-900 font-medium'"
                                class="px-3.5 py-1.5 rounded-lg text-xs transition">
                            Standar ({{ $nonRegulerCount }})
                        </button>
                    </div>

                    <div class="relative sm:w-72">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" x-model="search" placeholder="Cari nama, kode, slug..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs focus:border-[#509c38] focus:ring-1 focus:ring-[#509c38] transition">
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-200 text-xs uppercase font-semibold text-gray-600 tracking-wider">
                                    <th class="px-6 py-4">Gambar</th>
                                    <th class="px-6 py-4">Nama Program & Subtitle</th>
                                    <th class="px-6 py-4">Tipe Program</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($portalPrograms as $prog)
                                    <tr class="hover:bg-gray-50/80 transition-colors"
                                        x-show="matches({
                                            title: '{{ addslashes($prog->title) }}',
                                            code: '{{ addslashes($prog->code ?? '') }}',
                                            slug: '{{ addslashes($prog->slug) }}',
                                            category: '{{ addslashes($prog->category_name ?? $prog->category_key) }}',
                                            is_reguler: {{ $prog->is_reguler ? 'true' : 'false' }}
                                        })">
                                        <!-- Visual / Poster Thumbnail -->
                                        <td class="px-6 py-4">
                                            <div class="w-14 h-14 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shrink-0 shadow-xs p-1 flex items-center justify-center">
                                                <img src="{{ $prog->getResolvedImageUrl() }}" 
                                                     alt="{{ $prog->title }}" 
                                                     class="w-full h-full object-contain rounded">
                                            </div>
                                        </td>

                                        <!-- Nama Program & Subtitle -->
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-gray-900 leading-snug">{{ $prog->title }}</p>
                                            @if($prog->subtitle)
                                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $prog->subtitle }}</p>
                                            @endif
                                        </td>

                                        <!-- Kategori / Tipe Program -->
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#509c38]"></span>
                                                {{ $prog->category_name ?? 'Program Pelatihan' }}
                                            </span>
                                            @if(!empty($prog->curriculum_pillars) || !empty($prog->objectives))
                                                <span class="block text-[10px] text-emerald-600 mt-1 font-medium">+ Rincian Kurikulum</span>
                                            @endif
                                        </td>

                                        <!-- Status Aktif -->
                                        <td class="px-6 py-4 text-center">
                                            <form action="{{ route('admin.portal_programs.toggle', $prog) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold transition cursor-pointer {{ $prog->is_active ? 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200' }}"
                                                        title="Klik untuk mengubah status">
                                                    <i data-lucide="{{ $prog->is_active ? 'check-circle' : 'x-circle' }}" class="w-3.5 h-3.5"></i>
                                                    {{ $prog->is_active ? 'Aktif' : 'Non-Aktif' }}
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- Edit -->
                                                <a href="{{ route('admin.portal_programs.edit', $prog) }}" 
                                                   class="p-2 rounded-lg text-xs font-semibold bg-white text-amber-600 border border-amber-200 hover:bg-amber-50 transition"
                                                   title="Edit Program">
                                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                                </a>

                                                <!-- Hapus -->
                                                <form action="{{ route('admin.portal_programs.destroy', $prog) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus program {{ addslashes($prog->title) }} secara permanen?');" 
                                                      class="inline-block">
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
                                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                            <div class="w-16 h-16 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-4">
                                                <i data-lucide="book-open" class="w-8 h-8"></i>
                                            </div>
                                            <p class="text-base font-bold text-gray-800 mb-1">Belum Ada Program Pelatihan</p>
                                            <p class="text-xs text-gray-500 max-w-sm mx-auto mb-5">Program yang Anda tambahkan di sini akan langsung tampil pada katalog dan halaman detail portal.</p>
                                            <a href="{{ route('admin.portal_programs.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#509c38] text-white text-xs font-bold hover:bg-green-700 shadow-sm transition">
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
