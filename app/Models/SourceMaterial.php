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
        // if value is array with one string element, convert it to a JSON string
        if (is_array($value) && count($value) === 1 && is_string($value[0])) {
            if(json_decode($value[0], true) !== null) {
                $this->attributes['composition'] = json_decode($value[0], true);
            } else {
                $this->attributes['composition'] = $value;
            }
        } else {
            $this->attributes['composition'] = $value;
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
