<?php

namespace App\Services;

class ProgramCatalogService
{
    /**
     * Get all programs structured by category (dynamically from DB, with fallback).
     */
    public static function getAllPrograms(): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('portal_programs')) {
                $dbPrograms = \App\Models\PortalProgram::where('is_active', true)
                    ->orderBy('order')
                    ->orderBy('id')
                    ->get();

                if ($dbPrograms->isNotEmpty()) {
                    $programs = [];
                    foreach ($dbPrograms as $p) {
                        $programs[$p->slug] = [
                            'id' => $p->id,
                            'slug' => $p->slug,
                            'code' => $p->code ?? strtoupper($p->slug),
                            'title' => $p->title,
                            'subtitle' => $p->subtitle,
                            'category_key' => $p->category_key ?? 'trainee',
                            'category_name' => $p->category_name ?? 'Program Pelatihan',
                            'target_role' => $p->target_role,
                            'duration' => $p->duration,
                            'format' => $p->format,
                            'image' => $p->getResolvedImageUrl(),
                            'hero_image' => $p->getResolvedHeroImageUrl(),
                            'badge' => $p->badge,
                            'is_reguler' => (bool) $p->is_reguler,
                            'overview' => $p->overview,
                            'objectives' => $p->objectives ?? [],
                            'curriculum_pillars' => $p->curriculum_pillars ?? [],
                            'learning_method' => $p->learning_method ?? [
                                'ratio' => '70 : 20 : 10',
                                'desc' => '70% experiential learning, 20% social learning, dan 10% formal learning'
                            ],
                            'order' => $p->order,
                        ];
                    }
                    return $programs;
                }
            }
        } catch (\Throwable $e) {
            // fallback if db error
        }

        return self::getStaticFallbackPrograms();
    }

    /**
     * Static fallback list of default programs.
     */
    public static function getStaticFallbackPrograms(): array
    {
        return [
            // =================================================================
            // 01. PROGRAM TRAINEE (REGULER)
            // =================================================================
            'fat' => [
                'slug' => 'fat',
                'code' => 'FAT',
                'title' => 'Field Assistant Training (FAT)',
                'subtitle' => 'Agronomi & Budidaya Perkebunan Kelapa Sawit',
                'category_key' => 'trainee',
                'category_name' => 'Program Trainee',
                'target_role' => 'Calon Asisten Kebun (Planter)',
                'duration' => '6 Bulan (Reguler & Mentoring Berkesinambungan)',
                'format' => 'Metode 70:20:10 (70% Experiential, 20% Social, 10% Formal Learning)',
                'image' => '/images/posters/poster_fat.jpg',
                'hero_image' => '/images/programs/fat_estate.jpg',
                'badge' => 'Agronomi & Lapangan Kebun',
                'overview' => 'Program Field Assistant Training (FAT) dirancang untuk menyiapkan calon asisten kebun dengan membekali peserta dengan pengetahuan, keterampilan dan sikap untuk bekerja di Perkebunan Kelapa Sawit. Dilaksanakan secara reguler (terjadwal dan berkesinambungan) selama 6 bulan, serta pendampingan dalam bentuk mentoring, guna menyiapkan calon asisten sebelum berkarir di Perkebunan Kelapa Sawit, sesuai dengan budaya First Resources.',
                'objectives' => [
                    'Membekali calon asisten afdeling dengan keterampilan teknis di perkebunan, khususnya kelapa sawit.',
                    'Membekali calon asisten afdeling dengan keterampilan managerial and leadership.',
                    'Membentuk pola pikir, perilaku dan sikap mental sebagai Planter sesuai budaya First Resources.',
                ],
                'curriculum_pillars' => [
                    'Materi Kompetensi Umum',
                    'Materi Kompetensi Teknis',
                    'Materi Kompetensi Pendukung'
                ],
                'learning_method' => [
                    'ratio' => '70 : 20 : 10',
                    'desc' => '70% experiential learning, 20% social learning, dan 10% formal learning'
                ],
                'phases' => [
                    [
                        'name' => 'Fase 01: In-Class Pembekalan Agronomi & Karakter',
                        'duration' => 'Bulan 1 - 2',
                        'location' => 'Training Center Kebun Kubang',
                        'desc' => 'Pembekalan teori menyeluruh kultur teknis budidaya, standar mutu First Resources, pembinaan mental fisik disiplin (Bintalsik), serta dasar kepemimpinan operasional.'
                    ],
                    [
                        'name' => 'Fase 02: On-the-Job Training (OJT) Afdeling Perkebunan',
                        'duration' => 'Bulan 3 - 5',
                        'location' => 'Estate Operasional',
                        'desc' => 'Praktik langsung mendampingi Asisten Kebun senior dan Mandor 1 dalam mengelola afdeling (700-1.000 Ha), membagi ancak kerja panen, supervisi harian, dan sensus lapangan.'
                    ],
                    [
                        'name' => 'Fase 03: Penugasan Mandiri & Sidang Komisi Evaluasi',
                        'duration' => 'Bulan 6',
                        'location' => 'Unit Penempatan & Kantor Wilayah',
                        'desc' => 'Mengemban tanggung jawab mandiri (Acting Assistant) serta mempresentasikan karya tulis ilmiah/improvement project di hadapan Dewan Komisi Direksi Operasional.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Modul 01: Botani, Morfologi & Siklus Hidup Kelapa Sawit',
                        'hours' => '24 JP',
                        'topics' => [
                            'Fisiologi dan perkembangan vegetatif & generatif kelapa sawit',
                            'Perbedaan karakteristik varietas bibit unggul',
                            'Kebutuhan iklim, tanah, dan keseimbangan neraca air perkebunan'
                        ]
                    ],
                    [
                        'module' => 'Modul 02: Manajemen Panen & Kualitas TPH',
                        'hours' => '40 JP',
                        'topics' => [
                            'Kriteria matang panen fraksi 1-3 dan dampak asam lemak bebas (ALB)',
                            'Penataan rotasi panen dan sistem ancak giring/tetap',
                            'Tata kelola TPH, pencatatan SPB, dan sistem angkut tuntas ke PKS',
                            'Pencegahan losses brondolan tertinggal di piringan dan pasar rintis'
                        ]
                    ],
                    [
                        'module' => 'Modul 03: Kultur Teknis Pemupukan Presisi & Gulma',
                        'hours' => '32 JP',
                        'topics' => [
                            'Interpretasi hasil analisa daun (LSU) dan analisa tanah (SSU)',
                            'Pengendalian gulma piringan, pasar pikul, dan gawangan sawit',
                            'Manajemen pupuk anorganik, abu janjang, dan aplikasi limbah cair (Land Application)',
                            'Kalibrasi alat semprot (knapsack sprayer) dan takaran mangkok pupuk'
                        ]
                    ],
                    [
                        'module' => 'Modul 04: Pengendalian Hama & Penyakit Terpadu (HPT)',
                        'hours' => '32 JP',
                        'topics' => [
                            'Teknik sensus serangan ulat pemakan daun kelapa sawit (UPDKS)',
                            'Aplikasi fogging, mist blower, dan injeksi batang (trunk injection)',
                            'Pengendalian hayati: Konservasi burung hantu (Tyto alba) dan tanaman inang predator',
                            'Sanitasi pokok terinfeksi Ganoderma dan pencegahan penularan'
                        ]
                    ],
                    [
                        'module' => 'Modul 05: Kepemimpinan, Hubungan Industrial & K3 Kebun',
                        'hours' => '28 JP',
                        'topics' => [
                            'Standar Keselamatan dan Kesehatan Kerja (K3) serta pemakaian APD di kebun',
                            'Psikologi mandor dan pendekatan pembinaan karyawan panen/rawat',
                            'Pencegahan kecelakaan kerja dan mitigasi risiko sengatan binatang berbisa',
                            'Penyusunan Rencana Kerja Harian (RKH) dan Rencana Kerja Bulanan (RKB)'
                        ]
                    ]
                ],
                'career_path' => 'Field Assistant (Asisten Kebun) → Asisten Kepala (Askep Estate) → Estate Manager → Regional Head Operasional'
            ],

            // =================================================================
            // MAT
            // =================================================================
            'mat' => [
                'slug' => 'mat',
                'code' => 'MAT',
                'title' => 'Mill Assistant Training (MAT)',
                'subtitle' => 'Teknik Pengolahan & Manajemen Pabrik Kelapa Sawit (PKS)',
                'category_key' => 'trainee',
                'category_name' => 'Program Trainee',
                'target_role' => 'Calon Asisten Pabrik (Engineer)',
                'duration' => '6 Bulan (Reguler & Mentoring Berkesinambungan)',
                'format' => 'Metode 70:20:10 (70% Experiential, 20% Social, 10% Formal Learning)',
                'image' => '/images/posters/poster_mat.jpg',
                'hero_image' => '/images/programs/mat_mill.jpg',
                'badge' => 'Teknik & Pengolahan PKS',
                'overview' => 'Program MAT dirancang untuk menyiapkan calon asisten pabrik dengan membekali peserta dengan pengetahuan, keterampilan dan sikap untuk bekerja di Pabrik Kelapa Sawit (PKS). Program MAT dilaksanakan secara reguler (terjadwal dan berkesinambungan) selama 6 bulan, serta pedampingan dalam bentuk mentoring, guna menyiapkan mental sebagai Engineer sebelum berkarir di Perkebunan Kelapa Sawit, sesuai dengan budaya First Resources.',
                'objectives' => [
                    'Menyiapkan calon asisten pabrik.',
                    'Membekali calon asisten pabrik dengan keterampilan teknis di pabrik kelapa sawit.',
                    'Membekali calon asisten pabrik dengan keterampilan managerial and leadership.',
                    'Membentuk pola pikir, perilaku dan sikap mental sebagai Engineer sesuai budaya First Resources.',
                ],
                'curriculum_pillars' => [
                    'Materi Kompetensi Umum',
                    'Materi Kompetensi Teknis',
                    'Materi Kompetensi Pendukung'
                ],
                'learning_method' => [
                    'ratio' => '70 : 20 : 10',
                    'desc' => '70% experiential learning, 20% social learning, dan 10% formal learning'
                ],
                'phases' => [
                    [
                        'name' => 'Fase 01: In-Class Teknik Pengolahan & Termodinamika',
                        'duration' => 'Bulan 1 - 2',
                        'location' => 'Training Center & Lab Simulasi PKS',
                        'desc' => 'Pembekalan teori mesin industri PKS, prinsip neraca massa dan energi, keselamatan bejana tekan/boiler, serta pengenalan standar K3 pabrik.'
                    ],
                    [
                        'name' => 'Fase 02: On-the-Job Training Stasiun Pengolahan',
                        'duration' => 'Bulan 3 - 5',
                        'location' => 'Pabrik Kelapa Sawit (PKS)',
                        'desc' => 'Praktik rotasi shift mendampingi Asisten Proses dan Asisten Maintenance di seluruh stasiun mesin pengolahan.'
                    ],
                    [
                        'name' => 'Fase 03: Penugasan Shift Mandiri & Sidang Komisi',
                        'duration' => 'Bulan 6',
                        'location' => 'PKS Penempatan',
                        'desc' => 'Memegang kendali shift proses secara mandiri dan mempertahankan tugas akhir analisa efisiensi pabrik di hadapan pimpinan divisi teknik.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Modul 01: Stasiun Penerimaan TBS, Loading Ramp & Sterilisasi',
                        'hours' => '30 JP',
                        'topics' => [
                            'Sortasi mutu TBS dan manajemen antrean armada pengangkut',
                            'Pola perebusan (sterilization cycle) 2-peak dan 3-peak',
                            'Efektivitas perontokan brondolan di mesin threshing drum'
                        ]
                    ],
                    [
                        'module' => 'Modul 02: Stasiun Kempa (Pressing) & Pemurnian (Clarification)',
                        'hours' => '36 JP',
                        'topics' => [
                            'Pengaturan temperatur dan injeksi steam di digester',
                            'Pengoperasian screw press dan monitoring tekanan amper motor',
                            'Pemisahan fase minyak di sand trap tank, vibrating screen, dan continuous settling tank (CST)',
                            'Purifikasi minyak murni di centrifuge dan vacuum oil dryer'
                        ]
                    ],
                    [
                        'module' => 'Modul 03: Stasiun Kernel & Recovery Biji Sawit',
                        'hours' => '24 JP',
                        'topics' => [
                            'Pemisahan serat dan biji di depericarper drum',
                            'Pengeringan nut di nut silo dan kalibrasi ripple mill pemecah cangkang',
                            'Pemisahan inti dan cangkang dengan hydrocyclone dan claybath separator'
                        ]
                    ],
                    [
                        'module' => 'Modul 04: Pembangkit Uap (Boiler), Turbin & Utilitas Pabrik',
                        'hours' => '36 JP',
                        'topics' => [
                            'Sistem pengolahan air umpan boiler (External & Internal Water Treatment)',
                            'Pengendalian tekanan uap, water gauge, dan katup pengaman (safety valve)',
                            'Sinkronisasi daya turbin uap dengan genset diesel utilitas'
                        ]
                    ],
                    [
                        'module' => 'Modul 05: Pengendalian Losses, Laboratorium & K3 Lingkungan Pabrik',
                        'hours' => '30 JP',
                        'topics' => [
                            'Titik pengambilan sampel cairan dan padatan untuk uji laboratorium',
                            'Pengendalian Baku Mutu Air Limbah di kolam Anaerobic (WWTP)',
                            'Penerapan Lockout/Tagout (LOTO) dan keselamatan ruang terbatas (Confined Space)'
                        ]
                    ]
                ],
                'career_path' => 'Mill Assistant (Asisten Proses/Maintenance) → Mill Manager (Manajer PKS) → Engineering Head → Regional Technical Director'
            ],

            // =================================================================
            // TAT
            // =================================================================
            'tat' => [
                'slug' => 'tat',
                'code' => 'TAT',
                'title' => 'Traction Assistant Training (TAT)',
                'subtitle' => 'Manajemen Traksi, Armada Angkut & Bengkel Unit',
                'category_key' => 'trainee',
                'category_name' => 'Program Trainee',
                'target_role' => 'Calon Asisten Traksi (Engineer)',
                'duration' => '6 Bulan (Reguler & Mentoring Berkesinambungan)',
                'format' => 'Metode 70:20:10 (70% Experiential, 20% Social, 10% Formal Learning)',
                'image' => '/images/posters/poster_tat.jpg',
                'hero_image' => '/images/programs/tat_traction.jpg',
                'badge' => 'Traksi & Bengkel Alat Berat',
                'overview' => 'Program TAT dirancang untuk menyiapkan calon asisten traksi dengan membekali peserta dengan pengetahuan, keterampilan dan sikap untuk bekerja di unit traksi di perkebunan kelapa sawit. Program TAT dilaksanakan secara reguler (terjadwal dan berkesinambungan) selama 6 bulan, serta pedampingan dalam bentuk mentoring, guna menyiapkan mental sebagai Engineer sebelum berkarir di Perkebunan Kelapa Sawit, sesuai dengan budaya First Resources.',
                'objectives' => [
                    'Menyiapkan calon asisten traksi.',
                    'Membekali calon asisten traksi dengan keterampilan teknis di unit traksi.',
                    'Membekali calon asisten traksi dengan keterampilan managerial and leadership.',
                    'Membentuk pola pikir, perilaku dan sikap mental sebagai Engineer sesuai budaya First Resources.',
                ],
                'curriculum_pillars' => [
                    'Materi Kompetensi Umum',
                    'Materi Kompetensi Teknis',
                    'Materi Kompetensi Pendukung'
                ],
                'learning_method' => [
                    'ratio' => '70 : 20 : 10',
                    'desc' => '70% experiential learning, 20% social learning, dan 10% formal learning'
                ],
                'phases' => [
                    [
                        'name' => 'Fase 01: In-Class Sistem Mekanikal & Manajemen Workshop',
                        'duration' => 'Bulan 1 - 2',
                        'location' => 'Training Center & Bengkel Utama',
                        'desc' => 'Dasar mekanikal mesin diesel modern, sistem hidrolik alat berat, electrical wiring, dan pengantar manajemen logistik traksi.'
                    ],
                    [
                        'name' => 'Fase 02: On-the-Job Training Bengkel Unit & Lapangan',
                        'duration' => 'Bulan 3 - 5',
                        'location' => 'Workshop Traksi Estate',
                        'desc' => 'Praktik overhaul mesin, rotasi pergantian ban, inspeksi harian armada (P2H), dan penjadwalan alat berat pembuatan jalan kebun.'
                    ],
                    [
                        'name' => 'Fase 03: Penugasan Mandiri & Sidang Komisi',
                        'duration' => 'Bulan 6',
                        'location' => 'Unit Penempatan Traksi',
                        'desc' => 'Memimpin bengkel traksi secara mandiri dan mempertahankan proyek efisiensi armada di hadapan dewan penguji.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Modul 01: Mesin Diesel & Sistem Bahan Bakar Common Rail',
                        'hours' => '32 JP',
                        'topics' => [
                            'Prinsip kerja mesin diesel 4 langkah dan siklus pembakaran',
                            'Perawatan sistem suplai bahan bakar solar dan filtrasi injektor',
                            'Diagnosis gangguan asap knalpot, overheat, dan kehilangan kompresi'
                        ]
                    ],
                    [
                        'module' => 'Modul 02: Sistem Transmisi, Gardan & Hidrolik Alat Berat',
                        'hours' => '36 JP',
                        'topics' => [
                            'Pemeliharaan gearbox, clutch plate, drive shaft, dan diferensial roda belakang',
                            'Sirkuit hidrolik pompa, cylinder bucket, dan control valve alat berat (Excavator/Grader)',
                            'Kalibrasi tekanan hidrolik dan penanganan kebocoran seal oli'
                        ]
                    ],
                    [
                        'module' => 'Modul 03: Manajemen Pemeliharaan Terjadwal (Preventive Maintenance)',
                        'hours' => '28 JP',
                        'topics' => [
                            'Jadwal servis berkala interval 250, 500, 1000 Jam Operasi (HM/KM)',
                            'Standar Pemeriksaan dan Perawatan Harian (P2H) bagi driver dan operator',
                            'Tata kelola program oli, greasing sasis, dan analisis keausan oli (Oil Sampling)'
                        ]
                    ],
                    [
                        'module' => 'Modul 04: Manajemen Ban, BBM & Logistik Sparepart',
                        'hours' => '24 JP',
                        'topics' => [
                            'Pola rotasi ban, pengawasan tekanan angin, dan analisis penyebab ban robek/pecah',
                            'Monitoring rasio konsumsi solar per kilometer/ritase angkut TBS',
                            'Tata kelola kartu stok suku cadang, reorder point, dan peredaman kanibalisasi part'
                        ]
                    ],
                    [
                        'module' => 'Modul 05: K3 Bengkel, Pengelolaan Limbah B3 & Kelistrikan Genset',
                        'hours' => '24 JP',
                        'topics' => [
                            'Penanganan oli bekas, aki bekas, dan filter oli berstandar lingkungan hidup',
                            'Sistem pengoperasian dan sinkronisasi genset diesel pembangkit listrik perumahan',
                            'Pencegahan bahaya dongkrak rubuh, semburan oli hidrolik, dan kebakaran las karbit'
                        ]
                    ]
                ],
                'career_path' => 'Traction Assistant (Asisten Traksi) → Workshop Head Regional → Logistics & Fleet Director'
            ],

            // =================================================================
            // AAT
            // =================================================================
            'aat' => [
                'slug' => 'aat',
                'code' => 'AAT',
                'title' => 'Administration Assistant Training (AAT)',
                'subtitle' => 'Tata Kelola Administrasi, Akuntansi & Finansial Unit',
                'category_key' => 'trainee',
                'category_name' => 'Program Trainee',
                'target_role' => 'Calon Kepala Administrasi Kebun (KASI) / Planter',
                'duration' => '6 Bulan (Reguler & Mentoring Berkesinambungan)',
                'format' => 'Metode 70:20:10 (70% Experiential, 20% Social, 10% Formal Learning)',
                'image' => '/images/posters/poster_aat.jpg',
                'hero_image' => '/images/programs/aat_admin.jpg',
                'badge' => 'Administrasi & Finansial',
                'overview' => 'Program AAT dirancang untuk menyiapkan calon Kepala Administrasi Kebun (KASI) dengan membekali peserta dengan pengetahuan, keterampilan dan sikap untuk bekerja di Perkebunan Kelapa Sawit. Program AAT dilaksanakan secara reguler (terjadwal dan berkesinambungan) selama 6 bulan, serta pedampingan dalam bentuk mentoring, guna menyiapkan mental sebagai PLANTER sebelum berkarir di Perkebunan Kelapa Sawit, sesuai dengan budaya First Resources.',
                'objectives' => [
                    'Menyiapkan calon asisten administrasi.',
                    'Membekali calon asisten administrasi dengan keterampilan teknis di administrasi.',
                    'Membekali calon asisten administrasi dengan keterampilan managerial and leadership.',
                    'Membentuk pola pikir, perilaku dan sikap mental sebagai Planter sesuai budaya First Resources.',
                ],
                'curriculum_pillars' => [
                    'Materi Kompetensi Umum',
                    'Materi Kompetensi Teknis',
                    'Materi Kompetensi Pendukung'
                ],
                'learning_method' => [
                    'ratio' => '70 : 20 : 10',
                    'desc' => '70% experiential learning, 20% social learning, dan 10% formal learning'
                ],
                'phases' => [
                    [
                        'name' => 'Fase 01: In-Class Akuntansi Biaya Perkebunan & ERP',
                        'duration' => 'Bulan 1 - 2',
                        'location' => 'Training Center & Lab Komputer',
                        'desc' => 'Chart of Accounts, tata kelola kas unit (imprest fund), modul ERP Perkebunan, dan regulasi ketenagakerjaan.'
                    ],
                    [
                        'name' => 'Fase 02: On-the-Job Training Kantor Unit Operasional',
                        'duration' => 'Bulan 3 - 5',
                        'location' => 'Kantor Kebun / PKS Penempatan',
                        'desc' => 'Praktik pembukuan riil, stock opname fisik gudang, verifikasi premi panen karyawan, dan rekonsiliasi data.'
                    ],
                    [
                        'name' => 'Fase 03: Penugasan Mandiri KTU & Sidang Komisi',
                        'duration' => 'Bulan 6',
                        'location' => 'Kantor Wilayah / Head Office',
                        'desc' => 'Memimpin administrasi unit secara mandiri dan mempertahankan tugas akhir analisis pengendalian budget di hadapan Direksi Finansial.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Modul 01: Akuntansi Biaya Operasional (Cost Accounting)',
                        'hours' => '32 JP',
                        'topics' => [
                            'Struktur biaya kebun: Biaya Tanaman Belum Menghasilkan (TBM) vs Tanaman Menghasilkan (TM)',
                            'Alokasi biaya langsung (upah, pupuk, pestisida) dan biaya tidak langsung unit',
                            'Penyusunan laporan bulanan biaya pokok produksi TBS per hektar dan CPO per kilogram'
                        ]
                    ],
                    [
                        'module' => 'Modul 02: Payroll & Hubungan Industrial Ketenagakerjaan',
                        'hours' => '32 JP',
                        'topics' => [
                            'Kalkulasi upah pokok, lembur, insentif premi panen, dan potongan BPJS Kesehatan/Ketenagakerjaan',
                            'Regulasi hubungan kerja, Perjanjian Kerja Bersama (PKB), dan manajemen cuti karyawan',
                            'Audit verifikasi kehadiran buku mandor vs data absensi digital'
                        ]
                    ],
                    [
                        'module' => 'Modul 03: Manajemen Pergudangan & Pengendalian Persediaan',
                        'hours' => '28 JP',
                        'topics' => [
                            'Prosedur penerimaan barang, penerbitan Bukti Penerimaan Barang (BPB) dan Goods Issue',
                            'Teknik stock opname berkala pupuk ratusan ton, BBM solar, dan suku cadang bengkel',
                            'Pencegahan selisih stok fisik vs sistem dan pemusnahan barang rusak/kadaluarsa'
                        ]
                    ],
                    [
                        'module' => 'Modul 04: Pengelolaan Kas Unit, Perbankan & Perpajakan',
                        'hours' => '24 JP',
                        'topics' => [
                            'Tata kelola kas kecil (Petty Cash) dengan sistem imprest fund',
                            'Penyusunan cash flow proyeksi mingguan dan rekonsiliasi rekening koran bank',
                            'Pemotongan PPh Pasal 21 atas upah karyawan dan PPh 23 jasa pihak ketiga'
                        ]
                    ],
                    [
                        'module' => 'Modul 05: Sistem ERP Perkebunan & Internal Control Audit',
                        'hours' => '24 JP',
                        'topics' => [
                            'Pengoperasian end-to-end modul ERP modul finance, inventory, dan harvesting',
                            'Kepatuhan terhadap SOP internal dan mitigasi risiko fraud / kebocoran dana operasional',
                            'Teknik menghadapi audit internal maupun external auditor independen'
                        ]
                    ]
                ],
                'career_path' => 'Kepala Tata Usaha (KTU) → Koordinator Administrasi Wilayah → Finance & Admin Manager Group'
            ],

            // =================================================================
            // DMT
            // =================================================================
            'dmt' => [
                'slug' => 'dmt',
                'code' => 'DMT',
                'title' => 'Downstream Trainee (DMT)',
                'subtitle' => 'Teknologi Kilang Rafinasi, Fraksinasi & Industri Oleokimia Hilir',
                'category_key' => 'trainee',
                'category_name' => 'Program Trainee',
                'target_role' => 'Calon Supervisor Downstream / Asisten Kilang (Engineer)',
                'duration' => '6 Bulan (Reguler & Mentoring Berkesinambungan)',
                'format' => 'Metode 70:20:10 (70% Experiential, 20% Social, 10% Formal Learning)',
                'image' => '/images/posters/poster_dmt.jpg',
                'hero_image' => '/images/programs/dmt_downstream.jpg',
                'badge' => 'Kilang Hilir & Oleokimia',
                'overview' => 'Program Downstream Manajemen Training (DMT) dirancang untuk menyiapkan calon supervisor downstream dengan membekali peserta dengan pengetahuan, keterampilan dan sikap untuk bekerja di downstream atau refinery. Dilaksanakan secara reguler (terjadwal dan berkesinambungan) selama 6 bulan, serta pedampingan dalam bentuk mentoring, guna menyiapkan calon asisten sebelum berkarir di unit Downstream, sesuai dengan budaya First Resources.',
                'objectives' => [
                    'Menyiapkan calon supervisor downstream.',
                    'Membekali calon supervisor downstream dengan keterampilan teknis di downstream.',
                    'Membekali calon supervisor downstream dengan keterampilan managerial and leadership.',
                    'Membentuk pola pikir, perilaku dan sikap mental sebagai Engineer sesuai budaya First Resources.',
                ],
                'curriculum_pillars' => [
                    'Materi Kompetensi Umum',
                    'Materi Kompetensi Teknis',
                    'Materi Kompetensi Pendukung'
                ],
                'learning_method' => [
                    'ratio' => '70 : 20 : 10',
                    'desc' => '70% experiential learning, 20% social learning, dan 10% formal learning'
                ],
                'phases' => [
                    [
                        'name' => 'Fase 01: In-Class Kimia Fraksinasi & Otomasi Industri',
                        'duration' => 'Bulan 1 - 2',
                        'location' => 'Training Center Industri Hilir',
                        'desc' => 'Prinsip perpindahan massa, degumming, bleaching, deodorization, serta filosofi otomasi sensor industri.'
                    ],
                    [
                        'name' => 'Fase 02: On-the-Job Training Unit Rafinasi & Fraksinasi',
                        'duration' => 'Bulan 3 - 5',
                        'location' => 'Complex Refinery & Oleochemicals',
                        'desc' => 'Praktik di ruang kontrol DCS, pemantauan filter press membran fraksinasi, dan pengujian mutu di laboratorium QC.'
                    ],
                    [
                        'name' => 'Fase 03: Penugasan Shift Mandiri & Sidang Komisi',
                        'duration' => 'Bulan 6',
                        'location' => 'Plant Penempatan',
                        'desc' => 'Mengawasi jalannya shift kilang penuh dan mempresentasikan evaluasi peningkatan yield minyak olahan.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Modul 01: Proses Rafinasi (Degumming & Bleaching)',
                        'hours' => '32 JP',
                        'topics' => [
                            'Pemisahan gum getah fosfatida dengan injeksi asam fosfat encer',
                            'Penjerapan zat warna karotenoid dan logam berat dengan bleaching earth aktif',
                            'Pengoperasian Niagara Filter dan pembersihan cake spent earth'
                        ]
                    ],
                    [
                        'module' => 'Modul 02: Deodorisasi Termal Bertekanan Vakum',
                        'hours' => '36 JP',
                        'topics' => [
                            'Prinsip distilasi uap vakum tinggi untuk memisahkan Asam Lemak Bebas (FFA)',
                            'Penghilangan bau dan pembentukan distilat asam lemak sawit (PFAD)',
                            'Sistem vakum ejektor uap dan penghematan energi heat exchanger economizer'
                        ]
                    ],
                    [
                        'module' => 'Modul 03: Fraksinasi Kering & Kristalisasi Presisi',
                        'hours' => '32 JP',
                        'topics' => [
                            'Pemrograman kurva pendinginan di crystallizer tank',
                            'Pemisahan fraksi cair (RBD Palm Olein) dan fraksi padat (RBD Palm Stearin)',
                            'Pengoperasian membrane filter press bertekanan hidrolik tinggi'
                        ]
                    ],
                    [
                        'module' => 'Modul 04: Pengendalian Sistem Otomasi DCS / SCADA & Kalibrasi',
                        'hours' => '24 JP',
                        'topics' => [
                            'Interpretasi diagram P&ID (Piping and Instrumentation Diagram)',
                            'Sistem alarm otomatis, interlock trip darurat, dan safety shutdown',
                            'Kalibrasi instrumen transmitter suhu, laju aliran (flow rate), dan tekanan'
                        ]
                    ],
                    [
                        'module' => 'Modul 05: Quality Control, Food Safety & Sertifikasi Ekspor',
                        'hours' => '24 JP',
                        'topics' => [
                            'Analisa laboratorium parameter FFA, Colour Lovibond, IV (Iodine Value), dan DOBI',
                            'Penerapan standar keamanan pangan FSSC 22000, HACCP, GMP, Kosher, dan Halal',
                            'Prosedur penanganan tangki timbun pelabuhan dan pemuatan kapal tanker (Ship Loading)'
                        ]
                    ]
                ],
                'career_path' => 'Shift Supervisor Kilang → Plant Production Assistant → Refinery Operation Manager → Head of Downstream Processing'
            ],

            // =================================================================
            // 02. PROGRAM KEJURUAN TEKNIKAL
            // =================================================================
            'teknikal-estate' => [
                'slug' => 'teknikal-estate',
                'code' => 'TEK-ESTATE',
                'title' => 'Teknikal Estate',
                'subtitle' => 'Agronomi Lanjutan, Peningkatan Standar Budidaya & Pemupukan',
                'category_key' => 'teknikal',
                'category_name' => 'Kejuruan Teknikal',
                'target_role' => 'Mandor, Mandor 1, Asisten Kebun, & Asisten Kepala (Askep)',
                'duration' => 'Program Modular (1 - 5 Hari per Batch Workshop/Klinik)',
                'format' => 'Klinik Lapangan, Workshop Singkat, Bedah Kasus Blok, & Pendampingan',
                'image' => '/images/programs/fat_estate.jpg',
                'hero_image' => '/images/programs/fat_estate.jpg',
                'badge' => 'Kejuruan Teknikal Kebun',
                'overview' => 'Program peningkatan keahlian teknis agronomi kebun sawit berkelanjutan bagi praktisi aktif. Dirancang untuk mempertajam kapabilitas penyelesaian masalah di lapangan (problem solving), standardisasi teknik kultur teknis terbaik, serta pemanfaatan data analisa laboratorium guna mendongkrak produktivitas tonase TBS per hektar.',
                'objectives' => [
                    'Meningkatkan kapabilitas mandor dan asisten dalam pengawasan mutu kerja harian.',
                    'Memperbaiki teknik pemupukan 4T guna menekan pemborosan pupuk anorganik bernilai tinggi.',
                    'Mengoptimalkan penanganan Tanaman Belum Menghasilkan (TBM) agar cepat menghasilkan.',
                    'Menekan angka serangan hama Ganoderma dan ulat pemakan daun sawit secara sistematis.'
                ],
                'phases' => [
                    [
                        'name' => 'Tahap 1: Diagnostic & Pre-Assessment Lapangan',
                        'duration' => 'Hari 1',
                        'location' => 'Blok Perkebunan Terpilih',
                        'desc' => 'Identifikasi deviasi mutu kerja dan analisa akar masalah operasional agronomi setempat.'
                    ],
                    [
                        'name' => 'Tahap 2: Hands-on Workshop & Kalibrasi Standar',
                        'duration' => 'Hari 2 - 3',
                        'location' => 'Training Center / Balai Afdeling',
                        'desc' => 'Simulasi perbaikan metode kerja, kalibrasi alat, dan studi kasus penanganan variasi blok.'
                    ],
                    [
                        'name' => 'Tahap 3: Action Plan & Evaluasi Berkala',
                        'duration' => '3 Bulan Pendampingan',
                        'location' => 'Estate Asal Peserta',
                        'desc' => 'Implementasi perbaikan di blok kerja masing-masing dengan audit monitoring bulanan.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Klinik Kinerja Mandor Sawit',
                        'hours' => '16 JP',
                        'topics' => [
                            'Teknik pengawasan ancak kerja panen dan pembersihan piringan',
                            'Komunikasi instruksi kerja yang jelas, tegas, dan manusiawi',
                            'Pemeriksaan kepatuhan APD harian tenaga kerja sebelum apel pagi'
                        ]
                    ],
                    [
                        'module' => 'Manajemen Pemupukan Efektif & Presisi',
                        'hours' => '16 JP',
                        'topics' => [
                            'Teknik penaburan pupuk tunggal dan majemuk di piringan sawit',
                            'Pencegahan pencucian pupuk pada topografi tanah miring dan musim hujan',
                            'Kalibrasi mangkok takar pupuk dan audit sisa karung pupuk'
                        ]
                    ],
                    [
                        'module' => 'Klinik HPT & Proteksi Tanaman Lanjutan',
                        'hours' => '16 JP',
                        'topics' => [
                            'Teknik sensus cepat populasi ulat api dan ulat kantung',
                            'Teknik mist blowing dan fogging pada pokok tinggi (>10 meter)',
                            'Isolasi parit dan aplikasi hayati Trichoderma untuk mengendalikan Ganoderma'
                        ]
                    ],
                    [
                        'module' => 'Standardisasi Panen & Tuntas Mutu TPH',
                        'hours' => '16 JP',
                        'topics' => [
                            'Eliminasi panen buah mentah dan brondolan tertinggal',
                            'Perawatan tangkai buah dan penataan susunan TBS di TPH',
                            'Sinkronisasi jadwal panen dan kedatangan unit truk angkut'
                        ]
                    ]
                ],
                'career_path' => 'Peningkatan Kompetensi Teknis Profesional Mandor → Mandor 1 → Asisten Kebun Teladan'
            ],

            'teknikal-mill' => [
                'slug' => 'teknikal-mill',
                'code' => 'TEK-MILL',
                'title' => 'Teknikal Mill',
                'subtitle' => 'Keandalan Mesin, Efisiensi Pengolahan & Penekanan Losses PKS',
                'category_key' => 'teknikal',
                'category_name' => 'Kejuruan Teknikal',
                'target_role' => 'Operator Mesin, Mandor Pabrik, Asisten PKS, & Masinis',
                'duration' => 'Program Modular (2 - 5 Hari per Topik Spesifik)',
                'format' => 'Praktik Stasiun Mesin, Kalibrasi Sensor, Audit Losses, & Bedah Kerusakan',
                'image' => '/images/programs/mat_mill.jpg',
                'hero_image' => '/images/programs/mat_mill.jpg',
                'badge' => 'Kejuruan Teknikal Pabrik',
                'overview' => 'Program spesialisasi teknikal bagi tim operasional pabrik kelapa sawit untuk menjaga kontinuitas pengolahan TBS, memaksimalkan rendemen minyak (OER), menekan rasio kehilangan minyak (oil losses), serta menerapkan preventive maintenance mesin pabrik secara disiplin.',
                'objectives' => [
                    'Mengurangi angka downtime mendadak mesin akibat kerusakan fatal.',
                    'Menekan persentase oil losses di stasiun kempa, klarifikasi, dan janjang kosong.',
                    'Meningkatkan keterampilan mekanikal operator dalam melakukan overhaul ringan.',
                    'Memastikan kepatuhan keselamatan pengoperasian bejana uap boiler bertekanan tinggi.'
                ],
                'phases' => [
                    [
                        'name' => 'Tahap 1: Pengukuran Baseline Parameter Mesin',
                        'duration' => 'Hari 1',
                        'location' => 'PKS Lokasi Pelatihan',
                        'desc' => 'Pemeriksaan getaran, suhu bearing, tekanan uap, dan pengambilan sampel losses.'
                    ],
                    [
                        'name' => 'Tahap 2: Kalibrasi & Standardisasi Operasi',
                        'duration' => 'Hari 2 - 3',
                        'location' => 'Stasiun Mesin PKS',
                        'desc' => 'Praktik penyetelan ulang alat, alignment kopling, dan simulasi skenario gangguan.'
                    ],
                    [
                        'name' => 'Tahap 3: Uji Petik & Sertifikasi Keterampilan',
                        'duration' => 'Hari 4',
                        'location' => 'Ruang Teori & Stasiun',
                        'desc' => 'Uji kompetensi teknikal mandiri operator dan pemberian sertifikat keahlian internal.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Basic Mechanic for Mill Operators',
                        'hours' => '20 JP',
                        'topics' => [
                            'Dasar-dasar pelumasan bearing, penggantian oli gearbox, dan rantai konveyor',
                            'Pengenalan baut pengikat, torsi pengencangan, dan alignment pulley-belt',
                            'Deteksi dini bunyi aneh dan panas berlebih pada mesin putar'
                        ]
                    ],
                    [
                        'module' => 'Manajemen Operasional & Perawatan Boiler PKS',
                        'hours' => '24 JP',
                        'topics' => [
                            'Rasio perbandingan bahan bakar cangkang dan fiber sawit',
                            'Prosedur blowdown berkala untuk mencegah pembentukan kerak pipa air',
                            'Penanganan darurat kebocoran pipa boiler dan trip listrik otomatis'
                        ]
                    ],
                    [
                        'module' => 'Total Productive Maintenance (TPM) di PKS',
                        'hours' => '16 JP',
                        'topics' => [
                            'Autonomous Maintenance oleh operator stasiun masing-masing',
                            'Pencatatan riwayat kerusakan mesin di form logsheet harian',
                            'Perencanaan jadwal overhaul mingguan dan penyediaan suku cadang kritis'
                        ]
                    ],
                    [
                        'module' => 'Audit Kehilangan Minyak (Oil Losses Reduction)',
                        'hours' => '20 JP',
                        'topics' => [
                            'Metode sampling air limbah condesate, sludge separator, dan fiber press',
                            'Penyetelan tekanan hidrolik cone press untuk ekstraksi maksimal tanpa memecahkan biji',
                            'Monitoring efisiensi clarifier tank dan temperature control di tiap tangki'
                        ]
                    ]
                ],
                'career_path' => 'Operator Terlatih → Mandor Stasiun → Asisten Maintenance PKS'
            ],

            'teknikal-traksi' => [
                'slug' => 'teknikal-traksi',
                'code' => 'TEK-TRAKSI',
                'title' => 'Teknikal Traksi & Alat Berat',
                'subtitle' => 'Perawatan Dump Truck, Workshop Bengkel & Keselamatan Armada',
                'category_key' => 'teknikal',
                'category_name' => 'Kejuruan Teknikal',
                'target_role' => 'Mekanik Bengkel, Operator Alat Berat, Driver Dump Truck, & Asisten Traksi',
                'duration' => 'Program Modular (2 - 5 Hari per Modul)',
                'format' => 'Hands-on Workshop Bengkel, Uji Emisi, Defensive Driving di Jalur Tanah, & Praktik Las',
                'image' => '/images/programs/tat_traction.jpg',
                'hero_image' => '/images/programs/tat_traction.jpg',
                'badge' => 'Kejuruan Teknikal Traksi',
                'overview' => 'Program penguatan keahlian bengkel unit perkebunan guna mempertahankan keandalan armada angkutan TBS dan alat berat perbaikan infrastruktur jalan. Menitikberatkan pada preventive maintenance, efisiensi bahan bakar solar, manajemen usia ban, dan keselamatan berkendara di kontur jalan perkebunan.',
                'objectives' => [
                    'Meningkatkan keterampilan mekanik dalam mendiagnosis kerusakan mesin diesel common rail.',
                    'Menanamkan budaya defensive driving bagi driver guna menekan risiko insiden terbalik di jalan kebun.',
                    'Mengoptimalkan umur pakai ban melalui rotasi berkala dan pengawasan tekanan angin ban.',
                    'Menerapkan standardisasi bengkel 5R untuk keselamatan kerja mekanik.'
                ],
                'phases' => [
                    [
                        'name' => 'Tahap 1: Teori Sistem Mesin & Analisa Kerusakan',
                        'duration' => 'Hari 1',
                        'location' => 'Training Room Bengkel',
                        'desc' => 'Prinsip kerja komponen kendaraan, wiring diagram, dan standar keselamatan workshop.'
                    ],
                    [
                        'name' => 'Tahap 2: Bongkar Pasang & Perbaikan Komponen',
                        'duration' => 'Hari 2 - 3',
                        'location' => 'Bengkel Traksi Unit',
                        'desc' => 'Praktik overhaul transmisi, perbaikan hidrolik dump, dan kalibrasi pompa injeksi.'
                    ],
                    [
                        'name' => 'Tahap 3: Uji Jalan & Penilaian Kinerja',
                        'duration' => 'Hari 4',
                        'location' => 'Track Jalan Perkebunan',
                        'desc' => 'Uji performa armada setelah perbaikan dan uji kemahiran driver di medan tanjakan/lumpur.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Operation & Maintenance Dump Truck Mesin Euro 4',
                        'hours' => '24 JP',
                        'topics' => [
                            'Diagnosis sensor elektronik dan kode error OBD pada truk Euro 4',
                            'Perawatan filter solar ganda dan antisipasi kualitas biosolar B35',
                            'Pembersihan filter udara dan sistem intercooler'
                        ]
                    ],
                    [
                        'module' => 'Sistem Hidrolik Alat Berat (Excavator / Motor Grader)',
                        'hours' => '24 JP',
                        'topics' => [
                            'Penggantian seal cylinder bucket, boom, dan arm yang rembes',
                            'Pengujian tekanan relief valve sistem hidrolik',
                            'Flushing oli hidrolik dan penggantian filter magnetik'
                        ]
                    ],
                    [
                        'module' => 'Defensive Driving di Area Perkebunan',
                        'hours' => '16 JP',
                        'topics' => [
                            'Teknik pengereman di jalan tanah licin dan turunan curam bermuatan TBS',
                            'Manajemen kecepatan dan jarak aman konvoi truk di jalan poros perkebunan',
                            'Pemeriksaan wajib harian P2H sebelum menyalakan mesin'
                        ]
                    ],
                    [
                        'module' => 'Pengelolaan Umur Ban & Efisiensi Konsumsi BBM',
                        'hours' => '16 JP',
                        'topics' => [
                            'Pola rotasi ban depan-belakang sesuai aus telapak',
                            'Analisis penyebab keausan tidak merata (spooring/camber alami)',
                            'Teknik berkendara irit solar (Eco-Driving) di perkebunan'
                        ]
                    ]
                ],
                'career_path' => 'Mekanik Muda → Mekanik Senior → Kepala Bengkel Traksi (Mandor Traksi)'
            ],

            'teknikal-downstream' => [
                'slug' => 'teknikal-downstream',
                'code' => 'TEK-DOWNSTREAM',
                'title' => 'Teknikal Downstream',
                'subtitle' => 'Rafinasi CPO, Kristalisasi Fraksinasi, Otomasi DCS & Oleokimia',
                'category_key' => 'teknikal',
                'category_name' => 'Kejuruan Teknikal',
                'target_role' => 'Operator Kilang, Teknisi Instrumen/Otomasi, QC Chemist, & Supervisor Kilang',
                'duration' => 'Program Modular (2 - 5 Hari per Topik)',
                'format' => 'Simulasi DCS/SCADA, Kalibrasi Sensor, Audit Food Safety, & Praktik Laboratorium',
                'image' => '/images/programs/dmt_downstream.jpg',
                'hero_image' => '/images/programs/dmt_downstream.jpg',
                'badge' => 'Kejuruan Teknikal Downstream',
                'overview' => 'Program pelatihan penguasaan teknologi pemurnian minyak nabati di kilang industri hilir. Melatih teknisi dan operator dalam mengendalikan parameter kritis fraksinasi, pemurnian vakum, pemeliharaan pompa industri, serta pemenuhan standar mutu pangan global.',
                'objectives' => [
                    'Mengoptimalkan kejernihan minyak goreng dan yield fraksi Olein.',
                    'Mampu membaca alarm peringatan dini di panel DCS untuk mencegah shutdown kilang.',
                    'Menjaga kepatuhan standar sanitasi pangan ekspor (FSSC 22000 & Halal).',
                    'Menguasai teknik maintenance pompa industri dan mechanical seal.'
                ],
                'phases' => [
                    [
                        'name' => 'Tahap 1: Pengenalan Proses Kimia & P&ID',
                        'duration' => 'Hari 1',
                        'location' => 'Training Room Kilang',
                        'desc' => 'Alur proses kilang, parameter suhu/tekanan/laju alir, dan pengenalan instrumen.'
                    ],
                    [
                        'name' => 'Tahap 2: Simulasi Kontrol DCS & Troubleshooting',
                        'duration' => 'Hari 2 - 3',
                        'location' => 'Control Room & Kilang',
                        'desc' => 'Praktik penanganan gangguan fluktuasi vakum, kebocoran filter, dan pemulihan sistem.'
                    ],
                    [
                        'name' => 'Tahap 3: Verifikasi Mutu Produk Akhir',
                        'duration' => 'Hari 4',
                        'location' => 'Laboratorium QC',
                        'desc' => 'Pengujian sampel produk hasil simulasi terhadap parameter ekspor.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Refinery & Dry Fractionation System Lanjutan',
                        'hours' => '24 JP',
                        'topics' => [
                            'Pengendalian titik kristalisasi trigliserida jenuh dan tak jenuh',
                            'Optimasi siklus penekanan membran filter press fraksinasi',
                            'Troubleshooting kejernihan minyak (Cloud Point) saat musim dingin'
                        ]
                    ],
                    [
                        'module' => 'Operation & Maintenance Pompa Industri Kilang',
                        'hours' => '20 JP',
                        'topics' => [
                            'Perawatan mechanical seal berpendingin dan pencegahan kavitasi pompa',
                            'Penggantian impeller dan bearing pompa sentrifugal minyak panas',
                            'Alignment kopling laser motor penggerak pompa kilang'
                        ]
                    ],
                    [
                        'module' => 'Sistem Kalibrasi Instrumen & Otomasi PLC/DCS',
                        'hours' => '24 JP',
                        'topics' => [
                            'Kalibrasi control valve pneumatik dan transmitter 4-20 mA',
                            'Troubleshooting sinyal input/output PLC dan safety loop interlock',
                            'Pembersihan sensor suhu RTD dan mass flow meter Coriolis'
                        ]
                    ],
                    [
                        'module' => 'Food Safety & Kepatuhan Sertifikasi Ekspor',
                        'hours' => '16 JP',
                        'topics' => [
                            'Manajemen alergen, kontaminan fisik, dan bahaya kimia di industri pangan',
                            'Audit kesiapan inspeksi Halal, Kosher, RSPO, dan FSSC 22000',
                            'Sanitasi jalur pipa dan tangki penyimpanan (CIP System)'
                        ]
                    ]
                ],
                'career_path' => 'Operator Kilang Mahir → DCS Panel Operator → Shift Supervisor Downstream'
            ],

            'administrasi-k3' => [
                'slug' => 'administrasi-k3',
                'code' => 'TEK-ADM-K3',
                'title' => 'Administrasi, K3 & Sustainability',
                'subtitle' => 'Tata Kelola Administrasi Unit, Keselamatan Kerja & Kepatuhan Lingkungan',
                'category_key' => 'teknikal',
                'category_name' => 'Kejuruan Teknikal',
                'target_role' => 'Staf Tata Usaha, Tim Tanggap Darurat Karhutla, Officer K3 & Sustainability',
                'duration' => 'Program Modular (2 - 4 Hari per Topik)',
                'format' => 'Simulasi Kebakaran Lapangan, Audit SOP Dokumen, K3 First Aid, & Studi Kasus Legalitas',
                'image' => '/images/programs/aat_admin.jpg',
                'hero_image' => '/images/programs/aat_admin.jpg',
                'badge' => 'Kejuruan Administrasi & K3',
                'overview' => 'Program pelatihan yang memastikan kepatuhan regulasi lingkungan hidup, kesiapsiagaan darurat pencegahan kebakaran hutan dan lahan (Karhutla), perlindungan keselamatan kerja karyawan, serta tata kelola administrasi pembukuan unit yang rapi dan terstandardisasi.',
                'objectives' => [
                    'Meningkatkan kesiapsiagaan regu pemadam kebakaran kebun dengan respons waktu cepat.',
                    'Menjamin kepatuhan dokumen ketelusuran (traceability) audit sertifikasi ISPO & RSPO.',
                    'Meningkatkan akurasi pencatatan keluar masuk barang di gudang unit.',
                    'Mencetak personel P3K bersertifikat di setiap afdeling kebun dan pabrik.'
                ],
                'phases' => [
                    [
                        'name' => 'Tahap 1: Pembekalan Regulasi & Prosedur Operasional',
                        'duration' => 'Hari 1',
                        'location' => 'Training Center',
                        'desc' => 'Peraturan perundang-undangan K3, undang-undang lingkungan hidup, dan audit SOP administrasi.'
                    ],
                    [
                        'name' => 'Tahap 2: Simulasi Praktik Darurat & Tata Kelola',
                        'duration' => 'Hari 2 - 3',
                        'location' => 'Area Embung / Gudang Unit',
                        'desc' => 'Simulasi pemadaman api, evakuasi medis darurat, dan uji petik rekonsiliasi stok gudang.'
                    ],
                    [
                        'name' => 'Tahap 3: Uji Petik Kesiapsiagaan & Evaluasi',
                        'duration' => 'Hari 4',
                        'location' => 'Unit Terkait',
                        'desc' => 'Audit mendadak terhadap kelengkapan APAR, kotak P3K, dan kerapian arsip tata usaha.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Simulasi & Penanggulangan Karhutla Perkebunan',
                        'hours' => '24 JP',
                        'topics' => [
                            'Teknik penggelaran selang, pengoperasian pompa jinjing dan pompa apung',
                            'Pola formasi regu pemadam dan pembacaan arah mata angin di lahan gambut',
                            'Patroli titik api (Hotspot Monitoring) berbasis citra satelit dan menara pantau'
                        ]
                    ],
                    [
                        'module' => 'K3 First Aid & Tanggap Darurat Medis Lapangan',
                        'hours' => '16 JP',
                        'topics' => [
                            'Teknik Bantuan Hidup Dasar (CPR/RJP) pada korban henti napas',
                            'Penanganan luka gigitan ular berbisa dan patah tulang di pedalaman kebun',
                            'Prosedur evakuasi darurat menggunakan tandu lapangan ke klinik kebun'
                        ]
                    ],
                    [
                        'module' => 'Warehouse & Inventory Management Berbasis ERP',
                        'hours' => '20 JP',
                        'topics' => [
                            'Pencatatan mutasi pupuk, herbisida, dan sparepart di sistem digital',
                            'Tata letak gudang (Layouting) berdasarkan tingkat perputaran barang (Fast/Slow Moving)',
                            'Penanganan material Bahan Berbahaya dan Beracun (B3) serta Material Safety Data Sheet (MSDS)'
                        ]
                    ],
                    [
                        'module' => 'Kepatuhan Audit ISPO / RSPO & Ketelusuran TBS',
                        'hours' => '16 JP',
                        'topics' => [
                            'Dokumentasi rantai pasok TBS dari blok kebun berizin hingga pelabuhan',
                            'Pengelolaan kawasan Nilai Konservasi Tinggi (NKT / HCV) dan sempadan sungai',
                            'Penyusunan berkas bukti pemenuhan klausul audit berkala'
                        ]
                    ]
                ],
                'career_path' => 'Staf Administrasi/K3 → Officer Lingkungan & K3 Kebun → Koordinator Sustainability Regional'
            ],

            // =================================================================
            // 03. MANAGERIAL & LEADERSHIP (SOFT SKILLS & KEPEMIMPINAN)
            // =================================================================
            'managerial-leadership' => [
                'slug' => 'managerial-leadership',
                'code' => 'MGR-LEAD',
                'title' => 'Managerial & Leadership Development',
                'subtitle' => 'Pengembangan Karakter, Supervisi Berdaya & Kepemimpinan Insan First Resources',
                'category_key' => 'managerial',
                'category_name' => 'Managerial & Leadership',
                'target_role' => 'Mandor 1, Asisten, Askep, Mill Manager, Estate Manager, & Kepala Departemen',
                'duration' => 'Program Bertingkat (Workshop Intensif 2 - 4 Hari per Tingkatan Kepemimpinan)',
                'format' => 'Executive Workshop, Behavioral Coaching, Role-Playing Lapangan, & Analisa Studi Kasus Nyata',
                'image' => '/images/slide4.png',
                'hero_image' => '/images/slide4.png',
                'badge' => 'Managerial & Leadership',
                'overview' => 'Program strategis pengembangan kapasitas kepemimpinan bagi seluruh level hierarki manajemen operasional perkebunan. Berfokus pada pembentukan karakter insan First Resources yang berintegritas, kemampuan memimpin tim multikultural, pemecahan masalah operasional berbasis data, serta ketajaman eksekusi strategi bisnis perkebunan kelapa sawit.',
                'objectives' => [
                    'Membangun pola pikir kepemimpinan proaktif dan berorientasi hasil (results-oriented).',
                    'Mengembangkan kemampuan coaching dan mentoring untuk melipatgandakan kinerja bawahan.',
                    'Mengasah keterampilan Problem Solving & Decision Making (PSDM) terhadap krisis lapangan.',
                    'Menyelaraskan target individu afdeling/pabrik dengan visi jangka panjang perusahaan.'
                ],
                'phases' => [
                    [
                        'name' => 'Level 1: Supervisory Management (Mandor 1 & Asisten Baru)',
                        'duration' => '2 Hari Workshop',
                        'location' => 'Training Center Wilayah',
                        'desc' => 'Keterampilan supervisi harian, manajemen waktu, delegasi tugas yang jelas, dan teknik umpan balik (feedback) konstruktif.'
                    ],
                    [
                        'name' => 'Level 2: Leader as Coach (Asisten Senior & Askep)',
                        'duration' => '3 Hari Workshop + Pendampingan',
                        'location' => 'Pusat Pembinaan Terpadu',
                        'desc' => 'Seni mendengarkan aktif, menggali potensi tim tanpa instruksi searah, dan teknik mengurai konflik internal tenaga kerja.'
                    ],
                    [
                        'name' => 'Level 3: Strategic Executive Leadership (Manager & General Manager)',
                        'duration' => '4 Hari Retreat Kepemimpinan',
                        'location' => 'Executive Hall & Hotel Mitra',
                        'desc' => 'Analisis bisnis makro komoditas sawit, pengelolaan anggaran strategis, kepemimpinan transformasional, dan talent development.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'The 7 Habits of Highly Effective People',
                        'hours' => '24 JP',
                        'topics' => [
                            'Habit 1-3: Penguasaan diri (Proaktif, Memulai dengan Tujuan Akhir, Dahulukan yang Utama)',
                            'Habit 4-6: Kemenangan publik (Berpikir Menang-Menang, Berusaha Memahami Sebelum Dipahami, Sinergi)',
                            'Habit 7: Mengasah gergaji (Pembaruan berkelanjutan fisik, mental, emosional, dan spiritual)'
                        ]
                    ],
                    [
                        'module' => 'Leader As Coach untuk Supervisi Lapangan',
                        'hours' => '20 JP',
                        'topics' => [
                            'Metode Coaching GROW Model (Goal, Reality, Options, Will)',
                            'Teknik bertanya mendalam (Powerful Questioning) untuk menggugah inisiatif anak buah',
                            'Perbedaan antara Coaching, Mentoring, Counseling, dan Directing'
                        ]
                    ],
                    [
                        'module' => 'Problem Solving & Decision Making (PSDM) Berbasis Data',
                        'hours' => '20 JP',
                        'topics' => [
                            'Metode Analisis Situasi, Analisis Persoalan (Root Cause Analysis - Fishbone & 5-Why)',
                            'Analisis Keputusan (Decision Analysis) memilih alternatif solusi paling efektif',
                            'Analisis Persoalan Potensial (Potential Problem Analysis) mitigasi risiko sebelum implementasi'
                        ]
                    ],
                    [
                        'module' => 'Supervisory Excellence & Manajemen Konflik Lapangan',
                        'hours' => '16 JP',
                        'topics' => [
                            'Teknik menghadapi karyawan bermasalah dan penurunan motivasi kerja',
                            'Penyampaian apresiasi yang membakar semangat tim tanpa pilih kasih',
                            'Membangun budaya disiplin kerja tanpa menciptakan rasa takut'
                        ]
                    ],
                    [
                        'module' => 'Strategic Budgeting & Kinerja Bisnis Unit',
                        'hours' => '16 JP',
                        'topics' => [
                            'Penyelarasan Key Performance Indicators (KPI) dengan anggaran tahunan',
                            'Analisis varians biaya operasional bulanan dan langkah korektif cepat',
                            'Presentasi laporan kinerja di hadapan jajaran Direksi'
                        ]
                    ]
                ],
                'career_path' => 'Asisten → Asisten Kepala (Askep) → Estate / Mill Manager → General Manager Regional'
            ],

            // =================================================================
            // 04. TALENT MANAGEMENT
            // =================================================================
            'talent-management' => [
                'slug' => 'talent-management',
                'code' => 'TALENT-MGMT',
                'title' => 'Talent Management',
                'subtitle' => 'Pengembangan Talenta Berkelanjutan Berbasis Kerangka Kerja 70:20:10',
                'category_key' => 'talent',
                'category_name' => 'Talent Management',
                'target_role' => 'Karyawan Berkinerja Tinggi (High Performers) Seluruh Divisi',
                'duration' => 'Program Berkelanjutan Terintegrasi (1 - 2 Tahun Siklus Evaluasi)',
                'format' => 'Job Enrichment, Strategic Project Assignment, Mentoring Eksekutif, & Rotasi Lintas Fungsi',
                'image' => '/images/talent.png',
                'hero_image' => '/images/talent.png',
                'badge' => 'Talent Management',
                'overview' => 'Inisiatif strategis First Resources Academy dalam mengidentifikasi, mengasah, dan membina karier para kader pemimpin masa depan (Succession Pipeline). Menggunakan metodologi pembinaan terbukti 70:20:10 untuk mencetak pemimpin tangguh yang siap mengemban amanah strategis perusahaan.',
                'objectives' => [
                    'Mengamankan ketersediaan suksesi kepemimpinan di seluruh unit operasional kebun dan pabrik.',
                    'Memberikan penugasan nyata pemecahan problem strategis bernilai ekonomi tinggi bagi grup.',
                    'Memfasilitasi transfer pengetahuan kepemimpinan langsung dari para pimpinan puncak perusahaan.',
                    'Membangun jalur karir transparan dan berkeadilan berbasis meritokrasi dan pencapaian nyata.'
                ],
                'phases' => [
                    [
                        'name' => 'Fase 01: Talent Identification & 9-Box Grid Mapping',
                        'duration' => 'Evaluasi Kuartal 1',
                        'location' => 'Seluruh Wilayah Operasional',
                        'desc' => 'Pemetaan talenta berdasarkan perpaduan antara kinerja historis (Performance) dan potensi kepemimpinan (Potential).'
                    ],
                    [
                        'name' => 'Fase 02: Individual Development Plan (IDP) & Strategic Project',
                        'duration' => 'Bulan 4 - 10',
                        'location' => 'Penugasan Khusus di Unit / Head Office',
                        'desc' => 'Pelaksanaan proyek inovasi efisiensi operasional dengan pendampingan mentor pimpinan senior.'
                    ],
                    [
                        'name' => 'Fase 03: Executive Presentation & Promosi',
                        'duration' => 'Akhir Tahun',
                        'location' => 'Direksi Operasional',
                        'desc' => 'Presentasi capaian proyek inovasi dan penentuan jalur promosi ke jenjang manajerial berikutnya.'
                    ]
                ],
                'syllabus' => [
                    [
                        'module' => 'Pilar 70%: Experiential Learning (Penugasan Lapangan Nyata)',
                        'hours' => '70% Porsi Pembinaan',
                        'topics' => [
                            'Strategic Improvement Project mengatasi kendala operasional spesifik',
                            'Rotasi penugasan lintas afdeling atau penugasan audit kendali mutu',
                            'Peran pengganti sementara (Acting Position) saat pejabat definitif cuti'
                        ]
                    ],
                    [
                        'module' => 'Pilar 20%: Social Learning (Mentoring & Feedback Eksekutif)',
                        'hours' => '20% Porsi Pembinaan',
                        'topics' => [
                            'Sesi mentoring rutin satu-lawan-satu dengan General Manager atau Direktur',
                            'Peer learning dan sharing session antar talenta terbaik lintas regional',
                            'Evaluasi umpan balik 360 derajat dari atasan, rekan sejawat, dan bawahan'
                        ]
                    ],
                    [
                        'module' => 'Pilar 10%: Formal Learning (Executive Masterclass & Benchmarking)',
                        'hours' => '10% Porsi Pembinaan',
                        'topics' => [
                            'Executive Masterclass bersama pakar industri perkebunan terkemuka',
                            'Studi banding ke fasilitas operasional unggulan dan industri hilir tercanggih',
                            'Sertifikasi profesi manajemen perkebunan terakreditasi'
                        ]
                    ]
                ],
                'career_path' => 'High Performer → Fast-Track Talent Pool → Pemimpin Kunci Operasional & Manajemen Puncak'
            ],
        ];
    }

    /**
     * Get a single program by its slug.
     */
    public static function getProgramBySlug(string $slug): ?array
    {
        $programs = self::getAllPrograms();
        return $programs[$slug] ?? null;
    }

    /**
     * Get programs grouped by their primary categories.
     */
    public static function getGroupedPrograms(): array
    {
        $all = self::getAllPrograms();
        $grouped = [
            'trainee' => [
                'name' => 'Program Trainee (Reguler)',
                'badge' => '5 Jalur Karir',
                'description' => 'Kaderisasi calon Asisten selama 6 bulan melalui jalur FAT Kebun, MAT Pabrik, TAT Traksi, AAT Administrasi, dan DMT Kilang Hilir.',
                'items' => []
            ],
            'teknikal' => [
                'name' => 'Kejuruan Teknikal',
                'badge' => '5 Bidang Kejuruan',
                'description' => 'Program peningkatan keahlian spesifik di bidang Teknikal Estate, Teknikal Mill, Teknikal Traksi, Teknikal Downstream, dan Administrasi & K3.',
                'items' => []
            ],
            'managerial' => [
                'name' => 'Managerial & Leadership',
                'badge' => 'Soft Skills & Kepemimpinan',
                'description' => 'Pengembangan kapasitas kepemimpinan, supervisi mandor, coaching, dan karakter insan First Resources di semua level hierarki.',
                'items' => []
            ],
            'talent' => [
                'name' => 'Talent Management',
                'badge' => 'Talent Management',
                'description' => 'Inisiatif pembinaan karir berbasis metode 70:20:10 guna mempersiapkan suksesi pemimpin masa depan.',
                'items' => []
            ]
        ];

        foreach ($all as $program) {
            $key = $program['category_key'] ?? 'trainee';
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'name' => $program['category_name'] ?? ucfirst($key),
                    'badge' => 'Program',
                    'description' => '',
                    'items' => []
                ];
            }
            $grouped[$key]['items'][] = $program;
        }

        return $grouped;
    }

    /**
     * Get programs grouped into sub-bab: Reguler and Non-Reguler.
     */
    public static function getProgramsGroupedBySubBab(): array
    {
        $all = self::getAllPrograms();

        $reguler = [];
        $nonReguler = [];

        foreach ($all as $slug => $prog) {
            if (!empty($prog['is_reguler'])) {
                $reguler[$slug] = $prog;
            } else {
                $nonReguler[$slug] = $prog;
            }
        }

        return [
            'reguler' => [
                'name' => 'Program Reguler',
                'badge' => count($reguler) . ' Program Trainee',
                'description' => 'Program kaderisasi calon Asisten operasional (FAT, MAT, TAT, AAT, DMT) selama 6 bulan melalui In-Class & On-the-Job Training.',
                'color' => 'blue',
                'items' => $reguler,
            ],
            'non_reguler' => [
                'name' => 'Program Non-Reguler',
                'badge' => count($nonReguler) . ' Program Kejuruan & Leadership',
                'description' => 'Program keahlian managerial, teknikal spesialis kelapa sawit, administrasi & K3, serta manajemen talenta karir.',
                'color' => 'orange',
                'items' => $nonReguler,
            ],
        ];
    }
}
