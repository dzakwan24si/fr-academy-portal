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
            font-size: 7.5pt;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.25;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .w-full { width: 100%; }
        .mb-1 { margin-bottom: 3px; }
        .mb-2 { margin-bottom: 5px; }

        /* Card container */
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 8px;
            margin-bottom: 5px;
        }
        .card-header-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        /* Banner Card */
        .banner-card {
            border: 1px solid #0b1a30;
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff;
            margin-bottom: 5px;
        }
        .banner-header {
            background-color: #0b1a30;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 3.5px 8px;
            text-transform: uppercase;
        }
        .banner-body {
            padding: 5px 8px;
        }

        /* Lists */
        .bullet-item {
            font-size: 6.8pt;
            color: #334155;
            line-height: 1.35;
            margin-bottom: 5px;
        }
        .bullet-item:last-child {
            margin-bottom: 0;
        }

        /* Score Pills */
        .score-pill {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 2px 7px;
            margin-bottom: 2.5px;
            font-size: 6.5pt;
            background-color: #ffffff;
        }
    </style>
</head>
<body>

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
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 1px;">
        <tr>
            {{-- DATA DIRI --}}
            <td style="width: 44%; vertical-align: top; padding-right: 3px;">
                <div class="card" style="min-height: 102px;">
                    <div class="card-header-title" style="color: #0f172a;">
                        <span style="color: #059669;">&#128100;</span> DATA DIRI
                    </div>
                    <table style="width: 100%; font-size: 6.8pt;">
                        <tr>
                            <td style="width: 32%; color: #64748b; padding: 1.5px 0;">&bull; Employee ID</td>
                            <td style="color: #0f172a; font-weight: bold; padding: 1.5px 0;">: {{ $talent->employee_id ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">&bull; Jabatan</td>
                            <td style="color: #0f172a; font-weight: bold; padding: 1.5px 0;">: {{ $talent->jabatan ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">&bull; Lokasi OJT</td>
                            <td style="color: #0f172a; font-weight: bold; padding: 1.5px 0;">: {{ $talent->lokasi_ojt ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">&bull; Umur</td>
                            <td style="color: #0f172a; font-weight: bold; padding: 1.5px 0;">: {{ $talent->umur ? (str_contains(strtolower($talent->umur), 'tahun') ? $talent->umur : $talent->umur . ' Tahun') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">&bull; Masa Kerja</td>
                            <td style="color: #0f172a; font-weight: bold; padding: 1.5px 0;">: {{ $talent->masa_kerja ? (str_contains(strtolower($talent->masa_kerja), 'tahun') ? $talent->masa_kerja : $talent->masa_kerja . ' Tahun') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">&bull; Alumni</td>
                            <td style="color: #0f172a; font-weight: bold; padding: 1.5px 0;">: {{ $talent->alumni ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            {{-- ASPEK KEPRIBADIAN & PROFILING --}}
            <td style="width: 56%; vertical-align: top; padding-left: 3px;">
                <div class="card" style="min-height: 102px;">
                    <div class="card-header-title" style="color: #d97706;">
                        <span style="color: #d97706;">&#128161;</span> ASPEK KEPRIBADIAN &amp; PROFILING
                    </div>
                    <div style="font-size: 6.8pt; color: #334155; line-height: 1.38; text-align: justify;">
                        {{ $talent->profilling ?: 'Belum ada catatan aspek kepribadian & profiling untuk talent ini.' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. SECTION 2: HISTORI PEKERJAAN & KEY ACHIEVEMENT --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 1px;">
        <tr>
            {{-- HISTORI PEKERJAAN --}}
            <td style="width: 50%; vertical-align: top; padding-right: 3px;">
                <div class="card" style="min-height: 85px;">
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
                </div>
            </td>

            {{-- KEY ACHIEVEMENT --}}
            <td style="width: 50%; vertical-align: top; padding-left: 3px;">
                <div class="card" style="min-height: 85px;">
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
                </div>
            </td>
        </tr>
    </table>

    {{-- 4. SECTION 3: PROGRESS IDP --}}
    <div class="card" style="margin-bottom: 5px;">
        <div class="card-header-title" style="color: #0f172a; margin-bottom: 5px;">
            <span style="color: #2563eb;">&#128202;</span> PROGRESS IDP (INDIVIDUAL DEVELOPMENT PLAN)
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                {{-- Refreshment --}}
                @php $ref = min(100, max(0, (float)($talent->refreshment ?? 0))); @endphp
                <td style="width: 32%; vertical-align: top; padding-right: 6px;">
                    <table style="width: 100%; font-size: 6.8pt; margin-bottom: 2px;">
                        <tr>
                            <td style="color: #64748b; font-weight: bold; text-transform: uppercase;">REFRESHMENT</td>
                            <td style="text-align: right; color: #0f172a; font-weight: bold;">{{ $ref }}%</td>
                        </tr>
                    </table>
                    <div style="height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                        <div style="height: 5px; width: {{ $ref }}%; background: #0b1a30; border-radius: 3px;"></div>
                    </div>
                </td>

                {{-- Softskill --}}
                @php $soft = min(100, max(0, (float)($talent->softskill ?? 0))); @endphp
                <td style="width: 32%; vertical-align: top; padding-left: 3px; padding-right: 3px;">
                    <table style="width: 100%; font-size: 6.8pt; margin-bottom: 2px;">
                        <tr>
                            <td style="color: #64748b; font-weight: bold; text-transform: uppercase;">SOFTSKILL</td>
                            <td style="text-align: right; color: #0f172a; font-weight: bold;">{{ $soft }}%</td>
                        </tr>
                    </table>
                    <div style="height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                        <div style="height: 5px; width: {{ $soft }}%; background: #10b981; border-radius: 3px;"></div>
                    </div>
                </td>

                {{-- OJT --}}
                @php $ojt = min(100, max(0, (float)($talent->ojt ?? 0))); @endphp
                <td style="width: 32%; vertical-align: top; padding-left: 6px;">
                    <table style="width: 100%; font-size: 6.8pt; margin-bottom: 2px;">
                        <tr>
                            <td style="color: #64748b; font-weight: bold; text-transform: uppercase;">OJT</td>
                            <td style="text-align: right; color: #0f172a; font-weight: bold;">{{ $ojt }}%</td>
                        </tr>
                    </table>
                    <div style="height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                        <div style="height: 5px; width: {{ $ojt }}%; background: #ea580c; border-radius: 3px;"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 5. SECTION 4: STRENGTH & WEAKNESS --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 1px;">
        <tr>
            {{-- STRENGTH --}}
            <td style="width: 50%; vertical-align: top; padding-right: 3px;">
                <div class="banner-card" style="min-height: 75px;">
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
                </div>
            </td>

            {{-- WEAKNESS --}}
            <td style="width: 50%; vertical-align: top; padding-left: 3px;">
                <div class="banner-card" style="min-height: 75px;">
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
                </div>
            </td>
        </tr>
    </table>

    {{-- 6. SECTION 5: CATATAN & REKOMENDASI MENTOR & NILAI MENTOR --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 1px;">
        <tr>
            {{-- CATATAN MENTOR --}}
            <td style="width: 58%; vertical-align: top; padding-right: 3px;">
                <div class="card" style="min-height: 98px;">
                    <div class="card-header-title" style="color: #2563eb;">
                        <span style="color: #2563eb;">&#9998;</span> CATATAN &amp; REKOMENDASI MENTOR
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
                </div>
            </td>

            {{-- NILAI MENTOR --}}
            <td style="width: 42%; vertical-align: top; padding-left: 3px;">
                <div class="card" style="min-height: 98px;">
                    <table style="width: 100%; margin-bottom: 4px;">
                        <tr>
                            <td class="card-header-title" style="color: #0f172a; padding: 0;">
                                <span style="color: #64748b;">&#128197;</span> NILAI MENTOR
                            </td>
                            <td style="text-align: right; color: #94a3b8; font-size: 6.2pt; padding: 0;">
                                (skala 10 &ndash; 100)
                            </td>
                        </tr>
                    </table>

                    @php
                        $monthPairs = array_chunk($displayedMonths, 2, true);
                    @endphp

                    <table style="width: 100%; border-collapse: separate; border-spacing: 3px 0;">
                        @foreach($monthPairs as $pair)
                            <tr>
                                @foreach($pair as $mKey => $mLabel)
                                    @php
                                        $val = $talent->{"nilai_{$mKey}"};
                                        $scoreText = ($val !== null && $val !== '') ? $val : '0';
                                    @endphp
                                    <td style="width: 50%; vertical-align: top; padding: 0;">
                                        <div class="score-pill">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td style="color: #64748b; font-size: 6.5pt;">{{ $mLabel }}</td>
                                                    <td style="text-align: right; font-weight: bold; color: #0f172a; font-size: 6.5pt;">{{ $scoreText }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                @endforeach
                                @if(count($pair) == 1)
                                    <td style="width: 50%;"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- 7. SECTION 6: DEVELOPMENT SUGGESTION --}}
    <div class="card" style="margin-bottom: 5px;">
        <div class="card-header-title" style="color: #059669;">
            <span style="color: #059669;">&#9889;</span> DEVELOPMENT SUGGESTION
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
    <table style="width: 100%; border-collapse: separate; border-spacing: 4px 0; margin-bottom: 4px;">
        <tr>
            {{-- Nilai OJT --}}
            <td style="width: 33.33%; background-color: #0e2246; border-radius: 8px; padding: 5px 8px; text-align: center;">
                <div style="font-size: 6.5pt; font-weight: bold; color: #94a3b8; text-transform: uppercase;">
                    NILAI OJT
                </div>
                <div style="font-size: 13pt; font-weight: 900; color: #ffffff; margin-top: 1px;">
                    {{ $talent->nilai_ojt !== null && $talent->nilai_ojt !== '' ? $talent->nilai_ojt : '0' }}
                </div>
            </td>

            {{-- Evaluasi Panelis --}}
            <td style="width: 33.33%; background-color: #0e2246; border-radius: 8px; padding: 5px 8px; text-align: center;">
                <div style="font-size: 6.5pt; font-weight: bold; color: #94a3b8; text-transform: uppercase;">
                    EVALUASI PANELIS
                </div>
                <div style="font-size: 13pt; font-weight: 900; color: #ffffff; margin-top: 1px;">
                    {{ $talent->nilai_evaluasi_panelis !== null && $talent->nilai_evaluasi_panelis !== '' ? $talent->nilai_evaluasi_panelis : '0' }}
                </div>
            </td>

            {{-- Nilai Akhir --}}
            <td style="width: 33.33%; background-color: #064e3b; border-radius: 8px; padding: 5px 8px; text-align: center;">
                <div style="font-size: 6.5pt; font-weight: bold; color: #a7f3d0; text-transform: uppercase;">
                    NILAI AKHIR
                </div>
                <div style="font-size: 13pt; font-weight: 900; color: #34d399; margin-top: 1px;">
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
    <div style="background-color: #0b1a30; border-radius: 8px; padding: 5px 10px; text-align: center; margin-bottom: 5px;">
        <div style="font-size: 7.8pt; font-weight: 900; color: #ffffff; letter-spacing: 0.5px;">
            {{ $statusText }}
        </div>
    </div>

    {{-- 9. FOOTER BANNER (Navy Background) --}}
    <div style="background-color: #0b1a30; border-radius: 8px; padding: 6px 12px;">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: middle;">
                    <div style="color: #ffffff; font-size: 7.2pt; font-weight: bold; letter-spacing: 0.5px;">
                        FIRST RESOURCES EXECUTIVE SUMMARY REPORT
                    </div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="color: #f59e0b; font-size: 7.2pt; font-weight: 900; letter-spacing: 0.5px;">
                        LEAD THE CHANGE
                    </div>
                    <div style="color: #94a3b8; font-size: 5.8pt; font-style: italic;">
                        Berubah - Bertindak - Berdampak
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
