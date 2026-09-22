<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Tugas - {{ $dailyClass->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .page { background: white; width: 210mm; min-height: 297mm; margin: 2rem auto; padding: 15mm; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="text-center mb-4 no-print py-4">
        <button onclick="window.print()" class="px-4 py-2 bg-[#064e3b] text-white rounded shadow font-bold hover:bg-[#043326]">Print PDF</button>
        <a href="{{ route('training-reguler.daily-classes.show', $dailyClass->id) }}" class="px-4 py-2 bg-gray-500 text-white rounded shadow font-bold hover:bg-gray-600 ml-2">Kembali</a>
    </div>

    <div class="page">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-gray-800 pb-4 mb-6">
            <div class="flex items-center gap-4">
                <img src="https://www.first-resources.com/wp-content/uploads/2024/06/fr-logo.png" alt="Logo" class="h-12 w-auto object-contain">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 uppercase">Rekap Tugas Trainee</h1>
                    <p class="text-sm text-gray-600">FR Academy - {{ $dailyClass->trainingProgram->title }}</p>
                </div>
            </div>
            <div class="text-right text-sm">
                <table class="text-left ml-auto">
                    <tr><td class="font-bold pr-3 text-gray-700">Tanggal</td><td>: {{ \Carbon\Carbon::parse($dailyClass->date)->format('d M Y') }}</td></tr>
                    <tr><td class="font-bold pr-3 text-gray-700">Materi</td><td>: {{ $dailyClass->title }}</td></tr>
                    <tr><td class="font-bold pr-3 text-gray-700">Trainer</td><td>: {{ $dailyClass->trainer_name }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-sm border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 text-center w-12">No</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">NIK</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Nama Trainee</th>
                    @foreach($dailyClass->tasks as $task)
                        <th class="border border-gray-300 px-3 py-2 text-left">{{ $task->title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($trainees as $idx => $trainee)
                <tr>
                    <td class="border border-gray-300 px-3 py-3 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-gray-300 px-3 py-3 font-mono">{{ $trainee->nik }}</td>
                    <td class="border border-gray-300 px-3 py-3 font-semibold">{{ $trainee->name }}</td>
                    @foreach($dailyClass->tasks as $task)
                        @php
                            $sub = $submissions->where('task_id', $task->id)->where('trainee_id', $trainee->id)->first();
                        @endphp
                        <td class="border border-gray-300 px-3 py-3 font-medium {{ ($sub && $sub->status == 'sudah') ? 'text-green-600' : 'text-red-500' }}">
                            {{ ($sub && $sub->status == 'sudah') ? 'Dikumpulkan' : 'Belum' }}
                        </td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 3 + $dailyClass->tasks->count() }}" class="border border-gray-300 px-3 py-6 text-center text-gray-500">Tidak ada data trainee aktif untuk kelas ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-12 flex justify-end">
            <div class="text-center w-48">
                <p class="mb-16 text-sm text-gray-600">Mengetahui,</p>
                <p class="font-bold text-gray-900 border-b border-gray-400 pb-1">{{ auth()->check() ? auth()->user()->name : 'Administrator' }}</p>
                <p class="text-xs text-gray-500 mt-1">Admin</p>
            </div>
        </div>
    </div>
</body>
</html>
