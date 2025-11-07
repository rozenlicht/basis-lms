<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

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

    protected function isStarred(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool) $value,
        );
    }

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
    
    // While saving, if the properties seems to be a string containing valid JSON, parse it into an array
    public function setPropertiesAttribute($value)
    {
        if (is_array($value) && count($value) === 1 && is_string($value[0])) {
            if(json_decode($value[0], true) !== null) {
                $this->attributes['properties'] = json_decode($value[0], true);
            } else {
                $this->attributes['properties'] = $value;
            }
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

    public function starredByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'source_material_user')->withTimestamps();
    }

    public function isStarredBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (array_key_exists('is_starred', $this->attributes)) {
            return (bool) $this->attributes['is_starred'];
        }

        return $this->starredByUsers()
            ->where('user_id', $user->getKey())
            ->exists();
    }

    public function scopeWithIsStarredFor(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query;
        }

        return $query->withExists([
            'starredByUsers as is_starred' => fn ($relationQuery) => $relationQuery->where('user_id', $user->getKey()),
        ]);
    }
}
