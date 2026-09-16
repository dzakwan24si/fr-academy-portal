<x-admin-layout>

<x-slot name="title">Database Asisten / Trainee | FR Academy</x-slot>
    <x-slot name="header">Database Asisten / Trainee | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/Asisten.jsx')
    <div id="asisten-root"></div>
</x-admin-layout>
