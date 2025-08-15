<?php

namespace App\Filament\Resources\SourceMaterialResource\Widgets;

use App\Models\SourceMaterial;
use Filament\Widgets\ChartWidget;

class CompositionBarWidget extends ChartWidget
{
    protected static ?string $heading = 'Composition';

    public ?SourceMaterial $record = null;

    protected function getData(): array
    {
        // Data is of shape ['label' => 0.02], retrieve from $this->record->composition
        $compositions = collect($this->record->composition)->sort()->reverse();
        return [
            'datasets' => [
                [
                    'label' => 'Composition',
                    'data' => $compositions->values()->toArray(),
                    'backgroundColor' => '#04688f',
                    'borderColor' => '#04688f',
                ],
            ],
            'labels' => $compositions->keys()->toArray(),
        ];
    }

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
            'datalabels' => [
                'color' => '#000',
                'anchor' => 'start',
                'align' => 'top',
                'font' => [
                    'size' => 9
                ]
            ],
        ],
    ];

    protected function getType(): string
    {
        return 'bar';
    }
}
