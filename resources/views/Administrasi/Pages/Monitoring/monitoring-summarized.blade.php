@extends('Administrasi.Layouts.app')

@section('title', 'Monitoring Summarized | FR Academy')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Monitoring/MonitoringSummarized.jsx')
    <div id="monitoring-summarized-root"></div>
@endsection
