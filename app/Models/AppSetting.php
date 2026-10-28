<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        return $setting !== null ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }

    /**
     * Get image URL with fallback to default asset.
     */
    public static function getImageUrl(string $key, string $default): string
    {
        $val = static::get($key);
        if (! $val) {
            return str_starts_with($default, 'http://') || str_starts_with($default, 'https://')
                ? $default
                : asset($default);
        }
        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        return Storage::url($val);
    }

    /**
     * Format a slide path to full URL.
     */
    public static function formatSlideUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::url($path);
    }

    /**
     * Get hero slides array (full URLs for display).
     * Supports unlimited dynamic slides.
     */
    public static function getHeroSlides(): array
    {
        $raw = static::getRawHeroSlides();

        return array_map([static::class, 'formatSlideUrl'], $raw);
    }

    /**
     * Get raw hero slide paths.
     */
    public static function getRawHeroSlides(): array
    {
        $val = static::get('home_hero_slides');
        if ($val) {
            $decoded = json_decode($val, true);
            if (is_array($decoded) && ! empty($decoded)) {
                return array_values($decoded);
            }
        }

        // Backward compatibility if home_hero_slide_1..4 were set
        $legacy = [];
        for ($i = 1; $i <= 4; $i++) {
            $path = static::get("home_hero_slide_{$i}");
            if ($path) {
                $legacy[] = $path;
            }
        }

        if (! empty($legacy)) {
            return $legacy;
        }

        // Default 4 slides
        return [
            'images/welcomefr.jpg',
            'images/slide2.png',
            'images/slide3.png',
            'images/slide4.png',
        ];
    }

    /**
     * Default list of portal menu links.
     */
    public static function defaultPortalLinks(): array
    {
        return [
            [
                'id' => 'elearning',
                'name' => 'E-Learning',
                'url' => 'https://e-learning.first-resources.com',
                'target' => '_blank',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'id' => 'mess',
                'name' => 'Mess FR Academy',
                'url' => 'https://mess-fr-academy.my.id/',
                'target' => '_blank',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'id' => 'peta_sdm',
                'name' => 'Peta SDM Operasional',
                'url' => 'https://petasdm-academy.first-resources.com/',
                'target' => '_blank',
                'is_active' => true,
                'order' => 3,
            ],
        ];
    }

    /**
     * Get all portal menu links.
     *
     * @param  bool  $onlyActive  If true, only returns active links.
     */
    public static function getPortalLinks(bool $onlyActive = false): array
    {
        $val = static::get('portal_menu_links');
        if (! $val) {
            $links = static::defaultPortalLinks();
        } else {
            $decoded = json_decode($val, true);
            $links = is_array($decoded) && ! empty($decoded) ? $decoded : static::defaultPortalLinks();
        }

        // Sort by order ascending
        usort($links, function ($a, $b) {
            $orderA = isset($a['order']) ? (int) $a['order'] : 0;
            $orderB = isset($b['order']) ? (int) $b['order'] : 0;

            return $orderA <=> $orderB;
        });

        if ($onlyActive) {
            $links = array_values(array_filter($links, function ($item) {
                return ! empty($item['is_active']);
            }));
        }

        return $links;
    }

    /**
     * Save portal menu links.
     */
    public static function savePortalLinks(array $links): void
    {
        static::set('portal_menu_links', json_encode(array_values($links)));
    }

    /**
     * Format any media path or external URL to full web accessible URL.
     */
    public static function formatMediaUrl(?string $path, string $default = ''): string
    {
        if (! $path) {
            $path = $default;
        }
        if (! $path) {
            return '';
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::url($path);
    }

    /**
     * Default list of dynamic pillar cards.
     */
    public static function defaultPillarCards(): array
    {
        return [
            [
                'id' => 'p1',
                'title' => 'Regular Training Program',
                'desc' => 'Program pengembangan berkesinambungan selama enam bulan dengan pendampingan mentoring untuk menyiapkan calon staf dan asisten perkebunan.',
                'btn_text' => 'Pelajari lebih lanjut',
                'btn_url' => '/program/fat',
                'image' => 'images/slide3.png',
                'icon' => 'images/reguler.png',
            ],
            [
                'id' => 'p2',
                'title' => 'Non-Regular Training Program',
                'desc' => 'Pelatihan berdurasi singkat untuk meningkatkan kompetensi teknis dan manajerial staf operasional di Kebun, Pabrik (Mill), Traksi, hingga Administrasi.',
                'btn_text' => 'Pelajari lebih lanjut',
                'btn_url' => '/program/managerial-leadership',
                'image' => 'https://i.ibb.co.com/3mg5bHQN/image-14.png',
                'icon' => 'images/nonreguler.png',
            ],
            [
                'id' => 'p3',
                'title' => 'Talent Management Program',
                'desc' => 'Program akselerasi terstruktur yang dirancang khusus untuk mempersiapkan staf berpotensi (high talent) menjadi pemimpin masa depan perusahaan.',
                'btn_text' => 'Pelajari lebih lanjut',
                'btn_url' => '/program/talent-management',
                'image' => 'https://i.ibb.co.com/XrLSNbzC/image-13.png',
                'icon' => 'images/talent.png',
            ],
        ];
    }

    /**
     * Get dynamic pillar cards list.
     */
    public static function getPillarCards(): array
    {
        $val = static::get('home_pillars_cards');
        if (! $val) {
            $cards = static::defaultPillarCards();
        } else {
            $decoded = json_decode($val, true);
            $cards = is_array($decoded) && ! empty($decoded) ? $decoded : static::defaultPillarCards();
        }

        foreach ($cards as &$card) {
            $card['image_url'] = static::formatMediaUrl($card['image'] ?? 'images/slide3.png');
            $card['icon_url'] = static::formatMediaUrl($card['icon'] ?? 'images/reguler.png');
        }

        return array_values($cards);
    }

    /**
     * Default list of points for Accordion 1 (Program Reguler).
     */
    public static function defaultAcc1Items(): array
    {
        return [
            [
                'title' => 'Field Assistant Training (FAT)',
                'desc' => 'Program yang dirancang untuk menyiapkan calon Asisten Kebun agar memiliki pengetahuan, keterampilan, dan sikap kerja sesuai budaya First Resources.',
            ],
            [
                'title' => 'Mill Assistant Training (MAT)',
                'desc' => 'Program yang dirancang untuk menyiapkan calon Asisten Pabrik dengan kompetensi teknis dan kepemimpinan yang sesuai kebutuhan operasional pabrik.',
            ],
            [
                'title' => 'Traction Assistant Training (TAT)',
                'desc' => 'Program untuk menyiapkan calon Asisten Traksi yang memiliki kemampuan teknis dan manajerial sesuai standar perusahaan.',
            ],
            [
                'title' => 'Administration Assistant Training (AAT)',
                'desc' => 'Program untuk mempersiapkan calon Asisten Administrasi yang memiliki kompetensi administrasi, analisis, dan pengelolaan kerja sesuai budaya perusahaan.',
            ],
            [
                'title' => 'Downstream Management Trainee (DMT)',
                'desc' => 'Program yang dirancang untuk menyiapkan calon supervisor di unit downstream agar memiliki kompetensi teknis dan perilaku kerja yang sesuai dengan kebutuhan operasional.',
            ],
            [
                'title' => 'Management Associates Program (MAP)',
                'desc' => 'Program khusus yang ditujukan untuk mempersiapkan high talent menjadi future leader di berbagai business unit. Program ini dirancang sesuai kebutuhan operasional dan mencakup seluruh aspek bisnis perusahaan, mulai dari upstream, downstream, hingga support department. MAP diharapkan menjadi talent pool bagi kader pemimpin perusahaan di masa depan.',
            ],
        ];
    }

    /**
     * Default list of points for Accordion 2 (Program Non-Reguler).
     */
    public static function defaultAcc2Items(): array
    {
        return [
            [
                'title' => 'Teknikal Estate',
                'desc' => 'Training untuk meningkatkan kompetensi teknis para staf di Kebun seperti: Klinik Kerja Mandor, Estate Coaching Program, Pengelolaan TBM, Klinik HPT, Manajemen Pemupukan dll.',
            ],
            [
                'title' => 'Teknikal Mill',
                'desc' => 'Training untuk meningkatkan kompentensi para staf di Pabrik, seperti: Mill Mentoring, Manajemen Operasional Boiler, Pengelolaan Water Treatment, Total Productive Maintenance, Klinik Kinerja Pabrik, dll.',
            ],
            [
                'title' => 'Teknikal Traksi',
                'desc' => 'Training untuk meningkatkan kompentensi para staf di Traksi seperti: Manajemen Workshop, Operation and Maintenance DT, Perawatan Genset, Defensive Driving, Overhaul DT Euro 4, dll.',
            ],
            [
                'title' => 'Teknikal Downstream',
                'desc' => 'Training untuk meningkatkan kompentensi para staf di Downstream seperti: Operation and Maintenance Pump, Ref and Frac Operation for Operator, Sistem Kalibrasi PLC, Operasional Oleochemical for Operator, dll.',
            ],
            [
                'title' => 'Teknikal Administrasi & Sustain',
                'desc' => 'Training untuk meningkatkan kompentensi tenaga Administrasi dan Sustainability, seperti: Pemadaman Kebakaran Hutan dan Lahan, K3-First Aid, Mentoring Asisten Administrasi, Pelatihan Pamong TPA, dll.',
            ],
            [
                'title' => 'Managerial & Softskill',
                'desc' => 'Training untuk meningkatkan kompetensi Softskills seluruh staf, seperti: Supervisory Management untuk Asisten, Fundamental Leadership untuk Askep, Strategic Leadership untuk Manager, Problem Solving and Decision Making, dll.',
            ],
        ];
    }

    /**
     * Default list of points for Accordion 3 (Inisiatif Program).
     */
    public static function defaultAcc3Items(): array
    {
        return [
            [
                'title' => 'Klinik Kinerja Mandor',
            ],
            [
                'title' => 'Learning Festival',
            ],
        ];
    }

    /**
     * Get accordion items by accordion number (1, 2, or 3).
     */
    public static function getAccordionItems(int $number): array
    {
        $key = "home_acc{$number}_items";
        $defaultMethod = "defaultAcc{$number}Items";
        $defaults = method_exists(static::class, $defaultMethod) ? static::$defaultMethod() : [];

        $val = static::get($key);
        if (! $val) {
            return $defaults;
        }

        $decoded = json_decode($val, true);

        return is_array($decoded) && ! empty($decoded) ? array_values($decoded) : $defaults;
    }

    /**
     * Save accordion items by accordion number (1, 2, or 3).
     */
    public static function saveAccordionItems(int $number, array $items): void
    {
        $clean = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $title = trim($item['title'] ?? '');
            if ($title === '') {
                continue;
            }
            $cleanItem = ['title' => $title];
            if (isset($item['desc'])) {
                $cleanItem['desc'] = trim($item['desc']);
            }
            $clean[] = $cleanItem;
        }

        static::set("home_acc{$number}_items", json_encode(array_values($clean)));
    }

    /**
     * Default list of accordions.
     */
    public static function defaultAccordions(): array
    {
        return [
            [
                'id' => 'acc_1',
                'title' => 'PROGRAM REGULER',
                'desc' => 'Program pelatihan dasar bagi karyawan baru maupun calon karyawan yang akan ditempatkan di berbagai kebun operasional.',
                'items' => static::defaultAcc1Items(),
            ],
            [
                'id' => 'acc_2',
                'title' => 'PROGRAM NON-REGULER',
                'desc' => 'Pelatihan khusus yang dirancang sesuai dengan kebutuhan spesifik divisi atau tantangan operasional terkini.',
                'items' => static::defaultAcc2Items(),
            ],
            [
                'id' => 'acc_3',
                'title' => 'INISIATIF PROGRAM',
                'desc' => 'Beragam inisiatif strategis dan kegiatan komprehensif yang dirancang khusus untuk mendorong budaya inovasi, peningkatan kompetensi, serta kolaborasi antar divisi:',
                'items' => static::defaultAcc3Items(),
            ],
        ];
    }

    /**
     * Get all dynamic accordions.
     */
    public static function getAccordions(): array
    {
        $val = static::get('home_accordions');
        if (! $val) {
            // Check legacy individual keys
            $t1 = static::get('home_acc1_title');
            if ($t1) {
                return [
                    [
                        'id' => 'acc_1',
                        'title' => static::get('home_acc1_title', 'PROGRAM REGULER'),
                        'desc' => static::get('home_acc1_desc', ''),
                        'items' => static::getAccordionItems(1),
                    ],
                    [
                        'id' => 'acc_2',
                        'title' => static::get('home_acc2_title', 'PROGRAM NON-REGULER'),
                        'desc' => static::get('home_acc2_desc', ''),
                        'items' => static::getAccordionItems(2),
                    ],
                    [
                        'id' => 'acc_3',
                        'title' => static::get('home_acc3_title', 'INISIATIF PROGRAM'),
                        'desc' => static::get('home_acc3_desc', ''),
                        'items' => static::getAccordionItems(3),
                    ],
                ];
            }

            return static::defaultAccordions();
        }

        $decoded = json_decode($val, true);

        return is_array($decoded) && ! empty($decoded) ? array_values($decoded) : static::defaultAccordions();
    }

    /**
     * Save dynamic accordions.
     */
    public static function saveAccordions(array $accordions): void
    {
        $clean = [];
        foreach ($accordions as $idx => $acc) {
            if (! is_array($acc)) {
                continue;
            }
            $title = trim($acc['title'] ?? '');
            if ($title === '') {
                continue;
            }
            $cleanItems = [];
            if (isset($acc['items']) && is_array($acc['items'])) {
                foreach ($acc['items'] as $it) {
                    if (! is_array($it)) {
                        continue;
                    }
                    $itTitle = trim($it['title'] ?? '');
                    if ($itTitle === '') {
                        continue;
                    }
                    $cleanIt = ['title' => $itTitle];
                    if (isset($it['desc']) && trim($it['desc']) !== '') {
                        $cleanIt['desc'] = trim($it['desc']);
                    }
                    $cleanItems[] = $cleanIt;
                }
            }

            $clean[] = [
                'id' => $acc['id'] ?? ('acc_'.($idx + 1)),
                'title' => $title,
                'desc' => trim($acc['desc'] ?? ''),
                'items' => $cleanItems,
            ];
        }

        static::set('home_accordions', json_encode(array_values($clean)));
    }

    /**
     * Default list of Program Navigation Menus (Header & Dropdown).
     */
    public static function defaultProgramNavMenus(): array
    {
        return [
            [
                'id' => 'menu_reguler',
                'key' => 'reguler',
                'name' => 'Program Reguler',
                'has_submenu' => true,
                'url' => null,
                'order' => 1,
                'is_active' => true,
                'submenus' => [
                    ['id' => 'sub_fat', 'name' => 'Program FAT', 'url' => '/program/fat', 'order' => 1, 'is_active' => true],
                    ['id' => 'sub_mat', 'name' => 'Program MAT', 'url' => '/program/mat', 'order' => 2, 'is_active' => true],
                    ['id' => 'sub_tat', 'name' => 'Program TAT', 'url' => '/program/tat', 'order' => 3, 'is_active' => true],
                    ['id' => 'sub_dmt', 'name' => 'Program DMT', 'url' => '/program/dmt', 'order' => 4, 'is_active' => true],
                    ['id' => 'sub_aat', 'name' => 'Program AAT', 'url' => '/program/aat', 'order' => 5, 'is_active' => true],
                    ['id' => 'sub_pmt', 'name' => 'Program PMT', 'url' => '/program/pmt', 'order' => 6, 'is_active' => true],
                    ['id' => 'sub_ma', 'name' => 'Program Management Associate', 'url' => '/program/management-associate', 'order' => 7, 'is_active' => true],
                ],
            ],
            [
                'id' => 'menu_managerial',
                'key' => 'managerial',
                'name' => 'Managerial & Leadership',
                'has_submenu' => true,
                'url' => null,
                'order' => 2,
                'is_active' => true,
                'submenus' => [
                    ['id' => 'sub_hic', 'name' => 'High Impact Communication', 'url' => '/program/high-impact-communication', 'order' => 1, 'is_active' => true],
                    ['id' => 'sub_cip', 'name' => 'Continuous Improvement Program', 'url' => '/program/continuous-improvement-program', 'order' => 2, 'is_active' => true],
                ],
            ],
            [
                'id' => 'menu_estate',
                'key' => 'estate',
                'name' => 'Estate',
                'has_submenu' => true,
                'url' => null,
                'order' => 3,
                'is_active' => true,
                'submenus' => [
                    ['id' => 'sub_kkm', 'name' => 'Klinik Kerja Mandor', 'url' => '/program/klinik-kerja-mandor', 'order' => 1, 'is_active' => true],
                    ['id' => 'sub_spp', 'name' => 'Sistem Pengancakan Panen', 'url' => '/program/sistem-pengancakan-panen', 'order' => 2, 'is_active' => true],
                ],
            ],
            [
                'id' => 'menu_mill',
                'key' => 'mill',
                'name' => 'Mill',
                'has_submenu' => true,
                'url' => null,
                'order' => 4,
                'is_active' => true,
                'submenus' => [
                    ['id' => 'sub_wtp', 'name' => 'Pengelolaan Water Treatment', 'url' => '/program/pengelolaan-water-treatment', 'order' => 1, 'is_active' => true],
                    ['id' => 'sub_pmp', 'name' => 'Manajemen perawatan pompa', 'url' => '/program/manajemen-perawatan-pompa', 'order' => 2, 'is_active' => true],
                ],
            ],
            [
                'id' => 'menu_traksi',
                'key' => 'traksi',
                'name' => 'Traksi',
                'has_submenu' => true,
                'url' => null,
                'order' => 5,
                'is_active' => true,
                'submenus' => [
                    ['id' => 'sub_rpdt', 'name' => 'Refreshment Perawatan Dump Truck', 'url' => '/program/refreshment-perawatan-dump-truck', 'order' => 1, 'is_active' => true],
                    ['id' => 'sub_dd', 'name' => 'Defensive Driving', 'url' => '/program/defensive-driving', 'order' => 2, 'is_active' => true],
                ],
            ],
            [
                'id' => 'menu_downstream',
                'key' => 'downstream',
                'name' => 'Downstream',
                'has_submenu' => true,
                'url' => null,
                'order' => 6,
                'is_active' => true,
                'submenus' => [
                    ['id' => 'sub_so', 'name' => 'Super Olein Training', 'url' => '/program/super-olein-training', 'order' => 1, 'is_active' => true],
                ],
            ],
            [
                'id' => 'menu_administrasi',
                'key' => 'administrasi',
                'name' => 'Administrasi',
                'has_submenu' => true,
                'url' => null,
                'order' => 7,
                'is_active' => true,
                'submenus' => [
                    ['id' => 'sub_damkar', 'name' => 'Damkarhutlabun', 'url' => '/program/damkarhutlabun', 'order' => 1, 'is_active' => true],
                    ['id' => 'sub_k3', 'name' => 'K3 First Aid', 'url' => '/program/k3-first-aid', 'order' => 2, 'is_active' => true],
                ],
            ],
            [
                'id' => 'menu_talent',
                'key' => 'talent',
                'name' => 'Talent Management',
                'has_submenu' => false,
                'url' => '/program/talent-management',
                'order' => 8,
                'is_active' => true,
                'submenus' => [],
            ],
        ];
    }

    /**
     * Get Program Navigation Menus.
     */
    public static function getProgramNavMenus(bool $onlyActive = false): array
    {
        $val = static::get('header_program_nav_menus');
        if (! $val) {
            $menus = static::defaultProgramNavMenus();
        } else {
            $decoded = json_decode($val, true);
            $menus = is_array($decoded) && ! empty($decoded) ? $decoded : static::defaultProgramNavMenus();
        }

        // Sort by order
        usort($menus, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        if ($onlyActive) {
            $menus = array_filter($menus, fn ($m) => ! empty($m['is_active']));
        }

        foreach ($menus as &$menu) {
            if (! isset($menu['submenus']) || ! is_array($menu['submenus'])) {
                $menu['submenus'] = [];
            } else {
                usort($menu['submenus'], fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
                if ($onlyActive) {
                    $menu['submenus'] = array_values(array_filter($menu['submenus'], fn ($s) => ! empty($s['is_active'])));
                }
            }
        }

        return array_values($menus);
    }

    /**
     * Save Program Navigation Menus.
     */
    public static function saveProgramNavMenus(array $menus): void
    {
        $clean = [];
        foreach ($menus as $idx => $menu) {
            if (! is_array($menu)) {
                continue;
            }
            $name = trim($menu['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $cleanSubmenus = [];
            if (isset($menu['submenus']) && is_array($menu['submenus'])) {
                foreach ($menu['submenus'] as $subIdx => $sub) {
                    if (! is_array($sub)) {
                        continue;
                    }
                    $subName = trim($sub['name'] ?? '');
                    if ($subName === '') {
                        continue;
                    }
                    $cleanSubmenus[] = [
                        'id' => $sub['id'] ?? ('sub_'.time().'_'.$subIdx),
                        'name' => $subName,
                        'url' => trim($sub['url'] ?? '#'),
                        'order' => isset($sub['order']) ? (int) $sub['order'] : ($subIdx + 1),
                        'is_active' => isset($sub['is_active']) ? (bool) $sub['is_active'] : true,
                    ];
                }
                usort($cleanSubmenus, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
            }

            $hasSubmenu = ! empty($menu['has_submenu']);
            $key = trim($menu['key'] ?? '') ?: \Illuminate\Support\Str::slug($name);

            $clean[] = [
                'id' => $menu['id'] ?? ('menu_'.time().'_'.$idx),
                'key' => $key,
                'name' => $name,
                'has_submenu' => $hasSubmenu,
                'url' => $hasSubmenu ? null : trim($menu['url'] ?? '#'),
                'order' => isset($menu['order']) ? (int) $menu['order'] : ($idx + 1),
                'is_active' => isset($menu['is_active']) ? (bool) $menu['is_active'] : true,
                'submenus' => $cleanSubmenus,
            ];
        }

        usort($clean, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        static::set('header_program_nav_menus', json_encode(array_values($clean)));
    }
}
