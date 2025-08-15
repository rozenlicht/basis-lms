<?php

namespace App\Http\Controllers;

use App\Filament\Resources\ContainerResource;
use App\Models\Container;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function show($containerId)
    {
        // Logic to generate and return the QR code for the given container ID
        $container = Container::findOrFail($containerId);
        $qrCode = QrCode::size(100)->generate(
            ContainerResource::getUrl('view-lite', ['record' => $containerId])
        );
        return view('container-label', ['qrCode' => $qrCode, 'container' => $container]);
    }
}
