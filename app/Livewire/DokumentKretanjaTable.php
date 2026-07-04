<?php

namespace App\Livewire;

use App\Actions\Admin\DeleteDokumentKretanja;
use App\Models\DokumentKretanja;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DokumentKretanjaTable extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $teamId = null;

    public bool $allowDelete = false;

    public bool $showAllTeams = false;

    public ?int $pregledDokumentId = null;

    public ?int $confirmingDeleteId = null;

    #[On('dokumentKreiran')]
    public function refreshList(): void
    {
        $this->resetPage();
        $this->confirmingDeleteId = null;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openPregled(int $id): void
    {
        $this->pregledDokumentId = $id;
    }

    public function closePregled(): void
    {
        $this->pregledDokumentId = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id, DeleteDokumentKretanja $deleter): void
    {
        abort_unless(auth()->user()?->is_super_admin && $this->allowDelete, 403);

        $dokument = $this->dokumentQuery()->findOrFail($id);

        $deleter->delete($dokument);

        $this->confirmingDeleteId = null;
        $this->dispatch('evidencijaUpdated');
        $this->dispatch('notify', message: 'DOKO dokument je obrisan. Povezani izveštaji su ponovo dostupni.', type: 'success');
    }

    protected function dokumentQuery()
    {
        if ($this->showAllTeams && auth()->user()?->is_super_admin) {
            return DokumentKretanja::query();
        }

        return DokumentKretanja::forTeam($this->teamId);
    }

    public function getDokumentiProperty()
    {
        return $this->dokumentQuery()
            ->when($this->showAllTeams, fn ($q) => $q->with('team'))
            ->withCount('dnevneEvidencije')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('broj_dokumenta', 'like', '%'.$this->search.'%')
                        ->orWhere('indeksni_broj', 'like', '%'.$this->search.'%')
                        ->orWhere('vrsta_otpada', 'like', '%'.$this->search.'%')
                        ->orWhere('primalac_naziv', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function getPregledDokumentProperty(): ?DokumentKretanja
    {
        if (! $this->pregledDokumentId) {
            return null;
        }

        return $this->dokumentQuery()
            ->with(['dnevneEvidencije' => fn ($q) => $q->orderBy('datum')])
            ->find($this->pregledDokumentId);
    }

    public function render()
    {
        return view('livewire.dokument-kretanja-table', [
            'dokumenti' => $this->dokumenti,
            'pregledDokument' => $this->pregledDokument,
        ]);
    }
}
