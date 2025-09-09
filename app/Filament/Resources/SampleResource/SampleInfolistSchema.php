<?php

namespace App\Filament\Resources\SampleResource\SampleInfolistSchema;

use App\Enums\DocumentType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists;

final class SampleInfolistSchema
{
    public static function schema(bool $collapsed = true): array
    {
        return [
            Infolists\Components\Section::make('Sample Information')
                ->columnSpan(1)
                ->icon('heroicon-o-puzzle-piece')
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                ])
                ->schema([
                    Infolists\Components\TextEntry::make('unique_ref')
                        ->label('Unique Reference')
                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('sourceMaterial.unique_ref')
                        ->label('Source Material Reference')
                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                    Infolists\Components\TextEntry::make('description')
                        ->markdown()
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Processing Steps')
                ->icon('heroicon-o-list-bullet')
                ->columnSpan(1)
                ->collapsible()
                ->collapsed(true)
                ->headerActions(
                    [
                        \Filament\Infolists\Components\Actions\Action::make('addProcessingStep')
                            ->label('Add Step')
                            ->size('sm')
                            ->icon('heroicon-o-plus')
                            ->modalWidth('sm')
                            ->form([
                                TextInput::make('name')
                                    ->label('Step Name')
                                    ->required(),
                                Textarea::make('content')
                                    ->label('Description')
                                    ->required()
                                    ->rows(3),
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
                                ->size(Infolists\Components\TextEntry\TextEntrySize::Medium),
                            Infolists\Components\TextEntry::make('content')
                                ->label('Description')
                                ->formatStateUsing(fn (string $state): string => nl2br($state) ?? '')
                                ->hiddenLabel()
                                ->markdown()
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->contained(false)
                        ->extraAttributes(['class' => 'space-y-3']),
                ]),

            Infolists\Components\Section::make('Container & Location')
                ->icon('heroicon-o-archive-box')
                ->columnSpan(1)
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                ])
                ->collapsed($collapsed)
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
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 4,
                ])
                ->columnSpan(1)
                ->collapsed($collapsed)
                ->schema([
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
                ->columnSpan(1)
                ->collapsible()
                ->collapsed(false)
                ->headerActions(
                    [
                        \Filament\Infolists\Components\Actions\Action::make('addDocument')
                            ->label('Add Document')
                            ->size('sm')
                            ->icon('heroicon-o-plus')
                            ->modalWidth('sm')
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
                                        ->height(100)
                                        ->width(100)
                                        ->square()
                                        ->extraAttributes(['class' => 'rounded-lg shadow-md mx-auto']),

                                    Infolists\Components\TextEntry::make('name')
                                        ->label('')
                                        ->hiddenLabel()
                                        ->weight('bold')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Medium)
                                        ->extraAttributes(['class' => 'mt-2 text-center']),

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
                                        ->extraAttributes(['class' => 'mt-1 text-center']),

                                    Infolists\Components\TextEntry::make('description')
                                        ->label('')
                                        ->hiddenLabel()
                                        ->markdown()
                                        ->limit(50)
                                        ->extraAttributes(['class' => 'mt-2 text-sm text-gray-600 text-center'])
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
                                        ->extraAttributes(['class' => 'mt-3 justify-center'])
                                        ->alignment('center'),
                                ])
                                ->extraAttributes([
                                    'class' => 'border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200 bg-white w-full max-w-xs mx-auto'
                                ])
                                ->columnSpan(1),
                        ])
                        ->columns(1)
                        ->contained(false)
                        ->extraAttributes(['class' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 justify-items-center'])

                ]),
            ];
    }
}