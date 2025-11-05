<?php

namespace App\Filament\Resources\Samples\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Samples\SampleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSamples extends ListRecords
{
    protected static string $resource = SampleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
