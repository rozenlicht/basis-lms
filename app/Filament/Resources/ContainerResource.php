<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContainerResource\Pages;
use App\Filament\Resources\ContainerResource\RelationManagers;
use App\Infolists\Components\ContainerContentEntry;
use App\Models\Container;
use App\Models\ContainerPosition;
use App\Models\Sample;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;

class ContainerResource extends Resource
{
    protected static ?string $model = Container::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->label('Container Name'),

                Forms\Components\TextInput::make('compartments_x_size')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->label('Compartments X Size'),

                Forms\Components\TextInput::make('compartments_y_size')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->label('Compartments Y Size'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Container Name'),

                Tables\Columns\TextColumn::make('readable_size')
                    ->sortable()
                    ->label('Compartment Size')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->recordUrl(fn (Container $record): string => ContainerResource::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name'),
                Infolists\Components\TextEntry::make('readable_size')
                    ->label('Compartments Size'),
                ContainerContentEntry::make('positions')
                    ->columnSpanFull()
                    ->registerActions([
                        Action::make('addPosition')
                            ->label('Add Position')
                            ->button()
                            ->size('xs')
                            ->form([
                                Forms\Components\TextInput::make('x')
                                    ->label('X Position')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1),
                                Forms\Components\TextInput::make('y')
                                    ->label('Y Position')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1),
                                Forms\Components\Radio::make('position_type')
                                    ->label('Position Type')
                                    ->options([
                                        'sample' => 'Sample',
                                        'custom' => 'Custom Name',
                                    ])
                                    ->default('sample')
                                    ->required()
                                    ->reactive(),
                                Forms\Components\Select::make('sampleId')
                                    ->label('Sample')
                                    ->options(function() {
                                        // Get samples that are not assigned to any container position
                                        $assignedSampleIds = ContainerPosition::whereNotNull('sample_id')->pluck('sample_id');
                                        return Sample::query()
                                            ->whereNotIn('id', $assignedSampleIds)
                                            ->pluck('unique_ref', 'id');
                                    })
                                    ->searchable()
                                    ->placeholder('Select Sample')
                                    ->visible(fn($get) => $get('position_type') === 'sample'),
                                Forms\Components\TextInput::make('customName')
                                    ->label('Custom Name')
                                    ->placeholder('Enter custom name')
                                    ->visible(fn($get) => $get('position_type') === 'custom'),
                            ])
                            ->icon('heroicon-o-pencil')
                            ->action(function(array $data, Container $record, $arguments) {
                                if ($data['position_type'] === 'sample' && $data['sampleId']) {
                                    $record->setPositionSample($data['x'], $data['y'], $data['sampleId']);
                                    Notification::make()
                                        ->title('Sample added to container position')
                                        ->success()
                                        ->send();
                                } elseif ($data['position_type'] === 'custom' && $data['customName']) {
                                    $record->setPositionCustomName($data['x'], $data['y'], $data['customName']);
                                    Notification::make()
                                        ->title('Custom name added to container position')
                                        ->success()
                                        ->send();
                                }
                            })
                            ->modalWidth('md')
                            ->modalHeading('Add Position'),
                        Action::make('editPosition')
                            ->label('Edit Position')
                            ->button()
                            ->size('xs')
                            ->form(function($arguments) {
                                $position = ContainerPosition::find($arguments['positionId'] ?? null);
                                return [
                                    Forms\Components\TextInput::make('x')
                                        ->label('X Position')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->default($position?->compartment_x),
                                    Forms\Components\TextInput::make('y')
                                        ->label('Y Position')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->default($position?->compartment_y),
                                    Forms\Components\Radio::make('position_type')
                                        ->label('Position Type')
                                        ->options([
                                            'sample' => 'Sample',
                                            'custom' => 'Custom Name',
                                        ])
                                        ->default($position?->hasSample() ? 'sample' : 'custom')
                                        ->required()
                                        ->reactive(),
                                    Forms\Components\Select::make('sampleId')
                                        ->label('Sample')
                                        ->options(function() use ($position) {
                                            // Get samples that are not assigned to any container position
                                            $assignedSampleIds = ContainerPosition::whereNotNull('sample_id')
                                                ->where('id', '!=', $position?->id ?? 0)
                                                ->pluck('sample_id');
                                            return Sample::query()
                                                ->whereNotIn('id', $assignedSampleIds)
                                                ->pluck('unique_ref', 'id');
                                        })
                                        ->searchable()
                                        ->placeholder('Select Sample')
                                        ->visible(fn($get) => $get('position_type') === 'sample')
                                        ->default($position?->sample_id),
                                    Forms\Components\TextInput::make('customName')
                                        ->label('Custom Name')
                                        ->placeholder('Enter custom name')
                                        ->visible(fn($get) => $get('position_type') === 'custom')
                                        ->default($position?->custom_name),
                                ];
                            })
                            ->icon('heroicon-o-pencil')
                            ->action(function(array $data, Container $record, $arguments) {
                                $position = ContainerPosition::find($arguments['positionId'] ?? null);
                                if ($position) {
                                    if ($data['position_type'] === 'sample' && $data['sampleId']) {
                                        $record->setPositionSample($data['x'], $data['y'], $data['sampleId']);
                                        Notification::make()
                                            ->title('Position updated with sample')
                                            ->success()
                                            ->send();
                                    } elseif ($data['position_type'] === 'custom' && $data['customName']) {
                                        $record->setPositionCustomName($data['x'], $data['y'], $data['customName']);
                                        Notification::make()
                                            ->title('Position updated with custom name')
                                            ->success()
                                            ->send();
                                    }
                                }
                            })
                            ->modalWidth('md')
                            ->modalHeading('Edit Position'),
                        Action::make('clearPosition')
                            ->label('Clear Position')
                            ->button()
                            ->size('xs')
                            ->color('danger')
                            ->icon('heroicon-o-trash')
                            ->action(function(array $data, Container $record, $arguments) {
                                $record->clearPosition($arguments['x'], $arguments['y']);
                                Notification::make()
                                    ->title('Position cleared')
                                    ->success()
                                    ->send();
                            })
                            ->requiresConfirmation()
                            ->modalHeading('Clear Position')
                            ->modalDescription('Are you sure you want to clear this position?'),
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContainers::route('/'),
            'edit' => Pages\EditContainer::route('/{record}/edit'),
            'view' => Pages\ViewContainer::route('/{record}'),
            'view-lite' => Pages\ViewContainerLite::route('/{record}/lite'),
        ];
    }
}
