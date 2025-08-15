<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SourceMaterialResource\Pages;
use App\Filament\Resources\SourceMaterialResource\RelationManagers;
use App\Models\SourceMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use ValentinMorice\FilamentJsonColumn\JsonColumn;
use Filament\Infolists;

class SourceMaterialResource extends Resource
{
    protected static ?string $model = SourceMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?int $navigationSort = 1;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ─────────────────────────── Overview ───────────────────────────
                Infolists\Components\Section::make('Overview')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('unique_ref')->label('Reference'),
                        TextEntry::make('name')->label('Name'),
                        TextEntry::make('supplier')->label('Supplier'),
                        TextEntry::make('supplier_identifier')->label('Supplier ID'),
                        TextEntry::make('grade')->label('Grade'),
                        TextEntry::make('description')->markdown(),
                    ]),

                // ─────── Dimensions & Composition side‑by‑side ───────
                Infolists\Components\Grid::make()
                    ->columns(2)
                    ->schema([
                        Infolists\Components\Section::make('Dimensions (mm)')
                            ->columnSpan(1)
                            ->columns(3)
                            ->schema([
                                TextEntry::make('width_mm')->label('Width'),
                                TextEntry::make('height_mm')->label('Height'),
                                TextEntry::make('thickness_mm')->label('Thickness'),
                            ]),
                        ViewEntry::make('composition_bar')
                            ->view('filament.view-entries.composition-bar-widget'),
                    ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Administration')
                    ->schema([
                        Forms\Components\TextInput::make('unique_ref')
                            ->required()
                            ->helperText('Warning: changes in this ref will not propagate to existing samples')
                            ->unique('source_materials', 'unique_ref'),
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('supplier')
                            ->default('TATA Steel // Arjan Rijkenberg'),
                        Forms\Components\TextInput::make('supplier_identifier'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                    ]),
                Section::make('Technical')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('grade')->columnSpan(2),
                        Fieldset::make('Dimensions')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('width_mm')->numeric()->columnSpan(1),
                                Forms\Components\TextInput::make('height_mm')->numeric()->columnSpan(1),
                                Forms\Components\TextInput::make('thickness_mm')->numeric()->columnSpan(1),
                            ]),
                        JsonColumn::make('composition')
                            ->default('{
  "C": 0,
  "Mn": 0,
  "Si": 0,
  "Al": 0,
  "P": 0,
  "S": 0,
  "Nb": 0,
  "Ti": 0,
  "N": 0,
  "Cu": 0,
  "Cr": 0,
  "Ni": 0,
  "Mo": 0,
  "V": 0,
  "B": 0
}')
                            ->columnSpanFull(),
                        JsonColumn::make('properties')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unique_ref')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier_identifier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('samples_count')
                    ->counts('samples')
                    ->label('Samples')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('grade')
                    ->collapsible(),
                Group::make('name')
                    ->collapsible(),
                Group::make('supplier_identifier')
                    ->collapsible()
            ])
            ->defaultGroup('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->form([
                        Forms\Components\TextInput::make('unique_ref')
                            ->required()
                            ->unique('source_materials', 'unique_ref'),
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // BulkAction::make('Composition report')
                    // ->icon('heroicon-m-arrow-down-tray')
                    // ->openUrlInNewTab()
                    // ->deselectRecordsAfterCompletion()
                    //     ->action(function (Collection $records, $livewire) {
                    //        $livewire->redirect(route('composition-report', ['source_materials' => $records->pluck('id')->toArray()]), true);
                    //     })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\NotesRelationManager::class,
            RelationManagers\SamplesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSourceMaterials::route('/'),
            'create' => Pages\CreateSourceMaterial::route('/create'),
            'edit' => Pages\EditSourceMaterial::route('/{record}/edit'),
            'view' => Pages\ViewSourceMaterial::route('/{record}/view'),
        ];
    }
}
