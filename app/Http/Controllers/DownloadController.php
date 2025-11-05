<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function attachment($path)
    {
        // Require authentication
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        
        $disk = Storage::disk('local');
        
        // Decode the path (it will be URL encoded)
        $filePath = urldecode($path);
        
        // Security: Ensure the path is within the attachments directory
        // This prevents directory traversal attacks
        if (!str_starts_with($filePath, 'assets/attachments/')) {
            abort(403, 'Invalid file path');
        }
        
        // Check if file exists
        if (!$disk->exists($filePath)) {
            abort(404, 'File not found');
        }
        
        // Return file download
        $fullPath = $disk->path($filePath);
        return response()->download($fullPath);
    }
}

