<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FR Academy - Employee Monitoring</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('Logo First Resource.png') }}">
    @viteReactRefresh
    @vite(['resources/views/Administrasi/Pages/Monitoring_Spesifik/main.jsx', 'resources/css/app.css'])
</head>
<body class="bg-[#F8F9FA] antialiased font-sans">
    <div id="root"></div>
</body>
</html>
