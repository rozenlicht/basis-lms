<?php

namespace App\Filament\Resources\ContainerResource\Pages;

use App\Filament\Resources\ContainerResource;
use App\Filament\Resources\SampleResource;
use App\Models\ContainerPosition;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewContainer extends Page
{
    use InteractsWithRecord;
   
    protected static string $resource = ContainerResource::class;

    protected static string $view = 'filament.pages.view-container';

    protected static ?string $title = 'View Container';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addPosition')
                ->label('Add Position')
                ->icon('heroicon-o-plus')
                ->form([
                    \Filament\Forms\Components\TextInput::make('x')
                        ->label('X Position')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    \Filament\Forms\Components\TextInput::make('y')
                        ->label('Y Position')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                    \Filament\Forms\Components\Radio::make('position_type')
                        ->label('Position Type')
                        ->options([
                            'sample' => 'Sample',
                            'custom' => 'Custom Name',
                        ])
                        ->default('sample')
                        ->required()
                        ->reactive(),
                    \Filament\Forms\Components\Select::make('sample_id')
                        ->label('Sample')
                        ->options(function() {
                            $assignedSampleIds = ContainerPosition::whereNotNull('sample_id')->pluck('sample_id');
                            return \App\Models\Sample::query()
                                ->whereNotIn('id', $assignedSampleIds)
                                ->pluck('unique_ref', 'id');
                        })
                        ->searchable()
                        ->placeholder('Select Sample')
                        ->visible(fn($get) => $get('position_type') === 'sample'),
                    \Filament\Forms\Components\TextInput::make('custom_name')
                        ->label('Custom Name')
                        ->placeholder('Enter custom name')
                        ->visible(fn($get) => $get('position_type') === 'custom'),
                ])
                ->action(function (array $data): void {
                    if ($data['position_type'] === 'sample' && $data['sample_id']) {
                        $this->record->setPositionSample($data['x'], $data['y'], $data['sample_id']);
                    } elseif ($data['position_type'] === 'custom' && $data['custom_name']) {
                        $this->record->setPositionCustomName($data['x'], $data['y'], $data['custom_name']);
                    }
                }),
            Actions\Action::make('printQR')
                ->label('Print QR Code')
                ->icon('heroicon-o-printer')
                ->action(fn () => redirect()->route('qr-code.show', ['containerId' => $this->record->id])),
            Actions\Action::make('edit')
                ->label('Edit Container')
                ->icon('heroicon-o-pencil')
                ->action(fn () => redirect(ContainerResource::getUrl('edit', [
                    'record' => $this->record->id,
                ]))),
            Actions\DeleteAction::make()
        ];
    }

    public function deletePosition(int $x, int $y)
    {
        $this->record->clearPosition($x, $y);
        return redirect(ContainerResource::getUrl('view', [
            'record' => $this->record->id,
        ]))->with('success', 'Position cleared');
    }

    public function gotoSample(int $sampleId): void
    {
        $sample = \App\Models\Sample::find($sampleId);
        if ($sample) {
            // Redirect to the sample edit page or perform any other action
            $this->redirect(SampleResource::getUrl('view', [
                'record' => $sampleId,
            ]));
        }
    }

    public function openAddPositionModal(int $x, int $y): void
    {
        $this->mountAction('addPosition', ['x' => $x, 'y' => $y]);
    }
}
