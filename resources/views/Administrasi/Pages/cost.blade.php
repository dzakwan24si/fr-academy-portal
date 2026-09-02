@extends('Administrasi.Layouts.app')

@section('title', 'Cost Management | FR Academy Enterprise Portal')

@section('content')

{{-- ============================== PAGE HEADER ============================== --}}
<div class="flex items-c:\Users\ASUS\Kalender-Training-FR-Academy\src\layouts c:\Users\ASUS\Kalender-Training-FR-Academy\src\layouts\AdminLayout.jsx c:\Users\ASUS\Kalender-Training-FR-Academy\src\pages c:\Users\ASUS\Kalender-Training-FR-Academy\src\pages\Dashboard.jsx c:\Users\ASUS\Kalender-Training-FR-Academy\src\pages\DataManagement.jsx c:\Users\ASUS\Kalender-Training-FR-Academy\src\pages\Schedule.jsx c:\Users\ASUS\Kalender-Training-FR-Academy\src\pages\TrainingCalendar.jsxstart justify-between mb-5">
    <div>
        <h1 class="text-2xl font-bold text-[#18181B] tracking-tight">Cost Management</h1>
        <p class="text-sm text-[#71717A] mt-0.5">Manage monthly costs and monitor budget versus realization.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('administrasi.cost.export', ['year' => $selectedYear, 'month' => $selectedMonth]) }}"
           class="flex items-center gap-2 px-4 py-2 bg-white border border-[#E5E7EB] text-[#374151] text-sm font-medium rounded-lg hover:bg-[#F9FAFB] transition-colors">
            <i data-lucide="download" class="w-4 h-4"></i> Export Report
        </a>
        <button onclick="document.getElementById('modalAdd').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-[#4B6A34] text-white text-sm font-semibold rounded-lg hover:bg-[#3d5829] transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Cost
        </button>
    </div>
</div>

