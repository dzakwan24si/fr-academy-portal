<x-admin-layout>
    <x-slot:title>
        Tambah Role Baru
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            
            <div class="mb-6">
                <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Role
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50">
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Tambah Role Baru</h3>
                    <p class="mt-1 text-sm text-gray-500">Tentukan nama Role baru beserta default permissions (Read & Edit) untuk setiap modul.</p>
                </div>
                
                <div class="p-6">
                    <form method="post" action="{{ route('admin.roles.store') }}" class="space-y-8">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700">Nama Role</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1.5 block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-gray-700" required autofocus placeholder="Contoh: Admin Operasional / Staff Keuangan">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="border-gray-200">

                        <!-- SECTION 1: Permissions Halaman Utama (Read & Edit Matrix) -->
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">Hak Akses Modul Operasional (Read & Edit)</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Tentukan izin melihat (Read) dan mengubah/mengelola (Edit) untuk masing-masing halaman utama.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="toggleAllOperational(true, false)" class="text-xs font-semibold px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                        Semua Read-Only
                                    </button>
                                    <button type="button" onclick="toggleAllOperational(true, true)" class="text-xs font-semibold px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition">
                                        Semua Full Access
                                    </button>
                                </div>
                            </div>

                            @php
                                $operationalModules = [
                                    [
                                        'key' => 'dashboard',
                                        'name' => 'Dashboard',
                                        'desc' => 'Dashboard Utama, Dashboard MR, & Monitoring Tahunan.',
                                        'icon' => 'layout-grid',
                                        'read' => 'view_dashboard',
                                        'edit' => 'edit_dashboard',
                                    ],
                                    [
                                        'key' => 'program',
                                        'name' => 'Program',
                                        'desc' => 'Program, Program / Batch, Schedule, Realisasi vs Target, Training Record.',
                                        'icon' => 'layers-3',
                                        'read' => 'view_program',
                                        'edit' => 'edit_program',
                                    ],
                                    [
                                        'key' => 'monitoring',
                                        'name' => 'Monitoring',
                                        'desc' => 'Monitoring Summarized, Monitoring Alumni, Database Asisten, Monitoring Detailed.',
                                        'icon' => 'line-chart',
                                        'read' => 'view_monitoring',
                                        'edit' => 'edit_monitoring',
                                    ],
                                    [
                                        'key' => 'talent',
                                        'name' => 'Talent',
                                        'desc' => 'Monitoring Talent & Talent Pool.',
                                        'icon' => 'sparkles',
                                        'read' => 'view_talent',
                                        'edit' => 'edit_talent',
                                    ],
                                    [
                                        'key' => 'administrasi',
                                        'name' => 'Administrasi (Umum)',
                                        'desc' => 'Rekrutmen, Cuti, Man Power Planning (MPP), & Profil Trainee.',
                                        'icon' => 'file-text',
                                        'read' => 'view_administrasi',
                                        'edit' => 'edit_administrasi',
                                        'legacy' => 'access_administrasi',
                                    ],
                                    [
                                        'key' => 'cost',
                                        'name' => '↳ Sub-Modul Cost & Finansial',
                                        'desc' => 'Cost Operasional, VRA, CAPEX, & Biaya Rekruit. (Khusus Bagian Keuangan)',
                                        'icon' => 'banknote',
                                        'read' => 'view_cost',
                                        'edit' => 'edit_cost',
                                        'is_nested' => true,
                                    ],
                                    [
                                        'key' => 'data_aset',
                                        'name' => 'Data Aset (Inventaris)',
                                        'desc' => 'Dashboard Inventaris, Gudang & Kantor, Stock Opname, Pembelian Barang.',
                                        'icon' => 'boxes',
                                        'read' => 'view_data_aset',
                                        'edit' => 'edit_data_aset',
                                    ],
                                ];
                            @endphp

                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50 text-gray-700 font-bold text-xs uppercase tracking-wider">
                                        <tr>
                                            <th class="px-5 py-3.5 text-left">Nama Modul / Halaman</th>
                                            <th class="px-4 py-3.5 text-center w-36">
                                                <div class="flex items-center justify-center gap-1.5 text-blue-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Read (Lihat)</span>
                                                </div>
                                            </th>
                                            <th class="px-4 py-3.5 text-center w-36">
                                                <div class="flex items-center justify-center gap-1.5 text-emerald-700">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit (Kelola)</span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($operationalModules as $mod)
                                            @php
                                                $isNested = $mod['is_nested'] ?? false;
                                                $readActive = is_array(old('permissions')) && in_array($mod['read'], old('permissions'));
                                                $editActive = is_array(old('permissions')) && in_array($mod['edit'], old('permissions'));
                                            @endphp
                                            <tr class="{{ $isNested ? 'bg-amber-50/50' : 'hover:bg-gray-50/70' }} transition-colors">
                                                <td class="px-5 py-3.5 {{ $isNested ? 'pl-9' : '' }}">
                                                    <div class="flex items-start gap-2.5">
                                                        @if($isNested)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 mt-0.5">
                                                                Sub-Administrasi
                                                            </span>
                                                        @endif
                                                        <div>
                                                            <span class="font-bold text-gray-900 {{ $isNested ? 'text-amber-950 font-semibold' : '' }}">
                                                                {{ $mod['name'] }}
                                                            </span>
                                                            <p class="text-xs text-gray-500 mt-0.5 {{ $isNested ? 'text-amber-700/80 font-medium' : '' }}">
                                                                {{ $mod['desc'] }}
                                                            </p>
                                                            @if($isNested)
                                                                <p class="text-[11px] text-amber-600 mt-1 italic">
                                                                    💡 Catatan: Jika tidak dicentang, menu Cost tidak akan ditampilkan & tidak dapat diedit meski role memiliki akses Edit Administrasi.
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3.5 text-center align-middle">
                                                    <label class="inline-flex items-center justify-center p-2 rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                                        <input type="checkbox" name="permissions[]" value="{{ $mod['read'] }}" 
                                                            id="perm_{{ $mod['read'] }}"
                                                            data-group="operational"
                                                            data-type="read"
                                                            data-pair="{{ $mod['edit'] }}"
                                                            onchange="syncReadEdit(this, '{{ $mod['edit'] }}', 'read')"
                                                            class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer"
                                                            {{ $readActive ? 'checked' : '' }}>
                                                    </label>
                                                </td>
                                                <td class="px-4 py-3.5 text-center align-middle">
                                                    <label class="inline-flex items-center justify-center p-2 rounded-lg cursor-pointer hover:bg-emerald-50 transition">
                                                        <input type="checkbox" name="permissions[]" value="{{ $mod['edit'] }}" 
                                                            id="perm_{{ $mod['edit'] }}"
                                                            data-group="operational"
                                                            data-type="edit"
                                                            data-pair="{{ $mod['read'] }}"
                                                            onchange="syncReadEdit(this, '{{ $mod['read'] }}', 'edit')"
                                                            class="h-5 w-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                                            {{ $editActive ? 'checked' : '' }}>
                                                    </label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SECTION 2: Hak Akses Khusus & Sistem -->
                        <div class="pt-4">
                            <h4 class="text-base font-bold text-gray-900 mb-1">Pengaturan Sistem & Fitur Tambahan</h4>
                            <p class="text-xs text-gray-500 mb-4">Fitur khusus pengelolaan konten, pengguna, dan konfigurasi master data sistem.</p>

                            @php
                                $systemPermissions = [
                                    'manage_articles'          => ['title' => 'Manajemen Artikel', 'desc' => 'Mengelola artikel, berita, dan publikasi portal.'],
                                    'manage_users'             => ['title' => 'Manajemen User', 'desc' => 'Mengelola akun, kredensial, dan data pengguna portal.'],
                                    'manage_roles'             => ['title' => 'Manajemen Role & Akses', 'desc' => 'Mengelola hak akses dan penugasan peran sistem.'],
                                    'manage_tv_display'        => ['title' => 'Manajemen TV Display', 'desc' => 'Mengelola playlist, layout, dan tayangan TV Informasi.'],
                                    'manage_events'            => ['title' => 'Manajemen Tema Event', 'desc' => 'Mengelola tema, banner event, dan pengumuman homepage.'],
                                    'manage_competency'        => ['title' => 'Kelola Standar Kompetensi', 'desc' => 'Mengelola kamus kompetensi teknis, jabatan, dan indikator.'],
                                    'access_learning_activity' => ['title' => 'Akses Modul Learning Activity', 'desc' => 'Akses ke dashboard & materi pelatihan modul Non Reguler.'],
                                    'manage_career'            => ['title' => 'Manajemen Karir', 'desc' => 'Mengelola lowongan kerja, persyaratan, dan link karir.'],
                                ];
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                @foreach($systemPermissions as $permKey => $info)
                                    @php
                                        $checked = is_array(old('permissions')) && in_array($permKey, old('permissions'));
                                    @endphp
                                    <label class="relative flex items-start border border-gray-200 rounded-xl p-3.5 cursor-pointer hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permKey }}" 
                                                class="focus:ring-[#509c38] h-4 w-4 text-[#509c38] border-gray-300 rounded cursor-pointer"
                                                {{ $checked ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="font-bold text-gray-900 block text-xs">{{ $info['title'] }}</span>
                                            <span class="text-[11px] text-gray-500 font-mono block mt-0.5">{{ $permKey }}</span>
                                            <span class="text-xs text-gray-500 mt-1 block">{{ $info['desc'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-bold rounded-xl text-white bg-[#509c38] hover:bg-[#3d7a2b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#509c38] transition-colors">
                                Simpan Role Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Script UX: Otomatisasi Checkbox Read & Edit -->
    <script>
        function syncReadEdit(checkbox, pairId, type) {
            const pairCheckbox = document.getElementById('perm_' + pairId);
            if (!pairCheckbox) return;

            if (type === 'edit' && checkbox.checked) {
                // Jika Edit dicentang, Read otomatis dicentang
                pairCheckbox.checked = true;
            } else if (type === 'read' && !checkbox.checked) {
                // Jika Read dimatikan, Edit otomatis dimatikan
                pairCheckbox.checked = false;
            }
        }

        function toggleAllOperational(readVal, editVal) {
            document.querySelectorAll('input[data-group="operational"][data-type="read"]').forEach(cb => {
                cb.checked = readVal;
            });
            document.querySelectorAll('input[data-group="operational"][data-type="edit"]').forEach(cb => {
                cb.checked = editVal;
            });
        }
    </script>
</x-admin-layout>
