<x-admin-layout>
    <x-slot name="title">Manajemen Akun Pengguna</x-slot>
    <x-slot name="header">Pengguna Sistem</x-slot>
    <x-slot name="headerSub">Kelola semua akun pengguna, detail kontak, hak akses (role), dan riwayat aktivitas login.</x-slot>

    <x-slot name="headerAction">
        <a href="{{ route('admin.users.create') }}" class="px-5 py-2.5 bg-[#509c38] text-white font-semibold rounded-xl hover:bg-[#3d7a2b] transition shadow-sm inline-flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah User
        </a>
    </x-slot>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-[#509c38] p-4 rounded-r-xl shadow-sm mb-6 flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-[#509c38]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm mb-6 flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="relative w-full sm:w-80 md:w-96">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, atau NIK..." class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#509c38] focus:border-transparent transition">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="px-5 py-2.5 bg-[#509c38] text-white text-sm font-semibold rounded-xl hover:bg-[#3d7a2b] transition shadow-sm">
                    Cari
                </button>
                @if($search)
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-200 transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left">
                <thead class="bg-gray-50/75 border-b border-gray-200 text-gray-500 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="py-4 px-6">Pengguna & NIK</th>
                        <th scope="col" class="py-4 px-6">Role Akses</th>
                        <th scope="col" class="py-4 px-6">Status</th>
                        <th scope="col" class="py-4 px-6">Login Terakhir</th>
                        <th scope="col" class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/70 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                @php
                                    $initial = strtoupper(substr($user->name, 0, 1));
                                    $avatarColors = [
                                        'bg-purple-100 text-purple-700',
                                        'bg-amber-100 text-amber-700',
                                        'bg-emerald-100 text-emerald-700',
                                        'bg-blue-100 text-blue-700',
                                        'bg-rose-100 text-rose-700',
                                        'bg-indigo-100 text-indigo-700',
                                        'bg-teal-100 text-teal-700',
                                    ];
                                    $avatarColor = $avatarColors[ord($initial) % count($avatarColors)];
                                @endphp
                                <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $avatarColor }} flex items-center justify-center font-bold text-sm">
                                    {{ $initial }}
                                </div>
                                <div class="ml-3.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-gray-900 leading-tight">{{ $user->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            <span class="px-2 py-0.5 text-[10px] font-semibold text-emerald-800 bg-emerald-100 rounded-full">
                                                Anda
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5">
                                        @if($user->nik && $user->email)
                                            NIK: {{ $user->nik }} <span class="text-gray-300 font-sans mx-1">•</span> <span class="font-sans text-gray-400">{{ $user->email }}</span>
                                        @elseif($user->nik)
                                            NIK: {{ $user->nik }}
                                        @elseif($user->email)
                                            <span class="font-sans text-gray-500">{{ $user->email }}</span>
                                        @else
                                            <span class="font-sans text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <div class="flex flex-wrap gap-1.5 items-center">
                                @forelse($user->roles as $role)
                                    @php
                                        $roleName = $role->name;
                                        $isSuperadmin = $roleName === 'Superadmin';
                                        $roleBadgeClasses = match($roleName) {
                                            'Superadmin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'Top Management', 'Direktur', 'Manager', 'Atasan' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Koordinator Mess' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Kepala TU' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'Trainer', 'Instruktur' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'Administrasi', 'Admin E-Information' => 'bg-teal-50 text-teal-700 border-teal-200',
                                            'Staff' => 'bg-slate-50 text-slate-700 border-slate-200',
                                            default => 'bg-gray-50 text-gray-700 border-gray-200'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 inline-flex items-center gap-1.5 text-xs font-semibold rounded-full border {{ $roleBadgeClasses }}">
                                        @if($isSuperadmin)
                                            <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-current opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        @endif
                                        <span>{{ $roleName }}</span>
                                    </span>
                                @empty
                                    <span class="px-3 py-1 inline-flex items-center text-xs font-medium rounded-full bg-gray-50 text-gray-500 border border-gray-200">
                                        No Role
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm whitespace-nowrap">
                            @if($user->last_login_at)
                                <span class="text-xs font-medium text-gray-700">
                                    {{ $user->last_login_at->translatedFormat('d M Y, H.i') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Belum pernah</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap space-x-1">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors inline-block" title="Edit Akun">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>

                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p class="text-base font-medium text-gray-900">Belum ada data user</p>
                            <p class="text-sm">Tidak ditemukan user yang cocok dengan pencarian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
