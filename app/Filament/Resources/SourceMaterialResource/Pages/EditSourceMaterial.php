<?php

namespace App\Filament\Resources\SourceMaterialResource\Pages;

use App\Filament\Resources\SourceMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSourceMaterial extends EditRecord
{
    protected static string $resource = SourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
