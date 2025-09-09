<?php

namespace App\Filament\Resources;

use App\Enums\DocumentType;
use App\Filament\Resources\SampleResource\Pages;
use App\Filament\Resources\SampleResource\RelationManagers;
use App\Filament\Resources\SampleResource\SampleInfolistSchema\SampleInfolistSchema;
use App\Models\Sample;
use App\Models\ProcessingStepTemplate;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class SampleResource extends Resource
{
    protected static ?string $model = Sample::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('unique_ref')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('container_id')
                    ->relationship('container', 'name')
                    ->nullable()
                    ->label('Container'),
                Forms\Components\Select::make('source_material_id')
                    ->relationship('sourceMaterial', 'unique_ref')
                    ->required(),
                Section::make('Technical Information')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('width_mm')
                            ->numeric(),
                        Forms\Components\TextInput::make('height_mm')
                            ->numeric(),
                        Forms\Components\TextInput::make('thickness_mm')
                            ->numeric(),
                        Forms\Components\KeyValue::make('properties'),
                    ]),
                Section::make('Processing')
                    ->collapsed()
                    ->schema([
                        Repeater::make('processingSteps')
                            ->relationship('processingSteps')
                            ->hiddenLabel()
                            ->collapsed()
                            ->schema([

                                // Manual entry fields
                                TextInput::make('name')
                                    ->label('Name')
                                    ->columnSpanFull()
                                    ->maxWidth('lg')
                                    ->required(),

                                Textarea::make('content')
                                    ->label('Text')
                                    ->rows(3)

                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add Processing Step')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Processing Step')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unique_ref')
                    ->formatStateUsing(fn(Sample $record) => $record->sourceMaterial->unique_ref . '-' . $record->unique_ref)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sourceMaterial.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('width_mm')
                ->label('Dimensions (mm)')
                ->formatStateUsing(fn(Sample $record) => $record->width_mm . ' x ' . $record->height_mm . ' x ' . $record->thickness_mm)
            ])
            ->filters([
                //
            ])
            ->recordUrl(null)
            ->recordAction('view-slideover')
            ->actions([
                Tables\Actions\Action::make('view-slideover')
                ->label('View')
                ->hiddenLabel()
                ->icon('heroicon-o-eye')
                ->color('info')
                ->button()
                ->infolist(SampleInfolistSchema::schema(collapsed: false))
                ->slideOver()
                ,
                Tables\Actions\ViewAction::make()
                ->hiddenLabel()
                ->color('success')
                ->button()
                ->icon('heroicon-o-arrow-top-right-on-square'),
                Tables\Actions\EditAction::make()
                ->hiddenLabel()
                ->button()
                ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                ->hiddenLabel()
                ->button()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Delete Sample')
                ->modalDescription('Are you sure you want to delete this sample? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete it'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(2)
            ->schema(SampleInfolistSchema::schema());
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSamples::route('/'),
            'create' => Pages\CreateSample::route('/create'),
            'edit' => Pages\EditSample::route('/{record}/edit'),
            'view' => Pages\ViewSample::route('/{record}/view'),
        ];
    }
}
