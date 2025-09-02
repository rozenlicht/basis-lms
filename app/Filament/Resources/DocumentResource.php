<?php

namespace App\Filament\Resources;

use App\Enums\DocumentType;
use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    // order 999
    protected static ?int $navigationSort = 999;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('documentable_type')
                    ->required()
                    ->maxLength(255)
                    ->label('Related Model Type'),

                Forms\Components\TextInput::make('documentable_id')
                    ->required()
                    ->numeric()
                    ->label('Related Model ID'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Document Name')
                    ->placeholder('Enter document name'),

                Forms\Components\Select::make('type')
                    ->required()
                    ->options(DocumentType::options())
                    ->label('Document Type')
                    ->placeholder('Select document type'),

                Forms\Components\Textarea::make('description')
                    ->maxLength(500)
                    ->label('Description')
                    ->placeholder('Enter document description (optional)')
                    ->rows(3),

                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->label('Upload File')
                    ->directory('documents')
                    ->helperText('Upload any file type. No size restrictions.')
                    ->storeFileNamesIn('original_filename'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (DocumentType $state): string => match ($state) {
                        DocumentType::Drawing => 'info',
                        DocumentType::Photo => 'success',
                        DocumentType::Specification => 'warning',
                        DocumentType::Other => 'gray',
                    })
                    ->formatStateUsing(fn (DocumentType $state): string => $state->label()),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('documentable_type')
                    ->label('Related To')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(DocumentType::options())
                    ->label('Document Type'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->url(fn ($record) => $record->view_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => in_array(
                        \Storage::mimeType($record->file_path) ?? '',
                        ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf']
                    )),

                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn ($record) => $record->download_url)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Document')
                    ->modalDescription('Are you sure you want to delete this document? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, delete it'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Documents')
                        ->modalDescription('Are you sure you want to delete the selected documents? This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, delete them'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No documents found')
            ->emptyStateDescription('Create your first document to get started.')
            ->emptyStateIcon('heroicon-o-document-plus');
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
