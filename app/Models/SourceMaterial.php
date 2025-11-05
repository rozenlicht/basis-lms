<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SourceMaterial extends Model
{
    use SoftDeletes;
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

    // While saving, if the composition seems to be a string containing valid JSON, parse it into an array
    public function setCompositionAttribute($value)
    {
        if (is_string($value) && json_decode($value, true) !== null) {
            $this->attributes['composition'] = json_decode($value, true);
        } else {
            $this->attributes['composition'] = $value;
        }
    }

    // While saving, if the properties seems to be a string containing valid JSON, parse it into an array
    public function setPropertiesAttribute($value)
    {
        if (is_string($value) && json_decode($value, true) !== null) {
            $this->attributes['properties'] = json_decode($value, true);
        } else {
            $this->attributes['properties'] = $value;
        }
    }
    
    public function samples()
    {
        return $this->hasMany(Sample::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
