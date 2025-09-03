<?php

namespace App\Filament\Resources\SourceMaterialResource\RelationManagers;

use App\Enums\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('view_url')
                    ->label('Preview')
                    ->size(60)
                    ->height(120)
                    ->width(120)
                    ->square()
                    ->url(fn ($record) => $record->view_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record ? in_array(
                        Storage::mimeType($record->file_path) ?? '',
                        ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
                    ) : false),

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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload Document')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->url(fn ($record) => $record->view_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => in_array(
                        Storage::mimeType($record->file_path) ?? '',
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
            ->emptyStateHeading('No documents uploaded')
            ->emptyStateDescription('Upload your first document to get started.')
            ->emptyStateIcon('heroicon-o-document-plus');
    }
}
