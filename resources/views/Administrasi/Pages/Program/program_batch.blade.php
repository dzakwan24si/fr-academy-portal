<x-admin-layout>

<x-slot name="title">Program | FR Academy</x-slot>
    <x-slot name="header">Program | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Program/ProgramBatch.jsx')
    <div id="program-batch-root"></div>
</x-admin-layout>
