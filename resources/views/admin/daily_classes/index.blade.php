<x-admin-layout>
    <x-slot name="title">Sesi Pelatihan</x-slot>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-gray-800 tracking-tight">Sesi Pelatihan & Tugas</h1>
            <p class="text-gray-500 mt-2 text-sm md:text-base">Pilih program training di bawah ini untuk memantau absensi dan tugas kelas.</p>
        </div>
    </div>

    @php
        $types = $programs->pluck('type')->unique()->values();
        $years = $programs->pluck('year')->unique()->sortDesc()->values();
        $defaultYear = $years->first() ?? date('Y');
        $defaultType = $types->first() ?? '';
    @endphp

    <div x-data="{ 
        activeType: '{{ $defaultType }}',
        activeYear: '{{ $defaultYear }}',
        activeProgram: null,
        programsData: [
            @foreach($programs as $prog)
                { id: '{{ $prog->id }}', name: '{{ addslashes($prog->name) }}', type: '{{ $prog->type }}', year: '{{ $prog->year }}' },
            @endforeach
        ],
        classCounts: {
            @foreach($programs as $prog)
                '{{ $prog->id }}': {{ $dailyClasses->where('training_program_id', $prog->id)->count() }},
            @endforeach
        },
        get filteredPrograms() {
            return this.programsData.filter(p => p.type === this.activeType && p.year === this.activeYear);
        }
    }" x-init="$watch('activeType', value => activeProgram = null); $watch('activeYear', value => activeProgram = null)">
        
        <!-- Filter Bar: Type Tabs and Year Dropdown -->
        <div class="mb-8 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Type Tabs -->
            <div class="flex flex-wrap gap-2">
                @foreach($types as $type)
                    <button @click="activeType = '{{ $type }}'" 
                            :class="activeType === '{{ $type }}' ? 'bg-[#28844b] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="px-5 py-2.5 rounded-xl font-bold transition-all text-sm tracking-wide">
                        {{ $type }}
                    </button>
                @endforeach
            </div>
            
            <!-- Year Dropdown -->
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Tahun Angkatan</span>
                <select x-model="activeYear" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-[#28844b] focus:border-[#28844b] block p-2.5 font-bold outline-none cursor-pointer">
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Programs List for Selected Type & Year -->
        <div class="mb-8">
            <h3 class="text-lg font-black text-gray-800 mb-4" x-text="'Pilih Program ' + activeType + ' - ' + activeYear"></h3>
            
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" x-show="filteredPrograms.length > 0">
                <template x-for="prog in filteredPrograms" :key="prog.id">
                    <button @click="activeProgram = prog.id" 
                            :class="activeProgram === prog.id ? 'bg-[#28844b] text-white shadow-xl ring-2 ring-[#28844b] ring-offset-2 border-[#28844b] scale-[1.02]' : 'bg-white text-gray-700 hover:bg-emerald-50 hover:border-emerald-200 border-gray-200 shadow-md hover:shadow-lg'" 
                            class="text-left p-5 lg:p-6 rounded-2xl border transition-all duration-300 relative overflow-hidden group transform">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xl font-black tracking-tight" :class="activeProgram === prog.id ? 'text-white' : 'text-emerald-700'" x-text="prog.name"></span>
                            <div class="p-2 rounded-xl" :class="activeProgram === prog.id ? 'bg-white/20 text-white' : 'bg-gray-50 text-emerald-600'">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                        </div>
                        <p class="text-[12px] font-bold uppercase tracking-wider mt-1" :class="activeProgram === prog.id ? 'text-emerald-100' : 'text-gray-500'" x-text="prog.year"></p>
                    </button>
                </template>
            </div>
            
            <div x-show="filteredPrograms.length === 0" class="text-center py-8 bg-gray-50 border border-dashed border-gray-300 rounded-3xl" style="display: none;">
                <p class="text-gray-500 font-medium">Tidak ada program untuk filter yang dipilih.</p>
            </div>
        </div>

        <!-- Initial State (No Program Selected) -->
        <div x-show="!activeProgram && filteredPrograms.length > 0" x-transition.opacity class="relative overflow-hidden bg-white border border-gray-200 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 md:p-12 text-center">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-green-50 rounded-full opacity-50 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-50 rounded-full opacity-50 blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="w-24 h-24 bg-gradient-to-br from-green-100 to-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-white">
                    <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-2">Pilih Salah Satu Program</h3>
                <p class="text-gray-500 max-w-md mx-auto text-sm">
                    Silakan klik salah satu program di atas untuk melihat dan mengelola materi sesinya.
                </p>
            </div>
        </div>

        <!-- Classroom Content -->
        <div x-show="activeProgram" x-transition.opacity style="display: none;" class="mt-10">
            
            <!-- Section Boundary & Add Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b-2 border-gray-100 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-800">Daftar Materi Sesi</h2>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider" x-text="programsData.find(p => p.id == activeProgram)?.name"></p>
                    </div>
                </div>
                
                <!-- Alpine component state for modal handled at the root, or we can just use Alpine x-data="{ addModalOpen: false }" -->
                <button onclick="document.getElementById('addDailyClassModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 bg-[#28844b] text-lime-400 px-5 py-2.5 rounded-xl font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Materi
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                
            @foreach($dailyClasses as $class)
            <div x-show="activeProgram == '{{ $class->training_program_id }}'" class="group bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg hover:border-slate-300 transition-all duration-300 transform relative flex flex-col" style="display: none;">
                <div class="absolute top-0 left-0 w-full h-1 bg-[#28844b]"></div>
                <div class="p-6 pb-4 border-b border-slate-100 flex-grow">
                    <div class="flex justify-between items-start mb-3">
                        <div class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-md text-xs font-bold shadow-sm">
                            {{ $class->trainingProgram->name }}
                        </div>
                        <button onclick='openEditModal(@json($class))' class="text-slate-400 hover:text-[#28844b] transition-colors p-1" title="Edit Materi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 leading-tight mb-1">{{ $class->title }}</h2>
                    <p class="text-slate-500 text-sm font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ $class->trainer_name }}
                    </p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    <div class="flex items-center justify-between text-sm text-slate-600 mb-4 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::parse($class->date)->format('d M Y') }}
                        </div>
                        <div class="text-slate-700 font-bold bg-white px-2 py-1 rounded border border-slate-200 text-xs shadow-sm">
                            {{ $class->attendances->count() }} Trainee
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mb-4">
                        <div class="flex-1 text-center bg-white rounded-lg border border-slate-200 p-2 shadow-sm">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Hadir</p>
                            <p class="text-lg font-black text-[#28844b]">{{ $class->attendances->where('status', 'hadir')->count() }}</p>
                        </div>
                        <div class="flex-1 text-center bg-white rounded-lg border border-slate-200 p-2 shadow-sm">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Tugas</p>
                            @php
                                $tasksSent = 0;
                                foreach($class->tasks as $task) {
                                    $tasksSent += $task->submissions->where('status', 'sudah')->count();
                                }
                            @endphp
                            <p class="text-lg font-black text-[#28844b]">{{ $tasksSent }}</p>
                        </div>
                        <div class="flex-1 text-center bg-white rounded-lg border border-slate-200 p-2 shadow-sm relative group/tooltip cursor-help">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Evaluasi</p>
                            <p class="text-lg font-black text-amber-500">{{ $class->traineeEvaluations->count() ?? 0 }}</p>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-32 p-2 bg-gray-800 text-white text-[10px] rounded opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all shadow-lg z-10 text-center">
                                Trainee yang sudah mengisi evaluasi
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $hasContent = !empty($class->date) || $class->attendances->count() > 0 || $class->tasks->count() > 0;
                    @endphp

                    @if($hasContent)
                        <form action="{{ route('training-reguler.daily-classes.toggle-completed', $class->id) }}" method="POST" class="mb-2" onsubmit="return confirm('{{ $class->is_completed ? 'Kembalikan status materi ini ke aktif/berjalan?' : 'Tandai materi ini sebagai \"Pelatihan Selesai\"?' }}');">
                            @csrf
                            @if($class->is_completed)
                                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded-xl text-xs font-bold transition-all" title="Klik untuk membatalkan status selesai">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Status: Pelatihan Selesai (Batal)</span>
                                </button>
                            @else
                                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 bg-white text-emerald-700 border border-emerald-300 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-bold transition-all">
                                    <svg class="w-4 h-4 text-emerald-600 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Pelatihan Selesai</span>
                                </button>
                            @endif
                        </form>
                    @endif

                    <a href="{{ route('training-reguler.daily-classes.show', $class->id) }}" class="block w-full py-2 bg-white border border-slate-300 text-slate-700 text-center rounded-xl font-bold hover:bg-slate-50 hover:text-[#28844b] hover:border-[#28844b] transition-all shadow-sm">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @endforeach
            
            <!-- Empty state when a filter has no cards -->
            <div x-show="activeProgram && classCounts[activeProgram] === 0" style="display: none;" class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-12 bg-gray-50 border border-dashed border-gray-300 rounded-3xl">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <p class="text-gray-500 font-medium">Belum ada kelas harian yang tercatat untuk program ini.</p>
            </div>

        </div> <!-- End Grid -->
        </div> <!-- End Classroom Content -->
    </div>

    <!-- Modal Tambah Kelas Harian -->
    <div id="addDailyClassModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 opacity-100">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                <h3 class="text-lg font-black text-slate-800">Tambah Sesi Pelatihan</h3>
                <button onclick="document.getElementById('addDailyClassModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('training-reguler.daily-classes.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <!-- Hidden input to pass the currently active program ID -->
                    <input type="hidden" name="training_program_id" x-bind:value="activeProgram">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Kelas <span class="text-red-500">*</span></label>
                        <input type="date" name="date" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Materi <span class="text-red-500">*</span></label>
                        <input type="text" name="title" placeholder="Cth: Dasar Pengolahan PKS" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Trainer <span class="text-red-500">*</span></label>
                        <input type="text" name="trainer_name" placeholder="Cth: Budi Santoso" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('addDailyClassModal').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-lime-400 font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Edit Kelas Harian -->
    <div id="editDailyClassModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 opacity-100">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                <h3 class="text-lg font-black text-slate-800">Edit Sesi Pelatihan</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="editClassForm" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Hidden input to keep the original program ID -->
                    <input type="hidden" name="training_program_id" id="edit_training_program_id">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Kelas <span class="text-red-500">*</span></label>
                        <input type="date" name="date" id="edit_date" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Materi <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="edit_title" placeholder="Cth: Dasar Pengolahan PKS" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Trainer <span class="text-red-500">*</span></label>
                        <input type="text" name="trainer_name" id="edit_trainer_name" placeholder="Cth: Budi Santoso" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-lime-400 font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(classData) {
            document.getElementById('edit_training_program_id').value = classData.training_program_id;
            document.getElementById('edit_date').value = classData.date ? classData.date.substring(0, 10) : '';
            document.getElementById('edit_title').value = classData.title;
            document.getElementById('edit_trainer_name').value = classData.trainer_name;
            
            // Set action URL
            document.getElementById('editClassForm').action = `/training-reguler/daily-classes/${classData.id}`;
            
            document.getElementById('editDailyClassModal').classList.remove('hidden');
        }
        
        function closeEditModal() {
            document.getElementById('editDailyClassModal').classList.add('hidden');
        }
    </script>
</x-admin-layout>

