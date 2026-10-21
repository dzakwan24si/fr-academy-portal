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
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Pengumpulan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Lihat dan unduh file tugas yang dikumpulkan oleh peserta.</p>
            </div>
            @php
                $submissionsCount = $task->submissions->where('status', 'sudah')->count();
            @endphp
            <div class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg text-sm font-medium flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $submissionsCount }} Peserta Telah Mengumpulkan
            </div>
        </div>
        
        <div class="p-6">
            @php
                $submissions = $task->submissions->filter(function($sub) {
                    return $sub->status == 'sudah' && !empty($sub->file_path);
                });
            @endphp
            
            @if($submissions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($submissions as $sub)
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col h-full overflow-hidden">
                            
                            @php
                                $files = is_array($sub->file_path) ? $sub->file_path : [$sub->file_path];
                                $firstFile = $files[0] ?? null;
                            @endphp
                            <!-- Card Header -->
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-[#064e3b]/10 text-[#064e3b] font-bold flex items-center justify-center shrink-0">
                                        {{ substr($sub->trainee->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-800 text-sm truncate">{{ $sub->trainee->name }}</h4>
                                        <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $sub->trainee->nik }}</p>
                                    </div>
                                </div>
                                @if($firstFile)
                                    <a href="{{ Storage::url($firstFile) }}" download="{{ basename($firstFile) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white border border-slate-200 hover:border-[#064e3b] hover:bg-emerald-50 text-slate-600 hover:text-[#064e3b] rounded-lg text-xs font-semibold shadow-sm transition-all shrink-0" title="Unduh File">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>Unduh</span>
                                    </a>
                                @endif
                            </div>
                            
                            <!-- Card Body (Files) -->
                            <div class="p-4 flex flex-col gap-2 flex-1">
                                <p class="text-xs font-semibold text-slate-400 mb-1">FILE TERLAMPIR</p>
                                @foreach($files as $file)
                                    @php 
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']))
                                        <div class="mb-2 relative rounded border border-slate-200 overflow-hidden group">
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="block">
                                                <img src="{{ Storage::url($file) }}" alt="Preview" class="w-full h-24 object-cover hover:scale-105 transition-transform duration-300">
                                            </a>
                                            <a href="{{ Storage::url($file) }}" download="{{ basename($file) }}" title="Unduh Gambar" class="absolute top-2 right-2 p-1.5 bg-black/60 hover:bg-black/80 text-white rounded-md shadow backdrop-blur-sm transition-all opacity-80 hover:opacity-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                        </div>
                                    @elseif($ext === 'pdf')
                                        <div class="mb-2 relative rounded-lg border border-slate-200 group bg-slate-50" style="overflow: hidden; height: 160px;">
                                            <!-- CSS Hack to hide scrollbar -->
                                            <div style="width: calc(100% + 20px); height: calc(100% + 56px); margin-top: -56px;">
                                                <iframe src="{{ Storage::url($file) }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH" class="w-full h-full pointer-events-none" frameborder="0"></iframe>
                                            </div>
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="absolute inset-0 z-10 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span class="text-white text-xs font-semibold px-3 py-1.5 bg-black/70 rounded-md">Buka PDF Sepenuhnya</span>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <a href="{{ Storage::url($file) }}" target="_blank" class="flex-1 min-w-0 flex items-center gap-3 p-2.5 bg-white border border-slate-200 hover:border-[#064e3b] hover:bg-emerald-50 rounded-lg text-sm text-slate-600 hover:text-[#064e3b] transition-colors group/file">
                                            <svg class="w-5 h-5 text-slate-400 group-hover/file:text-[#064e3b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            <span class="truncate" title="{{ basename($file) }}">{{ basename($file) }}</span>
                                        </a>
                                        <a href="{{ Storage::url($file) }}" download="{{ basename($file) }}" title="Unduh File" class="p-2.5 bg-white border border-slate-200 hover:border-[#064e3b] hover:bg-emerald-50 text-slate-500 hover:text-[#064e3b] rounded-lg transition-colors flex items-center justify-center shrink-0 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 mt-auto text-xs text-slate-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Dikirim: {{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '-' }}
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
    </div>
</x-admin-layout>




