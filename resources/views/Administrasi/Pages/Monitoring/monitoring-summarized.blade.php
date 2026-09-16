<x-admin-layout>

<x-slot name="title">Monitoring Summarized | FR Academy</x-slot>
    <x-slot name="header">Monitoring Summarized | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/MonitoringSummarized.jsx')
    <div id="monitoring-summarized-root"></div>
</x-admin-layout>
