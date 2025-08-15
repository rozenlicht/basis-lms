<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerNote extends Model
{
    protected $fillable = [
        'author_id',
        'container_id',
        'compartment_x',
        'compartment_y',
        'content',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
