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
