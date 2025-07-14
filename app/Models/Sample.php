<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = [
        'unique_ref',
        'source_material_id',
        'type',
        'test',
        'testing_date',
        'description',
        'angle_wrt_source_material',
        'width_mm',
        'height_mm',
        'thickness_mm',
        'properties',
    ];

    public function sourceMaterial()
    {
        return $this->belongsTo(SourceMaterial::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
