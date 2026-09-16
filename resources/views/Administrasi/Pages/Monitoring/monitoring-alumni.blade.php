<x-admin-layout>

<x-slot name="title">Monitoring Alumni | FR Academy</x-slot>
    <x-slot name="header">Monitoring Alumni | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/MonitoringAlumni.jsx')
    <div id="monitoring-alumni-root"></div>
</x-admin-layout>
