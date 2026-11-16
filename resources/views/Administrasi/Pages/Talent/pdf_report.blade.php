<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Individual Summary Report - {{ $talent->nama }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 7mm 8mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.35;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .w-full { width: 100%; }

        /* Card container */
        .card {
            background-color: #f0f6ff;
            background-color: rgba(238, 245, 255, 0.75);
            border: 1px solid #d0e3fc;
            border-radius: 9px;
            padding: 8px 10px;
            page-break-inside: avoid;
            box-sizing: border-box;
        }
        .card-header-title {
            font-size: 8.5pt;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }

        /* Banner Card */
        .banner-card {
            border: 1px solid #d0e3fc;
            border-radius: 9px;
            overflow: hidden;
            background-color: #f0f6ff;
            background-color: rgba(238, 245, 255, 0.75);
            page-break-inside: avoid;
            box-sizing: border-box;
        }
        .banner-header {
            background-color: #0b1a30;
            color: #ffffff;
            font-size: 8.2pt;
            font-weight: 800;
            padding: 5px 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .banner-body {
            padding: 7px 10px;
        }

        /* Lists */
        .bullet-item {
            font-size: 8pt;
            color: #334155;
            line-height: 1.4;
            margin-bottom: 5px;
            text-align: justify;
        }
        .bullet-item:last-child {
            margin-bottom: 0;
        }

        /* Score Pills */
        .score-pill {
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 7px 12px;
            background-color: #ffffff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        /* Screen Preview Mode (A4 Centered Sheet) */
        @media screen {
            body {
                background-color: #0b1329;
                padding-top: 80px;
                padding-bottom: 40px;
                margin: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
            }
            .a4-page {
                width: 210mm;
                min-height: 297mm;
                max-width: 210mm;
                background-color: #ffffff;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
                border-radius: 4px;
                padding: 7mm 8mm;
                margin: 0 auto;
                box-sizing: border-box;
            }
        }

        /* Print Mode */
        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm 8mm;
            }
            .no-print {
                display: none !important;
            }
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .a4-page {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    @if(request()->query('html') == '1' || request()->query('print') == '1')
    {{-- TOP PREVIEW & PRINT BAR (Matching Exact Design) --}}
    <div class="no-print" style="position: fixed; top: 0; left: 0; right: 0; height: 64px; background-color: #0b1329; border-bottom: 1px solid #1e293b; z-index: 99999; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        {{-- Left: Icon & Title --}}
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background-color: #14532d; display: flex; align-items: center; justify-content: center; color: #4ade80; flex-shrink: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
            </div>
            <div>
                <div style="color: #ffffff; font-weight: 800; font-size: 14px; letter-spacing: 0.5px;">
                    PRATINJAU CETAK LAPORAN PDF A4
                </div>
                <div style="color: #94a3b8; font-size: 11.5px; margin-top: 2px;">
                    Seluruh card telah diperbesar secara presisi, teks mudah dibaca &amp; pas penuh 1 halaman A4.
                </div>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div style="display: flex; align-items: center; gap: 12px;">
            <button onclick="window.print()" style="background-color: #2e7d32; hover:background-color: #1b5e20; color: #ffffff; border: none; border-radius: 8px; padding: 9px 18px; font-size: 12.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 8px rgba(46, 125, 50, 0.4); font-family: inherit;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Simpan PDF / Cetak</span>
            </button>

            <button onclick="window.close()" style="background-color: #1e293b; border: 1px solid #334155; color: #ffffff; border-radius: 8px; padding: 9px 18px; font-size: 12.5px; font-weight: 700; cursor: pointer; font-family: inherit;">
                Tutup
            </button>
        </div>
    </div>
    @endif

    {{-- A4 PAGE SHEET CONTAINER --}}
    <div class="a4-page">

    {{-- 1. TOP HEADER BANNER (Navy Background) --}}
    <div style="background-color: #0b1a30; border-radius: 10px; padding: 10px 14px; margin-bottom: 6px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                {{-- Logo FR --}}
                <td style="width: 58px; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <div style="background-color: #14532d; border-radius: 6px; padding: 4px; width: 48px; text-align: center;">
                            <img src="{{ $logoBase64 }}" style="width: 40px; height: auto; max-height: 38px; display: block; margin: 0 auto;">
                        </div>
                    @else
                        <div style="background-color: #15803d; border-radius: 6px; width: 48px; height: 38px; color: #ffffff; font-weight: 900; font-size: 7pt; text-align: center; line-height: 38px;">
                            FIRST
                        </div>
                    @endif
                </td>

                {{-- Title, Name & Badges --}}
                <td style="vertical-align: middle; padding-left: 10px;">
                    <div style="color: #94a3b8; font-size: 7.5pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;">
                        INDIVIDUAL SUMMARY REPORT
                    </div>
                    <div style="color: #f59e0b; font-size: 15pt; font-weight: 900; text-transform: uppercase; margin: 2px 0 5px 0; letter-spacing: 0.5px;">
                        {{ $talent->nama }}
                    </div>
                    <div>
                        <span style="background-color: #172a46; border: 1px solid #233c63; border-radius: 10px; padding: 2px 9px; font-size: 7.2pt; display: inline-block; margin-right: 6px;">
                            <span style="color: #f59e0b; font-weight: bold;">Talent to:</span>
                            <span style="color: #ffffff; font-weight: bold;">{{ $talent->talent_to ?: ($talent->talent ?: '-') }}</span>
                        </span>
                        <span style="background-color: #172a46; border: 1px solid #233c63; border-radius: 10px; padding: 2px 9px; font-size: 7.2pt; display: inline-block;">
                            <span style="color: #94a3b8; font-weight: bold;">MENTOR:</span>
                            <span style="color: #ffffff; font-weight: bold;">{{ $talent->mentor ?: ($talent->mentor_frlc ?: '-') }}</span>
                        </span>
                    </div>
                </td>

                {{-- Circular Photo --}}
                <td style="width: 60px; text-align: right; vertical-align: middle;">
                    @if(!empty($photoBase64))
                        <img src="{{ $photoBase64 }}" style="width: 52px; height: 52px; border-radius: 26px; border: 2.5px solid #22c55e; object-fit: cover;">
                    @else
                        <div style="width: 52px; height: 52px; border-radius: 26px; border: 2.5px solid #22c55e; background-color: #172a46; color: #ffffff; font-weight: 900; font-size: 14pt; line-height: 50px; text-align: center; margin-left: auto;">
                            {{ strtoupper(substr($talent->nama, 0, 1)) }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- 2. SECTION 1: DATA DIRI & ASPEK KEPRIBADIAN --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 7px;">
        <tr>
            {{-- DATA DIRI --}}
            <td class="card" style="width: 44%; vertical-align: top;">
                <div class="card-header-title" style="color: #0f172a;">
                    <span style="color: #059669;">&#128100;</span> DATA DIRI
                </div>
                <table style="width: 100%; font-size: 7.8pt;">
                    <tr>
                        <td style="width: 32%; color: #64748b; padding: 2.2px 0;">&bull; Employee ID</td>
                        <td style="color: #0f172a; font-weight: bold; padding: 2.2px 0;">: {{ $talent->employee_id ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2.2px 0;">&bull; Jabatan</td>
                        <td style="color: #0f172a; font-weight: bold; padding: 2.2px 0;">: {{ $talent->jabatan ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2.2px 0;">&bull; Lokasi OJT</td>
                        <td style="color: #0f172a; font-weight: bold; padding: 2.2px 0;">: {{ $talent->lokasi_ojt ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2.2px 0;">&bull; Umur</td>
                        <td style="color: #0f172a; font-weight: bold; padding: 2.2px 0;">: {{ $talent->umur ? (str_contains(strtolower($talent->umur), 'tahun') ? $talent->umur : $talent->umur . ' Tahun') : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2.2px 0;">&bull; Masa Kerja</td>
                        <td style="color: #0f172a; font-weight: bold; padding: 2.2px 0;">: {{ $talent->masa_kerja ? (str_contains(strtolower($talent->masa_kerja), 'tahun') ? $talent->masa_kerja : $talent->masa_kerja . ' Tahun') : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 2.2px 0; vertical-align: top;">&bull; Alumni</td>
                        <td style="color: #0f172a; font-weight: bold; padding: 2.2px 0; vertical-align: top;">: {!! nl2br(e(wordwrap($talent->alumni ?: '-', 22, "\n", false))) !!}</td>
                    </tr>
                </table>
            </td>

            {{-- ASPEK KEPRIBADIAN & PROFILING --}}
            <td class="card" style="width: 56%; vertical-align: top;">
                <div class="card-header-title" style="color: #d97706;">
                    <span style="color: #d97706;">&#128161;</span> ASPEK KEPRIBADIAN &amp; PROFILING
                </div>
                <div style="font-size: 8pt; color: #334155; line-height: 1.45; text-align: justify;">
                    {{ $talent->profilling ?: 'Belum ada catatan aspek kepribadian & profiling untuk talent ini.' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. SECTION 2: HISTORI PEKERJAAN & KEY ACHIEVEMENT --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 7px;">
        <tr>
            {{-- HISTORI PEKERJAAN --}}
            <td class="card" style="width: 50%; vertical-align: top;">
                <div class="card-header-title" style="color: #059669;">
                    <span style="color: #059669;">&#127891;</span> HISTORI PEKERJAAN / PENGALAMAN
                </div>
                @forelse($historiList as $hItem)
                    <div class="bullet-item">
                        {{ $hItem }}
                    </div>
                @empty
                    <div class="bullet-item" style="color: #94a3b8;">
                        Belum ada riwayat histori pekerjaan.
                    </div>
                @endforelse
            </td>

            {{-- KEY ACHIEVEMENT --}}
            <td class="card" style="width: 50%; vertical-align: top;">
                <div class="card-header-title" style="color: #d97706;">
                    <span style="color: #f59e0b;">&#9733;</span> KEY ACHIEVEMENT / PRESTASI
                </div>
                @forelse($achievementList as $aItem)
                    <div class="bullet-item">
                        {{ $aItem }}
                    </div>
                @empty
                    <div class="bullet-item" style="color: #94a3b8;">
                        Belum ada data prestasi / achievement.
                    </div>
                @endforelse
            </td>
        </tr>
    </table>

    {{-- 4. SECTION 3: PROGRESS IDP --}}
    <div class="card" style="margin-top: 16px; margin-bottom: 16px; padding: 14px 18px; border-left: 4px solid #2563eb;">
        <div class="card-header-title" style="color: #0f172a; margin-bottom: 12px; font-size: 10.5pt; letter-spacing: 0.5px;">
            <span style="color: #2563eb; font-size: 13pt; margin-right: 4px;">&#128202;</span> PROGRESS IDP (INDIVIDUAL DEVELOPMENT PLAN)
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                {{-- Refreshment --}}
                @php $ref = min(100, max(0, (float)($talent->refreshment ?? 0))); @endphp
                <td style="width: 32%; vertical-align: top; padding-right: 12px;">
                    <table style="width: 100%; margin-bottom: 6px;">
                        <tr>
                            <td style="color: #475569; font-size: 9pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">REFRESHMENT</td>
                            <td style="text-align: right; color: #0b1a30; font-weight: 900; font-size: 13.5pt;">{{ $ref }}%</td>
                        </tr>
                    </table>
                    <div style="height: 16px; background: #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <div style="height: 16px; width: {{ $ref }}%; background: #0b1a30; border-radius: 8px;"></div>
                    </div>
                </td>

                {{-- Softskill --}}
                @php $soft = min(100, max(0, (float)($talent->softskill ?? 0))); @endphp
                <td style="width: 32%; vertical-align: top; padding-left: 6px; padding-right: 6px;">
                    <table style="width: 100%; margin-bottom: 6px;">
                        <tr>
                            <td style="color: #475569; font-size: 9pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">SOFTSKILL</td>
                            <td style="text-align: right; color: #10b981; font-weight: 900; font-size: 13.5pt;">{{ $soft }}%</td>
                        </tr>
                    </table>
                    <div style="height: 16px; background: #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <div style="height: 16px; width: {{ $soft }}%; background: #10b981; border-radius: 8px;"></div>
                    </div>
                </td>

                {{-- OJT --}}
                @php $ojt = min(100, max(0, (float)($talent->ojt ?? 0))); @endphp
                <td style="width: 32%; vertical-align: top; padding-left: 12px;">
                    <table style="width: 100%; margin-bottom: 6px;">
                        <tr>
                            <td style="color: #475569; font-size: 9pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">OJT</td>
                            <td style="text-align: right; color: #ea580c; font-weight: 900; font-size: 13.5pt;">{{ $ojt }}%</td>
                        </tr>
                    </table>
                    <div style="height: 16px; background: #e2e8f0; border-radius: 8px; overflow: hidden;">
                        <div style="height: 16px; width: {{ $ojt }}%; background: #ea580c; border-radius: 8px;"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 5. SECTION 4: STRENGTH & WEAKNESS --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 7px;">
        <tr>
            {{-- STRENGTH --}}
            <td class="banner-card" style="width: 50%; vertical-align: top; padding: 0;">
                <div class="banner-header">
                    <span style="color: #10b981; font-weight: bold; margin-right: 2px;">&#10004;</span>
                    STRENGTH (KELEBIHAN)
                </div>
                <div class="banner-body">
                    @forelse($strengthList as $sItem)
                        <div class="bullet-item">
                            {{ $sItem }}
                        </div>
                    @empty
                        <div class="bullet-item" style="color: #94a3b8;">
                            -
                        </div>
                    @endforelse
                </div>
            </td>

            {{-- WEAKNESS --}}
            <td class="banner-card" style="width: 50%; vertical-align: top; padding: 0;">
                <div class="banner-header">
                    <span style="color: #f59e0b; font-weight: bold; margin-right: 2px;">&#9888;</span>
                    WEAKNESS (AREA PENGEMBANGAN)
                </div>
                <div class="banner-body">
                    @forelse($weaknessList as $wItem)
                        <div class="bullet-item">
                            {{ $wItem }}
                        </div>
                    @empty
                        <div class="bullet-item" style="color: #94a3b8;">
                            -
                        </div>
                    @endforelse
                </div>
            </td>
        </tr>
    </table>
    {{-- 6. SECTION 5: CATATAN & REKOMENDASI MENTOR & NILAI MENTOR --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 7px;">
        <tr>
            {{-- CATATAN MENTOR --}}
            <td class="card" style="width: 58%; vertical-align: top;">
                <div class="card-header-title" style="color: #2563eb;">
                    <span style="color: #2563eb;">&#9998;</span> CATATAN &amp; REKOMENDASI MENTOR
                    @if(!empty($selectedMonthName))
                        <span style="font-size: 7.2pt; color: #64748b; font-weight: normal; margin-left: 4px; text-transform: uppercase;">
                            (BULAN {{ strtoupper($selectedMonthName) }})
                        </span>
                    @endif
                </div>
                @forelse($catatanMentorList as $cItem)
                    <div class="bullet-item">
                        {{ $cItem }}
                    </div>
                @empty
                    <div class="bullet-item" style="color: #94a3b8;">
                        -
                    </div>
                @endforelse
            </td>

            {{-- NILAI MENTOR --}}
            <td class="card" style="width: 42%; vertical-align: top;">
                <table style="width: 100%; margin-bottom: 6px;">
                    <tr>
                        <td class="card-header-title" style="color: #0f172a; padding: 0;">
                            <span style="color: #64748b;">&#128197;</span> NILAI MENTOR
                        </td>
                        <td style="text-align: right; color: #94a3b8; font-size: 7pt; padding: 0;">
                            (skala 10 &ndash; 100)
                        </td>
                    </tr>
                </table>

                @php
                    $allMonthsList = [
                        'januari' => 'Januari', 'februari' => 'Februari', 'maret' => 'Maret',
                        'april' => 'April', 'mei' => 'Mei', 'juni' => 'Juni',
                        'juli' => 'Juli', 'agustus' => 'Agustus', 'september' => 'September',
                        'oktober' => 'Oktober', 'november' => 'November', 'desember' => 'Desember'
                    ];

                    $activeScores = [];
                    foreach ($allMonthsList as $mKey => $mLabel) {
                        $val = $talent->{"nilai_{$mKey}"} ?? null;
                        if ($val !== null && $val !== '' && is_numeric($val) && (float)$val > 0) {
                            $activeScores[] = [
                                'label' => $mLabel,
                                'score' => (float)$val,
                            ];
                        }
                    }
                    $scoreChunks = array_chunk($activeScores, 2);
                @endphp

                @if(count($activeScores) > 0)
                    <table style="width: 100%; border-collapse: separate; border-spacing: 5px 6px;">
                        @foreach($scoreChunks as $chunk)
                            <tr>
                                @if(count($chunk) == 2)
                                    {{-- Genap: Bagi 50% kiri & 50% kanan --}}
                                    @foreach($chunk as $item)
                                        <td style="width: 50%; padding: 0; vertical-align: middle;">
                                            <div class="score-pill">
                                                <table style="width: 100%; border-collapse: collapse;">
                                                    <tr>
                                                        <td style="color: #475569; font-size: 8.2pt; font-weight: 600; vertical-align: middle;">{{ $item['label'] }}</td>
                                                        <td style="text-align: right; font-weight: 900; color: #0f172a; font-size: 11pt; vertical-align: middle;">{{ $item['score'] }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    @endforeach
                                @else
                                    {{-- Ganjil: 1 bulan memakan 100% lebar baris (colspan="2") --}}
                                    @php $item = $chunk[0]; @endphp
                                    <td colspan="2" style="width: 100%; padding: 0; vertical-align: middle;">
                                        <div class="score-pill">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="color: #475569; font-size: 8.2pt; font-weight: 600; vertical-align: middle;">{{ $item['label'] }}</td>
                                                    <td style="text-align: right; font-weight: 900; color: #0f172a; font-size: 11pt; vertical-align: middle;">{{ $item['score'] }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div style="font-size: 7.5pt; color: #94a3b8; font-style: italic; padding: 12px 0; text-align: center;">
                        Belum ada data nilai mentor.
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- 7. SECTION 6: DEVELOPMENT SUGGESTION --}}
    <div class="card" style="margin-bottom: 7px;">
        <div class="card-header-title" style="color: #059669;">
            <span style="color: #059669;">&#9889;</span> DEVELOPMENT SUGGESTION
            @if(!empty($selectedMonthName))
                <span style="font-size: 7.2pt; color: #64748b; font-weight: normal; margin-left: 4px; text-transform: uppercase;">
                    (BULAN {{ strtoupper($selectedMonthName) }})
                </span>
            @endif
        </div>
        @forelse($devSuggestionList as $dItem)
            <div class="bullet-item">
                {{ $dItem }}
            </div>
        @empty
            <div class="bullet-item" style="color: #94a3b8;">
                -
            </div>
        @endforelse
    </div>

    {{-- 8. SECTION 7: SCORE CARDS & STATUS BANNER --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 6px;">
        <tr>
            {{-- Nilai OJT --}}
            <td style="width: 33.33%; background-color: #0e2246; border-radius: 9px; padding: 7px 10px; text-align: center;">
                <div style="font-size: 7pt; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                    NILAI OJT
                </div>
                <div style="font-size: 15pt; font-weight: 900; color: #ffffff; margin-top: 2px;">
                    {{ $talent->nilai_ojt !== null && $talent->nilai_ojt !== '' ? $talent->nilai_ojt : '0' }}
                </div>
            </td>

            {{-- Evaluasi Panelis --}}
            <td style="width: 33.33%; background-color: #0e2246; border-radius: 9px; padding: 7px 10px; text-align: center;">
                <div style="font-size: 7pt; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                    EVALUASI PANELIS
                </div>
                <div style="font-size: 15pt; font-weight: 900; color: #ffffff; margin-top: 2px;">
                    {{ $talent->nilai_evaluasi_panelis !== null && $talent->nilai_evaluasi_panelis !== '' ? $talent->nilai_evaluasi_panelis : '0' }}
                </div>
            </td>

            {{-- Nilai Akhir --}}
            <td style="width: 33.33%; background-color: #064e3b; border-radius: 9px; padding: 7px 10px; text-align: center;">
                <div style="font-size: 7pt; font-weight: bold; color: #a7f3d0; text-transform: uppercase; letter-spacing: 0.5px;">
                    NILAI AKHIR
                </div>
                <div style="font-size: 15pt; font-weight: 900; color: #34d399; margin-top: 2px;">
                    {{ $talent->nilai_akhir !== null && $talent->nilai_akhir !== '' ? $talent->nilai_akhir : '0' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Status Evaluasi Banner --}}
    @php
        $hasScores = (!empty($talent->nilai_ojt) || !empty($talent->nilai_evaluasi_panelis) || !empty($talent->nilai_akhir));
        $statusText = ($hasScores && !empty($talent->keterangan_evaluasi))
            ? strtoupper($talent->keterangan_evaluasi)
            : 'BELUM DINILAI / DATA KOSONG';
    @endphp
    <div style="background-color: #0b1a30; border-radius: 9px; padding: 6.5px 12px; text-align: center; margin-bottom: 6px;">
        <div style="font-size: 8.5pt; font-weight: 900; color: #ffffff; letter-spacing: 0.8px;">
            {{ $statusText }}
        </div>
    </div>

    {{-- 9. FOOTER BANNER (Navy Background) --}}
    <div style="background-color: #0b1a30; border-radius: 9px; padding: 8px 14px;">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: middle;">
                    <div style="color: #ffffff; font-size: 7.8pt; font-weight: bold; letter-spacing: 0.5px;">
                        FIRST RESOURCES EXECUTIVE SUMMARY REPORT
                    </div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="color: #f59e0b; font-size: 7.8pt; font-weight: 900; letter-spacing: 0.5px;">
                        LEAD THE CHANGE
                    </div>
                    <div style="color: #94a3b8; font-size: 6.2pt; font-style: italic; margin-top: 1px;">
                        Berubah - Bertindak - Berdampak
                    </div>
                </td>
            </tr>
        </table>
    </div>

    </div> <!-- /.a4-page -->

</body>
</html>
