<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function download(Document $document): StreamedResponse
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to document.');
        }

        // Check if file exists
        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::path($document->file_path);
        $originalFilename = $document->original_filename ?? basename($document->file_path);

        return response()->streamDownload(function () use ($filePath) {
            echo file_get_contents($filePath);
        }, $originalFilename, [
            'Content-Type' => Storage::mimeType($document->file_path),
            'Content-Disposition' => 'attachment; filename="' . $originalFilename . '"',
        ]);
    }

    public function view(Document $document): StreamedResponse
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(403, 'Unauthorized access to document.');
        }

        // Check if file exists
        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::path($document->file_path);
        $mimeType = Storage::mimeType($document->file_path);

        // For images and PDFs, we can display them inline
        $disposition = in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf']) 
            ? 'inline' 
            : 'attachment';

        return response()->streamDownload(function () use ($filePath) {
            echo file_get_contents($filePath);
        }, basename($document->file_path), [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition . '; filename="' . basename($document->file_path) . '"',
        ]);
    }
}
