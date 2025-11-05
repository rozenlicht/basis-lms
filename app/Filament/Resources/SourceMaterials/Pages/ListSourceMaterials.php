<?php

namespace App\Filament\Resources\SourceMaterials\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SourceMaterials\SourceMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSourceMaterials extends ListRecords
{
    protected static string $resource = SourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
