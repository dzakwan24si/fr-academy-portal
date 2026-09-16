@extends('Administrasi.Layouts.app')

@section('title', 'Program_Batch | FR Academy')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/views/Administrasi/Pages/Program/ProgramBatch.jsx')
    <div id="program-batch-root"></div>
@endsection
