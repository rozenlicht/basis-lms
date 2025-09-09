<?php

namespace App\Filament\Resources;

use App\Enums\DocumentType;
use App\Enums\SampleType;
use App\Enums\TestType;
use App\Filament\Resources\SampleResource\Pages;
use App\Filament\Resources\SampleResource\RelationManagers;
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
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            ->schema([
                Infolists\Components\Section::make('Sample Information')
                    ->columnSpan(1)
                    ->icon('heroicon-o-puzzle-piece')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('unique_ref')
                            ->label('Unique Reference')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('sourceMaterial.unique_ref')
                            ->label('Source Material Reference')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                        Infolists\Components\TextEntry::make('type')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('description')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Processing Steps')
                    ->icon('heroicon-o-list-bullet')
                    ->columnSpan(1)
                    ->headerActions(
                        [
                            \Filament\Infolists\Components\Actions\Action::make('addProcessingStep')
                                ->label('Add processing step')
                                ->size('xs')
                                ->icon('heroicon-o-plus')
                                ->form([
                                    TextInput::make('name')
                                        ->label('Step Name')
                                        ->required(),
                                    Textarea::make('content')
                                        ->label('Description')
                                        ->required(),
                                ])
                                ->action(function (array $data, $record) {
                                    $record->processingSteps()->create($data);
                                })
                        ]
                    )
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('processingSteps')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Step Name')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                Infolists\Components\TextEntry::make('content')
                                    ->label('Description')
                                    ->hiddenLabel()
                                    ->markdown()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->contained(false),
                    ]),

                Infolists\Components\Section::make('Container & Location')
                    ->icon('heroicon-o-archive-box')
                    ->columnSpan(1)
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('container.name')
                            ->label('Container Name')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('compartment_x')
                            ->label('Position')
                            ->formatStateUsing(fn($record) => "x:{$record->compartment_x}, y:{$record->compartment_y}")
                            ->badge()
                            ->color('info'),
                    ]),

                Infolists\Components\Section::make('Technical Specifications')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(4)
                    ->columnSpan(1)
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('angle_wrt_source_material')
                            ->label('Angle (degrees)')
                            ->suffix('°'),
                        Infolists\Components\TextEntry::make('width_mm')
                            ->label('Width')
                            ->suffix(' mm'),
                        Infolists\Components\TextEntry::make('height_mm')
                            ->label('Height')
                            ->suffix(' mm'),
                        Infolists\Components\TextEntry::make('thickness_mm')
                            ->label('Thickness')
                            ->suffix(' mm'),
                        Infolists\Components\KeyValueEntry::make('properties')
                            ->label('Properties')
                            ->columnSpanFull(),
                    ]),
                Infolists\Components\Section::make('Documents')
                    ->icon('heroicon-o-document-text')
                    ->columnSpan(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->headerActions(
                        [
                            \Filament\Infolists\Components\Actions\Action::make('addDocument')
                                ->label('Add document')
                                ->size('xs')
                                ->icon('heroicon-o-plus')
                                ->form([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->label('Document Name')
                                        ->placeholder('Enter document name'),

                                    Select::make('type')
                                        ->required()
                                        ->options(DocumentType::options())
                                        ->label('Document Type')
                                        ->placeholder('Select document type'),

                                    Textarea::make('description')
                                        ->maxLength(500)
                                        ->label('Description')
                                        ->placeholder('Enter document description (optional)')
                                        ->rows(3),

                                    FileUpload::make('file_path')
                                        ->required()
                                        ->label('Upload File')
                                        ->directory('documents')
                                        ->helperText('Upload any file type. No size restrictions.')
                                        ->storeFileNamesIn('original_filename'),
                                ])
                                ->action(function (array $data, $record) {
                                    $record->documents()->create($data);
                                })
                        ]
                    )
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('documents')
                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\Section::make()
                                    ->schema([
                                        Infolists\Components\ImageEntry::make('preview_url')
                                            ->label('')
                                            ->height(120)
                                            ->width(120)
                                            ->square()
                                            ->extraAttributes(['class' => 'rounded-lg shadow-md']),

                                        Infolists\Components\TextEntry::make('name')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->weight('bold')
                                            ->size(Infolists\Components\TextEntry\TextEntrySize::Medium)
                                            ->extraAttributes(['class' => 'mt-2']),

                                        Infolists\Components\TextEntry::make('type')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color(fn(DocumentType $state): string => match ($state) {
                                                DocumentType::Drawing => 'info',
                                                DocumentType::Photo => 'success',
                                                DocumentType::Micrograph => 'success',
                                                DocumentType::Specification => 'warning',
                                                DocumentType::Other => 'gray',
                                            })
                                            ->formatStateUsing(fn(DocumentType $state): string => $state->label())
                                            ->extraAttributes(['class' => 'mt-1']),

                                        Infolists\Components\TextEntry::make('description')
                                            ->label('')
                                            ->hiddenLabel()
                                            ->markdown()
                                            ->limit(60)
                                            ->extraAttributes(['class' => 'mt-2 text-sm text-gray-600'])
                                            ->visible(fn($state) => !empty($state)),

                                        Infolists\Components\Actions::make([
                                            Infolists\Components\Actions\Action::make('view')
                                                ->label('View')
                                                ->icon('heroicon-o-eye')
                                                ->color('primary')
                                                ->size('sm')
                                                ->url(fn($record) => $record->view_url)
                                                ->openUrlInNewTab(),

                                            Infolists\Components\Actions\Action::make('download')
                                                ->label('Download')
                                                ->icon('heroicon-o-arrow-down-tray')
                                                ->color('gray')
                                                ->size('sm')
                                                ->url(fn($record) => $record->download_url)
                                                ->openUrlInNewTab(),
                                        ])
                                            ->extraAttributes(['class' => 'mt-3'])
                                            ->button()
                                            ->link(),
                                    ])
                                    ->extraAttributes([
                                        'class' => 'border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200 bg-white'
                                    ])
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->contained(false)
                            ->extraAttributes(['class' => 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4'])

                    ]),
            ]);
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
