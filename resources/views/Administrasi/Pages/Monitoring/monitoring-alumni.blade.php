@extends('Administrasi.Layouts.app')

@section('title', 'Monitoring Alumni | FR Academy')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/MonitoringAlumni.jsx')
    <div id="monitoring-alumni-root"></div>
@endsection
