<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\CareerProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageSettingController extends Controller
{
    /**
     * Tampilkan formulir pengaturan konten halaman.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'beranda');

        // Dynamic Hero Slides
        $rawSlides = AppSetting::getRawHeroSlides();
        $slides = [];
        foreach ($rawSlides as $idx => $path) {
            $slides[] = [
                'index' => $idx,
                'path' => $path,
                'url' => AppSetting::formatSlideUrl($path),
            ];
        }

        // Defaults untuk Beranda
        $beranda = [
            'home_hero_badge' => AppSetting::get('home_hero_badge', 'FIRST RESOURCES ACADEMY'),
            'home_hero_title' => AppSetting::get('home_hero_title', 'Membangun Value, Menumbuhkan Karakter & Menyiapkan Masa Depan'),
            'home_hero_tagline' => AppSetting::get('home_hero_tagline', '"Kebun Itu Sebaik Manusianya."'),
            'home_hero_desc' => AppSetting::get('home_hero_desc', 'FR Academy hadir sebagai pusat pengembangan kompetensi dan pembentukan karakter kerja untuk menciptakan human capital perusahaan yang unggul, adaptif, dan siap menghadapi tantangan bisnis.'),
            'home_hero_btn1_text' => AppSetting::get('home_hero_btn1_text', 'Jelajahi Program'),
            'home_hero_btn1_url' => AppSetting::get('home_hero_btn1_url', '#program'),
            'home_hero_btn2_text' => AppSetting::get('home_hero_btn2_text', 'Baca Artikel'),
            'home_hero_btn2_url' => AppSetting::get('home_hero_btn2_url', '/artikel'),

            'home_vision_badge' => AppSetting::get('home_vision_badge', 'TENTANG FR ACADEMY'),
            'home_vision_title' => AppSetting::get('home_vision_title', 'Visi FR Academy'),
            'home_vision_desc' => AppSetting::get('home_vision_desc', 'Meningkatkan nilai perusahaan melalui pembelajaran berkelanjutan yang berkualitas dengan menjadi mitra strategis dalam pengembangan SDM. Kami merancang lingkungan belajar yang mengintegrasikan pengetahuan teknis lapangan dengan nilai-nilai kepemimpinan.'),
            'home_vision_p1_title' => AppSetting::get('home_vision_p1_title', 'Instruktur Internal Ahli'),
            'home_vision_p1_desc' => AppSetting::get('home_vision_p1_desc', 'Dibimbing langsung oleh praktisi internal kompeten mulai dari level Manager hingga Direktur, serta External Expert sesuai bidangnya.'),
            'home_vision_p2_title' => AppSetting::get('home_vision_p2_title', 'Fasilitas Terintegrasi'),
            'home_vision_p2_desc' => AppSetting::get('home_vision_p2_desc', 'Terintegrasi dengan kebun 300 Ha dan R&D, dilengkapi ruang kelas, mess 48 kamar, sarana olahraga, hingga area praktik langsung.'),

            'home_vision_img_1' => AppSetting::getImageUrl('home_vision_img_1', 'https://i.ibb.co.com/hFZ1CScS/slides-6.jpg'),
            'home_vision_img_2' => AppSetting::getImageUrl('home_vision_img_2', 'https://i.ibb.co.com/Rp1kWtfV/image-12.png'),

            // Pilar Program Utama
            'home_pillars_badge' => AppSetting::get('home_pillars_badge', 'PILAR PROGRAM UTAMA'),
            'home_pillars_title' => AppSetting::get('home_pillars_title', 'Tiga Pilar Program Utama'),
            'home_pillars_desc' => AppSetting::get('home_pillars_desc', 'FR Academy berfokus pada pengembangan SDM melalui tiga pilar pembelajaran utama.'),

            'home_p1_title' => AppSetting::get('home_p1_title', 'Regular Training Program'),
            'home_p1_desc' => AppSetting::get('home_p1_desc', 'Program pengembangan berkesinambungan selama enam bulan dengan pendampingan mentoring untuk menyiapkan calon staf dan asisten perkebunan.'),
            'home_p1_btn_text' => AppSetting::get('home_p1_btn_text', 'Pelajari lebih lanjut'),
            'home_p1_btn_url' => AppSetting::get('home_p1_btn_url', '/program/fat'),
            'home_p1_img' => AppSetting::getImageUrl('home_p1_img', 'images/slide3.png'),
            'home_p1_icon' => AppSetting::getImageUrl('home_p1_icon', 'images/reguler.png'),

            'home_p2_title' => AppSetting::get('home_p2_title', 'Non-Regular Training Program'),
            'home_p2_desc' => AppSetting::get('home_p2_desc', 'Pelatihan berdurasi singkat untuk meningkatkan kompetensi teknis dan manajerial staf operasional di Kebun, Pabrik (Mill), Traksi, hingga Administrasi.'),
            'home_p2_btn_text' => AppSetting::get('home_p2_btn_text', 'Pelajari lebih lanjut'),
            'home_p2_btn_url' => AppSetting::get('home_p2_btn_url', '/program/managerial-leadership'),
            'home_p2_img' => AppSetting::getImageUrl('home_p2_img', 'https://i.ibb.co.com/3mg5bHQN/image-14.png'),
            'home_p2_icon' => AppSetting::getImageUrl('home_p2_icon', 'images/nonreguler.png'),

            'home_p3_title' => AppSetting::get('home_p3_title', 'Talent Management Program'),
            'home_p3_desc' => AppSetting::get('home_p3_desc', 'Program akselerasi terstruktur yang dirancang khusus untuk mempersiapkan staf berpotensi (high talent) menjadi pemimpin masa depan perusahaan.'),
            'home_p3_btn_text' => AppSetting::get('home_p3_btn_text', 'Pelajari lebih lanjut'),
            'home_p3_btn_url' => AppSetting::get('home_p3_btn_url', '/program/talent-management'),
            'home_p3_img' => AppSetting::getImageUrl('home_p3_img', 'https://i.ibb.co.com/XrLSNbzC/image-13.png'),
            'home_p3_icon' => AppSetting::getImageUrl('home_p3_icon', 'images/talent.png'),

            // Statistik Capaian
            'home_stat1_value' => AppSetting::get('home_stat1_value', '300'),
            'home_stat1_unit' => AppSetting::get('home_stat1_unit', 'Ha'),
            'home_stat1_label' => AppSetting::get('home_stat1_label', 'Area Praktik & Simulasi'),
            'home_stat1_icon' => AppSetting::getImageUrl('home_stat1_icon', 'images/300h.png'),

            'home_stat2_value' => AppSetting::get('home_stat2_value', '70:20:10'),
            'home_stat2_unit' => AppSetting::get('home_stat2_unit', ''),
            'home_stat2_label' => AppSetting::get('home_stat2_label', 'Metode Pembelajaran (Experiential, Social, Formal)'),
            'home_stat2_icon' => AppSetting::getImageUrl('home_stat2_icon', 'images/learningmethod.png'),

            'home_stat3_value' => AppSetting::get('home_stat3_value', '3'),
            'home_stat3_unit' => AppSetting::get('home_stat3_unit', ''),
            'home_stat3_label' => AppSetting::get('home_stat3_label', 'Pusat Pelatihan & Learning Station'),
            'home_stat3_icon' => AppSetting::getImageUrl('home_stat3_icon', 'images/3pillar.png'),

            // Program Accordion
            'home_program_badge' => AppSetting::get('home_program_badge', 'MENCETAK PLANTER & ENGINEER UNGGUL'),
            'home_program_title' => AppSetting::get('home_program_title', 'Membangun Value, Menumbuhkan Karakter & <br> Menyiapkan Masa Depan'),
            'home_acc1_title' => AppSetting::get('home_acc1_title', 'PROGRAM REGULER'),
            'home_acc1_desc' => AppSetting::get('home_acc1_desc', 'Program pelatihan dasar bagi karyawan baru maupun calon karyawan yang akan ditempatkan di berbagai kebun operasional.'),
            'home_acc2_title' => AppSetting::get('home_acc2_title', 'PROGRAM NON-REGULER'),
            'home_acc2_desc' => AppSetting::get('home_acc2_desc', 'Pelatihan khusus yang dirancang sesuai dengan kebutuhan spesifik divisi atau tantangan operasional terkini.'),
            'home_acc3_title' => AppSetting::get('home_acc3_title', 'INISIATIF PROGRAM'),
            'home_acc3_desc' => AppSetting::get('home_acc3_desc', 'Beragam inisiatif strategis dan kegiatan komprehensif yang dirancang khusus untuk mendorong budaya inovasi, peningkatan kompetensi, serta kolaborasi antar divisi:'),
        ];

        // Defaults untuk Tentang Kami
        $tentang = [
            'about_hero_badge' => AppSetting::get('about_hero_badge', 'TENTANG KAMI'),
            'about_hero_title' => AppSetting::get('about_hero_title', 'Center of Excellence dalam Membentuk Insan Agribisnis Berkelas Dunia'),
            'about_hero_col1' => AppSetting::get('about_hero_col1', 'Sejak 1996, First Resources Academy menjadi kawah candradimuka dalam membina kompetensi teknis, disiplin, dan kepemimpinan operasional perkebunan.'),
            'about_hero_col2' => AppSetting::get('about_hero_col2', 'Mengintegrasikan kurikulum berstandar industri, pengajar praktisi internal, dan 300 hektare sarana simulasi kebun terpadu di Kampar, Riau.'),
            'about_hero_bg' => AppSetting::getImageUrl('about_hero_bg', 'images/welcomefr.jpg'),

            'about_profile_badge' => AppSetting::get('about_profile_badge', '[ 01 / PROFIL & MISI ]'),
            'about_profile_title' => AppSetting::get('about_profile_title', 'Membentuk Karakter & Ketangguhan Generasi Pemimpin Agribisnis'),
            'about_profile_desc' => AppSetting::get('about_profile_desc', 'First Resources Academy didirikan sebagai komitmen strategis First Resources Group dalam memastikan regenerasi sumber daya manusia yang handal, adaptif, dan berintegritas tinggi. Kami tidak hanya mentransfer keahlian teknis budidaya agronomi dan pengolahan pabrik kelapa sawit, tetapi menanamkan etos kerja unggul, kepatuhan SOP, dan kepemimpinan operasional berwawasan masa depan.'),
            'about_profile_img' => AppSetting::getImageUrl('about_profile_img', 'images/slide4.png'),
            'about_profile_img_caption' => AppSetting::get('about_profile_img_caption', 'Pelatihan Kelas & Pembekalan K3 Staf Operasional Perkebunan'),
            'about_profile_img_location' => AppSetting::get('about_profile_img_location', 'Pusat Pelatihan Kebun Kubang, Kampar'),

            'about_philosophy_badge' => AppSetting::get('about_philosophy_badge', '[ 02 / FILOSOFI & SKALA ]'),
            'about_philosophy_title' => AppSetting::get('about_philosophy_title', 'Lebih dari Sekadar Pusat Pelatihan — Kami Membentuk Sikap Kerja, Ketajaman Analisis, dan Kepemimpinan Nyata di Lapangan.'),
        ];

        // Daftar Menu Portal (Header & Mobile Dropdown)
        $portalLinks = AppSetting::getPortalLinks(false);

        // Daftar Program Karir (Career)
        $careerPrograms = CareerProgram::latest()->get();

        // Daftar Program Portal (Pelatihan Reguler & Non-Reguler)
        $portalPrograms = \App\Models\PortalProgram::orderBy('order')->orderBy('id')->get();

        // Dynamic Pillar Cards (Bisa 1, 3, 5, 7, atau lebih)
        $pillarCards = AppSetting::getPillarCards();

        // Dynamic Accordions & Poin (Uniform & Fleksibel)
        $accordions = AppSetting::getAccordions();

        return view('admin.page_settings.index', compact('tab', 'beranda', 'tentang', 'slides', 'portalLinks', 'careerPrograms', 'portalPrograms', 'pillarCards', 'accordions'));
    }

    /**
     * Simpan pembaruan konten Beranda.
     */
    public function updateBeranda(Request $request)
    {
        $validated = $request->validate([
            'home_hero_badge' => 'nullable|string|max:100',
            'home_hero_title' => 'required|string|max:255',
            'home_hero_tagline' => 'nullable|string|max:255',
            'home_hero_desc' => 'required|string',
            'home_hero_btn1_text' => 'nullable|string|max:100',
            'home_hero_btn1_url' => 'nullable|string|max:255',
            'home_hero_btn2_text' => 'nullable|string|max:100',
            'home_hero_btn2_url' => 'nullable|string|max:255',

            'replace_slides' => 'nullable|array',
            'replace_slides.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'new_slides' => 'nullable|array',
            'new_slides.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            'home_vision_badge' => 'nullable|string|max:100',
            'home_vision_title' => 'required|string|max:255',
            'home_vision_desc' => 'required|string',
            'home_vision_p1_title' => 'nullable|string|max:150',
            'home_vision_p1_desc' => 'nullable|string',
            'home_vision_p2_title' => 'nullable|string|max:150',
            'home_vision_p2_desc' => 'nullable|string',

            'home_vision_img_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'home_vision_img_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            // Card 3: Pilar Program Utama
            'home_pillars_badge' => 'nullable|string|max:100',
            'home_pillars_title' => 'required|string|max:255',
            'home_pillars_desc' => 'required|string',
            'pillars' => 'nullable|array',
            'pillar_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'pillar_icons.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',

            // Card 4: Statistik Capaian
            'home_stat1_value' => 'required|string|max:50',
            'home_stat1_unit' => 'nullable|string|max:50',
            'home_stat1_label' => 'required|string|max:150',
            'home_stat1_icon' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',

            'home_stat2_value' => 'required|string|max:50',
            'home_stat2_unit' => 'nullable|string|max:50',
            'home_stat2_label' => 'required|string|max:150',
            'home_stat2_icon' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',

            'home_stat3_value' => 'required|string|max:50',
            'home_stat3_unit' => 'nullable|string|max:50',
            'home_stat3_label' => 'required|string|max:150',
            'home_stat3_icon' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',

            // Card 5: Accordion Program
            'home_program_badge' => 'nullable|string|max:100',
            'home_program_title' => 'required|string|max:255',
            'accordions' => 'nullable|array',
        ]);

        // Simpan input teks
        $textFields = [
            'home_hero_badge', 'home_hero_title', 'home_hero_tagline', 'home_hero_desc',
            'home_hero_btn1_text', 'home_hero_btn1_url', 'home_hero_btn2_text', 'home_hero_btn2_url',
            'home_vision_badge', 'home_vision_title', 'home_vision_desc',
            'home_vision_p1_title', 'home_vision_p1_desc', 'home_vision_p2_title', 'home_vision_p2_desc',
            // Pilar Header
            'home_pillars_badge', 'home_pillars_title', 'home_pillars_desc',
            // Stats
            'home_stat1_value', 'home_stat1_unit', 'home_stat1_label',
            'home_stat2_value', 'home_stat2_unit', 'home_stat2_label',
            'home_stat3_value', 'home_stat3_unit', 'home_stat3_label',
            // Accordion Header
            'home_program_badge', 'home_program_title',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                AppSetting::set($field, $request->input($field));
            }
        }

        // Kelola Slide Foto Dinamis
        $currentSlides = AppSetting::getRawHeroSlides();

        // 1. Ganti slide yang ada jika ada upload replace
        if ($request->hasFile('replace_slides')) {
            foreach ($request->file('replace_slides') as $index => $file) {
                if ($file && isset($currentSlides[$index])) {
                    $oldPath = $currentSlides[$index];
                    if ($oldPath && ! str_starts_with($oldPath, 'images/') && ! str_starts_with($oldPath, 'http')) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    $stored = $file->store('page_settings', 'public');
                    $currentSlides[$index] = $stored;
                }
            }
        }

        // 2. Tambah slide-slide baru jika diunggah
        if ($request->hasFile('new_slides')) {
            foreach ($request->file('new_slides') as $newFile) {
                if ($newFile) {
                    $stored = $newFile->store('page_settings', 'public');
                    $currentSlides[] = $stored;
                }
            }
        }

        AppSetting::set('home_hero_slides', json_encode(array_values($currentSlides)));

        // Upload foto Visi & Statistik
        $imageFields = [
            'home_vision_img_1', 'home_vision_img_2',
            'home_stat1_icon', 'home_stat2_icon', 'home_stat3_icon',
        ];
        foreach ($imageFields as $imgField) {
            if ($request->hasFile($imgField)) {
                $oldPath = AppSetting::get($imgField);
                if ($oldPath && ! str_starts_with($oldPath, 'images/') && ! str_starts_with($oldPath, 'http')) {
                    Storage::disk('public')->delete($oldPath);
                }

                $storedPath = $request->file($imgField)->store('page_settings', 'public');
                AppSetting::set($imgField, $storedPath);
            }
        }

        // Simpan Pilar Program Dinamis (Bisa 1, 3, 5, 7, atau berapapun)
        if ($request->has('pillars') && is_array($request->input('pillars'))) {
            $submittedPillars = $request->input('pillars');
            $pillarImages = $request->file('pillar_images') ?? [];
            $pillarIcons = $request->file('pillar_icons') ?? [];
            $cleanPillars = [];

            foreach ($submittedPillars as $p) {
                $pId = $p['id'] ?? ('p_'.uniqid());
                $title = trim($p['title'] ?? '');
                if ($title === '') {
                    continue;
                }

                $imagePath = $p['image'] ?? 'images/slide3.png';
                if (isset($pillarImages[$pId]) && $pillarImages[$pId]) {
                    if ($imagePath && ! str_starts_with($imagePath, 'images/') && ! str_starts_with($imagePath, 'http')) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $imagePath = $pillarImages[$pId]->store('page_settings', 'public');
                }

                $iconPath = $p['icon'] ?? 'images/reguler.png';
                if (isset($pillarIcons[$pId]) && $pillarIcons[$pId]) {
                    if ($iconPath && ! str_starts_with($iconPath, 'images/') && ! str_starts_with($iconPath, 'http')) {
                        Storage::disk('public')->delete($iconPath);
                    }
                    $iconPath = $pillarIcons[$pId]->store('page_settings', 'public');
                }

                $cleanPillars[] = [
                    'id' => $pId,
                    'title' => $title,
                    'desc' => trim($p['desc'] ?? ''),
                    'btn_text' => trim($p['btn_text'] ?? 'Pelajari lebih lanjut'),
                    'btn_url' => trim($p['btn_url'] ?? ''),
                    'image' => $imagePath,
                    'icon' => $iconPath,
                ];
            }

            AppSetting::set('home_pillars_cards', json_encode(array_values($cleanPillars)));
        }

        // Simpan Accordion & Poin Dinamis (Uniform & Fleksibel)
        if ($request->has('accordions') && is_array($request->input('accordions'))) {
            AppSetting::saveAccordions($request->input('accordions'));
        }

        return redirect()->route('admin.page_settings.index', ['tab' => 'beranda'])
            ->with('success', 'Konten Halaman Utama (Beranda) dan Slider Foto berhasil diperbarui!');
    }

    /**
     * Hapus satu slide tertentu.
     */
    public function deleteSlide(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $index = (int) $request->input('index');
        $slides = AppSetting::getRawHeroSlides();

        if (count($slides) <= 1) {
            return redirect()->route('admin.page_settings.index', ['tab' => 'beranda'])
                ->withErrors(['slide' => 'Slider harus memiliki minimal 1 slide foto!']);
        }

        if (isset($slides[$index])) {
            $path = $slides[$index];
            if ($path && ! str_starts_with($path, 'images/') && ! str_starts_with($path, 'http')) {
                Storage::disk('public')->delete($path);
            }
            array_splice($slides, $index, 1);
            AppSetting::set('home_hero_slides', json_encode(array_values($slides)));
        }

        return redirect()->route('admin.page_settings.index', ['tab' => 'beranda'])
            ->with('success', 'Slide foto berhasil dihapus!');
    }

    /**
     * Reset slider foto ke 4 foto bawaan sistem.
     */
    public function resetSlides()
    {
        $defaultSlides = [
            'images/welcomefr.jpg',
            'images/slide2.png',
            'images/slide3.png',
            'images/slide4.png',
        ];

        AppSetting::set('home_hero_slides', json_encode($defaultSlides));

        return redirect()->route('admin.page_settings.index', ['tab' => 'beranda'])
            ->with('success', 'Slider foto berhasil di-reset ke 4 foto bawaan sistem!');
    }

    /**
     * Simpan pembaruan konten Tentang Kami.
     */
    public function updateTentang(Request $request)
    {
        $validated = $request->validate([
            'about_hero_badge' => 'nullable|string|max:100',
            'about_hero_title' => 'required|string|max:255',
            'about_hero_col1' => 'required|string',
            'about_hero_col2' => 'required|string',
            'about_hero_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            'about_profile_badge' => 'nullable|string|max:100',
            'about_profile_title' => 'required|string|max:255',
            'about_profile_desc' => 'required|string',
            'about_profile_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'about_profile_img_caption' => 'nullable|string|max:255',
            'about_profile_img_location' => 'nullable|string|max:255',

            'about_philosophy_badge' => 'nullable|string|max:100',
            'about_philosophy_title' => 'required|string|max:255',
        ]);

        // Simpan input teks
        $textFields = [
            'about_hero_badge', 'about_hero_title', 'about_hero_col1', 'about_hero_col2',
            'about_profile_badge', 'about_profile_title', 'about_profile_desc',
            'about_profile_img_caption', 'about_profile_img_location',
            'about_philosophy_badge', 'about_philosophy_title',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                AppSetting::set($field, $request->input($field));
            }
        }

        // Upload file gambar
        $imageFields = ['about_hero_bg', 'about_profile_img'];

        foreach ($imageFields as $imgField) {
            if ($request->hasFile($imgField)) {
                $oldPath = AppSetting::get($imgField);
                if ($oldPath && ! str_starts_with($oldPath, 'images/') && ! str_starts_with($oldPath, 'http')) {
                    Storage::disk('public')->delete($oldPath);
                }

                $storedPath = $request->file($imgField)->store('page_settings', 'public');
                AppSetting::set($imgField, $storedPath);
            }
        }

        return redirect()->route('admin.page_settings.index', ['tab' => 'tentang'])
            ->with('success', 'Konten Halaman Tentang Kami berhasil diperbarui!');
    }

    /**
     * Tambah tautan/website baru ke Menu Portal.
     */
    public function storePortalLink(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'target' => 'nullable|in:_blank,_self',
            'is_active' => 'nullable',
            'order' => 'nullable|integer|min:0',
        ]);

        $links = AppSetting::getPortalLinks(false);

        $newOrder = isset($validated['order']) && $validated['order'] !== ''
            ? (int) $validated['order']
            : count($links) + 1;

        $newLink = [
            'id' => 'portal_'.time().'_'.bin2hex(random_bytes(2)),
            'name' => trim($validated['name']),
            'url' => trim($validated['url']),
            'target' => $validated['target'] ?? '_blank',
            'is_active' => $request->has('is_active'),
            'order' => $newOrder,
        ];

        $links[] = $newLink;
        AppSetting::savePortalLinks($links);

        return redirect()->route('admin.page_settings.index', ['tab' => 'portal'])
            ->with('success', 'Website "'.$newLink['name'].'" berhasil ditambahkan ke menu Portal Header!');
    }

    /**
     * Perbarui tautan/website di Menu Portal.
     */
    public function updatePortalLink(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'target' => 'nullable|in:_blank,_self',
            'is_active' => 'nullable',
            'order' => 'nullable|integer|min:0',
        ]);

        $links = AppSetting::getPortalLinks(false);
        $found = false;

        foreach ($links as &$link) {
            if ($link['id'] === $id) {
                $link['name'] = trim($validated['name']);
                $link['url'] = trim($validated['url']);
                $link['target'] = $validated['target'] ?? '_blank';
                $link['is_active'] = $request->has('is_active');
                if (isset($validated['order'])) {
                    $link['order'] = (int) $validated['order'];
                }
                $found = true;
                break;
            }
        }
        unset($link);

        if ($found) {
            AppSetting::savePortalLinks($links);

            return redirect()->route('admin.page_settings.index', ['tab' => 'portal'])
                ->with('success', 'Menu Portal berhasil diperbarui!');
        }

        return redirect()->route('admin.page_settings.index', ['tab' => 'portal'])
            ->with('error', 'Menu Portal tidak ditemukan.');
    }

    /**
     * Hapus tautan website dari Menu Portal.
     */
    public function deletePortalLink(string $id)
    {
        $links = AppSetting::getPortalLinks(false);
        $filtered = array_values(array_filter($links, fn ($item) => $item['id'] !== $id));

        AppSetting::savePortalLinks($filtered);

        return redirect()->route('admin.page_settings.index', ['tab' => 'portal'])
            ->with('success', 'Tautan website berhasil dihapus dari Menu Portal.');
    }

    /**
     * Toggle status aktif / non-aktif tautan Menu Portal.
     */
    public function togglePortalLink(string $id)
    {
        $links = AppSetting::getPortalLinks(false);
        $statusText = '';

        foreach ($links as &$link) {
            if ($link['id'] === $id) {
                $link['is_active'] = empty($link['is_active']);
                $statusText = $link['is_active'] ? 'diaktifkan' : 'dinonaktifkan';
                break;
            }
        }
        unset($link);

        AppSetting::savePortalLinks($links);

        return redirect()->route('admin.page_settings.index', ['tab' => 'portal'])
            ->with('success', 'Status menu Portal berhasil '.$statusText.'.');
    }

    /**
     * Reset daftar Menu Portal ke default 3 website awal.
     */
    public function resetPortalLinks()
    {
        AppSetting::savePortalLinks(AppSetting::defaultPortalLinks());

        return redirect()->route('admin.page_settings.index', ['tab' => 'portal'])
            ->with('success', 'Menu Portal berhasil dikembalikan ke pengaturan awal (3 website).');
    }
}
