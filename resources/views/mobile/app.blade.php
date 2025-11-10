<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BASIS Mobile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css') }}">
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-100 font-sans antialiased">
    <livewire:mobile.app-shell />

    @livewireScripts
</body>
</html>
