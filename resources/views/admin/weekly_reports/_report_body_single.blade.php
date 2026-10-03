

@php
    // Collect all photos across all sessions for cover
    $allPhotos = $weeklyReport->trainingSessions->flatMap(fn($s) => $s->photos);
    // Collect all participants across sessions (unique by trainee_id)
    $totalParticipants = $weeklyReport->trainingSessions->flatMap(fn($s) => $s->sessionParticipants)->unique('trainee_id')->count();
    // First session for header info
    $firstSession = $weeklyReport->trainingSessions->first();
    // Global program fallback
    $program = $weeklyReport->trainingProgram;

    // Helper: split text into clean list items, removing manual bullets/numbers
    $parseListLines = function ($text) {
        if (!$text) return collect();
        return collect(preg_split('/\r\n|\r|\n/', trim($text)))
            ->map(fn($line) => trim(preg_replace('/^(\d+\.\s*|[-•*]\s*)/', '', $line)))
            ->filter(fn($line) => $line !== '');
    };

    // Grouping sessions by program
    $sessionsGrouped = $weeklyReport->trainingSessions->groupBy('program_source');
    if ($sessionsGrouped->isEmpty()) {
        $sessionsGrouped->put($program->name ?? 'PROGRAM', collect([]));
    }
@endphp

<!-- LOOPING PER PROGRAM -->
@foreach($sessionsGrouped as $programName => $sessions)
    @php
        // Ambil data program spesifik untuk iterasi ini
        $currentProgram = \App\Models\TrainingProgram::where('name', $programName)->first() ?? $program;
    @endphp

    <!-- PAGE 1: COVER -->
    <div class="page-a4 relative flex flex-col justify-between overflow-hidden" style="background-image:url('{{ asset('cover-1.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        @php
            $coverSrcs=[];
            if (!$weeklyReport->is_merged) {
                foreach([1,2,3] as $n){
                    $coverPhoto=$weeklyReport->{'cover_photo_'.$n};
                    if($coverPhoto)$coverSrcs[]=Storage::url($coverPhoto);
                }
            }
            if(empty($coverSrcs)){
                $programPhotos = $sessions->flatMap(fn($s) => $s->photos);
                if($programPhotos->count()>0){
                    $coverSrcs=$programPhotos->take(3)->map(fn($p)=>Storage::url($p->photo_path))->values()->toArray();
                }
            }
            $diamonds=[
              ['top'=>271,'left'=>-149,'size'=>608],
              ['top'=>463,'left'=>308,'size'=>594],
              ['top'=>781,'left'=>-24,'size'=>608],
          ];
        @endphp
        <!-- Elemen Latar Belakang & Judul -->
        
        <div class="p-10 pt-12 z-20 relative flex flex-col items-end">
            <div class="text-right" style="max-width:75%;margin-top:80px;margin-right:35px;">
                <h1 class="font-black tracking-tighter text-white drop-shadow-md" style="font-size:66px;line-height:1;margin-right:70px;">{{\Carbon\Carbon::parse($weeklyReport->week_start_date)->format('Y')}}</h1>
                <h2 class="font-black text-white drop-shadow-md" style="font-size:76px;line-height:.95;margin-top:8px;transform:translateX(15px);">WEEKLY<br>REPORT</h2>
                <h3 class="font-bold tracking-widest uppercase text-yellow-300 drop-shadow-md" style="font-size:22px;margin-top:18px;transform:translateX(-10px);">REGULER {{ $currentProgram->name ?? $programName ?? 'PROGRAM' }}</h3>
            </div>
        </div>
          <div class="absolute inset-0 z-10 pointer-events-none overflow-hidden" style="clip-path: inset(0px);">
              @foreach($diamonds as $idx=>$pos)
                @php $src=$coverSrcs[$idx]??null; @endphp
                @if($src)
                    <div style="position:absolute;top:{{$pos['top']}}px;left:{{$pos['left']}}px;width:{{$pos['size']}}px;height:{{$pos['size']}}px;filter:drop-shadow(0 10px 15px rgba(0,0,0,.25));">
                        <svg width="{{$pos['size']}}" height="{{$pos['size']}}" viewBox="0 0 {{$pos['size']}} {{$pos['size']}}">
                            <defs>
                                <clipPath id="diamondClip{{$idx}}">
                                    <polygon points="{{$pos['size']/2}},11 {{$pos['size']-11}},{{$pos['size']/2}} {{$pos['size']/2}},{{$pos['size']-11}} 11,{{$pos['size']/2}}" />
                                </clipPath>
                            </defs>
                            <polygon points="{{$pos['size']/2}},0 {{$pos['size']}},{{$pos['size']/2}} {{$pos['size']/2}},{{$pos['size']}} 0,{{$pos['size']/2}}" fill="#fff" />
                            <image href="{{$src}}" width="{{$pos['size']}}" height="{{$pos['size']}}" preserveAspectRatio="xMidYMid slice" clip-path="url(#diamondClip{{$idx}})" />
                        </svg>
                    </div>
                @endif
              @endforeach
          </div>
          <div class="pl-4 relative z-10 flex justify-start pb-8" style="top:3px;">
              <div class="bg-[#d97706] text-white px-5 py-2 text-sm font-bold tracking-wider shadow-lg border-2 border-white">
                  http://e-learning.first-resources.com/
              </div>
          </div>
    </div>


    <!-- PAGE 2: INTRO PROGRAM (REGULER) -->
    @if(!$weeklyReport->is_merged && is_array($weeklyReport->strip_photos) && count($weeklyReport->strip_photos) > 0)
    <div class="page-a4 bg-white p-10 flex flex-col">
        <div class="flex justify-between items-center border-b-2 border-gray-800 pb-2 mb-6 text-xs font-bold text-gray-600">
            <span>{{ \Carbon\Carbon::parse($weeklyReport->week_start_date)->locale('id')->translatedFormat('d') }} – {{ \Carbon\Carbon::parse($weeklyReport->week_end_date)->locale('id')->translatedFormat('d F Y') }}</span>
            <span>REGULER ENGINEERING</span>
        </div>

        @php
            // Prefer strip_photos for page 2 strip, fallback to session photos
            $programPhotos = $sessions->flatMap(fn($s) => $s->photos);
            $page2Photos = [];
            if (!$weeklyReport->is_merged && is_array($weeklyReport->strip_photos) && count($weeklyReport->strip_photos) > 0) {
                foreach ($weeklyReport->strip_photos as $sp) {
                    $page2Photos[] = Storage::url($sp);
                }
            } else {
                $page2Photos = $programPhotos->take(4)->map(fn($p) => Storage::url($p->photo_path))->values()->toArray();
            }
            
            if (!$weeklyReport->is_merged && $weeklyReport->cover_photo_main) {
                $bigPhotoSrc = Storage::url($weeklyReport->cover_photo_main);
            } else {
                $mainP = $programPhotos->count() > 4 ? $programPhotos->get(4) : $programPhotos->first();
                $bigPhotoSrc = $mainP ? Storage::url($mainP->photo_path) : null;
            }
        @endphp
        <div class="flex border border-gray-300 divide-x divide-gray-300 mb-8 overflow-hidden" style="height: 90px;">
            @forelse($page2Photos as $photoSrc)
                <div class="flex-1 min-w-0 bg-gray-200 overflow-hidden">
                    <img src="{{ $photoSrc }}" class="w-full h-full object-cover block" style="display:block;">
                </div>
            @empty
                <div class="flex-1 bg-gray-100 flex items-center justify-center text-gray-400 text-xs font-bold">
                    Belum ada foto
                </div>
            @endforelse
        </div>

        <h1 class="text-[#1e3a5f] text-center w-full" style="font-family: 'Georgia', 'Times New Roman', serif; font-weight: 700; font-size: 6.5rem; line-height: 0.9; letter-spacing: .01em; margin-bottom: 1.25rem;">REGULER</h1>
        
        <div class="w-full mb-6 overflow-hidden rounded-sm shadow-sm bg-gray-200" style="height: 260px;">
            @if($bigPhotoSrc)
                <img src="{{ $bigPhotoSrc }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm font-bold">Foto Dokumentasi</div>
            @endif
        </div>

        <h2 class="text-2xl font-black text-[#1e3a8a] mb-3">
            {{ $currentProgram->name ?? $programName ?? '-' }} - {{ \Carbon\Carbon::parse($weeklyReport->week_start_date)->format('Y') }}
        </h2>

        <div class="flex gap-8 flex-1">
            <div class="w-2/3">
                <p class="font-bold text-gray-800 leading-relaxed text-justify" style="font-size:19px;">
                    {{ $currentProgram->description ?? 'Deskripsi program belum diisi.' }}
                </p>
            </div>
            <div class="w-1/3 border-l-2 border-gray-200 pl-6">
                <h3 class="text-xl font-black text-[#1e3a8a] mb-2" style="font-style: italic;">"Quote<br>Pelatihan"</h3>
                <p class="text-sm font-bold text-gray-800 leading-relaxed" style="font-style: italic;">
                    {{ $currentProgram->quote ?? 'Belum ada quote untuk laporan ini.' }}
                </p>
                <div style="width: 60px; height: 2px; background: #1e3a8a; margin-top: 10px;"></div>
            </div>
        </div>
    </div>
    @endif

