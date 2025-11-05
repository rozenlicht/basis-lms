<?php

namespace App\Filament\Resources\SourceMaterials\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Samples\SampleResource;
use App\Models\Sample;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SamplesRelationManager extends RelationManager
{
    protected static string $relationship = 'samples';

    public function isReadOnly(): bool
    {
        return false;
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unique_ref')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('unique_ref')
            ->columns([
                TextColumn::make('unique_ref')
                ->formatStateUsing(fn (Sample $record) => $record->sourceMaterial->unique_ref . '-' . $record->unique_ref)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn (Sample $record) => SampleResource::getUrl('view', ['record' => $record]))
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
