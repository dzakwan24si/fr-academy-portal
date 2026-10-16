<x-admin-layout>
    <x-slot name="title">Detail Sesi Pelatihan</x-slot>

        @php
    $attendanceCounts = [];
    foreach ($dates as $date) {
        $attendanceCounts[$date] = $dailyClass->attendances->where('status', 'hadir')->where('attendance_date', $date)->count();
    }
@endphp
    <div x-data="{ showAddTask: false, selectedDay: '{{ count($dates) > 0 ? $dates[0] : '' }}', attendanceCounts: {{ json_encode($attendanceCounts) }} }">
        
    <!-- Premium Header Section -->
    <div class="relative bg-white rounded-3xl p-8 mb-8 shadow-sm border border-slate-200 overflow-hidden">
        <!-- Decorative Background Pattern -->
        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-full opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-48 h-48 bg-gradient-to-tr from-blue-50 to-blue-100 rounded-full opacity-50 blur-2xl"></div>

        <!-- TOP HEADER ROW: Title & Primary Actions -->
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md text-xs font-bold uppercase tracking-wide">
                        {{ $dailyClass->trainingProgram->name }}
                    </span>
                    @if($dailyClass->date)
                        @php
                            \Carbon\Carbon::setLocale('id');
                            $start = \Carbon\Carbon::parse($dailyClass->date);
                            $end = $dailyClass->end_date ? \Carbon\Carbon::parse($dailyClass->end_date) : $start;
                            if ($start->isSameDay($end)) {
                                $dateString = $start->translatedFormat('d F Y');
                            } elseif ($start->isSameMonth($end)) {
                                $dateString = $start->format('d') . ' - ' . $end->translatedFormat('d F Y');
                            } elseif ($start->isSameYear($end)) {
                                $dateString = $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M Y');
                            } else {
                                $dateString = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
                            }
                        @endphp
                        <span class="text-sm font-medium text-slate-600 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $dateString }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">{{ $dailyClass->title }}</h1>
            </div>

            <!-- ACTION BUTTONS ROW -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="document.getElementById('editClassModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium text-sm transition-all shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Detail Pelatihan
                </button>



                <!-- Import is Primary -->
                <button type="button" @click="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#28844b] text-white rounded-lg hover:bg-[#1d6438] font-medium text-sm transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Peserta
                </button>
                
                <!-- DROPDOWN FOR MORE ACTIONS -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 font-medium text-sm transition-all shadow-sm">
                        Lainnya
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 min-w-[240px] bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50 hidden" :class="{'hidden': !open}">
                        <!-- <form action="{{ route('training-non-reguler.daily-classes.notify', $dailyClass->id) }}" method="POST" class="w-full" onsubmit="return confirm('Kirim notifikasi jadwal ke seluruh peserta di kelas ini?');">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                Beri Notifikasi
                            </button>
                        </form> -->
                        
                        
                        <button type="button" @click="document.getElementById('addTaskModal').classList.remove('hidden'); open = false;" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tugas
                        </button>
                        @if($dailyClass->qr_code_token)
                            <a :href="`{{ route('training-non-reguler.daily-classes.print-qr', $dailyClass->id) }}?date=${selectedDay}`" target="_blank" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Cetak QR
                            </a>
                        @else
                            <form action="{{ route('training-non-reguler.daily-classes.generate-qr', $dailyClass->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Buat QR
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('training-non-reguler.daily-classes.toggle-evaluation', $dailyClass->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                <svg class="w-4 h-4 {{ $dailyClass->is_evaluation_open ? 'text-amber-500' : 'text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                {{ $dailyClass->is_evaluation_open ? 'Tutup Evaluasi' : 'Kirim Evaluasi' }}
                            </button>
                        </form>

                        <a href="{{ $dailyClass->material_url ?: 'https://e-learning.first-resources.com/' }}" target="_blank" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            Buka Modul/Materi di LMS
                        </a>

                        {{-- 
                        <a href="{{ route('training-non-reguler.daily-classes.export-training-record', $dailyClass->id) }}" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Data (Format Training Record)
                        </a>
                        --}}

                        @if($trainees->count() > 0)
                        <div class="h-px bg-slate-200 my-1"></div>
                        <form action="{{ route('training-non-reguler.daily-classes.trainees.destroy-all', $dailyClass->id) }}" method="POST" class="w-full" onsubmit="return confirm('PERINGATAN: Hapus semua peserta?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Kosongkan Kelas
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- METADATA CARD (The Details) -->
        <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm mb-8">
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Data Point 1 -->
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Instruktur</span>
                    <div class="flex items-center gap-2 text-slate-800 font-medium">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ $dailyClass->trainer_name ?? '-' }}
                    </div>
                </div>

                <!-- Data Point 2 -->
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Waktu Pelaksanaan</span>
                    <div class="flex items-center gap-2 text-slate-800 font-medium">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @if($dailyClass->start_time || $dailyClass->end_time)
                            {{ $dailyClass->start_time ? \Carbon\Carbon::parse($dailyClass->start_time)->format('H:i') : '?' }} - {{ $dailyClass->end_time ? \Carbon\Carbon::parse($dailyClass->end_time)->format('H:i') : 'Selesai' }} WIB
                        @else
                            -
                        @endif
                    </div>
                </div>

                <!-- Data Point 3 -->
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Provider</span>
                    <div class="flex items-center gap-2 text-slate-800 font-medium">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        {{ $dailyClass->provider ?? '-' }}
                    </div>
                </div>

                <!-- Data Point 4 -->
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Lokasi & Ruangan</span>
                    <div class="flex items-center gap-2 text-slate-800 font-medium">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        {{ $dailyClass->location ?: '-' }} 
                        @if($dailyClass->room)
                            <span class="text-slate-400 font-normal">({{ $dailyClass->room }})</span>
                        @endif
                        @if($dailyClass->region)
                            <span class="text-slate-400 font-normal">({{ $dailyClass->region }})</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Container -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <!-- Stat: Total Trainee -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-1">Total Trainee</p>
                <p class="text-2xl font-bold text-gray-800 leading-none">{{ $trainees->count() }}</p>
            </div>
        </div>

        <!-- Stat: Hadir -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-[#28844b] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-1" x-text="`Hadir (Absen QR)` + (selectedDay ? ` - ` + new Date(selectedDay).toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}) : '')">Hadir (Absen QR)</p>
                <p class="text-2xl font-bold text-gray-800 leading-none" x-text="attendanceCounts[selectedDay] || 0">{{ $dailyClass->attendances->where('status', 'hadir')->count() }}</p>
            </div>
        </div>

        <!-- Stat: Tugas -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-1">Tugas Masuk</p>
                @php
                    $totalTasksSubmitted = 0;
                    foreach($dailyClass->tasks as $task) {
                        $totalTasksSubmitted += $task->submissions->where('status', 'sudah')->count();
                    }
                @endphp
                <p class="text-2xl font-bold text-gray-800 leading-none">{{ $totalTasksSubmitted }}</p>
            </div>
        </div>
    </div>

    <!-- Daftar Tugas Section -->
    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-[#28844b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            Tugas Kelas Ini
        </h3>
        @if($dailyClass->tasks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($dailyClass->tasks as $task)
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col relative overflow-hidden group hover:border-[#28844b] transition-all cursor-pointer" @click="window.location.href = '{{ route('training-non-reguler.tasks.show', $task->id) }}'">
                <div class="absolute top-0 left-0 w-1 h-full bg-[#28844b]"></div>
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-bold text-gray-800 text-lg leading-tight pr-4 group-hover:text-[#28844b] transition-colors">{{ $task->title }}</h4>
                    <form action="{{ route('training-non-reguler.daily-classes.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini secara permanen?');" class="shrink-0 relative z-10" @click.stop>
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors bg-slate-50 hover:bg-red-50 p-1.5 rounded-lg border border-transparent hover:border-red-100" title="Hapus Tugas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                <p class="text-xs text-slate-500 flex items-center gap-1.5 mb-4 font-medium">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tenggat: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y, H:i') }}
                </p>
                <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Pengumpulan</span>
                    <span class="text-xs font-black text-[#28844b] bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 group-hover:bg-[#28844b] group-hover:text-white transition-colors">
                        {{ $task->submissions->where('status', 'sudah')->count() }} / {{ $trainees->count() }} sudah kirim
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-gray-50 rounded-xl p-8 border border-dashed border-gray-300 text-center">
            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <p class="text-gray-500 font-medium">Belum ada tugas untuk kelas ini.</p>
        </div>
        @endif
    </div>

            <!-- Evaluasi Section -->
    <div class="mt-8 mb-6" x-data="evaluationData()">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#28844b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Hasil Evaluasi Training
        </h3>
        
        @if($dailyClass->traineeEvaluations->count() > 0)
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between border-t-8 border-t-[#28844b]">
            <div class="mb-4 sm:mb-0">
                <h4 class="font-bold text-gray-800 text-2xl mb-1">{{ $dailyClass->traineeEvaluations->count() }} responses</h4>
                <p class="text-gray-500 text-sm">Menampilkan ringkasan, analisis per pertanyaan, dan respons individu.</p>
            </div>
            <div class="flex items-center gap-2">
                  <button @click="showEvalModal = true" class="bg-white border border-gray-300 hover:bg-gray-50 text-[#28844b] font-medium py-2 px-4 rounded shadow-sm transition-colors flex items-center gap-2">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                      View Responses
                  </button>
                  <form action="{{ route('training-non-reguler.daily-classes.clear-evaluation', $dailyClass->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Anda yakin ingin menghapus semua respons evaluasi untuk sesi ini? Data yang dihapus tidak dapat dikembalikan.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="bg-white border border-gray-300 hover:bg-red-50 text-red-600 font-medium py-2 px-3 rounded shadow-sm transition-colors flex items-center gap-2 group" title="Hapus Semua Evaluasi">
                          <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                  </form>
              </div>
        </div>
        
                @php
            $evaluations = $dailyClass->traineeEvaluations;
            $totalEvals = $evaluations->count();
            $summary = [];
            
            $questionTexts = [
                1 => "Manfaat materi training terhadap pekerjaan",
                2 => "Kelengkapan materi",
                3 => "Lewat training ini keahlian dan ilmu saya bertambah",
                4 => "Materi training ini dapat saya terapkan di pekerjaan saya",
                5 => "Penguasaan materi oleh instruktur / trainer",
                6 => "Metode penyampaian materi oleh instruktur / trainer",
                7 => "Kecepatan dalam pemberian materi training",
                8 => "Contoh-contoh yang diberikan oleh trainer",
                9 => "Trainer dalam menjawab pertanyaan dan bimbingan praktek",
                10 => "Fasilitas kelas (White board, flip chart, LCD, kenyamanan ruangan, dsb)",
                11 => "Pelayanan Mess & Akomodasi (Makan, penginapan, dsb)",
                12 => "Pelayanan Learning Centre selama kegiatan berlangsung"
            ];
            
            for($i = 1; $i <= 12; $i++) {
                $summary["q$i"] = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
            }
            
            $evalArray = [];
            foreach($evaluations as $index => $eval) {
                foreach($eval->answers as $qKey => $ans) {
                    if(isset($summary[$qKey][$ans])) {
                        $summary[$qKey][$ans]++;
                    }
                }
                $evalArray[] = [
                    'id' => $eval->id,
                    'name' => $eval->trainee->name ?? 'Anonim',
                    'date' => $eval->created_at->format('d M Y, H:i'),
                    'answers' => $eval->answers,
                    'explanation_de' => $eval->explanation_de,
                    'kesan_pesan' => $eval->kesan_pesan
                ];
            }
        @endphp
        
                        <!-- Modal Professional Dashboard Style -->
        <div x-show="showEvalModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm" style="display: none;">
            <div @click.outside="showEvalModal = false" class="bg-slate-50 w-full max-w-5xl h-[90vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden relative">
                
                <!-- Header -->
                <div class="px-4 sm:px-8 py-5 bg-white border-b border-slate-200 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="font-bold text-slate-800 text-xl">Laporan Evaluasi</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $dailyClass->title }}</p>
                    </div>
                    <button @click="showEvalModal = false" class="text-slate-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                                                <!-- Tabs -->
                <div class="flex border-b border-slate-200 bg-white px-4 sm:px-8 shrink-0 overflow-x-auto whitespace-nowrap custom-scrollbar">
                    <button @click="tab = 'summary'" :class="tab === 'summary' ? 'text-[#28844b] border-b-2 border-[#28844b] font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium'" class="px-6 py-4 text-sm transition-colors focus:outline-none">
                        Ringkasan (Summary)
                    </button>
                    <button @click="tab = 'individual'" :class="tab === 'individual' ? 'text-[#28844b] border-b-2 border-[#28844b] font-semibold' : 'text-slate-500 hover:text-slate-700 font-medium'" class="px-6 py-4 text-sm transition-colors focus:outline-none">
                        Hasil Per Individu
                    </button>
                </div>

                <!-- Content Area -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-8 bg-slate-50" style="scrollbar-width: thin;">
                    
                    <!-- TAB 1: SUMMARY -->
                    <div x-show="tab === 'summary'">
                        <!-- Overview Card -->
                        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-8 flex items-center justify-between shadow-sm">
                            <div>
                                <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Total Partisipasi</p>
                                <h2 class="text-3xl font-black text-slate-800 mt-1">{{ $totalEvals }} <span class="text-lg font-medium text-slate-500">Trainee</span></h2>
                            </div>
                            <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center text-[#28844b]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                        
                        @php
                            $summaryGroups = [
                                'A. Isi Materi Pelatihan' => [1, 2, 3, 4],
                                'B. Instruktur/Trainer' => [5, 6, 7, 8, 9],
                                'C. Fasilitas dan Pelayanan' => [10, 11, 12]
                            ];
                        @endphp
                        
                        <!-- Questions Grid -->
                        <div class="space-y-10">
                            @foreach($summaryGroups as $groupTitle => $qNumbers)
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 mb-4 pb-2 border-b-2 border-slate-100 flex items-center gap-2">
                                    <div class="w-2 h-6 bg-[#28844b] rounded-full"></div>
                                    {{ $groupTitle }}
                                </h3>
                                <div class="space-y-6">
                                    @foreach($qNumbers as $num)
                                    @php
                                        $qKey = 'q' . $num;
                                        $counts = $summary[$qKey];
                                    @endphp
                                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                                        <div class="flex gap-3 mb-6">
                                            <span class="text-slate-400 font-bold text-sm">Q{{ $num }}.</span>
                                            <h4 class="text-sm font-semibold text-slate-800">{{ $questionTexts[$num] }}</h4>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach(['A', 'B', 'C', 'D', 'E'] as $grade)
                                            @php
                                                $val = $counts[$grade];
                                                $pct = $totalEvals > 0 ? round(($val / $totalEvals) * 100) : 0;
                                            @endphp
                                            <div class="flex items-center text-sm">
                                                <div class="w-6 font-bold text-slate-500">{{ $grade }}</div>
                                                <div class="flex-1 bg-slate-100 rounded-md h-4 mx-3 overflow-hidden">
                                                    <div class="bg-[#28844b] h-full rounded-md transition-all duration-500" style="width: {{ $pct }}%"></div>
                                                </div>
                                                <div class="w-16 text-right">
                                                    <span class="font-bold text-slate-700">{{ $val }}</span> 
                                                    <span class="text-xs text-slate-500">({{ $pct }}%)</span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB 2: INDIVIDUAL -->
                    <div x-show="tab === 'individual'" style="display: none;">
                        
                                                <!-- Control Bar Sticky Wrapper -->
                        <div class="sticky -top-4 sm:-top-8 -mx-4 sm:-mx-8 px-4 sm:px-8 pt-4 sm:pt-8 pb-6 bg-slate-50 z-20">
                            <!-- Control Bar Card -->
                            <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                                
                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:block">Peserta:</span>
                                    <div x-data="{ openSelect: false }" class="relative w-full sm:w-[320px]">
                                        <!-- Trigger Button -->
                                        <button @click="openSelect = !openSelect" @click.away="openSelect = false" class="w-full bg-slate-50 border border-slate-200 hover:border-[#28844b] rounded-lg px-4 py-2.5 text-left text-sm font-semibold text-slate-700 shadow-sm flex items-center justify-between transition-colors focus:outline-none focus:ring-2 focus:ring-[#28844b]/20">
                                            <span x-text="(currentIndex + 1) + '. ' + (currentEval.name || 'Pilih Peserta')" class="truncate"></span>
                                            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" :class="{'rotate-180': openSelect}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transition: transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        
                                        <!-- Dropdown Menu -->
                                        <div x-show="openSelect" style="display: none;" class="absolute z-[200] w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl max-h-64 overflow-y-auto custom-scrollbar" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                            <template x-for="(ev, idx) in evaluations">
                                                <div @click="currentIndex = idx; openSelect = false" 
                                                     class="px-4 py-3 text-sm cursor-pointer transition-colors border-b border-slate-50 last:border-0 flex items-center gap-2"
                                                     :class="currentIndex === idx ? 'bg-emerald-50/50 text-[#28844b]' : 'text-slate-600 hover:bg-slate-50'">
                                                    <svg x-show="currentIndex === idx" class="w-4 h-4 text-[#28844b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    <div x-show="currentIndex !== idx" class="w-4 h-4 shrink-0"></div>
                                                    <span x-text="(idx + 1) + '. ' + ev.name" :class="currentIndex === idx ? 'font-bold' : 'font-medium'" class="truncate"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-center gap-2 text-slate-600 border border-slate-100 p-1 rounded-lg bg-slate-50/50 w-full sm:w-auto">
                                    <button @click="if(currentIndex > 0) currentIndex--" class="p-1.5 hover:bg-white hover:shadow-sm hover:text-[#28844b] rounded-md disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:shadow-none disabled:hover:text-slate-600 transition-all" :disabled="currentIndex === 0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <span class="text-sm font-bold text-slate-700 w-16 text-center"><span x-text="currentIndex + 1"></span><span class="text-slate-300 font-normal mx-1">/</span><span x-text="evaluations.length"></span></span>
                                    <button @click="if(currentIndex < evaluations.length - 1) currentIndex++" class="p-1.5 hover:bg-white hover:shadow-sm hover:text-[#28844b] rounded-md disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:shadow-none disabled:hover:text-slate-600 transition-all" :disabled="currentIndex === 0 && evaluations.length <= 1 || currentIndex === evaluations.length - 1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                        
                        <!-- Answers List -->
                        <div class="space-y-6" x-show="evaluations.length > 0">
                            
                            <div class="flex items-center justify-end text-xs text-slate-500 font-medium mb-4">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Dikirim pada: <span x-text="currentEval.date" class="ml-1 text-slate-700"></span>
                            </div>

                            <template x-for="(qNumbers, groupTitle) in questionGroups">
                                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                                        <h3 class="text-sm font-bold text-[#28844b] uppercase tracking-wide flex items-center gap-2">
                                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <span x-text="groupTitle"></span>
                                        </h3>
                                    </div>
                                    <div class="divide-y divide-slate-100">
                                        <!-- Iterasi Pertanyaan per Grup -->
                                        <template x-for="i in qNumbers">
                                            <div class="p-6 flex flex-col xl:flex-row xl:items-center justify-between gap-4 hover:bg-slate-50 transition-colors" x-show="currentEval.answers">
                                                <div class="flex-1">
                                                    <div class="flex gap-2">
                                                        <span class="text-slate-400 font-bold text-sm" x-text="i + '.'"></span>
                                                        <p class="text-sm font-semibold text-slate-700" x-text="questions[i]"></p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Compact Horizontal Options -->
                                                <div class="flex gap-2 pl-6 xl:pl-0">
                                                    <template x-for="opt in ['A', 'B', 'C', 'D', 'E']">
                                                        <div :class="currentEval.answers['q' + i] === opt ? 'bg-[#28844b] text-white border-[#28844b] font-bold shadow-sm' : 'bg-white text-slate-500 border-slate-200 font-medium'" 
                                                             class="w-10 h-10 rounded-lg border flex items-center justify-center text-sm transition-all">
                                                            <span x-text="opt"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Additional Feedback -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="bg-white rounded-xl border border-slate-200 p-6">
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Kesan & Pesan</h4>
                                    <p class="text-sm text-slate-800" x-text="currentEval.kesan_pesan || '-'"></p>
                                </div>
                                
                                <div class="bg-white rounded-xl border border-slate-200 p-6" x-show="currentEval.explanation_de">
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Penjelasan (Jika memilih D/E)</h4>
                                    <p class="text-sm text-slate-800" x-text="currentEval.explanation_de || '-'"></p>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <script>
            function evaluationData() {
                return {
                    showEvalModal: false,
                    tab: 'summary', // summary, individual
                    evaluations: @json($evalArray),
                    questions: @json($questionTexts),
                    questionGroups: {
                        'A. Isi Materi Pelatihan': [1, 2, 3, 4],
                        'B. Instruktur / Trainer': [5, 6, 7, 8, 9],
                        'C. Fasilitas & Pelayanan': [10, 11, 12]
                    },
                    currentIndex: 0,
                    get currentEval() {
                        return this.evaluations[this.currentIndex] || {};
                    }
                }
            }
        </script>
        
        @else
        <div class="bg-gray-50 rounded-xl p-8 border border-dashed border-gray-300 text-center">
            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-gray-500 font-medium">Belum ada trainee yang mengisi evaluasi untuk sesi ini.</p>
        </div>
        @endif
    </div>


            <!-- Tabs for Days -->
        @if(count($dates) > 1)
        <div class="flex gap-2 mb-4 overflow-x-auto whitespace-nowrap custom-scrollbar">
            @foreach($dates as $index => $d)
            <button type="button" @click="selectedDay = '{{ $d }}'" 
                    :class="selectedDay === '{{ $d }}' ? 'bg-[#28844b] text-white border-[#28844b]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-lg font-bold text-sm border shadow-sm transition-all focus:outline-none">
                Hari {{ $index + 1 }} ({{ \Carbon\Carbon::parse($d)->translatedFormat('d M Y') }})
            </button>
            @endforeach
        </div>
        @endif
    
        <!-- Data Table Container -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Daftar Peserta & Absensi
            </h2>
            <div class="flex items-center gap-2">
                <button type="button" @click="document.getElementById('addManualModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-xs transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Peserta
                </button>
                <a :href="`{{ route('training-non-reguler.daily-classes.print-attendance', $dailyClass->id) }}?date=${selectedDay}`" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium text-xs transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Absensi
                </a>
            </div>
        </div>
        
        <div class="w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm">
                                  <thead class="bg-gray-50 border-b border-gray-200">
                      <tr>
                          <th class="px-6 py-3 font-semibold text-gray-600 w-16">No</th>
                          <th class="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Employee ID</th>
                          <th class="px-6 py-3 font-semibold text-gray-600 min-w-[200px]">Employee Name</th>
                          <th class="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Talent To</th>
                          <th class="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Unit</th>
                          <th class="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap border-r border-gray-200">Region</th>
                          
                                                      @foreach($dates as $d)
                            <th class="px-6 py-3 font-semibold text-gray-600 text-center min-w-[120px]" x-show="selectedDay === '{{ $d }}'" x-cloak>Absensi</th>
                            @endforeach
                          <th class="px-6 py-3 font-semibold text-gray-600 text-center w-20">Aksi</th>
                      </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                      @foreach($trainees as $index => $trainee)
                      <tr class="hover:bg-gray-50">
                          <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                          <td class="px-6 py-4 font-mono text-gray-600">{{ $trainee->nik }}</td>
                          <td class="px-6 py-4">
                              <div class="flex flex-col">
                                  <span class="font-bold text-gray-800">{{ $trainee->name }}</span>
                              </div>
                          </td>
                          <td class="px-6 py-4 text-gray-600">{{ $trainee->talent_to ?? '-' }}</td>
                          <td class="px-6 py-4 text-gray-600">{{ $trainee->unit ?? '-' }}</td>
                          <td class="px-6 py-4 text-gray-600 border-r border-gray-100">{{ $trainee->origin ?? '-' }}</td>
                          
                          @foreach($dates as $d)
                          @php
                              $attendance = $dailyClass->attendances->where('trainee_id', $trainee->id)->where('attendance_date', $d)->first();
                              $statusRaw = $attendance ? strtolower($attendance->status) : 'belum';
                              $time = $attendance && $attendance->scanned_at ? \Carbon\Carbon::parse($attendance->scanned_at)->format('H:i') : '-';
                              
                              if ($statusRaw === 'belum' && \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($d)->startOfDay())) {
                                  $statusRaw = 'alpha';
                              }
                              $status = ucfirst($statusRaw);
                          @endphp
                          <td class="px-4 py-4 text-center border-r border-gray-100 relative group" x-show="selectedDay === '{{ $d }}'" x-cloak>
                              <div class="flex flex-col items-center gap-1.5">
                                  @if($status == 'Hadir')
                                      <button type="button" onclick="document.getElementById('editStatusModal_{{ $trainee->id }}_{{ $d }}').classList.remove('hidden')" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700 shadow-sm border border-green-200 hover:opacity-80 transition-opacity tooltip" title="Klik untuk edit">
                                          Hadir
                                      </button>
                                      <span class="text-[10px] text-gray-500 bg-gray-100 px-1.5 rounded">{{ $time }}</span>
                                  @else
                                      <button type="button" onclick="document.getElementById('editStatusModal_{{ $trainee->id }}_{{ $d }}').classList.remove('hidden')" class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-700 shadow-sm border border-amber-200 hover:opacity-80 transition-opacity tooltip" title="Klik untuk edit">
                                          {{ $status }}
                                      </button>
                                  @endif
                              </div>
                              
                              <!-- Modal Edit Status Absensi untuk {{ $trainee->name }} Hari {{ $d }} -->
                              <div id="editStatusModal_{{ $trainee->id }}_{{ $d }}" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
                                  <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 opacity-100">
                                      <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                                          <div>
                                              <h3 class="text-lg font-black text-slate-800">Edit Absensi</h3>
                                              <p class="text-sm font-medium text-slate-500 mt-1">{{ $trainee->name }} ({{ \Carbon\Carbon::parse($d)->format('d M') }})</p>
                                          </div>
                                          <button onclick="document.getElementById('editStatusModal_{{ $trainee->id }}_{{ $d }}').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors p-2 bg-white rounded-full shadow-sm">
                                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                          </button>
                                      </div>
                                      
                                      <form action="{{ route('training-non-reguler.daily-classes.attendance.update', $dailyClass->id) }}" method="POST">
                                          @csrf
                                          <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">
                                          <input type="hidden" name="attendance_date" value="{{ $d }}">
                                          <div class="p-6 space-y-5">
                                              <div>
                                                  <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Status Kehadiran <span class="text-red-500">*</span></label>
                                                  <div class="relative">
                                                      <select name="status" class="w-full pl-4 pr-10 py-3 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium text-slate-700 transition-colors appearance-none cursor-pointer" required>
                                                          <option value="belum" {{ strtolower($statusRaw) == 'belum' ? 'selected' : '' }}>Belum Absen</option>
                                                          <option value="hadir" {{ strtolower($statusRaw) == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                          <option value="sakit" {{ strtolower($statusRaw) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                          <option value="izin" {{ strtolower($statusRaw) == 'izin' ? 'selected' : '' }}>Izin</option>
                                                          <option value="alpha" {{ strtolower($statusRaw) == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                                      </select>
                                                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-3xl">
                                              <button type="button" onclick="document.getElementById('editStatusModal_{{ $trainee->id }}_{{ $d }}').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-200 hover:text-slate-800 transition-colors">Batal</button>
                                              <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-white font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Perubahan</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                          </td>
                          @endforeach
                          <td class="px-6 py-4 text-center">
                              <form action="{{ route('training-non-reguler.daily-classes.trainees.destroy', ['daily_class' => $dailyClass->id, 'trainee' => $trainee->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus {{ $trainee->name }} dari sesi ini? Data absensi untuk sesi ini juga akan dihapus.');">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="p-2 text-slate-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors tooltip" title="Hapus Peserta">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                  </button>
                              </form>
                          </td>
                      </tr>
                      @endforeach
                  </tbody>
            </table>
        </div>
    </div>
        <!-- Add Manual Modal -->
    <div id="addManualModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 opacity-100">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                <h3 class="text-lg font-black text-slate-800">Tambah Peserta Manual</h3>
                <button type="button" onclick="document.getElementById('addManualModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('training-non-reguler.daily-classes.add-manual', $dailyClass->id) }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIK <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" required placeholder="Contoh: 2023030337">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" required placeholder="Contoh: Ahmad Pajar">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jabatan (Talent To) <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <input type="text" name="talent_to" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" placeholder="Contoh: Askep">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Unit/PT <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <input type="text" name="unit" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" placeholder="Contoh: ANJAS">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Region <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <input type="text" name="region" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" placeholder="Contoh: Riau">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addManualModal').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 active:scale-95 transition-all shadow-md">Simpan Peserta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 opacity-100">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                <h3 class="text-lg font-black text-slate-800">Tambah Tugas</h3>
                <button type="button" onclick="document.getElementById('addTaskModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('training-non-reguler.daily-classes.tasks.store', $dailyClass->id) }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Tugas <span class="text-red-500">*</span></label>
                        <input type="text" name="title" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required placeholder="Contoh: Resume Materi, Unggah Foto Kegiatan, dll">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tenggat Waktu (Deadline) <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="deadline" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addTaskModal').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-white font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>

        <!-- Edit Class Modal -->
    <div id="editClassModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 opacity-100 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50 shrink-0">
                <h3 class="text-lg font-black text-slate-800">Edit Detail Pelatihan</h3>
                <button type="button" onclick="document.getElementById('editClassModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('training-non-reguler.daily-classes.update', $dailyClass) }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                @method('PUT')
                <input type="hidden" name="training_program_id" value="{{ $dailyClass->training_program_id }}">
                <input type="hidden" name="title" value="{{ $dailyClass->title }}">
                <input type="hidden" name="material_url" value="{{ $dailyClass->material_url }}">
                <input type="hidden" name="mess_url" value="{{ $dailyClass->mess_url }}">
                <div class="p-6 overflow-y-auto space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">TGL MULAI <span class="text-red-500">*</span></label>
                            <input type="date" name="date" required value="{{ $dailyClass->date ? \Carbon\Carbon::parse($dailyClass->date)->format('Y-m-d') : '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">TGL SELESAI <span class="text-slate-400 font-semibold">(OPSIONAL)</span></label>
                            <input type="date" name="end_date" value="{{ $dailyClass->end_date ? \Carbon\Carbon::parse($dailyClass->end_date)->format('Y-m-d') : '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-medium">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">WAKTU MULAI <span class="text-slate-400 font-semibold">(OPSIONAL)</span></label>
                            <input type="time" name="start_time" value="{{ $dailyClass->start_time ? \Carbon\Carbon::parse($dailyClass->start_time)->format('H:i') : '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">WAKTU SELESAI <span class="text-slate-400 font-semibold">(OPSIONAL)</span></label>
                            <input type="time" name="end_time" value="{{ $dailyClass->end_time ? \Carbon\Carbon::parse($dailyClass->end_time)->format('H:i') : '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-medium">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">NAMA INSTRUKTUR <span class="text-red-500">*</span></label>
                            <input type="text" name="trainer_name" required value="{{ $dailyClass->trainer_name }}" placeholder="Cth: Budi Santoso" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm placeholder:text-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">LOKASI TRAINING <span class="text-slate-400 font-semibold">(OPSIONAL)</span></label>
                            <input type="text" name="location" value="{{ $dailyClass->location }}" placeholder="Cth: FRLC / Kandir / Online" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm placeholder:text-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">RUANGAN <span class="text-slate-400 font-semibold">(OPSIONAL)</span></label>
                            <input type="text" name="room" value="{{ $dailyClass->room }}" placeholder="Cth: R.Planters" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm placeholder:text-slate-400 font-medium">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">PROVIDER <span class="text-slate-400 font-semibold">(OPSIONAL)</span></label>
                            <input type="text" name="provider" value="{{ $dailyClass->provider }}" placeholder="Cth: Internal" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm placeholder:text-slate-400 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 tracking-wide uppercase mb-2">REGION <span class="text-slate-400 font-semibold">(OPSIONAL)</span></label>
                            <input type="text" name="region" value="{{ $dailyClass->region }}" placeholder="Cth: Region Riau" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm placeholder:text-slate-400 font-medium">
                        </div>
                    </div>
                </div>
                
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 shrink-0">
                    <button type="button" onclick="document.getElementById('editClassModal').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 active:scale-95 transition-all shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Import Excel Modal -->
    <div id="importModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all scale-100 opacity-100">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50 shrink-0">
                <h3 class="text-lg font-black text-slate-800">Import Peserta (Excel)</h3>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('training-non-reguler.daily-classes.import', $dailyClass->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-6 overflow-y-auto space-y-4 flex-1">
                @csrf
                
                @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <!-- Re-open modal if there are errors -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('importModal').classList.remove('hidden');
                    });
                </script>
                @endif
                
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 mb-4">
                        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs text-blue-700 font-medium leading-relaxed">
                            Materi ini adalah wadah. <strong>Silakan tentukan Tanggal Pelatihan dan Trainer untuk batch ini</strong>. Upload file Excel (.xlsx, .csv). Pastikan memiliki baris header dengan nama: No, Employee ID, Employee Name, Talent To, Unit, Region.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tgl Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="date" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tgl Selesai <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="date" name="end_date" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Waktu Mulai <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="time" name="start_time" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Waktu Selesai <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="time" name="end_time" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Instruktur <span class="text-red-500">*</span></label>
                            <input type="text" name="trainer_name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" required placeholder="Cth: Budi Santoso">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lokasi Training <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="location" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" placeholder="Cth: FRLC / Kandir / Online">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ruangan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="room" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" placeholder="Cth: R.Planters">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Provider <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="provider" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" placeholder="Cth: Internal">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Region <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="region" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" placeholder="Cth: Region Riau">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih File Excel <span class="text-red-500">*</span></label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-medium transition-all" required>
                    </div>
                </div>
                
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 shrink-0">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 active:scale-95 transition-all shadow-md">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>

    </div>
    
    <script>
        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId);
            
            // Create a temporary textarea to hold the text
            var tempInput = document.createElement("textarea");
            tempInput.value = copyText.value;
            document.body.appendChild(tempInput);
            
            // Select and copy
            tempInput.select();
            document.execCommand("copy");
            
            // Remove the temporary element
            document.body.removeChild(tempInput);
            
            // Show feedback
            alert("Link Portal Peserta disalin ke clipboard!");
        }
    </script>

    <!-- Blast Notification Modal -->
    <div id="blastModal" class="fixed inset-0 z-[100] hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</div>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2" id="modal-title">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Blast Pengumuman
                    </h3>
                    <button type="button" @click="document.getElementById('blastModal').classList.add('hidden')" class="text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="{{ route('admin.notifications.blast') }}" method="POST">
                    @csrf
                    <div class="px-6 pt-5 pb-6">
                        <p class="text-sm text-slate-500 mb-5">Kirimkan notifikasi langsung ke layar HP seluruh peserta. Pastikan peserta telah mengizinkan notifikasi di browser mereka.</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Judul Pengumuman</label>
                                <input type="text" name="title" required placeholder="Contoh: Tugas Baru!" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Isi Pesan</label>
                                <textarea name="body" required rows="3" placeholder="Contoh: Sesi materi hari ini digeser menjadi jam 10:00 WIB..." class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="document.getElementById('blastModal').classList.add('hidden')" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl hover:bg-slate-50 font-bold text-sm transition-all shadow-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Kirim Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



</x-admin-layout>































