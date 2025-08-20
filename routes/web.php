<?php

use App\Http\Controllers\Exports\CompositionReportController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->away('https://www.tue.nl/en/research/research-labs/multiscale-lab');
});


$domain = config('app.domain');

$routeDefinition = function () {
    Route::get('/', function () {
        return redirect()->route('filament.admin.auth.login');
    });
    Route::get('/login', function () {
        return redirect()->route('filament.admin.auth.login');
    })->name('login');
    Route::get('/qr-code/{containerId}', [QrCodeController::class, 'show'])->name('qr-code.show');
};

// DOMAIN ROUTES

if ($domain) {
    Route::domain($domain)->group($routeDefinition);
} else {
    $routeDefinition();
}