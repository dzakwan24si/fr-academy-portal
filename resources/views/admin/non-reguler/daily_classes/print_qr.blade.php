<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code - {{ $dailyClass->title }}</title>
    <!-- Tailwind via CDN for quick styling in print view -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
            }
        }
        
        body {
            background-color: #f8fafc;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="fixed top-4 right-4 no-print flex gap-2">
        <button onclick="window.close()" class="px-4 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 font-bold shadow-sm transition-colors">Tutup</button>
        <button onclick="window.print()" class="px-4 py-2 bg-[#064e3b] text-white rounded-lg hover:bg-[#043326] font-bold shadow-sm transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Halaman Ini
        </button>
    </div>

    <div class="print-container bg-white w-full max-w-3xl p-12 md:p-16 flex flex-col items-center justify-between border border-slate-200 shadow-2xl rounded-3xl mx-auto text-center relative overflow-hidden" style="min-height: 90vh;">
        
        <!-- Decoration -->
        <div class="absolute top-0 left-0 w-full h-4 bg-[#064e3b]"></div>
        
        <!-- Header -->
        <div class="w-full">
            <h1 class="text-4xl md:text-5xl font-black text-slate-800 uppercase tracking-widest mb-4 leading-tight mt-4">{{ $dailyClass->title }}</h1>
            <p class="text-2xl text-slate-600 font-medium">Program: <span class="font-bold text-slate-800">{{ $dailyClass->trainingProgram->name }}</span></p>
            <p class="text-xl text-slate-500 mt-2 font-medium">{{ \Carbon\Carbon::parse($printDate)->format('l, d F Y') }} • Trainer: {{ $dailyClass->trainer_name }}</p>
        </div>
        
        <!-- QR Section -->
        <div class="w-full mt-10 mb-8">
            <div class="bg-white p-8 rounded-3xl border-[12px] border-slate-100 mx-auto w-fit shadow-sm relative">
                <!-- Helper arrows -->
                <div class="absolute -top-6 -left-6 w-12 h-12 border-t-8 border-l-8 border-[#064e3b] rounded-tl-2xl"></div>
                <div class="absolute -top-6 -right-6 w-12 h-12 border-t-8 border-r-8 border-[#064e3b] rounded-tr-2xl"></div>
                <div class="absolute -bottom-6 -left-6 w-12 h-12 border-b-8 border-l-8 border-[#064e3b] rounded-bl-2xl"></div>
                <div class="absolute -bottom-6 -right-6 w-12 h-12 border-b-8 border-r-8 border-[#064e3b] rounded-br-2xl"></div>
                
                @php
                    $qrUrl = $dailyClass->qr_code_token . '|' . $printDate;
                @endphp
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=600x600&margin=0&data={{ urlencode($qrUrl) }}" alt="QR Code" class="w-[350px] h-[350px] md:w-[450px] md:h-[450px] object-contain">
            </div>
        </div>
        
        <!-- Footer / Manual Code -->
        <div class="w-full pt-4 mt-auto">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Atau Masukkan Kode Token Manual:</p>
            <p class="text-6xl font-mono font-black text-[#064e3b] tracking-[0.3em]">{{ substr($dailyClass->qr_code_token, 0, 8) }}</p>
        </div>
        
    </div>

    <!-- Auto trigger print dialog if requested -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                // window.print();
            }, 1000);
        }
    </script>
</body>
</html>

