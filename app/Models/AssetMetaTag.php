<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMetaTag extends Model
{
    protected $fillable = [
        'asset_id',
        'key',
        'value',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
