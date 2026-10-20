<x-admin-layout>

<x-slot name="title">Kalender | FR Academy</x-slot>
    <x-slot name="header">Kalender | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Program/SchedulePage.jsx')
    <div id="schedule-root"></div>
</x-admin-layout>
