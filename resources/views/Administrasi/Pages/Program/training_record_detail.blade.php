<x-admin-layout>
<x-slot name="title">Training Record Detail | FR Academy</x-slot>
    <x-slot name="header">Training Record Detail | FR Academy</x-slot>

<div class="space-y-5"><a href="{{ route('administrasi.training-record.index') }}" class="text-sm text-[#4B6A34]">&larr; Kembali</a><div><h1 class="text-2xl font-bold text-[#18181B]">Training Record Detail</h1><p class="text-sm text-[#71717A]">Activity information bersumber dari Program/Batch yang dipilih.</p></div><div class="grid gap-5 lg:grid-cols-3"><section class="rounded-xl border bg-white p-5"><h2 class="mb-4 font-bold">Participant Information</h2>@foreach(['participant_identifier'=>'NIK','participant_name'=>'Nama','gender'=>'Jenis Kelamin','company'=>'Nama PT','work_location'=>'Lokasi Kerja','group_name'=>'Group','region'=>'REGION','gol'=>'GOL','position'=>'Jabatan','periode'=>'Periode','bulan'=>'Bulan','tahun'=>'Tahun'] as $field => $label)<div class="mb-3"><p class="text-[10px] uppercase text-[#71717A]">{{ $label }}</p><p class="text-sm {{ in_array($field, ['bulan', 'participant_identifier']) ? 'font-semibold' : '' }}">{{ $record->{$field} ?: '-' }}</p></div>@endforeach</section><section class="rounded-xl border bg-white p-5"><h2 class="mb-4 font-bold">Training Activity Information</h2>@foreach(['Nama Pelatihan' => $record->activityName(), 'Batch/Activity ID' => $record->program_id, 'Mulai Program' => $record->activityStartDate(), 'Selesai Program' => $record->activityEndDate(), 'Jumlah Hari' => $activity?->jumlah_hari ?? $activity?->total_hari, 'Jam Pelatihan / Peserta' => $record->activityHoursPerParticipant(), 'Instruktur' => $record->instructor ?: $record->activityTrainer(), 'Tempat Pelatihan' => $record->program_type === 'reguler' ? $activity?->Lokasi_training : $activity?->lokasi_training, 'Reg / Non Reg' => ucfirst(str_replace('_', ' ', $record->program_type))] as $label => $value)<div class="mb-3"><p class="text-[10px] uppercase text-[#71717A]">{{ $label }}</p><p class="text-sm">{{ $value ?? '-' }}</p></div>@endforeach</section><section class="rounded-xl border bg-white p-5"><h2 class="mb-4 font-bold">Training Record Information</h2><form method="POST" action="{{ route('administrasi.training-record.update', $record) }}" class="space-y-3">@csrf @method('PUT')<input type="hidden" name="program_type" value="{{ $record->program_type }}"><input type="hidden" name="program_id" value="{{ $record->program_id }}">@foreach(['participant_identifier'=>'NIK','participant_name'=>'Nama','gender'=>'Jenis Kelamin','company'=>'Nama PT','work_location'=>'Lokasi Kerja','group_name'=>'Group','region'=>'REGION','gol'=>'GOL','position'=>'Jabatan','instructor'=>'Nama Instruktur','internal_external'=>'Internal/Eksternal','level'=>'Level','periode'=>'Periode'] as $field => $label)<div class="space-y-1"><label class="text-[10px] font-bold uppercase text-[#71717A]">{{ $label }}</label><input name="{{ $field }}" value="{{ $record->{$field} }}" placeholder="{{ $label }}" class="w-full rounded-lg border px-3 py-2 text-sm"></div>@endforeach
<div class="space-y-1">
    <label class="text-[10px] font-bold uppercase text-[#71717A]">Bulan</label>
    <select name="bulan" class="w-full rounded-lg border px-3 py-2 text-sm bg-white">
        <option value="">Pilih Bulan</option>
        @foreach(['JAN' => 'Januari (JAN)', 'FEB' => 'Februari (FEB)', 'MAR' => 'Maret (MAR)', 'APR' => 'April (APR)', 'MEI' => 'Mei (MEI)', 'JUN' => 'Juni (JUN)', 'JUL' => 'Juli (JUL)', 'AGU' => 'Agustus (AGU)', 'SEP' => 'September (SEP)', 'OKT' => 'Oktober (OKT)', 'NOV' => 'November (NOV)', 'DES' => 'Desember (DES)'] as $val => $lbl)
            <option value="{{ $val }}" {{ strtoupper((string) $record->bulan) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>
</div>
<div class="space-y-1">
    <label class="text-[10px] font-bold uppercase text-[#71717A]">Tahun</label>
    <input type="number" name="tahun" value="{{ $record->tahun }}" placeholder="Tahun" min="2000" max="2100" class="w-full rounded-lg border px-3 py-2 text-sm">
</div>
<div class="space-y-1">
    <label class="text-[10px] font-bold uppercase text-[#71717A]">Keterangan</label>
    <input name="notes" value="{{ $record->notes }}" placeholder="Keterangan" class="w-full rounded-lg border px-3 py-2 text-sm">
</div>
<button class="w-full rounded-lg bg-[#4B6A34] px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button></form></section></div></div>
</x-admin-layout>
