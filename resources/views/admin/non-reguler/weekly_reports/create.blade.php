<x-admin-layout>
    <x-slot name="title">Buat Laporan Mingguan</x-slot>
    <x-slot name="header">Buat Laporan Mingguan</x-slot>
    <x-slot name="headerSub">Isi form di bawah ini untuk melaporkan aktivitas mingguan, absensi, dan nilai trainee.</x-slot>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada isian form:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" 
         x-data="weeklyReportForm({{ $programs->toJson() }})">
        <form action="{{ route('training-non-reguler.weekly-reports.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf

            <!-- Jika tidak ada trainer yang terdeteksi, kita butuh trainer fallback (sudah diatasi di controller) -->
            @if($trainer)
                <input type="hidden" name="trainer_id" value="{{ $trainer->id }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Training Program -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Program Pelatihan</label>
                    <select name="training_program_id" x-model="selectedProgramId" @change="updateTrainees" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                        <option value="">-- Pilih Program --</option>
                        <template x-for="program in programs" :key="program.id">
                            <option :value="program.id" x-text="program.name + ' (' + program.year + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Week Start Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai (Minggu Ini)</label>
                    <input type="date" name="week_start_date" value="{{ old('week_start_date') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>

                <!-- Week End Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai (Minggu Ini)</label>
                    <input type="date" name="week_end_date" value="{{ old('week_end_date') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>
                    <div class="md:col-span-2 mt-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-1.5">Tema Laporan</label>
                        <select name="theme" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#28844b]/30 focus:border-[#28844b] outline-none transition bg-gray-50 hover:bg-white cursor-pointer">
                            <option value="reguler" >Tema Reguler (Biru)</option>
                            <option value="kemerdekaan" >Tema Kemerdekaan (Merah Putih)</option>
                        </select>
                    </div>

                <!-- Training Purpose -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tujuan Pelatihan (Minggu Ini)</label>
                    <textarea name="training_purpose" rows="3" onkeydown="if(window.handleAutoNumbering) window.handleAutoNumbering(event)" onfocus="if(window.handleAutoNumberingFocus) window.handleAutoNumberingFocus(event)" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" required placeholder="Jelaskan tujuan atau ringkasan aktivitas minggu ini...">{{ old('training_purpose') }}</textarea>
                </div>

                <!-- Quote (Optional) -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Quote Inspiratif (Opsional)</label>
                    <input type="text" name="quote" value="{{ old('quote') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Kutipan penyemangat untuk minggu ini...">
                </div>

                <!-- Photos Upload -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Dokumentasi (Bisa lebih dari satu)</label>
                    <input type="file" name="photos[]" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 p-2 bg-gray-50 text-gray-700">
                    <p class="text-xs text-gray-500 mt-2">Maks. 2MB per foto. Format: JPEG, PNG, JPG.</p>
                </div>
            </div>

            <!-- Trainees Section -->
            <div x-show="trainees.length > 0" style="display: none;" class="mt-10">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-6">Daftar Trainee & Evaluasi</h3>
                
                <div class="overflow-x-auto border rounded-xl">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 font-semibold text-sm text-gray-600">Nama Trainee</th>
                                <th class="p-4 font-semibold text-sm text-gray-600 text-center">Hadir</th>
                                <th class="p-4 font-semibold text-sm text-gray-600">Nilai Praktik</th>
                                <th class="p-4 font-semibold text-sm text-gray-600">Nilai Kelas</th>
                                <th class="p-4 font-semibold text-sm text-gray-600">Partisipasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="trainee in trainees" :key="trainee.id">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4">
                                        <span x-text="trainee.name" class="font-medium text-gray-800"></span>
                                        <p class="text-xs text-gray-500" x-text="trainee.major + ' - ' + trainee.university"></p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <input type="checkbox" :name="'trainees[' + trainee.id + '][is_present]'" value="1" checked 
                                            class="w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500 cursor-pointer">
                                    </td>
                                    <td class="p-4">
                                        <input type="number" step="0.01" min="0" max="100" :name="'trainees[' + trainee.id + '][practice_score]'" 
                                            class="w-24 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="0 - 100" required>
                                    </td>
                                    <td class="p-4">
                                        <input type="number" step="0.01" min="0" max="100" :name="'trainees[' + trainee.id + '][in_class_score]'" 
                                            class="w-24 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="0 - 100" required>
                                    </td>
                                    <td class="p-4">
                                        <select :name="'trainees[' + trainee.id + '][participation_type]'" class="w-32 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                            <option value="aktif">Aktif</option>
                                            <option value="pasif">Pasif</option>
                                        </select>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State for Trainees -->
            <div x-show="selectedProgramId !== '' && trainees.length === 0" style="display: none;" class="mt-6 p-6 text-center bg-gray-50 rounded-xl border border-gray-200 text-gray-500">
                Program ini belum memiliki trainee. Silakan pilih program lain atau tambahkan trainee terlebih dahulu.
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('training-non-reguler.weekly-reports.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm">
                    Simpan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- AlpineJS Script for Form Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('weeklyReportForm', (initialPrograms) => ({
                programs: initialPrograms,
                selectedProgramId: '',
                trainees: [],
                
                updateTrainees() {
                    if (this.selectedProgramId) {
                        const selectedProgram = this.programs.find(p => p.id == this.selectedProgramId);
                        this.trainees = selectedProgram ? selectedProgram.trainees : [];
                    } else {
                        this.trainees = [];
                    }
                }
            }))
        })

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
</x-admin-layout>
