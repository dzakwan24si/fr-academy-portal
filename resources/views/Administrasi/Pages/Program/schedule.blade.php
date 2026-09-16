<x-admin-layout>

<x-slot name="title">Schedule Bulanan | FR Academy</x-slot>
    <x-slot name="header">Schedule Bulanan | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Program/SchedulePage.jsx')
    <div id="schedule-root"></div>
</x-admin-layout>
