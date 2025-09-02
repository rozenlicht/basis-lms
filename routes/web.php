<?php

use App\Http\Controllers\Exports\CompositionReportController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;


$domain = config('app.basis_domain');

$routeDefinition = function () {
    Route::get('/login', function () {
        return redirect()->route('filament.admin.auth.login');
    })->name('login');
    Route::get('/qr-code/{containerId}', [QrCodeController::class, 'show'])->name('qr-code.show');
    
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