{{-- ============================== YEAR NAVIGATION ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl p-3 mb-3">
    <div class="flex items-center gap-1.5 overflow-x-auto" id="year-tabs">
        @php
            $showYears = array_filter($years, fn($y) => $y >= date('Y') - 3 && $y <= date('Y') + 2);
            $showYears = array_values($showYears);
        @endphp
        @foreach($showYears as $yr)
        <a href="{{ route('administrasi.cost.index', ['year' => $yr, 'month' => $selectedMonth]) }}"
           class="flex-shrink-0 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all
                  {{ $yr == $selectedYear ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#71717A] hover:bg-[#F4F4F5]' }}">
            {{ $yr }}
        </a>
        @endforeach
        <button onclick="document.getElementById('modalNewYear').classList.remove('hidden')"
                class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-[#4B6A34] border border-dashed border-[#4B6A34] hover:bg-[#F0F5EB] transition-colors ml-1">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Year
        </button>
    </div>
</div>

{{-- ============================== MONTH TAB NAVIGATION ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl p-3 mb-5">
    @php
    $monthNames = [
        1=>'January', 2=>'February', 3=>'March', 4=>'April',
        5=>'May',     6=>'June',     7=>'July',   8=>'August',
        9=>'September',10=>'October',11=>'November',12=>'December'
    ];
    @endphp
    <div class="flex items-center gap-0.5 overflow-x-auto">
        @foreach($monthNames as $num => $label)
        <a href="{{ route('administrasi.cost.index', ['year' => $selectedYear, 'month' => $num]) }}"
           class="flex-shrink-0 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all whitespace-nowrap
                  {{ $num == $selectedMonth
                      ? 'bg-[#4B6A34] text-white shadow-sm'
                      : 'text-[#71717A] hover:bg-[#F4F4F5]' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- ============================== PERIOD INDICATOR ============================== --}}
<div class="flex items-center gap-3 mb-5">
    <div class="flex items-center gap-2 text-sm text-[#374151]">
        <span class="w-2 h-2 bg-[#4B6A34] rounded-full animate-pulse"></span>
        <span class="font-semibold">Current Report: {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</span>
    </div>
</div>

{{-- ============================== SUMMARY CARDS (4 cards) ============================== --}}
<div class="grid grid-cols-4 gap-4 mb-5">

    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#71717A] mb-2">Budget Bulan Ini</p>
        <p class="text-xl font-bold text-[#18181B]">Rp {{ number_format($summary['budgetTotal'], 0, ',', '.') }}</p>
    </div>

    <div class="bg-[#F0F5EB] border-2 border-[#4B6A34] rounded-xl p-5 relative overflow-hidden">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#4B6A34] mb-2">Realisasi S/D Bulan Ini</p>
        <p class="text-xl font-bold text-[#4B6A34]">Rp {{ number_format($summary['ytdRealisasi'], 0, ',', '.') }}</p>
        @if($summary['ytdBudget'] > 0)
        @php $ytdPct = round($summary['ytdRealisasi'] / $summary['ytdBudget'] * 100, 1); @endphp
        <span class="absolute top-3 right-3 bg-[#4B6A34] text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5">
            <i data-lucide="trending-up" class="w-3 h-3"></i> {{ $ytdPct }}%
        </span>
        @endif
    </div>

    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#71717A] mb-2">Budget S/D Bulan Ini</p>
        <p class="text-xl font-bold text-[#18181B]">Rp {{ number_format($summary['ytdBudget'], 0, ',', '.') }}</p>
    </div>

    <div class="bg-white border border-[#E5E7EB] rounded-xl p-5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#71717A] mb-2">Total Budget Tahunan</p>
        <p class="text-xl font-bold text-[#18181B]">Rp {{ number_format($summary['annualBudget'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- ============================== FLASH MESSAGE ============================== --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg" id="flash-msg">
    <i data-lucide="check-circle" class="w-4 h-4"></i>
    {{ session('success') }}
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto"><i data-lucide="x" class="w-4 h-4"></i></button>
</div>
@endif

{{-- ============================== MONTHLY COST REPORT TABLE ============================== --}}
<div class="bg-white border border-[#E5E7EB] rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-[#E5E7EB]">
        <h3 class="font-bold text-base text-[#18181B]">Monthly Cost Report</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[#E5E7EB]">
                    {{-- Row 1: Main headers --}}
                    <th class="px-5 py-3 text-left text-xs font-bold text-[#71717A] uppercase tracking-wider" rowspan="2">Account Name</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-[#71717A] uppercase tracking-wider" rowspan="2">Bulan Ini</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-[#71717A] uppercase tracking-wider border-l border-[#E5E7EB]" colspan="3">S/D Bulan Ini</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-[#71717A] uppercase tracking-wider border-l border-[#E5E7EB]" rowspan="2">Tahunan</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-[#71717A] uppercase tracking-wider" rowspan="2">Action</th>
                </tr>
                <tr class="border-b-2 border-[#E5E7EB]">
                    <th class="px-5 py-2 text-right text-[11px] font-semibold text-[#71717A] border-l border-[#E5E7EB]">Realisasi</th>
                    <th class="px-5 py-2 text-right text-[11px] font-semibold text-[#71717A]">Budget</th>
                    <th class="px-5 py-2 text-center text-[11px] font-semibold text-[#71717A]">%</th>
                </tr>
            </thead>
            <tbody>

                @forelse($grouped as $kategoriName => $items)

                {{-- SECTION HEADER ROW --}}
                <tr class="bg-[#F9FAFB] border-t border-[#E5E7EB]">
                    <td colspan="7" class="px-5 py-2">
                        <span class="text-xs font-bold text-[#374151] uppercase tracking-widest">{{ $kategoriName }}</span>
                    </td>
                </tr>

                {{-- ITEM ROWS --}}
                @php
                    $subtotalBulanIni    = 0;
                    $subtotalYtdReal     = 0;
                    $subtotalYtdBudget   = 0;
                    $subtotalAnnual      = 0;
                @endphp

                @foreach($items as $row)
                @php
                    $subtotalBulanIni  += $row->bulan_ini;
                    $subtotalYtdReal   += $row->ytd_realisasi;
                    $subtotalYtdBudget += $row->ytd_budget;
                    $subtotalAnnual    += $row->annual_budget;
                    $rowPct = $row->ytd_budget > 0 ? ($row->ytd_realisasi / $row->ytd_budget * 100) : 0;
                    $isOver = $rowPct > 100;
                @endphp
                <tr class="border-b border-[#F4F4F5] hover:bg-[#FAFAFA] transition-colors">
                    <td class="px-5 py-3 text-sm text-[#18181B] pl-8">{{ $row->nama_item }}</td>
                    <td class="px-5 py-3 text-right text-sm text-[#52525B]">
                        {{ $row->bulan_ini > 0 ? number_format($row->bulan_ini, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-[#52525B] border-l border-[#F4F4F5]">
                        {{ $row->ytd_realisasi > 0 ? number_format($row->ytd_realisasi, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-[#52525B]">
                        {{ $row->ytd_budget > 0 ? number_format($row->ytd_budget, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-semibold {{ $isOver ? 'text-red-500' : 'text-[#52525B]' }}">
                            {{ $row->ytd_budget > 0 ? number_format($rowPct, 1) . '%' : '-' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-[#52525B] border-l border-[#F4F4F5]">
                        {{ $row->annual_budget > 0 ? number_format($row->annual_budget, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditModal({{ $row->cost_id }})"
                                    class="p-1 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            </button>
                            <button onclick="openDeleteModal({{ $row->cost_id }}, '{{ addslashes($row->nama_item) }}')"
                                    class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach

                {{-- SUBTOTAL ROW --}}
                @php
                    $subPct = $subtotalYtdBudget > 0 ? ($subtotalYtdReal / $subtotalYtdBudget * 100) : 0;
                    $subOver = $subPct > 100;
                @endphp
                <tr class="bg-[#F4F4F5] border-t border-[#E5E7EB]">
                    <td class="px-5 py-2.5 text-xs font-bold text-[#374151] uppercase pl-8">Subtotal {{ $kategoriName }}</td>
                    <td class="px-5 py-2.5 text-right text-xs font-bold text-[#18181B]">{{ number_format($subtotalBulanIni, 0, ',', '.') }}</td>
                    <td class="px-5 py-2.5 text-right text-xs font-bold text-[#18181B] border-l border-[#E5E7EB]">{{ number_format($subtotalYtdReal, 0, ',', '.') }}</td>
                    <td class="px-5 py-2.5 text-right text-xs font-bold text-[#18181B]">{{ number_format($subtotalYtdBudget, 0, ',', '.') }}</td>
                    <td class="px-5 py-2.5 text-center text-xs font-bold {{ $subOver ? 'text-red-500' : 'text-[#18181B]' }}">
                        {{ $subtotalYtdBudget > 0 ? number_format($subPct, 1) . '%' : '-' }}
                    </td>
                    <td class="px-5 py-2.5 text-right text-xs font-bold text-[#18181B] border-l border-[#E5E7EB]">{{ number_format($subtotalAnnual, 0, ',', '.') }}</td>
                    <td></td>
                </tr>

                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-[#A1A1AA]">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                        <p class="text-sm font-medium">Belum ada data Cost untuk periode ini</p>
                        <p class="text-xs mt-1">Klik "+ Tambah Cost" untuk menambahkan data</p>
                    </td>
                </tr>
                @endforelse

            </tbody>

            {{-- GRAND TOTAL FOOTER --}}
            @if(count($grouped) > 0)
            <tfoot>
                <tr class="bg-[#4B6A34]">
                    <td class="px-5 py-3.5 text-sm font-bold text-white uppercase tracking-wide">Total Cost</td>
                    <td class="px-5 py-3.5 text-right text-sm font-bold text-white">{{ number_format($summary['budgetTotal'], 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-right text-sm font-bold text-white border-l border-white/20">{{ number_format($summary['ytdRealisasi'], 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-right text-sm font-bold text-white">{{ number_format($summary['ytdBudget'], 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-center text-sm font-bold text-white">
                        @if($summary['ytdBudget'] > 0)
                            {{ number_format($summary['ytdRealisasi'] / $summary['ytdBudget'] * 100, 1) }}%
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right text-sm font-bold text-white border-l border-white/20">{{ number_format($summary['annualBudget'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ================================================================
     MODALS
================================================================ --}}

{{-- ---- MODAL: ADD COST ---- --}}
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalAdd').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Tambah Data Cost</h2>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form method="POST" action="{{ route('administrasi.cost.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="tahun" value="{{ $selectedYear }}">
            <input type="hidden" name="bulan" value="{{ $selectedMonth }}">

            <div class="bg-[#F0F5EB] border border-[#C6D9B3] rounded-lg px-4 py-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-[#71717A]">Periode</span>
                <span class="text-sm font-bold text-[#4B6A34]">{{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" required
                            class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none bg-white">
                        <option value="">— Pilih —</option>
                        <option value="Karyawan">Karyawan</option>
                        <option value="ATK & Percetakan">ATK &amp; Percetakan</option>
                        <option value="Konsumsi & Jamuan">Konsumsi &amp; Jamuan</option>
                        <option value="Utilitas & Listrik">Utilitas &amp; Listrik</option>
                        <option value="Perjalanan Dinas">Perjalanan Dinas</option>
                        <option value="Pemeliharaan">Pemeliharaan</option>
                        <option value="Komunikasi">Komunikasi</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Sub Kategori</label>
                    <input type="text" name="sub_kategori" placeholder="Opsional"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Item / Account <span class="text-red-500">*</span></label>
                <input type="text" name="nama_item" required placeholder="Contoh: Gaji, Tunjangan, Kertas A4..."
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Keterangan</label>
                <input type="text" name="keterangan" placeholder="Opsional"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Budget Bulan Ini (Rp)</label>
                    <input type="number" name="budget" min="0" step="1" placeholder="0"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Realisasi Bulan Ini (Rp)</label>
                    <input type="number" name="realisasi" min="0" step="1" placeholder="0"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">No. Bukti</label>
                    <input type="text" name="no_bukti" placeholder="BV/2026/07/001"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---- MODAL: EDIT ---- --}}
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalEdit').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Edit Data Cost</h2>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <form id="formEdit" method="POST" action="" class="px-6 py-5 space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" id="edit_kategori" required
                            class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none bg-white">
                        <option value="">— Pilih —</option>
                        <option value="Karyawan">Karyawan</option>
                        <option value="ATK & Percetakan">ATK &amp; Percetakan</option>
                        <option value="Konsumsi & Jamuan">Konsumsi &amp; Jamuan</option>
                        <option value="Utilitas & Listrik">Utilitas &amp; Listrik</option>
                        <option value="Perjalanan Dinas">Perjalanan Dinas</option>
                        <option value="Pemeliharaan">Pemeliharaan</option>
                        <option value="Komunikasi">Komunikasi</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Sub Kategori</label>
                    <input type="text" name="sub_kategori" id="edit_sub_kategori"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Nama Item <span class="text-red-500">*</span></label>
                <input type="text" name="nama_item" id="edit_nama_item" required
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Keterangan</label>
                <input type="text" name="keterangan" id="edit_keterangan"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Budget (Rp)</label>
                    <input type="number" name="budget" id="edit_budget" min="0" step="1"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Realisasi (Rp)</label>
                    <input type="number" name="realisasi" id="edit_realisasi" min="0" step="1"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">No. Bukti</label>
                    <input type="text" name="no_bukti" id="edit_no_bukti"
                           class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none font-mono">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-[#E5E7EB]">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829] shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ---- MODAL: DELETE ---- --}}
<div id="modalDelete" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="px-6 py-6 text-center">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="trash-2" class="w-7 h-7 text-red-500"></i>
            </div>
            <h3 class="text-lg font-bold text-[#18181B] mb-1">Hapus Data Cost</h3>
            <p class="text-sm font-semibold text-[#18181B] mb-5" id="delete_name_label">—</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('modalDelete').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <form id="formDelete" method="POST" action="" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ---- MODAL: TAMBAH TAHUN ---- --}}
<div id="modalNewYear" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('modalNewYear').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xs mx-4">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#E5E7EB]">
            <h2 class="text-base font-bold text-[#18181B]">Add Year</h2>
            <button onclick="document.getElementById('modalNewYear').classList.add('hidden')"
                    class="p-1.5 hover:bg-[#F4F4F5] rounded-lg"><i data-lucide="x" class="w-5 h-5 text-[#71717A]"></i></button>
        </div>
        <div class="px-5 py-4 space-y-4">
            <p class="text-xs text-[#71717A]">Masukkan tahun baru untuk mulai mencatat data Cost.</p>
            <div>
                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Tahun</label>
                <input type="number" id="newYearInput" min="2000" max="2099" placeholder="{{ date('Y') + 1 }}"
                       class="w-full px-3 py-2.5 border border-[#E5E7EB] rounded-lg text-sm focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalNewYear').classList.add('hidden')"
                        class="flex-1 px-4 py-2 text-sm text-[#374151] bg-white border border-[#E5E7EB] rounded-lg hover:bg-[#F9FAFB]">Batal</button>
                <button type="button" onclick="goToNewYear()"
                        class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-[#4B6A34] rounded-lg hover:bg-[#3d5829]">Pilih</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditModal(id) {
    fetch(`/administrasi/cost/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const d = res.data;
            document.getElementById('formEdit').action = `/administrasi/cost/${id}`;
            document.getElementById('edit_kategori').value    = d.kategori     || '';
            document.getElementById('edit_sub_kategori').value= d.sub_kategori || '';
            document.getElementById('edit_nama_item').value   = d.nama_item    || '';
            document.getElementById('edit_keterangan').value  = d.keterangan   || '';
            document.getElementById('edit_budget').value      = d.budget       || 0;
            document.getElementById('edit_realisasi').value   = d.realisasi    || 0;
            document.getElementById('edit_tanggal').value     = d.tanggal      || '';
            document.getElementById('edit_no_bukti').value    = d.no_bukti     || '';
            document.getElementById('modalEdit').classList.remove('hidden');
        });
}

function openDeleteModal(id, name) {
    document.getElementById('delete_name_label').textContent = name;
    document.getElementById('formDelete').action = `/administrasi/cost/${id}`;
    document.getElementById('modalDelete').classList.remove('hidden');
}

function goToNewYear() {
    const yr = document.getElementById('newYearInput').value;
    if (!yr) return;
    window.location.href = `/administrasi/cost?year=${yr}&month={{ $selectedMonth }}`;
}
</script>
@endpush
