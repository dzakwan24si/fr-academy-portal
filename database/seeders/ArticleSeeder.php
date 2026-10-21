<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan author default
        $author = User::where('email', 'admin@fr-academy.com')->first()
            ?? User::first();
        $authorId = $author ? $author->id : null;

        // 2. Siapkan direktori storage untuk featured image
        if (! Storage::disk('public')->exists('articles')) {
            Storage::disk('public')->makeDirectory('articles');
        }

        // Helper untuk menyalin gambar contoh dari public/images ke storage/articles
        $copySampleImage = function (string $sourceFilename, string $targetFilename): ?string {
            $sourcePath = public_path('images/' . $sourceFilename);
            if (File::exists($sourcePath)) {
                $targetRelative = 'articles/' . $targetFilename;
                $targetPath = storage_path('app/public/' . $targetRelative);
                if (! File::exists($targetPath)) {
                    File::copy($sourcePath, $targetPath);
                }
                return $targetRelative;
            }
            return null;
        };

        $imgSlide2 = $copySampleImage('slide2.png', 'article-mt-program.png');
        $imgSawit = $copySampleImage('sawit.png', 'article-digital-plantation.png');
        $imgWelcome = $copySampleImage('welcomefr.jpg', 'article-leadership-skills.png');
        $imgSlide4 = $copySampleImage('slide4.png', 'article-competency-schedule.png');
        $imgSlide5 = $copySampleImage('slide5.png', 'article-alumni-success.png');
        $imgPillar = $copySampleImage('3pillar.png', 'article-hse-safety.png');

        // 3. Buat Kategori
        $categoriesData = [
            ['name' => 'Kegiatan & Pelatihan', 'slug' => 'kegiatan-pelatihan'],
            ['name' => 'Pengumuman', 'slug' => 'pengumuman'],
            ['name' => 'Tips & Pengembangan Diri', 'slug' => 'tips-pengembangan-diri'],
            ['name' => 'Teknologi & Inovasi', 'slug' => 'teknologi-inovasi'],
            ['name' => 'Kisah Sukses Alumni', 'slug' => 'kisah-sukses-alumni'],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                ['name' => $catData['name']]
            );
        }

        // 4. Data Artikel
        $articles = [
            [
                'title' => 'Pembukaan Program Management Trainee (FAT & MAT) Angkatan Baru di FR Academy',
                'slug' => 'pembukaan-program-management-trainee-fat-mat-angkatan-baru',
                'category_slug' => 'kegiatan-pelatihan',
                'featured_image' => $imgSlide2,
                'tags' => ['Management Trainee', 'FAT', 'MAT', 'Pelatihan Reguler', 'Learning Center'],
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'content' => <<<HTML
<p class="lead"><strong>First Resources Academy</strong> resmi menyambut puluhan talenta muda terpilih dari berbagai universitas terkemuka di seluruh Indonesia untuk mengikuti pembukaan Program Management Trainee angkatan terbaru.</p>

<p>Program Management Trainee (MT) yang terdiri dari <em>Field Assistant Trainee (FAT)</em> dan <em>Mill Assistant Trainee (MAT)</em> ini dirancang untuk mencetak calon pemimpin operasional kebun dan pabrik kelapa sawit yang tangguh, berintegritas tinggi, dan berorientasi pada hasil nyata.</p>

<h3>Fokus Pembelajaran dan Kurikulum Unggulan</h3>
<p>Selama menjalani program intensif di Learning Center dan on-the-job training di unit kebun/pabrik operasional, para peserta akan dibekali dengan kurikulum komprehensif yang meliputi:</p>
<ul>
    <li><strong>Agronomi & Manajemen Perkebunan:</strong> Pemahaman menyeluruh mengenai kultur teknis kelapa sawit, pemupukan presisi, panen, dan pengelolaan lahan berkelanjutan.</li>
    <li><strong>Operasional Pabrik Kelapa Sawit (PKS):</strong> Efisiensi pengolahan TBS, pemeliharaan mesin stasiun pengolahan, dan standar kualitas CPO & Kernel.</li>
    <li><strong>Leadership & People Management:</strong> Pembentukan karakter kepemimpinan, komunikasi efektif, supervisi mandor, dan kedisiplinan kerja tim di lapangan.</li>
    <li><strong>Keselamatan Kerja (HSE):</strong> Penerapan standar K3 zero-accident di setiap lini aktivitas kerja.</li>
</ul>

<p>Manajemen First Resources Academy berharap para peserta dapat memanfaatkan kesempatan emas ini dengan sungguh-sungguh demi mewujudkan operasional agribisnis kelas dunia yang berkelanjutan.</p>
HTML
            ],
            [
                'title' => 'Penerapan Digitalisasi dan Precision Agriculture dalam Operasional Kebun Kelapa Sawit Modern',
                'slug' => 'penerapan-digitalisasi-dan-precision-agriculture-dalam-operasional-kebun-kelapa-sawit',
                'category_slug' => 'teknologi-inovasi',
                'featured_image' => $imgSawit,
                'tags' => ['Digitalisasi', 'Precision Agriculture', 'GIS', 'Agronomi', 'Inovasi'],
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'content' => <<<HTML
<p class="lead">Transformasi digital kini bukan lagi sekadar wacana di industri perkebunan kelapa sawit, melainkan instrumen esensial dalam mendorong produktivitas dan efisiensi biaya operasional kebun.</p>

<p>Melalui implementasi <strong>Precision Agriculture</strong>, First Resources mengintegrasikan analisis spasial berbasis Sistem Informasi Geografis (GIS), pemetaan udara berkala menggunakan drone, serta sensor lapangan untuk memantau kesehatan tanaman dan dinamika hara tanah secara real-time.</p>

<h3>Manfaat Utama Digitalisasi Kebun</h3>
<ul>
    <li><strong>Akurasi Pemupukan dan Nutrisi:</strong> Penentuan dosis dan rekomendasi pupuk berbasis data titik koordinat blok spesifik, menghindari pemborosan bahan dan menjaga kesuburan tanah.</li>
    <li><strong>Deteksi Dini Hama & Penyakit:</strong> Analisis citra vegetasi (NDVI) memudahkan tim proteksi tanaman mendeteksi serangan hama seperti ulat api atau gejala ganoderma sebelum menyebar luas.</li>
    <li><strong>Monitoring Kinerja Panen Harian:</strong> Pelaporan digital memudahkan asisten dan mandor melacak restan TBS, rotasi panen, serta kehadiran tenaga kerja secara akurat.</li>
</ul>

<p>Melalui modul pelatihan di FR Academy, para asisten lapangan dilatih agar mahir mengoperasikan perangkat digital dan memanfaatkan analitik data sebagai dasar pengambilan keputusan operasional harian.</p>
HTML
            ],
            [
                'title' => '5 Keterampilan Kepemimpinan Wajib bagi Asisten Kebun dan Pabrik Kelapa Sawit',
                'slug' => '5-keterampilan-kepemimpinan-wajib-bagi-asisten-kebun-dan-pabrik-kelapa-sawit',
                'category_slug' => 'tips-pengembangan-diri',
                'featured_image' => $imgWelcome,
                'tags' => ['Kepemimpinan', 'Softskill', 'Supervisi', 'Pengembangan Diri', 'Manajemen Tim'],
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'content' => <<<HTML
<p class="lead">Menjadi seorang Asisten Lapangan atau Asisten Pabrik di lingkungan perkebunan tidak hanya membutuhkan kompetensi teknis agronomis atau permesinan, tetapi juga kecakapan kepemimpinan yang matang.</p>

<p>Berikut adalah 5 keterampilan kepemimpinan utama yang ditekankan dalam modul Softskill First Resources Academy:</p>

<h3>1. Komunikasi yang Jelas dan Asertif</h3>
<p>Asisten harus mampu menyampaikan instruksi kerja kerja harian (morning briefing) kepada mandor dan pekerja lapangan dengan lugas, tegas, namun tetap menghargai martabat setiap anggota tim.</p>

<h3>2. Problem Solving di Garis Depan</h3>
<p>Kondisi lapangan yang dinamis seperti cuaca ekstrem, kendala transportasi buah, atau trouble mesin menuntut kemampuan berpikir cepat dan solusi praktis tanpa mengorbankan standar operasional prosedur (SOP).</p>

<h3>3. Empati dan Manajemen Konflik</h3>
<p>Mengelola ratusan tenaga kerja dengan latar belakang budaya yang beragam memerlukan kepekaan emosional, kemampuan mendengar secara aktif, dan keadilan dalam menyelesaikan perselisihan.</p>

<h3>4. Keteladanan dan Integritas (Lead by Example)</h3>
<p>Disiplin waktu kehadiran di apel pagi, ketaatan pada SOP keselamatan kerja, dan transparansi laporan kerja adalah pondasi reputasi seorang pemimpin lapangan yang dihormati.</p>

<h3>5. Pengembangan Talenta Bawahan (Coaching & Mentoring)</h3>
<p>Pemimpin sejati bukan yang memiliki banyak pengikut, melainkan yang mampu membina mandor dan staf bawahannya agar tumbuh mandiri dan memiliki kinerja prima.</p>
HTML
            ],
            [
                'title' => 'Jadwal dan Panduan Pelaksanaan Uji Kompetensi Operasional Asisten Semester Ini',
                'slug' => 'jadwal-dan-panduan-pelaksanaan-uji-kompetensi-operasional-asisten-semester-ini',
                'category_slug' => 'pengumuman',
                'featured_image' => $imgSlide4,
                'tags' => ['Uji Kompetensi', 'Sertifikasi', 'Pengumuman', 'Diklat', 'Asesmen'],
                'status' => 'published',
                'published_at' => now()->subDays(14),
                'content' => <<<HTML
<p class="lead">Diberitahukan kepada seluruh Asisten Lapangan, Asisten Traksi, dan Asisten Pabrik di lingkungan First Resources Group bahwa rangkaian <strong>Uji Kompetensi dan Asesmen Kinerja Operasional</strong> akan segera diselenggarakan.</p>

<p>Asesmen ini bertujuan untuk mengukur standarisasi kecakapan teknis, pemahaman regulasi industri, dan kesiapan karier profesional masing-masing personil sesuai jenjang kompetensi perusahaan.</p>

<h3>Tahapan Pelaksanaan Uji Kompetensi</h3>
<ol>
    <li><strong>Tes Teori dan Studi Kasus Berbasis Web:</strong> Dilaksanakan serentak melalui portal e-learning/ujian FR Academy dengan materi seputar kultur teknis, SOP, budget, dan manajemen operasional.</li>
    <li><strong>Verifikasi Dokumen Kinerja Portofolio:</strong> Evaluasi pencapaian target kerja unit, audit kepatuhan agronomi, dan laporan perbaikan berkelanjutan (Continuous Improvement).</li>
    <li><strong>Wawancara Panel Asesor & Pimpinan Wilayah:</strong> Sesi pendalaman kepemimpinan operasional bersama jajaran Regional Manager dan Tim Asesor Akademi.</li>
</ol>

<p>Seluruh peserta diharapkan mempersiapkan diri secara optimal. Untuk jadwal detail per regional dan tata tertib ujian, silakan unduh surat edaran resmi pada menu portal internal.</p>
HTML
            ],
            [
                'title' => 'Dari Trainee Menjadi Estate Manager: Kisah Inspiratif Alumni FR Academy',
                'slug' => 'dari-trainee-menjadi-estate-manager-kisah-inspiratif-alumni-fr-academy',
                'category_slug' => 'kisah-sukses-alumni',
                'featured_image' => $imgSlide5,
                'tags' => ['Kisah Sukses', 'Alumni', 'Inspirasi', 'Karier', 'Estate Manager'],
                'status' => 'published',
                'published_at' => now()->subDays(20),
                'content' => <<<HTML
<p class="lead">Perjalanan karier di industri kelapa sawit adalah proses penempaan ketangguhan mental, dedikasi tanpa henti, dan komitmen terhadap keunggulan operasional.</p>

<p>Hal inilah yang dibuktikan oleh salah satu lulusan terbaik Field Assistant Trainee (FAT) angkatan pertama FR Academy, yang kini dipercaya menjabat sebagai Estate Manager memimpin ribuan hektar areal konsesi produktif.</p>

<h3>Disiplin di Learning Center Membentuk Karakter Tangguh</h3>
<p><em>"Saat pertama kali masuk akademi, tantangan terberat adalah adaptasi fisik dan kedisiplinan semi-militer yang ketat. Namun justru pembinaan mental itulah yang menjadi modal utama saat kami menghadapi tantangan nyata di lapangan,"</em> tuturnya.</p>

<p>Ia menekankan bahwa keberhasilan memimpin estate tidak diraih dalam semalam. Konsistensi dalam menjaga kualitas pemeliharaan tanaman, hubungan harmonis dengan masyarakat sekitar, serta perhatian tulus terhadap kesejahteraan pekerja adalah kunci pencapaian target produksi yang berkelanjutan.</p>

<p>Semoga kisah ini menjadi pemicu semangat bagi seluruh peserta trainee yang saat ini tengah menempuh pendidikan di FR Academy.</p>
HTML
            ],
            [
                'title' => 'Komitmen Keselamatan Kerja: Penerapan Budaya Zero Accident di Pabrik Kelapa Sawit',
                'slug' => 'komitmen-keselamatan-kerja-penerapan-budaya-zero-accident-di-pabrik-kelapa-sawit',
                'category_slug' => 'kegiatan-pelatihan',
                'featured_image' => $imgPillar,
                'tags' => ['HSE', 'K3', 'Zero Accident', 'Pabrik Kelapa Sawit', 'Safety First'],
                'status' => 'published',
                'published_at' => now()->subDays(28),
                'content' => <<<HTML
<p class="lead">Keselamatan dan kesehatan kerja (K3) adalah prioritas non-negosiasi dalam seluruh lini operasional First Resources, khususnya pada lingkungan pabrik kelapa sawit (PKS) yang memiliki risiko operasional tinggi.</p>

<p>Melalui program pelatihan refreshment K3 yang diselenggarakan secara berkelanjutan oleh FR Academy, seluruh karyawan dan staf dibekali dengan pemahaman mendalam tentang identifikasi bahaya, penilaian risiko, dan mitigasi kecelakaan kerja.</p>

<h3>Prinsip Utama Penerapan HSE di Lingkungan Pabrik</h3>
<ul>
    <li><strong>Kepatuhan APD Wajib:</strong> Penggunaan helm pengaman, sepatu safety, kacamata pelindung, dan ear plug di area stasiun produksi yang bising atau bersuhu tinggi.</li>
    <li><strong>Sistem Lock Out Tag Out (LOTO):</strong> Penerapan prosedur isolasi energi mekanikal dan elektrikal secara ketat saat pelaksanaan perbaikan atau pemeliharaan mesin.</li>
    <li><strong>Kesiapsiagaan Tanggap Darurat:</strong> Simulasi evakuasi berkala, kesiapan regu pemadam kebakaran internal, serta pemeliharaan sistem hidran dan apar.</li>
    <li><strong>Safety Talk Rutin:</strong> Pengingat keselamatan kerja setiap pergantian shift untuk memastikan fokus dan kewaspadaan seluruh operator.</li>
</ul>

<p>Menciptakan lingkungan kerja yang aman dan sehat bukan hanya kewajiban regulasi, tetapi wujud kepedulian nyata perusahaan terhadap setiap insan pekerja.</p>
HTML
            ],
        ];

        foreach ($articles as $artData) {
            $category = $categories[$artData['category_slug']] ?? null;

            Article::updateOrCreate(
                ['slug' => $artData['slug']],
                [
                    'title' => $artData['title'],
                    'content' => $artData['content'],
                    'category_id' => $category?->id,
                    'tags' => $artData['tags'],
                    'featured_image' => $artData['featured_image'],
                    'status' => $artData['status'],
                    'published_at' => $artData['published_at'],
                    'author_id' => $authorId,
                ]
            );
        }
    }
}
