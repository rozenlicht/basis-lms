<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\Sample;
use Filament\Infolists\Components\Actions\Action;

class ContainerContentEntry extends Entry
{
    protected string $view = 'infolists.components.container-content-entry';

    public function EditSample(int $sampleId): void
    {
        $sample = Sample::find($sampleId);
        if ($sample) {
            // Redirect to the sample edit page or perform any other action
            $this->redirect(SampleResource::getUrl('edit', [
                'record' => $sampleId,
            ]));
        }
    }
}
