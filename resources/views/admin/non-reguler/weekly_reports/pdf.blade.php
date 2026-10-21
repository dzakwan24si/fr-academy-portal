<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Weekly Report - {{ $weeklyReport->trainingProgram->name ?? 'Program' }}</title>
  <style>
@page { margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

        .page { width: 210mm; min-height: 297mm; page-break-after: always; padding: 0; position: relative; overflow: hidden; background: #fff; }
        .page:last-child { page-break-after: auto; }

        /* ======================== COVER PAGE ======================== */
        .cover-page { padding: 0; background-color: #1e3a8a; }
        .cover-header { background-color: #1e3a8a; padding: 40px 30px 20px; text-align: right; }
        .cover-year { font-size: 60px; font-weight: 900; color: #fff; line-height: 1; letter-spacing: -2px; }
        .cover-title { font-size: 70px; font-weight: 900; color: #fff; line-height: 0.9; margin-top: 8px; }
        .cover-sub { font-size: 18px; font-weight: 700; color: #fcd34d; letter-spacing: 3px; margin-top: 12px; }
        .cover-photos { display: table; width: 100%; height: 120mm; background: #2563eb; }
        .cover-photo-cell { display: table-cell; width: 33.33%; vertical-align: middle; text-align: center; padding: 5px; }
        .cover-photo-img { width: 100%; height: 100px; object-fit: cover; }
        .cover-footer { background-color: #d97706; color: #fff; font-size: 11px; font-weight: 700; padding: 8px 20px; letter-spacing: 1px; }

        /* ======================== SECTION HEADER ======================== */
        /* FIX: display:flex removed. Vertical centering approximated with padding since
           DomPDF cannot reliably center content vertically without table layout across
           a min-height div. Adjust top/bottom padding if title wraps to 2 lines. */
        .section-header { background-color: #f3f4f6; min-height: 297mm; text-align: center; padding: 120px 40px; }
        .section-header-label { font-size: 11px; font-weight: 700; color: #9ca3af; letter-spacing: 3px; margin-bottom: 12px; }
        .section-header-title { font-size: 36px; font-weight: 900; color: #1f2937; letter-spacing: 3px; border-top: 4px solid #1f2937; border-bottom: 4px solid #1f2937; padding: 16px 40px; }

        /* ======================== SESSION PAGE ======================== */
        .session-page { padding: 0; position: relative; }
        .session-top { display: table; width: 100%; height: 160px; background-color: #f3f4f6; margin-bottom: 24px; position: relative; }
        
        .session-top-left { display: table-cell; width: 40%; height: 160px; vertical-align: top; padding: 0; }
        .session-top-right { display: table-cell; width: 60%; height: 160px; vertical-align: top; padding: 20px; text-align: right; background-color: #f3f4f6; }

        .session-top-photo { width: 100%; height: 160px; object-fit: cover; }
        .session-top-photo-empty { width: 100%; height: 160px; background: #e5e7eb; }

        .session-top-date { font-size: 16px; font-weight: 900; color: #1f2937; margin-bottom: 5px; }
        .session-top-date-sub { font-size: 12px; color: #6b7280; font-weight: 700; }

        .content-wrapper { padding: 0 16mm 10mm 16mm; }
        .content-grid { display: table; width: 100%; }
        .content-left { display: table-cell; width: 55%; vertical-align: top; padding-right: 32px; }
        .content-right { display: table-cell; width: 45%; vertical-align: top; border-left: 2px solid #f3f4f6; padding-left: 14px; }

        .section-badge { display: inline-block; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 12px; margin-bottom: 8px; }
        .info-table { width: 100%; font-size: 10px; font-weight: 600; color: #374151; margin-top: 4px; border-collapse: collapse; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table td:first-child { width: 70px; color: #6b7280; }
        .purpose-text { font-size: 10px; color: #374151; line-height: 1.5; }
        .result-bullet { font-size: 10px; color: #374151; line-height: 1.5; padding-left: 14px; text-indent: -14px; margin-bottom: 2px; }

        /* Trainer Card */
        .trainer-photo { width: 100%; height: 120px; object-fit: cover; object-position: top; border-radius: 4px; background: #e5e7eb; }

        /* FIX: flex -> table/table-cell; gradient replaced with solid color since
           DomPDF renders CSS gradients inconsistently */
        .trainer-photo-empty { width: 100%; height: 120px; background: #374151; border-radius: 4px; display: table; }
        .trainer-initials { font-size: 28px; font-weight: 900; color: #fff; letter-spacing: 2px; text-align: center; display: table-cell; vertical-align: middle; }
        .trainer-name { color: #fff; font-weight: 700; font-size: 11px; padding: 6px 8px; text-align: center; border-radius: 0 0 4px 4px; }

        /* Participants */
        .participants-list { margin-top: 8px; }
        .participant-row { display: table; width: 100%; border-bottom: 1px solid #f3f4f6; padding: 3px 0; }
        .participant-num { display: table-cell; width: 18px; height: 18px; border-radius: 50%; color: #fff; font-size: 8px; font-weight: 900; text-align: center; vertical-align: middle; }
        .participant-name { display: table-cell; font-size: 10px; font-weight: 600; color: #374151; padding-left: 6px; vertical-align: middle; }
        .participant-type { display: table-cell; font-size: 9px; color: #9ca3af; text-align: right; vertical-align: middle; }

        /* Doc Photo */
        .doc-photo { width: 100%; height: 100px; object-fit: cover; border: 3px solid #e5e7eb; margin-top: 8px; }

        /* FOOTER */
        .session-footer { position: absolute; bottom: 10mm; left: 16mm; right: 16mm; border-top: 1px solid #d1d5db; padding-top: 8px; font-size: 10px; font-weight: 700; color: #374151; }
        .session-footer-company { font-size: 12px; font-weight: 900; margin-bottom: 2px; }
        .session-footer-addr { font-size: 9px; color: #6b7280; }

        /* QUIZ PAGE */
        .quiz-page { padding: 16mm 14mm; }
        .quiz-title { font-size: 20px; font-weight: 900; color: #1e3a8a; letter-spacing: 2px; border-bottom: 4px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 20px; }
        .data-table thead tr { color: #fff; }
        .data-table thead td { padding: 6px 10px; font-weight: 700; }
        .data-table tbody tr:nth-child(even) { background: #f9fafb; }
        .data-table tbody td { padding: 5px 10px; border-bottom: 1px solid #e5e7eb; }
        .badge-top { background: #d1fae5; color: #065f46; font-size: 9px; font-weight: 700; padding: 1px 6px; border-radius: 4px; }
        .badge-bottom { background: #fee2e2; color: #991b1b; font-size: 9px; font-weight: 700; padding: 1px 6px; border-radius: 4px; }
        .rank-num { font-size: 16px; font-weight: 900; color: #d97706; text-align: center; }

        /* INTRO PAGE (Page 2) */
        .intro-page { padding: 12mm 14mm; }
        .photo-grid { display: table; width: 100%; margin-top: 6px; table-layout: fixed; }
        .photo-row { display: table-row; }
        .photo-cell { display: table-cell; padding: 2px; width: 50%; vertical-align: top; }
        .photo-box { width: 100%; height: 130px; object-fit: cover; border-radius: 4px; border: 1px solid #d1d5db; }
        .intro-reguler { font-size: 90px; font-weight: 900; color: #1e3a5f; line-height: 0.85; text-align: center; font-style: normal; margin-bottom: 12px; }
        .intro-main-photo { width: 100%; height: 200px; object-fit: cover; background: #e5e7eb; border-radius: 4px; margin-bottom: 14px; }

        /* FIX: flex -> table/table-cell */
        .intro-main-photo-empty { width: 100%; height: 200px; background: #e5e7eb; border-radius: 4px; display: table; margin-bottom: 14px; }
        .intro-program-name { font-size: 20px; font-weight: 900; color: #1e3a8a; margin-bottom: 10px; }
        .intro-desc { font-size: 13px; color: #374151; line-height: 1.6; text-align: justify; }
        .intro-quote-title { font-size: 18px; font-weight: 900; color: #1e3a8a; font-style: italic; margin-bottom: 8px; }
        .intro-quote-text { font-size: 11px; color: #6b7280; line-height: 1.6; font-style: italic; }
        .intro-grid { display: table; width: 100%; }
        .intro-grid-left { display: table-cell; width: 65%; vertical-align: top; padding-right: 24px; }
        .intro-grid-right { display: table-cell; width: 35%; vertical-align: top; border-left: 2px solid #e5e7eb; padding-left: 20px; }
    
        </style>
</head>
<body>

@foreach($selectedReports as $weeklyReport)
    <!-- START: CONTAINER SATU LAPORAN PENUH -->
    <div class="laporan-wrapper theme-{{ $weeklyReport->theme ?? 'reguler' }}">

@php
    $allPhotos = $weeklyReport->trainingSessions->flatMap(fn($s) => $s->photos);
    
    $parseListLines = function ($text) {
        if (!$text) return collect();
        return collect(preg_split('/\r\n|\r|\n/', trim($text)))
            ->map(fn($line) => trim(preg_replace('/^(\d+\.\s*|[-•*]\s*)/', '', $line)))
            ->filter(fn($line) => $line !== '');
    };
    
    $sessionsGrouped = $weeklyReport->trainingSessions->groupBy('program_source');

    $theme = $weeklyReport->theme ?? 'reguler';
    $isKemerdekaan = $theme === 'kemerdekaan';
    
    $colorPrimary = $isKemerdekaan ? '#dc2626' : '#1e3a8a'; 
    $colorPrimaryLight = $isKemerdekaan ? '#ef4444' : '#2563eb';
    $colorFooter = $isKemerdekaan ? '#b91c1c' : '#d97706';
    
    $coverBg = $isKemerdekaan ? 'url("' . str_replace('\\', '/', public_path('cover-kemerdekaan.png')) . '")' : 'none';
    $footerBg = $isKemerdekaan ? 'url("' . str_replace('\\', '/', public_path('footer-kemerdekaan.png')) . '")' : 'none';

@endphp

@forelse($sessionsGrouped as $programName => $sessions)
    @php
        $currentProgram = \App\Models\TrainingProgram::where('name', $programName)->first() ?? $weeklyReport->trainingProgram;
        $programPhotos = $sessions->flatMap(fn($s) => $s->photos);
        
        $coverSrcs = [];
        if (!$weeklyReport->is_merged) {
            foreach([1,2,3] as $n){
                $cp = $weeklyReport->{'cover_photo_'.$n};
                if($cp) $coverSrcs[] = public_path('storage/'.ltrim($cp, '/'));
            }
        }
        if (empty($coverSrcs)) {
            if($programPhotos->count() > 0){
                $coverSrcs = $programPhotos->take(3)->map(fn($p) => public_path('storage/'.ltrim($p->photo_path,'/')))->values()->toArray();
            }
        }

        $page2Photos = [];
        if (!$weeklyReport->is_merged && is_array($weeklyReport->strip_photos) && count($weeklyReport->strip_photos) > 0) {
            foreach($weeklyReport->strip_photos as $sp){
                $page2Photos[] = public_path('storage/'.ltrim($sp,'/'));
            }
        } else {
            $page2Photos = $programPhotos->take(4)->map(fn($p) => public_path('storage/'.ltrim($p->photo_path,'/')))->values()->toArray();
        }
        
        if (!$weeklyReport->is_merged && $weeklyReport->cover_photo_main) {
            $bigPhotoSrc = public_path('storage/'.ltrim($weeklyReport->cover_photo_main,'/'));
        } else {
            $mainP = $programPhotos->count() > 4 ? $programPhotos->get(4) : $programPhotos->first();
            $bigPhotoSrc = $mainP ? public_path('storage/'.ltrim($mainP->photo_path,'/')) : null;
        }
    @endphp

    {{-- ============================================================ --}}
    {{-- PAGE 1: COVER                                                --}}
    {{-- ============================================================ --}}
    <div class="page cover-page" style="background-color: {{ $colorPrimary }}; background-image: {{ $coverBg }}; background-size: cover; background-position: center;">
        <div class="cover-header">
            <div class="cover-year">{{ \Carbon\Carbon::parse($weeklyReport->week_start_date)->format('Y') }}</div>
            <div class="cover-title">WEEKLY<br>REPORT</div>
            <div class="cover-sub">REGULER {{ strtoupper($currentProgram->name ?? 'PROGRAM') }}</div>
        </div>

        @if(count($coverSrcs) > 0)
        <div style="display:table;width:100%;height:110mm;" style="background: {{ $colorPrimary }};">
            @foreach($coverSrcs as $csrc)
            <div style="display:table-cell;vertical-align:middle;text-align:center;padding:6px;">
                <div style="width:130px;height:130px;margin:auto;transform:rotate(45deg);overflow:hidden;border:6px solid #fff;box-shadow:0 8px 20px rgba(0,0,0,.3);">
                    <img src="{{ $csrc }}" style="width:185%;height:185%;object-fit:cover;transform:translate(-22%,-22%) rotate(-45deg);display:block;" alt="cover">
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="height:110mm;" style="background: {{ $colorPrimaryLight }};"></div>
        @endif

        <div class="cover-footer" style="background-color: {{ $colorFooter }}; background-image: {{ $footerBg }}; background-size: cover; background-position: center;">http://e-learning.first-resources.com/</div>
    </div>

    {{-- ============================================================ --}}
    {{-- PAGE 2: INTRO PROGRAM                                        --}}
    {{-- ============================================================ --}}
    @if(!$weeklyReport->is_merged && is_array($weeklyReport->strip_photos) && count($weeklyReport->strip_photos) > 0)
    <div class="page intro-page" style="padding: 20mm 16mm;">
        <div style="display:table;width:100%;border-bottom:2px solid #1f2937;padding-bottom:6px;margin-bottom:14px;">
            <div style="display:table-cell;font-size:10px;font-weight:700;color:#6b7280;">{{ \Carbon\Carbon::parse($weeklyReport->week_start_date)->translatedFormat('d') }} – {{ \Carbon\Carbon::parse($weeklyReport->week_end_date)->translatedFormat('d F Y') }}</div>
            <div style="display:table-cell;font-size:10px;font-weight:700;color:#6b7280;text-align:right;">REGULER ENGINEERING</div>
        </div>

        {{-- Strip foto atas --}}
        @if(count($page2Photos) > 0)
        <div style="display:table;width:100%;height:70px;margin-bottom:14px;border:1px solid #e5e7eb;">
            @foreach($page2Photos as $pSrc)
            <div style="display:table-cell;vertical-align:top;">
                <img src="{{ $pSrc }}" style="width:100%;height:70px;object-fit:cover;display:block;" alt="foto">
            </div>
            @endforeach
        </div>
        @else
        <div style="width:100%;height:70px;background:#e5e7eb;margin-bottom:14px;"></div>
        @endif

        <div class="intro-reguler" style="color: {{ $colorPrimary }};">REGULER</div>

        {{-- Foto besar tengah --}}
        @if($bigPhotoSrc)
        <img src="{{ $bigPhotoSrc }}" class="intro-main-photo" alt="foto utama">
        @else
        <div class="intro-main-photo-empty"><span style="display:table-cell;vertical-align:middle;text-align:center;font-size:13px;font-weight:700;color:#9ca3af;">Foto Dokumentasi</span></div>
        @endif

        <div class="intro-program-name" style="color: {{ $colorPrimary }};">{{ $currentProgram->name ?? '-' }} - {{ \Carbon\Carbon::parse($weeklyReport->week_start_date)->format('Y') }}</div>

        <div class="intro-grid">
            <div class="intro-grid-left">
                <p class="intro-desc">{{ $currentProgram->description ?? 'Deskripsi program belum diisi.' }}</p>
            </div>
            <div class="intro-grid-right">
                <div class="intro-quote-title" style="color: {{ $colorPrimary }};">"Quote<br>Pelatihan"</div>
                <p class="intro-quote-text">{{ $currentProgram->quote ?? 'Belum ada quote untuk laporan ini.' }}</p>
                <div style="width:50px;height:2px;margin-top:10px;" style="background: {{ $colorPrimary }};"></div>
            </div>
        </div>
    </div>
    @endif

    @foreach($sessions as $session)
    @php
        $sessionTypeLabel = match($session->session_type) {
            'orientasi' => 'ORIENTASI',
            'inclass'   => 'INCLASS',
            'ojt'       => 'ON THE JOB TRAINING',
            'supervisi_ojt' => 'SUPERVISI OJT',
            'moa'       => 'MOA',
            default     => strtoupper($session->session_type)
        };
        $sessionColor = $colorPrimary;
        $sessionParticipants = $session->sessionParticipants;
        $sessionEval = $session->evaluation;
        $sessionPhotos = $session->photos;
        $resultLines = $parseListLines($session->training_result ?? $session->results_or_impressions ?? null);
        $ojtPlacements = $session->ojtPlacements ?? collect();
        $ojtByRegion = $ojtPlacements->groupBy('region');
        $purposeLines = $parseListLines($session->training_purpose);
        $trainerPhotoPath = $session->trainer_photo_path ? public_path('storage/'.ltrim($session->trainer_photo_path,'/')) : null;
        $firstSessionPhoto = $sessionPhotos->count() > 0 ? public_path('storage/'.ltrim($sessionPhotos->first()->photo_path,'/')) : null;
        $secondSessionPhoto = $sessionPhotos->count() > 1 ? public_path('storage/'.ltrim($sessionPhotos[1]->photo_path,'/')) : null;
        $tName = $session->trainer_name;
        $initials = $tName ? collect(explode(' ', $tName))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('') : '';
    @endphp

    <div class="page session-page">
        {{-- TOP ROW --}}
        <div class="session-top">
            <div class="session-top-left">
                @if($firstSessionPhoto)
                    <img src="{{ $firstSessionPhoto }}" class="session-top-photo" alt="foto sesi">
                @else
                    <div class="session-top-photo-empty"></div>
                @endif
            </div>
            <div class="session-top-right" @if(count($sessionPhotos) <= 1) style="background-color: {{ $sessionColor }}; text-align: center; display: table-cell; vertical-align: middle; padding-left: 100px; padding-right: 20px;" @endif>
                <div class="session-top-date" @if(count($sessionPhotos) <= 1) style="position: absolute; top: 12px; right: 16px; text-align: right;" @endif>
                    {{ \Carbon\Carbon::parse($session->date_start)->locale('id')->translatedFormat('d') }} – 
                    {{ \Carbon\Carbon::parse($session->date_end)->locale('id')->translatedFormat('d F Y') }}
                </div>
                @if(count($sessionPhotos) > 1)
                    <div class="session-top-date-sub">REGULER {{ strtoupper($currentProgram->name ?? 'PROGRAM') }}</div>
                @else
                    <h2 style="font-weight: 900; font-size: 30px; color: white; text-transform: uppercase; line-height: 1.2; text-shadow: 1px 1px 3px rgba(0,0,0,0.2); margin: 0; padding-top: 40px;">
                        {{ $session->title }}
                    </h2>
                @endif
            </div>
        </div>

        <div style="position: absolute; left: 40%; top: 80px; margin-left: -90px; margin-top: -90px; width: 180px; height: 180px; z-index: 10;">
            <svg width="180" height="180" viewBox="0 0 180 180" style="position: absolute; top: 0; left: 0; z-index: 1;">
                <circle cx="90" cy="90" r="90" fill="{{ $sessionColor }}" fill-opacity="0.25" />
                <circle cx="90" cy="90" r="65" fill="#ffffff" />
            </svg>
            <div style="position: absolute; top: 0; left: 0; width: 180px; height: 180px; display: table; z-index: 2;">
                    <div style="display: table-cell; vertical-align: middle; text-align: center;">
                          <div style="font-weight: 700; font-size: 11px; color: {{ $sessionColor }}; margin-bottom: 2px; text-transform: uppercase; max-width: 120px; margin-left: auto; margin-right: auto; word-wrap: break-word; line-height: 1.2;">
                              {{ Str::limit($session->title, 40) }}
                          </div>
                    </div>
            </div>
        </div>

        <div class="content-wrapper">
            {{-- CONTENT GRID --}}
            <div class="content-grid">
            {{-- LEFT --}}
            <div class="content-left">
                {{-- Tujuan Training --}}
                <div style="margin-bottom:12px;">
                    <span class="section-badge" style="background-color:{{ $sessionColor }};">TUJUAN TRAINING</span>
                    @if($purposeLines->count() > 0)
                        <ol style="margin: 0; padding-left: 14px;">
                        @foreach($purposeLines as $line)
                            <li class="purpose-text" style="margin-bottom: 2px; padding-left: 4px;">{{ $line }}</li>
                        @endforeach
                        </ol>
                    @else
                        <p class="purpose-text">Tujuan training belum didefinisikan untuk sesi ini.</p>
                    @endif
                </div>

                {{-- Tempat & Peserta --}}
                <div style="border:1px solid #e5e7eb;padding:10px;border-radius:6px;background:#f9fafb;margin-bottom:12px;">
                    <span class="section-badge" style="background-color:{{ $sessionColor }};">TEMPAT &amp; PESERTA</span>
                    <table class="info-table">
                        <tr><td>Tanggal</td><td>: {{ \Carbon\Carbon::parse($session->date_start)->locale('id')->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($session->date_end)->locale('id')->translatedFormat('d M Y') }}</td></tr>
                        <tr><td>Lokasi</td><td>: {{ $session->location ?? '-' }}</td></tr>
                        <tr><td>Provider</td><td>: {{ $session->provider ?? 'FR Academy' }}</td></tr>
                        @if($session->trainer_name)
                        <tr><td>Trainer</td><td>: {{ $session->trainer_name }}</td></tr>
                        @endif
                        <tr><td>Peserta</td><td>: {{ in_array($session->session_type, ['ojt', 'supervisi_ojt']) ? ($session->ojtPlacements ?? collect())->count() : $sessionParticipants->count() }} Orang</td></tr>
                    </table>
                </div>

                {{-- Hasil Training --}}
                @if($resultLines->count() > 0)
                <div style="margin-bottom:12px;">
                    <span class="section-badge" style="background-color:{{ $sessionColor }};">HASIL TRAINING / PESAN &amp; KESAN</span>
                    <ol style="margin: 0; padding-left: 14px;">
                    @foreach($resultLines as $line)
                        <li class="result-bullet" style="text-indent: 0; padding-left: 4px;">{{ $line }}</li>
                    @endforeach
                    </ol>
                </div>
                @endif

                {{-- OJT Locations --}}
                @if(in_array($session->session_type, ['ojt', 'supervisi_ojt']) && $ojtPlacements->count() > 0)
                <div style="padding: 0 16mm 10mm 16mm; margin-top: -15px;">
                    <h2 class="section-badge" style="background-color: {{ $sessionColor }};">LOKASI OJT</h2>
                    <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 12px;">
                        <tr>
                            @php
                                $regionsCollection = collect($ojtByRegion);
                                $half = floor($regionsCollection->count() / 2);
                                if ($half == 0) $half = 1;
                                $leftRegions = $regionsCollection->slice(0, $half);
                                $rightRegions = $regionsCollection->slice($half);
                            @endphp

                            <!-- Kolom Kiri -->
                            <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                                @foreach($leftRegions as $region => $placements)
                                    <div style="position: relative; min-height: 250px; padding-bottom: 20px;">
                                        <!-- Map Background -->
                                        <div style="position: absolute; top: 15px; left: 0; width: 100%; text-align: center; z-index: -1;">
                                            @php 
                                                $regionSlug = Str::slug($region);
                                                $mapPath = public_path('map-' . $regionSlug . '.png');
                                            @endphp
                                            @if(file_exists($mapPath))
                                                <img src="{{ $mapPath }}" alt="{{ $region }}" style="width: 250px; max-height: 200px; object-fit: contain; opacity: 0.85;">
                                            @else
                                                <div style="font-weight: 900; color: #e5e7eb; font-size: 24px; text-transform: uppercase;">
                                                    {{ $region ?: 'REGION' }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Data Table -->
                                        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                            @foreach($placements as $index => $p)
                                                <tr>
                                                    <td style="width: 15px; padding: 6px 0; font-weight: bold; vertical-align: top; line-height: 1.3;">{{ $loop->iteration }}.</td>
                                                    <td style="width: 65%; padding: 6px 0; font-weight: bold; vertical-align: top; line-height: 1.3; text-align: left;">
                                                        {!! $p->trainee->name ? htmlspecialchars($p->trainee->name) : '&nbsp;' !!}
                                                    </td>
                                                    <td style="width: 35%; padding: 6px 0 6px 15px; font-weight: bold; vertical-align: top; line-height: 1.3; text-align: left;">
                                                        {!! $p->location_code ? htmlspecialchars($p->location_code) : '&nbsp;' !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @endforeach
                            </td>

                            <!-- Kolom Kanan -->
                            <td style="width: 50%; vertical-align: top; padding-left: 15px;">
                                @foreach($rightRegions as $region => $placements)
                                    <div style="position: relative; min-height: 250px; padding-bottom: 20px;">
                                        <!-- Map Background -->
                                        <div style="position: absolute; top: 15px; left: 0; width: 100%; text-align: center; z-index: -1;">
                                            @php 
                                                $regionSlug = Str::slug($region);
                                                $mapPath = public_path('map-' . $regionSlug . '.png');
                                            @endphp
                                            @if(file_exists($mapPath))
                                                <img src="{{ $mapPath }}" alt="{{ $region }}" style="width: 250px; max-height: 200px; object-fit: contain; opacity: 0.85;">
                                            @else
                                                <div style="font-weight: 900; color: #e5e7eb; font-size: 24px; text-transform: uppercase;">
                                                    {{ $region ?: 'REGION' }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Data Table -->
                                        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                            @foreach($placements as $index => $p)
                                                <tr>
                                                    <td style="width: 15px; padding: 6px 0; font-weight: bold; vertical-align: top; line-height: 1.3;">{{ $loop->iteration }}.</td>
                                                    <td style="width: 65%; padding: 6px 0; font-weight: bold; vertical-align: top; line-height: 1.3; text-align: left;">
                                                        {!! $p->trainee->name ? htmlspecialchars($p->trainee->name) : '&nbsp;' !!}
                                                    </td>
                                                    <td style="width: 35%; padding: 6px 0 6px 15px; font-weight: bold; vertical-align: top; line-height: 1.3; text-align: left;">
                                                        {!! $p->location_code ? htmlspecialchars($p->location_code) : '&nbsp;' !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                @endif

                {{-- Evaluation --}}
                @if($sessionEval)
                <div style="margin-bottom:12px;">
                    <span class="section-badge" style="background-color:{{ $sessionColor }};">EVALUASI SESI</span>
                    <table style="width:100%;font-size:10px;border-collapse:collapse;margin-top:6px;">
                        <tr>
                            <td style="width:33%;text-align:center;padding:4px;vertical-align:bottom;">
                                <div style="height:{{ $sessionEval->materi_percentage }}px;min-height:4px;max-height:60px;background:#22c55e;width:30px;margin:0 auto;"></div>
                                <div style="font-weight:700;color:#374151;margin-top:3px;">{{ $sessionEval->materi_percentage }}%</div>
                                <div style="color:#6b7280;font-size:9px;">Materi</div>
                            </td>
                            <td style="width:33%;text-align:center;padding:4px;vertical-align:bottom;">
                                <div style="height:{{ $sessionEval->trainer_percentage }}px;min-height:4px;max-height:60px;background:#3b82f6;width:30px;margin:0 auto;"></div>
                                <div style="font-weight:700;color:#374151;margin-top:3px;">{{ $sessionEval->trainer_percentage }}%</div>
                                <div style="color:#6b7280;font-size:9px;">Trainer</div>
                            </td>
                            <td style="width:33%;text-align:center;padding:4px;vertical-align:bottom;">
                                <div style="height:{{ $sessionEval->fasilitas_percentage }}px;min-height:4px;max-height:60px;background:#ef4444;width:30px;margin:0 auto;"></div>
                                <div style="font-weight:700;color:#374151;margin-top:3px;">{{ $sessionEval->fasilitas_percentage }}%</div>
                                <div style="color:#6b7280;font-size:9px;">Fasilitas</div>
                            </td>
                        </tr>
                    </table>
                </div>
                @endif
            </div>

            {{-- RIGHT --}}
            <div class="content-right">
                  {{-- Trainer Card --}}
                  @if($session->trainer_name)
                  <div style="text-align:right; margin-bottom:15px; position:relative; width: 100%;">
                      <div style="display:inline-block; text-align:center;">
                          @if($trainerPhotoPath && file_exists($trainerPhotoPath))
                              <img src="{{ $trainerPhotoPath }}" class="trainer-photo" alt="trainer" style="max-height: 180px; width: auto; max-width: 100%; display: block; margin: 0 auto; border-radius: 0; box-shadow: none; border: 2px solid #e5e7eb; padding: 2px; background: #fff; object-fit: contain;">
                          @else
                              <div class="trainer-photo-empty" style="width: 110px; height: 140px; display: table; margin: 0 auto; border-radius: 0; border: 2px solid #e5e7eb; padding: 2px;">
                                  <div class="trainer-initials">{{ $initials }}</div>
                              </div>
                          @endif
                          <div class="trainer-name" style="background-color:{{ $sessionColor }}; display: inline-block; padding: 6px 20px; border-radius: 0; position: relative; margin-top: 8px; z-index: 10; font-size: 11px;">
                              {{ $session->trainer_name }}
                          </div>
                      </div>
                  </div>
                  @endif

                {{-- Dokumentasi --}}
                @php
                    $docPhotos = $sessionPhotos;
                @endphp
                @if($docPhotos->count() > 0)
                <div style="margin-bottom:12px;">
                    <div style="background-color:{{ $sessionColor }}; color:#fff; font-size:10px; font-weight:700; text-align:center; padding:4px;">DOKUMENTASI</div>
                    <div style="display:table; width:100%; border-collapse: separate; border-spacing: 4px; margin-top:2px;">
                        @foreach($docPhotos->chunk(2) as $chunk)
                        <div style="display:table-row;">
                            @foreach($chunk as $p)
                            <div style="display:table-cell; width:50%; vertical-align:top;">
                                <img src="{{ public_path('storage/'.ltrim($p->photo_path,'/')) }}" style="width:100%; height:80px; object-fit:cover; border:2px solid #e5e7eb;" alt="dok">
                                <div style="text-align:center; font-size:8px; font-weight:bold; color:#555; margin-top:2px; text-transform:uppercase;">
                                    {{ $p->photo_type === 'ojt' ? 'ON THE JOB TRAINING' : ($p->photo_type === 'training' ? 'TRAINING' : $p->photo_type) }}
                                </div>
                            </div>
                            @endforeach
                            @if($chunk->count() == 1)
                            <div style="display:table-cell; width:50%;"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @elseif($secondSessionPhoto && !$firstSessionPhoto) 
                {{-- Fallback jika foto cuma 1 dan masuk ke header --}}
                @endif

                {{-- Participants --}}
                @php
                    $aktifTrainees = $sessionParticipants->filter(fn($p) => in_array(strtolower($p->participation_type), ['aktif', 'active']));
                    $pasifTrainees = $sessionParticipants->filter(fn($p) => in_array(strtolower($p->participation_type), ['pasif', 'passive']));
                    $otherTrainees = $sessionParticipants->filter(fn($p) => !in_array(strtolower($p->participation_type), ['aktif', 'active', 'pasif', 'passive']));
                @endphp

                @if($aktifTrainees->count() > 0)
                <div style="margin-bottom:12px;">
                    <div style="background-color:{{ $sessionColor }}; color:#fff; font-size:10px; font-weight:700; text-align:center; padding:4px;">TRAINEE AKTIF</div>
                    <div style="margin-top:6px; padding-left:4px;">
                        @foreach($aktifTrainees as $p)
                        <div style="font-size:10px; padding:2px 0;">• {{ $p->trainee->name ?? 'Trainee' }}</div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($pasifTrainees->count() > 0)
                <div style="margin-bottom:12px;">
                    <div style="background-color:{{ $sessionColor }}; color:#fff; font-size:10px; font-weight:700; text-align:center; padding:4px;">TRAINEE PASIF</div>
                    <div style="margin-top:6px; padding-left:4px;">
                        @foreach($pasifTrainees as $p)
                        <div style="font-size:10px; padding:2px 0;">• {{ $p->trainee->name ?? 'Trainee' }}</div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($otherTrainees->count() > 0)
                <div style="margin-bottom:12px;">
                    <div style="background-color:{{ $sessionColor }}; color:#fff; font-size:10px; font-weight:700; text-align:center; padding:4px;">LAINNYA</div>
                    <div style="margin-top:6px; padding-left:4px;">
                        @foreach($otherTrainees as $p)
                        <div style="font-size:10px; padding:2px 0;">• {{ $p->trainee->name ?? 'Trainee' }} ({{ strtoupper($p->participation_type) }})</div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="session-footer">
            <div class="session-footer-company" style="color:{{ $sessionColor }};">FR ACADEMY</div>
            <div class="session-footer-addr">Gedung Learning Center, Kompleks PT Panca Surya Garden, Jln Kubang Raya Desa Kubang Jaya Kec Siak Hulu, Kab Kampar, Riau</div>
        </div>
    </div>
    @endforeach
@empty
{{-- FIX: flex -> table/table-cell for the empty-state fallback --}}
<div class="page" style="display:table;width:100%;height:100%;">
    <p style="display:table-cell;vertical-align:middle;text-align:center;font-size:14px;font-weight:700;color:#9ca3af;">Belum ada sesi yang diinputkan untuk laporan ini.</p>
</div>
@endforelse

{{-- ============================================================ --}}
{{-- PORTAL SUMMARY PAGE (ABSENSI & TUGAS)                           --}}
{{-- ============================================================ --}}
@php
    $dailyClassesData = isset($dailyClasses) ? $dailyClasses : ($weeklyReport->dailyClasses ?? collect());
@endphp
@if($dailyClassesData->count() > 0)
<div class="page quiz-page">
    <div class="quiz-title" style="color: {{ $colorPrimary }}; border-bottom-color: {{ $colorPrimary }};">REKAP ABSENSI &amp; TUGAS HARIAN</div>
    
    <p style="font-size:11px;font-weight:700;color:#374151;margin-bottom:8px;text-transform:uppercase;">Data Diambil Dari Portal Trainee</p>
    <table class="data-table">
        <thead>
            <tr style="background-color:#1e3a8a;">
                <td style="width:25%;">Trainee</td>
                <td style="width:25%;text-align:center;">Total Hadir</td>
                <td style="width:25%;text-align:center;">Total Sakit/Izin/Alpha</td>
                <td style="width:25%;text-align:center;">Tugas Dikumpulkan</td>
            </tr>
        </thead>
        <tbody>
            @php
                $traineeStats = [];
                foreach($weeklyReport->trainingProgram->trainees as $t) {
                    $traineeStats[$t->id] = [
                        'name' => $t->name,
                        'hadir' => 0,
                        'other' => 0,
                        'tasks_submitted' => 0,
                        'tasks_total' => 0
                    ];
                }
                
                foreach($dailyClassesData as $dc) {
                    // Attendance
                    foreach($dc->attendances as $att) {
                        if (isset($traineeStats[$att->trainee_id])) {
                            if (strtolower($att->status) == 'hadir') {
                                $traineeStats[$att->trainee_id]['hadir']++;
                            } else {
                                $traineeStats[$att->trainee_id]['other']++;
                            }
                        }
                    }
                    
                    // Tasks
                    foreach($dc->tasks as $task) {
                        foreach($traineeStats as $tId => $stats) {
                            $traineeStats[$tId]['tasks_total']++;
                        }
                        foreach($task->submissions as $sub) {
                            if (isset($traineeStats[$sub->trainee_id]) && $sub->status == 'sudah') {
                                $traineeStats[$sub->trainee_id]['tasks_submitted']++;
                            }
                        }
                    }
                }
            @endphp
            
            @foreach($traineeStats as $tId => $stat)
            <tr>
                <td style="font-weight:600;color:#1f2937;">{{ $stat['name'] }}</td>
                <td style="text-align:center;font-weight:700;color:#10b981;">{{ $stat['hadir'] }} Hari</td>
                <td style="text-align:center;font-weight:700;color:#ef4444;">{{ $stat['other'] }} Hari</td>
                <td style="text-align:center;font-weight:700;color:#3b82f6;">{{ $stat['tasks_submitted'] }} / {{ $stat['tasks_total'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top:auto;border-top:2px solid #1f2937;padding-top:8px;display:table;width:100%;">
        <span style="display:table-cell;color:#1e3a8a;font-weight:700;font-size:12px;">FR ACADEMY</span>
        <span style="display:table-cell;text-align:right;font-size:10px;color:#6b7280;">Laporan Data Portal Trainee</span>
    </div>
</div>

@if(!$loop->last || $weeklyReport->quizScores->count() > 0 || $weeklyReport->rankings->count() > 0)
    <div style="page-break-after: always; clear: both;"></div>
@endif

@endif

{{-- ============================================================ --}}
{{-- QUIZ & RANKING PAGE                                          --}}
{{-- ============================================================ --}}
@if($weeklyReport->quizScores->count() > 0 || $weeklyReport->rankings->count() > 0)
<div class="page quiz-page">
    <div class="quiz-title" style="color: {{ $colorPrimary }}; border-bottom-color: {{ $colorPrimary }};">NILAI &amp; RANKING MINGGUAN</div>

    @if($weeklyReport->quizScores->count() > 0)
    <p style="font-size:11px;font-weight:700;color:#374151;margin-bottom:8px;text-transform:uppercase;">Nilai Quiz</p>
    <table class="data-table">
        <thead>
            <tr style="background-color:#1e3a8a;">
                <td style="width:40%;">Trainee</td>
                <td style="width:40%;">Materi</td>
                <td style="width:20%;text-align:center;">Nilai</td>
            </tr>
        </thead>
        <tbody>
            @foreach($weeklyReport->quizScores as $qs)
            <tr>
                <td style="font-weight:600;color:#1f2937;">{{ $qs->trainee->name ?? '-' }}</td>
                <td style="color:#6b7280;">{{ $qs->materi_name }}</td>
                <td style="text-align:center;font-weight:700;color:#1e3a8a;">{{ number_format($qs->score, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($weeklyReport->rankings->count() > 0)
    <p style="font-size:11px;font-weight:700;color:#374151;margin-bottom:8px;text-transform:uppercase;">Ranking Mingguan</p>
    <table class="data-table">
        <thead>
            <tr style="background-color:#d97706;">
                <td style="width:12%;text-align:center;">Rank</td>
                <td style="width:40%;">Trainee</td>
                <td style="width:20%;text-align:center;">Rata-Rata</td>
                <td style="width:28%;text-align:center;">Kategori</td>
            </tr>
        </thead>
        <tbody>
            @foreach($weeklyReport->rankings->sortBy('rank') as $r)
            <tr>
                <td class="rank-num">{{ $r->rank }}</td>
                <td style="font-weight:600;color:#1f2937;">{{ $r->trainee->name ?? '-' }}</td>
                <td style="text-align:center;font-weight:700;">{{ number_format($r->average_score, 2) }}</td>
                <td style="text-align:center;">
                    @if($r->rank_group === 'top')
                        <span class="badge-top">TOP</span>
                    @elseif($r->rank_group === 'bottom')
                        <span class="badge-bottom">BOTTOM</span>
                    @else
                        <span style="color:#9ca3af;font-size:10px;">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div style="margin-top:auto;border-top:2px solid #1f2937;padding-top:8px;display:table;width:100%;">
        <span style="display:table-cell;color:#1e3a8a;font-weight:700;font-size:12px;">FR ACADEMY</span>
        <span style="display:table-cell;text-align:right;font-size:10px;color:#6b7280;">Gedung Learning Center, Kompleks PT Panca Surya Garden</span>
    </div>
</div>
@endif
    </div>
    <!-- END: CONTAINER SATU LAPORAN PENUH -->

    <!-- PAGE BREAK UNTUK MEMISAHKAN DENGAN LAPORAN BERIKUTNYA -->
    @if(!$loop->last)
        <div style="page-break-after: always; clear: both;"></div>
    @endif
@endforeach
</body>
</html>