<!-- PAGE(s): One per session -->
    @foreach($sessions as $session)
    @php
        $sessionTypeLabel = match($session->session_type) {
            'orientasi' => 'ORIENTASI',
            'inclass' => 'INCLASS',
            'ojt' => 'ON THE JOB TRAINING',
            'supervisi_ojt' => 'SUPERVISI OJT',
            'moa' => 'MOA',
            default => strtoupper($session->session_type)
        };
        $sessionColor = '#1e3a8a';
        $sessionParticipants = $session->sessionParticipants;
        $activeParticipants = $sessionParticipants->where('participation_type', 'aktif');
        $passiveParticipants = $sessionParticipants->where('participation_type', 'pasif');
        $sessionEval = $session->evaluation;
        $sessionPhotos = $session->photos;
        
        $bannerLeftPhoto = $sessionPhotos->where('photo_type', 'banner_left')->first();
        $bannerRightPhoto = $sessionPhotos->where('photo_type', 'banner_right')->first();
        $gridPhotos = $sessionPhotos->where('photo_type', 'dokumentasi')->values();

        // Backward compatibility for old data
        if (!$bannerLeftPhoto && $sessionPhotos->count() > 0 && $sessionPhotos->first()->created_at < '2026-09-15 03:00:00') {
            $bannerLeftPhoto = $sessionPhotos->first();
            if ($sessionPhotos->count() > 1) {
                $bannerRightPhoto = $sessionPhotos[1];
            }
            $gridPhotos = $sessionPhotos->slice(2)->values();
        }

        $resultLines = $parseListLines($session->training_result ?? $session->results_or_impressions);
        $ojtPlacements = $session->ojtPlacements ?? collect();
        $ojtByRegion = $ojtPlacements->groupBy('region');
    @endphp

    <div class="page-a4 bg-white flex flex-col relative overflow-hidden" style="padding: 0 !important;">
        <!-- Header Strip Pita Menyatu -->
        <div style="display: flex; width: 100%; height: 160px; position: relative; margin-bottom: 24px; background-color: #f3f4f6;">
            <!-- Kolom Kiri: Foto Pertama -->
            <div style="width: 40%; height: 100%; overflow: hidden; position: relative;">
                @if($bannerLeftPhoto)
                    <img src="{{ Storage::url($bannerLeftPhoto->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 12px; font-weight: bold;">Foto Kiri</div>
                @endif
            </div>

            <!-- Kolom Kanan: Foto Kedua & Teks Tanggal -->
            <div style="width: 60%; height: 100%; overflow: hidden; position: relative;">
                @if($bannerRightPhoto && empty($session->banner_right_text))
                    <img src="{{ Storage::url($bannerRightPhoto->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: {{ $sessionColor }}; padding-left: 100px; padding-right: 20px;">
                        <h2 style="font-weight: 900; font-size: 32px; color: white; text-align: center; text-transform: uppercase; line-height: 1.2; text-shadow: 1px 1px 3px rgba(0,0,0,0.2);">
                            {{ $session->banner_right_text ?: $session->title }}
                        </h2>
                    </div>
                @endif
                
                <!-- Teks Tanggal Overlay -->
                <div style="position: absolute; top: 12px; right: 16px; text-align: right; z-index: 10; color: {{ $bannerRightPhoto ? 'black' : 'black' }};">
                    @php
                        $start = \Carbon\Carbon::parse($session->date_start)->locale('id');
                        $end = \Carbon\Carbon::parse($session->date_end)->locale('id');
                        
                        if ($start->isSameDay($end)) {
                            $dateText = $start->translatedFormat('l, d M Y');
                        } else {
                            $dayRange = $start->translatedFormat('l') . ' - ' . $end->translatedFormat('l');
                            
                            if ($start->isSameMonth($end) && $start->isSameYear($end)) {
                                $dateRange = $start->format('d') . ' - ' . $end->translatedFormat('d M Y');
                            } elseif ($start->isSameYear($end)) {
                                $dateRange = $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M Y');
                            } else {
                                $dateRange = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
                            }
                            $dateText = $dayRange . ', ' . $dateRange;
                        }
                    @endphp
                    <h3 style="font-weight: 900; font-size: 16px; line-height: 1.2; margin: 0; color: black; text-shadow: 1px 1px 4px rgba(255,255,255,0.9), -1px -1px 4px rgba(255,255,255,0.9);">
                        {{ $dateText }}
                    </h3>
                </div>
            </div>

