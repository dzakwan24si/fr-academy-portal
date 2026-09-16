<x-admin-layout>

<x-slot name="title">Data Rekrutmen Karyawan | FR Academy</x-slot>
    <x-slot name="header">Data Rekrutmen Karyawan | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Administrasi/Rekrutmen.jsx')
    <div id="rekrutmen-root"></div>
</x-admin-layout>
