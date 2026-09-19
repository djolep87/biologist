<?php

namespace App\Livewire;

use App\Models\DnevnaEvidencija;
use App\Support\TeamAccess;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DnevnaEvidencijaTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterMesec = '';

    public string $filterGodina = '';

    public string $filterKarakter = '';

    public string $filterIndeksniBroj = '';

    public string $filterStatus = '';

    public string $sortBy = 'datum';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    public ?int $confirmingDeleteId = null;

    public bool $evidenciarMode = false;

    #[On('zahtevPoslat')]
    public function onZahtevPoslat(): void
    {
        $this->resetPage();
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'filterMesec' => ['except' => ''],
        'filterGodina' => ['except' => ''],
        'filterKarakter' => ['except' => ''],
        'filterIndeksniBroj' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount(bool $evidenciarMode = false): void
    {
        $this->evidenciarMode = $evidenciarMode || TeamAccess::isEvidenciarOnly(auth()->user());

        if ($this->filterGodina === '') {
            $this->filterGodina = (string) now()->year;
        }

        if ($this->filterMesec === '') {
            $this->filterMesec = (string) now()->month;
        }
    }

    #[On('evidencijaUpdated')]
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

    public function updatingFilterMesec(): void
    {
        $this->resetPage();
    }

    public function updatingFilterGodina(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKarakter(): void
    {
        $this->resetPage();
    }

    public function updatingFilterIndeksniBroj(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function selectIndeks(string $indeksniBroj): void
    {
        $this->filterIndeksniBroj = $indeksniBroj;
        $this->resetPage();
    }

    public function clearIndeksFilter(): void
    {
        $this->filterIndeksniBroj = '';
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterMesec = (string) now()->month;
        $this->filterGodina = (string) now()->year;
        $this->filterKarakter = '';
        $this->filterIndeksniBroj = '';
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function openCreate(): void
    {
        if (! TeamAccess::canCreateEvidencija(auth()->user())) {
            $this->dispatch('notify', message: 'Nemate dozvolu za unos evidencije.', type: 'error');

            return;
        }

        $this->dispatch('openCreateForm');
    }

    public function openEdit(int $id): void
    {
        if ($this->evidenciarMode) {
            $evidencija = DnevnaEvidencija::forTeam()->obicna()->findOrFail($id);
            if ($evidencija->user_id !== auth()->id()) {
                $this->dispatch('notify', message: 'Možete menjati samo svoje unose.', type: 'error');

                return;
            }
        }

        $this->dispatch('openEditForm', evidencijaId: $id);
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
    {
        if ($this->evidenciarMode || ! TeamAccess::hasFullTeamAccess(auth()->user())) {
            $this->dispatch('notify', message: 'Nemate dozvolu za brisanje evidencije.', type: 'error');

            return;
        }

        $evidencija = DnevnaEvidencija::forTeam()->obicna()->findOrFail($id);

        $user = auth()->user();
        if ($evidencija->user_id !== $user->id && ! $user->ownsTeam($evidencija->team)) {
            $this->dispatch('notify', message: 'Nemate dozvolu za brisanje ove evidencije.', type: 'error');

            return;
        }

        $teamId = $evidencija->team_id;
        $indeksniBroj = $evidencija->indeksni_broj;

        $evidencija->delete();

        DnevnaEvidencija::recalculateStanjeZaIndeks($teamId, $indeksniBroj);

        $this->confirmingDeleteId = null;
        $this->dispatch('evidencijaUpdated');
        $this->dispatch('notify', message: 'Evidencija je obrisana.', type: 'success');
    }

    protected function filteredQuery()
    {
        return DnevnaEvidencija::forTeam()
            ->obicna()
            ->with(['dokumentKretanja', 'zahtevi'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('naziv_otpada', 'like', '%'.$this->search.'%')
                        ->orWhere('indeksni_broj', 'like', '%'.$this->search.'%')
                        ->orWhere('naziv_primaoca', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterMesec !== '', fn ($q) => $q->where('mesec', (int) $this->filterMesec))
            ->when($this->filterGodina !== '', fn ($q) => $q->where('godina', (int) $this->filterGodina))
            ->when($this->filterKarakter !== '', fn ($q) => $q->where('karakter_otpada', $this->filterKarakter))
            ->when($this->filterIndeksniBroj !== '', fn ($q) => $q->where('indeksni_broj', $this->filterIndeksniBroj))
            ->when($this->filterStatus === 'ceka', fn ($q) => $q->where('predat_operateru', false))
            ->when($this->filterStatus === 'predato', fn ($q) => $q->where('predat_operateru', true));
    }

    /**
     * Upit za aktivnost u periodu (proizvedeno, predato, broj unosa).
     */
    protected function periodActivityQuery()
    {
        return DnevnaEvidencija::forTeam()
            ->obicna()
            ->when($this->filterGodina !== '', fn ($q) => $q->where('godina', (int) $this->filterGodina))
            ->when($this->filterMesec !== '', fn ($q) => $q->where('mesec', (int) $this->filterMesec))
            ->when($this->filterIndeksniBroj !== '', fn ($q) => $q->where('indeksni_broj', $this->filterIndeksniBroj));
    }

    /**
     * Upit za snapshot skladišta na kraj perioda (YTD ako je izabran mesec).
     */
    protected function periodSkladisteQuery()
    {
        return DnevnaEvidencija::forTeam()
            ->obicna()
            ->when($this->filterGodina !== '', fn ($q) => $q->where('godina', (int) $this->filterGodina))
            ->when($this->filterMesec !== '', fn ($q) => $q->where('mesec', '<=', (int) $this->filterMesec))
            ->when($this->filterIndeksniBroj !== '', fn ($q) => $q->where('indeksni_broj', $this->filterIndeksniBroj));
    }

    public function getIndeksPregledProperty()
    {
        $godina = $this->filterGodina !== '' ? (int) $this->filterGodina : null;
        $mesec = $this->filterMesec !== '' ? (int) $this->filterMesec : null;

        return DnevnaEvidencija::summariesByIndeks(null, $godina, $mesec);
    }

    public function getEvidencijeProperty()
    {
        return $this->filteredQuery()
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function getTotalsProperty(): array
    {
        return DnevnaEvidencija::calculatePeriodTotals(
            $this->periodActivityQuery(),
            $this->periodSkladisteQuery(),
        );
    }

    public function getPeriodLabelProperty(): string
    {
        $meseci = [
            1 => 'januar', 2 => 'februar', 3 => 'mart', 4 => 'april',
            5 => 'maj', 6 => 'jun', 7 => 'jul', 8 => 'avgust',
            9 => 'septembar', 10 => 'oktobar', 11 => 'novembar', 12 => 'decembar',
        ];

        if ($this->filterGodina !== '' && $this->filterMesec !== '') {
            $mesec = (int) $this->filterMesec;

            return ($meseci[$mesec] ?? $mesec).'. '.$this->filterGodina.'.';
        }

        if ($this->filterGodina !== '') {
            return 'Godina '.$this->filterGodina.'.';
        }

        return 'Svi periodi';
    }

    public function render()
    {
        return view('livewire.dnevna-evidencija-table', [
            'evidencije' => $this->evidencije,
            'totals' => $this->totals,
            'indeksPregled' => $this->indeksPregled,
            'periodLabel' => $this->periodLabel,
            'evidenciarMode' => $this->evidenciarMode,
        ]);
    }
}
