<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kelulusan</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        /* White box to hide the dummy name 'Kimberly Nguyen' */
        .name-cover {
            position: absolute;
            top: 300px;
            left: 15%;
            width: 70%;
            height: 100px;
            background-color: white;
            z-index: 1;
        }
        /* Our dynamic name */
        .name-text {
            position: absolute;
            top: 310px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 55px;
            font-family: 'Georgia', serif;
            font-style: italic;
            font-weight: bold;
            color: #f59e0b; /* Orange/Yellow matching the design */
            z-index: 2;
        }

        /* White box to hide the dummy description */
        .desc-cover {
            position: absolute;
            top: 500px;
            left: 10%;
            width: 80%;
            height: 70px;
            background-color: white;
            z-index: 1;
        }
        /* Our dynamic description */
        .desc-text {
            position: absolute;
            top: 510px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 18px;
            color: #1e293b;
            z-index: 2;
            line-height: 1.5;
        }

        /* White box to hide the dummy date */
        .date-cover {
            position: absolute;
            top: 620px;
            left: 30%;
            width: 40%;
            height: 30px;
            background-color: white;
            z-index: 1;
        }
        /* Our dynamic date */
        .date-text {
            position: absolute;
            top: 625px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #0f4a3f; /* Dark green */
            z-index: 2;
        }
    </style>
</head>
<body>
    <?php
        $path = public_path('images/dummy_cert.png');
        $base64 = '';
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    ?>
    <img src="{{ $base64 }}" class="bg-image" />

    <div class="certificate-container">
        <!-- Cover and replace Name -->
        <div class="name-cover"></div>
        <div class="name-text">{{ $traineeName }}</div>
        
        <!-- Cover and replace Description -->
        <div class="desc-cover"></div>
        <div class="desc-text">
            {{ $certDescription ?? 'Atas keberhasilannya menyelesaikan pelatihan dengan sangat baik pada kelas:' }}<br>
            <strong>"{{ $className }}"</strong>
        </div>

        <!-- Cover and replace Date and Signature -->
        <div class="date-cover"></div>
        <div class="date-text">
            Diterbitkan pada: {{ $certDate }}<br>
            <span style="font-size: 14px; color: #475569;">{{ $certSignerRole ?? 'Ketua Penyelenggara' }}</span><br><br><br>
            <strong style="text-decoration: underline;">{{ $certSignerName ?? 'Darcy' }}</strong>
        </div>
    </div>
</body>
</html>
