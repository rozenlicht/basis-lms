<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Exports\CompositionReportController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


$domain = config('app.basis_domain');

$routeDefinition = function () {
    Route::get('/login', function () {
        return redirect()->route('filament.admin.auth.login');
    })->name('login');

    Route::get('/mobile/login', function () {
        if (Auth::check()) {
            return redirect()->route('mobile.app');
        }

        return view('mobile.login');
    })->name('mobile.login');

    Route::post('/mobile/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('mobile.app'));
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('status', 'Invalid credentials.');
    })->name('mobile.login.attempt');

    Route::post('/mobile/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mobile.login');
    })->name('mobile.logout');

    Route::get('/qr-code/{containerId}', [QrCodeController::class, 'show'])->name('qr-code.show');
    Route::get('/download/attachment/{path}', [DownloadController::class, 'attachment'])->where('path', '.*')->name('download.attachment');

    Route::get('/mobile', function () {
        if (! Auth::check()) {
            return redirect()->route('mobile.login');
        }

        return view('mobile.app');
    })->name('mobile.app');

    Route::middleware(['auth'])->group(function () {
        // Additional authenticated mobile routes can go here
    });

    // Redirect all /admin/* requests to /app/*
    Route::get('/admin/{path}', function ($path) {
        return redirect('/app/' . $path, 301);
    })->where('path', '.*');
};

// DOMAIN ROUTES

if ($domain) {
    Route::domain($domain)->get('/', function () {
        return redirect()->route('filament.admin.auth.login');
    });
    Route::domain('www.' . config('app.domain'))->get('/', function () {
        return redirect()->away('https://www.tue.nl/en/research/research-labs/multiscale-lab');
    });
    Route::domain(config('app.domain'))->get('/', function () {
        return redirect()->away('https://www.tue.nl/en/research/research-labs/multiscale-lab');
    });
    Route::domain($domain)->group($routeDefinition);
} else {
    $routeDefinition();
}