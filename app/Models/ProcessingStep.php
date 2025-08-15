<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingStep extends Model
{
    protected $fillable = [
        'name',
        'description',
        'content',
        'template_id',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'sample_id');
    }
    
    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }
}
