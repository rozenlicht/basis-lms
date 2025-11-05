<?php

namespace App\Filament\Resources\Samples\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\ProcessingStepTemplate;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class ProcessingStepsRelationManager extends RelationManager
{
    protected static string $relationship = 'processingSteps';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('template_id')
                    ->label('Load from Template (Optional)')
                    ->options(ProcessingStepTemplate::all()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Choose a template to pre-fill...')
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state) {
                            $template = ProcessingStepTemplate::find($state);
                            if ($template) {
                                $set('name', $template->name);
                                $set('content', $template->content);
                            }
                        }
                    }),
                
                TextInput::make('name')
                    ->label('Step Name')
                    ->required(),
                
                Textarea::make('description')
                    ->label('Description')
                    ->rows(2),
                
                Textarea::make('content')
                    ->label('Step Content')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->limit(100)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
