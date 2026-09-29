{{-- MODAL FORM: Buat Laporan Mingguan Baru (Single Page via Component) --}}
<div x-data="weeklyReportForm({{ $programs->toJson() }})">
    <x-admin-modal 
        show="showWizard" 
        close="closeWizard()"
        title="Buat Laporan Mingguan Baru" 
        subtitle="Isi data laporan secara berurutan di bawah ini." 
        maxWidth="4xl"
        headerBg="bg-green-600"
        titleColor="text-white"
        subtitleColor="text-green-100"
        closeBtnColor="text-white hover:text-green-200">
        
        <form id="wizard-form" action="{{ route('training-non-reguler.weekly-reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div x-show="step === 1" id="wizard-step-1" class="px-4 sm:px-8">
            {{-- 1. INFORMASI UTAMA --}}
            <div class="mb-8">
                <h4 class="text-[#28844b] font-bold text-sm tracking-wide mb-4">1. INFORMASI UTAMA</h4>
                <div class="grid grid-cols-1 md:grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Program Training <span class="text-red-500">*</span></label>
                        <select name="training_program_id" x-model="selectedProgramId" @change="updateTrainees()" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-green-500 outline-none bg-white">
                            <option value="">— Pilih Program —</option>
                            <template x-for="program in programs" :key="program.id">
                                <option :value="program.id" x-text="program.name + ' (' + program.year + ')'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="week_start_date" x-model="weekStartDate" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="week_end_date" x-model="weekEndDate" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-green-500 outline-none">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tema Laporan</label>
                    <select name="theme" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-green-500 outline-none bg-gray-50 hover:bg-white cursor-pointer">
                        <option value="reguler">Tema Reguler (Biru)</option>
                        <option value="kemerdekaan">Tema Kemerdekaan (Merah Putih)</option>
                    </select>
                </div>
            </div>

            <div x-data="coverPhotoManager()" class="mb-8">
                {{-- 2. COVER HALAMAN 1 (Wajib) --}}
                <div class="mb-6">
                    <h4 class="text-[#28844b] font-bold text-sm tracking-wide mb-4">2. FOTO COVER (HALAMAN 1) <span class="text-red-500 ml-1 font-normal text-[11px]">*Wajib (3 Foto)</span></h4>
                    <div class="flex flex-wrap gap-4 justify-center sm:justify-start">
                        @foreach([1,2,3] as $n)
                        <div class="relative w-28 flex-shrink-0">
                            <div class="w-full h-28 rounded border border-gray-300 bg-gray-50 flex items-center justify-center relative overflow-hidden">
                                <img x-show="covers[{{ $n }}]" :src="covers[{{ $n }}]" class="w-full h-full object-cover absolute inset-0">
                                <div x-show="!covers[{{ $n }}]" class="text-center text-gray-400">
                                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px]">Foto {{ $n }}</span>
                                </div>
                                <button type="button" x-show="covers[{{ $n }}]" @click="clearCover({{ $n }})" class="absolute top-1 right-1 w-5 h-5 bg-red-500/90 text-white rounded flex items-center justify-center shadow-sm hover:bg-red-600 z-10" title="Hapus foto ini">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <input type="file" id="cover-photo-{{ $n }}" name="cover_photo_{{ $n }}" accept="image/*" @change="handleCover($event, {{ $n }})" class="sr-only">
                            <label for="cover-photo-{{ $n }}" class="mt-1.5 w-full text-center block text-xs font-semibold text-green-700 cursor-pointer hover:text-green-800" x-text="covers[{{ $n }}] ? 'Ganti Foto' : '+ Pilih Foto'"></label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. FOTO HALAMAN 2 (Opsional) --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-[#28844b] font-bold text-sm tracking-wide">3. FOTO HALAMAN 2 <span class="text-gray-400 font-normal text-[11px] ml-1 uppercase">(Opsional)</span></h4>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <span class="text-[11px] font-semibold text-gray-500" x-text="showPage2 ? 'Halaman 2 Aktif' : 'Lewati Halaman 2'"></span>
                            <div class="relative w-8 h-4 rounded-full transition-colors border border-gray-300" :class="showPage2 ? 'bg-green-500 border-green-500' : 'bg-gray-200'">
                                <input type="checkbox" x-model="showPage2" class="sr-only">
                                <div class="absolute top-[1px] left-[1px] w-3 h-3 bg-white rounded-full shadow-sm transition-transform" :class="showPage2 ? 'translate-x-[14px]' : 'translate-x-0'"></div>
                            </div>
                        </label>
                    </div>
                    
                    <div x-show="!showPage2" class="py-3 text-center bg-gray-50 border border-gray-200 border-dashed rounded text-xs text-gray-500 font-medium">
                        Anda melewatkan foto halaman 2. (Centang tombol di atas untuk mengisi).
                    </div>

                    <div x-show="showPage2" class="grid grid-cols-1 md:grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 rounded border border-gray-200 bg-gray-50">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-semibold text-gray-700">Foto Strip Atas (Banyak)</label>
                                <button type="button" @click="clearAllStrip()" x-show="stripPreviews.length > 0" class="text-[10px] text-red-500 hover:text-red-700 font-bold">&times; Hapus Semua</button>
                            </div>
                            <input type="file" id="cover-strip" name="page_2_strip_photos[]" multiple accept="image/*" @change="addStripPhotos($event)" class="sr-only">
                            <input type="hidden" name="delete_all_strip_photos" :value="stripPreviews.length === 0 ? '1' : '0'">
                            <input type="hidden" name="skip_page_2" :value="!showPage2 ? '1' : '0'">
                            <label for="cover-strip" class="inline-flex items-center px-3 py-1.5 text-[11px] font-semibold text-gray-600 bg-white border border-gray-300 rounded cursor-pointer hover:border-green-400 hover:text-green-700 transition">
                                + Tambah Foto Strip
                            </label>
                            <div x-show="stripPreviews.length > 0" class="mt-2 flex flex-wrap gap-2">
                                <template x-for="(preview, pi) in stripPreviews" :key="preview.uid">
                                    <div class="relative group cursor-pointer" @click="removeStripPhoto(pi)" title="Hapus foto ini">
                                        <img :src="preview.url" class="w-10 h-10 object-cover rounded border border-gray-300 group-hover:brightness-50 transition-all">
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="p-3 rounded border border-gray-200 bg-gray-50">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Foto Besar Tengah (1 Foto)</label>
                            <div class="flex gap-3">
                                <div class="relative w-16 flex-shrink-0">
                                    <div class="w-full h-16 rounded bg-white overflow-hidden border border-gray-300 flex items-center justify-center relative">
                                        <img x-show="coverMain" :src="coverMain" class="w-full h-full object-cover absolute inset-0">
                                        <div x-show="!coverMain"><svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                        <button type="button" x-show="coverMain" @click="coverMain = null; document.getElementById('cover-main').value = '';" class="absolute top-1 right-1 w-4 h-4 bg-red-500/90 text-white rounded-sm flex items-center justify-center hover:bg-red-600 z-10"><svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    </div>
                                    <input type="file" id="cover-main" name="cover_photo_main" accept="image/*" @change="e => { const f = e.target.files[0]; if(f){ const r = new FileReader(); r.onload = ev => coverMain = ev.target.result; r.readAsDataURL(f); } }" class="sr-only">
                                    <label for="cover-main" class="mt-1 w-full text-center block text-[10px] font-semibold text-green-700 cursor-pointer hover:text-green-800" x-text="coverMain ? 'Ganti Foto' : '+ Pilih'"></label>
                                </div>
                                <p class="text-[10px] text-gray-500 leading-relaxed">Highlight aktivitas besar.<br>(Opsional)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>


            <div x-show="step === 2" id="wizard-step-2" class="px-4 sm:px-8" x-cloak>
            {{-- 4. SESI PELATIHAN --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[#28844b] font-bold text-sm tracking-wide">4. SESI PELATIHAN</h4>
                    <button type="button" @click="addSession()" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-green-700 bg-green-50 border border-green-200 rounded hover:bg-green-100 transition">
                        + Tambah Sesi
                    </button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(session, index) in sessions" :key="session.id">
                        @include('admin.non-reguler.weekly_reports.partials._session_card')
                    </template>
                    <div x-show="sessions.length === 0" class="text-center p-4 border border-dashed border-gray-300 rounded text-gray-400 text-xs font-medium">
                        Sesi belum ada. Klik "Tambah Sesi".
                    </div>
            </div>
            </div>
            </div>

            <div x-show="step === 3" id="wizard-step-3" class="px-4 sm:px-8" x-cloak>
            {{-- 5. NILAI UJIAN --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[#28844b] font-bold text-sm tracking-wide">5. NILAI UJIAN (MATRIX)</h4>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <span class="text-[11px] font-semibold text-gray-500" x-text="showQuizAndRanking ? 'Aktif' : 'Lewati'"></span>
                        <div class="relative w-8 h-4 rounded-full transition-colors border border-gray-300" :class="showQuizAndRanking ? 'bg-green-500 border-green-500' : 'bg-gray-200'">
                            <input type="checkbox" x-model="showQuizAndRanking" class="sr-only">
                            <div class="absolute top-[1px] left-[1px] w-3 h-3 bg-white rounded-full shadow-sm transition-transform" :class="showQuizAndRanking ? 'translate-x-[14px]' : 'translate-x-0'"></div>
                        </div>
                    </label>
                </div>

                <div x-show="!showQuizAndRanking" class="py-3 text-center text-gray-400 border border-gray-200 border-dashed rounded bg-gray-50 text-xs font-medium">
                    Input nilai ujian dilewati.
                </div>

                <div x-show="showQuizAndRanking && !selectedProgramId" class="py-3 text-center text-amber-600 bg-amber-50 border border-amber-200 rounded text-xs font-medium">
                    Pilih program di Bagian 1 terlebih dahulu.
                </div>
                
                <div x-show="showQuizAndRanking && selectedProgramId && trainees.length === 0" class="py-3 text-center text-red-600 bg-red-50 border border-red-200 rounded text-xs font-medium">
                    Program pelatihan ini belum memiliki data Trainee. Silakan tambahkan trainee terlebih dahulu.
                </div>

                <div x-show="showQuizAndRanking && trainees.length > 0" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 mb-4 bg-gray-50 border border-gray-200 rounded-md">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-2">Foto Kiri Atas Banner Ujian (Opsional)</label>
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 border rounded bg-white flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="quizPhotoPreview">
                                        <img :src="quizPhotoPreview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!quizPhotoPreview">
                                        <span class="text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                    </template>
                                </div>
                                <input type="file" name="cover_photo_5" accept="image/*" @change="handleQuizPhoto" class="block w-full text-[10px] text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 outline-none cursor-pointer">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-700 mb-2">Foto Kanan Atas Banner Ujian (Opsional)</label>
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 border rounded bg-white flex items-center justify-center overflow-hidden shrink-0">
                                    <template x-if="quizPhotoPreview2">
                                        <img :src="quizPhotoPreview2" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!quizPhotoPreview2">
                                        <span class="text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                    </template>
                                </div>
                                <input type="file" name="cover_photo_6" accept="image/*" @change="handleQuizPhoto2" class="block w-full text-[10px] text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 outline-none cursor-pointer">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" @click="addSubject()" class="text-[11px] font-bold text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded hover:bg-green-100 transition">
                            + Tambah Materi
                        </button>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded">
                        <table class="w-full text-left text-xs whitespace-nowrap">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-2 py-1.5 font-semibold text-gray-700 sticky left-0 bg-gray-100 z-10 border-r border-gray-200" rowspan="2">Nama Trainee</th>
                                    <th class="px-2 py-1.5 font-semibold text-gray-700 text-center border-b border-gray-200" :colspan="subjects.length">Materi Ujian</th>
                                    <th class="px-2 py-1.5 font-semibold text-gray-700 text-center border-l border-gray-200" rowspan="2">Avg</th>
                                    <th class="px-2 py-1.5 font-semibold text-gray-700 text-center" rowspan="2">Rank</th>
                                    <th class="px-2 py-1.5 font-semibold text-gray-700 text-center" rowspan="2">Kat</th>
                                </tr>
                                <tr>
                                    <template x-for="(subj, sIdx) in subjects" :key="sIdx">
                                        <th class="px-2 py-1.5 font-normal min-w-[140px] border-r border-gray-200">
                                            <div class="relative flex items-center w-full">
                                                <input type="text" x-model="subjects[sIdx]" :name="'subjects['+sIdx+']'" list="modal-subjects-list" placeholder="Materi..." class="w-full px-2 py-1.5 pr-7 border border-gray-300 rounded bg-white text-[10px] outline-none focus:border-green-500">
                                                <button type="button" @click="removeSubject(sIdx)" x-show="subjects.length > 1" class="absolute right-1.5 w-4 h-4 bg-red-100 text-red-600 rounded-full flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors text-[10px] font-bold" title="Hapus materi">&times;</button>
                                            </div>
                                        </th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="trainee in trainees" :key="trainee.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-1.5 font-medium text-gray-700 sticky left-0 bg-white border-r border-gray-200 z-10" x-text="trainee.name"></td>
                                        <template x-for="(subj, sIdx) in subjects" :key="sIdx">
                                            <td class="px-1.5 py-1.5 border-r border-gray-100">
                                                <input type="number" step="0.01" x-model="quizScores[trainee.id][sIdx]" @input="calculateRanks()" :name="'quiz_scores['+trainee.id+']['+sIdx+']'" placeholder="0-100" class="w-full px-1.5 py-1 border border-gray-300 rounded text-[11px] outline-none focus:border-green-500 text-center">
                                            </td>
                                        </template>
                                        <td class="px-2 py-1.5 border-l border-gray-200 text-center"><input type="number" step="0.01" readonly :value="rankings[trainee.id]?.avg" :name="'rankings['+trainee.id+'][average_score]'" class="w-12 text-[11px] font-bold text-center bg-transparent border-0 outline-none" tabindex="-1"></td>
                                        <td class="px-2 py-1.5 text-center"><input type="number" readonly :value="rankings[trainee.id]?.rank" :name="'rankings['+trainee.id+'][rank]'" class="w-8 text-[11px] font-bold text-center bg-transparent border-0 outline-none" tabindex="-1"></td>
                                        <td class="px-2 py-1.5 text-center">
                                            <span class="text-[9px] font-bold px-1 py-0.5 rounded" :class="rankings[trainee.id]?.group === 'Top' ? 'bg-green-100 text-green-700' : rankings[trainee.id]?.group === 'Bottom' ? 'bg-red-100 text-red-700' : 'text-gray-400'" x-text="rankings[trainee.id]?.group || '-'"></span>
                                            <input type="hidden" :value="rankings[trainee.id]?.group ? rankings[trainee.id].group.toLowerCase() : ''" :name="'rankings['+trainee.id+'][rank_group]'">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <datalist id="modal-subjects-list">
                        <option value="Dasar-dasar PKS"></option>
                        <option value="Profil Agroindustri"></option>
                        <option value="Tinjauan Agronomy Kelapa Sawit"></option>
                        <option value="Profil Perusahaan"></option>
                        <option value="Corporate Culture"></option>
                    </datalist>
                    
                    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <label class="block text-[11px] font-semibold text-gray-700 mb-2">Catatan Tambahan / Remedial (Opsional)</label>
                        <textarea x-model="quizNotes" name="quiz_notes" rows="2" style="min-height: 50px;" x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-on:focus="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-on:mouseenter="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-effect="quizNotes; setTimeout(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }, 100)" placeholder="Contoh: Trainee yang mendapatkan nilai dibawah 70 akan melaksanakan ujian remedial di tanggal ..." class="w-full px-2 py-1.5 border border-gray-300 rounded text-[11px] outline-none focus:border-green-500 overflow-hidden"></textarea>
                    </div>
                </div>
            </div>
            </div>

            <div x-show="step === 4" id="wizard-step-4" class="px-4 sm:px-8" x-cloak>
            {{-- 6. TRAINEE RESIGN --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[#28844b] font-bold text-sm tracking-wide">6. TRAINEE RESIGN <span class="text-gray-400 font-normal text-[11px] ml-1 uppercase">(Opsional)</span></h4>
                    <button type="button" @click="addResignee()" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition">
                        + Tambah Trainee
                    </button>
                </div>
                
                <div class="mb-4 p-3 bg-white border border-gray-200 rounded">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Foto Kiri Atas Banner (Opsional)</label>
                    <input type="file" id="cover-photo-4" name="cover_photo_4" accept="image/*" @change="handleResignBanner($event)" class="sr-only">
                    <label for="cover-photo-4" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded cursor-pointer hover:bg-gray-100">
                        + Pilih Foto Banner
                    </label>
                    <div x-show="resignBannerPreview" class="mt-3 relative w-32 h-20 rounded border border-gray-300 overflow-hidden">
                        <img :src="resignBannerPreview" class="w-full h-full object-cover">
                        <button type="button" @click="clearResignBanner()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 hover:bg-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                </div>

                <div x-show="resignees.length === 0" class="py-3 text-center text-gray-400 border border-gray-200 border-dashed rounded bg-gray-50 text-xs font-medium">
                    Tidak ada trainee yang resign minggu ini.
                </div>

                <div class="space-y-4">
                    <template x-for="(res, index) in resignees" :key="res.id">
                        <div class="border border-red-200 rounded p-3 bg-red-50/50 relative">
                            <button type="button" @click="removeResignee(index)" class="absolute top-1.5 right-1.5 text-red-400 hover:text-red-600 p-1" title="Hapus Data"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            <div class="grid grid-cols-1 md:grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Pilih Trainee <span class="text-red-500">*</span></label>
                                    <select x-model="res.trainee_id" @change="updateResigneeTrainee(index)" :name="'resignees['+index+'][trainee_id]'" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs outline-none focus:border-red-500 mb-2">
                                        <option value="">-- Pilih Trainee --</option>
                                        <template x-for="t in trainees" :key="t.id"><option :value="t.id" x-text="t.name"></option></template>
                                    </select>
                                    <div x-show="res.traineeObj" class="bg-white border border-gray-200 rounded p-1.5 flex gap-3 mb-2">
                                        <div class="relative w-12 h-12 rounded bg-gray-100 border border-gray-200 overflow-hidden shrink-0 group cursor-pointer" @click="document.getElementById('resign-photo-'+index).click()">
                                            <template x-if="res.photoPreview">
                                                <img :src="res.photoPreview" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!res.photoPreview && res.traineeObj && res.traineeObj.photo_path">
                                                <img :src="'/storage/' + res.traineeObj.photo_path" class="w-full h-full object-cover opacity-60 grayscale">
                                            </template>
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                                <span class="text-white text-[8px] font-semibold text-center leading-tight">Ubah<br>Foto</span>
                                            </div>
                                        </div>
                                        <input type="file" :id="'resign-photo-'+index" @change="handleResignPhoto($event, index)" :name="'resignees['+index+'][photo]'" accept="image/*" class="hidden">
                                        <input type="hidden" :name="'resignees['+index+'][delete_photo]'" :value="res.photoPreview === '' ? '1' : '0'">
                                        
                                        <div class="text-[10px] leading-tight flex flex-col justify-center">
                                            <p class="font-bold text-gray-800" x-text="res.traineeObj?.name"></p>
                                            <p class="text-gray-500 mb-1">Asal: <span x-text="res.traineeObj?.origin || '-'"></span></p>
                                            <div class="flex gap-1.5">
                                                 <button type="button" @click="document.getElementById('resign-photo-'+index).click()" class="text-[9px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-1.5 py-0.5 rounded">Upload Foto</button>
                                                 <button type="button" x-show="res.photoPreview" @click="res.photoPreview = ''; document.getElementById('resign-photo-'+index).value = ''" class="text-[9px] font-bold text-red-500 hover:text-red-700 bg-red-50 px-1.5 py-0.5 rounded">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                                        <div><label class="block text-[10px] text-gray-600 mb-1">Tempat Lahir</label><input type="text" x-model="res.birth_place" :name="'resignees['+index+'][birth_place]'" class="w-full px-2 py-1 border border-gray-300 rounded text-xs outline-none"></div>
                                        <div><label class="block text-[10px] text-gray-600 mb-1">Tgl Lahir</label><input type="date" x-model="res.birth_date" :name="'resignees['+index+'][birth_date]'" class="w-full px-2 py-1 border border-gray-300 rounded text-xs outline-none"></div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Nilai Terakhir (Opsional)</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                                        <div><label class="block text-[10px] text-gray-500">Avg In-Class</label><input type="number" step="0.01" x-model="res.avg_inclass_score" :name="'resignees['+index+'][avg_inclass_score]'" class="w-full px-2 py-1 border border-gray-300 rounded text-[11px] outline-none"></div>
                                        <div><label class="block text-[10px] text-gray-500">Rank In-Class</label><input type="number" x-model="res.inclass_rank" :name="'resignees['+index+'][inclass_rank]'" class="w-full px-2 py-1 border border-gray-300 rounded text-[11px] outline-none"></div>
                                        <div><label class="block text-[10px] text-gray-500">Avg Praktek</label><input type="number" step="0.01" x-model="res.avg_practice_score" :name="'resignees['+index+'][avg_practice_score]'" class="w-full px-2 py-1 border border-gray-300 rounded text-[11px] outline-none"></div>
                                        <div><label class="block text-[10px] text-gray-500">Rank Praktek</label><input type="number" x-model="res.practice_rank" :name="'resignees['+index+'][practice_rank]'" class="w-full px-2 py-1 border border-gray-300 rounded text-[11px] outline-none"></div>
                                    </div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Alasan Resign</label>
                                    <textarea x-model="res.reason" :name="'resignees['+index+'][reason]'" rows="2" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs outline-none focus:border-red-500" placeholder="Deskripsi..."></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            </div>
            <div class="-mx-4 sm:-mx-8 -mb-6 mt-8 px-4 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center gap-2 sm:gap-3 rounded-b-2xl flex-wrap sm:flex-nowrap">
                <button type="button" x-show="step > 1" @click="prevStep()" class="px-3 sm:px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition text-xs sm:text-sm whitespace-nowrap">Kembali</button>
                <div x-show="step === 1"></div> <!-- Spacer for flex-between -->
                
                <div class="flex gap-2">
                    <button type="button" @click="closeWizard()" class="px-3 sm:px-5 py-2 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition text-xs sm:text-sm whitespace-nowrap">Batal</button>
                    <button type="button" x-show="step < 4" @click="nextStep()" class="px-4 sm:px-8 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition text-xs sm:text-sm shadow-md whitespace-nowrap">Selanjutnya</button>
                    <button type="button" x-show="step === 4" @click="submitWizard()" class="px-3 sm:px-5 py-2 bg-[#28844b] text-white font-bold rounded-lg hover:bg-green-700 transition flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm shadow-md whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Laporan
                    </button>
                </div>
            </div>
        </form>
    </x-admin-modal>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('weeklyReportForm', (programs) => ({
        programs,
        step: 1,
        selectedProgramId:'', weekStartDate:'', weekEndDate:'',
        trainees:[], resignees:[], showQuizAndRanking:false,
        subjects:['Dasar-dasar PKS','Profil Agroindustri','Tinjauan Agronomy Kelapa Sawit','Profil Perusahaan','Corporate Culture'],
        quizScores:{}, remedialNotes:{}, rankings:{}, quizPhotoPreview: null, quizPhotoPreview2: null, quizNotes: '',
        sessions:[{id:Date.now(),title:'',trainerPreview:null,trainerFileName:'',bannerPreviewLeft:null,bannerPreviewRight:null,docFiles:[],docPreviews:[],existing_photos:[],showTujuan:false,showHasil:false,showEval:false,showTrainerPhoto:false,showDokumentasi:true,showPeserta:false,showOjt:false,evaluations:{baik_sekali:{materi:'',trainer:'',fasilitas:''},baik:{materi:'',trainer:'',fasilitas:''},cukup_baik:{materi:'',trainer:'',fasilitas:''},kurang_baik:{materi:'',trainer:'',fasilitas:''},tidak_baik:{materi:'',trainer:'',fasilitas:''}}}],
        updateTrainees(){
            if(this.selectedProgramId){const p=this.programs.find(p=>p.id==this.selectedProgramId);this.trainees=p?(p.trainees||[]):[];}else{this.trainees=[];}
            this.resignees=[];this.quizScores={};this.remedialNotes={};this.rankings={};
            this.trainees.forEach(t=>{this.quizScores[t.id]={};this.remedialNotes[t.id]='';});
        },
        nextStep() { if (this.step < 4) this.step++; },
        prevStep() { if (this.step > 1) this.step--; },
        submitWizard(){
            if(!this.selectedProgramId){alert('Bagian 1: Pilih Program Training!'); this.step=1; return;}
            if(!this.weekStartDate||!this.weekEndDate){alert('Bagian 1: Isi Tanggal Mulai dan Selesai!'); this.step=1; return;}
            
            const c1 = document.getElementById('cover-photo-1').files.length > 0;
            const c2 = document.getElementById('cover-photo-2').files.length > 0;
            const c3 = document.getElementById('cover-photo-3').files.length > 0;
            if(!c1 || !c2 || !c3){alert('Bagian 2: Tiga foto cover halaman 1 wajib diisi!'); this.step=1; return;}

            const form = document.getElementById('wizard-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                const firstInvalid = form.querySelector(':invalid');
                if(firstInvalid) {
                    const stepDiv = firstInvalid.closest('[id^="wizard-step-"]');
                    if(stepDiv) {
                        const stepNum = parseInt(stepDiv.id.replace('wizard-step-', ''));
                        if(stepNum) this.step = stepNum;
                    }
                }
                return;
            }
            
            form.submit();
        },
        addSubject(){this.subjects.push('');},
        handleQuizPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => { this.quizPhotoPreview = e.target.result; };
            reader.readAsDataURL(file);
        },
        handleQuizPhoto2(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => { this.quizPhotoPreview2 = e.target.result; };
            reader.readAsDataURL(file);
        },
        removeSubject(index){if(this.subjects.length>1&&confirm('Hapus materi?')){this.subjects.splice(index,1);this.calculateRanks();}},
        calculateRanks(){
            let avgs=[];
            this.trainees.forEach(t=>{
                let s=this.quizScores[t.id]||{},sum=0,cnt=0;
                this.subjects.forEach((sub,i)=>{let v=parseFloat(s[i]);if(!isNaN(v)){sum+=v;cnt++;}});
                avgs.push({id:t.id,avg:cnt>0?(sum/cnt):null});
            });
            let sorted=[...avgs].filter(a=>a.avg!==null).sort((a,b)=>b.avg-a.avg);
            let nr={};
            sorted.forEach((item,i)=>{nr[item.id]={avg:item.avg.toFixed(2),rank:i+1,group:i<3?'Top':(i>=sorted.length-3?'Bottom':'')};});
            avgs.filter(a=>a.avg===null).forEach(item=>{nr[item.id]={avg:null,rank:'',group:''};});
            this.rankings=nr;
        },
        addResignee(){this.resignees.push({id:Date.now(),trainee_id:'',traineeObj:null,birth_place:'',birth_date:'',avg_inclass_score:'',inclass_rank:'',avg_practice_score:'',practice_rank:'',reason:'',photoPreview:null});},
        removeResignee(index){if(confirm('Hapus?'))this.resignees.splice(index,1);},
        updateResigneeTrainee(index){const tId=this.resignees[index].trainee_id;this.resignees[index].traineeObj=this.trainees.find(t=>t.id==tId)||null;},
        handleResignPhoto(event,index){const file=event.target.files[0];if(!file)return;const r=new FileReader();r.onload=(e)=>{this.resignees[index].photoPreview=e.target.result;};r.readAsDataURL(file);},
        resignBannerPreview: null,
        handleResignBanner(event){const f=event.target.files[0];if(!f)return;const r=new FileReader();r.onload=(e)=>{this.resignBannerPreview=e.target.result;};r.readAsDataURL(f);},
        clearResignBanner(){this.resignBannerPreview=null;const inp=document.getElementById('cover-photo-4');if(inp)inp.value='';},
        addSession(){let newOjt = {};
                          let newParticipants = {};
                          this.trainees.forEach(t => {
                              newOjt[t.id] = { location_code: t.ojt_location_code || '', region: t.ojt_region || '' };
                              newParticipants[t.id] = { is_participating: false, participation_type: 'aktif' };
                          });
                          this.sessions.push({
                              id:Date.now(),title:'',type:'inclass',trainerPreview:null,trainerFileName:'',bannerPreviewLeft:null,bannerPreviewRight:null,banner_right_text:'',docFiles:[],docPreviews:[],existing_photos:[],showTujuan:false,showHasil:false,showEval:false,showTrainerPhoto:false,showDokumentasi:true,showPeserta:false,showOjt:false,evaluations:{baik_sekali:{materi:'',trainer:'',fasilitas:''},baik:{materi:'',trainer:'',fasilitas:''},cukup_baik:{materi:'',trainer:'',fasilitas:''},kurang_baik:{materi:'',trainer:'',fasilitas:''},tidak_baik:{materi:'',trainer:'',fasilitas:''}},
                              ojt: newOjt,
                              participants: newParticipants
                          });},
        removeSession(index){if(confirm('Hapus sesi?'))this.sessions.splice(index,1);},
        handleTrainerPhoto(event,index){const file=event.target.files[0];if(!file)return;const r=new FileReader();r.onload=(e)=>{this.sessions[index].trainerPreview=e.target.result;this.sessions[index].trainerFileName=file.name;};r.readAsDataURL(file);},
        clearTrainerPhoto(index){this.sessions[index].trainerPreview=null;this.sessions[index].trainerFileName='';},
        handleBannerLeft(event,index){const file=event.target.files[0];if(!file)return;const r=new FileReader();r.onload=(e)=>{this.sessions[index].bannerPreviewLeft=e.target.result;};r.readAsDataURL(file);},
        handleBannerRight(event,index){const file=event.target.files[0];if(!file)return;const r=new FileReader();r.onload=(e)=>{this.sessions[index].bannerPreviewRight=e.target.result;};r.readAsDataURL(file);},
        addDocPhotos(event,index){
            const newFiles=Array.from(event.target.files);
            newFiles.forEach(file=>{
                const isDup=this.sessions[index].docFiles.some(f=>f.name===file.name&&f.size===file.size&&f.lastModified===file.lastModified);
                if(!isDup){this.sessions[index].docFiles.push(file);const r=new FileReader();r.onload=(e)=>{this.sessions[index].docPreviews.push({uid:file.name+'-'+Date.now(),url:e.target.result,name:file.name});};r.readAsDataURL(file);}
            });
            this.$nextTick(()=>{this._rebuildDocInput(index);});event.target.value='';
        },
        removeDocPhoto(index,pi){this.sessions[index].docFiles.splice(pi,1);this.sessions[index].docPreviews.splice(pi,1);this.$nextTick(()=>{this._rebuildDocInput(index);});},
        _rebuildDocInput(index){const sid=this.sessions[index].id;const inp=document.getElementById('doc-input-'+sid);if(!inp)return;const dt=new DataTransfer();this.sessions[index].docFiles.forEach(f=>dt.items.add(f));inp.files=dt.files;}
    }));

    Alpine.data('coverPhotoManager',()=>({
        covers:{1:null,2:null,3:null},coverMain:null,stripFiles:[],stripPreviews:[],
        showPage2: false,
        handleCover(event,n){const f=event.target.files[0];if(!f)return;const r=new FileReader();r.onload=(e)=>{this.covers[n]=e.target.result;};r.readAsDataURL(f);},
        clearCover(n){this.covers[n]=null;const inp=document.getElementById('cover-photo-'+n);if(inp)inp.value='';},
        addStripPhotos(event){
            const newFiles=Array.from(event.target.files);
            newFiles.forEach(file=>{
                const isDup=this.stripFiles.some(f=>f.name===file.name&&f.size===file.size&&f.lastModified===file.lastModified);
                if(!isDup){this.stripFiles.push(file);const r=new FileReader();r.onload=(e)=>{this.stripPreviews.push({uid:file.name+'-'+Date.now(),url:e.target.result});};r.readAsDataURL(file);}
            });
            this.$nextTick(()=>{this._rebuildStripInput();});event.target.value='';
        },
        removeStripPhoto(index){this.stripFiles.splice(index,1);this.stripPreviews.splice(index,1);this.$nextTick(()=>{this._rebuildStripInput();});},
        clearAllStrip(){this.stripFiles=[];this.stripPreviews=[];this.$nextTick(()=>{this._rebuildStripInput();});},
        _rebuildStripInput(){const inp=document.getElementById('cover-strip');if(!inp)return;const dt=new DataTransfer();this.stripFiles.forEach(f=>dt.items.add(f));inp.files=dt.files;}
    }));
});

window.handleAutoNumbering = function(e) {
    if (e.key === 'Enter') {
        const el = e.target;
        const start = el.selectionStart;
        const end = el.selectionEnd;
        const text = el.value;
        const lines = text.substr(0, start).split('\n');
        const lastLine = lines[lines.length - 1];
        
        const matchNum = lastLine.match(/^\s*(\d+)([\.\)]?\s+)(.*)/);
        const matchBul = lastLine.match(/^\s*([-*•])(\s+)(.*)/);

        if (matchNum) {
            e.preventDefault();
            if (matchNum[3].trim() === '') {
                const newText = text.substring(0, start - lastLine.length) + text.substring(end);
                el.value = newText;
                el.selectionStart = el.selectionEnd = start - lastLine.length;
            } else {
                const nextNum = parseInt(matchNum[1]) + 1;
                const insertText = '\n' + nextNum + matchNum[2];
                el.value = text.substring(0, start) + insertText + text.substring(end);
                el.selectionStart = el.selectionEnd = start + insertText.length;
            }
            el.dispatchEvent(new Event('input', { bubbles: true }));
        } else if (matchBul) {
            e.preventDefault();
            if (matchBul[3].trim() === '') {
                const newText = text.substring(0, start - lastLine.length) + text.substring(end);
                el.value = newText;
                el.selectionStart = el.selectionEnd = start - lastLine.length;
            } else {
                const insertText = '\n' + matchBul[1] + matchBul[2];
                el.value = text.substring(0, start) + insertText + text.substring(end);
                el.selectionStart = el.selectionEnd = start + insertText.length;
            }
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
};

window.handleAutoNumberingFocus = function(e) {
    const el = e.target;
    if (el.value.trim() === '') {
        el.value = '1. ';
        el.dispatchEvent(new Event('input', { bubbles: true }));
    }
};
</script>
