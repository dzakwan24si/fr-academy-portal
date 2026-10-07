<x-layout>
    <x-slot name="title">Standar Kompetensi Teknis - FR Academy</x-slot>

    <style>
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                margin: 10mm 12mm;
            }

            header, footer, nav, .no-print, #competency-type-tabs {
                display: none !important;
            }

            body, main {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            section {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                background: transparent !important;
            }

            .print-full {
                width: 100% !important;
                max-width: 100% !important;
                grid-column: 1 / -1 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            article {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Hilangkan scrollbar & sticky di mode cetak */
            .sticky, .overflow-x-auto, .overflow-y-auto {
                position: static !important;
                overflow: visible !important;
            }

            /* Format Matriks Tabel Cetak agar 100% fit dan tidak terpotong */
            .competency-matrix, #competency-matrix {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
                font-size: 10.5px !important;
                margin-top: 12px !important;
            }

            .competency-matrix th, .competency-matrix td,
            #competency-matrix th, #competency-matrix td {
                word-wrap: break-word !important;
                white-space: normal !important;
                padding: 6px 8px !important;
                border: 1px solid #cbd5e1 !important;
                vertical-align: top !important;
            }

            /* Alokasi Lebar Kolom yang Presisi untuk A4 Portrait */
            .competency-matrix th:first-child, .competency-matrix td:first-child,
            #competency-matrix th:first-child, #competency-matrix td:first-child {
                width: 18% !important;
                font-weight: 700 !important;
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
            }

            .competency-matrix th:nth-child(2), .competency-matrix td:nth-child(2),
            #competency-matrix th:nth-child(2), #competency-matrix td:nth-child(2) { width: 20.5% !important; }
            .competency-matrix th:nth-child(3), .competency-matrix td:nth-child(3),
            #competency-matrix th:nth-child(3), #competency-matrix td:nth-child(3) { width: 20.5% !important; }
            .competency-matrix th:nth-child(4), .competency-matrix td:nth-child(4),
            #competency-matrix th:nth-child(4), #competency-matrix td:nth-child(4) { width: 20.5% !important; }
            .competency-matrix th:nth-child(5), .competency-matrix td:nth-child(5),
            #competency-matrix th:nth-child(5), #competency-matrix td:nth-child(5) { width: 20.5% !important; }

            /* Warna Khusus Header Level pada Cetak */
            .competency-matrix th:nth-child(2), #competency-matrix th:nth-child(2) { background-color: #f0fdf4 !important; color: #166534 !important; }
            .competency-matrix th:nth-child(3), #competency-matrix th:nth-child(3) { background-color: #dcfce7 !important; color: #14532d !important; }
            .competency-matrix th:nth-child(4), #competency-matrix th:nth-child(4) { background-color: #bbf7d0 !important; color: #052e16 !important; }
            .competency-matrix th.bg-teal-primary, .competency-matrix th:nth-child(5),
            #competency-matrix th.bg-teal-primary, #competency-matrix th:nth-child(5) {
                background-color: #0f6259 !important;
                color: #ffffff !important;
                font-weight: 800 !important;
            }

            /* Penataan Butir Pengetahuan / Keterampilan / Development */
            .grid {
                display: block !important;
            }

            #competency-items-knowledge,
            #competency-items-skill,
            #competency-items-development {
                page-break-inside: avoid;
                break-inside: avoid;
                margin-top: 14px !important;
                border: 1px solid #e2e8f0 !important;
                background-color: #ffffff !important;
            }

            .print-page-break {
                page-break-after: always !important;
                break-after: page !important;
            }

            tr[style*="display: none"] { display: none !important; }
        }
    </style>

    @if(($isAll ?? false) && ($autoPrint ?? false))
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () { window.print(); }, 500);
            });
        </script>
    @endif

    <!-- Hero -->
    <section class="no-print relative h-[260px] md:h-[300px] pt-20 overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/slide2.png') }}" alt="Standar Kompetensi FR Academy" class="w-full h-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-teal-primary/60 to-black/60"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
            <span class="text-green-accent font-bold tracking-widest uppercase text-xs md:text-sm mb-3 block">Kamus Kompetensi</span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-3">Standar Kompetensi Teknis</h1>
            <p class="text-base md:text-lg text-teal-100 max-w-2xl mx-auto font-light">
                Acuan kemampuan setiap jabatan, dari Basic hingga Expert, untuk bidang Estate dan Mill / PKS.
            </p>
        </div>
    </section>

    @if($empty)
        <section class="py-24 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-2xl mx-auto px-4 text-center">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-teal-50 dark:bg-gray-800 flex items-center justify-center text-teal-primary dark:text-teal-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Belum ada standar kompetensi yang dipublikasikan</h2>
                <p class="text-gray-500 dark:text-gray-400">Silakan kembali lagi nanti.</p>
            </div>
        </section>
    @else
        @php
            $routeFor = fn (string $c, ?int $y = null, ?int $k = null) => route('competency.public', array_filter([
                'code' => $c,
                'tahun' => $y,
                'k' => $k,
            ], fn ($v) => $v !== null));
            $levelHeaderTone = [
                1 => 'bg-teal-50 text-teal-800 dark:bg-teal-900/30 dark:text-teal-200',
                2 => 'bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-100',
                3 => 'bg-teal-200 text-teal-900 dark:bg-teal-800/70 dark:text-white',
                4 => 'bg-teal-primary text-white',
            ];
            $itemTone = [
                'knowledge' => ['ring' => 'border-sky-200 dark:border-sky-900', 'badge' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300', 'dot' => 'bg-sky-500'],
                'skill' => ['ring' => 'border-emerald-200 dark:border-emerald-900', 'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300', 'dot' => 'bg-emerald-500'],
                'development' => ['ring' => 'border-amber-200 dark:border-amber-900', 'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300', 'dot' => 'bg-amber-500'],
            ];
        @endphp

        <!-- Toolbar: tab jenis + tahun + cetak -->
        <div class="no-print sticky top-20 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center justify-between gap-3">
                <nav class="flex gap-2 overflow-x-auto" aria-label="Jenis kamus kompetensi" id="competency-type-tabs">
                    @foreach($yearsByCode as $tabCode => $tabYears)
                        @php $isActive = $tabCode === $code; @endphp
                        <a href="{{ $routeFor($tabCode) }}"
                           id="competency-tab-{{ $tabCode }}"
                           @if($isActive) aria-current="page" @endif
                           class="px-5 py-2.5 rounded-full font-bold text-sm whitespace-nowrap transition-all duration-300 {{ $isActive ? 'bg-teal-primary text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-teal-primary dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-gray-800' }}">
                            {{ $codeLabels[$tabCode] }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-3">
                    @if(count($years) > 1)
                        <label for="competency-year" class="text-sm font-medium text-gray-600 dark:text-gray-300">Tahun</label>
                        <select id="competency-year"
                                onchange="window.location.href = this.value"
                                class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 py-2 pl-3 pr-8 focus:ring-2 focus:ring-teal-primary focus:border-teal-primary">
                            @foreach($years as $optionYear)
                                <option value="{{ $routeFor($code, $optionYear) }}" @selected($optionYear === $year)>{{ $optionYear }}</option>
                            @endforeach
                        </select>
                    @else
                        <span class="inline-flex items-center rounded-full bg-teal-50 dark:bg-gray-800 text-teal-primary dark:text-teal-300 text-sm font-semibold px-4 py-2">Edisi {{ $year }}</span>
                    @endif

                    <!-- Dropdown Cetak (Cetak Kompetensi Ini / Cetak Seluruh Kamus) -->
                    <div x-data="{ printOpen: false }" class="relative inline-block text-left">
                        <button type="button" @click="printOpen = !printOpen" class="hidden sm:inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-200 px-3.5 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4 text-teal-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <span>Cetak</span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="printOpen" @click.away="printOpen = false" x-cloak class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1.5 z-50">
                            @if(!$isAll)
                                <button type="button" @click="printOpen = false; window.print();" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-teal-50 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-teal-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Cetak Kompetensi Ini (#{{ $selectedIndex }})</span>
                                </button>
                                <a href="{{ $routeFor($code, $year) }}&all=1&print=1" target="_blank" @click="printOpen = false" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-teal-primary dark:text-teal-400 hover:bg-teal-50 dark:hover:bg-gray-700 flex items-center gap-2 border-t border-gray-100 dark:border-gray-700">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <span>Cetak Seluruh Kamus ({{ $competencies->count() }} Kompetensi)</span>
                                </a>
                            @else
                                <button type="button" @click="printOpen = false; window.print();" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-teal-primary dark:text-teal-400 hover:bg-teal-50 dark:hover:bg-gray-700 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-teal-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    <span>Cetak Seluruh Kamus Sekarang</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300 py-10 md:py-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-teal-primary dark:text-white">{{ $dictionary->name }} <span class="text-green-accent">{{ $dictionary->year }}</span></h2>
                    @if($dictionary->description)
                        <p class="mt-2 text-gray-600 dark:text-gray-400 max-w-3xl whitespace-pre-line">{{ $dictionary->description }}</p>
                    @endif
                </div>

                @if($isAll)
                    <!-- Notice Banner Mode Cetak Semua -->
                    <div class="no-print mb-8 bg-teal-50 dark:bg-gray-800 border-l-4 border-teal-primary p-4 rounded-r-xl shadow-sm flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-teal-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Mode Cetak Seluruh Kamus ({{ $allCompetencies->count() }} Kompetensi)</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Seluruh dokumen kamus disajikan berurutan dengan jeda halaman untuk cetak.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ $routeFor($code, $year) }}" class="px-3.5 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg text-xs hover:bg-gray-50 transition">
                                Kembali ke Mode Per-Kompetensi
                            </a>
                            <button type="button" onclick="window.print()" class="px-4 py-2 bg-teal-primary text-white font-bold rounded-lg text-xs hover:bg-teal-700 shadow-sm transition inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Cetak Sekarang
                            </button>
                        </div>
                    </div>

                    <!-- Tampilan Seluruh Kompetensi untuk Cetak -->
                    <div class="space-y-10">
                        @foreach($allCompetencies as $idx => $comp)
                            @php $levelMap = $comp->levelMap(); @endphp
                            <article class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 md:p-8 print-page-break">
                                <p class="text-xs font-bold uppercase tracking-widest text-green-accent mb-2">Kompetensi {{ $idx + 1 }} dari {{ $allCompetencies->count() }}</p>
                                <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $idx + 1 }}. {{ $comp->name }}</h3>
                                @if($comp->definition)
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line mb-6">{{ $comp->definition }}</p>
                                @endif

                                <!-- Matriks jabatan x level -->
                                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                    <table class="min-w-[880px] w-full text-sm border-collapse competency-matrix">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sticky left-0 z-10 w-44 bg-gray-100 dark:bg-gray-900 text-left text-gray-700 dark:text-gray-200 font-bold px-4 py-3 border-b border-gray-200 dark:border-gray-700">Jabatan</th>
                                                @foreach($levelLabels as $level => $label)
                                                    <th scope="col" class="{{ $levelHeaderTone[$level] }} text-left font-bold px-4 py-3 border-b border-gray-200 dark:border-gray-700">{{ $label }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dictionary->positions as $position)
                                                <tr class="align-top odd:bg-white even:bg-gray-50/70 dark:odd:bg-gray-800 dark:even:bg-gray-800/60">
                                                    <th scope="row" class="sticky left-0 z-10 bg-inherit text-left font-semibold text-teal-primary dark:text-teal-300 px-4 py-4 border-b border-gray-100 dark:border-gray-700">{{ $position->name }}</th>
                                                    @foreach($levelLabels as $level => $label)
                                                        @php $text = trim((string) $levelMap->get("{$position->id}-{$level}", '')); @endphp
                                                        <td class="px-4 py-4 border-b border-gray-100 dark:border-gray-700 text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $text !== '' ? $text : '–' }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Butir: pengetahuan, keterampilan, development -->
                                <div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-5">
                                    @foreach($itemTypeLabels as $type => $typeLabel)
                                        @php $typeItems = $comp->itemsOfType($type); @endphp
                                        @continue($typeItems->isEmpty())
                                        <section class="rounded-xl border {{ $itemTone[$type]['ring'] }} p-5 bg-white dark:bg-gray-800">
                                            <h4 class="inline-block rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $itemTone[$type]['badge'] }} mb-4">{{ $typeLabel }}</h4>
                                            <ul class="space-y-3">
                                                @foreach($typeItems as $item)
                                                    <li class="flex gap-3 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                                        <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full {{ $itemTone[$type]['dot'] }}"></span>
                                                        <span class="whitespace-pre-line">{{ $item->body }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </section>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                @elseif($selected === null)
                    <p class="text-gray-500 dark:text-gray-400">Kamus ini belum memiliki kompetensi.</p>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <!-- Kiri: daftar kompetensi -->
                        <aside class="print-hide-left lg:col-span-4 xl:col-span-3 no-print">
                            <!-- Mobile: dropdown -->
                            <div class="lg:hidden">
                                <label for="competency-select" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih kompetensi</label>
                                <select id="competency-select"
                                        onchange="window.location.href = this.value"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 py-3 pl-3 pr-8 focus:ring-2 focus:ring-teal-primary focus:border-teal-primary">
                                    @foreach($competencies as $i => $competency)
                                        <option value="{{ $routeFor($code, $year, $i + 1) }}#detail" @selected(($i + 1) === $selectedIndex)>{{ $i + 1 }}. {{ $competency->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Desktop: daftar bernomor -->
                            <nav class="hidden lg:block sticky top-44 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-3 max-h-[calc(100vh-12rem)] overflow-y-auto" aria-label="Daftar kompetensi">
                                <p class="px-3 pt-2 pb-3 text-xs font-bold uppercase tracking-widest text-gray-400">Kompetensi</p>
                                <ol class="space-y-1">
                                    @foreach($competencies as $i => $competency)
                                        @php $active = ($i + 1) === $selectedIndex; @endphp
                                        <li>
                                            <a href="{{ $routeFor($code, $year, $i + 1) }}#detail"
                                               id="competency-link-{{ $i + 1 }}"
                                               @if($active) aria-current="true" @endif
                                               class="flex items-start gap-3 rounded-xl px-3 py-2.5 text-sm transition {{ $active ? 'bg-teal-primary text-white shadow' : 'text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-gray-700' }}">
                                                <span class="flex-shrink-0 w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center {{ $active ? 'bg-white/20 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300' }}">{{ $i + 1 }}</span>
                                                <span class="leading-snug {{ $active ? 'font-semibold' : '' }}">{{ $competency->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        </aside>

                        <!-- Kanan: detail -->
                        <div id="detail" class="print-full lg:col-span-8 xl:col-span-9 scroll-mt-44 min-w-0"
                             x-data="{ position: 'all' }">
                            <article class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 md:p-8">
                                <p class="text-xs font-bold uppercase tracking-widest text-green-accent mb-2">Kompetensi {{ $selectedIndex }} dari {{ $competencies->count() }}</p>
                                <h3 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white mb-4">{{ $selectedIndex }}. {{ $selected->name }}</h3>
                                @if($selected->definition)
                                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $selected->definition }}</p>
                                @endif

                                <!-- Filter jabatan -->
                                <div class="no-print mt-8 flex flex-wrap items-center gap-3">
                                    <label for="competency-position-filter" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tampilkan jabatan</label>
                                    <select id="competency-position-filter" x-model="position"
                                            class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 py-2 pl-3 pr-8 focus:ring-2 focus:ring-teal-primary focus:border-teal-primary">
                                        <option value="all">Semua</option>
                                        @foreach($dictionary->positions as $position)
                                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Matriks jabatan x level -->
                                @php $levelMap = $selected->levelMap(); @endphp
                                <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                    <table class="min-w-[880px] w-full text-sm border-collapse competency-matrix" id="competency-matrix">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sticky left-0 z-10 w-44 bg-gray-100 dark:bg-gray-900 text-left text-gray-700 dark:text-gray-200 font-bold px-4 py-3 border-b border-gray-200 dark:border-gray-700">Jabatan</th>
                                                @foreach($levelLabels as $level => $label)
                                                    <th scope="col" class="{{ $levelHeaderTone[$level] }} text-left font-bold px-4 py-3 border-b border-gray-200 dark:border-gray-700">{{ $label }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dictionary->positions as $position)
                                                <tr x-show="position === 'all' || position === '{{ $position->id }}'"
                                                    class="align-top odd:bg-white even:bg-gray-50/70 dark:odd:bg-gray-800 dark:even:bg-gray-800/60">
                                                    <th scope="row" class="sticky left-0 z-10 bg-inherit text-left font-semibold text-teal-primary dark:text-teal-300 px-4 py-4 border-b border-gray-100 dark:border-gray-700">{{ $position->name }}</th>
                                                    @foreach($levelLabels as $level => $label)
                                                        @php $text = trim((string) $levelMap->get("{$position->id}-{$level}", '')); @endphp
                                                        <td class="px-4 py-4 border-b border-gray-100 dark:border-gray-700 text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $text !== '' ? $text : '–' }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="no-print mt-2 text-xs text-gray-400 md:hidden">Geser tabel ke samping untuk melihat semua level.</p>

                                <!-- Butir: pengetahuan, keterampilan, development -->
                                <div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-5">
                                    @foreach($itemTypeLabels as $type => $typeLabel)
                                        @php $typeItems = $selected->itemsOfType($type); @endphp
                                        @continue($typeItems->isEmpty())
                                        <section class="rounded-xl border {{ $itemTone[$type]['ring'] }} p-5 bg-white dark:bg-gray-800" id="competency-items-{{ $type }}">
                                            <h4 class="inline-block rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $itemTone[$type]['badge'] }} mb-4">{{ $typeLabel }}</h4>
                                            <ul class="space-y-3">
                                                @foreach($typeItems as $item)
                                                    <li class="flex gap-3 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                                        <span class="mt-2 flex-shrink-0 w-1.5 h-1.5 rounded-full {{ $itemTone[$type]['dot'] }}"></span>
                                                        <span class="whitespace-pre-line">{{ $item->body }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </section>
                                    @endforeach
                                </div>
                            </article>

                            <!-- Sebelumnya / berikutnya -->
                            <div class="no-print mt-6 flex items-center justify-between gap-4">
                                @if($selectedIndex > 1)
                                    <a href="{{ $routeFor($code, $year, $selectedIndex - 1) }}#detail" class="inline-flex items-center gap-2 rounded-full border border-gray-300 dark:border-gray-600 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-teal-50 dark:hover:bg-gray-800 transition">
                                        &larr; <span class="hidden sm:inline">{{ $competencies[$selectedIndex - 2]->name }}</span><span class="sm:hidden">Sebelumnya</span>
                                    </a>
                                @else
                                    <span></span>
                                @endif
                                @if($selectedIndex < $competencies->count())
                                    <a href="{{ $routeFor($code, $year, $selectedIndex + 1) }}#detail" class="inline-flex items-center gap-2 rounded-full bg-teal-primary text-white px-5 py-2.5 text-sm font-semibold hover:bg-teal-700 shadow transition">
                                        <span class="hidden sm:inline">{{ $competencies[$selectedIndex]->name }}</span><span class="sm:hidden">Berikutnya</span> &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
</x-layout>
