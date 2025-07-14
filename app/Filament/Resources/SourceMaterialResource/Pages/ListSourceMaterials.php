<?php

namespace App\Filament\Resources\SourceMaterialResource\Pages;

use App\Filament\Resources\SourceMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSourceMaterials extends ListRecords
{
    protected static string $resource = SourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
