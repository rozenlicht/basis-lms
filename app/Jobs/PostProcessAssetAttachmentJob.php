<?php

namespace App\Jobs;

use App\Models\Asset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class PostProcessAssetAttachmentJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Asset $asset, public string $filePath, public bool $isMainPath = false)
    {
        //
    }

    /**
     * Get the storage disk for Filament files
     */
    private function getStorageDisk()
    {
        $disk = config('filament.default_filesystem_disk', config('filesystems.default'));
        return Storage::disk($disk);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $path = $this->filePath;
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        // Process HDR files
        if ($extension === 'hdr') {
            $this->processHdrFile($path);
        }
        
        // Generate thumbnail for image files (including TIF)
        if ($this->isImageFile($extension)) {
            $this->generateThumbnail($path, $this->isMainPath);
        }
    }

    /**
     * Check if the file extension is an image format we can process
     */
    private function isImageFile(string $extension): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tif', 'tiff'];
        return in_array($extension, $imageExtensions);
    }

    /**
     * Generate thumbnail for an image file
     */
    private function generateThumbnail(string $filePath, bool $isMainPath): void
    {
        try {
            $disk = $this->getStorageDisk();
            
            // Determine which driver to use (Imagick is better for TIF files)
            $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
            $manager = new ImageManager($driver);
            
            // Read the image from storage
            $imageContent = $disk->get($filePath);
            $image = $manager->read($imageContent);
            
            // Generate thumbnail (max 300x300, maintaining aspect ratio)
            $image->cover(300, 300);
            
            // Create thumbnail path - always save in assets/thumbnails
            $filename = pathinfo($filePath, PATHINFO_FILENAME);
            $thumbnailPath = 'assets/thumbnails/' . $filename . '_thumb.jpg';
            
            // Ensure thumbnail directory exists
            $thumbnailDir = 'assets/thumbnails';
            if (!$disk->exists($thumbnailDir)) {
                $disk->makeDirectory($thumbnailDir);
            }
            
            // Save thumbnail as JPEG
            $thumbnailContent = (string) $image->toJpeg(85);
            $disk->put($thumbnailPath, $thumbnailContent);
            
            // Update asset with thumbnail path
            if ($isMainPath) {
                $this->asset->path_thumbnail = $thumbnailPath;
                $this->asset->save();
            }
            // For attachments, we could store thumbnails in the attachments metadata if needed
            
        } catch (\Exception $e) {
            // Log error but don't fail the job
            Log::warning('Failed to generate thumbnail for ' . $filePath . ': ' . $e->getMessage());
        }
    }

    /**
     * Process HDR file and extract metadata
     */
    private function processHdrFile(string $path): void
    {
        $disk = $this->getStorageDisk();
        $content = $disk->get($path);
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            // if contains '=' then split on '=' and add to tags
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                $key = strtolower($parts[0]);
                $value = $parts[1];

                // if key is "Detector", also add it as "type"

                if ($key === 'detector') {
                    $this->asset->tags = array_merge($this->asset->tags ?? [], ['type' => $value]);
                }

                $this->asset->tags = array_merge($this->asset->tags ?? [], [$key => $value]);
            }
        }

        // If asset tag has 'device' == 'MIRA3 GMU', calculate fov_width based on pixelsizex * image width
        if ($this->asset->tags['device'] === 'MIRA3 GMU') {
            // get image width and height from the image
            $content = $disk->get($this->asset->path);
            $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
            $manager = new ImageManager($driver);
            $image = $manager->read($content);
            $imageWidth = $image->width();
            $imageHeight = $image->height();

            // Divide the width by the # of detectors
            $detectorCount = count(explode(', ', $this->asset->tags['detector'] ?? ''));
            $imageWidth = $imageWidth / $detectorCount;

            $pixelSizeX = $this->asset->tags['pixelsizex'];
            $pixelSizeY = $this->asset->tags['pixelsizey'];
            $fovWidth = $pixelSizeX * $imageWidth;
            $fovHeight = $pixelSizeY * $imageHeight;
            $this->asset->tags = array_merge($this->asset->tags ?? [], ['fov_width' => $fovWidth, 'fov_height' => $fovHeight]);
        }

        $this->asset->save();
    }
}
