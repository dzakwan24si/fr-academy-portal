<x-admin-layout>
    <x-slot name="title">Materi Pelatihan</x-slot>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-gray-800 tracking-tight">Sesi Pelatihan & Tugas</h1>
            <p class="text-gray-500 mt-2 text-sm md:text-base">Pilih program training di bawah ini untuk mengelola materi kelas.</p>
        </div>
    </div>

    @php
        $types = $programs->pluck('type')->unique()->values();
        $defaultType = $types->first() ?? '';
    @endphp

    <div x-data="{ 
        activeType: '{{ $defaultType }}',
        activeProgram: null,
        programsData: [
            @foreach($programs as $prog)
                { id: '{{ $prog->id }}', name: '{{ addslashes($prog->name) }}', type: '{{ $prog->type }}' },
            @endforeach
        ],
        dailyClasses: [
            @foreach($dailyClasses as $class)
                { id: '{{ $class->id }}', training_program_id: '{{ $class->training_program_id }}' },
            @endforeach
        ],
        classCounts: {
            @foreach($programs as $prog)
                '{{ $prog->id }}': {{ $dailyClasses->where('training_program_id', $prog->id)->count() }},
            @endforeach
        },
        get filteredPrograms() {
            return this.programsData.filter(p => p.type === this.activeType);
        },
        getIconPath(name) {
            if (name.includes('Managerial')) return 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z';
            if (name.includes('Estate')) return 'M3 21h18M5 21V7l7-4 7 4v14M9 21v-4m6 4v-4';
            if (name.includes('Mill')) return 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4';
            if (name.includes('Traksi')) return 'M8 14H5a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v3m0 0l3-3h3v3.586a2 2 0 01-.586 1.414l-2 2A2 2 0 0116 15h-1m-4 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z';
            if (name.includes('Downstream')) return 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z';
            if (name.includes('Administrasi')) return 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01';
            return 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
        }
    }" x-init="
        if (filteredPrograms.length === 1) {
            activeProgram = filteredPrograms[0].id;
        }
        $watch('activeType', value => {
            if (filteredPrograms.length === 1) {
                activeProgram = filteredPrograms[0].id;
            } else {
                activeProgram = null;
            }
        });
    ">
        
        <!-- Filter Bar -->
        <div class="mb-8 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4" x-show="programsData.length > 1">
            <div class="flex flex-wrap gap-2">
                @foreach($types as $type)
                    <button @click="activeType = '{{ $type }}'"
                            :class="activeType === '{{ $type }}' ? 'bg-[#28844b] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            class="px-5 py-2.5 rounded-xl font-bold transition-all text-sm tracking-wide">
                        {{ $type }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Programs List (Multi Program Mode) -->
        <div class="mb-8" x-show="programsData.length > 1">
            <h3 class="text-lg font-black text-gray-800 mb-4" x-text="'Program ' + activeType"></h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4" x-show="filteredPrograms.length > 0">
                <template x-for="prog in filteredPrograms" :key="prog.id">
                    <button @click="activeProgram = prog.id" 
                            :class="[
                                activeProgram === prog.id ? 'bg-[#28844b] text-white shadow-xl ring-2 ring-[#28844b] ring-offset-2 border-[#28844b] scale-[1.02]' : 'bg-white text-gray-700 hover:bg-emerald-50 hover:border-emerald-200 border-gray-200 shadow-md hover:shadow-lg',
                                'w-full'
                            ]" 
                            class="text-left p-5 lg:p-6 rounded-2xl border transition-all duration-300 relative overflow-hidden group transform">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xl font-black tracking-tight" :class="activeProgram === prog.id ? 'text-white' : 'text-emerald-700'" x-text="prog.name"></span>
                            <div class="p-2 rounded-xl shrink-0" :class="activeProgram === prog.id ? 'bg-white/20 text-white' : 'bg-gray-50 text-emerald-600'">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(prog.name)"></path></svg>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
            
            <div x-show="filteredPrograms.length === 0" class="text-center py-8 bg-gray-50 border border-dashed border-gray-300 rounded-3xl" style="display: none;">
                <p class="text-gray-500 font-medium">Tidak ada program untuk filter yang dipilih.</p>
            </div>
        </div>

        <!-- Single Program Banner Mode -->
        <div x-show="programsData.length === 1" style="display: none;" class="mb-10 relative overflow-hidden bg-[#28844b] rounded-3xl p-8 lg:p-10 text-white shadow-xl flex items-center justify-between">
            <div class="absolute right-0 top-0 w-64 h-full opacity-10 flex items-center justify-end pr-8 pointer-events-none">
                <svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(filteredPrograms[0]?.name || '')"></path></svg>
            </div>
            <div class="relative z-10">
                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-bold tracking-wider uppercase mb-3" x-text="activeType"></span>
                <h2 class="text-3xl lg:text-4xl font-black mb-2" x-text="filteredPrograms[0]?.name"></h2>
                <p class="text-emerald-100 max-w-xl text-sm lg:text-base">Kelola daftar materi dan sesi pelatihan untuk program ini. Tambahkan materi baru atau edit yang sudah ada.</p>
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
                <h3 class="text-2xl font-black text-gray-800 mb-2">Pilih Kategori Program</h3>
                <p class="text-gray-500 max-w-md mx-auto text-sm">
                    Silakan klik salah satu kategori di atas untuk melihat dan mengelola materi (wadah sesi) di dalamnya.
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
                        <h2 class="text-xl font-black text-gray-800">Daftar Materi</h2>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider" x-text="programsData.find(p => p.id == activeProgram)?.name"></p>
                    </div>
                </div>
                
                <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#28844b] text-white rounded-xl text-sm font-bold hover:bg-[#1d6b3a] transition-all shadow-[0_4px_12px_rgba(40,132,75,0.3)] hover:shadow-[0_6px_16px_rgba(40,132,75,0.4)] active:scale-95 shrink-0">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Materi
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($dailyClasses as $class)
                <div x-show="activeProgram == '{{ $class->training_program_id }}'" class="group bg-white rounded-2xl p-6 shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-slate-200 hover:border-emerald-200 transition-all duration-300 relative flex flex-col h-full transform hover:-translate-y-1 overflow-hidden">
                    
                    <!-- Subtle top accent line -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-[#28844b] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div class="pr-4">
                            <h3 class="font-extrabold text-slate-800 text-lg leading-snug group-hover:text-emerald-700 transition-colors">{{ $class->title }}</h3>
                            <div class="mt-2 flex items-center gap-2 text-xs font-medium text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                <span>{{ $class->tasks->count() }} Tugas</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300 mx-1"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>{{ $class->attendances->pluck('trainee_id')->unique()->count() }} Terdaftar</span>
                            </div>
                            @if($class->is_completed)
                                <div class="mt-2.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        Pelatihan Selesai
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button onclick="document.getElementById('editModal_{{ $class->id }}').classList.remove('hidden')" class="p-2 text-slate-400 hover:bg-slate-100 hover:text-emerald-600 rounded-xl transition-colors shrink-0 tooltip" title="Edit Materi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <form action="{{ route('training-non-reguler.daily-classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi pelatihan ini secara permanen? Semua data peserta dan absensi di dalamnya akan ikut terhapus!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors shrink-0 tooltip" title="Hapus Materi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mb-6 space-y-2.5 mt-auto relative z-10">
                        
                        @if($class->mess_url)
                            <a href="{{ $class->mess_url }}" target="_blank" class="flex items-center justify-between text-[13px] font-bold text-amber-600 bg-amber-50/70 hover:bg-amber-500 hover:text-white px-4 py-2.5 rounded-xl transition-all duration-300 w-full group/link">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> 
                                    Pesan Mess
                                </span>
                                <svg class="w-4 h-4 opacity-50 group-hover/link:opacity-100 transform group-hover/link:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @endif
                    </div>
                    


                    <div class="mt-2 flex relative z-10">
                        <a href="{{ route('training-non-reguler.daily-classes.show', $class->id) }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-[#28844b] hover:text-white transition-all shadow-sm hover:shadow-emerald-200 group/btn">
                            Kelola Peserta & Jadwal
                            <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
                
                <!-- Edit Modal -->
                <div id="editModal_{{ $class->id }}" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 opacity-100">
                        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                            <h3 class="text-lg font-black text-slate-800">Edit Materi Sesi</h3>
                            <button type="button" onclick="document.getElementById('editModal_{{ $class->id }}').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <form action="{{ route('training-non-reguler.daily-classes.update', $class->id) }}" method="POST" class="p-6">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <input type="hidden" name="training_program_id" value="{{ $class->training_program_id }}">
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Materi <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" value="{{ $class->title }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required>
                                </div>
                                

                            </div>
                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                                <button type="button" onclick="document.getElementById('editModal_{{ $class->id }}').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-white font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div x-show="!dailyClasses.some(c => c.training_program_id == activeProgram)" class="text-center py-12 bg-white border border-dashed border-gray-300 rounded-3xl" style="display: none;">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <p class="text-gray-500 font-medium text-lg">Belum Ada Materi</p>
                <p class="text-sm text-gray-400 mt-1 mb-6 max-w-sm mx-auto">Klik tombol "Buat Materi" untuk menambahkan wadah sesi di program ini.</p>
                <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-colors">
                    Mulai Buat Materi
                </button>
            </div>
        </div>
        
        <!-- Create Modal -->
        <div id="createModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-slate-900/50 backdrop-blur-sm transition-opacity text-left p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 opacity-100">
                <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-800">Buat Materi Baru</h3>
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('training-non-reguler.daily-classes.store') }}" method="POST" class="p-6">
                    @csrf
                    <!-- Dynamic Program ID via Alpine -->
                    <input type="hidden" name="training_program_id" :value="activeProgram">
                    
                    <div class="space-y-4">
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex gap-3">
                            <svg class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xs text-indigo-700 font-medium leading-relaxed">
                                Anda sedang membuat materi (wadah) untuk program <strong x-text="programsData.find(p => p.id == activeProgram)?.name"></strong>. Tanggal dan Trainer akan ditambahkan saat Anda meng-import peserta.
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Materi <span class="text-red-500">*</span></label>
                            <input type="text" name="title" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#28844b] focus:border-[#28844b] font-medium transition-all" required placeholder="Cth: Awareness Leadership">
                        </div>
                        

                    </div>
                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#28844b] text-white font-bold hover:bg-[#1d6b3a] active:scale-95 transition-all shadow-md">Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <script>
        // Store dailyClasses data for alpine usage
        document.addEventListener('alpine:init', () => {
            Alpine.data('dailyClasses', () => ({
                dailyClasses: @json($dailyClasses)
            }))
        })
    </script>
</x-admin-layout>




