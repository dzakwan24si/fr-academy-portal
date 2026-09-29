<x-admin-layout>
    <x-slot name="title">Detail Sesi Pelatihan</x-slot>

    <div x-data="{ showAddTask: false }">
    <!-- Clean Header Section -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-bold border border-gray-200 uppercase tracking-wide">
                    {{ $dailyClass->trainingProgram->name }}
                </span>
                <span class="text-gray-500 text-sm font-medium">
                    {{ $dailyClass->date->format('d M Y') }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $dailyClass->title }}</h1>
            <p class="text-gray-500 text-sm mt-1">Trainer: <span class="font-medium text-gray-700">{{ $dailyClass->trainer_name }}</span></p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex gap-2">
            @if($dailyClass->qr_code_token)
            <a href="{{ route('training-reguler.daily-classes.print-qr', $dailyClass->id) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-100 font-medium text-sm transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak QR
            </a>
            @else
            <form action="{{ route('training-reguler.daily-classes.generate-qr', $dailyClass->id) }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Generate QR
                </button>
            </form>
            @endif
            
            <button type="button" @click="showAddTask = true" class="flex items-center gap-2 px-4 py-2 bg-[#008f5d] text-white rounded-lg hover:bg-[#00734b] font-medium text-sm transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Tugas
            </button>

            <form action="{{ route('training-reguler.daily-classes.toggle-evaluation', $dailyClass->id) }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 {{ $dailyClass->is_evaluation_open ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg font-medium text-sm transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    {{ $dailyClass->is_evaluation_open ? 'Tutup Evaluasi' : 'Kirim Evaluasi' }}
                </button>
            </form>
            
            <a href="{{ route('training-reguler.print.attendance') }}" target="_blank" class="hidden items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Absen
            </a>
            <a href="{{ route('training-reguler.daily-classes.print-tasks', $dailyClass->id) }}" target="_blank" class="hidden items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Tugas
            </a>
        </div>
    </div>

    <!-- Quick Stats Container -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <!-- Stat: Total Trainee -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-1">Total Trainee</p>
                <p class="text-2xl font-bold text-gray-800 leading-none">{{ $trainees->count() }}</p>
            </div>
        </div>

        <!-- Stat: Hadir -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-1">Hadir (Absen QR)</p>
                <p class="text-2xl font-bold text-gray-800 leading-none">{{ $dailyClass->attendances->where('status', 'hadir')->count() }}</p>
            </div>
        </div>

        <!-- Stat: Tugas -->
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
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
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col relative overflow-hidden group hover:border-[#28844b] transition-all cursor-pointer" @click="window.location.href = '{{ route('training-reguler.tasks.show', $task->id) }}'">
                <div class="absolute top-0 left-0 w-1 h-full bg-[#28844b]"></div>
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-bold text-gray-800 text-lg leading-tight pr-4 group-hover:text-[#28844b] transition-colors">{{ $task->title }}</h4>
                    <form action="{{ route('training-reguler.daily-classes.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini secara permanen?');" class="shrink-0 relative z-10" @click.stop>
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
    <div class="mt-8 mb-6">
        <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            Hasil Evaluasi Training
        </h3>
        @if($dailyClass->traineeEvaluations->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($dailyClass->traineeEvaluations as $eval)
            <div class="bg-white rounded-xl p-5 border-l-4 border-l-amber-500 border-y border-r border-gray-200 shadow-sm flex flex-col relative overflow-hidden">
                <div class="flex justify-between border-b border-gray-100 pb-2 mb-3">
                    <h4 class="font-bold text-gray-800">{{ $eval->trainee->name ?? 'Anonim' }}</h4>
                    <span class="text-xs text-gray-500">{{ $eval->created_at->format('d M Y, H:i') }}</span>
                </div>
                
                <div class="mb-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach($eval->answers as $q => $ans)
                        <span class="px-2 py-1 bg-gray-50 text-[10px] rounded border {{ in_array($ans, ['D','E']) ? 'border-red-400 text-red-700' : 'border-gray-200 text-gray-600' }}">
                            {{ strtoupper($q) }}: <strong class="font-bold text-gray-900 {{ in_array($ans, ['D','E']) ? 'text-red-700' : '' }}">{{ $ans }}</strong>
                        </span>
                        @endforeach
                    </div>
                </div>

                @if($eval->explanation_de)
                <div class="mb-3 p-2 bg-red-50 text-red-800 rounded text-xs">
                    <strong class="block mb-1">Penjelasan (Nilai D/E):</strong>
                    {{ $eval->explanation_de }}
                </div>
                @endif

                <div class="p-3 bg-amber-50 text-amber-800 rounded text-xs italic">
                    <strong class="block not-italic mb-1 text-amber-900">Kesan & Pesan:</strong>
                    "{{ $eval->kesan_pesan }}"
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-gray-50 rounded-xl p-8 border border-dashed border-gray-300 text-center">
            <p class="text-gray-500 font-medium">Belum ada trainee yang mengisi evaluasi untuk sesi ini.</p>
        </div>
        @endif
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50">
            <div>
                <h3 class="font-bold text-gray-800">Daftar Trainee & Progres Sesi</h3>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    Sinkronisasi Portal Aktif
                </span>
            </div>
        </div>
        
        <!-- Table Body -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-gray-600">Nama Trainee</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-center">Kehadiran</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-center">Waktu Scan</th>
                        <th class="px-6 py-3 font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($trainees as $trainee)
                    @php
                        $attendance = $dailyClass->attendances->where('trainee_id', $trainee->id)->first();
                        $statusRaw = $attendance ? strtolower($attendance->status) : 'belum';
                        $time = $attendance && $attendance->scanned_at ? \Carbon\Carbon::parse($attendance->scanned_at)->format('H:i') : '-';
                        
                        // Alpha otomatis
                        if ($statusRaw === 'belum' && \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($dailyClass->date)->startOfDay())) {
                            $statusRaw = 'alpha';
                        }
                        $status = ucfirst($statusRaw);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-800">{{ $trainee->name }}</span>
                                <span class="text-xs text-gray-500 font-mono">{{ $trainee->nik }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($status == 'Hadir')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                                    Hadir
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-700">
                                    {{ $status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-gray-600">{{ $time }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="document.getElementById('editStatusModal_{{ $trainee->id }}').classList.remove('hidden')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Modal Edit Status Absensi untuk {{ $trainee->name }} -->
                    <div id="editStatusModal_{{ $trainee->id }}" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
                        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 opacity-100">
                            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                                <div>
                                    <h3 class="text-lg font-black text-slate-800">Edit Absensi</h3>
                                    <p class="text-sm font-medium text-slate-500 mt-1">{{ $trainee->name }}</p>
                                </div>
                                <button onclick="document.getElementById('editStatusModal_{{ $trainee->id }}').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors p-2 bg-white rounded-full shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                            
                            <form action="{{ route('training-reguler.daily-classes.attendance.update', $dailyClass->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">
                                <div class="p-6 space-y-5">
                                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
                                        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs text-blue-700 font-medium leading-relaxed">
                                            Gunakan fitur ini untuk mengubah status kehadiran trainee secara manual (misal: jika ada yang lupa scan, izin, sakit, atau alasan lainnya).
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Status Kehadiran <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <select name="status" class="w-full pl-4 pr-10 py-3 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 font-medium text-slate-700 transition-colors appearance-none cursor-pointer" required>
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
                                    <button type="button" onclick="document.getElementById('editStatusModal_{{ $trainee->id }}').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-200 hover:text-slate-800 transition-colors">Batal</button>
                                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-white font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add Task Modal -->
    <div x-show="showAddTask" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
        <div @click.away="showAddTask = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 opacity-100">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                <h3 class="text-lg font-black text-slate-800">Tambah Tugas</h3>
                <button type="button" @click="showAddTask = false" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('training-reguler.daily-classes.tasks.store', $dailyClass->id) }}" method="POST" class="p-6">
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
                    <button type="button" @click="showAddTask = false" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-lime-400 font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>

    </div>
</x-admin-layout>
