<?php

namespace App\Filament\Resources\Assets;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ViewAsset;
use App\Models\Asset;
use App\Models\Sample;
use App\Models\SourceMaterial;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Tapp\FilamentValueRangeFilter\Filters\ValueRangeFilter;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ─────────────────────────── Image Preview ───────────────────────────
                Grid::make()
                    ->columns(2)
                    ->schema([
                        Section::make('Preview')
                            ->icon('heroicon-o-photo')
                            ->columnSpan([
                                'sm' => 1,
                                'lg' => 1,
                            ])
                            ->schema([
                                ImageEntry::make('path_thumbnail')
                                    ->hiddenLabel()
                                    ->defaultImageUrl(url('/images/placeholder-doc.jpg'))
                                    ->height('300px')
                                    ->extraAttributes([
                                        'class' => 'rounded-lg',
                                    ])
                                    ->disk('local')
                                    ->visibility('private')
                                    ->hidden(fn ($record) => !$record->path_thumbnail),
                            ]),

                        // ─────────────────────────── File Information ───────────────────────────
                        Section::make('File Information')
                            ->icon('heroicon-o-document')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpanFull(),
                                
                                TextEntry::make('mime_type')
                                    ->label('MIME Type')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('size_kb')
                                    ->label('Size')
                                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . ' KB' : 'N/A')
                                    ->badge()
                                    ->color('gray'),
                                
                                TextEntry::make('fov_width_um')
                                    ->label('FOV Width')
                                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) . ' µm' : 'N/A')
                                    ->badge()
                                    ->color('gray'),
                                
                                TextEntry::make('detector')
                                    ->label('Detector')
                                    ->badge()
                                    ->color('success')
                                    ->visible(fn ($record) => !empty($record->detector)),
                            ]),
                    ])
                    ->columnSpanFull(),

                // ─────────────────────────── Tags ───────────────────────────
                Section::make('Tags')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->collapsed(true)
                    ->schema([
                        KeyValueEntry::make('tags')
                            ->label('')
                            ->visible(fn ($record) => !empty($record->tags)),
                        TextEntry::make('tags_empty')
                            ->label('')
                            ->state('No tags assigned')
                            ->hidden(fn ($record) => !empty($record->tags)),
                    ]),

                // ─────────────────────────── Attachments ───────────────────────────
                Section::make('Attachments')
                    ->icon('heroicon-o-paper-clip')
                    ->collapsible()
                    ->collapsed(false)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('attachments')
                            ->label('')
                            ->formatStateUsing(function ($state, $record) {
                                $list = $record->attachments;
                                if (empty($list) || !is_array($list)) {
                                    return '';
                                }
                                $links = [];
                                foreach ($list as $attachment) {
                                    $filename = basename($attachment);
                                    $url = route('download.attachment', ['path' => urlencode($attachment)]);
                                    $links[] = '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:text-primary-700 underline">' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '</a>';
                                }
                                
                                return implode('<br>', $links);
                            })
                            ->html()
                            ->visible(fn ($record) => !empty($record->attachments))
                    ]),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                Select::make('user_id')->relationship('user', 'name')->default(auth()->id())->searchable()->preload()->required(),
                MorphToSelect::make('subject')->columns(2)->searchable()->preload()->types([
                    MorphToSelect\Type::make(Sample::class)->titleAttribute('unique_ref'),
                    MorphToSelect\Type::make(SourceMaterial::class)->titleAttribute('unique_ref')
                ])->columnSpanFull(),
                FileUpload::make('path')
                    ->directory('assets')
                    ->columnSpanFull()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        // If name is empty and a file was uploaded, set name from filename
                        if (empty($get('name')) && $state) {
                            $filename = null;
                            
                            // Handle UploadedFile instance (original file before processing)
                            if ($state instanceof UploadedFile) {
                                $filename = pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME);
                            } 
                            // Handle array (multiple files)
                            elseif (is_array($state) && !empty($state)) {
                                $file = $state[0] ?? null;
                                if ($file instanceof UploadedFile) {
                                    $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                } elseif (is_string($file)) {
                                    $filename = pathinfo(basename($file), PATHINFO_FILENAME);
                                }
                            }
                            // Handle string path (after processing)
                            elseif (is_string($state)) {
                                $filename = pathinfo(basename($state), PATHINFO_FILENAME);
                            }
                            
                            if ($filename) {
                                $set('name', $filename);
                            }
                        }
                    }),
                FileUpload::make('attachments')
                    ->directory('assets/attachments')
                    ->multiple()
                    ->columnSpanFull(),
                KeyValue::make('tags')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('path_thumbnail')
                        ->square()
                        ->imageSize(210)
                        ->defaultImageUrl(url('/images/placeholder-doc.jpg')),
                    TextColumn::make('name')
                    ->weight('bold')
                    ->limit(50),
                    Split::make([
                        TextColumn::make('table_tags')->badge()
                    ]),
                ])
            ])
            ->filters([
                SelectFilter::make('tags->type')
                    ->options(['BSE' => 'BSE', 'SE' => 'SE', 'EDS' => 'EDS', 'IPF' => 'IPF', 'KAM' => 'KAM'])
                    ->query(
                        function (Builder $query, $state): Builder {
                            $value = $state['value'] ?? null;
                            if ($value) {
                                return $query->whereJsonContains('tags', ['type' => $value]);
                            }
                            return $query;
                        }
                    ),
                    ValueRangeFilter::make('tags->fov_width')
                    ->label('FOV Width (µm)')
                    ->query(
                        function (Builder $query, $state): Builder {
                            $condition = $state['range_condition'] ?? null;
                            if($condition) {
                                $isBetween = $condition === 'between' || $condition === 'not_between';
                                $operator = match($condition) {
                                    'between' => 'between',
                                    'not_between' => 'not between',
                                    'less_than' => '<',
                                    'less_than_equal' => '<=',
                                    'greater_than' => '>',
                                    'greater_than_equal' => '>=',   
                                };
                                // Use something like WHERE CAST(JSON_EXTRACT(tags, '$.price') AS DECIMAL(10,2)) < 200;
                                if($isBetween) {
                                    $value1 = floatval($state['range_' . $condition . '_from']) / (1000 * 1000) ?? null;
                                    $value2 = floatval($state['range_' . $condition . '_to']) / (1000 * 1000) ?? null;
                                    // Do < AND > in the query
                                    if($value1 && $value2) {
                                        $query->whereRaw("CAST(JSON_EXTRACT(tags, '$.fov_width') AS DECIMAL(10,10)) < ? AND CAST(JSON_EXTRACT(tags, '$.fov_width') AS DECIMAL(10,10)) > ?", [$value1, $value2]);
                                    }
                                } else {
                                    $value = floatval($state['range_' . $condition]) / (1000 * 1000) ?? null;
                                    if($value) {
                                        $query->whereRaw("CAST(JSON_EXTRACT(tags, '$.fov_width') AS DECIMAL(10,10)) $operator ?", [$value]);
                                    }
                                }
                            }
                            return $query;
                        }
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->recordUrl(fn (Asset $record) => AssetResource::getUrl('view', ['record' => $record]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->contentGrid([
                'md' => 3,
                'xl' => 4,
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
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit' => EditAsset::route('/{record}/edit'),
            'view' => ViewAsset::route('/{record}'),
        ];
    }
}
