<x-admin-layout>
    <x-slot name="title">Profil Talent | FR Academy</x-slot>
    <x-slot name="header">Profil Talent | FR Academy</x-slot>

    @push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F4F4F5;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }

        /* Robust Responsive Split-Screen Layout */
        .talent-split-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            align-items: stretch;
            width: 100%;
        }
        .talent-directory-sidebar {
            width: 100%;
        }
        .talent-detail-panel {
            width: 100%;
            min-width: 0;
            flex: 1 1 0%;
        }

        @media (min-width: 1024px) {
            .talent-split-container {
                flex-direction: row;
                align-items: flex-start;
            }
            .talent-directory-sidebar {
                width: 320px;
                min-width: 320px;
                max-width: 320px;
                flex-shrink: 0;
            }
        }

        /* 2-Column Responsive Grid */
        .grid-2-col {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        @media (min-width: 768px) {
            .grid-2-col {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* 3-Column Responsive Grid */
        .grid-3-col {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        @media (min-width: 768px) {
            .grid-3-col {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Notes vs Chart (7 : 5) */
        .grid-notes-chart {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .grid-notes-chart {
                grid-template-columns: 7fr 5fr;
            }
        }

        /* 4-Column Bottom Summary */
        .grid-4-summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        @media (min-width: 768px) {
            .grid-4-summary {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media print {
            nav, aside, header, .no-print, .action-buttons, #talent-directory-panel {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-full-width {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
    @endpush

    <div class="space-y-5 pb-12 w-full">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- TOP HEADER BAR --}}
        <div class="bg-[#1F3E22] text-white rounded-2xl px-5 py-4 shadow-md no-print w-full" style="background-color: #1F3E22 !important; color: #ffffff !important;">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center shrink-0" style="background-color: rgba(16, 185, 129, 0.2) !important; border: 1px solid rgba(52, 211, 153, 0.3) !important;">
                        <i data-lucide="sprout" class="w-6 h-6 text-emerald-300" style="color: #6ee7b7 !important;"></i>
                    </div>
                    <div>
                        <h1 class="text-lg md:text-xl font-black tracking-tight text-white flex items-center gap-2" style="color: #ffffff !important;">
                            DASHBOARD TALENT DEVELOPMENT {{ date('Y') }}
                        </h1>
                        <p class="text-xs text-emerald-200/80 font-medium" style="color: rgba(167, 243, 208, 0.8) !important;">
                            First Resources Group &bull; Talent Management System
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Download Template --}}
                    <a href="{{ route('administrasi.profil-talent.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-bold text-white transition-all" style="background-color: rgba(255, 255, 255, 0.15) !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; color: #ffffff !important;">
                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5" style="color: #ffffff !important;"></i>
                        <span>Template Excel</span>
                    </a>

                    {{-- Export Excel --}}
                    <a href="{{ route('administrasi.profil-talent.export', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-bold text-white transition-all" style="background-color: rgba(255, 255, 255, 0.15) !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; color: #ffffff !important;">
                        <i data-lucide="download" class="w-3.5 h-3.5" style="color: #ffffff !important;"></i>
                        <span>Export Excel</span>
                    </a>

                    {{-- Import Modal Trigger --}}
                    <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-bold text-white transition-all cursor-pointer" style="background-color: rgba(255, 255, 255, 0.15) !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; color: #ffffff !important;">
                        <i data-lucide="upload" class="w-3.5 h-3.5 text-emerald-300" style="color: #6ee7b7 !important;"></i>
                        <span>Import CSV/Excel</span>
                    </button>

                    {{-- Tambah Data Talent --}}
                    <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 rounded-xl bg-[#509c38] hover:bg-[#43832f] text-white px-3.5 py-1.5 text-xs font-bold shadow-sm transition-all cursor-pointer" style="background-color: #509c38 !important; color: #ffffff !important;">
                        <i data-lucide="plus" class="w-4 h-4" style="color: #ffffff !important;"></i>
                        <span>+ Tambah Talent</span>
                    </button>

                    @if($talents->count() > 0)
                    {{-- Hapus Semua Data --}}
                    <button type="button" onclick="openDeleteAllModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-400/40 bg-rose-500/20 hover:bg-rose-500/30 text-rose-200 px-3 py-1.5 text-xs font-bold transition-all cursor-pointer" style="background-color: rgba(244, 63, 94, 0.2) !important; border: 1px solid rgba(251, 113, 133, 0.4) !important; color: #fecdd3 !important;">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5" style="color: #fecdd3 !important;"></i>
                        <span>Hapus Semua</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- MAIN SPLIT-SCREEN CONTAINER --}}
        <div class="talent-split-container">

            {{-- ==================== LEFT COLUMN: DIREKTORI TALENT ==================== --}}
            <div id="talent-directory-panel" class="talent-directory-sidebar bg-white rounded-2xl border border-slate-200 shadow-xs p-4 space-y-3.5 no-print">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <i data-lucide="contact-round" class="w-4 h-4 text-emerald-700"></i>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Direktori Talent</h2>
                    </div>
                    <span id="talent-count-badge" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $talents->count() }} Talent
                    </span>
                </div>

                {{-- Search Box --}}
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="directory-search" placeholder="Cari nama / ID..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                {{-- Filter Kategori Dropdown --}}
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase shrink-0">Kat:</span>
                    <select id="directory-category" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl py-1.5 px-2.5 text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $kategori === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Talent Cards List --}}
                <div id="directory-list" class="space-y-2 max-h-[720px] overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($talents as $t)
                        @php
                            $isSelected = $selectedTalent && $selectedTalent->id === $t->id;
                        @endphp
                        <a href="{{ route('administrasi.profil-talent.index', array_merge(request()->query(), ['selected_id' => $t->id])) }}"
                           data-name="{{ strtolower($t->nama) }}"
                           data-nik="{{ strtolower($t->employee_id ?? '') }}"
                           data-cat="{{ $t->talent ?? '' }}"
                           class="talent-item-card block p-3 rounded-xl border transition-all duration-150 {{ $isSelected ? 'bg-emerald-50/80 border-emerald-500 shadow-xs ring-2 ring-emerald-500/30' : 'bg-white border-slate-200/80 hover:bg-slate-50 hover:border-slate-300' }}">
                            <div class="flex items-center gap-3">
                                {{-- Avatar Initials --}}
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isSelected ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    {{ $t->initials }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-1">
                                        <h3 class="text-xs font-bold text-slate-900 truncate {{ $isSelected ? 'text-emerald-950 font-black' : '' }}">
                                            {{ $t->nama }}
                                        </h3>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                                        <span class="font-mono text-[10px] text-slate-400 font-semibold">{{ $t->employee_id ?? '-' }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 truncate max-w-[120px] font-medium">
                                            {{ $t->jabatan ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i data-lucide="users-round" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            Belum ada data talent.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ==================== RIGHT COLUMN: TALENT DOSSIER DETAIL ==================== --}}
            <div class="talent-detail-panel space-y-5 print-full-width">

                @if($selectedTalent)
                {{-- 1. TOP ROW: PROFILE HERO CARD & PROGRESS IDP --}}
                <div class="grid-2-col items-stretch">

                    {{-- Left of Hero: Profile Summary & Actions --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div>
                            {{-- Photo & Identity --}}
                            <div class="flex flex-col items-center text-center">
                                <div class="relative mb-3">
                                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-100 border-2 border-emerald-500/40 p-1 shadow-sm flex items-center justify-center">
                                        @if($selectedTalent->photo_url)
                                            <img src="{{ $selectedTalent->photo_url }}" alt="{{ $selectedTalent->nama }}"
                                                 class="w-full h-full object-cover rounded-xl"
                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($selectedTalent->nama) }}&background=1F3E22&color=fff&size=200';">
                                        @else
                                            <div class="w-full h-full bg-emerald-800 text-white font-black text-2xl flex items-center justify-center rounded-xl">
                                                {{ $selectedTalent->initials }}
                                            </div>
                                        @endif
                                    </div>
                                    @if($selectedTalent->no)
                                    <span class="absolute -top-1.5 -right-1.5 px-2 py-0.5 text-[10px] font-black bg-emerald-600 text-white rounded-full shadow-xs">
                                        #{{ $selectedTalent->no }}
                                    </span>
                                    @endif
                                </div>

                                <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $selectedTalent->nama }}</h2>
                                <p class="text-xs font-mono text-slate-400 font-semibold mt-0.5">{{ $selectedTalent->employee_id ?? '-' }}</p>
                            </div>

                            {{-- 4 Meta Pill Boxes --}}
                            <div class="grid grid-cols-2 gap-2.5 mt-4">
                                <div class="bg-slate-50/80 border border-slate-200/70 rounded-xl p-2.5">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Target Ke</span>
                                    <span class="text-xs font-bold text-slate-800">{{ $selectedTalent->talent_to ?? '-' }}</span>
                                </div>
                                <div class="bg-slate-50/80 border border-slate-200/70 rounded-xl p-2.5">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Lokasi OJT</span>
                                    <span class="text-xs font-bold text-slate-800 truncate block" title="{{ $selectedTalent->lokasi_ojt }}">
                                        {{ $selectedTalent->lokasi_ojt ?? '-' }}
                                    </span>
                                </div>
                                <div class="bg-amber-50/50 border border-amber-200/60 rounded-xl p-2.5">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-amber-700 block">Status OJT</span>
                                    <span class="text-xs font-bold text-amber-900">{{ $selectedTalent->status_ojt ?? '-' }}</span>
                                </div>
                                <div class="bg-slate-50/80 border border-slate-200/70 rounded-xl p-2.5">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Region</span>
                                    <span class="text-xs font-bold text-slate-800">{{ $selectedTalent->region ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col gap-2 mt-5 no-print">
                            <button type="button" onclick="openEditModal({{ $selectedTalent->id }})"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs py-2.5 shadow-xs transition-colors">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                <span>Edit Feedback & Evaluasi</span>
                            </button>

                            <div class="flex items-center gap-2">
                                <button type="button" onclick="window.print()"
                                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-[#509c38] hover:bg-[#43832f] text-white font-bold text-xs py-2 shadow-xs transition-colors">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    <span>Export PDF / Cetak</span>
                                </button>

                                <button type="button" onclick="openDeleteSingleModal({{ $selectedTalent->id }}, '{{ addslashes($selectedTalent->nama) }}')"
                                        class="px-3 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors"
                                        title="Hapus Talent Ini">
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Right of Hero: Progress IDP Card --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 mb-3">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                                <i data-lucide="trending-up" class="w-4 h-4 text-emerald-700"></i>
                                <span>Progress IDP (Individual Development Plan)</span>
                            </div>
                        </div>

                        {{-- Main Radial Overall Progress --}}
                        @php
                            $ref = $selectedTalent->refreshment !== null && $selectedTalent->refreshment !== '' ? (float) $selectedTalent->refreshment : 0;
                            if ($ref > 0 && $ref <= 1.0) { $ref = $ref * 100; }

                            $soft = $selectedTalent->softskill !== null && $selectedTalent->softskill !== '' ? (float) $selectedTalent->softskill : 0;
                            if ($soft > 0 && $soft <= 1.0) { $soft = $soft * 100; }

                            $ojtP = $selectedTalent->ojt !== null && $selectedTalent->ojt !== '' ? (float) $selectedTalent->ojt : 0;
                            if ($ojtP > 0 && $ojtP <= 1.0) { $ojtP = $ojtP * 100; }

                            $ovp = $selectedTalent->overall_progress !== null && $selectedTalent->overall_progress !== '' ? (float) $selectedTalent->overall_progress : 0;
                            if ($ovp > 0 && $ovp <= 1.0) { $ovp = $ovp * 100; }
                            if ($ovp <= 0 && ($ref > 0 || $soft > 0 || $ojtP > 0)) {
                                $ovp = round(($ref + $soft + $ojtP) / 3, 1);
                            }
                        @endphp

                        <div class="bg-slate-900 rounded-2xl p-5 text-white flex flex-col items-center justify-center text-center my-auto shadow-inner">
                            <div class="relative w-28 h-28 flex items-center justify-center">
                                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-slate-800" stroke-width="3.5" stroke="currentColor" fill="none"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    <path class="text-emerald-400 transition-all duration-700 ease-out" stroke-dasharray="{{ $ovp }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-xl font-black text-emerald-300 tracking-tight">{{ $ovp }}%</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-black tracking-widest text-emerald-400 uppercase mt-2">Overall Progress</span>
                        </div>

                        {{-- 3 Mini Gauge Rings --}}
                        <div class="grid grid-cols-3 gap-3 mt-4 pt-3 border-t border-slate-100">
                            {{-- Refreshment --}}
                            <div class="flex flex-col items-center text-center p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="relative w-12 h-12 flex items-center justify-center">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-200" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="text-emerald-500" stroke-dasharray="{{ $ref }}, 100" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    </svg>
                                    <span class="absolute text-[10px] font-black text-slate-800">{{ round($ref) }}%</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 mt-1">Refreshment</span>
                            </div>

                            {{-- Soft Skill --}}
                            <div class="flex flex-col items-center text-center p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="relative w-12 h-12 flex items-center justify-center">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-200" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="text-blue-500" stroke-dasharray="{{ $soft }}, 100" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    </svg>
                                    <span class="absolute text-[10px] font-black text-slate-800">{{ round($soft) }}%</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 mt-1">Soft Skill</span>
                            </div>

                            {{-- OJT Task --}}
                            <div class="flex flex-col items-center text-center p-2 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="relative w-12 h-12 flex items-center justify-center">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-200" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        <path class="text-amber-500" stroke-dasharray="{{ $ojtP }}, 100" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    </svg>
                                    <span class="absolute text-[10px] font-black text-slate-800">{{ round($ojtP) }}%</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 mt-1">OJT Task</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. MIDDLE ROW: HISTORI PEKERJAAN & ACHIEVEMENT --}}
                <div class="grid-2-col">
                    {{-- Histori Pekerjaan --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-slate-700">
                            <i data-lucide="briefcase" class="w-4 h-4 text-emerald-700"></i>
                            <span>Histori Pekerjaan</span>
                        </div>
                        <div class="text-xs text-slate-600 space-y-2 whitespace-pre-line leading-relaxed">
                            {!! nl2br(e($selectedTalent->histori_pekerjaan ?: 'Belum ada histori pekerjaan.')) !!}
                        </div>
                    </div>

                    {{-- Achievement & Prestasi --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-amber-700">
                            <i data-lucide="trophy" class="w-4 h-4 text-amber-500"></i>
                            <span>Achievement & Prestasi</span>
                        </div>
                        <div class="text-xs text-slate-600 space-y-2 whitespace-pre-line leading-relaxed">
                            {!! nl2br(e($selectedTalent->achievement ?: 'Belum ada catatan achievement.')) !!}
                        </div>
                    </div>
                </div>

                {{-- 3. ROW: KOMPETENSI TALENT --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-slate-700">
                        <i data-lucide="award" class="w-4 h-4 text-emerald-700"></i>
                        <span>Kompetensi Talent</span>
                    </div>
                    <div class="grid-3-col">
                        <div class="bg-emerald-50/50 border border-emerald-200/60 rounded-xl p-3">
                            <span class="text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-md inline-block mb-1.5">Teknis</span>
                            <p class="text-xs text-slate-700 font-medium">{{ $selectedTalent->teknis ?: 'Tidak ada data' }}</p>
                        </div>
                        <div class="bg-blue-50/50 border border-blue-200/60 rounded-xl p-3">
                            <span class="text-[9px] font-black uppercase tracking-wider text-blue-800 bg-blue-100 px-2 py-0.5 rounded-md inline-block mb-1.5">Managerial & Leadership</span>
                            <p class="text-xs text-slate-700 font-medium">{{ $selectedTalent->leadership ?: 'Tidak ada data' }}</p>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-800 bg-slate-200 px-2 py-0.5 rounded-md inline-block mb-1.5">Training</span>
                            <p class="text-xs text-slate-700 font-medium">{{ $selectedTalent->training ?: 'Tidak ada data' }}</p>
                        </div>
                    </div>
                </div>

                {{-- 4. ROW: PROFIL PERSONAL, STRENGTH, & WEAKNESS --}}
                <div class="grid-3-col">
                    {{-- Profil & Data Personal --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-slate-700">
                            <i data-lucide="user-check" class="w-4 h-4 text-emerald-700"></i>
                            <span>Profil & Data Personal</span>
                        </div>
                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400">Jabatan Saat Ini</span>
                                <span class="font-bold text-slate-800">{{ $selectedTalent->jabatan ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400">Usia</span>
                                <span class="font-bold text-slate-800">{{ $selectedTalent->umur ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400">Masa Kerja</span>
                                <span class="font-bold text-slate-800">{{ $selectedTalent->masa_kerja ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400">Alumni</span>
                                <span class="font-bold text-slate-800 text-right truncate max-w-[170px]" title="{{ $selectedTalent->alumni }}">
                                    {{ $selectedTalent->alumni ?? '-' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400">Status Pernikahan</span>
                                <span class="font-bold text-slate-800">{{ $selectedTalent->status_pernikahan ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Agama</span>
                                <span class="font-bold text-slate-800">{{ $selectedTalent->agama ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Strength (Kelebihan) --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-emerald-700">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                            <span>Strength (Kelebihan)</span>
                        </div>
                        <div class="text-xs text-slate-700 space-y-2 whitespace-pre-line leading-relaxed">
                            {!! nl2br(e($selectedTalent->strength ?: 'Belum ada catatan kelebihan.')) !!}
                        </div>
                    </div>

                    {{-- Weakness (Area Pengembangan) --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-rose-700">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
                            <span>Weakness (Area Pengembangan)</span>
                        </div>
                        <div class="text-xs text-slate-700 space-y-2 whitespace-pre-line leading-relaxed">
                            {!! nl2br(e($selectedTalent->weakness ?: 'Belum ada catatan kelemahan.')) !!}
                        </div>
                    </div>
                </div>

                {{-- 5. ROW: PROFILING & TIM PENANGGUNG JAWAB --}}
                <div class="grid-2-col">
                    {{-- Aspek Kepribadian & Profiling --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-slate-700">
                            <i data-lucide="brain-circuit" class="w-4 h-4 text-emerald-700"></i>
                            <span>Aspek Kepribadian & Profiling</span>
                        </div>
                        <p class="text-xs text-slate-700 leading-relaxed text-justify">
                            {{ $selectedTalent->profilling ?: 'Belum ada profil asesmen kepribadian.' }}
                        </p>
                    </div>

                    {{-- Tim Penanggung Jawab --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5 mb-3 text-xs font-bold uppercase tracking-wider text-slate-700">
                            <i data-lucide="users-2" class="w-4 h-4 text-emerald-700"></i>
                            <span>Tim Penanggung Jawab</span>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Mentor Utama (Kebun / Mill)</span>
                                <span class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $selectedTalent->mentor ?: '-' }}</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Mentor FRLC</span>
                                <span class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $selectedTalent->mentor_frlc ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 6. ROW: CATATAN & REKOMENDASI MENTOR (DYNAMIC MONTH) + BAR CHART NILAI --}}
                <div class="grid-notes-chart">

                    {{-- Catatan & Rekomendasi Mentor with Dynamic Month Filter --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                        @php
                            $availableMonths = $selectedTalent->months_with_notes;
                            // Default to first available month or 'juli'
                            $firstMonthKey = !empty($availableMonths) ? array_key_first($availableMonths) : 'juli';
                        @endphp

                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                                <i data-lucide="message-square" class="w-4 h-4 text-emerald-700"></i>
                                <span>Catatan & Rekomendasi Mentor</span>
                            </div>

                            {{-- Month Filter Dropdown (Only shows non-null/available months) --}}
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-bold text-slate-400">BULAN:</span>
                                <select id="mentor-month-select" onchange="switchMentorMonth(this.value)"
                                        class="text-xs font-bold bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                    @forelse($availableMonths as $mKey => $mLabel)
                                        <option value="{{ $mKey }}" {{ $mKey === $firstMonthKey ? 'selected' : '' }}>
                                            {{ $mLabel }}
                                        </option>
                                    @empty
                                        <option value="juli">Juli (Tidak ada data)</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        {{-- Section Feedback Evaluasi --}}
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">Feedback Evaluasi</span>
                            <div id="feedback-evaluasi-content" class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 whitespace-pre-line min-h-[90px]">
                                {{ $selectedTalent->{"catatan_mentor_{$firstMonthKey}"} ?: ($selectedTalent->catatan_mentor_general ?: 'Belum ada catatan evaluasi untuk bulan ini.') }}
                            </div>
                        </div>

                        {{-- Section Development Suggestion --}}
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">Development Suggestion</span>
                            <div id="dev-suggestion-content" class="text-xs text-slate-700 leading-relaxed bg-slate-50/70 border border-slate-100 rounded-xl p-3.5 whitespace-pre-line min-h-[90px]">
                                {{ $selectedTalent->{"dev_suggestion_{$firstMonthKey}"} ?: ($selectedTalent->development_suggestion_general ?: 'Belum ada saran pengembangan untuk bulan ini.') }}
                            </div>
                        </div>
                    </div>

                    {{-- Bar Chart: Nilai Mentor (Only non-null months) --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                        @php
                            $scoresList = $selectedTalent->months_with_scores;
                        @endphp

                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                                <i data-lucide="bar-chart-2" class="w-4 h-4 text-emerald-700"></i>
                                <span>Nilai Mentor</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-semibold">Skala 0 - 100</span>
                        </div>

                        @if(!empty($scoresList))
                            <div class="pt-2">
                                {{-- SVG Bar Chart --}}
                                <div class="h-48 flex items-end justify-around gap-2 px-2 border-b border-slate-100 pb-2">
                                    @php
                                        $barColors = ['#0284C7', '#D97706', '#10B981', '#6366F1', '#EC4899', '#8B5CF6', '#F59E0B', '#14B8A6'];
                                        $ci = 0;
                                    @endphp
                                    @foreach($scoresList as $sKey => $sData)
                                        @php
                                            $heightPct = min(100, max(5, ($sData['score'] / 100) * 100));
                                            $col = $barColors[$ci % count($barColors)];
                                            $ci++;
                                        @endphp
                                        <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                                            {{-- Tooltip Score on Hover --}}
                                            <span class="text-[11px] font-black text-slate-700 mb-1 opacity-90 group-hover:scale-110 transition-transform">
                                                {{ $sData['score'] }}
                                            </span>
                                            {{-- Bar --}}
                                            <div class="w-full max-w-[36px] rounded-t-lg transition-all duration-500 ease-out shadow-xs group-hover:brightness-95"
                                                 style="height: {{ $heightPct }}%; background-color: {{ $col }};">
                                            </div>
                                            {{-- Month label --}}
                                            <span class="text-[10px] font-bold text-slate-500 mt-2 uppercase">
                                                {{ $sData['short'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="h-48 flex flex-col items-center justify-center text-center text-slate-400 p-4">
                                <i data-lucide="bar-chart" class="w-8 h-8 text-slate-300 mb-2"></i>
                                <span class="text-xs">Belum ada nilai mentor yang tercatat.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 7. BOTTOM ROW: NILAI AKHIR & STATUS SUMMARY (LIGHT THEME CARDS) --}}
                @php
                    $grade = $selectedTalent->evaluation_grade;
                @endphp
                <div class="grid-4-summary items-stretch">
                    {{-- Nilai OJT (40%) --}}
                    <div class="bg-white rounded-2xl p-4 shadow-xs text-center flex flex-col justify-center border border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Nilai OJT</span>
                        <span class="text-2xl font-black text-slate-800 mt-1">
                            {{ $selectedTalent->nilai_ojt !== null && $selectedTalent->nilai_ojt !== '' ? $selectedTalent->nilai_ojt : '-' }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Bobot 40%</span>
                    </div>

                    {{-- Evaluasi Panelis (60%) --}}
                    <div class="bg-white rounded-2xl p-4 shadow-xs text-center flex flex-col justify-center border border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Evaluasi Panelis</span>
                        <span class="text-2xl font-black text-slate-800 mt-1">
                            {{ $selectedTalent->nilai_evaluasi_panelis !== null && $selectedTalent->nilai_evaluasi_panelis !== '' ? $selectedTalent->nilai_evaluasi_panelis : '-' }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Bobot 60%</span>
                    </div>

                    {{-- Nilai Akhir (Auto) --}}
                    <div class="{{ $grade ? $grade['card_bg'] : 'bg-white' }} rounded-2xl p-4 shadow-xs text-center flex flex-col justify-center border {{ $grade ? $grade['border_card'] : 'border-slate-200' }} transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $grade ? $grade['text_sub'] : 'text-slate-500' }}">Nilai Akhir</span>
                        <span class="text-2xl font-black {{ $grade ? $grade['text_number'] : 'text-slate-800' }} mt-1">
                            {{ $selectedTalent->nilai_akhir !== null && $selectedTalent->nilai_akhir !== '' ? $selectedTalent->nilai_akhir : '-' }}
                        </span>
                        <span class="text-[11px] {{ $grade ? $grade['text_sub'] : 'text-slate-400' }} font-semibold mt-0.5">OJT 40% + Panelis 60%</span>
                    </div>

                    {{-- Kategori Kelulusan / Predikat Box (Grade Box with bullets from image) --}}
                    <div class="{{ $grade ? $grade['card_bg'] : 'bg-white' }} rounded-2xl p-3.5 shadow-xs flex items-center gap-3.5 border {{ $grade ? $grade['border_card'] : 'border-slate-200' }} transition-colors">
                        @if($grade)
                            <div class="w-12 h-12 rounded-xl {{ $grade['bg_badge'] }} {{ $grade['text_badge'] }} border {{ $grade['border_badge'] }} flex items-center justify-center font-black text-xl shrink-0 shadow-xs">
                                {{ $grade['grade'] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-black {{ $grade['text_title'] }} uppercase tracking-wider block truncate">
                                    {{ $grade['title'] }}
                                </span>
                                <ul class="text-[11px] text-slate-700 mt-1 space-y-0.5 font-semibold">
                                    @foreach($grade['bullets'] as $b)
                                        <li class="flex items-center gap-1.5 truncate">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $grade['bullet_dot'] }} shrink-0"></span>
                                            <span>{{ $b }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="w-full text-center py-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Keterangan Evaluasi</span>
                                <span class="text-xs font-bold text-slate-600 mt-1 uppercase block">
                                    {{ $selectedTalent->keterangan_evaluasi ?: 'Belum Dinilai / Data Kosong' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                @else
                {{-- EMPTY STATE (No talent in database) --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users-round" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Data Talent</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">
                        Silakan tambahkan data talent satu per satu atau import data dari file Excel / CSV yang Anda miliki.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" onclick="openAddModal()" class="px-4 py-2 bg-[#509c38] hover:bg-[#43832f] text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                            + Tambah Talent Baru
                        </button>
                        <button type="button" onclick="openImportModal()" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl shadow-xs transition-all">
                            Import Excel / CSV
                        </button>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ==================== MODAL IMPORT EXCEL / CSV ==================== --}}
    <div id="modal-import" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <div class="px-6 py-4 bg-[#1F3E22] text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="upload-cloud" class="w-5 h-5 text-emerald-300"></i>
                    <h3 class="text-sm font-bold">Import Data Profil Talent</h3>
                </div>
                <button type="button" onclick="closeImportModal()" class="text-emerald-200 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('administrasi.profil-talent.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Pilih File Spreadsheet (CSV atau XLSX)</label>
                    <input type="file" name="file" required accept=".csv,.txt,.xlsx"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-200 rounded-xl p-2 focus:outline-none">
                    <p class="text-[11px] text-slate-400 mt-2">
                        Format kolom disesuaikan langsung dengan header Google Sites / Excel Profil Talent.
                    </p>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                    <span>Jika NIK / Employee ID sudah ada di sistem, data akan diperbarui (update). Jika belum ada, data baru akan ditambahkan.</span>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <a href="{{ route('administrasi.profil-talent.template') }}" class="text-xs font-bold text-emerald-700 hover:underline inline-flex items-center gap-1">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh Template
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeImportModal()" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-[#509c38] hover:bg-[#43832f] text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                            Import Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL TAMBAH DATA 1 PER 1 ==================== --}}
    <div id="modal-add" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="px-6 py-4 bg-[#1F3E22] text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="user-plus" class="w-5 h-5 text-emerald-300"></i>
                    <h3 class="text-sm font-bold">+ Tambah Talent Baru</h3>
                </div>
                <button type="button" onclick="closeAddModal()" class="text-emerald-200 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('administrasi.profil-talent.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-5 custom-scrollbar">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Employee ID / NIK</label>
                        <input type="text" name="employee_id" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori / Talent</label>
                        <input type="text" name="talent" placeholder="Estate, Mill, Traksi, dll." class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Saat Ini</label>
                        <input type="text" name="jabatan" placeholder="Asisten Kebun, dll." class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Jabatan (Talent To)</label>
                        <input type="text" name="talent_to" placeholder="Askep, Manager, dll." class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status OJT</label>
                        <select name="status_ojt" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option value="">-- Pilih Status OJT --</option>
                            <option value="Belum Penugasan">Belum Penugasan</option>
                            <option value="Sudah Penugasan">Sudah Penugasan</option>
                            <option value="Project Khusus">Project Khusus</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Lokasi OJT</label>
                        <input type="text" name="lokasi_ojt" placeholder="Plt. Askep di ATS 1" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Region</label>
                        <input type="text" name="region" placeholder="Riau, Kalbar, Kaltim, dll." class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Usia / Umur</label>
                        <input type="text" name="umur" placeholder="25 Tahun" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Masa Kerja</label>
                        <input type="text" name="masa_kerja" placeholder="2 Tahun" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alumni Universitas</label>
                        <input type="text" name="alumni" placeholder="Universitas Sumatera Utara" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Pernikahan</label>
                        <input type="text" name="status_pernikahan" placeholder="K/1, TK/0" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Agama</label>
                        <input type="text" name="agama" placeholder="Islam, Kristen, dll." class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mentor Utama</label>
                        <input type="text" name="mentor" placeholder="Jhon Setiawan" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mentor FRLC</label>
                        <input type="text" name="mentor_frlc" placeholder="Henri Hutajulu" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>

                {{-- Photo Options --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Link Foto (Google Drive / URL)</label>
                        <input type="text" name="foto_profil" placeholder="https://drive.google.com/file/d/..." class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Atau Upload Foto Lokal</label>
                        <input type="file" name="foto_file" accept="image/*" class="w-full text-xs text-slate-500 border border-slate-200 rounded-xl p-2 focus:outline-none">
                    </div>
                </div>

                {{-- Long Texts --}}
                <div class="space-y-3 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Aspek Kepribadian & Profiling</label>
                        <textarea name="profilling" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Histori Pekerjaan</label>
                            <textarea name="histori_pekerjaan" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Achievement & Prestasi</label>
                            <textarea name="achievement" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Strength (Kelebihan)</label>
                            <textarea name="strength" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Weakness (Area Pengembangan)</label>
                            <textarea name="weakness" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Progress & Scores --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Refreshment (%)</label>
                        <input type="number" step="0.1" min="0" max="100" id="add_refreshment" name="refreshment" placeholder="100" oninput="calcAddOverallProgress()" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Soft Skill (%)</label>
                        <input type="number" step="0.1" min="0" max="100" id="add_softskill" name="softskill" placeholder="100" oninput="calcAddOverallProgress()" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">OJT Task (%)</label>
                        <input type="number" step="0.1" min="0" max="100" id="add_ojt" name="ojt" placeholder="75" oninput="calcAddOverallProgress()" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Overall Progress (%)</label>
                        <input type="number" step="0.1" min="0" max="100" id="add_overall_progress" name="overall_progress" placeholder="91.7" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none bg-slate-50">
                        <span class="text-[9px] text-slate-400 mt-0.5 block">Otomatis / manual</span>
                    </div>
                </div>

                {{-- Evaluasi Akhir (OJT 40% & Panelis 60%) --}}
                <div class="space-y-3 pt-3 border-t border-slate-100">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 block">Penilaian Akhir Kelulusan</span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai OJT (40%)</label>
                            <input type="number" step="0.1" id="add_nilai_ojt" name="nilai_ojt" placeholder="0 - 100" oninput="calcAddNilaiAkhir()" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Evaluasi Panelis (60%)</label>
                            <input type="number" step="0.1" id="add_nilai_panelis" name="nilai_evaluasi_panelis" placeholder="0 - 100" oninput="calcAddNilaiAkhir()" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Akhir (Otomatis)</label>
                            <input type="number" step="0.1" id="add_nilai_akhir" name="nilai_akhir" readonly placeholder="Dihitung otomatis" class="w-full text-xs font-black bg-slate-100 text-slate-800 border border-slate-200 rounded-xl p-2.5 cursor-not-allowed">
                            <span class="text-[10px] text-slate-400 mt-1 block">(OJT &times; 40%) + (Panelis &times; 60%)</span>
                        </div>
                    </div>

                    {{-- Live Preview Box for Add Modal --}}
                    <div id="add_grade_preview" class="p-3 rounded-xl border border-slate-200 bg-slate-50 flex items-center gap-3">
                        <div id="add_grade_badge" class="w-10 h-10 rounded-lg bg-slate-200 text-slate-700 font-black text-lg flex items-center justify-center shrink-0">
                            -
                        </div>
                        <div>
                            <span id="add_grade_title" class="text-xs font-bold text-slate-800 block">Belum Dinilai</span>
                            <span id="add_grade_bullets" class="text-[11px] text-slate-500 block">Masukkan Nilai OJT & Nilai Panelis</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 shrink-0">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#509c38] hover:bg-[#43832f] text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        Simpan Talent
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL EDIT DATA & EVALUASI ==================== --}}
    @if($selectedTalent)
    <div id="modal-edit-{{ $selectedTalent->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[92vh] shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="px-6 py-4 bg-[#1F3E22] text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="edit-3" class="w-5 h-5 text-emerald-300"></i>
                    <h3 class="text-sm font-bold">Edit Feedback & Evaluasi: {{ $selectedTalent->nama }}</h3>
                </div>
                <button type="button" onclick="closeEditModal({{ $selectedTalent->id }})" class="text-emerald-200 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('administrasi.profil-talent.update', $selectedTalent->id) }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                @csrf
                @method('PUT')

                {{-- Tabs Switcher inside Modal --}}
                <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                    <button type="button" onclick="switchModalTab('tab-bio')" class="modal-tab-btn active px-3.5 py-1.5 text-xs font-bold rounded-lg bg-emerald-100 text-emerald-800" data-target="tab-bio">
                        Biodata & Penugasan
                    </button>
                    <button type="button" onclick="switchModalTab('tab-eval')" class="modal-tab-btn px-3.5 py-1.5 text-xs font-bold rounded-lg text-slate-600 hover:bg-slate-100" data-target="tab-eval">
                        Nilai & Evaluasi Bulanan (Jan - Des)
                    </button>
                    <button type="button" onclick="switchModalTab('tab-notes')" class="modal-tab-btn px-3.5 py-1.5 text-xs font-bold rounded-lg text-slate-600 hover:bg-slate-100" data-target="tab-notes">
                        Catatan & Saran Bulanan
                    </button>
                </div>

                {{-- TAB 1: BIODATA & PENUGASAN --}}
                <div id="tab-bio" class="modal-tab-content space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $selectedTalent->nama }}" required class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Employee ID / NIK</label>
                            <input type="text" name="employee_id" value="{{ $selectedTalent->employee_id }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori / Talent</label>
                            <input type="text" name="talent" value="{{ $selectedTalent->talent }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Saat Ini</label>
                            <input type="text" name="jabatan" value="{{ $selectedTalent->jabatan }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target Jabatan</label>
                            <input type="text" name="talent_to" value="{{ $selectedTalent->talent_to }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status OJT</label>
                            <select name="status_ojt" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option value="">-- Pilih Status OJT --</option>
                                <option value="Belum Penugasan" {{ $selectedTalent->status_ojt === 'Belum Penugasan' ? 'selected' : '' }}>Belum Penugasan</option>
                                <option value="Sudah Penugasan" {{ $selectedTalent->status_ojt === 'Sudah Penugasan' ? 'selected' : '' }}>Sudah Penugasan</option>
                                <option value="Project Khusus" {{ $selectedTalent->status_ojt === 'Project Khusus' ? 'selected' : '' }}>Project Khusus</option>
                                @if(!empty($selectedTalent->status_ojt) && !in_array($selectedTalent->status_ojt, ['Belum Penugasan', 'Sudah Penugasan', 'Project Khusus']))
                                    <option value="{{ $selectedTalent->status_ojt }}" selected>{{ $selectedTalent->status_ojt }}</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Lokasi OJT</label>
                            <input type="text" name="lokasi_ojt" value="{{ $selectedTalent->lokasi_ojt }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Region</label>
                            <input type="text" name="region" value="{{ $selectedTalent->region }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Usia</label>
                            <input type="text" name="umur" value="{{ $selectedTalent->umur }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">URL Foto (Google Drive / Link)</label>
                            <input type="text" name="foto_profil" value="{{ $selectedTalent->foto_profil }}" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Foto File Lokal</label>
                            <input type="file" name="foto_file" accept="image/*" class="w-full text-xs text-slate-500 border border-slate-200 rounded-xl p-2">
                        </div>
                    </div>

                    <div class="space-y-3 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Aspek Kepribadian & Profiling</label>
                            <textarea name="profilling" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">{{ $selectedTalent->profilling }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Histori Pekerjaan</label>
                                <textarea name="histori_pekerjaan" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">{{ $selectedTalent->histori_pekerjaan }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Achievement & Prestasi</label>
                                <textarea name="achievement" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">{{ $selectedTalent->achievement }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Strength (Kelebihan)</label>
                                <textarea name="strength" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">{{ $selectedTalent->strength }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Weakness (Area Pengembangan)</label>
                                <textarea name="weakness" rows="3" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">{{ $selectedTalent->weakness }}</textarea>
                            </div>
                        </div>

                        {{-- Progress IDP (%) --}}
                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-700 block mb-2">Progress IDP (Individual Development Plan)</span>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Refreshment (%)</label>
                                    <input type="number" step="0.1" min="0" max="100" id="edit_refreshment_{{ $selectedTalent->id }}" name="refreshment" value="{{ $ref > 0 ? $ref : '' }}" placeholder="100" oninput="calcEditOverallProgress({{ $selectedTalent->id }})" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Soft Skill (%)</label>
                                    <input type="number" step="0.1" min="0" max="100" id="edit_softskill_{{ $selectedTalent->id }}" name="softskill" value="{{ $soft > 0 ? $soft : '' }}" placeholder="100" oninput="calcEditOverallProgress({{ $selectedTalent->id }})" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">OJT Task (%)</label>
                                    <input type="number" step="0.1" min="0" max="100" id="edit_ojt_{{ $selectedTalent->id }}" name="ojt" value="{{ $ojtP > 0 ? $ojtP : '' }}" placeholder="75" oninput="calcEditOverallProgress({{ $selectedTalent->id }})" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Overall Progress (%)</label>
                                    <input type="number" step="0.1" min="0" max="100" id="edit_overall_progress_{{ $selectedTalent->id }}" name="overall_progress" value="{{ $ovp > 0 ? round($ovp, 1) : '' }}" placeholder="91.7" class="w-full text-xs border border-slate-200 rounded-xl p-2.5 focus:outline-none bg-slate-50">
                                    <span class="text-[9px] text-slate-400 mt-0.5 block">Otomatis / manual</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: NILAI & EVALUASI BULANAN --}}
                <div id="tab-eval" class="modal-tab-content hidden space-y-4">
                    <p class="text-xs text-slate-500">
                        Isi nilai evaluasi mentor per bulan (0 - 100). Bulan yang tidak diisi akan otomatis tidak ditampilkan pada grafik.
                    </p>
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                        @php
                            $allMonths = [
                                'januari' => 'Januari', 'februari' => 'Februari', 'maret' => 'Maret',
                                'april' => 'April', 'mei' => 'Mei', 'juni' => 'Juni',
                                'juli' => 'Juli', 'agustus' => 'Agustus', 'september' => 'September',
                                'oktober' => 'Oktober', 'november' => 'November', 'desember' => 'Desember'
                            ];
                        @endphp
                        @foreach($allMonths as $mKey => $mLabel)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">{{ $mLabel }}</label>
                                <input type="number" step="0.1" name="nilai_{{ $mKey }}"
                                       value="{{ $selectedTalent->{"nilai_{$mKey}"} }}"
                                       placeholder="-"
                                       class="w-full text-xs font-bold border border-slate-200 rounded-lg p-2 focus:bg-white">
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai OJT (40%)</label>
                            <input type="number" step="0.1" id="edit_nilai_ojt_{{ $selectedTalent->id }}" name="nilai_ojt" value="{{ $selectedTalent->nilai_ojt }}" oninput="calcEditNilaiAkhir({{ $selectedTalent->id }})" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Evaluasi Panelis (60%)</label>
                            <input type="number" step="0.1" id="edit_nilai_panelis_{{ $selectedTalent->id }}" name="nilai_evaluasi_panelis" value="{{ $selectedTalent->nilai_evaluasi_panelis }}" oninput="calcEditNilaiAkhir({{ $selectedTalent->id }})" class="w-full text-xs border border-slate-200 rounded-xl p-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Akhir (Otomatis)</label>
                            <input type="number" step="0.1" id="edit_nilai_akhir_{{ $selectedTalent->id }}" name="nilai_akhir" value="{{ $selectedTalent->nilai_akhir }}" readonly class="w-full text-xs font-black bg-slate-100 text-slate-800 border border-slate-200 rounded-xl p-2.5 cursor-not-allowed">
                            <span class="text-[10px] text-slate-400 mt-1 block">(OJT &times; 40%) + (Panelis &times; 60%)</span>
                        </div>
                    </div>

                    {{-- Live Preview of Predikat & Grade --}}
                    <div id="edit_grade_preview_{{ $selectedTalent->id }}" class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center gap-3">
                        <div id="edit_grade_badge_{{ $selectedTalent->id }}" class="w-10 h-10 rounded-lg bg-slate-200 text-slate-700 font-black text-lg flex items-center justify-center shrink-0">
                            -
                        </div>
                        <div>
                            <span id="edit_grade_title_{{ $selectedTalent->id }}" class="text-xs font-bold text-slate-800 block">Belum Dinilai</span>
                            <span id="edit_grade_bullets_{{ $selectedTalent->id }}" class="text-[11px] text-slate-500 block">Masukkan Nilai OJT & Nilai Panelis</span>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: CATATAN & SARAN BULANAN --}}
                <div id="tab-notes" class="modal-tab-content hidden space-y-5">
                    <p class="text-xs text-slate-500">
                        Catatan mentor dan development suggestion per bulan. Bulan yang diisi akan otomatis muncul di filter dashboard.
                    </p>
                    <div class="space-y-4">
                        @foreach($allMonths as $mKey => $mLabel)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                                <span class="text-xs font-black text-emerald-800 uppercase flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    {{ $mLabel }}
                                </span>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Catatan Mentor {{ $mLabel }}</label>
                                        <textarea name="catatan_mentor_{{ $mKey }}" rows="2" class="w-full text-xs border border-slate-200 rounded-xl p-2 focus:bg-white">{{ $selectedTalent->{"catatan_mentor_{$mKey}"} }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Development Suggestion {{ $mLabel }}</label>
                                        <textarea name="dev_suggestion_{{ $mKey }}" rows="2" class="w-full text-xs border border-slate-200 rounded-xl p-2 focus:bg-white">{{ $selectedTalent->{"dev_suggestion_{$mKey}"} }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 shrink-0">
                    <button type="button" onclick="closeEditModal({{ $selectedTalent->id }})" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ==================== MODAL HAPUS SEMUA DATA ==================== --}}
    <div id="modal-delete-all" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-rose-200 p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <i data-lucide="alert-octagon" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Hapus Semua Data Talent?</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Tindakan ini akan mengosongkan seluruh data Profil Talent di sistem. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <form action="{{ route('administrasi.profil-talent.destroyAll') }}" method="POST" class="flex items-center justify-center gap-2 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteAllModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                    Ya, Hapus Semua
                </button>
            </form>
        </div>
    </div>

    {{-- ==================== MODAL HAPUS SATU TALENT ==================== --}}
    <div id="modal-delete-single" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-rose-200 p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                <i data-lucide="trash-2" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Hapus Data Talent?</h3>
                <p id="delete-talent-name" class="text-xs text-slate-500 mt-1">
                    Apakah Anda yakin ingin menghapus data talent ini?
                </p>
            </div>
            <form id="delete-single-form" action="" method="POST" class="flex items-center justify-center gap-2 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteSingleModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Data for selected talent monthly notes & suggestions
        const monthlyNotesData = {
            @if($selectedTalent)
                @foreach(['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'] as $mKey)
                    "{{ $mKey }}": {
                        catatan: {!! json_encode($selectedTalent->{"catatan_mentor_{$mKey}"} ?: '') !!},
                        suggestion: {!! json_encode($selectedTalent->{"dev_suggestion_{$mKey}"} ?: '') !!}
                    },
                @endforeach
            @endif
        };

        const defaultGeneralNote = {!! json_encode($selectedTalent ? ($selectedTalent->catatan_mentor_general ?: '') : '') !!};
        const defaultGeneralSuggestion = {!! json_encode($selectedTalent ? ($selectedTalent->development_suggestion_general ?: '') : '') !!};

        function switchMentorMonth(monthKey) {
            const data = monthlyNotesData[monthKey] || {};
            const fbBox = document.getElementById('feedback-evaluasi-content');
            const sugBox = document.getElementById('dev-suggestion-content');

            const textCatatan = data.catatan || defaultGeneralNote || 'Belum ada catatan evaluasi untuk bulan ini.';
            const textSuggestion = data.suggestion || defaultGeneralSuggestion || 'Belum ada saran pengembangan untuk bulan ini.';

            if (fbBox) fbBox.textContent = textCatatan;
            if (sugBox) sugBox.textContent = textSuggestion;
        }

        // Live Directory Search & Filter
        const searchInput = document.getElementById('directory-search');
        const categorySelect = document.getElementById('directory-category');
        const talentCards = document.querySelectorAll('.talent-item-card');
        const countBadge = document.getElementById('talent-count-badge');

        function filterDirectory() {
            const query = (searchInput?.value || '').toLowerCase().trim();
            const selectedCat = categorySelect?.value || 'all';
            let visibleCount = 0;

            talentCards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const nik = card.getAttribute('data-nik') || '';
                const cat = card.getAttribute('data-cat') || '';

                const matchesQuery = !query || name.includes(query) || nik.includes(query);
                const matchesCat = (selectedCat === 'all') || (cat === selectedCat);

                if (matchesQuery && matchesCat) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (countBadge) {
                countBadge.textContent = `${visibleCount} Talent`;
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterDirectory);
        if (categorySelect) categorySelect.addEventListener('change', filterDirectory);

        // Modal Controls
        function openImportModal() {
            document.getElementById('modal-import')?.classList.remove('hidden');
        }
        function closeImportModal() {
            document.getElementById('modal-import')?.classList.add('hidden');
        }

        function openAddModal() {
            document.getElementById('modal-add')?.classList.remove('hidden');
            calcAddNilaiAkhir();
        }
        function closeAddModal() {
            document.getElementById('modal-add')?.classList.add('hidden');
        }

        function openEditModal(id) {
            document.getElementById(`modal-edit-${id}`)?.classList.remove('hidden');
            calcEditNilaiAkhir(id);
        }
        function closeEditModal(id) {
            document.getElementById(`modal-edit-${id}`)?.classList.add('hidden');
        }

        function getGradeDetails(score) {
            if (score === null || score === undefined || isNaN(score) || score <= 0) {
                return null;
            }
            if (score > 80) {
                return {
                    grade: 'A',
                    title: 'Excellent',
                    badgeBg: 'bg-blue-600',
                    badgeText: 'text-white',
                    badgeBorder: 'border-blue-400',
                    textColor: 'text-blue-600',
                    bullets: ['Ready', 'Placement / Talent Pool']
                };
            } else if (score >= 70) {
                return {
                    grade: 'B',
                    title: 'Good',
                    badgeBg: 'bg-emerald-600',
                    badgeText: 'text-white',
                    badgeBorder: 'border-emerald-400',
                    textColor: 'text-emerald-600',
                    bullets: ['Ready', 'Placement / Talent Pool']
                };
            } else if (score >= 60) {
                return {
                    grade: 'C',
                    title: 'Need Improvement',
                    badgeBg: 'bg-amber-500',
                    badgeText: 'text-white',
                    badgeBorder: 'border-amber-400',
                    textColor: 'text-amber-600',
                    bullets: ['Potential', 'Next Program']
                };
            } else {
                return {
                    grade: 'D',
                    title: 'Unsatisfactory',
                    badgeBg: 'bg-rose-600',
                    badgeText: 'text-white',
                    badgeBorder: 'border-rose-400',
                    textColor: 'text-rose-600',
                    bullets: ['Not Fit', 'Not Proper', 'Not Ready']
                };
            }
        }

        function calcEditNilaiAkhir(id) {
            const ojtInput = document.getElementById(`edit_nilai_ojt_${id}`);
            const panelisInput = document.getElementById(`edit_nilai_panelis_${id}`);
            const akhirInput = document.getElementById(`edit_nilai_akhir_${id}`);
            const badgeEl = document.getElementById(`edit_grade_badge_${id}`);
            const titleEl = document.getElementById(`edit_grade_title_${id}`);
            const bulletsEl = document.getElementById(`edit_grade_bullets_${id}`);

            const ojtRaw = ojtInput?.value !== '' ? parseFloat(ojtInput?.value) : NaN;
            const panelisRaw = panelisInput?.value !== '' ? parseFloat(panelisInput?.value) : NaN;

            if (!isNaN(ojtRaw) || !isNaN(panelisRaw)) {
                const ojt = !isNaN(ojtRaw) ? ojtRaw : 0;
                const panelis = !isNaN(panelisRaw) ? panelisRaw : 0;
                const total = Math.round(((ojt * 0.40) + (panelis * 0.60)) * 100) / 100;
                if (akhirInput) akhirInput.value = total;

                const info = getGradeDetails(total);
                if (info) {
                    if (badgeEl) {
                        badgeEl.className = `w-10 h-10 rounded-lg ${info.badgeBg} ${info.badgeText} font-black text-lg flex items-center justify-center shrink-0 border ${info.badgeBorder} shadow-sm`;
                        badgeEl.textContent = info.grade;
                    }
                    if (titleEl) {
                        titleEl.className = `text-xs font-bold ${info.textColor} block`;
                        titleEl.textContent = `${info.title} (Grade ${info.grade})`;
                    }
                    if (bulletsEl) {
                        bulletsEl.textContent = info.bullets.join(' • ');
                    }
                }
            } else {
                if (akhirInput) akhirInput.value = '';
                if (badgeEl) {
                    badgeEl.className = 'w-10 h-10 rounded-lg bg-slate-200 text-slate-700 font-black text-lg flex items-center justify-center shrink-0';
                    badgeEl.textContent = '-';
                }
                if (titleEl) {
                    titleEl.className = 'text-xs font-bold text-slate-800 block';
                    titleEl.textContent = 'Belum Dinilai';
                }
                if (bulletsEl) {
                    bulletsEl.textContent = 'Masukkan Nilai OJT & Nilai Panelis';
                }
            }
        }

        function calcAddNilaiAkhir() {
            const ojtInput = document.getElementById('add_nilai_ojt');
            const panelisInput = document.getElementById('add_nilai_panelis');
            const akhirInput = document.getElementById('add_nilai_akhir');
            const badgeEl = document.getElementById('add_grade_badge');
            const titleEl = document.getElementById('add_grade_title');
            const bulletsEl = document.getElementById('add_grade_bullets');

            const ojtRaw = ojtInput?.value !== '' ? parseFloat(ojtInput?.value) : NaN;
            const panelisRaw = panelisInput?.value !== '' ? parseFloat(panelisInput?.value) : NaN;

            if (!isNaN(ojtRaw) || !isNaN(panelisRaw)) {
                const ojt = !isNaN(ojtRaw) ? ojtRaw : 0;
                const panelis = !isNaN(panelisRaw) ? panelisRaw : 0;
                const total = Math.round(((ojt * 0.40) + (panelis * 0.60)) * 100) / 100;
                if (akhirInput) akhirInput.value = total;

                const info = getGradeDetails(total);
                if (info) {
                    if (badgeEl) {
                        badgeEl.className = `w-10 h-10 rounded-lg ${info.badgeBg} ${info.badgeText} font-black text-lg flex items-center justify-center shrink-0 border ${info.badgeBorder} shadow-sm`;
                        badgeEl.textContent = info.grade;
                    }
                    if (titleEl) {
                        titleEl.className = `text-xs font-bold ${info.textColor} block`;
                        titleEl.textContent = `${info.title} (Grade ${info.grade})`;
                    }
                    if (bulletsEl) {
                        bulletsEl.textContent = info.bullets.join(' • ');
                    }
                }
            } else {
                if (akhirInput) akhirInput.value = '';
                if (badgeEl) {
                    badgeEl.className = 'w-10 h-10 rounded-lg bg-slate-200 text-slate-700 font-black text-lg flex items-center justify-center shrink-0';
                    badgeEl.textContent = '-';
                }
                if (titleEl) {
                    titleEl.className = 'text-xs font-bold text-slate-800 block';
                    titleEl.textContent = 'Belum Dinilai';
                }
                if (bulletsEl) {
                    bulletsEl.textContent = 'Masukkan Nilai OJT & Nilai Panelis';
                }
            }
        }

        function calcAddOverallProgress() {
            const r = parseFloat(document.getElementById('add_refreshment')?.value || NaN);
            const s = parseFloat(document.getElementById('add_softskill')?.value || NaN);
            const o = parseFloat(document.getElementById('add_ojt')?.value || NaN);
            const ovpEl = document.getElementById('add_overall_progress');
            const valid = [r, s, o].filter(x => !isNaN(x));
            if (valid.length > 0 && ovpEl) {
                ovpEl.value = Math.round((valid.reduce((a, b) => a + b, 0) / valid.length) * 10) / 10;
            }
        }

        function calcEditOverallProgress(id) {
            const r = parseFloat(document.getElementById(`edit_refreshment_${id}`)?.value || NaN);
            const s = parseFloat(document.getElementById(`edit_softskill_${id}`)?.value || NaN);
            const o = parseFloat(document.getElementById(`edit_ojt_${id}`)?.value || NaN);
            const ovpEl = document.getElementById(`edit_overall_progress_${id}`);
            const valid = [r, s, o].filter(x => !isNaN(x));
            if (valid.length > 0 && ovpEl) {
                ovpEl.value = Math.round((valid.reduce((a, b) => a + b, 0) / valid.length) * 10) / 10;
            }
        }

        function switchModalTab(tabId) {
            document.querySelectorAll('.modal-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.modal-tab-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-emerald-100', 'text-emerald-800');
                btn.classList.add('text-slate-600');
            });
            const activeTab = document.getElementById(tabId);
            if (activeTab) activeTab.classList.remove('hidden');

            const activeBtn = document.querySelector(`.modal-tab-btn[data-target="${tabId}"]`);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-emerald-100', 'text-emerald-800');
                activeBtn.classList.remove('text-slate-600');
            }
        }

        function openDeleteAllModal() {
            document.getElementById('modal-delete-all')?.classList.remove('hidden');
        }
        function closeDeleteAllModal() {
            document.getElementById('modal-delete-all')?.classList.add('hidden');
        }

        function openDeleteSingleModal(id, name) {
            const form = document.getElementById('delete-single-form');
            if (form) {
                form.action = `/administrasi/profil-talent/${id}`;
            }
            const nameEl = document.getElementById('delete-talent-name');
            if (nameEl) {
                nameEl.textContent = `Apakah Anda yakin ingin menghapus data talent ${name}?`;
            }
            document.getElementById('modal-delete-single')?.classList.remove('hidden');
        }
        function closeDeleteSingleModal() {
            document.getElementById('modal-delete-single')?.classList.add('hidden');
        }
    </script>
    @endpush
</x-admin-layout>
