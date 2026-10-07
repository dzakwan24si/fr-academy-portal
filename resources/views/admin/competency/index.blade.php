<x-admin-layout>
    <x-slot name="title">Standar Kompetensi - Kelola Kamus</x-slot>
    <x-slot name="header">Kelola Kamus Kompetensi</x-slot>
    <x-slot name="headerSub">Daftar kamus standar kompetensi teknis Estate & Mill/PKS.</x-slot>
    <x-slot name="headerAction">
        <button type="button" @click="$dispatch('open-create-modal')" class="px-5 py-2.5 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm inline-flex items-center text-sm">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Kamus Baru
        </button>
    </x-slot>

    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showCopyModal: false,
        editId: null,
        editName: '',
        editYear: '',
        editDescription: '',
        editAction: '',
        copyId: null,
        copyName: '',
        copyYear: '',
        copyAction: ''
    }" @open-create-modal.window="showCreateModal = true">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-[#509c38] p-4 rounded-r-xl shadow-sm">
                <div class="flex items-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-[#509c38] mr-3"></i>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                <div class="flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-3"></i>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-4">Jenis (Code)</th>
                            <th class="px-6 py-4">Nama Kamus</th>
                            <th class="px-6 py-4">Tahun</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Jabatan / Kompetensi</th>
                            <th class="px-6 py-4">Terakhir Diubah</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($dictionaries as $dict)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-gray-700">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $dict->code === 'estate' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ strtoupper($dict->code) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900">{{ $dict->name }}</p>
                                    @if($dict->description)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $dict->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    {{ $dict->year }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($dict->isPublished())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1.5"></span> Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span> Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <span class="font-semibold text-gray-900">{{ $dict->positions_count }}</span> jabatan, 
                                    <span class="font-semibold text-gray-900">{{ $dict->competencies_count }}</span> kompetensi
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    {{ $dict->updated_at ? $dict->updated_at->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Kelola / Detail -->
                                        <a href="{{ route('admin.competency.show', $dict) }}" class="px-3 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 font-semibold rounded-lg text-xs transition flex items-center gap-1">
                                            <i data-lucide="layers" class="w-3.5 h-3.5"></i> Kelola
                                        </a>

                                        <!-- Ubah Info -->
                                        <button type="button"
                                                @click="
                                                    editId = {{ $dict->id }};
                                                    editName = '{{ addslashes($dict->name) }}';
                                                    editYear = '{{ $dict->year }}';
                                                    editDescription = '{{ addslashes((string)$dict->description) }}';
                                                    editAction = '{{ route('admin.competency.update', $dict) }}';
                                                    showEditModal = true;
                                                "
                                                class="px-2.5 py-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg text-xs font-medium transition" title="Ubah Info">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>

                                        <!-- Publish / Unpublish -->
                                        <form action="{{ route('admin.competency.toggle_publish', $dict) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg transition border {{ $dict->isPublished() ? 'border-amber-300 text-amber-700 hover:bg-amber-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}"
                                                    title="{{ $dict->isPublished() ? 'Ubah ke Draft' : 'Publikasikan' }}">
                                                {{ $dict->isPublished() ? 'Unpublish' : 'Publish' }}
                                            </button>
                                        </form>

                                        <!-- Salin ke Tahun Baru -->
                                        <button type="button"
                                                @click="
                                                    copyId = {{ $dict->id }};
                                                    copyName = '{{ addslashes($dict->name) }}';
                                                    copyYear = '{{ $dict->year }}';
                                                    copyAction = '{{ route('admin.competency.copy', $dict) }}';
                                                    showCopyModal = true;
                                                "
                                                class="px-2.5 py-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-xs font-medium transition" title="Salin ke Tahun Baru">
                                            <i data-lucide="copy" class="w-4 h-4"></i>
                                        </button>

                                        <!-- Hapus (khusus Draft) -->
                                        @if(!$dict->isPublished())
                                            <form action="{{ route('admin.competency.destroy', $dict) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft kamus kompetensi {{ addslashes($dict->name) }} {{ $dict->year }}? Seluruh jabatan dan kompetensi di dalamnya akan terhapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg text-xs font-medium transition" title="Hapus Draft">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="px-2.5 py-1.5 text-gray-300 cursor-not-allowed text-xs" title="Published tidak boleh dihapus">&#128711;</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                                    <p class="font-semibold">Belum ada kamus kompetensi.</p>
                                    <p class="text-xs text-gray-400 mt-1">Klik "Tambah Kamus Baru" untuk mulai membuat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Kamus -->
        <x-admin-modal show="showCreateModal" title="Tambah Kamus Kompetensi Baru" headerBg="bg-[#509c38]">
            <form action="{{ route('admin.competency.store') }}" method="POST" class="space-y-5">
                @csrf
                <div x-data="{ codeType: 'estate', customCode: '' }">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kamus (Code)</label>
                    <select x-model="codeType" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                        <option value="estate">Estate (Perkebunan)</option>
                        <option value="mill">Mill (Pabrik Kelapa Sawit)</option>
                        <option value="ho">HO (Head Office / Kantor Pusat)</option>
                        <option value="custom">+ Tambah Kategori / Code Baru...</option>
                    </select>

                    <div x-show="codeType === 'custom'" x-cloak class="mt-2.5 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Slug Kategori Baru</label>
                        <input type="text" x-model="customCode" placeholder="Contoh: logistik, finance, hr" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-3 py-2 text-sm font-mono lowercase">
                        <p class="text-[11px] text-gray-500 mt-1">Gunakan huruf kecil, angka, atau garis hubung (tanpa spasi), contoh: <code>logistik</code> atau <code>head-office</code>.</p>
                    </div>

                    <input type="hidden" name="code" :value="codeType === 'custom' ? customCode.toLowerCase().replace(/[^a-z0-9_-]/g, '') : codeType">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kamus</label>
                    <input type="text" name="name" placeholder="Contoh: Estate Technical Competency Dictionary" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Edisi</label>
                    <input type="number" name="year" value="{{ date('Y') }}" min="2020" max="2100" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" placeholder="Penjelasan singkat mengenai dokumen/edisi ini..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-[#509c38] hover:bg-green-700 rounded-xl transition shadow-sm">Simpan Draft</button>
                </div>
            </form>
        </x-admin-modal>

        <!-- Modal Edit Info -->
        <x-admin-modal show="showEditModal" title="Ubah Info Kamus Kompetensi" headerBg="bg-gray-700">
            <form :action="editAction" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kamus</label>
                    <input type="text" name="name" x-model="editName" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Edisi</label>
                    <input type="number" name="year" x-model="editYear" min="2020" max="2100" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" x-model="editDescription" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-2.5 text-sm"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-gray-800 hover:bg-gray-900 rounded-xl transition shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </x-admin-modal>

        <!-- Modal Salin ke Tahun Baru -->
        <x-admin-modal show="showCopyModal" title="Salin Kamus ke Tahun Baru" headerBg="bg-blue-600">
            <form :action="copyAction" method="POST" class="space-y-5">
                @csrf
                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r-lg text-xs text-blue-800">
                    Proses ini akan menyalin seluruh <strong>jabatan, kompetensi, matriks level, dan butir indikator</strong> dari edisi <span x-text="copyYear"></span> ke tahun edisi baru. Hasil salinan berstatus <strong>Draft</strong>.
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun Target Baru</label>
                    <input type="number" name="target_year" :value="parseInt(copyYear) + 1" min="2020" max="2100" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 px-4 py-2.5 text-sm" required>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showCopyModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-sm">Salin Kamus</button>
                </div>
            </form>
        </x-admin-modal>
    </div>
</x-admin-layout>
