<?php

namespace App\Filament\Resources\ContainerResource\Pages;

use App\Filament\Resources\ContainerResource;
use App\Filament\Resources\SampleResource;
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
            Actions\Action::make('connectSample')
                ->label('Register Sample')
                ->icon('heroicon-o-plus')
                ->form([
                    \Filament\Forms\Components\Select::make('sample_id')
                        ->label('Select Sample')
                        ->options(\App\Models\Sample::whereNull('container_id')->pluck('unique_ref', 'id'))
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('x'),
                    \Filament\Forms\Components\TextInput::make('y'),
                ])
                ->action(function (array $data): void {
                    $sample = \App\Models\Sample::find($data['sample_id']);
                    if ($sample) {
                        $sample->update([
                            'container_id' => $this->record->id,
                            'compartment_x' => $data['x'],
                            'compartment_y' => $data['y']
                        ]);
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

    public function deleteSample(int $sampleId)
    {
        $sample = \App\Models\Sample::find($sampleId);
        if ($sample) {
            $sample->update(['container_id' => null, 'compartment_x' => null, 'compartment_y' => null]);
            return redirect(ContainerResource::getUrl('view', [
                'record' => $this->record->id,
            ]))->with('success', 'Sample removed from container');
        }
    }

    public function gotoSample(int $sampleId): void
    {
        $sample = \App\Models\Sample::find($sampleId);
        if ($sample) {
            // Redirect to the sample edit page or perform any other action
            $this->redirect(SampleResource::getUrl('edit', [
                'record' => $sampleId,
            ]));
        }
    }

    public function openConnectSampleModal(int $x, int $y): void
    {
        $this->mountAction('connectSample', ['x' => $x, 'y' => $y]);
    }
}
