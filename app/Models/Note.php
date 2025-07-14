<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'content',
        'author_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
