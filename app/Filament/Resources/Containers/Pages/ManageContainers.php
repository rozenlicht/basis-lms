<?php

namespace App\Filament\Resources\Containers\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Containers\ContainerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContainers extends ManageRecords
{
    protected static string $resource = ContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
