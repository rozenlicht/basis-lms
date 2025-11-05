<?php

namespace App\Models;

use App\Jobs\PostProcessAssetAttachmentJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'subject_type',
        'name',
        'path',
        'path_thumbnail',
        'mime_type',
        'size_kb',
        'tags',
        'attachments',
    ];
    protected $casts = [
        'tags' => 'array',
        'attachments' => 'array',
    ];

    // After creation, check for attachments and fire PostProcessAssetAttachmentJob for each attachment.
    protected static function booted()
    {
        static::created(function ($asset) {
            // Process main path file for thumbnail generation
            if ($asset->path) {
                PostProcessAssetAttachmentJob::dispatch($asset, $asset->path, true);
            }
            
            // Process attachments
            if ($asset->attachments) {
                foreach ($asset->attachments as $attachment) {
                    PostProcessAssetAttachmentJob::dispatch($asset, $attachment, false);
                }
            }
        });

        // if an attachment is added, fire PostProcessAssetAttachmentJob for the new attachment.
        static::updated(function ($asset) {
            // Process main path file if it changed
            if ($asset->isDirty('path') && $asset->path) {
                PostProcessAssetAttachmentJob::dispatch($asset, $asset->path, true);
            }
            
            // Process new attachments if they were added
            if ($asset->isDirty('attachments') && $asset->attachments) {
                foreach ($asset->attachments as $attachment) {
                    PostProcessAssetAttachmentJob::dispatch($asset, $attachment, false);
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function tableTags(): Attribute
    {
        // detector, fov_width
        $arr = [];
        if ($this->detector) {
            $arr[] = $this->detector;
        }
        if ($this->fov_width) {
            $arr[] = round($this->fov_width_um, 2) . ' µm';
        }
        return Attribute::make(
            get: fn ($value) => $arr,
        );
    }

    public function detector(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->tags['detector'] ?? null,
        );
    }

    public function fovWidth(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->tags['fov_width'] ?? null,
        );
    }

    public function fovHeight(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->tags['fov_height'] ?? null,
        );
    }

    public function fovWidthUm(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->fovWidth ? $this->fovWidth * 1000 * 1000 : null,
        );
    }

    public function fovHeightUm(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->fovHeight ? $this->fovHeight * 1000 * 1000 : null,
        );
    }
}
