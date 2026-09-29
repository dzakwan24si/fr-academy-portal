@php
    $programsJson = \App\Models\TrainingProgram::all()->toJson();
    $trainersJson = \App\Models\Trainer::all()->toJson();
@endphp
<x-admin-layout>
    <x-slot name="title">MOA</x-slot>
    <x-slot name="header">Daftar MOA</x-slot>
    <x-slot name="headerSub">Kelola data MOA.</x-slot>

    <!-- Modal Component Wrapper -->
    <div x-data="crudModalMoa('/training-reguler/moas', {{ $programsJson }}, {{ $trainersJson }})">
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 mt-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 font-medium">Total MOA</p>
                <p class="text-xl font-bold text-gray-800">{{ $total }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 font-medium">Draft</p>
                <p class="text-xl font-bold text-yellow-600">{{ $draft }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 font-medium">Submitted</p>
                <p class="text-xl font-bold text-green-600">{{ $submitted }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Data MOA</h3>
                <button @click="openModal()" class="px-4 py-1.5 bg-[#28844b] text-white text-sm font-bold rounded-lg hover:bg-green-700 transition shadow-sm">
                    Tambah MOA
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Program Training</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Trainer</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Periode</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($moas as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $item->trainingProgram->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $item->trainer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->week_start_date }} s/d {{ $item->week_end_date }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->status == 'submitted' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-3">
                                    <button @click="openModal({{ $item->toJson() }})" class="text-blue-600 hover:underline font-semibold">Edit</button>
                                    <form action="{{ route('training-reguler.moas.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-semibold">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data MOA.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($moas->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $moas->links() }}
            </div>
            @endif
        </div>

        <!-- Modal Component -->
        <x-admin-modal show="isOpen" titleHtml="x-text=&quot;isEdit ? 'Edit Data MOA' : 'Tambah MOA Baru'&quot;" subtitle="Lengkapi formulir di bawah ini" maxWidth="4xl">
            <form :action="formAction" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="mb-4">
                    <h4 class="text-[#28844b] font-bold text-sm tracking-wide mb-4">1. DATA UTAMA</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Program Training <span class="text-red-500">*</span></label>
                            <select name="training_program_id" x-model="formData.training_program_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none bg-white">
                                <option value="">-- Pilih Program --</option>
                                <template x-for="p in programs" :key="p.id">
                                    <option :value="p.id" x-text="p.name + ' (' + p.year + ')'" :selected="formData.training_program_id == p.id"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Trainer <span class="text-red-500">*</span></label>
                            <select name="trainer_id" x-model="formData.trainer_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none bg-white">
                                <option value="">-- Pilih Trainer --</option>
                                <template x-for="t in trainers" :key="t.id">
                                    <option :value="t.id" x-text="t.name" :selected="formData.trainer_id == t.id"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="week_start_date" x-model="formData.week_start_date" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="week_end_date" x-model="formData.week_end_date" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Status MOA <span class="text-red-500">*</span></label>
                            <select name="status" x-model="formData.status" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none bg-white">
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4 mt-6">
                    <h4 class="text-[#28844b] font-bold text-sm tracking-wide mb-4">2. KETERANGAN</h4>
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan</label>
                            <textarea name="notes" x-model="formData.notes" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="isOpen = false" class="px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-[#28844b] text-white font-bold rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </x-admin-modal>

    </div>

    <!-- AlpineJS CRUD Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('crudModalMoa', (baseUrl, programs, trainers) => ({
                isOpen: false,
                isEdit: false,
                baseUrl: baseUrl,
                programs: programs,
                trainers: trainers,
                formAction: '',
                formData: {
                    training_program_id: '',
                    trainer_id: '',
                    week_start_date: '',
                    week_end_date: '',
                    status: 'draft',
                    notes: ''
                },
                openModal(data = null) {
                    this.isOpen = true;
                    if (data) {
                        this.isEdit = true;
                        this.formAction = this.baseUrl + '/' + data.id;
                        this.formData = { ...data };
                        // Convert dates if needed, usually Laravel passes YYYY-MM-DD strings so it should bind correctly
                    } else {
                        this.isEdit = false;
                        this.formAction = this.baseUrl;
                        this.formData = {
                            training_program_id: '',
                            trainer_id: '',
                            week_start_date: '',
                            week_end_date: '',
                            status: 'draft',
                            notes: ''
                        };
                    }
                }
            }));
        });
    </script>
</x-admin-layout>