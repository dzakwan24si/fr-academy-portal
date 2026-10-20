<x-admin-layout>

<x-slot name="title">Realisasi Batch | FR Academy</x-slot>
    <x-slot name="header">Realisasi Batch | FR Academy</x-slot>


<div class="space-y-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#18181B]">Realisasi Batch</h1>
            <p class="mt-1 text-sm text-[#71717A]">Monitor realisasi batch dan peserta secara otomatis berdasarkan data program & training record.</p>
        </div>
    </div>

    @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    @php
        $monthCodes = [1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU', 9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES'];
        $currentMonthCode = $monthCodes[$month] ?? 'JAN';
    @endphp

    {{-- Year & Month Navigation --}}
    <div class="flex flex-col gap-3 rounded-xl border border-[#E5E7EB] bg-white p-3 shadow-xs">
        <div class="flex items-center gap-2 border-b border-[#F4F4F5] pb-2">
            <span class="text-xs font-bold uppercase tracking-wider text-[#71717A] shrink-0">Tahun:</span>
            <div class="flex items-center gap-1.5 overflow-x-auto">
                @foreach($availableYears as $y)
                    <a href="{{ route('administrasi.realisasi-vs-batch.index', ['year' => $y, 'month' => $currentMonthCode, 'type' => $type, 'region' => $region]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ (int)$year === (int)$y ? 'bg-[#4B6A34] text-white shadow-xs' : 'bg-slate-100 text-[#52525B] hover:bg-slate-200' }}">
                        {{ $y }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="flex items-center gap-1.5 overflow-x-auto">
            <span class="text-xs font-bold uppercase tracking-wider text-[#71717A] shrink-0">Bulan:</span>
            @foreach(range(1, 12) as $number)
                @php($code = $monthCodes[$number])
                <a href="{{ route('administrasi.realisasi-vs-batch.index', ['year' => $year, 'month' => $code, 'type' => $type, 'region' => $region]) }}" class="rounded-lg px-3 py-1.5 text-xs font-bold {{ $month === $number ? 'bg-[#4B6A34] text-white shadow-xs' : 'text-[#52525B] hover:bg-[#F4F4F5]' }}">
                    {{ $code }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="flex gap-2">
        @foreach(['all' => 'Semua', 'reguler' => 'Reguler', 'non_reguler' => 'Non Reguler'] as $value => $label)
            <a href="{{ route('administrasi.realisasi-vs-batch.index', ['year' => $year, 'month' => $currentMonthCode, 'type' => $value, 'region' => $region]) }}" class="rounded-lg border px-4 py-2 text-xs font-semibold {{ $type === $value ? 'border-[#4B6A34] bg-[#4B6A34] text-white' : 'border-[#E5E7EB] bg-white text-[#52525B]' }}">{{ $label }}</a>
        @endforeach
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-2 rounded-xl border border-[#E5E7EB] bg-white p-3 shadow-xs">
        <input type="hidden" name="year" value="{{ $year }}"><input type="hidden" name="month" value="{{ $currentMonthCode }}"><input type="hidden" name="type" value="{{ $type }}">
        <label class="text-xs font-bold uppercase tracking-wider text-[#71717A]" for="region">Region</label>
        <select id="region" name="region" onchange="this.form.submit()" class="rounded-lg border border-[#D4D4D8] px-3 py-2 text-xs bg-white"><option value="">Semua Region</option>@foreach($regions as $item)<option value="{{ $item }}" {{ strtolower($region) === strtolower($item) ? 'selected' : '' }}>{{ $item }}</option>@endforeach</select>
    </form>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
        @foreach([['Planned Batch', $summary['planned_batch']], ['Realized Batch', $summary['realized_batch']], ['Batch Realization', $summary['batch_percent'].'%'], ['Planned Participants', $summary['planned_participants']], ['Realized Participants', $summary['realized_participants']], ['Participant Realization', $summary['participant_percent'].'%']] as $card)
            <div class="rounded-xl border border-[#E5E7EB] bg-white p-4 shadow-xs"><p class="text-[10px] font-bold uppercase tracking-wider text-[#71717A]">{{ $card[0] }}</p><p class="mt-2 text-xl font-bold text-[#18181B]">{{ $card[1] }}</p></div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-[#E5E7EB] bg-white shadow-xs">
        <div class="border-b border-[#E5E7EB] px-5 py-4"><h2 class="text-sm font-bold text-[#18181B]">Daftar Realisasi Program vs Target Batch</h2><p class="text-xs text-[#71717A]">{{ count($rows) }} program akumulatif s/d {{ ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$month] }} (s/d {{ $currentMonthCode }}) {{ $year }}</p></div>
        <div class="overflow-x-auto"><table class="w-full text-xs"><thead class="bg-[#F9FAFB] text-left text-[10px] uppercase tracking-wider text-[#71717A]"><tr><th class="px-4 py-3">Program ID</th><th class="px-4 py-3">Nama Program</th><th class="px-4 py-3">Tipe</th><th class="px-4 py-3">Region</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Realisasi Batch</th><th class="px-4 py-3">% Batch</th><th class="px-4 py-3">Realisasi Peserta</th><th class="px-4 py-3">% Peserta</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead><tbody class="divide-y divide-[#F4F4F5]">
        @forelse($rows as $row)<tr class="hover:bg-[#FAFAFA]"><td class="px-4 py-3 font-semibold text-[#52525B]">#{{ $row->batch_id }}</td><td class="px-4 py-3 font-medium text-[#18181B]">{{ $row->program_name }}</td><td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $row->program_type === 'reguler' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">{{ ucfirst(str_replace('_', ' ', $row->program_type)) }}</span></td><td class="px-4 py-3 text-[#52525B]">{{ $row->region ?: '-' }}</td><td class="px-4 py-3 font-semibold text-[#18181B]">s/d {{ $currentMonthCode }}</td><td class="px-4 py-3 font-medium"><div class="flex items-center gap-1.5"><span>{{ $row->realized_batch }} / {{ $row->planned_batch }}</span></div></td><td class="px-4 py-3 font-semibold {{ $row->batch_percent >= 100 ? 'text-green-600' : 'text-[#18181B]' }}">{{ $row->batch_percent }}%</td><td class="px-4 py-3 font-medium">{{ $row->realized_participants }} / {{ $row->planned_participants }}</td><td class="px-4 py-3 font-semibold {{ $row->participant_percent >= 100 ? 'text-green-600' : 'text-[#18181B]' }}">{{ $row->participant_percent }}%</td><td class="px-4 py-3 text-right">@if($row->program_type === 'non_reguler')<button type="button" onclick="editRealisasiBatch('{{ $row->program_type }}', {{ $row->program_id }}, '{{ addslashes($row->program_name) }}', {{ $row->realized_batch }})" class="inline-flex items-center gap-1 font-semibold text-emerald-700 hover:text-emerald-800 hover:underline bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-md transition-colors cursor-pointer" title="Edit Realisasi Batch"><i data-lucide="pencil" class="w-3 h-3"></i><span>Edit Batch</span></button>@else<span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded" title="Realisasi batch reguler otomatis dihitung dari presensi Training Record"><i data-lucide="sparkles" class="w-3 h-3 text-slate-400"></i><span>Otomatis</span></span>@endif</td></tr>@empty<tr><td colspan="10" class="px-4 py-10 text-center text-sm text-[#71717A]">Belum ada program pada periode ini.</td></tr>@endforelse
        </tbody></table></div>
    </div>
</div>

<script>
function editRealisasiBatch(programType, programId, programName, currentVal) {
    const newVal = prompt(`Ubah Realisasi Batch untuk:\n"${programName}"`, currentVal);
    if (newVal === null) return;
    const numVal = parseInt(newVal);
    if (isNaN(numVal) || numVal < 0) {
        alert('Masukkan angka batch yang valid!');
        return;
    }

    fetch('{{ route("administrasi.realisasi-vs-batch.update-batch") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            program_type: programType,
            program_id: programId,
            realisasi_batch: numVal
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.message) {
            window.location.reload();
        } else {
            alert(data.error || 'Gagal menyimpan realisasi batch.');
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan saat meng-update realisasi batch.');
    });
}
</script>
</x-admin-layout>

