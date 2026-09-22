<div x-data="{ isOpen: index === 0 }" class="border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden mb-4 transition-all duration-200">
    <input type="hidden" :name="'sessions['+index+'][showEval]'" :value="session.showEval ? '1' : '0'">
    <input type="hidden" :name="'sessions['+index+'][id]'" :value="session.id">

    {{-- Accordion Header --}}
    <div @click="isOpen = !isOpen" class="flex items-center justify-between px-4 py-3 bg-gray-50/80 border-b border-gray-100 cursor-pointer hover:bg-gray-100 transition">
        <div class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-md bg-white border border-gray-300 text-gray-700 font-bold text-xs flex items-center justify-center shadow-sm" x-text="index + 1"></span>
            <span class="font-bold text-gray-800 text-sm" x-text="session.title || 'Sesi Baru'"></span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click.stop="removeSession(index)" class="text-gray-400 hover:text-red-500 transition p-1.5 rounded-md hover:bg-red-50" title="Hapus Sesi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>
    
    {{-- Accordion Body --}}
    <div x-show="isOpen" x-transition class="p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Judul Sesi</label>
                <input type="text" :name="'sessions['+index+'][title]'" x-model="session.title" placeholder="Boleh dikosongkan (otomatis 'Sesi Pelatihan')" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none bg-white transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Mulai</label>
                <input type="date" :name="'sessions['+index+'][date_start]'" x-model="session.date_start" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none bg-white transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Selesai</label>
                <input type="date" :name="'sessions['+index+'][date_end]'" x-model="session.date_end" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none bg-white transition">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Lokasi</label>
                <input type="text" :name="'sessions['+index+'][location]'" x-model="session.location" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none bg-white transition">
            </div>
            
            {{-- Banner Left Photo --}}
            <div class="p-3 border border-gray-100 bg-gray-50/50 rounded-lg">
                <label class="block text-xs font-semibold text-gray-700 mb-2">Foto Kiri Atas Banner</label>
                <input type="file" :name="'sessions['+index+'][photos][0]'" accept="image/*" @change="handleBannerLeft($event, index)" class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer transition">
                <template x-if="session.bannerPreviewLeft">
                    <div class="mt-2 relative inline-block"><img :src="session.bannerPreviewLeft" class="h-12 object-cover rounded-md border border-gray-200 shadow-sm"></div>
                </template>
                <template x-if="!session.bannerPreviewLeft && session.existing_photos && session.existing_photos[0]">
                    <div class="mt-2 relative inline-block">
                        <img :src="'/storage/' + session.existing_photos[0]" class="h-12 object-cover rounded-md border border-gray-200 shadow-sm">
                        <button type="button" @click="session.existing_photos[0] = null" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full p-0.5 hover:bg-red-600 shadow-md" title="Hapus Foto"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                </template>
                <input type="hidden" :name="'sessions['+index+'][delete_photo_0]'" :value="(session.existing_photos && session.existing_photos[0]) ? '0' : '1'">
            </div>
            
            {{-- Banner Right Photo / Text --}}
            <div class="p-3 border border-gray-100 bg-gray-50/50 rounded-lg">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Teks Kanan Atas Banner <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="text" :name="'sessions['+index+'][banner_right_text]'" x-model="session.banner_right_text" placeholder="Teks pengganti Judul Sesi di banner" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none bg-white mb-3 transition">
                
                <label class="block text-xs font-semibold text-gray-700 mb-2">Atau Gunakan Foto Kanan Atas</label>
                <input type="file" :name="'sessions['+index+'][photos][1]'" accept="image/*" @change="handleBannerRight($event, index)" class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer transition">
                <p class="text-[10px] text-gray-500 mt-1.5 leading-tight">Jika teks dan foto kosong, otomatis menampilkan <b>Judul Sesi</b>.</p>
                <template x-if="session.bannerPreviewRight">
                    <div class="mt-2 relative inline-block"><img :src="session.bannerPreviewRight" class="h-12 object-cover rounded-md border border-gray-200 shadow-sm"></div>
                </template>
                <template x-if="!session.bannerPreviewRight && session.existing_photos && session.existing_photos[1]">
                    <div class="mt-2 relative inline-block">
                        <img :src="'/storage/' + session.existing_photos[1]" class="h-12 object-cover rounded-md border border-gray-200 shadow-sm">
                        <button type="button" @click="session.existing_photos[1] = null" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full p-0.5 hover:bg-red-600 shadow-md" title="Hapus Foto"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                </template>
                <input type="hidden" :name="'sessions['+index+'][delete_photo_1]'" :value="(session.existing_photos && session.existing_photos[1]) ? '0' : '1'">
            </div>
        </div>
        
        {{-- Section Opsional --}}
        <div class="pt-2 border-t border-gray-100 mt-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Bagian Opsional (Aktifkan yang dibutuhkan):</p>
            <div class="flex flex-wrap gap-2">
                <template x-if="!session.showTujuan"><button type="button" @click="session.showTujuan = true" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 shadow-sm transition">+ Tujuan</button></template>
                <template x-if="!session.showHasil"><button type="button" @click="session.showHasil = true" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 shadow-sm transition">+ Hasil</button></template>
                <template x-if="!session.showEval"><button type="button" @click="session.showEval = true" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 shadow-sm transition">+ Evaluasi</button></template>
                <template x-if="!session.showTrainerPhoto"><button type="button" @click="session.showTrainerPhoto = true" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 shadow-sm transition">+ Foto Trainer</button></template>
                <template x-if="!session.showDokumentasi"><button type="button" @click="session.showDokumentasi = true" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 shadow-sm transition">+ Dokumentasi</button></template>
                <template x-if="!session.showPeserta && trainees.length > 0"><button type="button" @click="session.showPeserta = true" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 shadow-sm transition">+ Daftar Hadir</button></template>
                <template x-if="!session.showOjt"><button type="button" @click="session.showOjt = true" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 shadow-sm transition">+ OJT</button></template>
            </div>
        </div>
        
        {{-- Optional Fields Container --}}
        <div class="grid grid-cols-1 gap-4">
            <div x-show="session.showTujuan" x-transition class="p-4 bg-blue-50/50 border border-blue-100 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-blue-800">Tujuan Training</label>
                    <button type="button" @click="session.showTujuan=false" class="text-blue-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <textarea :name="'sessions['+index+'][training_purpose]'" x-model="session.training_purpose" rows="2" style="min-height: 80px;" x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="let resize = () => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'; }; resize(); let ro = new ResizeObserver(() => { if($el.offsetParent !== null) resize(); }); ro.observe($el); $watch('session.training_purpose', resize);" @keydown="window.handleAutoNumbering($event)" @focus="window.handleAutoNumberingFocus($event)" class="w-full px-3 py-2 text-sm border border-blue-200 rounded-lg outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white placeholder-gray-400 overflow-hidden transition" placeholder="Ketik tujuan dari sesi training ini..."></textarea>
            </div>
            
            <div x-show="session.showHasil" x-transition class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-emerald-800">Hasil Training</label>
                    <button type="button" @click="session.showHasil=false" class="text-emerald-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <textarea :name="'sessions['+index+'][training_result]'" x-model="session.training_result" rows="2" style="min-height: 80px;" x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="let resize = () => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'; }; resize(); let ro = new ResizeObserver(() => { if($el.offsetParent !== null) resize(); }); ro.observe($el); $watch('session.training_result', resize);" @keydown="window.handleAutoNumbering($event)" @focus="window.handleAutoNumberingFocus($event)" class="w-full px-3 py-2 text-sm border border-emerald-200 rounded-lg outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 bg-white placeholder-gray-400 overflow-hidden transition" placeholder="Ketik hasil yang didapatkan dari sesi training ini..."></textarea>
            </div>

            <div x-show="session.showEval" x-transition class="p-4 bg-purple-50/50 border border-purple-100 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-bold text-purple-800">Evaluasi Sesi</label>
                    <button type="button" @click="session.showEval=false" class="text-purple-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="overflow-x-auto border border-purple-100 rounded-lg">
                    <table class="w-full text-xs text-left bg-white">
                        <thead class="bg-purple-50 border-b border-purple-100">
                            <tr><th class="px-3 py-2 font-semibold text-purple-800">Skala Penilaian</th><th class="px-3 py-2 font-semibold text-purple-800">Materi</th><th class="px-3 py-2 font-semibold text-purple-800">Trainer</th><th class="px-3 py-2 font-semibold text-purple-800">Fasilitas</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(label, key) in {'baik_sekali':'Baik Sekali','baik':'Baik','cukup_baik':'Cukup Baik','kurang_baik':'Kurang Baik','tidak_baik':'Tidak Baik'}">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-3 py-2 font-medium text-gray-700" x-text="label"></td>
                                    <td class="px-3 py-1.5"><input type="number" min="0" max="100" x-model="session.evaluations[key].materi" :name="'sessions['+index+'][evaluations]['+key+'][materi]'" class="w-16 px-2 py-1 border border-gray-300 rounded text-center outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500"></td>
                                    <td class="px-3 py-1.5"><input type="number" min="0" max="100" x-model="session.evaluations[key].trainer" :name="'sessions['+index+'][evaluations]['+key+'][trainer]'" class="w-16 px-2 py-1 border border-gray-300 rounded text-center outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500"></td>
                                    <td class="px-3 py-1.5"><input type="number" min="0" max="100" x-model="session.evaluations[key].fasilitas" :name="'sessions['+index+'][evaluations]['+key+'][fasilitas]'" class="w-16 px-2 py-1 border border-gray-300 rounded text-center outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="session.showTrainerPhoto" x-transition class="p-4 bg-orange-50/50 border border-orange-100 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-bold text-orange-800">Foto & Nama Trainer (Opsional)</label>
                    <button type="button" @click="session.showTrainerPhoto=false" class="text-orange-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-20 h-20 border-2 border-dashed border-orange-200 rounded-xl bg-white flex items-center justify-center overflow-hidden shrink-0 shadow-sm relative group cursor-pointer" @click="document.getElementById('trainerPhoto_'+session.id).click()">
                        <template x-if="session.trainerPreview">
                            <img :src="session.trainerPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!session.trainerPreview && session.trainerFileName">
                            <img :src="session.trainerFileName" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!session.trainerPreview && !session.trainerFileName">
                            <span class="text-orange-300 flex flex-col items-center">
                                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[9px] font-semibold">Upload</span>
                            </span>
                        </template>
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <span class="text-white text-[10px] font-semibold">Ubah</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <input type="file" :name="'sessions['+index+'][trainer_photo]'" :id="'trainerPhoto_'+session.id" accept="image/*" @change="handleTrainerPhoto($event, index)" class="hidden">
                            <button type="button" @click="document.getElementById('trainerPhoto_'+session.id).click()" class="text-xs font-semibold px-3 py-1.5 border border-orange-200 bg-white text-orange-700 rounded-lg hover:bg-orange-50 transition shadow-sm">Pilih Foto</button>
                            <input type="hidden" :name="'sessions['+index+'][delete_trainer_photo]'" :value="session.trainerPreview ? '0' : '1'">
                        </div>
                        <div>
                            <label class="block text-[10px] text-orange-700/70 font-semibold mb-1 uppercase tracking-wide">Nama Trainer (Tercetak di Bawah Foto)</label>
                            <input type="text" :name="'sessions['+index+'][trainer_name]'" x-model="session.trainer_name" placeholder="Contoh: All Staff PT. SIR" class="w-full px-3 py-2 text-sm border border-orange-200 rounded-lg outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 bg-white transition">
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="session.showDokumentasi" x-transition class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <div class="flex justify-between items-center mb-3">
                    <label class="text-xs font-bold text-gray-700">Galeri Foto Dokumentasi (Bawah)</label>
                    <button type="button" @click="session.showDokumentasi=false" class="text-gray-400 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <input type="file" :name="'sessions['+index+'][dokumentasi][]'" multiple accept="image/*" @change="addDocPhotos($event, index)" class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 cursor-pointer transition mb-3">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <template x-for="(preview, pi) in session.docPreviews" :key="preview.uid">
                        <div class="relative bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm aspect-[4/3] group">
                            <img :src="preview.url" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-2">
                                <select x-model="preview.type" :name="'sessions['+index+'][dokumentasi_types]['+preview.originalIndex+']'" class="w-full bg-white/90 text-[10px] rounded border-0 py-0.5 outline-none font-semibold">
                                    <option value="training">Training</option>
                                    <option value="ojt">OJT</option>
                                </select>
                            </div>
                            <button type="button" @click="removeDocPhoto(index, pi)" class="absolute top-1.5 right-1.5 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-md transform scale-0 group-hover:scale-100 transition-transform"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="session.showPeserta && trainees.length > 0" x-transition class="p-4 bg-sky-50/50 border border-sky-100 rounded-xl">
                <div class="flex justify-between items-center mb-3">
                    <label class="text-xs font-bold text-sky-800">Daftar Kehadiran</label>
                    <button type="button" @click="session.showPeserta=false" class="text-sky-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="max-h-40 overflow-y-auto border border-sky-200 rounded-lg bg-white shadow-inner">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-sky-50 sticky top-0 border-b border-sky-100">
                            <tr><th class="px-3 py-2 font-semibold text-sky-800">Ikut? / Nama</th><th class="px-3 py-2 font-semibold text-sky-800">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="trainee in trainees" :key="trainee.id">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-3 py-2">
                                        <label class="flex items-center gap-2 cursor-pointer text-gray-700 font-medium">
                                            <input type="checkbox" x-model="session.participants[trainee.id].is_participating" :name="'sessions['+index+'][participants]['+trainee.id+'][is_participating]'" value="1" class="rounded text-sky-600 focus:ring-sky-500 w-3.5 h-3.5">
                                            <span x-text="trainee.name"></span>
                                        </label>
                                    </td>
                                    <td class="px-3 py-1.5">
                                        <select x-model="session.participants[trainee.id].participation_type" :disabled="!session.participants[trainee.id].is_participating" :name="'sessions['+index+'][participants]['+trainee.id+'][participation_type]'" class="w-full border border-gray-300 rounded bg-white text-xs py-1 px-2 outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 disabled:bg-gray-100 disabled:text-gray-400">
                                            <option value="aktif">Hadir Aktif</option>
                                            <option value="pasif">Hadir Pasif</option>
                                            <option value="absen">Tidak Hadir</option>
                                        </select>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="session.showOjt" x-transition class="p-4 bg-teal-50/50 border border-teal-100 rounded-xl">
                <div class="flex justify-between items-center mb-3">
                    <label class="text-xs font-bold text-teal-800">Lokasi OJT</label>
                    <button type="button" @click="session.showOjt=false" class="text-teal-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <template x-if="trainees.length === 0">
                    <p class="text-xs text-red-500 italic py-2">Pilih Program Pelatihan terlebih dahulu untuk memunculkan tabel lokasi OJT.</p>
                </template>
                <template x-if="trainees.length > 0">
                    <div class="max-h-40 overflow-y-auto border border-teal-200 rounded-lg bg-white shadow-inner">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-teal-50 sticky top-0 border-b border-teal-100">
                                <tr><th class="px-3 py-2 font-semibold text-teal-800">Nama</th><th class="px-3 py-2 font-semibold text-teal-800">Kode</th><th class="px-3 py-2 font-semibold text-teal-800">Region</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="trainee in trainees" :key="trainee.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-3 py-2 font-medium text-gray-700" x-text="trainee.name"></td>
                                        <td class="px-3 py-1.5"><input type="text" x-model="session.ojt[trainee.id].location_code" :name="'sessions['+index+'][ojt]['+trainee.id+'][location_code]'" class="w-full border border-gray-300 rounded bg-white px-2 py-1 text-xs outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                                        <td class="px-3 py-1.5"><input type="text" x-model="session.ojt[trainee.id].region" :name="'sessions['+index+'][ojt]['+trainee.id+'][region]'" class="w-full border border-gray-300 rounded bg-white px-2 py-1 text-xs outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
