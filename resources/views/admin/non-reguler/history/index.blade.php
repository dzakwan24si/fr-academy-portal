<x-admin-layout>
    <x-slot name="title">Riwayat Pelatihan</x-slot>
    <x-slot name="header">Riwayat Pelatihan</x-slot>
    <x-slot name="headerSub">Daftar semua pelatihan yang sudah selesai.</x-slot>

    <!-- Filter Section -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            <span class="font-semibold text-gray-700">Filter Riwayat</span>
        </div>
        <form action="{{ route('training-non-reguler.history.index') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <select name="program_id" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-64 p-2.5">
                <option value="">Semua Kategori Pelatihan</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Materi & Program</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Pelaksanaan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl Selesai</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Trainer</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pastClasses as $class)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-gray-800">{{ $class->title }}</p>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        Selesai
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $class->trainingProgram->name ?? 'Tanpa Program' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium border border-gray-200">
                                    {{ $class->date ? \Carbon\Carbon::parse($class->date)->format('d M Y') : '-' }}
                                    @if($class->end_date && $class->end_date != $class->date)
                                        - {{ \Carbon\Carbon::parse($class->end_date)->format('d M Y') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-md text-xs font-semibold border border-emerald-200">
                                    {{ $class->completed_at ? $class->completed_at->format('d M Y H:i') : ($class->end_date ? \Carbon\Carbon::parse($class->end_date)->format('d M Y') : '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700">{{ $class->trainer_name ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('training-non-reguler.daily-classes.show', $class->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors border border-blue-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Detail
                                    </a>
                                    <button type="button" onclick="openRestoreModal('{{ route('training-non-reguler.daily-classes.toggle-completed', $class->id) }}', '{{ addslashes($class->title) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-semibold hover:bg-amber-100 transition-colors border border-amber-200" title="Kembalikan ke materi aktif">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Aktifkan Kembali
                                    </button>
                                    <button type="button" onclick="openDeleteModal('{{ route('training-non-reguler.daily-classes.destroy', $class->id) }}', '{{ addslashes($class->title) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors border border-red-200" title="Hapus riwayat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="font-medium">Belum ada riwayat pelatihan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Aktifkan Kembali -->
    <div id="restoreModal" class="fixed inset-0 z-[120] flex items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm transition-all p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all text-center p-8 relative">
            <button type="button" onclick="document.getElementById('restoreModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors p-1.5 rounded-full hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Icon Undo -->
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center mx-auto mb-5 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>

            <h3 class="text-xl font-black text-slate-800 mb-2">Kembalikan ke Sesi Aktif?</h3>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">
                Materi <strong id="restoreClassTitle" class="text-slate-800"></strong> akan dikembalikan dari Riwayat Pelatihan ke daftar materi berjalan yang aktif.
            </p>

            <form id="restoreForm" action="" method="POST" class="flex gap-3">
                @csrf
                <button type="button" onclick="document.getElementById('restoreModal').classList.add('hidden')" class="flex-1 px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-5 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold shadow-lg shadow-amber-600/30 active:scale-95 transition-all">
                    Ya, Aktifkan
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Riwayat -->
    <div id="deleteModal" class="fixed inset-0 z-[120] flex items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm transition-all p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all text-center p-8 relative">
            <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors p-1.5 rounded-full hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Icon Red Trash -->
            <div class="w-16 h-16 rounded-2xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center mx-auto mb-5 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>

            <h3 class="text-xl font-black text-slate-800 mb-2">Hapus Riwayat Pelatihan?</h3>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">
                Data riwayat untuk materi <strong id="deleteClassTitle" class="text-slate-800"></strong> beserta seluruh catatan absensi dan evaluasi di dalamnya akan dihapus secara permanen.
            </p>

            <form id="deleteForm" action="" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="flex-1 px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-5 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold shadow-lg shadow-red-600/30 active:scale-95 transition-all">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

    <script>
        function openRestoreModal(actionUrl, classTitle) {
            document.getElementById('restoreForm').action = actionUrl;
            document.getElementById('restoreClassTitle').textContent = `"${classTitle}"`;
            document.getElementById('restoreModal').classList.remove('hidden');
        }

        function openDeleteModal(actionUrl, classTitle) {
            document.getElementById('deleteForm').action = actionUrl;
            document.getElementById('deleteClassTitle').textContent = `"${classTitle}"`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
    </script>
</x-admin-layout>
