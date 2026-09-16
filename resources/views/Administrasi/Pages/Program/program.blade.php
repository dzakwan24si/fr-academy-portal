@extends('Administrasi.Layouts.app')
@section('title', 'Program | FR Academy')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-[#E5E7EB] rounded-xl p-5 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-[#18181B]">Program Pelatihan</h1>
            <p class="mt-1 text-sm text-[#71717A]">Ringkasan program yang dihitung dari data Program Batch, Realisasi, dan Training Records.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('administrasi.program-batch.index') }}" class="inline-flex items-center gap-1.5 bg-[#4B6A34] hover:bg-[#3d572a] text-white px-3.5 py-2 rounded-lg text-xs font-semibold shadow-xs transition-all">
                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                <span>Kelola Program Batch</span>
            </a>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <form method="GET" action="{{ route('administrasi.program.index') }}" class="flex flex-wrap items-center gap-3 rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-xs">
        {{-- Filter Jenis Program --}}
        <div class="w-full sm:w-auto">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Jenis Program</label>
            <select name="type" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white font-medium text-slate-700 outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] cursor-pointer">
                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Jenis Program</option>
                <option value="reguler" {{ $type === 'reguler' ? 'selected' : '' }}>Reguler</option>
                <option value="non_reguler_soft_skill" {{ $type === 'non_reguler_soft_skill' ? 'selected' : '' }}>Non Reguler - Soft Skill</option>
                <option value="non_reguler_teknikal" {{ $type === 'non_reguler_teknikal' ? 'selected' : '' }}>Non Reguler - Teknikal</option>
                <option value="non_reguler" {{ $type === 'non_reguler' ? 'selected' : '' }}>Semua Non Reguler</option>
            </select>
        </div>

        {{-- Filter Tahun --}}
        <div class="w-full sm:w-auto">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Tahun</label>
            <select name="year" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white font-medium text-slate-700 outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] cursor-pointer">
                <option value="all" {{ $year === null ? 'selected' : '' }}>Semua Tahun</option>
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Region --}}
        <div class="w-full sm:w-auto">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Region</label>
            <select name="region" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white font-medium text-slate-700 outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34] cursor-pointer">
                <option value="">Semua Region</option>
                @foreach($regions as $item)
                    <option value="{{ $item }}" {{ strtolower($region) === strtolower($item) ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>

        {{-- Search Input --}}
        <div class="flex-1 min-w-[240px]">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-[#71717A] mb-1">Pencarian</label>
            <div class="relative">
                <input name="search" value="{{ $search }}" placeholder="Cari nama program, lokasi..." class="w-full rounded-lg border border-[#D4D4D8] px-3 py-2 text-sm bg-white outline-none focus:border-[#4B6A34] focus:ring-1 focus:ring-[#4B6A34]">
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 self-end">
            <button type="submit" class="rounded-lg bg-[#4B6A34] hover:bg-[#3d572a] px-4 py-2 text-sm font-semibold text-white shadow-xs transition-colors cursor-pointer">
                Filter
            </button>
            <a href="{{ route('administrasi.program.index') }}" class="rounded-lg bg-slate-100 hover:bg-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition-colors">
                Reset
            </a>
        </div>
    </form>

    {{-- Metric Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        @php
            $cards = [
                ['Total Programs', $groups->count(), 'program'],
                ['Total Batch', $groups->sum('total_batch'), 'batch'],
                ['Target Peserta', number_format($groups->sum('target_participants'), 0, ',', '.'), 'orang'],
                ['Realisasi Peserta', number_format($groups->sum('realized_participants'), 0, ',', '.'), 'orang'],
                ['% Peserta', ($groups->sum('target_participants') ? round($groups->sum('realized_participants') / $groups->sum('target_participants') * 100, 1) : 0) . '%', 'realisasi']
            ];
        @endphp
        @foreach($cards as $card)
            <div class="rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-xs">
                <p class="text-[10px] font-bold uppercase tracking-wider text-[#71717A]">{{ $card[0] }}</p>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-xl font-extrabold text-[#18181B]">{{ $card[1] }}</span>
                    <span class="text-xs font-semibold text-[#71717A]">{{ $card[2] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Data Table Card --}}
    <div class="overflow-hidden rounded-xl border border-[#E5E7EB] bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm text-left">
                <thead class="bg-[#F9FAFB] text-[10px] uppercase tracking-wider text-[#71717A] border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-4 py-3.5">Nama Program</th>
                        <th class="px-4 py-3.5">Jenis Program</th>
                        <th class="px-4 py-3.5">Region</th>
                        <th class="px-4 py-3.5 text-center">Total Batch</th>
                        <th class="px-4 py-3.5 text-center">Target Peserta</th>
                        <th class="px-4 py-3.5 text-center">Realisasi Batch</th>
                        <th class="px-4 py-3.5 text-center">Realisasi Peserta</th>
                        <th class="px-4 py-3.5 text-center">% Batch</th>
                        <th class="px-4 py-3.5 text-center">% Peserta</th>
                        <th class="px-4 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F4F4F5]">
                    @forelse($groups as $group)
                        <tr class="hover:bg-[#FAFAFA] transition-colors">
                            <td class="px-4 py-3.5 font-semibold text-[#18181B]">
                                {{ $group['name'] }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($group['type'] === 'reguler')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        Reguler
                                    </span>
                                @elseif(($group['sub_type'] ?? '') === 'soft_skill')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        Non Reguler - Soft Skill
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                        Non Reguler - Teknikal
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">{{ $group['region'] ?: '-' }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ $group['total_batch'] }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ number_format($group['target_participants'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ $group['realized_batch'] }}</td>
                            <td class="px-4 py-3.5 text-center font-medium">{{ number_format($group['realized_participants'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="font-bold {{ $group['batch_percent'] >= 100 ? 'text-emerald-700' : 'text-slate-700' }}">
                                    {{ $group['batch_percent'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="font-bold {{ $group['participant_percent'] >= 100 ? 'text-emerald-700' : 'text-slate-700' }}">
                                    {{ $group['participant_percent'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a class="inline-flex items-center gap-1 font-semibold text-[#4B6A34] hover:text-[#3d572a] hover:underline" href="{{ route('administrasi.program.show', ['type' => $group['type'], 'name' => $group['name'], 'year' => $year]) }}">
                                    <span>Detail</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-sm text-[#71717A]">
                                Tidak ada data program yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#E5E7EB] bg-[#F9FAFB] px-4 py-3 text-xs font-medium text-[#71717A] flex justify-between items-center">
            <span>Menampilkan {{ $groups->count() }} Program</span>
            <span>Total Target: {{ number_format($groups->sum('target_participants'), 0, ',', '.') }} Peserta</span>
        </div>
    </div>
</div>
@endsection
