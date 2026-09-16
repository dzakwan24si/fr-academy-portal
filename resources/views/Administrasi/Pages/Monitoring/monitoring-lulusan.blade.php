@extends('Administrasi.Layouts.app')

@section('title', 'Monitoring Lulusan Alumni | FR Academy')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/MonitoringLulusan.jsx')
    <div id="monitoring-lulusan-root"></div>
@endsection
