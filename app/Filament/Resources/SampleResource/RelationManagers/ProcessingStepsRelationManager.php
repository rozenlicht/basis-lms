<?php

namespace App\Filament\Resources\SampleResource\RelationManagers;

use App\Models\ProcessingStepTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;

class ProcessingStepsRelationManager extends RelationManager
{
    protected static string $relationship = 'processingSteps';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('template_id')
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
                
                Forms\Components\TextInput::make('name')
                    ->label('Step Name')
                    ->required(),
                
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2),
                
                Forms\Components\Textarea::make('content')
                    ->label('Step Content')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->limit(100)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
