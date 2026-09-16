@extends('Administrasi.Layouts.app')

@section('title', 'Database Asisten / Trainee | FR Academy')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/Asisten.jsx')
    <div id="asisten-root"></div>
@endsection
