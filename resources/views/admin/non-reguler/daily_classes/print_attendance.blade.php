<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Absensi - {{ $dailyClass->title }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 20px; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0 0 5px 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 0; color: #555; }
        .details-table { width: 100%; margin-bottom: 20px; }
        .details-table td { padding: 3px 0; vertical-align: top; }
        .details-table .label { font-weight: bold; width: 130px; }
        
        .attendance-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .attendance-table th, .attendance-table td { border: 1px solid #333; padding: 8px; text-align: left; }
        .attendance-table th { background-color: #f5f5f5; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .attendance-table tr:nth-child(even) { background-color: #fafafa; }
        
        .status-hadir { color: #166534; font-weight: bold; }
        .status-sakit { color: #854d0e; }
        .status-izin { color: #1e40af; }
        .status-alpha { color: #991b1b; }
        .status-belum { color: #64748b; font-style: italic; }

        .signature-section { margin-top: 50px; width: 100%; page-break-inside: avoid; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .signature-line { margin-top: 70px; border-top: 1px solid #333; padding-top: 5px; font-weight: bold; }

        @media print {
            body { padding: 0; }
            @page { margin: 1.5cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h1>Daftar Hadir Peserta Pelatihan</h1>
        <p>FR Academy - Program Non Reguler</p>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Nama Program</td>
            <td>: {{ $dailyClass->trainingProgram->name ?? '-' }}</td>
            <td class="label">Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($printDate)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Materi / Kelas</td>
            <td>: {{ $dailyClass->title }}</td>
            <td class="label">Waktu</td>
            <td>: {{ $dailyClass->start_time ? \Carbon\Carbon::parse($dailyClass->start_time)->format('H:i') : '?' }} - {{ $dailyClass->end_time ? \Carbon\Carbon::parse($dailyClass->end_time)->format('H:i') : 'Selesai' }} WIB</td>
        </tr>
        <tr>
            <td class="label">Instruktur</td>
            <td>: {{ $dailyClass->trainer_name ?? '-' }}</td>
            <td class="label">Lokasi / Ruang</td>
            <td>: {{ $dailyClass->room ?? '-' }} {{ $dailyClass->region ? '('.$dailyClass->region.')' : '' }}</td>
        </tr>
        <tr>
            <td class="label">Provider</td>
            <td>: {{ $dailyClass->provider ?? 'Internal' }}</td>
            <td class="label">Total Peserta</td>
            <td>: {{ $dailyClass->attendances->count() }} Orang</td>
        </tr>
    </table>

    <table class="attendance-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NIK</th>
                <th width="25%">Nama Peserta</th>
                <th width="15%">Talent To</th>
                <th width="15%">Unit / Region</th>
                <th width="15%">Status Kehadiran</th>
                <th width="10%">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyClass->attendances as $index => $attendance)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $attendance->trainee->nik ?? '-' }}</td>
                    <td style="font-weight: bold;">{{ $attendance->trainee->name ?? '-' }}</td>
                    <td>{{ $attendance->trainee->talent_to ?? '-' }}</td>
                    <td>{{ $attendance->trainee->unit ?? '-' }} {{ $attendance->trainee->origin ? '('.$attendance->trainee->origin.')' : '' }}</td>
                    <td style="text-align: center; text-transform: uppercase; font-size: 10px;">
                        @if($attendance->status == 'hadir')
                            <span class="status-hadir">HADIR</span>
                        @elseif($attendance->status == 'sakit')
                            <span class="status-sakit">SAKIT</span>
                        @elseif($attendance->status == 'izin')
                            <span class="status-izin">IZIN</span>
                        @elseif($attendance->status == 'alpha')
                            <span class="status-alpha">ALPHA</span>
                        @else
                            <span class="status-belum">BELUM ABSEN</span>
                        @endif
                    </td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; font-style: italic; color: #666;">
                        Belum ada data peserta (peserta belum di-import atau belum dimasukkan ke kelas ini).
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p style="margin-top: 5px;">Instruktur / Trainer</p>
            
            <div class="signature-line">
                {{ $dailyClass->trainer_name && $dailyClass->trainer_name != '-' ? $dailyClass->trainer_name : '( ................................... )' }}
            </div>
        </div>
    </div>
    
    <script>
        // Auto print dialog after short delay when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
