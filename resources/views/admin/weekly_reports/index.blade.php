<x-admin-layout>
    <x-slot name="title">Laporan Mingguan</x-slot>
    <x-slot name="header">Laporan Mingguan</x-slot>
    <x-slot name="headerSub">Kelola dan lihat seluruh laporan mingguan aktivitas training reguler.</x-slot>

    <div x-data="reportList()">

    {{-- Wizard Modal --}}
    @include('admin.weekly_reports._wizard_modal')


        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <!-- Total Laporan Card -->
            <div class="bg-emerald-600 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-emerald-600/30 cursor-pointer">
                <div class="relative z-10 text-white">
                    <p class="text-[11px] font-bold uppercase tracking-widest opacity-80 mb-1">Total Laporan</p>
                    <p class="text-3xl font-black leading-none">{{ $total ?? 0 }}</p>
                </div>
                <div class="absolute -bottom-4 -right-4 opacity-10">
                    <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full opacity-5 -translate-y-1/2 translate-x-1/3"></div>
            </div>

            <!-- Draft Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-amber-400 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mb-1">Draft</p>
                    <p class="text-3xl font-black text-slate-800 leading-none">{{ $draft ?? 0 }}</p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <!-- Submitted Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 border-l-4 border-l-emerald-500 p-5 relative overflow-hidden flex flex-col justify-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-slate-200/50 cursor-pointer">
                <div class="relative z-10">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mb-1">Submitted</p>
                    <p class="text-3xl font-black text-slate-800 leading-none">{{ $submitted ?? 0 }}</p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-[0.03] transform translate-x-1/4 translate-y-1/4">
                    <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="font-semibold text-sm">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="font-semibold text-sm">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Filter Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-6">
            <form action="{{ route('training-reguler.weekly-reports.index') }}" method="GET" class="p-5 flex flex-wrap gap-4 items-end bg-slate-50/50">
                <div class="flex-1 min-w-[200px] max-w-xs">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Bulan & Tahun</label>
                    <select name="month_year" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-shadow [color-scheme:light]">
                        <option value="" class="bg-white text-slate-700">Semua Bulan</option>
                        @foreach($months as $val => $label)
                            <option value="{{ $val }}" class="bg-white text-slate-700" {{ request('month_year') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px] max-w-xs">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Minggu</label>
                    <select name="week" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-shadow [color-scheme:light]">
                        <option value="" class="bg-white text-slate-700">Semua Minggu</option>
                        <option value="1" class="bg-white text-slate-700" {{ request('week') == '1' ? 'selected' : '' }}>Minggu 1 (Tgl 1 - 7)</option>
                        <option value="2" class="bg-white text-slate-700" {{ request('week') == '2' ? 'selected' : '' }}>Minggu 2 (Tgl 8 - 14)</option>
                        <option value="3" class="bg-white text-slate-700" {{ request('week') == '3' ? 'selected' : '' }}>Minggu 3 (Tgl 15 - 21)</option>
                        <option value="4" class="bg-white text-slate-700" {{ request('week') == '4' ? 'selected' : '' }}>Minggu 4 (Tgl 22 - Akhir)</option>
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm">
                        Filter
                    </button>
                    @if(request('month_year') || request('week'))
                        <a href="{{ route('training-reguler.weekly-reports.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <form action="{{ route('training-reguler.weekly-reports.merge') }}" method="POST" id="mergeForm">
                @csrf
                <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap justify-between items-center gap-4 bg-white">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Daftar Laporan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Semua laporan mingguan training reguler</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" x-show="selected.length > 1" x-transition
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm"
                            onclick="return confirm('Gabungkan ' + selected.length + ' laporan terpilih menjadi 1 laporan baru? Laporan lama akan dihapus.')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Gabungkan (<span x-text="selected.length"></span>)
                        </button>
                        <button type="button" @click="showWizard = true"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-all duration-200 ease-in-out hover:scale-105 active:scale-95 hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Buat Laporan Baru
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-4 w-12 text-center">
                                    <input type="checkbox" @change="selectAll($event)" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                </th>
                                <th class="px-5 py-4 text-center">#</th>
                                <th class="px-5 py-4">Program</th>
                                <th class="px-5 py-4">Periode</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($reports as $report)
                                <tr class="transition-colors duration-200 ease-in hover:bg-slate-50 group">
                                    <td class="px-5 py-4 text-center">
                                        <input type="checkbox" name="report_ids[]" value="{{ $report->id }}" x-model="selected" class="report-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-400 font-medium text-center">{{ method_exists($reports, 'firstItem') ? $reports->firstItem() + $loop->index : $loop->iteration }}</td>
                                    <td class="px-5 py-4">
                                        @if($report->is_merged)
                                            <span class="font-bold text-sm text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                                                Weekly Report {{ str_replace('&', 'and', $report->merged_program_names) }}
                                            </span>
                                        @else
                                            <span class="font-semibold text-sm text-gray-800">{{ $report->trainingProgram->name ?? '-' }} {{ isset($report->trainingProgram->year) ? '(' . $report->trainingProgram->year . ')' : '' }}</span>
                                        @endif
                                        <p class="hidden text-xs text-gray-400 mt-1">{{ $report->trainer->name ?? 'Trainer' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600">
                                        <div class="text-xs font-semibold">{{ \Carbon\Carbon::parse($report->week_start_date)->format('d M') }} — {{ \Carbon\Carbon::parse($report->week_end_date)->format('d M Y') }}</div>
                                        @php
                                            $startDay = \Carbon\Carbon::parse($report->week_start_date)->day;
                                            if ($startDay <= 7) $weekNum = 1;
                                            elseif ($startDay <= 14) $weekNum = 2;
                                            elseif ($startDay <= 21) $weekNum = 3;
                                            else $weekNum = 4;
                                        @endphp
                                        <div class="text-[11px] text-gray-400 mt-0.5">(Minggu ke-{{ $weekNum }})</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($report->status === 'draft')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 inline-block"></span> Draft
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span> Submitted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-70 group-hover:opacity-100 transition-all duration-300">
                                            <a href="{{ route('training-reguler.weekly-reports.show', $report->id) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] uppercase tracking-wide font-bold text-green-700 bg-green-50 rounded-md hover:bg-green-100 transition-all duration-200 hover:-translate-y-0.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Detail
                                            </a>
                                            @if($report->is_merged)
                                                <button type="button" onclick="alert('Laporan gabungan bersifat Read-Only dan tidak dapat diedit secara langsung. Jika ada kesalahan data, silakan gunakan tombol \'Batalkan Gabung\', lalu edit laporan aslinya satu per satu, dan gabungkan kembali.')" class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] uppercase tracking-wide font-bold text-gray-500 bg-gray-100 rounded-md hover:bg-gray-200 transition-all duration-200 hover:-translate-y-0.5 cursor-not-allowed">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                            @else
                                                <a href="{{ route('training-reguler.weekly-reports.edit', $report->id) }}"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] uppercase tracking-wide font-bold text-blue-700 bg-blue-50 rounded-md hover:bg-blue-100 transition-all duration-200 hover:-translate-y-0.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </a>
                                            @endif
                                            @if($report->is_merged)
                                                <button type="button" @click="confirmUnmerge({{ $report->id }}, '{{ addslashes($report->merged_program_names) }}')" class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] uppercase tracking-wide font-bold text-orange-600 bg-orange-50 rounded-md hover:bg-orange-100 transition-all duration-200 hover:-translate-y-0.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                    Batalkan Gabung
                                                </button>
                                            @endif
                                            <button type="button" @click="confirmDelete({{ $report->id }}, '{{ addslashes($report->trainingProgram->name ?? 'Laporan') }} - Minggu ke-{{ $report->week_number }}')" class="inline-flex items-center gap-1 px-3 py-1.5 text-[11px] uppercase tracking-wide font-bold text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition-all duration-200 hover:-translate-y-0.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold text-slate-400">Belum ada laporan mingguan</p>
                                            <p class="text-xs text-slate-300">Klik tombol "Buat Laporan Baru" untuk memulai</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $reports->links() }}
            </div>
        </div>

        {{-- Hidden delete & unmerge forms --}}
        @foreach($reports as $report)
        <form id="delete-form-{{ $report->id }}" action="{{ route('training-reguler.weekly-reports.destroy', $report->id) }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
        @if($report->is_merged)
        <form id="unmerge-form-{{ $report->id }}" action="{{ route('training-reguler.weekly-reports.unmerge', $report->id) }}" method="POST" class="hidden">
            @csrf
        </form>
        @endif
        @endforeach

    <!-- Modal Hapus -->
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showDeleteModal" x-transition.opacity.duration.300ms class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-90 translate-y-4 sm:translate-y-0" class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all text-left">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Laporan?</h3>
                    <p class="text-sm text-gray-500 mb-6">Tindakan ini tidak dapat dibatalkan. Laporan <span class="font-bold text-gray-800" x-text="deleteTitle"></span> akan dihapus permanen.</p>
                    <div class="flex gap-3 justify-center">
                        <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</button>
                        <button type="button" @click="executeDelete()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Unmerge -->
    <div x-show="showUnmergeModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="showUnmergeModal" x-transition.opacity.duration.300ms class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="showUnmergeModal = false" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="showUnmergeModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-90 translate-y-4 sm:translate-y-0" class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all text-left">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Batalkan Gabungan?</h3>
                    <p class="text-sm text-gray-500 mb-6">Laporan gabungan <span class="font-bold text-gray-800" x-text="unmergeTitle"></span> akan dipisah kembali menjadi laporan asli.</p>
                    <div class="flex gap-3 justify-center">
                        <button type="button" @click="showUnmergeModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</button>
                        <button type="button" @click="executeUnmerge()" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-orange-600 rounded-xl hover:bg-orange-700 transition shadow-lg shadow-orange-200">Batalkan Gabungan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportList', () => ({
                selected: [],
                showWizard: false,
                showDeleteModal: false,
                deleteId: null,
                deleteTitle: '',
                confirmDelete(id, title) {
                    this.deleteId = id;
                    this.deleteTitle = title;
                    this.showDeleteModal = true;
                },
                executeDelete() {
                    if (this.deleteId) {
                        document.getElementById('delete-form-' + this.deleteId).submit();
                    }
                },
                showUnmergeModal: false,
                unmergeId: null,
                unmergeTitle: '',
                confirmUnmerge(id, title) {
                    this.unmergeId = id;
                    this.unmergeTitle = title;
                    this.showUnmergeModal = true;
                },
                executeUnmerge() {
                    if (this.unmergeId) {
                        document.getElementById('unmerge-form-' + this.unmergeId).submit();
                    }
                },
                closeWizard() {
                    this.showWizard = false;
                },
                selectAll(e) {
                    if(e.target.checked) {
                        this.selected = Array.from(document.querySelectorAll('.report-checkbox')).map(cb => cb.value);
                    } else {
                        this.selected = [];
                    }
                }
            }))
        })
    </script>
</x-admin-layout>