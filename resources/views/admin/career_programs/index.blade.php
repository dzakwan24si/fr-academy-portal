<x-admin-layout>
    <x-slot name="title">Manajemen Karir</x-slot>
    <x-slot name="header">Daftar Program Karir</x-slot>
    <x-slot name="headerSub">Kelola program karir yang ditampilkan di halaman publik.</x-slot>
    <x-slot name="headerAction">
        <a href="{{ route('admin.career_programs.create') }}" class="px-5 py-2.5 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm inline-flex items-center text-sm">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Program Baru
        </a>
    </x-slot>


    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-[#509c38] p-4 m-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-[#509c38]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-sm">
                        <th class="px-6 py-4 font-semibold text-gray-700">Visual Card</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Kode</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Nama Program</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Link GForm</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($programs as $program)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="w-16 h-10 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 shrink-0">
                                    @if($program->image)
                                        <img src="{{ asset('storage/' . $program->image) }}" alt="{{ $program->name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/bg_card_' . strtolower($program->code) . '.jpg') }}" 
                                             onerror="this.src='{{ asset('images/bg_card_fat.jpg') }}'"
                                             alt="{{ $program->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $program->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $program->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($program->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($program->gform_url)
                                    <a href="{{ $program->gform_url }}" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center">
                                        Link Form <i data-lucide="external-link" class="w-3 h-3 ml-1"></i>
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400 italic">Belum diset</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-start space-x-4">
                                    <a href="{{ route('admin.career_programs.edit', $program) }}" class="inline-flex items-center text-gray-600 text-sm font-medium hover:text-teal-600 transition">
                                        <i data-lucide="edit" class="w-4 h-4 mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.career_programs.destroy', $program) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center text-red-600 text-sm font-medium hover:text-red-800 transition">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                                <p>Belum ada program karir.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
