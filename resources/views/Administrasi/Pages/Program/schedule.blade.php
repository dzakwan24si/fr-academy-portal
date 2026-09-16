@extends('Administrasi.Layouts.app')

@section('title', 'Schedule Bulanan | FR Academy')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Program/SchedulePage.jsx')
    <div id="schedule-root"></div>
@endsection
