<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
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

        return view('admin.page_settings.index', compact('tab', 'beranda', 'tentang', 'slides'));
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
        ]);

        // Simpan input teks
        $textFields = [
            'home_hero_badge', 'home_hero_title', 'home_hero_tagline', 'home_hero_desc',
            'home_hero_btn1_text', 'home_hero_btn1_url', 'home_hero_btn2_text', 'home_hero_btn2_url',
            'home_vision_badge', 'home_vision_title', 'home_vision_desc',
            'home_vision_p1_title', 'home_vision_p1_desc', 'home_vision_p2_title', 'home_vision_p2_desc',
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

        // Upload foto Visi
        $visionImages = ['home_vision_img_1', 'home_vision_img_2'];
        foreach ($visionImages as $imgField) {
            if ($request->hasFile($imgField)) {
                $oldPath = AppSetting::get($imgField);
                if ($oldPath && ! str_starts_with($oldPath, 'images/') && ! str_starts_with($oldPath, 'http')) {
                    Storage::disk('public')->delete($oldPath);
                }

                $storedPath = $request->file($imgField)->store('page_settings', 'public');
                AppSetting::set($imgField, $storedPath);
            }
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
}
