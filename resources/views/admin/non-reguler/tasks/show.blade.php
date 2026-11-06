<x-admin-layout>
    <x-slot name="title">Review Pengumpulan Tugas</x-slot>

    <!-- Header Section (Hero) -->
    <div class="bg-[#064e3b] rounded-2xl p-6 md:p-8 mb-6 shadow-md">
        <div class="flex flex-col items-start gap-4">
            <a href="{{ route('training-non-reguler.daily-classes.show', $task->dailyClass->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 hover:bg-white/20 text-emerald-50 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Kelas
            </a>
            
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $task->title }}</h1>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-black/20 text-emerald-50 px-2.5 py-1 rounded text-xs font-medium">
                        <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        {{ $task->dailyClass->title }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-black/20 text-emerald-50 px-2.5 py-1 rounded text-xs font-medium">
                        <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tenggat: {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y, H:i') : '-' }}
                    </span>
                    @if($task->deadline)
                        @if(\Carbon\Carbon::parse($task->deadline)->isPast())
                            <span class="inline-flex items-center gap-1 bg-red-500/30 text-red-100 border border-red-400/30 px-2.5 py-1 rounded text-xs font-semibold">
                                <svg class="w-3.5 h-3.5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Tenggat Telah Berakhir
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 px-2.5 py-1 rounded text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Tenggat Aktif
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content with Alpine Task Viewer -->
    <div x-data="classroomTaskViewer()" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Pengumpulan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Lihat dan periksa file tugas yang dikumpulkan oleh peserta.</p>
            </div>
            @php
                $validSubmissions = $task->submissions->filter(function($sub) {
                    return $sub->status == 'sudah' && !empty($sub->file_path);
                });
                $submissionsCount = $validSubmissions->count();
                $lateCount = $validSubmissions->filter(function($sub) use ($task) {
                    return $task->deadline && $sub->submitted_at && \Carbon\Carbon::parse($sub->submitted_at)->gt(\Carbon\Carbon::parse($task->deadline));
                })->count();
                $onTimeCount = $submissionsCount - $lateCount;
            @endphp
            <div class="flex flex-wrap items-center gap-2">
                <div class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg text-sm font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $submissionsCount }} Peserta Mengumpulkan
                </div>
                @if($submissionsCount > 0)
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">
                        <span class="text-emerald-700 font-bold">{{ $onTimeCount }}</span> Tepat Waktu
                    </span>
                    @if($lateCount > 0)
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-xs font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span class="font-bold">{{ $lateCount }}</span> Terlambat
                        </span>
                    @endif
                    <!-- Download All Button -->
                    <a href="{{ route('training-non-reguler.tasks.download-all', $task->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#064e3b] hover:bg-[#043327] text-white rounded-lg text-xs font-semibold shadow-sm transition-all ml-1" title="Unduh semua file tugas peserta dalam satu arsip ZIP">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Unduh Semua Tugas (ZIP)</span>
                    </a>
                @endif
            </div>
        </div>
        
        <div class="p-6">
            @if($validSubmissions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($validSubmissions as $sub)
                        @php
                            $files = is_array($sub->file_path) ? $sub->file_path : [$sub->file_path];
                            $fileCount = count($files);
                            $firstFile = $files[0] ?? null;
                            $firstExt = $firstFile ? strtolower(pathinfo($firstFile, PATHINFO_EXTENSION)) : '';
                            $firstIsImg = in_array($firstExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                            $firstIsPdf = ($firstExt === 'pdf');

                            $filesData = [];
                            foreach($files as $f) {
                                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                                $filesData[] = [
                                    'url' => Storage::url($f),
                                    'name' => basename($f),
                                    'ext' => $ext,
                                    'isImage' => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']),
                                    'isPdf' => ($ext === 'pdf'),
                                ];
                            }

                            $isLate = false;
                            $diffText = null;
                            if ($task->deadline && $sub->submitted_at) {
                                $submittedTime = \Carbon\Carbon::parse($sub->submitted_at);
                                $deadlineTime = \Carbon\Carbon::parse($task->deadline);
                                if ($submittedTime->gt($deadlineTime)) {
                                    $isLate = true;
                                    $diffText = $submittedTime->diffForHumans($deadlineTime, [
                                        'parts' => 1,
                                        'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE
                                    ]);
                                }
                            }
                        @endphp
                        <div class="bg-white rounded-xl border {{ $isLate ? 'border-amber-200' : 'border-slate-200' }} shadow-sm flex flex-col overflow-hidden hover:shadow-md transition-shadow">
                            
                            <!-- Card Header: Profile Info & Status Badge (Clean & Spacious) -->
                            <div class="p-4 border-b border-slate-100 {{ $isLate ? 'bg-amber-50/40' : 'bg-slate-50/50' }} flex items-start justify-between gap-2.5">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-full {{ $isLate ? 'bg-amber-100 text-amber-800' : 'bg-[#064e3b]/10 text-[#064e3b]' }} font-bold flex items-center justify-center shrink-0 text-sm">
                                        {{ substr($sub->trainee->name ?? 'P', 0, 1) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-slate-800 text-sm leading-snug truncate" title="{{ $sub->trainee->name ?? 'Peserta' }}">
                                            {{ $sub->trainee->name ?? 'Peserta' }}
                                        </h4>
                                        <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $sub->trainee->nik ?? '-' }}</p>
                                    </div>
                                </div>

                                <!-- Status Badge (Aligned top-right without overlapping) -->
                                <div class="shrink-0 pt-0.5">
                                    @if($task->deadline && $sub->submitted_at)
                                        @if($isLate)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 rounded-md text-[11px] font-semibold whitespace-nowrap">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-md text-[11px] font-semibold whitespace-nowrap">
                                                Tepat Waktu
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Card Body: Preview & Actions -->
                            <div class="p-4 flex flex-col gap-3 flex-1">
                                <div class="relative rounded-xl border border-slate-200 bg-slate-100 overflow-hidden group cursor-pointer h-36 flex items-center justify-center"
                                     @click="openViewer({
                                         traineeName: @js($sub->trainee->name ?? 'Peserta'),
                                         traineeNik: @js($sub->trainee->nik ?? '-'),
                                         isLate: @js($isLate),
                                         submittedAt: @js($sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-'),
                                         downloadUrl: @js(route('training-non-reguler.tasks.submissions.download', ['task' => $task->id, 'submission' => $sub->id])),
                                         deleteAction: @js(route('training-non-reguler.tasks.submissions.destroy', ['task' => $task->id, 'submission' => $sub->id])),
                                         files: @js($filesData)
                                     })">
                                    
                                    @if($firstIsImg)
                                        <img src="{{ Storage::url($firstFile) }}" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @elseif($firstIsPdf)
                                        <div class="flex flex-col items-center justify-center gap-1 text-slate-500">
                                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-600">Dokumen PDF</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center justify-center gap-1 text-slate-500">
                                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-600">Dokumen {{ strtoupper($firstExt) }}</span>
                                        </div>
                                    @endif

                                    <!-- Counter Badge (e.g. +4 file lainnya) -->
                                    @if($fileCount > 1)
                                        <div class="absolute bottom-2.5 right-2.5 bg-black/75 backdrop-blur-sm text-white px-2 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 shadow">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>+{{ $fileCount - 1 }} File Lainnya</span>
                                        </div>
                                    @endif

                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 text-white text-xs font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Klik untuk Buka Viewer</span>
                                    </div>
                                </div>

                                <!-- Action Buttons: Lihat Tugas, Unduh, Hapus -->
                                <div class="flex items-center gap-2 pt-1">
                                    <button type="button" 
                                            @click="openViewer({
                                                traineeName: @js($sub->trainee->name ?? 'Peserta'),
                                                traineeNik: @js($sub->trainee->nik ?? '-'),
                                                isLate: @js($isLate),
                                                submittedAt: @js($sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-'),
                                                downloadUrl: @js(route('training-non-reguler.tasks.submissions.download', ['task' => $task->id, 'submission' => $sub->id])),
                                                deleteAction: @js(route('training-non-reguler.tasks.submissions.destroy', ['task' => $task->id, 'submission' => $sub->id])),
                                                files: @js($filesData)
                                            })" 
                                            class="flex-1 py-2 px-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#064e3b] text-xs font-bold border border-emerald-200 transition-colors flex items-center justify-center gap-1.5 shadow-sm"
                                            title="Periksa seluruh lampiran tugas">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Lihat ({{ $fileCount }})</span>
                                    </button>

                                    <!-- Quick Download Button -->
                                    <a href="{{ route('training-non-reguler.tasks.submissions.download', ['task' => $task->id, 'submission' => $sub->id]) }}" 
                                       class="py-2 px-3 rounded-lg bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 transition-colors flex items-center gap-1.5 shrink-0 shadow-sm" 
                                       title="{{ $fileCount > 1 ? 'Unduh Semua ' . $fileCount . ' File (ZIP)' : 'Unduh File' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>{{ $fileCount > 1 ? 'ZIP' : 'Unduh' }}</span>
                                    </a>

                                    <!-- Quick Delete Button -->
                                    <form action="{{ route('training-non-reguler.tasks.submissions.destroy', ['task' => $task->id, 'submission' => $sub->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus seluruh pengumpulan tugas dari {{ $sub->trainee->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-white hover:bg-red-50 text-slate-400 hover:text-red-600 border border-slate-200 hover:border-red-300 transition-colors flex items-center justify-center shrink-0 shadow-sm" title="Hapus Tugas Peserta">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Card Footer: Timestamp -->
                            <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 mt-auto text-xs flex items-center justify-between text-slate-400">
                                <span class="truncate">Dikirim: {{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-' }}</span>
                                @if($isLate)
                                    <span class="text-amber-800 font-semibold text-[10px] shrink-0">({{ $diffText ? "$diffText" : "Terlambat" }})</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 px-4 border border-dashed border-slate-300 rounded-xl bg-slate-50">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <h3 class="text-base font-semibold text-slate-700 mb-1">Belum Ada Pengumpulan</h3>
                    <p class="text-sm text-slate-500">Peserta belum mengirimkan file tugas mereka.</p>
                </div>
            @endif
        </div>

        <!-- ========================================================================= -->
        <!-- GOOGLE CLASSROOM STYLE VIEWER MODAL -->
        <!-- ========================================================================= -->
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-black/85 backdrop-blur-sm"
             @keydown.escape.window="closeViewer()"
             @keydown.left.window="prevFile()"
             @keydown.right.window="nextFile()"
             style="display: none;">
            
            <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-5xl h-[92vh] flex flex-col overflow-hidden shadow-2xl relative"
                 @click.away="closeViewer()">
                
                <!-- Modal Top Header -->
                <div class="px-5 py-3.5 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-sm sm:text-base text-white truncate" x-text="traineeName"></h3>
                                <template x-if="isLate">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">Terlambat</span>
                                </template>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                <span class="font-mono" x-text="traineeNik"></span>
                                <span>•</span>
                                <span x-text="'File ' + (currentIndex + 1) + ' dari ' + files.length"></span>
                                <span>•</span>
                                <span class="truncate" x-text="currentFile().name"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Download Current File -->
                        <a :href="currentFile().url" :download="currentFile().name" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white rounded-lg text-xs font-semibold transition" title="Unduh File yang Sedang Ditampilkan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span class="hidden sm:inline">Unduh File Ini</span>
                        </a>

                        <!-- Download All ZIP -->
                        <a :href="downloadUrl" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#064e3b] hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition shadow-sm" title="Unduh Seluruh File Tugas Peserta Ini (ZIP)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh Semua</span>
                        </a>

                        <!-- Delete Submission -->
                        <form :action="deleteAction" method="POST" onsubmit="return confirm('Yakin ingin menghapus seluruh pengumpulan tugas peserta ini?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 bg-slate-800 hover:bg-red-500/20 text-slate-400 hover:text-red-400 border border-slate-700 rounded-lg transition" title="Hapus Tugas Peserta">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>

                        <!-- Close Button -->
                        <button type="button" @click="closeViewer()" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg transition ml-1" title="Tutup (ESC)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Center Stage (Preview) -->
                <div class="flex-1 relative bg-black/60 flex items-center justify-center p-3 sm:p-6 overflow-hidden">
                    
                    <!-- Left Arrow Button -->
                    <button type="button" 
                            x-show="files.length > 1"
                            @click="prevFile()" 
                            :disabled="currentIndex === 0"
                            :class="currentIndex === 0 ? 'opacity-30 cursor-not-allowed' : 'opacity-80 hover:opacity-100 hover:scale-110'"
                            class="absolute left-3 sm:left-5 z-20 w-11 h-11 rounded-full bg-slate-800/80 border border-slate-700 text-white flex items-center justify-center transition shadow-lg backdrop-blur-sm"
                            title="File Sebelumnya (Panah Kiri)">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <!-- Preview Content -->
                    <div class="w-full h-full flex items-center justify-center">
                        <!-- Image Preview -->
                        <template x-if="currentFile().isImage">
                            <img :src="currentFile().url" :alt="currentFile().name" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl transition duration-200">
                        </template>

                        <!-- PDF Preview -->
                        <template x-if="currentFile().isPdf">
                            <div class="w-full h-full flex flex-col bg-white rounded-xl overflow-hidden shadow-2xl">
                                <div class="p-2 bg-slate-100 border-b border-slate-200 flex items-center justify-between text-xs text-slate-700">
                                    <span class="font-semibold" x-text="currentFile().name"></span>
                                    <a :href="currentFile().url" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 font-medium">
                                        <span>Buka di Tab Baru</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                                <iframe :src="currentFile().url" class="w-full flex-1" frameborder="0"></iframe>
                            </div>
                        </template>

                        <!-- Other File Types -->
                        <template x-if="!currentFile().isImage && !currentFile().isPdf">
                            <div class="bg-slate-800 border border-slate-700 p-8 rounded-2xl text-center max-w-md shadow-2xl">
                                <svg class="w-16 h-16 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <h4 class="text-white font-bold mb-1 break-all text-sm sm:text-base" x-text="currentFile().name"></h4>
                                <p class="text-xs text-slate-400 mb-4">Format dokumen tidak mendukung pratinjau langsung di peramban.</p>
                                <a :href="currentFile().url" :download="currentFile().name" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh File Dokumen</span>
                                </a>
                            </div>
                        </template>
                    </div>

                    <!-- Right Arrow Button -->
                    <button type="button" 
                            x-show="files.length > 1"
                            @click="nextFile()" 
                            :disabled="currentIndex === files.length - 1"
                            :class="currentIndex === files.length - 1 ? 'opacity-30 cursor-not-allowed' : 'opacity-80 hover:opacity-100 hover:scale-110'"
                            class="absolute right-3 sm:right-5 z-20 w-11 h-11 rounded-full bg-slate-800/80 border border-slate-700 text-white flex items-center justify-center transition shadow-lg backdrop-blur-sm"
                            title="File Selanjutnya (Panah Kanan)">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <!-- Modal Bottom Filmstrip (Thumbnail Carousel) -->
                <div x-show="files.length > 1" class="px-5 py-3 bg-slate-900 border-t border-slate-800 flex items-center justify-center gap-2.5 overflow-x-auto shrink-0">
                    <template x-for="(f, idx) in files" :key="idx">
                        <div @click="currentIndex = idx" 
                             :class="idx === currentIndex ? 'ring-2 ring-emerald-500 scale-105 border-transparent opacity-100' : 'opacity-50 hover:opacity-100 border-slate-700'"
                             class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg border-2 bg-slate-800 overflow-hidden cursor-pointer transition-all flex items-center justify-center shrink-0 relative"
                             :title="f.name">
                            <template x-if="f.isImage">
                                <img :src="f.url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="f.isPdf">
                                <span class="text-[10px] font-bold text-red-400 font-mono">PDF</span>
                            </template>
                            <template x-if="!f.isImage && !f.isPdf">
                                <span class="text-[10px] font-bold text-slate-300 font-mono" x-text="f.ext.toUpperCase()"></span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- Script for Viewer Component -->
    <script>
        function classroomTaskViewer() {
            return {
                isOpen: false,
                traineeName: '',
                traineeNik: '',
                isLate: false,
                submittedAt: '',
                downloadUrl: '',
                deleteAction: '',
                files: [],
                currentIndex: 0,

                openViewer(data) {
                    this.traineeName = data.traineeName;
                    this.traineeNik = data.traineeNik;
                    this.isLate = data.isLate;
                    this.submittedAt = data.submittedAt;
                    this.downloadUrl = data.downloadUrl;
                    this.deleteAction = data.deleteAction;
                    this.files = data.files || [];
                    this.currentIndex = 0;
                    this.isOpen = true;
                    document.body.classList.add('overflow-hidden');
                },

                closeViewer() {
                    this.isOpen = false;
                    document.body.classList.remove('overflow-hidden');
                },

                currentFile() {
                    return this.files[this.currentIndex] || { isImage: false, isPdf: false, url: '', name: '', ext: '' };
                },

                nextFile() {
                    if (this.currentIndex < this.files.length - 1) {
                        this.currentIndex++;
                    }
                },

                prevFile() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                    }
                }
            };
        }
    </script>
</x-admin-layout>
