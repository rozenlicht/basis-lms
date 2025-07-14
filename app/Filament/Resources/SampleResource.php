<?php

namespace App\Filament\Resources;

use App\Enums\SampleType;
use App\Enums\TestType;
use App\Filament\Resources\SampleResource\Pages;
use App\Filament\Resources\SampleResource\RelationManagers;
use App\Models\Sample;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\Select::make('source_material_id')
                    ->relationship('sourceMaterial', 'unique_ref')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->required()    
                    ->options(SampleType::options()),
                Forms\Components\Select::make('test')
                    ->required()
                    ->options(TestType::options()),
                Forms\Components\DateTimePicker::make('testing_date'),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('angle_wrt_source_material')
                    ->numeric(),
                Forms\Components\TextInput::make('width_mm')
                    ->numeric(),
                Forms\Components\TextInput::make('height_mm')
                    ->numeric(),
                Forms\Components\TextInput::make('thickness_mm')
                    ->numeric(),
                Forms\Components\TextInput::make('properties'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unique_ref')
                ->formatStateUsing(fn (Sample $record) => $record->sourceMaterial->unique_ref . '-' . $record->unique_ref)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sourceMaterial.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
