<?php

namespace App\Livewire\Mobile;

use App\Models\Sample;
use App\Models\SourceMaterial;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AppShell extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $activeTab = 'source-materials';

    public string $search = '';

    public ?string $selectedType = null;

    public ?int $selectedId = null;

    public string $detailTab = 'history';

    public $photoUpload = null;

    public string $noteContent = '';

    protected array $queryString = [
        'activeTab' => ['except' => 'source-materials'],
        'search' => ['except' => ''],
    ];

    protected array $rules = [
        'photoUpload' => 'nullable|image|max:5120',
        'noteContent' => 'nullable|string|max:2000',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage('sourcePage');
        $this->resetPage('samplePage');
        $this->resetSelection();
    }

    public function switchTab(string $tab): void
    {
        if (! in_array($tab, ['source-materials', 'samples'], true)) {
            return;
        }

        $this->activeTab = $tab;
        $this->resetPage('sourcePage');
        $this->resetPage('samplePage');
        $this->resetSelection();
    }

    public function selectRecord(string $type, int $id): void
    {
        if (! in_array($type, ['source-material', 'sample'], true)) {
            return;
        }

        $this->selectedType = $type;
        $this->selectedId = $id;
        $this->detailTab = 'history';
        $this->photoUpload = null;
        $this->noteContent = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function resetSelection(): void
    {
        $this->selectedType = null;
        $this->selectedId = null;
        $this->detailTab = 'history';
        $this->photoUpload = null;
        $this->noteContent = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function setDetailTab(string $tab): void
    {
        if (! in_array($tab, ['history', 'data', 'samples', 'notes'], true)) {
            return;
        }

        $this->detailTab = $tab;
    }

    public function updatedPhotoUpload(): void
    {
        $this->validateOnly('photoUpload');
        $this->storePhoto();
    }

    public function saveNote(): void
    {
        if (! $this->selectedType || ! $this->selectedId) {
            return;
        }

        $this->validate([
            'noteContent' => 'required|string|max:2000',
        ]);

        $record = $this->selectedType === 'source-material'
            ? $this->selectedSourceMaterial
            : $this->selectedSample;

        if (! $record) {
            return;
        }

        $record->notes()->create([
            'content' => $this->noteContent,
            'author_id' => Auth::id(),
        ]);

        $this->noteContent = '';
    }

    protected function storePhoto(): void
    {
        if (! $this->selectedType || ! $this->selectedId || ! $this->photoUpload) {
            return;
        }

        $record = $this->selectedType === 'source-material'
            ? $this->selectedSourceMaterial
            : $this->selectedSample;

        if (! $record) {
            return;
        }

        $path = $this->photoUpload->store('photos', 'public');

        $record->photos()->create([
            'user_id' => Auth::id(),
            'file_path' => $path,
        ]);

        $this->photoUpload = null;
    }

    protected function searchTerm(): string
    {
        return trim($this->search);
    }

    public function getSourceMaterialsProperty()
    {
        $term = $this->searchTerm();

        return SourceMaterial::query()
            ->with(['latestPhoto'])
            ->withCount('samples')
            ->when(
                $term !== '',
                fn (Builder $query) => $query->where(function (Builder $innerQuery) use ($term) {
                    $innerQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('unique_ref', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('supplier', 'like', "%{$term}%")
                        ->orWhere('supplier_identifier', 'like', "%{$term}%")
                        ->orWhere('grade', 'like', "%{$term}%");
                })
            )
            ->orderByDesc('updated_at')
            ->orderBy('name')
            ->paginate(perPage: 20, pageName: 'sourcePage');
    }

    public function getSamplesProperty()
    {
        $term = $this->searchTerm();

        return Sample::query()
            ->with(['sourceMaterial', 'latestPhoto'])
            ->when(
                $term !== '',
                fn (Builder $query) => $query->where(function (Builder $innerQuery) use ($term) {
                    $innerQuery
                        ->where('unique_ref', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhereHas('sourceMaterial', function (Builder $relationQuery) use ($term) {
                            $relationQuery
                                ->where('name', 'like', "%{$term}%")
                                ->orWhere('unique_ref', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                })
            )
            ->orderByDesc('updated_at')
            ->orderBy('unique_ref')
            ->paginate(perPage: 20, pageName: 'samplePage');
    }

    public function getSelectedSourceMaterialProperty(): ?SourceMaterial
    {
        if ($this->selectedType !== 'source-material' || ! $this->selectedId) {
            return null;
        }

        return SourceMaterial::query()
            ->with([
                'samples.latestPhoto',
                'samples.container',
                'processingSteps' => fn ($query) => $query->orderBy('created_at'),
                'notes.author',
                'photos',
            ])
            ->find($this->selectedId);
    }

    public function getSelectedSampleProperty(): ?Sample
    {
        if ($this->selectedType !== 'sample' || ! $this->selectedId) {
            return null;
        }

        return Sample::query()
            ->with([
                'sourceMaterial.processingSteps' => fn ($query) => $query->orderBy('created_at'),
                'sourceMaterial.samples.latestPhoto',
                'processingSteps' => fn ($query) => $query->orderBy('created_at'),
                'notes.author',
                'photos',
            ])
            ->find($this->selectedId);
    }

    public function render(): View
    {
        return view('livewire.mobile.app-shell');
    }
}

?>

