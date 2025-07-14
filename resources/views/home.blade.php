<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8fafc;
            font-family: "Inter", sans-serif;
        }

        a.btn {
            margin-top: 2rem;
            padding: 0.75rem 2rem;
            background-color: #04688f;
            color: #ffffff;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s ease-in-out;
        }

        a.btn:hover {
            background-color:rgba(4, 104, 143, 0.41);
        }

        img.logo {
            max-width: 240px;
            height: auto;
        }
    </style>
</head>
<body>
    <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo">

    <a href="{{ url('/admin/login') }}" class="btn">Login</a>
</body>
</html>
