<x-admin-layout>
    <x-slot:title>
        Edit User
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-8">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar User
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/50">
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Edit Data User</h3>
                    <p class="mt-1 text-sm text-gray-500">Perbarui detail, sandi, atau hak akses (role) untuk user ini.</p>
                </div>
                
                <div class="p-6">
                    <form method="post" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-gray-700" required autofocus>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-gray-700" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="nik" class="block text-sm font-medium text-gray-700">NIK (Nomor Induk Karyawan)</label>
                                <input type="text" name="nik" id="nik" value="{{ old('nik', $user->nik) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-gray-700">
                                <p class="mt-1 text-xs text-gray-500">Opsional, namun disarankan untuk karyawan FR.</p>
                                @error('nik')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <div class="bg-gray-50 p-4 rounded-xl space-y-4">
                            <h4 class="text-sm font-bold text-gray-900">Ubah Kata Sandi (Opsional)</h4>
                            <p class="text-xs text-gray-500 mb-4">Biarkan kosong jika tidak ingin mengubah sandi milik user ini.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                                    <input type="password" name="password" id="password" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-gray-700">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#509c38] focus:ring-[#509c38] px-4 py-3 text-gray-700">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Hak Akses / Role</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @forelse($roles as $role)
                                    <label class="relative flex items-start border border-gray-200 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="focus:ring-[#509c38] h-4 w-4 text-[#509c38] border-gray-300 rounded" 
                                                {{ (is_array(old('roles')) && in_array($role->name, old('roles'))) || (old('roles') === null && in_array($role->name, $userRoles)) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">{{ $role->name }}</span>
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500">Belum ada role yang terdaftar di database.</p>
                                @endforelse
                            </div>
                            @error('roles')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="border-gray-200">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Pengecualian / Direct Permissions (Opsional)</label>
                            <p class="text-xs text-gray-500 mb-4">Pilih ini hanya jika Anda ingin memberikan hak akses khusus yang TIDAK ada di dalam Role bawaan akun ini.</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @forelse($permissions as $permission)
                                    <label class="relative flex items-start border border-gray-200 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" 
                                                {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) || (old('permissions') === null && in_array($permission->name, $userPermissions)) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="font-medium text-gray-900">{{ $permission->name }}</span>
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
