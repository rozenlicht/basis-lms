<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContainerResource\Pages;
use App\Filament\Resources\ContainerResource\RelationManagers;
use App\Infolists\Components\ContainerContentEntry;
use App\Models\Container;
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
                ContainerContentEntry::make('samples')
                    ->columnSpanFull()
                    ->registerActions([
                        Action::make('addSample')
                            ->label('Add Sample')
                            ->button()
                            ->size('xs')
                            ->form([
                                Forms\Components\TextInput::make('x'),
                                Forms\Components\TextInput::make('y'),
                                Forms\Components\Select::make('sampleId')
                                    ->relationship('samples', 'unique_ref', fn() => Sample::query()->whereNull('container_id'))
                                    ->label('Sample')
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Select Sample'),
                            ])
                            ->icon('heroicon-o-pencil')
                            ->action(function(array $data, Container $record, $arguments) {
                                $sample = Sample::find($data['sampleId']);
                                if ($sample) {
                                    $sample->update([
                                        'container_id' => $record->id,
                                        'compartment_x' => $data['x'],
                                        'compartment_y' => $data['y'],
                                    ]);
                                    Notification::make()
                                        ->title('Sample added to container')
                                        ->success()
                                        ->send();
                                }
                            })
                            ->modalWidth('md')
                            ->modalHeading(fn($action, $arguments) => isset($arguments['sampleId']) ? 'Edit Sample' : 'Add Sample'),
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
