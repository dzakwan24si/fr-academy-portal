@extends('Administrasi.Layouts.app')

@section('title', 'MPP Jabatan | FR Academy Enterprise Portal')

@section('content')
    <!-- Alert Notifications -->
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
            <h2 class="text-3xl font-bold text-[#18181B] tracking-tight">MPP Jabatan</h2>
            <p class="text-[#71717A] mt-1 text-sm">Monitoring jumlah karyawan berdasarkan jabatan dan jenis jabatan</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Export Report Button -->
            <a href="{{ route('administrasi.mpp.export', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" 
               class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i> Export Report
            </a>

            <!-- Add Data Button -->
            <button onclick="openAddModal()" 
                    class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-sm transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i> + Tambah Data
            </button>
        </div>
    </div>

    <!-- 2. Year & Month Navigation Card -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs space-y-4">
        <!-- Year Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            @foreach ($years as $year)
                <a href="{{ route('administrasi.mpp.index', ['year' => $year, 'month' => $selectedMonth, 'search' => $search]) }}" 
                   class="px-6 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap {{ $selectedYear == $year ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5]' }}">
                    {{ $year }}
                </a>
            @endforeach

            <!-- + Tahun Baru Button -->
            <button onclick="openNewYearModal()" 
                    class="px-4 py-2 rounded-xl text-sm font-medium text-[#71717A] hover:text-[#4B6A34] hover:bg-emerald-50 border border-dashed border-[#D1D5DB] flex items-center gap-1.5 ml-auto transition-colors whitespace-nowrap">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> + Tahun Baru
            </button>
        </div>

        <div class="border-t border-[#F4F4F5]"></div>

        <!-- Month Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto">
            @foreach ($months as $num => $info)
                <a href="{{ route('administrasi.mpp.index', ['year' => $selectedYear, 'month' => $num, 'search' => $search]) }}" 
                   class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all whitespace-nowrap {{ $selectedMonth == $num ? 'bg-[#A3D9A5] text-[#1E3A1E] font-bold shadow-2xs' : 'bg-white border border-[#E5E7EB] text-[#52525B] hover:bg-[#F4F4F5]' }}">
                    {{ $info['short'] }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- 3. Main Data Table Card -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <!-- Card Header Context -->
        <div class="p-5 border-b border-[#E5E7EB] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <h3 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                <span>MPP Jabatan — {{ $monthInfo['full'] }} {{ $selectedYear }}</span>
            </h3>
            
            <div class="flex items-center gap-3">
                <!-- Search bar -->
                <form action="{{ route('administrasi.mpp.index') }}" method="GET" class="relative">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <input type="hidden" name="month" value="{{ $selectedMonth }}">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-[#A1A1AA] absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari Jabatan..." 
                           class="pl-8 pr-3 py-1.5 text-xs bg-[#F9FAFB] border border-[#E5E7EB] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 w-44 md:w-56">
                </form>

                <!-- Context Badge -->
                <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#F4F4F5] text-[#71717A] text-xs font-medium">
                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                    <span>Data entries map to {{ $monthInfo['short'] }} {{ $selectedYear }}</span>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b border-[#E5E7EB] text-xs font-semibold text-[#71717A] bg-white">
                        <th class="py-3.5 px-6 font-semibold">Nama Jabatan</th>
                        <th class="py-3.5 px-6 text-center font-semibold w-32">Staff</th>
                        <th class="py-3.5 px-6 text-center font-semibold w-32">PBT</th>
                        <th class="py-3.5 px-6 text-center font-semibold w-32">KHT</th>
                        <th class="py-3.5 px-6 text-center font-semibold w-32">KHL</th>
                        <th class="py-3.5 px-6 text-center font-semibold w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5] text-sm text-[#18181B]">
                    @forelse ($matrix as $row)
                        <tr class="hover:bg-[#F9FAFB] transition-colors group">
                            <!-- Nama Jabatan -->
                            <td class="py-4 px-6 font-medium text-[#18181B]">
                                {{ $row['nama_jabatan'] }}
                            </td>

                            <!-- Staff -->
                            <td class="py-4 px-6 text-center font-normal {{ $row['Staff'] > 0 ? 'text-[#18181B] font-semibold' : 'text-[#71717A]' }}">
                                {{ $row['Staff'] }}
                            </td>

                            <!-- PBT -->
                            <td class="py-4 px-6 text-center font-normal {{ $row['PBT'] > 0 ? 'text-[#18181B] font-semibold' : 'text-[#71717A]' }}">
                                {{ $row['PBT'] }}
                            </td>

                            <!-- KHT -->
                            <td class="py-4 px-6 text-center font-normal {{ $row['KHT'] > 0 ? 'text-[#18181B] font-semibold' : 'text-[#71717A]' }}">
                                {{ $row['KHT'] }}
                            </td>

                            <!-- KHL -->
                            <td class="py-4 px-6 text-center font-normal {{ $row['KHL'] > 0 ? 'text-[#18181B] font-semibold' : 'text-[#71717A]' }}">
                                {{ $row['KHL'] }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                            onclick="openEditModal('{{ addslashes($row['nama_jabatan']) }}')" 
                                            class="p-1.5 text-[#71717A] hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                            title="Edit Data">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="confirmDelete('{{ addslashes($row['nama_jabatan']) }}')" 
                                            class="p-1.5 text-[#71717A] hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" 
                                            title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#71717A]">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-[#F4F4F5] flex items-center justify-center text-[#A1A1AA]">
                                        <i data-lucide="users" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-[#18181B]">Belum ada data MPP untuk periode {{ $monthInfo['full'] }} {{ $selectedYear }}</p>
                                        <p class="text-xs text-[#71717A] mt-0.5">Klik tombol "+ Tambah Data" untuk mulai memasukkan jumlah karyawan per jabatan.</p>
                                    </div>
                                    <button onclick="openAddModal()" class="mt-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                        + Tambah Data MPP
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- 4. TOTAL ROW (Read-Only Automatically Calculated) -->
                @if(count($matrix) > 0)
                    <tfoot>
                        <tr class="bg-[#EBF3E8] border-t-2 border-[#D1E7DD] font-bold text-sm text-[#18181B]">
                            <td class="py-4 px-6 uppercase tracking-wider text-xs font-extrabold text-[#2F4321]">
                                TOTAL
                            </td>
                            <td class="py-4 px-6 text-center font-extrabold text-base text-[#4B6A34]">
                                {{ $totals['Staff'] }}
                            </td>
                            <td class="py-4 px-6 text-center font-extrabold text-base text-[#4B6A34]">
                                {{ $totals['PBT'] }}
                            </td>
                            <td class="py-4 px-6 text-center font-extrabold text-base text-[#4B6A34]">
                                {{ $totals['KHT'] }}
                            </td>
                            <td class="py-4 px-6 text-center font-extrabold text-base text-[#4B6A34]">
                                {{ $totals['KHL'] }}
                            </td>
                            <td class="py-4 px-6 text-center"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 5. MODAL: TAMBAH DATA MPP                                -->
    <!-- ======================================================== -->
    <div id="addModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
                <div>
                    <h3 class="text-base font-bold text-[#18181B]">Tambah Data MPP Jabatan</h3>
                    <p class="text-xs text-[#71717A] mt-0.5">
                        Periode Aktif: <strong class="text-primary-700">{{ $monthInfo['full'] }} {{ $selectedYear }}</strong>
                    </p>
                </div>
                <button onclick="closeModal('addModal')" class="text-[#71717A] hover:text-[#18181B] p-1.5 rounded-lg hover:bg-[#F4F4F5]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('administrasi.mpp.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <!-- Auto-assigned Year and Month context -->
                <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                <input type="hidden" name="bulan" value="{{ $selectedMonth }}">

                <!-- Context Banner inside Form -->
                <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-xl flex items-center gap-2.5 text-xs text-emerald-900 font-medium">
                    <i data-lucide="calendar" class="w-4 h-4 text-emerald-700 shrink-0"></i>
                    <span>Tahun ({{ $selectedYear }}) dan Bulan ({{ $monthInfo['full'] }}) otomatis ditetapkan.</span>
                </div>

                <!-- Nama Jabatan -->
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">
                        Nama Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_jabatan" 
                           list="jabatan_datalist" 
                           required 
                           placeholder="Contoh: Manager Ops / Asisten Agronomi" 
                           class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-medium">
                    <datalist id="jabatan_datalist">
                        @foreach($existingJabatan as $jbt)
                            <option value="{{ $jbt }}">
                        @endforeach
                    </datalist>
                </div>

                <!-- Input Grid for 4 Types of Jabatan -->
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-2">
                        Jumlah Karyawan per Jenis Jabatan:
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">Staff</label>
                            <input type="number" name="staff" value="0" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">PBT</label>
                            <input type="number" name="pbt" value="0" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">KHT</label>
                            <input type="number" name="kht" value="0" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">KHL</label>
                            <input type="number" name="khl" value="0" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 text-xs font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Data MPP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 6. MODAL: EDIT DATA MPP                                  -->
    <!-- ======================================================== -->
    <div id="editModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F9FAFB]">
                <div>
                    <h3 class="text-base font-bold text-[#18181B]">Edit Data MPP Jabatan</h3>
                    <p class="text-xs text-[#71717A] mt-0.5">
                        Periode: <strong class="text-primary-700">{{ $monthInfo['full'] }} {{ $selectedYear }}</strong>
                    </p>
                </div>
                <button onclick="closeModal('editModal')" class="text-[#71717A] hover:text-[#18181B] p-1.5 rounded-lg hover:bg-[#F4F4F5]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('administrasi.mpp.updateJabatan') }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                <input type="hidden" name="bulan" value="{{ $selectedMonth }}">
                <input type="hidden" name="original_nama_jabatan" id="edit_original_nama_jabatan">

                <!-- Nama Jabatan -->
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">
                        Nama Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_jabatan" 
                           id="edit_nama_jabatan" 
                           required 
                           class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 font-medium">
                </div>

                <!-- Input Grid for 4 Types of Jabatan -->
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-2">
                        Jumlah Karyawan per Jenis Jabatan:
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">Staff</label>
                            <input type="number" name="staff" id="edit_staff" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">PBT</label>
                            <input type="number" name="pbt" id="edit_pbt" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">KHT</label>
                            <input type="number" name="kht" id="edit_kht" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                        <div class="bg-[#F9FAFB] p-2.5 rounded-xl border border-[#E5E7EB] text-center">
                            <label class="block text-xs font-bold text-[#374151] mb-1">KHL</label>
                            <input type="number" name="khl" id="edit_khl" min="0" class="w-full text-center py-1.5 px-2 text-sm bg-white border border-[#E5E7EB] rounded-lg font-semibold focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E5E7EB]">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-xs font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 7. MODAL: DELETE CONFIRMATION                            -->
    <!-- ======================================================== -->
    <div id="deleteModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-200 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="trash-2" class="w-6 h-6"></i>
            </div>
            <h3 class="text-base font-bold text-[#18181B]">Hapus Data MPP?</h3>
            <p class="text-xs text-[#71717A] mt-1">
                Apakah Anda yakin ingin menghapus data MPP untuk jabatan <strong id="delete_jabatan_name" class="text-[#18181B]">-</strong> pada periode {{ $monthInfo['full'] }} {{ $selectedYear }}?
            </p>
            
            <form action="{{ route('administrasi.mpp.destroyJabatan') }}" method="POST" class="mt-5 flex items-center justify-center gap-2">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                <input type="hidden" name="bulan" value="{{ $selectedMonth }}">
                <input type="hidden" name="nama_jabatan" id="delete_nama_jabatan">

                <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-xs font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-lg">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 8. MODAL: TAHUN BARU                                     -->
    <!-- ======================================================== -->
    <div id="newYearModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-200">
            <h3 class="text-base font-bold text-[#18181B]">Pilih / Tambah Tahun Baru</h3>
            <p class="text-xs text-[#71717A] mt-1">Masukkan tahun yang ingin Anda buka atau kelola.</p>
            
            <form action="{{ route('administrasi.mpp.year') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                <div>
                    <label class="block text-xs font-medium text-[#374151] mb-1">Tahun (2000 - 2099)</label>
                    <input type="number" name="new_year" value="{{ $selectedYear + 1 }}" min="2000" max="2099" required class="w-full px-3 py-2 text-sm bg-white border border-[#E5E7EB] rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('newYearModal')" class="px-3 py-2 text-xs font-medium text-[#71717A] hover:bg-[#F4F4F5] rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-[#4B6A34] hover:bg-[#3d562a] text-white rounded-lg">
                        Buka Periode Tahun
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }

    function openAddModal() {
        openModal('addModal');
    }

    function openNewYearModal() {
        openModal('newYearModal');
    }

    function openEditModal(jabatan) {
        const year = '{{ $selectedYear }}';
        const month = '{{ $selectedMonth }}';

        fetch(`/administrasi/mpp/get-by-jabatan?nama_jabatan=${encodeURIComponent(jabatan)}&tahun=${year}&bulan=${month}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const data = res.data;
                    document.getElementById('edit_original_nama_jabatan').value = data.nama_jabatan;
                    document.getElementById('edit_nama_jabatan').value = data.nama_jabatan;
                    document.getElementById('edit_staff').value = data.counts.Staff || 0;
                    document.getElementById('edit_pbt').value = data.counts.PBT || 0;
                    document.getElementById('edit_kht').value = data.counts.KHT || 0;
                    document.getElementById('edit_khl').value = data.counts.KHL || 0;
                    openModal('editModal');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat data MPP untuk diedit.');
            });
    }

    function confirmDelete(jabatan) {
        document.getElementById('delete_jabatan_name').innerText = jabatan;
        document.getElementById('delete_nama_jabatan').value = jabatan;
        openModal('deleteModal');
    }
</script>
@endpush
