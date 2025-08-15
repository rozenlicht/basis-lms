<?php

use App\Http\Controllers\Exports\CompositionReportController;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/login', function () {
    return redirect()->to('/admin');
})->name('login');

Route::get('/exports/composition-report', CompositionReportController::class)->name('composition-report');

Route::get('/qr-code/{containerId}', [QrCodeController::class, 'show'])->name('qr-code.show');