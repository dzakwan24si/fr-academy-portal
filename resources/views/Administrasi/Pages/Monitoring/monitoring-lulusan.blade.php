<x-admin-layout>

<x-slot name="title">Monitoring Lulusan Alumni | FR Academy</x-slot>
    <x-slot name="header">Monitoring Lulusan Alumni | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/MonitoringLulusan.jsx')
    <div id="monitoring-lulusan-root"></div>
</x-admin-layout>
