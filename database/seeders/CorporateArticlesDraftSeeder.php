<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CorporateArticlesDraftSeeder extends Seeder
{
    /**
     * Run the database seeds for 9 corporate magazine draft articles.
     */
    public function run(): void
    {
        // 1. Dapatkan author default (Admin atau User pertama)
        $author = User::where('email', 'admin@fr-academy.com')->first() ?? User::first();
        $authorId = $author ? $author->id : null;

        // 2. Pastikan Kategori ada
        $category = Category::firstOrCreate(
            ['slug' => 'kegiatan-pelatihan'],
            ['name' => 'Kegiatan & Pelatihan']
        );

        // 3. Kumpulan 9 Draf Artikel
        $articles = [
            // ==========================================
            // ARTIKEL 1: Admin Critical Program
            // ==========================================
            [
                'title' => 'Bangkit Lebih Kuat: Dari Assessment Menuju Performa Unggul',
                'slug' => 'bangkit-lebih-kuat-dari-assessment-menuju-performa-unggul',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['Admin Critical Program', 'KTU', 'Kasi', 'Assessment', 'PDCA', '7 QC Tools', 'FR Academy', 'Continuous Improvement'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Melalui rangkaian pembelajaran, evaluasi, dan pendampingan yang terstruktur, Admin Critical Program membantu KTU dan Kasi meningkatkan kompetensi teknis, kemampuan analitis, serta kesiapan menghadapi tantangan operasional secara lebih profesional.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Bagaimana jika sebuah hasil assessment yang belum memuaskan bukan dianggap sebagai kegagalan, melainkan titik awal untuk menjadi lebih baik?"
    </p>
</blockquote>

<p>Di dunia kerja yang terus berkembang, setiap individu memiliki ruang untuk belajar dan bertumbuh. Assessment bukanlah alat untuk memberi label, melainkan kompas yang membantu perusahaan mengenali potensi sekaligus area yang masih perlu diperkuat. Dengan memahami titik awal tersebut, langkah pengembangan dapat dilakukan secara lebih terarah dan berdampak nyata.</p>

<p>Berangkat dari semangat tersebut, <strong>FR Academy</strong> menyelenggarakan <strong>Admin Critical Program</strong> sebagai tindak lanjut atas hasil <em>Assessment KTU dan Kasi Tahun 2025</em>. Program ini secara khusus ditujukan bagi peserta yang masih berada dalam kategori merah, dengan harapan mampu meningkatkan kompetensi hingga mencapai standar yang diharapkan perusahaan.</p>

<p>Dilaksanakan mulai <strong>5 Januari 2026</strong> secara daring melalui Zoom Meeting, program ini melibatkan peserta dari tiga region, yaitu <strong>Riau</strong> (1 KTU dan 5 Kasi), <strong>Kalimantan Barat</strong> (3 KTU dan 4 Kasi), serta <strong>Kalimantan Timur</strong> (1 KTU). Selama hampir satu bulan, peserta mengikuti serangkaian kegiatan yang dirancang untuk memperkuat kemampuan analisis, pemecahan masalah, penguasaan teknis, hingga kesiapan menghadapi tantangan pekerjaan.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Pengembangan yang Dimulai dari Data</h3>
<p>Salah satu prinsip penting dalam pengembangan sumber daya manusia adalah memberikan intervensi yang tepat berdasarkan kebutuhan nyata. Oleh karena itu, hasil assessment tidak berhenti sebagai laporan evaluasi, tetapi diterjemahkan menjadi program pembelajaran yang sistematis.</p>
<p>Admin Critical Program dirancang agar setiap peserta memperoleh kesempatan memperbaiki kompetensi melalui kombinasi pelatihan, pengukuran ulang, presentasi proyek, serta evaluasi berkelanjutan. Pendekatan ini memastikan bahwa peningkatan kompetensi dapat diukur secara objektif dan memberikan dampak nyata terhadap kinerja.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Belajar Memecahkan Masalah dengan Pendekatan PDCA dan 7 QC Tools</h3>
<p>Salah satu sesi utama dalam program ini adalah pelatihan <strong>Problem Solving menggunakan metode PDCA (Plan–Do–Check–Act) dan 7 QC Tools</strong> yang disampaikan oleh <strong>Pinondang Simanungkalit</strong> pada <strong>10 Januari 2026</strong>.</p>
<p>Melalui sesi ini, peserta dibekali pemahaman mengenai pentingnya pendekatan sistematis dalam menyelesaikan permasalahan operasional. Tidak semua masalah dapat diselesaikan dengan pengalaman semata; diperlukan kemampuan mengidentifikasi akar penyebab, menganalisis data, merancang tindakan perbaikan, serta mengevaluasi efektivitas solusi yang telah diterapkan.</p>
<p>Peserta mempelajari bagaimana menggunakan berbagai alat analisis kualitas untuk mengidentifikasi penyebab utama suatu masalah, menyusun rencana perbaikan, hingga menjalankan proyek peningkatan kinerja secara berkelanjutan. Kemampuan ini menjadi bekal penting bagi KTU dan Kasi yang setiap hari dihadapkan pada berbagai keputusan operasional yang memerlukan ketelitian dan analisis yang akurat.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Mengukur Kembali Potensi dan Kompetensi</h3>
<p>Sebagai bagian dari proses pengembangan, peserta juga mengikuti <strong>re-test psikotes</strong> yang dilaksanakan secara daring pada <strong>9 Januari 2026</strong>.</p>
<p>Pengukuran ini menggunakan <strong>CFIT (Culture Fair Intelligence Test)</strong> untuk melihat kemampuan inteligensi serta <strong>PAPI Kostick</strong> untuk memetakan karakteristik kepribadian yang berkaitan dengan perilaku kerja. Seluruh proses berlangsung secara daring dengan pengawasan dari tim <em>TO Admin & Recruitment Officer</em>.</p>
<p>Selain aspek psikologis, peserta juga mengikuti <strong>re-test teknis</strong> pada <strong>27 dan 29 Januari 2026</strong>. Evaluasi ini bertujuan mengukur peningkatan kompetensi teknis dibandingkan hasil assessment tahun sebelumnya sehingga perusahaan memperoleh gambaran objektif mengenai perkembangan setiap peserta.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Belajar dari Angka Melalui Analisis Biaya</h3>
<p>Rangkaian program juga mencakup <strong>Presentasi Analisa Biaya Tahun 2025</strong>, yang menjadi wadah bagi peserta untuk menunjukkan kemampuan analitis mereka.</p>
<p>Presentasi pertama dilaksanakan pada <strong>17 Januari 2026</strong> untuk peserta dari Region Kalimantan Barat dengan evaluator <strong>Pinondang Simanungkalit</strong> dan <strong>Monang</strong>.</p>
<p>Selanjutnya, pada <strong>21 Januari 2026</strong>, peserta dari Riau, Kalimantan Barat, dan Kalimantan Timur mempresentasikan hasil analisis biaya di hadapan tim evaluator yang terdiri dari <strong>Pinondang Simanungkalit, Agung Trisaksono, Monang, dan Benyamin</strong>.</p>
<p>Melalui kegiatan ini, peserta tidak hanya diminta memahami angka-angka dalam laporan, tetapi juga mampu menjelaskan penyebab terjadinya biaya, mengidentifikasi peluang efisiensi, serta memberikan rekomendasi perbaikan yang dapat diterapkan di unit kerja masing-masing.</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Masalah yang dianalisis dengan benar akan menghasilkan solusi yang lebih tepat dan berdampak bagi perusahaan."
    </p>
</blockquote>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Antusiasme untuk Terus Bertumbuh</h3>
<p>Meskipun seluruh rangkaian kegiatan dilaksanakan secara daring, semangat peserta tetap terlihat dalam setiap sesi. Diskusi berlangsung aktif, pertanyaan muncul dari berbagai region, dan presentasi menjadi ruang untuk saling belajar dari pengalaman masing-masing. Berbagai masukan dari evaluator membantu peserta melihat permasalahan dari sudut pandang yang lebih luas dan strategis.</p>
<p>Salah seorang peserta mengungkapkan bahwa program ini tidak hanya meningkatkan pemahaman teknis, tetapi juga mengubah cara berpikir dalam menganalisis masalah sebelum mengambil keputusan. Peserta lainnya menyampaikan bahwa proses evaluasi yang dilakukan secara bertahap memberikan motivasi untuk terus memperbaiki diri dan meningkatkan kualitas pekerjaan.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Dampak bagi Operasional Perusahaan</h3>
<p>Admin Critical Program merupakan investasi strategis perusahaan dalam membangun kualitas sumber daya manusia, khususnya pada fungsi administrasi dan pengelolaan operasional. Melalui peningkatan kompetensi para KTU dan Kasi, perusahaan diharapkan memperoleh berbagai manfaat, antara lain:</p>
<ul class="list-disc pl-6 space-y-2 my-4">
    <li>Meningkatnya kemampuan analisis dan pemecahan masalah operasional;</li>
    <li>Pengambilan keputusan yang lebih berbasis data dan fakta di lapangan;</li>
    <li>Peningkatan akurasi pengelolaan administrasi dan pengendalian biaya;</li>
    <li>Kemampuan teknis yang lebih baik dibandingkan hasil assessment sebelumnya;</li>
    <li>Terciptanya budaya belajar, evaluasi, dan perbaikan berkelanjutan (<em>continuous improvement</em>).</li>
</ul>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Setiap Evaluasi Adalah Kesempatan untuk Tumbuh</h3>
<p>Perjalanan menuju profesionalisme bukanlah proses yang instan. Dibutuhkan keberanian untuk menerima umpan balik, kemauan untuk belajar, dan komitmen untuk terus memperbaiki diri.</p>
<p>Melalui Admin Critical Program, FR Academy menegaskan bahwa setiap hasil evaluasi merupakan awal dari proses pengembangan, bukan akhir dari sebuah perjalanan. Karena organisasi yang hebat dibangun oleh individu-individu yang terus belajar. Dan setiap langkah kecil menuju perbaikan hari ini akan menjadi fondasi bagi kinerja yang lebih unggul di masa depan.</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Assessment bukan akhir dari perjalanan, tetapi titik awal untuk menjadi versi terbaik dari diri kita. Budaya belajar dan evaluasi yang berkelanjutan adalah fondasi lahirnya organisasi yang adaptif dan unggul."
    </p>
</blockquote>
HTML
            ],

            // ==========================================
            // ARTIKEL 2: Pelatihan Pengelolaan TPA
            // ==========================================
            [
                'title' => 'Menjaga yang Paling Berharga: Meningkatkan Kualitas Pengelolaan TPA untuk Keluarga Karyawan',
                'slug' => 'menjaga-yang-paling-berharga-meningkatkan-kualitas-pengelolaan-tpa',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['TPA', 'Penitipan Anak', 'Pamong TPA', 'Krani Afdeling', 'PT SIR Lukut', 'Work Life Balance', 'FR Academy', 'Kesejahteraan Karyawan'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Melalui pelatihan Pengelolaan Tempat Penitipan Anak (TPA), para pamong dan krani afdeling memperkuat pemahaman dalam menciptakan lingkungan pengasuhan yang aman, nyaman, dan mendukung tumbuh kembang anak-anak keluarga karyawan.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Ketika para orang tua bekerja di lapangan untuk menjaga produktivitas perusahaan, siapa yang menjaga buah hati mereka?"
    </p>
</blockquote>

<p>Pertanyaan sederhana ini menyimpan makna yang sangat besar. Di balik aktivitas operasional yang berlangsung setiap hari, terdapat ribuan keluarga yang menjadi bagian dari kehidupan perusahaan. Bagi para karyawan yang memiliki anak usia dini, ketenangan dalam bekerja sering kali berawal dari satu hal penting: keyakinan bahwa anak mereka berada dalam lingkungan yang aman, sehat, dan mendapatkan pengasuhan yang baik.</p>

<p>Di sinilah peran <strong>Tempat Penitipan Anak (TPA)</strong> menjadi sangat strategis. Tidak hanya berfungsi sebagai tempat penitipan, TPA merupakan ruang tumbuh bagi anak-anak sekaligus bentuk nyata kepedulian perusahaan terhadap kesejahteraan keluarga karyawan.</p>

<p>Sebagai upaya meningkatkan kualitas layanan TPA, FR Academy menyelenggarakan pelatihan <strong>Pengelolaan Tempat Penitipan Anak (TPA)</strong> pada <strong>25 Mei 2026</strong> di <strong>PT SIR Lukut</strong>. Kegiatan yang diikuti oleh sembilan peserta yang terdiri dari pamong TPA dan krani afdeling ini menghadirkan <strong>Satri G. Wibowo</strong> sebagai narasumber untuk memberikan refreshment mengenai pengelolaan TPA yang lebih efektif, aman, dan berorientasi pada kebutuhan anak.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">TPA: Lebih dari Sekadar Tempat Penitipan</h3>
<p>Dalam lingkungan perkebunan, keberadaan TPA memiliki fungsi yang jauh lebih luas dibandingkan sekadar fasilitas pendukung. TPA merupakan salah satu bentuk dukungan perusahaan terhadap keseimbangan antara kehidupan kerja dan keluarga (<em>work-life balance</em>). Kehadiran fasilitas ini membantu orang tua menjalankan tanggung jawab pekerjaan dengan lebih tenang karena anak-anak mereka mendapatkan pengawasan dan pendampingan yang memadai selama jam kerja berlangsung.</p>
<p>Namun, pengelolaan TPA yang baik membutuhkan kompetensi, kepedulian, dan kesadaran dari seluruh pihak yang terlibat. Karena itu, peningkatan kapasitas pamong dan petugas yang mengelola TPA menjadi bagian penting dalam menjaga kualitas layanan yang diberikan.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Membangun Awareness dalam Pengelolaan TPA</h3>
<p>Salah satu tujuan utama pelatihan ini adalah meningkatkan <em>awareness</em> atau kesadaran peserta terhadap pentingnya pengelolaan TPA yang sesuai dengan kebutuhan tumbuh kembang anak. Peserta diajak memahami bahwa setiap aktivitas yang dilakukan di TPA memiliki dampak terhadap kenyamanan, kesehatan, keamanan, dan perkembangan anak-anak yang berada dalam pengasuhan mereka.</p>
<p>Materi pelatihan membahas berbagai aspek penting, mulai dari pengawasan anak, kebersihan lingkungan, keselamatan fasilitas, kedisiplinan administrasi, hingga komunikasi yang baik dengan orang tua. Kualitas pengelolaan TPA tidak hanya ditentukan oleh fasilitas fisik, tetapi juga oleh kualitas perhatian dan kasih sayang yang diberikan kepada setiap anak.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Lingkungan yang Aman Membentuk Pengalaman Positif Anak</h3>
<p>Dalam dunia pendidikan anak usia dini, lingkungan yang aman dan nyaman merupakan fondasi penting bagi perkembangan fisik, emosional, dan sosial anak. Karena itu, peserta mendapatkan penyegaran mengenai pentingnya menjaga kebersihan area TPA, memastikan fasilitas bermain dalam kondisi aman, serta melakukan pengawasan yang konsisten terhadap aktivitas anak-anak.</p>
<p>Pelatihan juga menekankan pentingnya mengenali kebutuhan dasar anak, seperti kebutuhan akan perhatian, rasa aman, interaksi sosial yang positif, serta stimulasi yang mendukung perkembangan mereka.</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "TPA yang baik bukan hanya tempat menitipkan anak, tetapi tempat menumbuhkan rasa aman bagi keluarga. Ketika anak-anak merasa aman dan nyaman, orang tua dapat bekerja dengan lebih fokus dan tenang."
    </p>
</blockquote>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Komunikasi dan Kepedulian sebagai Kunci Pengasuhan</h3>
<p>Selain aspek teknis pengelolaan, pelatihan juga menyoroti pentingnya komunikasi yang baik antara pamong, krani afdeling, dan orang tua. Ketika komunikasi berjalan efektif, berbagai informasi terkait kondisi anak, kebutuhan khusus, maupun perkembangan harian dapat tersampaikan dengan baik. Hal ini akan membantu menciptakan hubungan yang lebih harmonis antara pengelola TPA dan keluarga karyawan.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Mendukung Produktivitas Melalui Kepedulian terhadap Keluarga</h3>
<p>Keberhasilan operasional perusahaan tidak hanya ditentukan oleh mesin, sistem, atau proses kerja yang baik. Di balik seluruh pencapaian tersebut terdapat para karyawan yang bekerja dengan penuh dedikasi. Ketika perusahaan mampu menghadirkan lingkungan yang mendukung kesejahteraan keluarga, maka karyawan akan bekerja dengan lebih fokus, tenang, dan produktif.</p>
<p>Membangun perusahaan yang kuat tidak hanya tentang mengelola aset dan operasional, tetapi juga tentang menjaga dan mendukung generasi masa depan yang tumbuh di dalam keluarga besar perusahaan.</p>
HTML
            ],

            // ==========================================
            // ARTIKEL 3: K3 First Aid
            // ==========================================
            [
                'title' => 'Safety Starts with You: Membangun Budaya K3 dan Pertolongan Pertama yang Menyelamatkan',
                'slug' => 'safety-starts-with-you-membangun-budaya-k3-dan-first-aid',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['K3', 'First Aid', 'HSE', 'Keselamatan Kerja', 'Zero Accident', 'FR Academy', 'Sustainability'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Melalui Pelatihan K3 First Aid, para supervisi dan staf dibekali kemampuan mengenali risiko, mencegah kecelakaan, serta memberikan respons cepat dalam kondisi darurat demi menciptakan lingkungan kerja yang lebih aman dan produktif.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Beberapa menit pertama yang menentukan segalanya. Bayangkan seorang karyawan mengalami cedera saat bekerja di lapangan. Dalam situasi seperti itu, keputusan yang diambil dalam beberapa menit pertama sering kali menentukan tingkat keparahan dampak yang terjadi."
    </p>
</blockquote>

<p>Sebagai upaya memperkuat budaya keselamatan, <strong>FR Academy</strong> menyelenggarakan <strong>Pelatihan K3 First Aid</strong> di berbagai unit operasional Regional Kalimantan Barat, Kalimantan Timur, dan Riau sepanjang <strong>Februari hingga Mei 2026</strong>. Program ini diikuti oleh lebih dari <strong>500 peserta</strong> yang terdiri dari supervisi dan staf dari berbagai fungsi kerja.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Keselamatan Kerja Adalah Investasi, Bukan Sekadar Kepatuhan</h3>
<p>Dalam industri perkebunan dan pengolahan kelapa sawit, aktivitas operasional melibatkan berbagai potensi risiko, mulai dari penggunaan alat berat, aktivitas pemanenan, pekerjaan di pabrik, hingga pekerjaan administratif yang tetap memiliki risiko kesehatan dan keselamatan tertentu.</p>
<p>Karena itu, Keselamatan dan Kesehatan Kerja (K3) tidak boleh dipandang hanya sebagai kewajiban administratif atau pemenuhan regulasi. K3 merupakan investasi jangka panjang yang berkontribusi langsung terhadap produktivitas, keberlangsungan operasional, dan perlindungan terhadap aset terpenting perusahaan: sumber daya manusia.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Menjangkau Berbagai Regional untuk Memperkuat Budaya Safety</h3>
<p>Pelatihan K3 First Aid dilaksanakan secara bertahap di 15 unit operasional:</p>
<ul class="list-disc pl-6 space-y-2 my-4">
    <li><strong>Regional Kalimantan Barat:</strong> PT USP, PT FAPE, PT LS, PT KALE, PT SMP, dan PT MKSK.</li>
    <li><strong>Regional Kalimantan Timur:</strong> PT BDL, PT MKBB, PT BPPJ, dan PT CAKM.</li>
    <li><strong>Regional Riau:</strong> PT IGJA, PT SAL, PT CPK, PT SAGM, dan PT SIR Lukut.</li>
</ul>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">First Aid: Keterampilan Penyelamat Nyawa</h3>
<p>Peserta dibekali kemampuan dasar pertolongan pertama (First Aid), mencakup penanganan luka terbuka, perdarahan, pingsan, cedera akibat benturan, hingga prosedur evakuasi awal sebelum tenaga medis tiba di lokasi. Respon cepat pada <em>golden minutes</em> sangat krusial dalam meminimalkan dampak keparahan cedera.</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Keselamatan kerja bukanlah prioritas yang bisa berubah, melainkan nilai yang harus dijaga setiap hari. Target produksi yang tinggi tidak akan berarti tanpa keselamatan yang terjaga."
    </p>
</blockquote>

<p>Pelatihan ini dipandu oleh kolaborasi narasumber praktis dan medis: <strong>Suprayitno</strong> (Manager Sustainability) serta tim medis <strong>Dr. Januardo Abednego</strong> dan <strong>dr. Dian Takwa</strong>, menghasilkan pemahaman komprehensif menuju target <em>Zero Accident</em> perusahaan.</p>
HTML
            ],

            // ==========================================
            // ARTIKEL 4: Damkarlabun
            // ==========================================
            [
                'title' => 'First Resources Gelar Kesiapsiagaan Karhutla Melalui Pelatihan Damkarlabun',
                'slug' => 'first-resources-gelar-kesiapsiagaan-karhutla-melalui-pelatihan-damkarlabun',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['Damkarlabun', 'Karhutla', 'Kebakaran Hutan dan Lahan', 'Sustainability', 'APAR', 'Tanggap Darurat', 'FR Academy'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Meningkatkan kompetensi, kesiapsiagaan, dan koordinasi tim dalam mencegah serta menanggulangi kebakaran lahan dan kebun demi operasional yang aman, berkelanjutan, dan bebas insiden.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Pencegahan kebakaran yang terbaik adalah kesadaran yang hadir sebelum munculnya api. Dalam situasi darurat, pengetahuan yang benar dapat menyelamatkan nyawa, aset, dan lingkungan."
    </p>
</blockquote>

<p>Bayangkan sebuah titik api kecil yang luput dari perhatian. Dalam hitungan menit, kobaran api dapat meluas, mengancam keselamatan pekerja, merusak aset perusahaan, dan menimbulkan dampak lingkungan yang signifikan. Di industri perkebunan, ancaman kebakaran bukanlah sekadar kemungkinan, melainkan risiko yang harus selalu diantisipasi.</p>

<p>Kesadaran inilah yang menjadi landasan pelaksanaan <strong>Pelatihan Damkarlabun (Pemadam Kebakaran Lahan dan Kebun)</strong> yang diselenggarakan oleh <strong>FR Academy bekerja sama dengan Departemen Sustainability</strong> di berbagai unit operasional First Resources Group sepanjang <strong>Februari hingga April 2026</strong>. Kegiatan ini melibatkan lebih dari 300 peserta dari Regional Kalimantan Barat dan Regional Riau.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Mengenal Kelas Kebakaran dan Penggunaan APAR yang Tepat</h3>
<p>Pelatihan dipandu oleh <strong>Suprayitno, Manager Sustainability</strong>. Peserta dibekali pemahaman mengenai klasifikasi kebakaran berdasarkan sumber bahan bakarnya (material padat, cairan mudah terbakar, hingga peralatan listrik) dan pemilihan media pemadam yang tepat (powder, foam, atau CO₂). Kesalahan dalam memilih media pemadam justru dapat memperburuk situasi kebakaran.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Pencegahan Lebih Murah daripada Pemadaman</h3>
<p>Pesan utama yang ditekankan adalah bahwa keberhasilan pengendalian kebakaran dimulai jauh sebelum munculnya api. Pencegahan dilakukan melalui inspeksi rutin, patroli area rawan, pemeliharaan pompa dan selang pemadam, serta pelaporan dini. Setiap pekerja adalah garda terdepan keselamatan dan mata-telinga perusahaan dalam mendeteksi risiko kebakaran.</p>
HTML
            ],

            // ==========================================
            // ARTIKEL 5: Dump Truck Euro 4
            // ==========================================
            [
                'title' => 'Sebelum Kunci Diputar: Membangun Budaya P2H pada Era Dump Truck Euro 4',
                'slug' => 'sebelum-kunci-diputar-membangun-budaya-p2h-dump-truck-euro-4',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['Dump Truck', 'Euro 4', 'P2H', 'Traksi', 'AdBlue', 'FRLS Kalbar', 'Operational Excellence'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Melalui program Refreshment Pengoperasian & Perawatan Dump Truck Euro 4, para operator traksi memperkuat kompetensi inspeksi harian dan perawatan unit untuk mendukung keselamatan, keandalan, serta produktivitas operasional.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Lebih baik berhenti beberapa menit untuk melakukan P2H, daripada berhenti berjam-jam karena unit mengalami kerusakan di tengah jalan."
    </p>
</blockquote>

<p>Sebuah dump truck siap beroperasi. Mesin terlihat normal, bahan bakar tersedia, dan target pekerjaan sudah menanti. Namun, bagaimana jika terdapat kebocoran kecil yang tidak terdeteksi, indikator sistem emisi yang terabaikan, atau kondisi ban yang tidak layak jalan? Dalam operasional transportasi, gangguan besar sering kali berawal dari hal-hal kecil yang luput dari perhatian.</p>

<p>Learning Station Kalimantan Barat menyelenggarakan program <strong>Refreshment Pengoperasian & Perawatan Dump Truck Euro 4</strong> pada <strong>31 Januari 2026</strong> di FRLS Kalimantan Barat. Kegiatan ini diikuti oleh 20 operator traksi dan dipandu oleh trainer <strong>Roby H., Elekson Sinaga, dan Sukirman</strong>.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Teknologi Euro 4 dan Peran Vital AdBlue</h3>
<p>Kendaraan modern Euro 4 menggunakan sistem emisi Selective Catalytic Reduction (SCR) yang memerlukan cairan khusus <strong>AdBlue</strong> untuk mengubah emisi nitrogen oksida (NOx) berbahaya menjadi uap air dan nitrogen ramah lingkungan. Kelalaian dalam pengisian AdBlue atau pemakaian cairan yang tidak sesuai spesifikasi dapat menyebabkan performa mesin dibatasi secara otomatis (<em>engine derate</em>) bahkan unit berhenti beroperasi.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Prinsip Operator: Cek – Dengar – Lihat – Lapor</h3>
<p>Peserta diajak menanamkan budaya preventif melalui prinsip sederhana: Cek kondisi cairan (oli, coolant, AdBlue), Dengar jika ada suara abnormal mesin, Lihat lampu indikator dasbor dan fisik ban/rem, serta Lapor segera kepada mekanik jika ditemukan anomali sebelum unit dipacu di jalur hauling.</p>
HTML
            ],

            // ==========================================
            // ARTIKEL 6: Water Treatment Plant (WTP)
            // ==========================================
            [
                'title' => 'Air yang Tak Terlihat, Dampak yang Menentukan: Memahami Pentingnya Water Treatment Plant bagi Keandalan Boiler',
                'slug' => 'air-yang-tak-terlihat-dampak-yang-menentukan-water-treatment-plant',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['Water Treatment Plant', 'WTP', 'Boiler', 'PT SAM 2', 'ECOLAB', 'Chemical Treatment', 'PKS', 'Mill Excellence'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Melalui pelatihan Pengelolaan Water Treatment Plant, peserta memperkuat kompetensi dalam pengolahan air, pengendalian kualitas, dan perhitungan dosis bahan kimia untuk menjaga performa boiler serta keberlangsungan operasional pabrik.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Air adalah darah bagi pabrik kelapa sawit. Ketika kualitas air tidak terjaga, maka jantung pabrik — yaitu boiler — akan menjadi pihak pertama yang merasakan dampaknya."
    </p>
</blockquote>

<p>Ketika sebuah boiler mengalami gangguan, perhatian biasanya langsung tertuju pada mesin, tekanan uap, atau sistem pembakaran. Namun, tahukah Anda bahwa akar masalah sering kali justru berawal dari air? Air baku yang tidak diolah dengan benar dapat menimbulkan kerak (<em>scaling</em>), korosi pipa uap, serta penurunan efisiensi perpindahan panas yang drastis.</p>

<p>FR Academy menyelenggarakan pelatihan <strong>Pengelolaan Water Treatment Plant</strong> pada <strong>15 Mei 2026</strong> di <strong>PT SAM 2</strong>, menghadirkan <strong>Tengku Arie Fahreza dari ECOLAB</strong> sebagai narasumber ahli pengelolaan air industri.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Ketelitian dalam Menghitung Dosis Chemical</h3>
<p>Fokus utama pelatihan ini adalah perhitungan presisi kebutuhan bahan kimia (koagulan, flokulan, oksigen scavenger, scale inhibitor) dan kalibrasi pompa dosing. Penggunaan bahan kimia yang kurang akan menyebabkan pembentukan kerak dan korosi pipa boiler, sementara dosis berlebih akan memboroskan biaya dan merusak stabilitas air.</p>
<p>Melalui kelas intensif ini, operator dan staf mill dibekali kemampuan membaca parameter laboratorium air secara mandiri, menjaga umur pakai pipa boiler, dan memastikan pasokan uap pabrik tetap andal tanpa <em>unscheduled downtime</em>.</p>
HTML
            ],

            // ==========================================
            // ARTIKEL 7: Bearing Refreshment by Vendor
            // ==========================================
            [
                'title' => 'Komponen Kecil, Dampak Besar: Memahami Peran Bearing dalam Menjaga Keandalan Operasional',
                'slug' => 'komponen-kecil-dampak-besar-peran-bearing-dalam-keandalan-operasional',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['Bearing', 'Maintenance', 'Reliability', 'Pabrik Kelapa Sawit', 'Traksi', 'FR Academy', 'Preventive Maintenance'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Melalui program Refreshment Bearing by Vendor, para insan teknik memperkuat pemahaman mengenai salah satu komponen paling vital dalam sistem permesinan yang berperan besar menjaga produktivitas dan keandalan operasional.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Sebagian besar kerusakan bearing dapat dicegah melalui instalasi, pelumasan, dan inspeksi yang tepat. Komponen kecil dapat menyebabkan dampak kerugian besar ketika keandalannya diabaikan."
    </p>
</blockquote>

<p>Apa yang terjadi ketika sebuah bearing berukuran hanya beberapa sentimeter mengalami kerusakan? Di lingkungan pabrik kelapa sawit dan unit traksi, kegagalan bearing pada conveyor, sterilizer, screw press, atau gearbox dapat menghentikan seluruh lini produksi dan menelan kerugian hingga jutaan rupiah.</p>

<p>Pada <strong>27 Februari 2026</strong>, FR Academy menggelar program <strong>Refreshment Bearing by Vendor</strong> yang diikuti oleh 29 teknisi dan supervisor, dipandu oleh praktisi teknis <strong>Tegar Barasa dan Hartanto</strong>.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">5 Penyebab Utama Kerusakan Bearing di Lapangan</h3>
<p>Pelatihan membedah faktor penyebab kegagalan bearing yang paling umum:</p>
<ul class="list-disc pl-6 space-y-2 my-4">
    <li><strong>Pelumasan tidak tepat:</strong> Kurang pelumas memicu panas gesekan; kelebihan pelumas (<em>over-greasing</em>) juga merusak seal dan meningkatkan suhu kerja.</li>
    <li><strong>Kontaminasi:</strong> Masuknya partikel debu, air, atau serbuk TBS ke dalam bantalan.</li>
    <li><strong>Misalignment:</strong> Ketidaksejajaran poros mesin saat instalasi.</li>
    <li><strong>Beban berlebih (Overload):</strong> Melebihi kapasitas muatan mekanikal yang direkomendasikan.</li>
    <li><strong>Metode instalasi keliru:</strong> Pemasangan paksa menggunakan palu tanpa alat penarik/pemanas induksi khusus.</li>
</ul>
<p>Dengan mengenali getaran, suara abnormal, dan kenaikan temperatur sejak dini, tim maintenance dapat melakukan perbaikan terjadwal sebelum mesin mengalami kerusakan fatal.</p>
HTML
            ],

            // ==========================================
            // ARTIKEL 8: Operational Boiler by Vendor
            // ==========================================
            [
                'title' => 'Menjaga Jantung Pabrik Tetap Berdetak: Membangun Keandalan Boiler Melalui Kompetensi dan Keselamatan Kerja',
                'slug' => 'menjaga-jantung-pabrik-tetap-berdetak-keandalan-boiler',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['Boiler', 'PKS', 'Mill Operation', 'Safety', 'Zero Accident', 'Super Andalas Steel', 'FR Academy', 'Efisiensi Energi'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Pelatihan Manajemen Operational Boiler by Vendor memperkuat pemahaman staf dan operator pabrik mengenai pengoperasian boiler yang aman, efisien, dan andal untuk mendukung produktivitas berkelanjutan.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Boiler yang andal tidak hanya menghasilkan energi uap, tetapi juga menjaga produktivitas dan keselamatan kerja seluruh karyawan pabrik kelapa sawit."
    </p>
</blockquote>

<p>Bayangkan jika jantung manusia berhenti berdetak selama beberapa menit. Di pabrik kelapa sawit, boiler memegang peranan serupa sebagai pemasok tenaga uap untuk turbin listrik dan proses perebusan TBS. Namun karena beroperasi pada tekanan dan suhu yang sangat tinggi, boiler memerlukan kompetensi operasional dan kepatuhan prosedur keselamatan tingkat tinggi.</p>

<p>FR Academy bekerja sama dengan <strong>PT Super Andalas Steel</strong> menyelenggarakan program <strong>Manajemen Operational Boiler by Vendor</strong> yang menjangkau total <strong>109 peserta</strong> di Regional Riau (PT SIR Lukut, PT SAM 1, PT PISP 1) dan Regional Kalimantan Barat (USP, LS, KALE, SMP, MKSK, MKSS via Zoom Meeting), dengan narasumber <strong>Ojak Paijon Pangaribuan</strong>.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Efisiensi Bahan Bakar dan Perawatan Preventif</h3>
<p>Selain keselamatan kerja, peserta dibekali kalkulasi kebutuhan bahan bakar biomassa (serat fiber dan cangkang sawit) untuk mengoptimalkan pembakaran tanpa pemborosan energi. Peserta juga mempelajari inspeksi harian terhadap valve, sight glass, instrumen tekanan, serta blowdown terjadwal untuk mencegah akumulasi lumpur di drum boiler.</p>
HTML
            ],

            // ==========================================
            // ARTIKEL 9: Downstream (Refinery, Fraksinasi, Biodiesel)
            // ==========================================
            [
                'title' => 'Menjaga Kualitas dari Hulu ke Hilir: Menyegarkan Kompetensi Operator Downstream untuk Produksi yang Unggul',
                'slug' => 'menjaga-kualitas-dari-hulu-ke-hilir-kompetensi-operator-downstream',
                'category_id' => $category->id,
                'author_id' => $authorId,
                'status' => 'draft',
                'featured_image' => null,
                'published_at' => null,
                'tags' => ['Downstream', 'Refinery', 'Fraksinasi', 'Biodiesel', 'FAME', 'PT ASK Bangsal Aceh', 'FR Academy', 'Quality Control'],
                'content' => <<<'HTML'
<p class="lead text-lg font-medium text-gray-800 dark:text-gray-200 mb-6 italic border-l-4 border-teal-600 pl-4">
    Melalui program Refresh Operasional Refinery, Fraksinasi, dan Biodiesel, FR Academy memperkuat kompetensi Foreman dan Operator agar mampu menjaga kapasitas produksi, kualitas produk, serta keandalan operasional di lini downstream.
</p>

<blockquote class="my-6 p-4 border-l-4 border-teal-500 bg-teal-50 dark:bg-gray-800 rounded-r-lg">
    <p class="text-base italic font-semibold text-teal-900 dark:text-teal-200">
        "Kualitas produk terbaik lahir dari proses yang terkendali dan dijalankan oleh insan yang kompeten. Dalam industri downstream, memahami parameter proses secara presisi adalah kunci menjaga kualitas minyak goreng dan energi terbarukan."
    </p>
</blockquote>

<p>Apa yang membedakan minyak sawit mentah (CPO) dengan minyak goreng jernih atau bahan bakar biodiesel berkualitas tinggi? Jawabannya ada pada rantai teknologi hilir kelapa sawit yang melibatkan proses pemurnian, pemisahan fraksi, dan transesterifikasi yang sangat presisi.</p>

<p>FR Academy menyelenggarakan program refreshment bagi Foreman dan Operator Downstream di <strong>PT ASK Bangsal Aceh</strong> dalam dua gelombang intensif: <strong>Refresh Operational Refinery & Fraksinasi</strong> (25–26 Februari 2026) dan <strong>Refresh Operational Biodiesel</strong> (28–29 Mei 2026), dipandu oleh trainer <strong>Tegar Barasa dan Hartanto</strong>.</p>

<h3 class="text-2xl font-bold text-teal-primary dark:text-white mt-8 mb-4">Refinery, Fraksinasi, dan Biodiesel (FAME)</h3>
<p>Pelatihan ini mengulas pengoperasian unit-unit berkapasitas besar: Refinery 1.000, 1.500, dan 2.500 ton per hari, Fraksinasi 1.000 dan 2.500 ton per hari, serta pabrik Biodiesel 750 dan 1.500 ton per hari.</p>
<p>Peserta memperdalam kontrol kualitas pada produk turunan: <strong>RBD Olein (minyak goreng)</strong>, <strong>Stearin</strong>, <strong>Fatty Acid Deodorizer Distillate (FAD)</strong>, serta <strong>FAME (Biodiesel)</strong> dan <strong>Gliserin</strong>. Pemahaman mendalam terhadap fluktuasi temperatur, tekanan vakum, dan rasio katalis memastikan setiap tetes produk memenuhi standar ekspor internasional.</p>
HTML
            ],
        ];

        // 4. Masukkan ke Database dengan updateOrCreate agar idempotent (bisa dijalankan berulang tanpa duplikat)
        $count = 0;
        foreach ($articles as $articleData) {
            Article::updateOrCreate(
                ['slug' => $articleData['slug']],
                $articleData
            );
            $count++;
        }

        $this->command->info("Berhasil memasukkan {$count} draf artikel korporat ke dalam database portal.");
    }
}
