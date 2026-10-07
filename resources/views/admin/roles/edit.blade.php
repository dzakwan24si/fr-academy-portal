<x-admin-layout>
    <x-slot:title>
        Edit Role
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-8">
                <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Role
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50">
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Edit Role</h3>
                    <p class="mt-1 text-sm text-gray-500">Ubah nama Role atau perbarui default permissions-nya.</p>
                </div>
                
                <div class="p-6">
                    <form method="post" action="{{ route('admin.roles.update', $role->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Role</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-gray-700" required autofocus>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="border-gray-200">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Default Permissions untuk Role ini</label>
                            <p class="text-xs text-gray-500 mb-4">Setiap user yang ditetapkan sebagai Role ini akan otomatis memiliki hak akses berikut.</p>
                            
                            @php
                                $permDetails = [
                                    'access_administrasi' => ['title' => 'Akses Modul Administrasi', 'desc' => 'Akses ke dashboard, program, monitoring, mpp, cuti, rekrutmen & inventaris.'],
                                    'view_cost'           => ['title' => 'Lihat Modul Cost (Read-Only)', 'desc' => 'Melihat & mengunduh laporan Cost, VRA, CAPEX, & Biaya Rekruit.'],
                                    'edit_cost'           => ['title' => 'Edit Modul Cost (Full Write)', 'desc' => 'Menambah, mengedit, menghapus, & mengimpor data modul Cost.'],
                                    'manage_articles'     => ['title' => 'Manajemen Artikel', 'desc' => 'Mengelola artikel & publikasi berita.'],
                                    'manage_users'        => ['title' => 'Manajemen User', 'desc' => 'Mengelola akun pengguna sistem.'],
                                    'manage_tv_display'   => ['title' => 'Manajemen TV Display', 'desc' => 'Mengelola playlist & layar TV.'],
                                    'manage_events'       => ['title' => 'Manajemen Tema Event', 'desc' => 'Mengelola event & banner di homepage.'],
                                    'manage_roles'        => ['title' => 'Manajemen Role & Akses', 'desc' => 'Mengelola peran & hak akses (permissions).'],
                                    'manage_competency'   => ['title' => 'Kelola Standar Kompetensi', 'desc' => 'Mengelola kamus kompetensi teknis, jabatan, dan butir indikator.'],
                                ];
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @forelse($permissions as $permission)
                                    @php
                                        $info = $permDetails[$permission->name] ?? ['title' => $permission->name, 'desc' => 'Izin sistem.'];
                                    @endphp
                                    <label class="relative flex items-start border border-gray-200 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="focus:ring-[#509c38] h-4 w-4 text-[#509c38] border-gray-300 rounded" 
                                                {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) || (old('permissions') === null && in_array($permission->name, $rolePermissions)) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="font-bold text-gray-900 block">{{ $info['title'] }}</span>
                                            <span class="text-xs text-gray-500 font-mono block mt-0.5">{{ $permission->name }}</span>
                                            <span class="text-xs text-gray-500 mt-1 block">{{ $info['desc'] }}</span>
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500">Belum ada permission yang terdaftar di database.</p>
                                @endforelse
                            </div>
                            @error('permissions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <button type="submit" class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-bold rounded-xl text-white bg-[#509c38] hover:bg-[#3d7a2b] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#509c38] transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</x-admin-layout>
