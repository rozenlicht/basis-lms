<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'file_path',
        'original_filename',
        'documentable_type',
        'documentable_id',
    ];

    protected $casts = [
        'type' => DocumentType::class,
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', $this);
    }

    public function getPreviewUrlAttribute(): string
    {
        // if image (based on file_path), return view url, otherwise, return images/placeholder-doc.jpg
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        if (
            in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])
        ) {
            return $this->view_url;
        }

        return asset('images/placeholder-doc.jpg');
    }

    public function getViewUrlAttribute(): string
    {
        return route('documents.view', $this);
    }

    public function getFileSizeAttribute(): string
    {
        if (!\Storage::exists($this->file_path)) {
            return 'Unknown';
        }

        $bytes = \Storage::size($this->file_path);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
