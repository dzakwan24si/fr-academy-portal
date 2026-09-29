<x-admin-layout>
    <x-slot name="title">Trainer</x-slot>
    <x-slot name="header">Daftar Trainer</x-slot>
    <x-slot name="headerSub">Kelola data pelatih (Trainer).</x-slot>

    <!-- Modal Component Wrapper -->
    <div x-data="crudModalTrainer('/training-reguler/trainers')">
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 mt-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-500 font-medium">Total Trainer</p>
                <p class="text-xl font-bold text-gray-800">{{ $total }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Data Trainer</h3>
                <button @click="openModal()" class="px-4 py-1.5 bg-[#28844b] text-white text-sm font-bold rounded-lg hover:bg-green-700 transition shadow-sm">
                    Tambah Trainer
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Trainer</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($trainers as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex gap-3">
                                    <button @click="openModal({{ $item->toJson() }})" class="text-blue-600 hover:underline font-semibold">Edit</button>
                                    <form action="{{ route('training-reguler.trainers.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-semibold">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-6 py-8 text-center text-gray-400">Belum ada data trainer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trainers->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $trainers->links() }}
            </div>
            @endif
        </div>

        <!-- Modal Component -->
        <x-admin-modal show="isOpen" titleHtml="x-text=&quot;isEdit ? 'Edit Data Trainer' : 'Tambah Trainer Baru'&quot;" subtitle="Lengkapi formulir di bawah ini" maxWidth="4xl">
            <form :action="formAction" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="mb-4">
                    <h4 class="text-[#28844b] font-bold text-sm tracking-wide mb-4">1. DATA UTAMA</h4>
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="formData.name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-1 focus:ring-[#28844b] focus:border-[#28844b] outline-none">
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
            Alpine.data('crudModalTrainer', (baseUrl) => ({
                isOpen: false,
                isEdit: false,
                baseUrl: baseUrl,
                formAction: '',
                formData: {
                    name: ''
                },
                openModal(data = null) {
                    this.isOpen = true;
                    if (data) {
                        this.isEdit = true;
                        this.formAction = this.baseUrl + '/' + data.id;
                        this.formData = { ...data };
                    } else {
                        this.isEdit = false;
                        this.formAction = this.baseUrl;
                        this.formData = {
                            name: ''
                        };
                    }
                }
            }));
        });
    </script>
</x-admin-layout>