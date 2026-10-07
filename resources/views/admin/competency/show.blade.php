<x-admin-layout>
    <x-slot name="title">Kelola Kamus - {{ $dictionary->name }} {{ $dictionary->year }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span>{{ $dictionary->name }}</span>
            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold uppercase {{ $dictionary->code === 'estate' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                {{ $dictionary->code }}
            </span>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $dictionary->isPublished() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ strtoupper($dictionary->status) }} ({{ $dictionary->year }})
            </span>
        </div>
    </x-slot>
    <x-slot name="headerSub">Kelola jabatan dan daftar kompetensi teknis untuk edisi ini.</x-slot>
    <x-slot name="headerAction">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.competency.index') }}" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm inline-flex items-center text-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
            </a>
            @if($dictionary->isPublished())
                <a href="{{ route('competency.public', ['code' => $dictionary->code, 'tahun' => $dictionary->year]) }}" target="_blank" class="px-4 py-2.5 bg-teal-50 text-teal-700 font-semibold rounded-xl hover:bg-teal-100 transition shadow-sm inline-flex items-center text-sm">
                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i> Pratinjau Publik
                </a>
            @endif
        </div>
    </x-slot>

    <div x-data="{
        showAddPosModal: false,
        showEditPosModal: false,
        showAddCompModal: false,
        editPosId: null,
        editPosName: '',
        editPosAction: ''
    }" class="space-y-8">

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-[#509c38] p-4 rounded-r-xl shadow-sm">
                <div class="flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-[#509c38] mr-3"></i>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                <div class="flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-3"></i>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- SECTION 1: DAFTAR JABATAN -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Jabatan ({{ $dictionary->positions->count() }})</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Jabatan yang dievaluasi pada kamus edisi {{ $dictionary->year }}.</p>
                </div>
                <button type="button" @click="showAddPosModal = true" class="px-4 py-2 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm inline-flex items-center text-xs">
                    <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i> Tambah Jabatan
                </button>
            </div>

            <div class="p-6">
                @if($dictionary->positions->isEmpty())
                    <p class="text-sm text-gray-500 italic text-center py-4">Belum ada jabatan di kamus ini.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($dictionary->positions as $index => $pos)
                            <div class="flex items-center justify-between bg-gray-50/80 border border-gray-200 rounded-xl px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 text-xs font-bold flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-bold text-gray-800 text-sm">{{ $pos->name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <!-- Reorder Up -->
                                    @if($index > 0)
                                        <form action="{{ route('admin.competency.positions.reorder', $dictionary) }}" method="POST" class="inline">
                                            @csrf
                                            @php
                                                $posIds = $dictionary->positions->pluck('id')->all();
                                                $temp = $posIds[$index];
                                                $posIds[$index] = $posIds[$index - 1];
                                                $posIds[$index - 1] = $temp;
                                            @endphp
                                            @foreach($posIds as $pid)
                                                <input type="hidden" name="position_ids[]" value="{{ $pid }}">
                                            @endforeach
                                            <button type="submit" class="p-1 text-gray-400 hover:text-gray-700 rounded transition" title="Naikkan">
                                                <i data-lucide="arrow-up" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Reorder Down -->
                                    @if($index < $dictionary->positions->count() - 1)
                                        <form action="{{ route('admin.competency.positions.reorder', $dictionary) }}" method="POST" class="inline">
                                            @csrf
                                            @php
                                                $posIds = $dictionary->positions->pluck('id')->all();
                                                $temp = $posIds[$index];
                                                $posIds[$index] = $posIds[$index + 1];
                                                $posIds[$index + 1] = $temp;
                                            @endphp
                                            @foreach($posIds as $pid)
                                                <input type="hidden" name="position_ids[]" value="{{ $pid }}">
                                            @endforeach
                                            <button type="submit" class="p-1 text-gray-400 hover:text-gray-700 rounded transition" title="Turunkan">
                                                <i data-lucide="arrow-down" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit Name -->
                                    <button type="button"
                                            @click="
                                                editPosId = {{ $pos->id }};
                                                editPosName = '{{ addslashes($pos->name) }}';
                                                editPosAction = '{{ route('admin.competency.positions.update', $pos) }}';
                                                showEditPosModal = true;
                                            "
                                            class="p-1 text-gray-500 hover:text-gray-800 rounded transition" title="Ubah Nama">
                                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.competency.positions.destroy', $pos) }}" method="POST" class="inline" onsubmit="return confirm('Menghapus jabatan ini akan menghapus semua uraian level terkait. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-red-500 hover:text-red-700 rounded transition" title="Hapus Jabatan">
                                            <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION 2: DAFTAR KOMPETENSI -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Kompetensi Teknis ({{ $dictionary->competencies->count() }})</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Kompetensi yang didefinisikan dan dinilai untuk setiap jabatan.</p>
                </div>
                <button type="button" @click="showAddCompModal = true" class="px-4 py-2 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm inline-flex items-center text-xs">
                    <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i> Tambah Kompetensi
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-4 w-16">No</th>
                            <th class="px-6 py-4">Nama Kompetensi & Definisi</th>
                            <th class="px-6 py-4">Kelengkapan Data</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($dictionary->competencies as $index => $comp)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 text-base">{{ $comp->name }}</p>
                                    @if($comp->definition)
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ $comp->definition }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    @php
                                        $expectedLevels = $dictionary->positions->count() * 4;
                                        $hasAllLevels = $expectedLevels > 0 && $comp->levels_count >= $expectedLevels;
                                    @endphp
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $hasAllLevels ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $comp->levels_count }} / {{ $expectedLevels }} sel level
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $comp->items_count }} butir indikator
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Detail Kompetensi -->
                                        <a href="{{ route('admin.competency.competencies.edit', $comp) }}" class="px-3 py-1.5 bg-[#509c38] text-white hover:bg-green-700 font-bold rounded-lg text-xs transition inline-flex items-center gap-1">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Uraian
                                        </a>

                                        <!-- Reorder Up -->
                                        @if($index > 0)
                                            <form action="{{ route('admin.competency.competencies.reorder', $dictionary) }}" method="POST" class="inline">
                                                @csrf
                                                @php
                                                    $compIds = $dictionary->competencies->pluck('id')->all();
                                                    $temp = $compIds[$index];
                                                    $compIds[$index] = $compIds[$index - 1];
                                                    $compIds[$index - 1] = $temp;
                                                @endphp
                                                @foreach($compIds as $cid)
                                                    <input type="hidden" name="competency_ids[]" value="{{ $cid }}">
                                                @endforeach
                                                <button type="submit" class="p-1 text-gray-400 hover:text-gray-700 rounded transition" title="Naikkan">
                                                    <i data-lucide="arrow-up" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Reorder Down -->
                                        @if($index < $dictionary->competencies->count() - 1)
                                            <form action="{{ route('admin.competency.competencies.reorder', $dictionary) }}" method="POST" class="inline">
                                                @csrf
                                                @php
                                                    $compIds = $dictionary->competencies->pluck('id')->all();
                                                    $temp = $compIds[$index];
                                                    $compIds[$index] = $compIds[$index + 1];
                                                    $compIds[$index + 1] = $temp;
                                                @endphp
                                                @foreach($compIds as $cid)
                                                    <input type="hidden" name="competency_ids[]" value="{{ $cid }}">
                                                @endforeach
                                                <button type="submit" class="p-1 text-gray-400 hover:text-gray-700 rounded transition" title="Turunkan">
                                                    <i data-lucide="arrow-down" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Hapus Kompetensi -->
                                        <form action="{{ route('admin.competency.competencies.destroy', $comp) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kompetensi {{ addslashes($comp->name) }} beserta seluruh level dan butir indikatornya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-red-600 hover:text-red-800 rounded transition" title="Hapus Kompetensi">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                                    <p class="font-semibold">Belum ada kompetensi di kamus ini.</p>
                                    <p class="text-xs text-gray-400 mt-1">Klik "Tambah Kompetensi" untuk membuat entry pertama.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Jabatan -->
        <x-admin-modal show="showAddPosModal" title="Tambah Jabatan Baru" headerBg="bg-[#509c38]">
            <form action="{{ route('admin.competency.positions.store', $dictionary) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Jabatan</label>
                    <input type="text" name="name" placeholder="Contoh: Asisten Kebun" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showAddPosModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-[#509c38] hover:bg-green-700 rounded-xl transition shadow-sm">Simpan Jabatan</button>
                </div>
            </form>
        </x-admin-modal>

        <!-- Modal Edit Jabatan -->
        <x-admin-modal show="showEditPosModal" title="Ubah Nama Jabatan" headerBg="bg-gray-700">
            <form :action="editPosAction" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Jabatan</label>
                    <input type="text" name="name" x-model="editPosName" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showEditPosModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-gray-800 hover:bg-gray-900 rounded-xl transition shadow-sm">Simpan</button>
                </div>
            </form>
        </x-admin-modal>

        <!-- Modal Tambah Kompetensi -->
        <x-admin-modal show="showAddCompModal" title="Tambah Kompetensi Baru" headerBg="bg-[#509c38]">
            <form action="{{ route('admin.competency.competencies.store', $dictionary) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kompetensi</label>
                    <input type="text" name="name" placeholder="Contoh: Replanting & Pembukaan Lahan" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Definisi Kompetensi (Opsional)</label>
                    <textarea name="definition" rows="3" placeholder="Penjelasan definisi kompetensi ini..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showAddCompModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-[#509c38] hover:bg-green-700 rounded-xl transition shadow-sm">Lanjut ke Edit Uraian</button>
                </div>
            </form>
        </x-admin-modal>
    </div>
</x-admin-layout>
