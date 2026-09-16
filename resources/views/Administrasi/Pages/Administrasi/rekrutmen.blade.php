@extends('Administrasi.Layouts.app')

@section('title', 'Data Rekrutmen Karyawan | FR Academy')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Administrasi/Rekrutmen.jsx')
    <div id="rekrutmen-root"></div>
@endsection
