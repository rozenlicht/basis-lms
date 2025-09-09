<?php

namespace App\Filament\Resources\SampleResource\Pages;

use App\Filament\Resources\SampleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSample extends ViewRecord
{
    protected static string $resource = SampleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->size('sm'),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->unique_ref;
    }

    protected function getInfolistSchema(): array
    {
        return \App\Filament\Resources\SampleResource\SampleInfolistSchema\SampleInfolistSchema::schema(false);
    }
}
