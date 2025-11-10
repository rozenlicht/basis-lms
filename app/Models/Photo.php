<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Photo extends Model
{
    protected $fillable = [
        'user_id',
        'file_path',
        'description',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
