<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = [
        'name',
        'compartments_x_size',
        'compartments_y_size',
    ];

    public function notes()
    {
        return $this->hasMany(ContainerNote::class);
    }

    public function samples()
    {
        return $this->hasMany(Sample::class);
    }


    public function getCompartmentsCountAttribute()
    {
        return $this->compartments_x_size * $this->compartments_y_size;
    }

    public function getReadableSizeAttribute()
    {
        return "{$this->compartments_x_size} x {$this->compartments_y_size}";
    }

    public function notesOfCompartment($x, $y)
    {
        return $this->notes()->where('compartment_x', $x)->where('compartment_y', $y)->get();
    }

    public function addNote($x, $y, $content, $authorId = null)
    {
        return $this->notes()->create([
            'compartment_x' => $x,
            'compartment_y' => $y,
            'content' => $content,
            'author_id' => $authorId,
        ]);
    }
}
