<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SourceMaterial extends Model
{
    protected $fillable = [
        'unique_ref',
        'name',
        'description',
        'grade',
        'supplier',
        'supplier_identifier',
        'composition',
        'width_mm',
        'height_mm',
        'thickness_mm',
        'properties',
    ];

    protected $casts = [
        'composition' => 'array',
        'properties' => 'array',
    ];

    public function samples()
    {
        return $this->hasMany(Sample::class);
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
