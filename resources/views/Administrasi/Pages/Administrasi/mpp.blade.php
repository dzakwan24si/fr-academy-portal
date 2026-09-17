<x-admin-layout>

<x-slot name="title">MPP Jabatan | FR Academy Enterprise Portal</x-slot>
    <x-slot name="header">MPP Jabatan | FR Academy Enterprise Portal</x-slot>


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
            <!-- Download Template Button -->
            <a href="{{ route('administrasi.mpp.template') }}"
               class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#71717A]"></i> Download Template
            </a>

            @can('edit_cost')
            <!-- Import CSV Button -->
            <button onclick="openModal('importModal')"
                    class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="upload" class="w-4 h-4 text-[#71717A]"></i> Import CSV
            </button>
            @endcan

            <!-- Export Report Button -->
            <a href="{{ route('administrasi.mpp.export', ['year' => $selectedYear, 'month' => $selectedMonth]) }}"
               class="bg-white border border-[#E5E7EB] hover:bg-[#F4F4F5] text-[#18181B] font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-2xs transition-colors">
                <i data-lucide="download" class="w-4 h-4 text-[#71717A]"></i> Export Excel
            </a>

            @can('edit_cost')
            <!-- Add Data Button -->
            <button onclick="openAddModal()"
                    class="bg-[#4B6A34] hover:bg-[#3d562a] text-white font-medium text-sm rounded-lg px-4 py-2 flex items-center gap-2 shadow-sm transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i> + Tambah Data
            </button>
            @endcan
        </div>
    </div>

    <!-- 2. Year & Month Navigation Card -->
    <div class="bg-white border border-[#E5E7EB] rounded-2xl p-4 shadow-2xs space-y-4">
        <!-- Year Tabs with Scroll and Tambah Tahun Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <div class="flex items-center gap-1.5 px-1 text-[#71717A] shrink-0">
                    <i data-lucide="calendar" class="w-4 h-4 text-[#4B6A34]"></i>
                    <span class="text-xs font-bold text-[#18181B] uppercase tracking-wider">Tahun:</span>
                </div>

                {{-- Scroll Left Button --}}
                <button type="button" onclick="scrollYearTabs('left')" class="p-1.5 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] hover:bg-gray-100 text-[#71717A] hover:text-[#18181B] transition-colors shrink-0" title="Scroll ke tahun sebelumnya">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>

                {{-- Horizontal Scrollable List --}}
                <div id="yearTabsContainer" class="flex items-center gap-2 overflow-x-auto py-1 px-1 scroll-smooth" style="scrollbar-width: thin;">
                    @foreach ($years as $year)
                        <a href="{{ route('administrasi.mpp.index', ['year' => $year, 'month' => $selectedMonth, 'search' => $search]) }}"
                           id="year-tab-{{ $year }}"
                           class="px-5 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ $selectedYear == $year ? 'bg-[#4B6A34] text-white shadow-sm' : 'text-[#71717A] hover:text-[#18181B] hover:bg-[#F4F4F5] bg-gray-50 border border-transparent hover:border-[#E5E7EB]' }}">
                            {{ $year }}
                        </a>
                    @endforeach
                </div>

                {{-- Scroll Right Button --}}
                <button type="button" onclick="scrollYearTabs('right')" class="p-1.5 rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] hover:bg-gray-100 text-[#71717A] hover:text-[#18181B] transition-colors shrink-0" title="Scroll ke tahun berikutnya">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Tambah Tahun Button --}}
            @can('edit_cost')
            <div class="shrink-0">
                <button type="button" onclick="openTambahTahunModal()"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#4B6A34] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 flex items-center gap-1.5 transition-colors shadow-2xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>+ Tambah Tahun</span>
                </button>
            </div>
            @endcan
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
                        @can('edit_cost')
                        <th class="py-3.5 px-6 text-center font-semibold w-24">Aksi</th>
                        @endcan
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
                            @can('edit_cost')
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
                            @endcan
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
                                        @can('edit_cost')
                                        <p class="text-xs text-[#71717A] mt-0.5">Klik tombol "+ Tambah Data" untuk mulai memasukkan jumlah karyawan per jabatan.</p>
                                        @endcan
                                    </div>
                                    @can('edit_cost')
                                    <button onclick="openAddModal()" class="mt-2 bg-[#4B6A34] hover:bg-[#3d562a] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                        + Tambah Data MPP
                                    </button>
                                    @endcan
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

    @can('edit_cost')
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
    <!-- 8. MODAL: IMPORT CSV                                     -->
    <!-- ======================================================== -->
    <div id="importModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden my-8 animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="p-6 border-b border-[#E5E7EB] flex items-center justify-between bg-[#F8F9FA]">
                <div>
                    <h3 class="text-base font-bold text-[#18181B] flex items-center gap-2">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5 text-primary-500"></i> Import Data MPP dari File CSV
                    </h3>
                    <p class="text-xs text-[#71717A] mt-0.5">
                        Upload file .csv untuk memasukkan data jumlah karyawan per jabatan secara massal.
                    </p>
                </div>
                <button onclick="closeModal('importModal')" class="text-[#71717A] hover:text-[#18181B]">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('administrasi.mpp.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <input type="hidden" name="month" value="{{ $selectedMonth }}">

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
                    <a href="{{ route('administrasi.mpp.template') }}"
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
                                <p class="text-[10px] text-[#71717A] mt-0.5">Memperbarui data jika nama jabatan & periode sama, atau menambah baru.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-2.5 p-3 rounded-lg border border-[#E5E7EB] bg-[#F8F9FA] cursor-pointer hover:border-rose-300">
                            <input type="radio" name="import_mode" value="replace" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <div>
                                <p class="text-xs font-bold text-rose-700">Timpa Data Bulan Ini</p>
                                <p class="text-[10px] text-[#71717A] mt-0.5">Menghapus data periode {{ $monthInfo['full'] }} {{ $selectedYear }} dan mengganti dengan isi CSV.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Columns Guide Info -->
                <div class="text-[11px] text-[#64748B] bg-slate-50 border border-slate-200 rounded-lg p-3">
                    <p class="font-bold text-[#334155] mb-1">Format kolom CSV yang didukung:</p>
                    <p class="font-mono text-[10px] text-[#475569] leading-relaxed mb-1">
                        <strong>Format Matrix:</strong> nama_jabatan, Staff, PBT, KHT, KHL, [tahun], [bulan]
                    </p>
                    <p class="font-mono text-[10px] text-[#475569] leading-relaxed">
                        <strong>Format Baris:</strong> nama_jabatan, jenis_jabatan, jumlah, [tahun], [bulan]
                    </p>
                    <p class="text-[10px] text-[#71717A] mt-1.5 italic">*Jika kolom tahun/bulan dikosongkan, sistem otomatis memakai tahun {{ $selectedYear }} & bulan {{ $monthInfo['full'] }}.</p>
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
    {{-- ============================== MODAL: TAMBAH TAHUN ============================== --}}
    <div id="modalTambahTahun" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeTambahTahunModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden p-6 z-10 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#4B6A34] flex items-center justify-center font-bold">
                        <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#18181B]">Tambah / Pilih Tahun</h3>
                        <p class="text-[11px] text-[#71717A]">Buka atau tambahkan periode MPP tahun baru</p>
                    </div>
                </div>
                <button type="button" onclick="closeTambahTahunModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="formTambahTahun" onsubmit="handleTambahTahun(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#374151] mb-1">Tahun Anggaran / Periode</label>
                    <input type="number" id="inputTahunBaru" min="2000" max="2099" step="1"
                           value="{{ max($years) + 1 }}" required
                           class="w-full px-3 py-2 border border-[#E5E7EB] rounded-xl text-sm font-bold text-center text-[#18181B] focus:ring-2 focus:ring-[#4B6A34]/20 focus:border-[#4B6A34] outline-none">
                </div>

                <div class="flex items-center justify-center gap-2">
                    <button type="button" onclick="document.getElementById('inputTahunBaru').value = {{ min($years) - 1 }}"
                            class="px-2.5 py-1 text-[11px] font-medium bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-700">
                        {{ min($years) - 1 }}
                    </button>
                    <button type="button" onclick="document.getElementById('inputTahunBaru').value = {{ date('Y') }}"
                            class="px-2.5 py-1 text-[11px] font-medium bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-700">
                        Tahun Ini ({{ date('Y') }})
                    </button>
                    <button type="button" onclick="document.getElementById('inputTahunBaru').value = {{ max($years) + 1 }}"
                            class="px-2.5 py-1 text-[11px] font-medium bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 text-gray-700">
                        {{ max($years) + 1 }}
                    </button>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeTambahTahunModal()"
                            class="flex-1 px-4 py-2 text-xs font-medium text-[#374151] bg-white border border-[#E5E7EB] rounded-xl hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 text-xs font-bold text-white bg-[#4B6A34] rounded-xl hover:bg-[#3d562a] shadow-sm">
                        Buka Tahun
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan

@push('scripts')
<script>
    function scrollYearTabs(direction) {
        const container = document.getElementById('yearTabsContainer');
        if (!container) return;
        const scrollAmount = 240;
        container.scrollBy({ left: direction === 'left' ? -scrollAmount : scrollAmount, behavior: 'smooth' });
    }

    function openTambahTahunModal() {
        const modal = document.getElementById('modalTambahTahun');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => document.getElementById('inputTahunBaru')?.focus(), 100);
        }
    }

    function closeTambahTahunModal() {
        const modal = document.getElementById('modalTambahTahun');
        if (modal) modal.classList.add('hidden');
    }

    function handleTambahTahun(e) {
        e.preventDefault();
        const year = document.getElementById('inputTahunBaru')?.value;
        if (!year) return;
        const url = new URL(window.location.href);
        url.searchParams.set('year', year);
        window.location.href = url.toString();
    }

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

    document.addEventListener('DOMContentLoaded', function () {
        const activeTab = document.getElementById('year-tab-{{ $selectedYear }}');
        if (activeTab) {
            activeTab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    });
</script>
@endpush

</x-admin-layout>
