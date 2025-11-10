<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProcessingStep extends Model
{
    protected $fillable = [
        'name',
        'description',
        'content',
        'template_id',
    ];

    public function processable(): MorphTo
    {
        return $this->morphTo();
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable');
    }
}
