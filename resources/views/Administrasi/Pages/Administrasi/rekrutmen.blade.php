<x-admin-layout>

<x-slot name="title">Data Rekrutmen Karyawan | FR Academy</x-slot>
    <x-slot name="header">Data Rekrutmen Karyawan | FR Academy</x-slot>


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.canEditAdministrasi = @json(auth()->user()?->can('edit_administrasi') ?? false);
        window.canEditCost = @json(auth()->user()?->can('edit_cost') ?? false);
    </script>
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Administrasi/Rekrutmen.jsx')
    <div id="rekrutmen-root"></div>
</x-admin-layout>
