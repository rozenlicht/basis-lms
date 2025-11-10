<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BASIS Mobile | Sign in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-primary-600 via-primary-500 to-primary-500 font-sans antialiased text-white">
    <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm rounded-3xl bg-white/95 p-8 text-slate-800 shadow-xl shadow-primary-900/20">
            <div class="mb-6 text-center">
                <p class="text-xs uppercase tracking-[0.32em] text-primary-500">Basis Mobile</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Sign in</h1>
                <p class="mt-2 text-sm text-slate-500">Access research materials and samples on the go.</p>
            </div>

            @if (session('status'))
                <p class="mb-4 rounded-lg bg-primary-50 px-4 py-3 text-sm text-primary-700">
                    {{ session('status') }}
                </p>
            @endif

            <form method="POST" action="{{ route('mobile.login.attempt') }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-slate-600">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100"
                    />
                    @error('email')
                        <p class="text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-slate-600">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-100"
                    />
                    @error('password')
                        <p class="text-xs font-semibold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-500">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary-500 focus:ring-primary-200"/>
                        Remember me
                    </label>
                </div>

                <button type="submit" class="w-full rounded-xl bg-primary-500 py-3 text-sm font-semibold text-white shadow hover:bg-primary-600">
                    Sign in
                </button>
            </form>
        </div>

        <p class="mt-6 text-sm text-white/80">Need desktop features? <a href="{{ route('filament.admin.auth.login') }}" class="font-semibold text-white hover:underline">Go to admin</a></p>
    </div>
</body>
</html>
