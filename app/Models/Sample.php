<?php

namespace App\Models;

use App\Models\ProcessingStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

class Sample extends Model
{
    protected $fillable = [
        'unique_ref',
        'container_id',
        'compartment_x',
        'compartment_y',
        'source_material_id',
        'description',
        'width_mm',
        'height_mm',
        'thickness_mm',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function sourceMaterial()
    {
        return $this->belongsTo(SourceMaterial::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function processingSteps(): MorphMany
    {
        return $this->morphMany(ProcessingStep::class, 'processable')
            ->orderBy('created_at');
    }

    public function processingTimeline(): Collection
    {
        $this->loadMissing('processingSteps', 'sourceMaterial.processingSteps');

        $materialSteps = $this->sourceMaterial
            ? $this->sourceMaterial
                ->processingSteps
                ->map(function (ProcessingStep $step): array {
                    return [
                        'id' => $step->id,
                        'name' => $step->name,
                        'description' => $step->description,
                        'content' => $step->content,
                        'performed_at' => optional($step->created_at)->format('Y-m-d'),
                        'scope' => 'source-material',
                        'sort_key' => optional($step->created_at)->timestamp ?? 0,
                    ];
                })
                ->sortBy('sort_key')
                ->values()
                ->map(function (array $step): array {
                    unset($step['sort_key']);

                    return $step;
                })
            : collect();

        $sampleSteps = $this->processingSteps
            ->map(function (ProcessingStep $step): array {
                return [
                    'id' => $step->id,
                    'name' => $step->name,
                    'description' => $step->description,
                    'content' => $step->content,
                    'performed_at' => optional($step->created_at)->format('Y-m-d'),
                    'scope' => 'sample',
                    'sort_key' => optional($step->created_at)->timestamp ?? 0,
                ];
            })
            ->sortBy('sort_key')
            ->values()
            ->map(function (array $step): array {
                unset($step['sort_key']);

                return $step;
            });

        return $materialSteps->concat($sampleSteps)->values();
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->latest();
    }

    public function latestPhoto(): MorphOne
    {
        return $this->morphOne(Photo::class, 'imageable')->latestOfMany();
    }

    public function containerPosition()
    {
        return $this->hasOne(ContainerPosition::class);
    }
}
