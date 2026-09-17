<x-admin-layout>
    <x-slot name="title">Histori Aktivitas Sistem | Audit Log</x-slot>
    <x-slot name="header">Histori Aktivitas Sistem</x-slot>

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
        <div>
            <h1 class="text-2xl font-bold text-[#18181B] tracking-tight flex items-center gap-2">
                <i data-lucide="history" class="w-6 h-6 text-[#509c38]"></i>
                Histori Aktivitas & Audit Log
            </h1>
            <p class="text-sm text-[#71717A] mt-0.5">
                Dokumentasi dan jejak audit aktivitas pengguna di seluruh modul sistem (Tambah, Edit, Hapus, Import).
            </p>
        </div>
    </div>

    {{-- STATS SUMMARY --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 mb-6">
        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-[#71717A] font-medium">Hari Ini</p>
                    <p class="text-xl font-bold text-[#18181B] mt-0.5">{{ number_format($stats['total_today']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-[#71717A] font-medium">Tambah Data</p>
                    <p class="text-xl font-bold text-[#18181B] mt-0.5">{{ number_format($stats['total_tambah']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-[#71717A] font-medium">Edit Data</p>
                    <p class="text-xl font-bold text-[#18181B] mt-0.5">{{ number_format($stats['total_edit']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-[#71717A] font-medium">Hapus Data</p>
                    <p class="text-xl font-bold text-[#18181B] mt-0.5">{{ number_format($stats['total_hapus']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i data-lucide="file-up" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-[#71717A] font-medium">Import Data</p>
                    <p class="text-xl font-bold text-[#18181B] mt-0.5">{{ number_format($stats['total_import']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 mb-6 shadow-2xs">
        <form method="GET" action="{{ route('administrasi.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            {{-- Search Keyword --}}
            <div class="relative md:col-span-1">
                <i data-lucide="search" class="w-4 h-4 text-[#9CA3AF] absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari user / deskripsi / IP..."
                       class="w-full pl-9 pr-3 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg text-xs font-medium focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#509c38]">
            </div>

            {{-- Module Filter --}}
            <div>
                <select name="module" class="w-full px-3 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg text-xs font-medium focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#509c38]">
                    <option value="">-- Semua Modul --</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Action Filter --}}
            <div>
                <select name="action" class="w-full px-3 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg text-xs font-medium focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#509c38]">
                    <option value="">-- Semua Aksi --</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date Range --}}
            <div class="flex gap-2">
                <input type="date" name="date_start" value="{{ request('date_start') }}" title="Tanggal Mulai"
                       class="w-1/2 px-2.5 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg text-xs font-medium focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#509c38]">
                <input type="date" name="date_end" value="{{ request('date_end') }}" title="Tanggal Sampai"
                       class="w-1/2 px-2.5 py-2 bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg text-xs font-medium focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#509c38]">
            </div>

            {{-- Filter & Reset Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#509c38] text-white text-xs font-semibold rounded-lg hover:bg-[#43832e] transition-colors shadow-2xs flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'module', 'action', 'date_start', 'date_end']))
                    <a href="{{ route('administrasi.activity-logs.index') }}" class="px-3 py-2 bg-[#F4F4F5] text-[#71717A] hover:bg-[#E4E4E7] text-xs font-semibold rounded-lg transition-colors flex items-center justify-center" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- DATA TABLE --}}
    <div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden shadow-2xs mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F9FAFB] text-[#71717A] uppercase font-bold tracking-wider border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-4 py-3.5 w-12 text-center">#</th>
                        <th class="px-4 py-3.5">Waktu</th>
                        <th class="px-4 py-3.5">Pengguna</th>
                        <th class="px-4 py-3.5">Aksi</th>
                        <th class="px-4 py-3.5">Modul</th>
                        <th class="px-4 py-3.5">Deskripsi</th>
                        <th class="px-4 py-3.5">IP Address</th>
                        <th class="px-4 py-3.5 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($activityLogs as $index => $log)
                        <tr class="hover:bg-[#F9FAFB] transition-colors">
                            <td class="px-4 py-3 text-center text-[#9CA3AF] font-medium">
                                {{ $activityLogs->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 font-medium text-[#374151] whitespace-nowrap">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <span class="block text-[10px] text-[#9CA3AF]">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-[#18181B] block">{{ $log->user_name ?? 'Sistem' }}</span>
                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-600 rounded">
                                    {{ $log->user_role ?? 'User' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $actionClass = match($log->action) {
                                        'Tambah' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Edit'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Hapus'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'Import' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        default  => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full border {{ $actionClass }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-medium text-[#374151]">
                                <span class="px-2 py-0.5 bg-[#F4F4F5] text-[#374151] rounded text-[11px]">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[#52525B] max-w-xs truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-3 text-[#71717A] font-mono text-[11px] whitespace-nowrap">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if(!empty($log->properties))
                                    <button onclick="openDetailModal({{ $log->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#E5E7EB] text-[#374151] hover:bg-[#F4F4F5] hover:border-[#D4D4D8] font-semibold rounded-lg text-xs transition-colors shadow-2xs">
                                        <i data-lucide="eye" class="w-3.5 h-3.5 text-[#509c38]"></i> Detail
                                    </button>
                                @else
                                    <span class="text-[#D4D4D8] text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-[#71717A]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="history" class="w-8 h-8 text-[#D4D4D8]"></i>
                                    <p class="font-medium text-sm">Belum ada histori aktivitas yang tercatat</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($activityLogs->hasPages())
            <div class="px-4 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB]">
                {{ $activityLogs->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL DETAIL LOG --}}
    <div id="modalDetail" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-xl shadow-xl border border-[#E5E7EB] w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">
            {{-- Modal Header --}}
            <div class="px-5 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
                <div>
                    <h3 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5 text-[#509c38]"></i>
                        Detail Perubahan Data
                    </h3>
                    <p id="modalSubTitle" class="text-xs text-[#71717A] mt-0.5"></p>
                </div>
                <button onclick="closeDetailModal()" class="text-[#9CA3AF] hover:text-[#18181B] transition-colors p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Modal Content --}}
            <div class="p-5 overflow-y-auto flex-1 space-y-4 text-xs">
                <div id="modalLoading" class="py-8 text-center text-[#71717A]">
                    <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto text-[#509c38] mb-2"></i>
                    <p>Memuat detail perubahan...</p>
                </div>

                <div id="modalBody" class="hidden space-y-4">
                    {{-- Rendered Diff Tables --}}
                    <div id="diffContainer" class="space-y-4"></div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-5 py-3 border-t border-[#E5E7EB] bg-[#F9FAFB] flex justify-end">
                <button onclick="closeDetailModal()" class="px-4 py-2 bg-white border border-[#E5E7EB] text-[#374151] font-semibold text-xs rounded-lg hover:bg-[#F4F4F5] transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT FOR MODAL FETCH & DIFF RENDER --}}
    <script>
        function openDetailModal(id) {
            const modal = document.getElementById('modalDetail');
            const loading = document.getElementById('modalLoading');
            const body = document.getElementById('modalBody');
            const subTitle = document.getElementById('modalSubTitle');
            const diffContainer = document.getElementById('diffContainer');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            body.classList.add('hidden');
            diffContainer.innerHTML = '';
            subTitle.textContent = '';

            fetch(`/administrasi/activity-logs/${id}`)
                .then(res => res.json())
                .then(response => {
                    if (response.status === 'success') {
                        const log = response.data;
                        subTitle.textContent = `${log.action} pada modul ${log.module} oleh ${log.user_name} (${log.created_at})`;

                        const props = log.properties || {};
                        let html = '';

                        if (props.old && props.new) {
                            // EDIT ACTION: Side by side / changed keys comparison
                            html += `
                                <div class="font-semibold text-[#18181B] mb-2">Kolom Yang Diubah:</div>
                                <div class="overflow-x-auto border border-[#E5E7EB] rounded-lg">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead class="bg-[#F9FAFB] text-[#71717A] uppercase font-bold border-b border-[#E5E7EB]">
                                            <tr>
                                                <th class="p-2.5">Field</th>
                                                <th class="p-2.5 text-rose-600 bg-rose-50/50">Nilai Lama</th>
                                                <th class="p-2.5 text-emerald-600 bg-emerald-50/50">Nilai Baru</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#E5E7EB]">
                            `;

                            const allKeys = Array.from(new Set([...Object.keys(props.old), ...Object.keys(props.new)]));

                            allKeys.forEach(key => {
                                const oldVal = formatVal(props.old[key]);
                                const newVal = formatVal(props.new[key]);

                                html += `
                                    <tr>
                                        <td class="p-2.5 font-semibold text-[#374151] bg-[#F9FAFB] font-mono">${key}</td>
                                        <td class="p-2.5 bg-rose-50/30 text-rose-800 break-words">${oldVal}</td>
                                        <td class="p-2.5 bg-emerald-50/30 text-emerald-800 break-words">${newVal}</td>
                                    </tr>
                                `;
                            });

                            html += `</tbody></table></div>`;
                        } else if (props.new) {
                            // CREATE ACTION: Display created attributes
                            html += `
                                <div class="font-semibold text-[#18181B] mb-2">Data Baru Ditambahkan:</div>
                                <div class="overflow-x-auto border border-[#E5E7EB] rounded-lg">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead class="bg-[#F9FAFB] text-[#71717A] uppercase font-bold border-b border-[#E5E7EB]">
                                            <tr>
                                                <th class="p-2.5 w-1/3">Field</th>
                                                <th class="p-2.5 text-emerald-600 bg-emerald-50/50">Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#E5E7EB]">
                            `;

                            Object.keys(props.new).forEach(key => {
                                html += `
                                    <tr>
                                        <td class="p-2.5 font-semibold text-[#374151] bg-[#F9FAFB] font-mono">${key}</td>
                                        <td class="p-2.5 bg-emerald-50/20 text-[#18181B] break-words">${formatVal(props.new[key])}</td>
                                    </tr>
                                `;
                            });

                            html += `</tbody></table></div>`;
                        } else if (props.old) {
                            // DELETE ACTION: Display deleted attributes
                            html += `
                                <div class="font-semibold text-[#18181B] mb-2">Data Yang Dihapus:</div>
                                <div class="overflow-x-auto border border-[#E5E7EB] rounded-lg">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead class="bg-[#F9FAFB] text-[#71717A] uppercase font-bold border-b border-[#E5E7EB]">
                                            <tr>
                                                <th class="p-2.5 w-1/3">Field</th>
                                                <th class="p-2.5 text-rose-600 bg-rose-50/50">Nilai Terhapus</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#E5E7EB]">
                            `;

                            Object.keys(props.old).forEach(key => {
                                html += `
                                    <tr>
                                        <td class="p-2.5 font-semibold text-[#374151] bg-[#F9FAFB] font-mono">${key}</td>
                                        <td class="p-2.5 bg-rose-50/20 text-[#18181B] break-words">${formatVal(props.old[key])}</td>
                                    </tr>
                                `;
                            });

                            html += `</tbody></table></div>`;
                        } else {
                            html = `<pre class="bg-gray-50 p-3 rounded text-xs font-mono overflow-x-auto">${JSON.stringify(props, null, 2)}</pre>`;
                        }

                        diffContainer.innerHTML = html;
                        loading.classList.add('hidden');
                        body.classList.remove('hidden');
                        if (window.lucide) lucide.createIcons();
                    }
                })
                .catch(err => {
                    loading.classList.add('hidden');
                    diffContainer.innerHTML = `<div class="p-3 bg-red-50 text-red-700 rounded-lg">Gagal mengambil detail log data.</div>`;
                    body.classList.remove('hidden');
                });
        }

        function closeDetailModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }

        function formatVal(val) {
            if (val === null || val === undefined) return '<span class="italic text-gray-400">null</span>';
            if (typeof val === 'object') return JSON.stringify(val);
            return String(val);
        }
    </script>
</x-admin-layout>
