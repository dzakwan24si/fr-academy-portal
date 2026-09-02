@extends('Administrasi.Layouts.app')

@section('title', 'VRA (Vehicle Running Account) | FR Academy Enterprise Portal')

@section('content')
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- 1. Page Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-primary-900 tracking-tight">VRA</h2>
            <p class="text-[#71717A] mt-1 text-sm">Pengelolaan biaya operasional dan penggunaan kendaraan berdasarkan periode</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Import CSV Button -->
            <button onclick="openModal('importModal')" 
                    class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="upload" class="w-4 h-4 text-[#71717A]"></i> Import CSV
            </button>

            <!-- Export Report Button -->
            <a href="{{ route('administrasi.vra.export', ['year' => $selectedYear, 'period' => $selectedPeriod]) }}" 
               class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i> Export Report
            </a>

            <!-- Add Data Button -->
            @if($selectedPeriod === 'overall')
                <button onclick="handleOverallAddData()" 
                        class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> + Tambah Data
                </button>
            @else
                <button onclick="openAddModal()" 
                        class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> + Tambah Data
                </button>
            @endif
        </div>
    </div>

    <!-- 2. Year Navigation Tabs -->
    <div class="bg-white border border-[#E5E7EB] rounded-xl p-2 shadow-2xs">
        <div class="flex items-center gap-2 overflow-x-auto">
            @foreach ($years as $year)
                <a href="{{ route('administrasi.vra.index', ['year' => $year, 'period' => $selectedPeriod, 'search' => $search]) }}" 
                   class="px-6 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap {{ $selectedYear == $year ? 'text-primary-700 font-bold border-b-2 border-primary-500 bg-primary-50/50' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                    {{ $year }}
                </a>
            @endforeach

            <!-- + Tahun Baru Button -->
            <button onclick="openNewYearModal()" 
                    class="px-4 py-2 rounded-lg text-sm font-medium text-[#71717A] hover:text-primary-600 hover:bg-primary-50/60 border border-dashed border-[#D1D5DB] flex items-center gap-1.5 ml-auto transition-colors whitespace-nowrap">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> + Tahun Baru
            </button>
        </div>
    </div>

    <!-- 3. Period Navigation Tabs (Overall + Jan..Des) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <!-- Overall Tab -->
        <a href="{{ route('administrasi.vra.index', ['year' => $selectedYear, 'period' => 'overall', 'search' => $search]) }}" 
           class="px-5 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ $selectedPeriod === 'overall' ? 'bg-[#4B6A34] text-white shadow-sm' : 'bg-white border border-[#E5E7EB] text-[#71717A] hover:bg-[#F4F4F5] hover:text-[#18181B]' }}">
            Overall
        </a>

        <!-- Month Tabs -->
        @foreach ($months as $mNum => $mName)
            <a href="{{ route('administrasi.vra.index', ['year' => $selectedYear, 'period' => $mNum, 'search' => $search]) }}" 
               class="px-5 py-2 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ $selectedPeriod == (string)$mNum ? 'bg-[#4B6A34] text-white shadow-sm' : 'bg-white border border-[#E5E7EB] text-[#71717A] hover:bg-[#F4F4F5] hover:text-[#18181B]' }}">
                {{ $mName }}
            </a>
        @endforeach
    </div>

    <!-- 4. Data Table Container -->
    <div class="bg-white border border-[#E5E7EB] rounded-xl shadow-sm overflow-hidden">
        <!-- Card Header with Title & Search -->
        <div class="p-6 border-b border-[#E5E7EB] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-[#18181B]">
                    @if($selectedPeriod === 'overall')
                        Ringkasan Biaya {{ $selectedYear }}
                    @else
                        Data VRA — {{ $months[(int)$selectedPeriod] ?? '' }} {{ $selectedYear }}
                    @endif
                </h3>
                <p class="text-xs text-[#71717A] mt-0.5">
                    @if($selectedPeriod === 'overall')
                        Total akumulasi biaya operasional tahunan seluruh kendaraan
                    @else
                        Rincian operasional bulanan kendaraan periode {{ $months[(int)$selectedPeriod] ?? '' }} {{ $selectedYear }}
                    @endif
                </p>
            </div>

            <!-- Search and Filter Form -->
            <div class="flex items-center gap-3">
                <form action="{{ route('administrasi.vra.index') }}" method="GET" class="relative w-64 md:w-80">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <input type="hidden" name="period" value="{{ $selectedPeriod }}">
                    
                    <span class="absolute inset-y-0 left-3 flex items-center text-[#A1A1AA] pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Polisi / Kendaraan..." 
                           class="w-full bg-[#F8F9FA] border border-[#E5E7EB] rounded-lg py-2 pl-9 pr-8 text-xs focus:outline-none focus:ring-1 focus:ring-primary-500 focus:bg-white text-[#18181B]">
                    
                    @if($search)
                        <a href="{{ route('administrasi.vra.index', ['year' => $selectedYear, 'period' => $selectedPeriod]) }}" 
                           class="absolute inset-y-0 right-3 flex items-center text-xs text-[#EF4444] hover:text-[#DC2626]">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </form>

                <button class="w-9 h-9 rounded-lg border border-[#E5E7EB] flex items-center justify-center text-[#71717A] hover:bg-[#F4F4F5] transition-colors" title="Filter options">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Table Content with Horizontal Scroll & Sticky Columns -->
        <div class="overflow-x-auto relative">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    @if($selectedPeriod === 'overall')
                        <!-- OVERALL VIEW HEADERS -->
                        <tr class="bg-[#EFF6FF]/60 text-[#475569] font-semibold text-xs border-b border-[#E5E7EB]">
                            <th class="py-3.5 px-5 uppercase tracking-wider sticky-col-1 bg-[#EFF6FF] shadow-xs">No. Polisi</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider sticky-col-2 bg-[#EFF6FF] shadow-xs">Kendaraan</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">Upah/Tahun (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">BBM/Tahun (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">Prev. Maint./Tahun (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">Biaya Lainnya (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right font-bold text-[#1E293B] bg-slate-50/50">Total Biaya (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">KM/Tahun</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    @else
                        <!-- MONTHLY VIEW HEADERS -->
                        <tr class="bg-[#EFF6FF]/60 text-[#475569] font-semibold text-xs border-b border-[#E5E7EB]">
                            <th class="py-3.5 px-5 uppercase tracking-wider sticky-col-1 bg-[#EFF6FF] shadow-xs">No. Polisi</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider sticky-col-2 bg-[#EFF6FF] shadow-xs">Kendaraan</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">Upah/Bulan (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">BBM/Bulan (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">Prev. Maint./Bulan (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">Biaya Lainnya (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right font-bold text-[#1E293B] bg-slate-50/50">Total Biaya (IDR)</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">KM Awal</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">KM Akhir</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-right">KM/Bulan</th>
                            <th class="py-3.5 px-5 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    @endif
                </thead>
                <tbody class="divide-y divide-[#E5E7EB] text-xs text-[#18181B]">
                    @forelse ($records as $item)
                        <tr class="hover:bg-[#F8F9FA] transition-colors group">
                            <!-- No. Polisi -->
                            <td class="py-3.5 px-5 font-semibold sticky-col-1 bg-white group-hover:bg-[#F8F9FA] shadow-xs whitespace-nowrap">
                                {{ $item->nomor_polisi }}
                            </td>

                            <!-- Kendaraan -->
                            <td class="py-3.5 px-5 font-medium sticky-col-2 bg-white group-hover:bg-[#F8F9FA] shadow-xs whitespace-nowrap text-[#374151]">
                                {{ $item->kendaraan }}
                            </td>

                            @if($selectedPeriod === 'overall')
                                <!-- Overall Columns -->
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->upah_tahun, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->BBM_tahun, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->preventif_maintenance_tahun, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->biaya_lainnya_tahun, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono font-bold text-[#111827] bg-slate-50/40">
                                    {{ number_format($item->total_biaya_tahun, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->KM_tahun, 0, ',', '.') }}
                                </td>
                            @else
                                <!-- Monthly Columns -->
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->upah_bulan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->BBM_bulan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->preventif_maintenance_bulan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->biaya_lainnya_bulan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono font-bold text-[#111827] bg-slate-50/40">
                                    {{ number_format($item->total_biaya_bulan, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->KM_awal, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->KM_akhir, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap font-mono text-[#4B5563]">
                                    {{ number_format($item->KM_bulan, 0, ',', '.') }}
                                </td>
                            @endif

                            <!-- Row Actions -->
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- View Detail -->
                                    <button type="button" onclick="showDetailModal({{ $item->vra_id }})" 
                                            class="p-1 text-[#71717A] hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Edit Data -->
                                    <button type="button" onclick="openEditModal({{ $item->vra_id }})" 
                                            class="p-1 text-[#71717A] hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors" title="Edit Data">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete Data -->
                                    <button type="button" onclick="confirmDelete({{ $item->vra_id }}, '{{ $item->nomor_polisi }}')" 
                                            class="p-1 text-[#71717A] hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors" title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $selectedPeriod === 'overall' ? 9 : 11 }}" class="py-12 text-center text-[#71717A]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="inbox" class="w-8 h-8 text-[#A1A1AA]"></i>
                                    <p class="font-medium text-sm">Belum ada data VRA untuk periode ini.</p>
                                    @if($selectedPeriod !== 'overall')
                                        <button onclick="openAddModal()" class="text-xs text-primary-600 hover:underline font-medium mt-1">
                                            + Tambah data untuk bulan ini
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- SUMMARY TOTAL FOOTER ROW -->
                @if($records->count() > 0)
                    <tfoot>
                        <tr class="bg-[#EFF6FF] text-[#1E293B] font-bold text-xs border-t-2 border-[#CBD5E1]">
                            <td class="py-4 px-5 sticky-col-1 bg-[#EFF6FF] shadow-xs">
                                Total
                            </td>
                            <td class="py-4 px-5 sticky-col-2 bg-[#EFF6FF] shadow-xs whitespace-nowrap">
                                {{ $totals['count'] }} Kendaraan
                            </td>

                            @if($selectedPeriod === 'overall')
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['upah'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['bbm'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['maintenance'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['lainnya'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-emerald-800 bg-emerald-50/70 text-sm">
                                    {{ number_format($totals['total_biaya'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['km'], 0, ',', '.') }}
                                </td>
                            @else
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['upah'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['bbm'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['maintenance'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['lainnya'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-emerald-800 bg-emerald-50/70 text-sm">
                                    {{ number_format($totals['total_biaya'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#475569]">-</td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#475569]">-</td>
                                <td class="py-4 px-5 text-right whitespace-nowrap font-mono text-[#0F172A]">
                                    {{ number_format($totals['km_bulan'], 0, ',', '.') }}
                                </td>
                            @endif
                            
                            <td class="py-4 px-5"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="p-5 border-t border-[#E5E7EB] flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-[#71717A]">
                Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} entries
            </p>
            <div>
                {{ $records->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- 1. ADD DATA MODAL -->
    <div id="addModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden my-8 animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F8F9FA]">
                <div>
                    <h3 class="text-lg font-bold text-[#18181B] flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-primary-500"></i> Tambah Data VRA
                    </h3>
                    <p class="text-xs text-[#71717A] mt-0.5">
                        Periode: <span class="font-bold text-primary-700">{{ $months[(int)$selectedPeriod] ?? 'Jan' }} {{ $selectedYear }}</span> (Ditentukan otomatis)
                    </p>
                </div>
                <button onclick="closeModal('addModal')" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('administrasi.vra.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                <!-- Automatic Context Hidden Inputs (No manual input of Year or Month) -->
                <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                <input type="hidden" name="bulan" value="{{ $selectedPeriod === 'overall' ? 1 : (int)$selectedPeriod }}">

                <!-- Section 1: Informasi Kendaraan -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-primary-900 mb-3 flex items-center gap-2">
                        <i data-lucide="truck" class="w-4 h-4 text-primary-500"></i> 1. Informasi Kendaraan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nomor Polisi <span class="text-rose-500">*</span></label>
                            <input type="text" name="nomor_polisi" required placeholder="Contoh: B 1234 ABC" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 uppercase font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis / Nama Kendaraan <span class="text-rose-500">*</span></label>
                            <input type="text" name="kendaraan" required placeholder="Contoh: Toyota Hilux Double Cabin" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Biaya Operasional -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-primary-900 mb-3 flex items-center gap-2">
                        <i data-lucide="banknote" class="w-4 h-4 text-primary-500"></i> 2. Biaya Operasional (IDR)
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Upah -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Upah / Bulan</label>
                            <input type="number" step="0.01" min="0" name="upah_bulan" id="add_upah_bulan" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Upah / Tahun</label>
                            <input type="number" step="0.01" min="0" name="upah_tahun" id="add_upah_tahun" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- BBM -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">BBM / Bulan</label>
                            <input type="number" step="0.01" min="0" name="BBM_bulan" id="add_bbm_bulan" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">BBM / Tahun</label>
                            <input type="number" step="0.01" min="0" name="BBM_tahun" id="add_bbm_tahun" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- Preventive Maintenance -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Prev. Maintenance / Bulan</label>
                            <input type="number" step="0.01" min="0" name="preventif_maintenance_bulan" id="add_maint_bulan" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Prev. Maintenance / Tahun</label>
                            <input type="number" step="0.01" min="0" name="preventif_maintenance_tahun" id="add_maint_tahun" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- Biaya Lainnya -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Lainnya / Bulan</label>
                            <input type="number" step="0.01" min="0" name="biaya_lainnya_bulan" id="add_lainnya_bulan" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Lainnya / Tahun</label>
                            <input type="number" step="0.01" min="0" name="biaya_lainnya_tahun" id="add_lainnya_tahun" oninput="calcAddTotals()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- Computed Totals Preview -->
                        <div class="bg-primary-50/50 p-3 rounded-lg border border-primary-100">
                            <label class="block text-xs font-bold text-primary-900">Total Biaya / Bulan (Auto)</label>
                            <p id="preview_add_total_bulan" class="text-sm font-mono font-bold text-primary-700 mt-1">IDR 0</p>
                        </div>
                        <div class="bg-primary-50/50 p-3 rounded-lg border border-primary-100">
                            <label class="block text-xs font-bold text-primary-900">Total Biaya / Tahun (Auto)</label>
                            <p id="preview_add_total_tahun" class="text-sm font-mono font-bold text-primary-700 mt-1">IDR 0</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kilometer -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-primary-900 mb-3 flex items-center gap-2">
                        <i data-lucide="gauge" class="w-4 h-4 text-primary-500"></i> 3. Penggunaan Kilometer (KM)
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM Awal</label>
                            <input type="number" step="0.01" min="0" name="KM_awal" id="add_km_awal" oninput="calcAddKM()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM Akhir</label>
                            <input type="number" step="0.01" min="0" name="KM_akhir" id="add_km_akhir" oninput="calcAddKM()" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM / Bulan</label>
                            <input type="number" step="0.01" min="0" name="KM_bulan" id="add_km_bulan" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM / Tahun</label>
                            <input type="number" step="0.01" min="0" name="KM_tahun" id="add_km_tahun" placeholder="0" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('addModal')" 
                            class="px-4 py-2 rounded-lg text-xs font-semibold text-[#71717A] hover:bg-[#F4F4F5] transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-xs rounded-lg px-5 py-2.5 shadow-sm transition-colors flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Data VRA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. EDIT DATA MODAL -->
    <div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden my-8 animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F8F9FA]">
                <div>
                    <h3 class="text-lg font-bold text-[#18181B] flex items-center gap-2">
                        <i data-lucide="edit-3" class="w-5 h-5 text-amber-600"></i> Edit Data VRA
                    </h3>
                    <p class="text-xs text-[#71717A] mt-0.5" id="edit_periode_info">
                        Periode dikontrol oleh data existing (tidak dapat diubah sembarangan)
                    </p>
                </div>
                <button onclick="closeModal('editModal')" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="editForm" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="period" value="{{ $selectedPeriod }}">

                <!-- Section 1: Informasi Kendaraan -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-primary-900 mb-3 flex items-center gap-2">
                        <i data-lucide="truck" class="w-4 h-4 text-primary-500"></i> 1. Informasi Kendaraan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nomor Polisi <span class="text-rose-500">*</span></label>
                            <input type="text" name="nomor_polisi" id="edit_nomor_polisi" required 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 uppercase font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Jenis / Nama Kendaraan <span class="text-rose-500">*</span></label>
                            <input type="text" name="kendaraan" id="edit_kendaraan" required 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Biaya Operasional -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-primary-900 mb-3 flex items-center gap-2">
                        <i data-lucide="banknote" class="w-4 h-4 text-primary-500"></i> 2. Biaya Operasional (IDR)
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Upah -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Upah / Bulan</label>
                            <input type="number" step="0.01" min="0" name="upah_bulan" id="edit_upah_bulan" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Upah / Tahun</label>
                            <input type="number" step="0.01" min="0" name="upah_tahun" id="edit_upah_tahun" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- BBM -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">BBM / Bulan</label>
                            <input type="number" step="0.01" min="0" name="BBM_bulan" id="edit_bbm_bulan" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">BBM / Tahun</label>
                            <input type="number" step="0.01" min="0" name="BBM_tahun" id="edit_bbm_tahun" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- Preventive Maintenance -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Prev. Maintenance / Bulan</label>
                            <input type="number" step="0.01" min="0" name="preventif_maintenance_bulan" id="edit_maint_bulan" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Prev. Maintenance / Tahun</label>
                            <input type="number" step="0.01" min="0" name="preventif_maintenance_tahun" id="edit_maint_tahun" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- Biaya Lainnya -->
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Lainnya / Bulan</label>
                            <input type="number" step="0.01" min="0" name="biaya_lainnya_bulan" id="edit_lainnya_bulan" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">Biaya Lainnya / Tahun</label>
                            <input type="number" step="0.01" min="0" name="biaya_lainnya_tahun" id="edit_lainnya_tahun" oninput="calcEditTotals()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>

                        <!-- Computed Totals Preview -->
                        <div class="bg-primary-50/50 p-3 rounded-lg border border-primary-100">
                            <label class="block text-xs font-bold text-primary-900">Total Biaya / Bulan (Auto)</label>
                            <p id="preview_edit_total_bulan" class="text-sm font-mono font-bold text-primary-700 mt-1">IDR 0</p>
                        </div>
                        <div class="bg-primary-50/50 p-3 rounded-lg border border-primary-100">
                            <label class="block text-xs font-bold text-primary-900">Total Biaya / Tahun (Auto)</label>
                            <p id="preview_edit_total_tahun" class="text-sm font-mono font-bold text-primary-700 mt-1">IDR 0</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kilometer -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-primary-900 mb-3 flex items-center gap-2">
                        <i data-lucide="gauge" class="w-4 h-4 text-primary-500"></i> 3. Penggunaan Kilometer (KM)
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM Awal</label>
                            <input type="number" step="0.01" min="0" name="KM_awal" id="edit_km_awal" oninput="calcEditKM()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM Akhir</label>
                            <input type="number" step="0.01" min="0" name="KM_akhir" id="edit_km_akhir" oninput="calcEditKM()" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM / Bulan</label>
                            <input type="number" step="0.01" min="0" name="KM_bulan" id="edit_km_bulan" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1">KM / Tahun</label>
                            <input type="number" step="0.01" min="0" name="KM_tahun" id="edit_km_tahun" 
                                   class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-xs focus:ring-2 focus:ring-primary-100 focus:border-primary-500 font-mono">
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('editModal')" 
                            class="px-4 py-2 rounded-lg text-xs font-semibold text-[#71717A] hover:bg-[#F4F4F5] transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-xs rounded-lg px-5 py-2.5 shadow-sm transition-colors flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. DETAIL VIEW MODAL -->
    <div id="detailModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden my-8 animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between bg-primary-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primary-950" id="detail_no_polisi">B 1234 ABC</h3>
                        <p class="text-xs text-[#71717A]" id="detail_kendaraan">Toyota Hilux</p>
                    </div>
                </div>
                <button onclick="closeModal('detailModal')" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 space-y-6">
                <!-- Metadata Periode -->
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 p-3 rounded-xl">
                    <span class="text-xs font-semibold text-[#64748B]">Periode Tercatat:</span>
                    <span id="detail_periode_badge" class="px-3 py-1 bg-white border border-slate-200 rounded-full text-xs font-bold text-primary-800 shadow-2xs">
                        Tahun 2026 - Bulan 1 (Januari)
                    </span>
                </div>

                <!-- Biaya Bulanan vs Biaya Tahunan Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Biaya Bulanan -->
                    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-primary-800 border-b border-[#E5E7EB] pb-2 mb-3 flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-primary-500"></i> Biaya Bulanan
                        </h4>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">Upah/Bulan</span>
                                <span id="detail_upah_bulan" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">BBM/Bulan</span>
                                <span id="detail_bbm_bulan" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">Prev. Maintenance</span>
                                <span id="detail_maint_bulan" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">Biaya Lainnya</span>
                                <span id="detail_lainnya_bulan" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between pt-2 text-sm font-bold text-primary-900 border-t border-[#E2E8F0]">
                                <span>Total Biaya/Bulan</span>
                                <span id="detail_total_bulan" class="font-mono text-primary-700">IDR 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Biaya Tahunan -->
                    <div class="bg-white border border-[#E5E7EB] rounded-xl p-4 shadow-2xs">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-primary-800 border-b border-[#E5E7EB] pb-2 mb-3 flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-4 h-4 text-primary-500"></i> Biaya Tahunan
                        </h4>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">Upah/Tahun</span>
                                <span id="detail_upah_tahun" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">BBM/Tahun</span>
                                <span id="detail_bbm_tahun" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">Prev. Maintenance</span>
                                <span id="detail_maint_tahun" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-dashed border-[#F1F5F9]">
                                <span class="text-[#64748B]">Biaya Lainnya</span>
                                <span id="detail_lainnya_tahun" class="font-mono font-semibold text-[#1E293B]">IDR 0</span>
                            </div>
                            <div class="flex justify-between pt-2 text-sm font-bold text-primary-900 border-t border-[#E2E8F0]">
                                <span>Total Biaya/Tahun</span>
                                <span id="detail_total_tahun" class="font-mono text-primary-700">IDR 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kilometer Metrics -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#334155] mb-3 flex items-center gap-2">
                        <i data-lucide="gauge" class="w-4 h-4 text-primary-500"></i> Penggunaan Kilometer
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <p class="text-[10px] text-[#64748B] uppercase font-semibold">KM Awal</p>
                            <p id="detail_km_awal" class="text-sm font-bold font-mono text-[#0F172A] mt-1">0</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <p class="text-[10px] text-[#64748B] uppercase font-semibold">KM Akhir</p>
                            <p id="detail_km_akhir" class="text-sm font-bold font-mono text-[#0F172A] mt-1">0</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <p class="text-[10px] text-[#64748B] uppercase font-semibold">KM / Bulan</p>
                            <p id="detail_km_bulan" class="text-sm font-bold font-mono text-primary-700 mt-1">0</p>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <p class="text-[10px] text-[#64748B] uppercase font-semibold">KM / Tahun</p>
                            <p id="detail_km_tahun" class="text-sm font-bold font-mono text-primary-700 mt-1">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Button -->
            <div class="p-4 border-t border-[#E5E7EB] bg-[#F8F9FA] flex justify-end">
                <button onclick="closeModal('detailModal')" 
                        class="px-5 py-2 rounded-lg text-xs font-semibold bg-[#4B6A34] text-white hover:bg-[#3d562a] transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- 4. DELETE CONFIRMATION MODAL -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-[#18181B]">Hapus Data VRA?</h3>
                <p class="text-xs text-[#71717A] mt-2">
                    Apakah Anda yakin ingin menghapus data VRA kendaraan <strong id="delete_target_name" class="text-[#18181B]"></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <form id="deleteForm" method="POST" class="p-4 bg-[#F8F9FA] border-t border-[#E5E7EB] flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <input type="hidden" name="period" value="{{ $selectedPeriod }}">
                <button type="button" onclick="closeModal('deleteModal')" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold text-[#71717A] hover:bg-[#E5E7EB] transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>

    <!-- 5. NEW YEAR MODAL -->
    <div id="newYearModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between">
                <h3 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-primary-500"></i> Tambah Periode Tahun
                </h3>
                <button onclick="closeModal('newYearModal')" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="{{ route('administrasi.vra.year') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Masukkan Tahun Baru</label>
                    <input type="number" name="new_year" min="2020" max="2099" value="{{ (int)$selectedYear + 1 }}" required 
                           class="w-full bg-white border border-[#D1D5DB] rounded-lg px-3.5 py-2 text-sm font-semibold focus:ring-2 focus:ring-primary-100 focus:border-primary-500">
                </div>
                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('newYearModal')" 
                            class="px-4 py-2 rounded-lg text-xs font-semibold text-[#71717A] hover:bg-[#F4F4F5]">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-xs rounded-lg px-4 py-2 shadow-sm transition-colors">
                        Pilih Tahun Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. OVERALL NOTICE MODAL -->
    <div id="overallNoticeModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 text-center">
                <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="info" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-[#18181B]">Pemberitahuan Tambah Data</h3>
                <p class="text-xs text-[#71717A] mt-2 leading-relaxed">
                    Tab <strong>Overall</strong> merupakan ringkasan tahunan. Untuk menambahkan data VRA baru, silakan pilih salah satu <strong>Tab Bulan (Jan – Des)</strong> terlebih dahulu.
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-1.5">
                    @foreach($months as $mNum => $mName)
                        <a href="{{ route('administrasi.vra.index', ['year' => $selectedYear, 'period' => $mNum]) }}" 
                           class="px-3 py-1 bg-slate-100 hover:bg-primary-500 hover:text-white rounded-md text-xs font-medium text-[#475569] transition-colors">
                            {{ $mName }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="p-4 bg-[#F8F9FA] border-t border-[#E5E7EB] flex justify-end">
                <button type="button" onclick="closeModal('overallNoticeModal')" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-[#4B6A34] text-white hover:bg-[#3d562a] transition-colors">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- 7. IMPORT CSV MODAL -->
    <div id="importModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden my-8 animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F8F9FA]">
                <div>
                    <h3 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5 text-primary-500"></i> Import Data VRA dari File CSV
                    </h3>
                    <p class="text-xs text-[#71717A] mt-0.5">
                        Upload file .csv untuk memasukkan data kendaraan dan biaya operasional secara massal.
                    </p>
                </div>
                <button onclick="closeModal('importModal')" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('administrasi.vra.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <input type="hidden" name="period" value="{{ $selectedPeriod }}">

                <!-- Template Download Banner -->
                <div class="bg-primary-50/70 border border-primary-100 rounded-xl p-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
                            <i data-lucide="download" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-primary-950">Belum punya format CSV?</p>
                            <p class="text-[11px] text-[#475569]">Unduh template siap isi dengan contoh data.</p>
                        </div>
                    </div>
                    <a href="{{ route('administrasi.vra.template') }}" 
                       class="px-3 py-1.5 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-semibold rounded-lg shadow-2xs transition-colors shrink-0 flex items-center gap-1.5">
                        <i data-lucide="file-down" class="w-3.5 h-3.5"></i> Download Template
                    </a>
                </div>

                <!-- File Input Area -->
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-2">Pilih File CSV <span class="text-rose-500">*</span></label>
                    <div class="border-2 border-dashed border-[#D1D5DB] rounded-xl p-5 text-center hover:border-primary-400 hover:bg-primary-50/30 transition-colors">
                        <i data-lucide="upload-cloud" class="w-8 h-8 text-[#9CA3AF] mx-auto mb-2"></i>
                        <input type="file" name="csv_file" id="csv_file_input" accept=".csv,text/csv,text/plain" required 
                               class="w-full text-xs text-[#4B5563] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                        <p class="text-[11px] text-[#9CA3AF] mt-2">Mendukung file .csv dengan delimiter koma (,) atau titik-koma (;)</p>
                    </div>
                </div>

                <!-- Import Option Mode -->
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Mode Import</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-start gap-2.5 p-3 rounded-lg border border-[#E5E7EB] bg-[#F8F9FA] cursor-pointer hover:border-primary-300">
                            <input type="radio" name="import_mode" value="append" checked class="mt-0.5 text-primary-600 focus:ring-primary-500">
                            <div>
                                <p class="text-xs font-bold text-[#18181B]">Tambah / Update</p>
                                <p class="text-[10px] text-[#71717A] mt-0.5">Memperbarui jika nomor polisi & periode sama, atau menambah baru.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-2.5 p-3 rounded-lg border border-[#E5E7EB] bg-[#F8F9FA] cursor-pointer hover:border-rose-300">
                            <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div>
                                <p class="text-xs font-bold text-rose-700">Timpa Seluruh Data</p>
                                <p class="text-[10px] text-[#71717A] mt-0.5">Menghapus data lama dan mengganti dengan isi file CSV.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Columns Guide Info -->
                <div class="text-[11px] text-[#64748B] bg-slate-50 border border-slate-200 rounded-lg p-3">
                    <p class="font-bold text-[#334155] mb-1">Kolom yang dikenali secara otomatis:</p>
                    <p class="font-mono text-[10px] text-[#475569] leading-relaxed">
                        nomor_polisi, kendaraan, tahun, bulan, upah_bulan, BBM_bulan, preventif_maintenance_bulan, biaya_lainnya_bulan, KM_awal, KM_akhir, KM_bulan, upah_tahun, BBM_tahun, preventif_maintenance_tahun, biaya_lainnya_tahun, KM_tahun
                    </p>
                </div>

                <!-- Footer Actions -->
                <div class="pt-2 flex items-center justify-end gap-3 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeModal('importModal')" 
                            class="px-4 py-2 rounded-lg text-xs font-semibold text-[#71717A] hover:bg-[#F4F4F5]">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-xs rounded-lg px-5 py-2.5 shadow-sm transition-colors flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4"></i> Mulai Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function handleOverallAddData() {
        openModal('overallNoticeModal');
    }

    function openAddModal() {
        openModal('addModal');
    }

    function openNewYearModal() {
        openModal('newYearModal');
    }

    // Live calculation for Add Data Modal
    function calcAddTotals() {
        const upahBulan = parseFloat(document.getElementById('add_upah_bulan').value) || 0;
        const bbmBulan = parseFloat(document.getElementById('add_bbm_bulan').value) || 0;
        const maintBulan = parseFloat(document.getElementById('add_maint_bulan').value) || 0;
        const lainnyaBulan = parseFloat(document.getElementById('add_lainnya_bulan').value) || 0;
        const totalBulan = upahBulan + bbmBulan + maintBulan + lainnyaBulan;
        document.getElementById('preview_add_total_bulan').innerText = 'IDR ' + totalBulan.toLocaleString('id-ID');

        const upahTahun = parseFloat(document.getElementById('add_upah_tahun').value) || 0;
        const bbmTahun = parseFloat(document.getElementById('add_bbm_tahun').value) || 0;
        const maintTahun = parseFloat(document.getElementById('add_maint_tahun').value) || 0;
        const lainnyaTahun = parseFloat(document.getElementById('add_lainnya_tahun').value) || 0;
        const totalTahun = upahTahun + bbmTahun + maintTahun + lainnyaTahun;
        document.getElementById('preview_add_total_tahun').innerText = 'IDR ' + totalTahun.toLocaleString('id-ID');
    }

    function calcAddKM() {
        const kmAwal = parseFloat(document.getElementById('add_km_awal').value) || 0;
        const kmAkhir = parseFloat(document.getElementById('add_km_akhir').value) || 0;
        if (kmAkhir >= kmAwal && kmAkhir > 0) {
            document.getElementById('add_km_bulan').value = (kmAkhir - kmAwal).toFixed(2);
        }
    }

    // Live calculation for Edit Data Modal
    function calcEditTotals() {
        const upahBulan = parseFloat(document.getElementById('edit_upah_bulan').value) || 0;
        const bbmBulan = parseFloat(document.getElementById('edit_bbm_bulan').value) || 0;
        const maintBulan = parseFloat(document.getElementById('edit_maint_bulan').value) || 0;
        const lainnyaBulan = parseFloat(document.getElementById('edit_lainnya_bulan').value) || 0;
        const totalBulan = upahBulan + bbmBulan + maintBulan + lainnyaBulan;
        document.getElementById('preview_edit_total_bulan').innerText = 'IDR ' + totalBulan.toLocaleString('id-ID');

        const upahTahun = parseFloat(document.getElementById('edit_upah_tahun').value) || 0;
        const bbmTahun = parseFloat(document.getElementById('edit_bbm_tahun').value) || 0;
        const maintTahun = parseFloat(document.getElementById('edit_maint_tahun').value) || 0;
        const lainnyaTahun = parseFloat(document.getElementById('edit_lainnya_tahun').value) || 0;
        const totalTahun = upahTahun + bbmTahun + maintTahun + lainnyaTahun;
        document.getElementById('preview_edit_total_tahun').innerText = 'IDR ' + totalTahun.toLocaleString('id-ID');
    }

    function calcEditKM() {
        const kmAwal = parseFloat(document.getElementById('edit_km_awal').value) || 0;
        const kmAkhir = parseFloat(document.getElementById('edit_km_akhir').value) || 0;
        if (kmAkhir >= kmAwal && kmAkhir > 0) {
            document.getElementById('edit_km_bulan').value = (kmAkhir - kmAwal).toFixed(2);
        }
    }

    // Month Names Map
    const monthNames = {
        1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April',
        5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus',
        9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
    };

    // Open Edit Modal with loaded data
    function openEditModal(id) {
        fetch(`/administrasi/vra/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const item = data.data;
                    document.getElementById('editForm').action = `/administrasi/vra/${id}`;
                    document.getElementById('edit_nomor_polisi').value = item.nomor_polisi;
                    document.getElementById('edit_kendaraan').value = item.kendaraan;
                    document.getElementById('edit_upah_bulan').value = item.upah_bulan;
                    document.getElementById('edit_upah_tahun').value = item.upah_tahun;
                    document.getElementById('edit_bbm_bulan').value = item.BBM_bulan;
                    document.getElementById('edit_bbm_tahun').value = item.BBM_tahun;
                    document.getElementById('edit_maint_bulan').value = item.preventif_maintenance_bulan;
                    document.getElementById('edit_maint_tahun').value = item.preventif_maintenance_tahun;
                    document.getElementById('edit_lainnya_bulan').value = item.biaya_lainnya_bulan;
                    document.getElementById('edit_lainnya_tahun').value = item.biaya_lainnya_tahun;
                    document.getElementById('edit_km_awal').value = item.KM_awal;
                    document.getElementById('edit_km_akhir').value = item.KM_akhir;
                    document.getElementById('edit_km_bulan').value = item.KM_bulan;
                    document.getElementById('edit_km_tahun').value = item.KM_tahun;
                    
                    document.getElementById('edit_periode_info').innerText = `Tahun ${item.tahun} - Bulan ${monthNames[item.bulan] || item.bulan}`;
                    calcEditTotals();
                    openModal('editModal');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat data VRA.');
            });
    }

    // Open Detail Modal
    function showDetailModal(id) {
        fetch(`/administrasi/vra/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const item = data.data;
                    document.getElementById('detail_no_polisi').innerText = item.nomor_polisi;
                    document.getElementById('detail_kendaraan').innerText = item.kendaraan;
                    document.getElementById('detail_periode_badge').innerText = `Tahun ${item.tahun} - Bulan ${item.bulan} (${monthNames[item.bulan] || ''})`;

                    document.getElementById('detail_upah_bulan').innerText = 'IDR ' + Number(item.upah_bulan).toLocaleString('id-ID');
                    document.getElementById('detail_bbm_bulan').innerText = 'IDR ' + Number(item.BBM_bulan).toLocaleString('id-ID');
                    document.getElementById('detail_maint_bulan').innerText = 'IDR ' + Number(item.preventif_maintenance_bulan).toLocaleString('id-ID');
                    document.getElementById('detail_lainnya_bulan').innerText = 'IDR ' + Number(item.biaya_lainnya_bulan).toLocaleString('id-ID');
                    document.getElementById('detail_total_bulan').innerText = 'IDR ' + Number(item.total_biaya_bulan).toLocaleString('id-ID');

                    document.getElementById('detail_upah_tahun').innerText = 'IDR ' + Number(item.upah_tahun).toLocaleString('id-ID');
                    document.getElementById('detail_bbm_tahun').innerText = 'IDR ' + Number(item.BBM_tahun).toLocaleString('id-ID');
                    document.getElementById('detail_maint_tahun').innerText = 'IDR ' + Number(item.preventif_maintenance_tahun).toLocaleString('id-ID');
                    document.getElementById('detail_lainnya_tahun').innerText = 'IDR ' + Number(item.biaya_lainnya_tahun).toLocaleString('id-ID');
                    document.getElementById('detail_total_tahun').innerText = 'IDR ' + Number(item.total_biaya_tahun).toLocaleString('id-ID');

                    document.getElementById('detail_km_awal').innerText = Number(item.KM_awal).toLocaleString('id-ID');
                    document.getElementById('detail_km_akhir').innerText = Number(item.KM_akhir).toLocaleString('id-ID');
                    document.getElementById('detail_km_bulan').innerText = Number(item.KM_bulan).toLocaleString('id-ID');
                    document.getElementById('detail_km_tahun').innerText = Number(item.KM_tahun).toLocaleString('id-ID');

                    openModal('detailModal');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat detail VRA.');
            });
    }

    // Delete Confirmation
    function confirmDelete(id, noPolisi) {
        document.getElementById('deleteForm').action = `/administrasi/vra/${id}`;
        document.getElementById('delete_target_name').innerText = noPolisi;
        openModal('deleteModal');
    }
</script>
@endpush
