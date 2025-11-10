<?php

namespace App\Filament\Resources\SourceMaterials\Pages;

use App\Filament\Resources\SourceMaterials\SourceMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSourceMaterial extends ViewRecord
{
    protected static string $resource = SourceMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->name ?? 'View Source Material';
    }
}
