<?php

namespace App\Livewire\Admin;

use App\Models\DnevnaEvidencija;
use App\Support\AdminTeamContext;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AdminEvidencijePregled extends Component
{
    use WithPagination;

    public int $filterGodina;

    public string $filterMesec = '';

    public string $filterIndeks = '';

    public string $filterStatus = '';

    public string $search = '';

    public function mount(): void
    {
        $this->filterGodina = now()->year;
    }

    #[On('admin-team-changed')]
    public function onTeamChanged(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterGodina(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMesec(): void
    {
        $this->resetPage();
    }

    public function updatingFilterIndeks(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function getTeamIdProperty(): ?int
    {
        return AdminTeamContext::selectedTeamId();
    }

    public function getIndeksOptionsProperty()
    {
        if (! $this->teamId) {
            return collect();
        }

        return DnevnaEvidencija::forTeam($this->teamId)
            ->select('indeksni_broj')
            ->distinct()
            ->orderBy('indeksni_broj')
            ->pluck('indeksni_broj');
    }

    public function getEvidencijeProperty()
    {
        if (! $this->teamId) {
            return DnevnaEvidencija::query()->whereRaw('0=1')->paginate(15);
        }

        return DnevnaEvidencija::forTeam($this->teamId)
            ->with('dokumentKretanja')
            ->when($this->filterGodina, fn ($q) => $q->where('godina', $this->filterGodina))
            ->when($this->filterMesec !== '', fn ($q) => $q->where('mesec', (int) $this->filterMesec))
            ->when($this->filterIndeks !== '', fn ($q) => $q->where('indeksni_broj', $this->filterIndeks))
            ->when($this->filterStatus === 'predato', fn ($q) => $q->where('predat_operateru', true))
            ->when($this->filterStatus === 'ceka', fn ($q) => $q->where('predat_operateru', false))
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->where('indeksni_broj', 'like', '%'.$this->search.'%')
                    ->orWhere('naziv_otpada', 'like', '%'.$this->search.'%');
            }))
            ->orderByDesc('datum')
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.admin-evidencije-pregled', [
            'evidencije' => $this->evidencije,
            'indeksOptions' => $this->indeksOptions,
            'teamId' => $this->teamId,
        ]);
    }
}
