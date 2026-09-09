<x-admin-layout>
    <x-slot name="title">Daftar Laporan Mingguan</x-slot>
    <x-slot name="header">Daftar Laporan Mingguan</x-slot>
    <x-slot name="headerSub">Kelola dan lihat daftar laporan mingguan dari para trainer.</x-slot>
    
    <div class="mb-6 flex justify-between items-center">
        <div>
            <!-- Breadcrumbs or other info could go here -->
        </div>
        <a href="{{ route('admin.weekly-reports.create') }}" class="px-6 py-2.5 bg-[#509c38] text-white font-bold rounded-xl hover:bg-green-700 transition shadow-sm inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Laporan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-sm uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Training Program</th>
                        <th class="px-6 py-4 font-semibold">Trainer</th>
                        <th class="px-6 py-4 font-semibold">Periode</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 text-gray-700">{{ $report->id }}</td>
                            <td class="px-6 py-4 text-gray-800 font-medium">{{ $report->trainingProgram->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $report->trainer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($report->week_start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($report->week_end_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                    {{ $report->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada laporan mingguan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reports->links() }}
        </div>
    </div>
</x-admin-layout>
