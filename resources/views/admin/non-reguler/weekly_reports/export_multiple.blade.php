<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Report - {{ $weeklyReport->trainingProgram->name ?? 'Program' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #e5e7eb; }
        .page-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 2rem auto;
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }
        @media print {
            body { background-color: white; margin: 0; }
            .page-a4 { margin: 0; box-shadow: none; width: 100%; min-height: 100vh; page-break-after: always; }
            .no-print { display: none !important; }
        }
        .shape-blue-top-right {
            position: absolute; top: -50px; right: -50px; width: 300px; height: 300px;
            background: #1e3a8a; transform: rotate(45deg); z-index: 1;
        }
        .shape-yellow-top-right {
            position: absolute; top: -20px; right: 80px; width: 150px; height: 150px;
            background: #d97706; transform: rotate(45deg); z-index: 0;
        }
        .bg-pattern {
            background-color: #2e4d9b;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="text-gray-800 antialiased py-8">
    @foreach($reports as $weeklyReport)
        
        <!-- INI ADALAH WRAPPER 1 LAPORAN UTUH (COVER SAMPAI AKHIR) -->
        <!-- DILARANG KERAS MENGUBAH APAPUN DI DALAM BLOK INI -->
        <div class="relative w-full"> 
            @include('admin.non-reguler.weekly_reports._report_body', ['weeklyReport' => $weeklyReport])
        </div>
        
        <!-- PEMISAH HALAMAN (HANYA DITAMBAHKAN JIKA BUKAN LAPORAN TERAKHIR) -->
        @if(!$loop->last)
            <div style="page-break-after: always; clear: both; break-after: page;"></div>
        @endif

    @endforeach
</body>
</html>
