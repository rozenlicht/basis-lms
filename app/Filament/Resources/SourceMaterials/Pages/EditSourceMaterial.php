<?php

namespace App\Filament\Resources\SourceMaterials\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\SourceMaterials\SourceMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSourceMaterial extends EditRecord
{
    protected static string $resource = SourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