<!-- Badge Lingkaran di Tengah (Sudah Presisi Simetris) -->
            <div style="position: absolute; left: 40%; top: 50%; margin-left: -110px; margin-top: -110px; z-index: 20; width: 220px; height: 220px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));">
                <!-- SVG Background -->
                <svg width="220" height="220" viewBox="0 0 220 220" style="position: absolute; top: 0; left: 0; z-index: 1;">
                    <circle cx="110" cy="110" r="110" fill="{{ $sessionColor }}" fill-opacity="0.8" />
                    <circle cx="110" cy="110" r="85" fill="#ffffff" />
                </svg>
                <!-- Text Container -->
                <div style="position: absolute; left: 0; top: 0; width: 220px; height: 220px; z-index: 2; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 0 35px;">
                    @php
                        $rawTitle = strtoupper($session->title ?? $sessionTypeLabel);
                        $words = explode(' ', $rawTitle);
                        $isFirstWordInclass = count($words) > 0 && $words[0] === 'INCLASS';
                    @endphp
                    @if($isFirstWordInclass)
                        <span style="font-weight: 900; font-size: 22px; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $sessionColor }}; line-height: 1.1; margin-bottom: 2px;">
                            INCLASS
                        </span>
                        @if(count($words) > 1)
                            <span style="font-weight: 900; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $sessionColor }}; line-height: 1.2;">
                                {{ implode(' ', array_slice($words, 1)) }}
                            </span>
                        @endif
                    @else
                        <span style="font-weight: 900; font-size: 15px; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $sessionColor }}; margin-bottom: 2px; line-height: 1.2;">
                            {{ $rawTitle }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="flex" style="padding: 0 40px {{ (in_array($session->session_type, ['ojt', 'supervisi_ojt']) && $ojtPlacements->count() > 0) ? '20px' : '110px' }} 40px;">
            <!-- Left Column -->
            <div class="flex flex-col gap-5" style="width: 55%; padding-right: 32px;">
                <div>
                    <h2 class="inline-block text-white px-3 py-1 font-bold text-sm mb-2" style="background-color: {{ $sessionColor }};">
                        TUJUAN TRAINING
                    </h2>

                    @php
                        $purposeLines = $parseListLines($session->training_purpose);
                    @endphp

                    @if($purposeLines->count() > 0)
                        <ol class="text-sm font-bold text-gray-800 leading-snug" style="text-align: justify; padding-left: 1.5rem; margin: 0; list-style-type: decimal;">
                            @foreach($purposeLines as $line)
                                <li style="margin: 0 0 0.15rem 0; padding-left: 0.2rem;">{{ $line }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-sm font-bold text-gray-800 leading-snug" style="text-align: justify; margin: 0;">Tujuan training belum didefinisikan.</p>
                    @endif
                </div>

                @if(!in_array($session->session_type, ['ojt', 'supervisi_ojt']))
                <div class="flex items-center gap-6 border border-gray-200 p-4 rounded-xl bg-gray-50 shadow-sm">
                    <div class="w-full">
                        <h2 class="inline-block text-white px-3 py-1 font-bold text-sm mb-2" style="background-color: {{ $sessionColor }};">TEMPAT & PESERTA</h2>
                        <table class="text-sm font-bold text-gray-800 w-full mt-1">
                            <tr><td class="w-24 pb-1">Tanggal</td><td class="pb-1">: {{ \Carbon\Carbon::parse($session->date_start)->locale('id')->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($session->date_end)->locale('id')->translatedFormat('d M Y') }}</td></tr>
                            @if(!empty(trim($session->location)) && trim($session->location) !== '-')
                                <tr><td class="pb-1">Lokasi</td><td class="pb-1">: {{ $session->location }}</td></tr>
                            @endif
                            <tr><td class="pb-1">Provider</td><td class="pb-1">: {{ $session->provider ?? 'FR Academy' }}</td></tr>
                            <tr><td class="pb-1">Peserta</td><td class="pb-1">: {{ $sessionParticipants->count() }} Orang</td></tr>
                        </table>
                    </div>
                </div>
                @endif

                @if($resultLines->count() > 0)
                <div class="mb-3">
                    <h2 class="inline-block text-white px-3 py-1 font-bold text-sm mb-2" style="background-color: {{ $sessionColor }};">
                        HASIL TRAINING / PESAN &amp; KESAN
                    </h2>
                    <ol class="text-sm font-bold text-gray-800 leading-snug" style="text-align: justify; padding-left: 1.5rem; margin: 0; list-style-type: decimal;">
                        @foreach($resultLines as $line)
                            <li style="margin: 0 0 0.15rem 0; padding-left: 0.2rem;">{{ $line }}</li>
                        @endforeach
                    </ol>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="flex flex-col gap-5 border-l-2 border-transparent" style="width: 45%; padding-left: 16px;">
                <!-- Trainer Info -->
                @if($session->trainer_name)
                <div class="flex flex-col items-end mb-4 w-full">
                    <div class="flex flex-col items-center">
                        @if($session->trainer_photo_path)
                            <img src="{{ Storage::url($session->trainer_photo_path) }}" 
                                 class="border-2 border-gray-200 bg-white p-0.5 shadow-sm"
                                 style="max-height: 140px; max-width: 100%; width: auto; object-fit: contain; display: block;">
                        @else
                            @php
                                $tName = $session->trainer_name;
                                $initials = collect(explode(' ', $tName))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                            @endphp
                            <div class="w-32 h-40 flex flex-col items-center justify-center bg-gray-800 text-white shadow-sm border-2 border-gray-200 p-0.5">
                                <span class="text-3xl font-black">{{ $initials }}</span>
                            </div>
                        @endif
                        
                        <!-- Label Nama -->
                        <div class="text-white py-1.5 px-5 font-bold text-xs shadow-md text-center inline-block relative z-10 rounded-none mt-2" style="background-color: {{ $sessionColor }};">
                            {{ $session->trainer_name }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Galeri Dokumentasi (Otomatis Besar jika 1 Foto, Grid jika > 1 Foto) -->
                @if($gridPhotos->count() > 0)
                <div class="w-full">
                    <h2 class="text-white px-3 py-1 font-bold text-sm mb-2 inline-block" style="background-color: {{ $sessionColor }};">DOKUMENTASI</h2>
                    @php
                        $shouldStackPhotos = (empty($session->trainer_name) || in_array($session->session_type, ['ojt', 'supervisi_ojt'])) && $gridPhotos->count() <= 2;
                    @endphp
                    @if($gridPhotos->count() == 1)
                        <div style="width: 100%; height: 140px; background-color: #f3f4f6; overflow: hidden; border: 2px solid #e5e7eb;">
                            <img src="{{ Storage::url($gridPhotos->first()->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </div>
                    @else
                        @if($shouldStackPhotos)
                            <div class="flex flex-col gap-3">
                                @foreach($gridPhotos as $photo)
                                    <div style="width: 100%; height: 120px; background-color: #f3f4f6; overflow: hidden; border: 2px solid #e5e7eb;">
                                        <img src="{{ Storage::url($photo->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @php
                                $gridCols = $gridPhotos->count() > 4 ? 3 : 2;
                                $gridHeight = $gridPhotos->count() > 4 ? '90px' : '110px';
                            @endphp
                            <div style="display: grid; grid-template-columns: repeat({{ $gridCols }}, 1fr); gap: 8px;">
                                @foreach($gridPhotos as $photo)
                                    <div style="height: {{ $gridHeight }}; background-color: #f3f4f6; overflow: hidden; border: 2px solid #e5e7eb;">
                                        <img src="{{ Storage::url($photo->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
                @endif
                
                <!-- Pemisahan Trainee Aktif & Pasif -->
                <div class="flex flex-col gap-3">
                    @if($activeParticipants->count() > 0)
                    <!-- Box Trainee Aktif -->
                    <div>
                        <h2 class="text-white px-3 py-1 font-bold text-sm mb-2 block text-center uppercase tracking-wider" style="background-color: {{ $sessionColor }};">TRAINEE AKTIF</h2>
                        <ul class="text-sm space-y-1.5 list-disc pl-5 text-gray-700">
                            @foreach($activeParticipants as $p)
                                <li class="py-0.5">
                                    <span class="font-bold text-gray-800">{{ $p->trainee->name ?? 'Trainee' }} {{ $p->trainee->university ? '(' . $p->trainee->university . ')' : '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($passiveParticipants->count() > 0)
                    <!-- Box Trainee Pasif -->
                    <div>
                        <h2 class="text-white px-3 py-1 font-bold text-sm mb-2 block text-center uppercase tracking-wider bg-[#1e3a8a]">TRAINEE PASIF</h2>
                        <ul class="text-sm space-y-1.5 list-disc pl-5 text-gray-700">
                            @foreach($passiveParticipants as $p)
                                <li class="py-0.5">
                                    <span class="font-bold text-gray-800">{{ $p->trainee->name ?? 'Trainee' }} {{ $p->trainee->university ? '(' . $p->trainee->university . ')' : '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    <!-- Grafik Evaluasi (Vertical) -->
                    @if($session->evaluations && $session->evaluations->count() > 0)
                    <div>
                        <h2 class="text-white px-3 py-1 font-bold text-sm mb-2 block uppercase tracking-wider" style="background-color: {{ $sessionColor }};">GRAFIK EVALUASI TRAINING</h2>
                        
                        @php
                            $rawEvals = [
                                'Baik Sekali' => $session->evaluations->where('category', 'Baik Sekali')->first(),
                                'Baik' => $session->evaluations->where('category', 'Baik')->first(),
                                'Cukup Baik' => $session->evaluations->where('category', 'Cukup Baik')->first(),
                                'Kurang Baik' => $session->evaluations->where('category', 'Kurang Baik')->first(),
                                'Tidak Baik' => $session->evaluations->where('category', 'Tidak Baik')->first(),
                            ];
                            
                            $evals = [];
                            foreach ($rawEvals as $label => $e) {
                                if ($e) {
                                    $totalVotes = floatval($e->materi_percentage) + floatval($e->trainer_percentage) + floatval($e->fasilitas_percentage);
                                    // Sembunyikan jika Kurang Baik atau Tidak Baik dan tidak ada vote
                                    if (($label === 'Kurang Baik' || $label === 'Tidak Baik') && $totalVotes == 0) {
                                        continue;
                                    }
                                    $evals[$label] = $e;
                                }
                            }
                        @endphp

                        <div style="background-color: #fff; text-align: center;">
                            <table style="width: 100%; table-layout: fixed; border-collapse: collapse; height: 180px; font-size: 8px;">
                                <tr style="height: 160px;">
                                    @php
                                        $totalParticipants = $weeklyReport->trainingProgram->trainees->where('status', '!=', 'resign')->count();
                                        $calcPct = function($val) use ($totalParticipants) {
                                            if ($totalParticipants == 0) return 0;
                                            return round((floatval($val) / $totalParticipants) * 100);
                                        };
                                    @endphp
                                     @foreach($evals as $label => $e)
                                        <td style="vertical-align: bottom; text-align: center; padding: 0 2px; {{ !$loop->last ? 'border-right: 1px solid #94a3b8;' : '' }}">
                                            <table style="width: 100%; height: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="vertical-align: bottom; width: 33%; padding: 0 1px;">
                                                        <div style="font-size: 6px; color: #666; font-weight: bold; margin-bottom: 1px;">{{ $calcPct($e->materi_percentage) }}%</div>
                                                        <table style="width: 100%; height: 140px; border-collapse: collapse;">
                                                            <tr><td style="height: {{ 100 - $calcPct($e->materi_percentage) }}%; padding: 0;"></td></tr>
                                                            <tr><td style="height: {{ $calcPct($e->materi_percentage) }}%; background-color: #3b82f6; padding: 0;"></td></tr>
                                                        </table>
                                                    </td>
                                                    <td style="vertical-align: bottom; width: 33%; padding: 0 1px;">
                                                        <div style="font-size: 6px; color: #666; font-weight: bold; margin-bottom: 1px;">{{ $calcPct($e->trainer_percentage) }}%</div>
                                                        <table style="width: 100%; height: 140px; border-collapse: collapse;">
                                                            <tr><td style="height: {{ 100 - $calcPct($e->trainer_percentage) }}%; padding: 0;"></td></tr>
                                                            <tr><td style="height: {{ $calcPct($e->trainer_percentage) }}%; background-color: #ef4444; padding: 0;"></td></tr>
                                                        </table>
                                                    </td>
                                                    <td style="vertical-align: bottom; width: 33%; padding: 0 1px;">
                                                        <div style="font-size: 6px; color: #666; font-weight: bold; margin-bottom: 1px;">{{ $calcPct($e->fasilitas_percentage) }}%</div>
                                                        <table style="width: 100%; height: 140px; border-collapse: collapse;">
                                                            <tr><td style="height: {{ 100 - $calcPct($e->fasilitas_percentage) }}%; padding: 0;"></td></tr>
                                                            <tr><td style="height: {{ $calcPct($e->fasilitas_percentage) }}%; background-color: #84cc16; padding: 0;"></td></tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endforeach
                                </tr>
                                <tr style="height: 20px;">
                                    @foreach($evals as $label => $e)
                                        <td style="text-align: center; padding-top: 5px; color: #374151; font-weight: bold; border-top: 1px solid #e5e7eb; font-size: 7px;">{{ $label }}</td>
                                    @endforeach
                                </tr>
                            </table>
                            
                            <!-- Legend -->
                            <div style="margin-top: 8px; font-size: 8px; color: #4b5563; font-weight: bold;">
                                <span style="display: inline-block; width: 8px; height: 8px; background-color: #3b82f6; margin-right: 2px;"></span> Materi &nbsp;&nbsp;
                                <span style="display: inline-block; width: 8px; height: 8px; background-color: #ef4444; margin-right: 2px;"></span> Trainer &nbsp;&nbsp;
                                <span style="display: inline-block; width: 8px; height: 8px; background-color: #84cc16; margin-right: 2px;"></span> Fasilitas
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @if(in_array($session->session_type, ['ojt', 'supervisi_ojt']) && $ojtPlacements->count() > 0)
        <!-- Lokasi OJT Full Width -->
        <div style="padding: 0 40px 10px 40px; position: relative; z-index: 20;">
            <h2 class="text-white px-6 py-1.5 font-bold text-sm mb-2 uppercase shadow-sm" style="background-color: {{ $sessionColor }}; display: inline-block; min-width: 150px;">LOKASI OJT</h2>
            
            @php
                $regionsCollection = collect($ojtByRegion);
                $half = floor($regionsCollection->count() / 2);
                if ($half == 0) $half = 1;
                $leftRegions = $regionsCollection->slice(0, $half);
                $rightRegions = $regionsCollection->slice($half);
            @endphp
            
            <div class="grid grid-cols-2 gap-x-12 text-sm text-gray-800">
                <!-- Kolom Kiri -->
                <div class="flex flex-col gap-y-4">
                    @foreach($leftRegions as $region => $placements)
                        <div class="w-full relative py-2" style="min-height: 140px;">
                            <!-- Map Background -->
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.85; z-index: 0; text-align: center;">
                                @php 
                                    $regionSlug = Str::slug($region);
                                    $mapPath = public_path('map-' . $regionSlug . '.png');
                                    $mapUrl = asset('map-' . $regionSlug . '.png');
                                @endphp
                                @if(file_exists($mapPath))
                                    <img src="{{ $mapUrl }}" alt="{{ $region }}" style="width: 300px; max-height: 140px; object-fit: contain; display: inline-block; padding-top: 10px;">
                                @else
                                    <div style="font-weight: 900; color: #e5e7eb; font-size: 40px; letter-spacing: 0.1em; text-transform: uppercase; padding-top: 80px;">
                                        {{ $region ?: 'REGION' }}
                                    </div>
                                @endif
                            </div>

                            <!-- Data Table -->
                            <table class="w-full text-sm relative z-10" style="text-shadow: 1px 1px 0 #fff, -1px 1px 0 #fff, 1px -1px 0 #fff, -1px -1px 0 #fff;">
                                @foreach($placements as $index => $p)
                                    <tr>
                                        <!-- Names -->
                                        <td class="py-1.5 w-6 font-semibold align-top">{{ $loop->iteration }}.</td>
                                        <td class="py-1.5 font-bold align-top leading-tight break-words pr-2 text-left">
                                            {{ $p->trainee->name ?? '&nbsp;' }}
                                        </td>
                                        
                                        <!-- Codes -->
                                        <td class="py-1.5 font-bold align-top leading-tight break-words text-left w-[35%] pl-4">
                                            {{ $p->location_code ?: '&nbsp;' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endforeach
                </div>

                <!-- Kolom Kanan -->
                <div class="flex flex-col gap-y-4">
                    @foreach($rightRegions as $region => $placements)
                        <div class="w-full relative py-2" style="min-height: 140px;">
                            <!-- Map Background -->
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.85; z-index: 0; text-align: center;">
                                @php 
                                    $regionSlug = Str::slug($region);
                                    $mapPath = public_path('map-' . $regionSlug . '.png');
                                    $mapUrl = asset('map-' . $regionSlug . '.png');
                                @endphp
                                @if(file_exists($mapPath))
                                    <img src="{{ $mapUrl }}" alt="{{ $region }}" style="width: 300px; max-height: 140px; object-fit: contain; display: inline-block; padding-top: 10px;">
                                @else
                                    <div style="font-weight: 900; color: #e5e7eb; font-size: 40px; letter-spacing: 0.1em; text-transform: uppercase; padding-top: 80px;">
                                        {{ $region ?: 'REGION' }}
                                    </div>
                                @endif
                            </div>

                            <!-- Data Table -->
                            <table class="w-full text-sm relative z-10" style="text-shadow: 1px 1px 0 #fff, -1px 1px 0 #fff, 1px -1px 0 #fff, -1px -1px 0 #fff;">
                                @foreach($placements as $index => $p)
                                    <tr>
                                        <!-- Names -->
                                        <td class="py-1.5 w-6 font-semibold align-top">{{ $loop->iteration }}.</td>
                                        <td class="py-1.5 font-bold align-top leading-tight break-words pr-2 text-left">
                                            {{ $p->trainee->name ?? '&nbsp;' }}
                                        </td>
                                        
                                        <!-- Codes -->
                                        <td class="py-1.5 font-bold align-top leading-tight break-words text-left w-[35%] pl-4">
                                            {{ $p->location_code ?: '&nbsp;' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- FOOTER -->
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 95px; overflow: hidden; z-index: 10;">
            <img src="{{ asset('footer.png') }}" alt="Footer" style="position:absolute; left:0; bottom:0; width:100%; height:95px; object-fit:cover; object-position:center bottom; display:block; z-index:0;">
            <div style="position:absolute; left:0; bottom:16px; z-index:2; padding-left:40px; color:#222; font-family:Arial, sans-serif; font-size:12px; line-height:1.25; padding-right:40px;">
                <div style="color:{{ $sessionColor }}; font-size:14px; font-weight:800; margin-bottom:2px;">
                    FR ACADEMY
                </div>
                <div>Gedung Learning Center, Kompleks PT Panca Surya</div>
                <div>Garden, Jln Kubang Raya Desa Kubang Jaya Kec Siak</div>
                <div>Hulu, Kab Kampar, Riau</div>
            </div>
        </div>

    </div>
    @endforeach
</div>
@if($sessions->isEmpty())
    <div class="page-a4 bg-white p-10 flex flex-col items-center justify-center relative pb-[95px]">
        <p class="text-gray-400 text-lg font-semibold">Belum ada sesi yang diinputkan untuk laporan ini.</p>
        
        <!-- FOOTER -->
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 95px; overflow: hidden; z-index: 10;">
            <img src="{{ asset('footer.png') }}" alt="Footer" style="position:absolute; left:0; bottom:0; width:100%; height:95px; object-fit:cover; object-position:center bottom; display:block; z-index:0;">
            <div style="position:absolute; left:0; bottom:16px; z-index:2; padding-left:40px; color:#222; font-family:Arial, sans-serif; font-size:12px; line-height:1.25; padding-right:40px;">
                <div style="color:#1e3a8a; font-size:14px; font-weight:800; margin-bottom:2px;">
                    FR ACADEMY
                </div>
                <div>Gedung Learning Center, Kompleks PT Panca Surya</div>
                <div>Garden, Jln Kubang Raya Desa Kubang Jaya Kec Siak</div>
                <div>Hulu, Kab Kampar, Riau</div>
            </div>
        </div>
    </div>
@endif
@endforeach

<!-- PAGE: PORTAL Summary -->
@php
    $dailyClassesData = isset($dailyClasses) ? $dailyClasses : ($weeklyReport->dailyClasses ?? collect());
@endphp
@if($dailyClassesData->count() > 0)
<div class="page-a4 bg-white flex flex-col overflow-hidden relative" style="padding: 0 !important;">
    <div style="display: flex; width: 100%; height: 160px; position: relative; background-color: #1e3a8a;">
        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: #1e3a8a; color: white;">
            <h2 style="font-size: 32px; font-weight: 900; letter-spacing: 0.05em; text-align: center;">REKAP ABSENSI &amp; TUGAS HARIAN</h2>
        </div>
    </div>
    
    <div style="padding: 40px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 20px; text-transform: uppercase;">Data Diambil Dari Portal Trainee</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="background-color: #1e3a8a; color: white;">
                    <th style="padding: 12px; text-align: left; width: 25%;">Trainee</th>
                    <th style="padding: 12px; text-align: center; width: 25%;">Total Hadir</th>
                    <th style="padding: 12px; text-align: center; width: 25%;">Total Sakit/Izin/Alpha</th>
                    <th style="padding: 12px; text-align: center; width: 25%;">Tugas Dikumpulkan</th>
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
                        foreach($dc->attendances as $att) {
                            if (isset($traineeStats[$att->trainee_id])) {
                                if (strtolower($att->status) == 'hadir') {
                                    $traineeStats[$att->trainee_id]['hadir']++;
                                } else {
                                    $traineeStats[$att->trainee_id]['other']++;
                                }
                            }
                        }
                        
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
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px; font-weight: 600; color: #1f2937;">{{ $stat['name'] }}</td>
                    <td style="padding: 12px; text-align: center; font-weight: 700; color: #10b981;">{{ $stat['hadir'] }} Hari</td>
                    <td style="padding: 12px; text-align: center; font-weight: 700; color: #ef4444;">{{ $stat['other'] }} Hari</td>
                    <td style="padding: 12px; text-align: center; font-weight: 700; color: #3b82f6;">{{ $stat['tasks_submitted'] }} / {{ $stat['tasks_total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- FOOTER -->
    <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 95px; overflow: hidden; z-index: 10;">
        <img src="{{ asset('footer.png') }}" alt="Footer" style="position:absolute; left:0; bottom:0; width:100%; height:95px; object-fit:cover; object-position:center bottom; display:block; z-index:0;">
        <div style="position:absolute; left:0; bottom:16px; z-index:2; padding-left:40px; color:#222; font-family:Arial, sans-serif; font-size:12px; line-height:1.25; padding-right:40px;">
            <div style="color:#1e3a8a; font-size:14px; font-weight:800; margin-bottom:2px;">FR ACADEMY</div>
            <div>Gedung Learning Center, Kompleks PT Panca Surya</div>
            <div>Garden, Jln Kubang Raya Desa Kubang Jaya Kec Siak</div>
            <div>Hulu, Kab Kampar, Riau</div>
        </div>
    </div>
</div>
@endif


<!-- PAGE: Quiz Scores & Rankings (if any) -->
@if($weeklyReport->quizScores->count() > 0 || $weeklyReport->rankings->count() > 0)
<div class="page-a4 bg-white flex flex-col overflow-hidden relative" style="padding: 0 !important;">
    <!-- Header Strip Pita Menyatu (DOMPDF Safe) -->
    <div style="display: flex; width: 100%; height: 160px; position: relative; background-color: #1e3a8a;">
        <!-- Kolom Kiri: Foto Banner -->
        <div style="width: 40%; height: 100%; overflow: hidden; position: relative;">
            @if($weeklyReport->cover_photo_5)
                <img src="{{ Storage::url($weeklyReport->cover_photo_5) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @elseif($weeklyReport->cover_photo_main)
                <img src="{{ Storage::url($weeklyReport->cover_photo_main) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: #d1d5db; color: #6b7280; font-weight: bold;">FOTO KIRI</div>
            @endif
        </div>

        <!-- Kolom Kanan: Foto Banner 2 -->
        <div style="width: 60%; height: 100%; overflow: hidden; position: relative; background-color: #1e3a8a;">
            @if($weeklyReport->cover_photo_6)
                <img src="{{ Storage::url($weeklyReport->cover_photo_6) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: #9ca3af; color: #4b5563; font-weight: bold;">FOTO KANAN</div>
            @endif
        </div>

        <!-- Badge Lingkaran Tengah -->
        <div style="position: absolute; left: 40%; top: 50%; margin-left: -90px; margin-top: -90px; z-index: 20; width: 180px; height: 180px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));">
            <!-- SVG Background -->
            <svg width="180" height="180" viewBox="0 0 180 180" style="position: absolute; top: 0; left: 0; z-index: 1;">
                <circle cx="90" cy="90" r="90" fill="#1e3a8a" fill-opacity="0.8" />
                <circle cx="90" cy="90" r="70" fill="#ffffff" />
            </svg>
            <!-- Text Container -->
            <div style="position: absolute; left: 0; top: 0; width: 180px; height: 180px; z-index: 2; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                <span style="font-weight: 900; font-size: 16px; text-transform: uppercase; letter-spacing: 0.05em; color: #1e3a8a; margin-bottom: 2px;">
                    NILAI
                </span>
                <span style="font-size: 16px; font-weight: 900; color: #1f2937; text-transform: uppercase; line-height: 1.2;">
                    UJIAN
                </span>
            </div>
        </div>
    </div>

    <div style="padding: 20px 40px;">
        @php
            $uniqueSubjects = $weeklyReport->quizScores->pluck('materi_name')->unique()->values()->all();
            if (empty($uniqueSubjects)) {
                $uniqueSubjects = ['Belum Ada Data'];
            }
            $rankingsByTrainee = $weeklyReport->rankings->keyBy('trainee_id');
            // Get trainees who have either a quiz score or a ranking
            $traineeIds = $weeklyReport->quizScores->pluck('trainee_id')
                ->merge($weeklyReport->rankings->pluck('trainee_id'))
                ->unique();
            $trainees = \App\Models\Trainee::whereIn('id', $traineeIds)->get();
            
            // Sort trainees by name or ID (original order for main chart)
            $trainees = $trainees->sortBy('name')->values();
            
            $colors = ['#ed7d31', '#4472c4', '#ffc000', '#5b9bd5', '#a5a5a5', '#70ad47'];
        @endphp

        <!-- MAIN CHART: NILAI MATERI UMUM -->
        <h3 style="text-align: center; margin-top: 30px; margin-bottom: 20px; font-size: 14px; font-weight: bold; color: #555;">NILAI MATERI UMUM</h3>
        
        <!-- Y-Axis + Bars Container -->
        <div style="display: flex; height: 150px; position: relative; margin-bottom: 0;">
            <!-- Y-Axis Labels -->
            <div style="width: 30px; position: relative; font-size: 8px; color: #999; text-align: right;">
                <span style="position: absolute; top: -4px; right: 5px;">100</span>
                <span style="position: absolute; top: 26px; right: 5px;">80</span>
                <span style="position: absolute; top: 56px; right: 5px;">60</span>
                <span style="position: absolute; top: 86px; right: 5px;">40</span>
                <span style="position: absolute; top: 116px; right: 5px;">20</span>
                <span style="position: absolute; top: 146px; right: 5px;">0</span>
            </div>
            
            <!-- Bars Area -->
            <div style="flex-grow: 1; display: flex; border-left: 1px solid #ccc; border-bottom: 1px solid #ccc; position: relative;">
                <!-- Grid Lines -->
                <div style="position: absolute; top: 0px; left: 0; right: 0; border-top: 1px dashed #eee; z-index: 1;"></div>
                <div style="position: absolute; top: 30px; left: 0; right: 0; border-top: 1px dashed #eee; z-index: 1;"></div>
                <div style="position: absolute; top: 60px; left: 0; right: 0; border-top: 1px dashed #eee; z-index: 1;"></div>
                <div style="position: absolute; top: 90px; left: 0; right: 0; border-top: 1px dashed #eee; z-index: 1;"></div>
                <div style="position: absolute; top: 120px; left: 0; right: 0; border-top: 1px dashed #eee; z-index: 1;"></div>
                
                @foreach($trainees as $t)
                    <!-- Trainee Column Group -->
                    <div style="flex: 1; display: flex; align-items: flex-end; justify-content: center; z-index: 2; height: 100%;">
                        @php $scores = $weeklyReport->quizScores->where('trainee_id', $t->id)->keyBy('materi_name'); @endphp
                        @foreach($uniqueSubjects as $index => $subj)
                            @php
                                $s = isset($scores[$subj]) ? $scores[$subj]->score : 0;
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <div style="width: 10%; margin: 0 1px; background-color: {{ $color }}; height: {{ $s }}%;"></div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Data Table (X-Axis & Legend) -->
        <table style="width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 20px;">
            <tbody>
                <!-- Name Row -->
                <tr>
                    <td style="width: 150px; border: 1px solid #eee; padding: 4px; background-color: #f9f9f9;"></td>
                    @foreach($trainees as $t)
                        <td style="border: 1px solid #eee; padding: 4px; text-align: center; font-weight: bold; width: {{ 100 / count($trainees) }}%;">{{ $t->name }}</td>
                    @endforeach
                </tr>
                <!-- Subject Rows -->
                @foreach($uniqueSubjects as $index => $subj)
                    @php $color = $colors[$index % count($colors)]; @endphp
                    <tr>
                        <td style="border: 1px solid #eee; padding: 4px; background-color: #f9f9f9; border-left: 3px solid {{ $color }};">{{ $subj }}</td>
                        @foreach($trainees as $t)
                            @php $scores = $weeklyReport->quizScores->where('trainee_id', $t->id)->keyBy('materi_name'); @endphp
                            <td style="border: 1px solid #eee; padding: 4px; text-align: center;">{{ isset($scores[$subj]) ? number_format($scores[$subj]->score, 2) : '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOP 2 & BOTTOM 2 Charts -->
        <div style="display: flex; gap: 20px;">
            @php
                $sortedForRanks = $trainees->sortBy(function($t) use ($rankingsByTrainee) {
                    return $rankingsByTrainee[$t->id]->rank ?? 999;
                })->values();
                
                $top2 = $sortedForRanks->take(2);
                $bottom2 = $sortedForRanks->reverse()->take(2)->reverse()->values(); // Get last 2
            @endphp
            
            <!-- TOP 2 -->
            <div style="flex: 1;">
                <h3 style="text-align: center; font-size: 22px; font-weight: 900; margin-bottom: 15px; margin-top: 0;">TOP 2</h3>
                <div style="border: 1px solid #ccc; padding: 15px 15px 25px 5px; position: relative; background: #fff;">
                    <div style="text-align: center; font-size: 11px; color: #666; margin-bottom: 5px;">Rank</div>
                    @php
                        $topAvgs = [];
                        foreach($top2 as $t) {
                            $rk = $rankingsByTrainee[$t->id] ?? null;
                            $topAvgs[] = $rk ? $rk->average_score : 0;
                        }
                        $topMax = (count($topAvgs) > 0 && max($topAvgs) > 0) ? ceil(max($topAvgs)) + 1 : 100;
                        $topMin = (count($topAvgs) > 0 && min($topAvgs) > 0) ? max(0, floor(min($topAvgs)) - 1) : 0;
                        if ($topMax <= $topMin) { $topMax = $topMin + 5; }
                        $topRange = $topMax - $topMin;
                        $topSteps = 5;
                        $topStepVal = $topRange / $topSteps;
                    @endphp
                    <div style="display: flex; height: 110px;">
                        <!-- Y-Axis for top -->
                        <div style="width: 40px; position: relative; font-size: 8px; color: #666; text-align: right; border-right: 1px solid #ccc;">
                            @for($i = 0; $i <= $topSteps; $i++)
                                @php $yPos = ($i / $topSteps) * 110 - 4; @endphp
                                <span style="position: absolute; top: {{ $yPos }}px; right: 5px;">{{ number_format($topMax - ($i * $topStepVal), 2, ',', '.') }}</span>
                            @endfor
                        </div>
                        
                        <!-- Bars Area -->
                        <div style="flex: 1; display: flex; justify-content: space-around; align-items: flex-end; border-bottom: 1px solid #ccc; position: relative;">
                            @foreach($top2 as $index => $t)
                                @php 
                                    $rk = $rankingsByTrainee[$t->id] ?? null; 
                                    $rankNum = $rk ? $rk->rank : '-';
                                    $avg = $rk ? $rk->average_score : 0;
                                    $height = (($avg - $topMin) / $topRange) * 110;
                                    $height = max(5, min(110, $height)); // clamp
                                    $color = $index == 0 ? '#00b050' : '#ffc000'; // Green for 1, Yellow for 2
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; width: 40%;">
                                    <div style="font-size: 16px; font-weight: 900; margin-bottom: 2px;">{{ $rankNum }}</div>
                                    <div style="width: 100%; background-color: {{ $color }}; height: {{ $height }}px;"></div>
                                    <div style="font-size: 8px; margin-top: 5px; text-align: center; color: #666;">{{ $t->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTTOM 2 -->
            <div style="flex: 1;">
                <h3 style="text-align: center; font-size: 22px; font-weight: 900; margin-bottom: 15px; margin-top: 0;">BOTTOM 2</h3>
                <div style="border: 1px solid #ccc; padding: 15px 15px 25px 5px; position: relative; background: #fff;">
                    <div style="text-align: center; font-size: 11px; color: #666; margin-bottom: 5px;">Rank</div>
                    @php
                        $botAvgs = [];
                        foreach($bottom2 as $t) {
                            $rk = $rankingsByTrainee[$t->id] ?? null;
                            $botAvgs[] = $rk ? $rk->average_score : 0;
                        }
                        $botMax = (count($botAvgs) > 0 && max($botAvgs) > 0) ? ceil(max($botAvgs)) + 1 : 100;
                        $botMin = (count($botAvgs) > 0 && min($botAvgs) > 0) ? max(0, floor(min($botAvgs)) - 1) : 0;
                        if ($botMax <= $botMin) { $botMax = $botMin + 5; }
                        $botRange = $botMax - $botMin;
                        $botSteps = 5;
                        $botStepVal = $botRange / $botSteps;
                    @endphp
                    <div style="display: flex; height: 110px;">
                        <!-- Y-Axis for bottom -->
                        <div style="width: 40px; position: relative; font-size: 8px; color: #666; text-align: right; border-right: 1px solid #ccc;">
                            @for($i = 0; $i <= $botSteps; $i++)
                                @php $yPos = ($i / $botSteps) * 110 - 4; @endphp
                                <span style="position: absolute; top: {{ $yPos }}px; right: 5px;">{{ number_format($botMax - ($i * $botStepVal), 2, ',', '.') }}</span>
                            @endfor
                        </div>
                        
                        <!-- Bars Area -->
                        <div style="flex: 1; display: flex; justify-content: space-around; align-items: flex-end; border-bottom: 1px solid #ccc; position: relative;">
                            @foreach($bottom2 as $index => $t)
                                @php 
                                    $rk = $rankingsByTrainee[$t->id] ?? null; 
                                    $rankNum = $rk ? $rk->rank : '-';
                                    $avg = $rk ? $rk->average_score : 0;
                                    $height = (($avg - $botMin) / $botRange) * 110;
                                    $height = max(5, min(110, $height)); // clamp
                                    $color = $index == 0 ? '#ffff00' : '#4472c4'; // Yellow for 2nd last, Blue for last
                                @endphp
                                <div style="display: flex; flex-direction: column; align-items: center; width: 42%; position: relative;">
                                    <span style="font-size: 22px; font-weight: 900; margin-bottom: 2px;">{{ $rankNum }}</span>
                                    <div style="width: 100%; background-color: {{ $color }}; height: {{ $height }}px;"></div>
                                    <span style="position: absolute; bottom: -20px; font-size: 9px; color: #333; width: 160%; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $t->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Notes -->
        @if(!empty($weeklyReport->quiz_notes))
        <div style="margin-top: 20px;">
            <p style="font-weight: bold; font-size: 11px; margin-bottom: 2px;">Notes :</p>
            <p style="font-size: 11px; font-weight: bold;">{{ $weeklyReport->quiz_notes }}</p>
        </div>
        @endif
    </div>

    <!-- FOOTER -->
    <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 95px; overflow: hidden; z-index: 10;">
        <img src="{{ asset('footer.png') }}" alt="Footer" style="position:absolute; left:0; bottom:0; width:100%; height:95px; object-fit:cover; object-position:center bottom; display:block; z-index:0;">
        <div style="position:absolute; left:0; bottom:16px; z-index:2; padding-left:40px; color:#222; font-family:Arial, sans-serif; font-size:12px; line-height:1.25; padding-right:40px;">
            <div style="color:#1e3a8a; font-size:14px; font-weight:800; margin-bottom:2px;">
                FR ACADEMY
            </div>
            <div>Gedung Learning Center, Kompleks PT Panca Surya</div>
            <div>Garden, Jln Kubang Raya Desa Kubang Jaya Kec Siak</div>
            <div>Hulu, Kab Kampar, Riau</div>
        </div>
    </div>
</div>
@endif

<!-- PAGE: Resigned Trainees -->
@php
    $resignedTrainees = \App\Models\Trainee::with('resignation')
        ->where('training_program_id', $weeklyReport->training_program_id)
        ->where('status', 'resign')
        ->get();
@endphp

@if($resignedTrainees->count() > 0)
    @foreach($resignedTrainees->chunk(2) as $resignChunk)
    <div class="page-a4 bg-white flex flex-col overflow-hidden relative" style="padding: 0 !important;">
    <!-- Header Strip Pita Menyatu (DOMPDF Safe) -->
    <div style="display: flex; width: 100%; height: 160px; position: relative; background-color: #1e3a8a;">
        <!-- Kolom Kiri: Foto Banner -->
        <div style="width: 40%; height: 100%; overflow: hidden; position: relative;">
            @if($weeklyReport->cover_photo_4)
                <img src="{{ Storage::url($weeklyReport->cover_photo_4) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @elseif($allPhotos->count() > 0)
                <img src="{{ Storage::url($allPhotos->first()->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: #d1d5db; color: #6b7280; font-weight: bold;">FOTO KIRI</div>
            @endif
        </div>

        <!-- Kolom Kanan: Judul Biru -->
        <div style="width: 60%; height: 100%; background-color: #1e3a8a; display: flex; flex-direction: column; justify-content: center; align-items: flex-end; padding-right: 64px;">
            <h2 style="margin: 0; color: white; font-size: 26px; font-weight: 900; letter-spacing: 0.05em; line-height: 1.2; text-align: right;">TRAINEE</h2>
            <h3 style="margin: 0; color: white; font-size: 26px; font-weight: 900; letter-spacing: 0.05em; line-height: 1.2; text-align: right;">MENGUNDURKAN DIRI</h3>
        </div>

        <!-- Badge Lingkaran Tengah -->
        <div style="position: absolute; left: 40%; top: 50%; margin-left: -90px; margin-top: -90px; z-index: 20; width: 180px; height: 180px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));">
            <!-- SVG Background -->
            <svg width="180" height="180" viewBox="0 0 180 180" style="position: absolute; top: 0; left: 0; z-index: 1;">
                <circle cx="90" cy="90" r="90" fill="#1e3a8a" fill-opacity="0.8" />
                <circle cx="90" cy="90" r="70" fill="#ffffff" />
            </svg>
            <!-- Text Container -->
            <div style="position: absolute; left: 0; top: 0; width: 180px; height: 180px; z-index: 2; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                <span style="font-weight: 900; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; color: #1e3a8a; margin-bottom: 2px;">
                    TRAINEE
                </span>
                <span style="font-size: 12px; font-weight: 900; color: #1f2937; text-transform: uppercase; line-height: 1.2;">
                    RESIGN
                </span>
            </div>
        </div>
    </div>

    <div class="flex-1 px-8 pt-8 flex flex-col gap-0">
        @foreach($resignChunk as $rt)
        @php $resign = $rt->resignation; @endphp
        <div class="{{ $loop->last && count($resignChunk) == 2 ? '' : 'pb-6' }} {{ !$loop->last ? 'border-b-4 border-black mb-6' : '' }}">
            <div class="flex justify-between items-start mb-4">
                <!-- Profile Image -->
                <div class="w-32 h-40 bg-gray-200 border-2 border-gray-300">
                    @if($resign && $resign->photo_path)
                        <img src="{{ Storage::url(ltrim($resign->photo_path, '/')) }}" class="w-full h-full object-cover">
                    @elseif($rt->photo_path)
                        <img src="{{ Storage::url(ltrim($rt->photo_path, '/')) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs text-center font-bold">FOTO<br>TRAINEE</div>
                    @endif
                </div>

                <!-- Data Profile -->
                <div class="flex-1 ml-6 pt-2">
                    <h3 class="inline-block bg-[#1e3a8a] text-white px-4 py-1 font-bold text-lg mb-3">Profil Trainee</h3>
                    <table class="text-sm font-semibold text-gray-800 w-full mt-2">
                        <tr><td class="w-24 pb-1 text-gray-500">Nama</td><td class="pb-1">: {{ $rt->name }}</td></tr>
                        <tr><td class="pb-1 text-gray-500">Asal</td><td class="pb-1">: {{ $rt->origin ?? '-' }}</td></tr>
                        <tr><td class="pb-1 text-gray-500">Lahir</td><td class="pb-1">: {{ $resign->birth_place ?? '-' }}</td></tr>
                        <tr><td class="pb-1 text-gray-500">Tgl Lahir</td><td class="pb-1">: {{ $resign && $resign->birth_date ? \Carbon\Carbon::parse($resign->birth_date)->format('d M Y') : '-' }}</td></tr>
                        <tr><td class="pb-1 text-gray-500">Alumni</td><td class="pb-1">: {{ $rt->university ?? '-' }}</td></tr>
                        <tr><td class="pb-1 text-gray-500">Jurusan</td><td class="pb-1">: {{ $rt->major ?? '-' }}</td></tr>
                    </table>
                </div>

                <!-- Scores from Resignation -->
                @if($resign)
                <div class="w-48">
                    <div class="bg-[#1e3a8a] text-white text-center font-bold py-1 text-sm">Nilai Materi In Class</div>
                    <div class="flex border-2 border-[#1e3a8a] text-center mb-4">
                        <div class="w-1/2 border-r-2 border-[#1e3a8a]">
                            <div class="bg-blue-100 text-[#1e3a8a] text-xs font-bold py-1">Rata-rata</div>
                            <div class="py-3 font-black text-xl text-gray-800">{{ number_format($resign->avg_inclass_score, 2) }}</div>
                        </div>
                        <div class="w-1/2">
                            <div class="bg-blue-100 text-[#1e3a8a] text-xs font-bold py-1">Ranking</div>
                            <div class="py-3 font-black text-xl text-gray-800">{{ $resign->inclass_rank ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="bg-[#1e3a8a] text-white text-center font-bold py-1 text-sm">Nilai Materi Praktek</div>
                    <div class="flex border-2 border-[#1e3a8a] text-center">
                        <div class="w-1/2 border-r-2 border-[#1e3a8a]">
                            <div class="bg-blue-100 text-[#1e3a8a] text-xs font-bold py-1">Rata-rata</div>
                            <div class="py-3 font-black text-xl text-gray-800">{{ number_format($resign->avg_practice_score, 2) }}</div>
                        </div>
                        <div class="w-1/2">
                            <div class="bg-blue-100 text-[#1e3a8a] text-xs font-bold py-1">Ranking</div>
                            <div class="py-3 font-black text-xl text-gray-800">{{ $resign->practice_rank ?: '-' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Alasan Resign -->
            <div class="mt-4">
                <h3 class="inline-block bg-[#1e3a8a] text-white px-4 py-1 font-bold text-lg mb-2">Alasan Resign</h3>
                <p class="text-sm font-bold text-gray-800 leading-relaxed mt-2 p-4 bg-gray-50 border border-gray-200 rounded-lg whitespace-pre-line" style="text-align: justify;">{{ $resign->reason ?? 'Belum ada penjelasan spesifik mengenai alasan pengunduran diri trainee ini.' }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- FOOTER -->
    <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 95px; overflow: hidden; z-index: 10;">
        <img src="{{ asset('footer.png') }}" alt="Footer" style="position:absolute; left:0; bottom:0; width:100%; height:95px; object-fit:cover; object-position:center bottom; display:block; z-index:0;">
        <div style="position:absolute; left:0; bottom:16px; z-index:2; padding-left:40px; color:#222; font-family:Arial, sans-serif; font-size:12px; line-height:1.25; padding-right:40px;">
            <div style="color:#1e3a8a; font-size:14px; font-weight:800; margin-bottom:2px;">
                FR ACADEMY
            </div>
            <div>Gedung Learning Center, Kompleks PT Panca Surya</div>
            <div>Garden, Jln Kubang Raya Desa Kubang Jaya Kec Siak</div>
            <div>Hulu, Kab Kampar, Riau</div>
        </div>
    </div>
    </div>
    @endforeach
@endif