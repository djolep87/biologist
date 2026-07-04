<?php

namespace App\Livewire\Admin;

use App\Models\DnevnaEvidencija;
use App\Models\Team;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class AdminEvidencijeTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterFirma = '';

    public string $filterGodina = '';

    public string $filterMesec = '';

    public string $filterIndeks = '';

    public string $filterKarakter = '';

    public string $filterStatus = '';

    public string $sortBy = 'datum';

    public string $sortDir = 'desc';

    public int $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterFirma' => ['except' => ''],
        'filterGodina' => ['except' => ''],
        'filterMesec' => ['except' => ''],
        'filterIndeks' => ['except' => ''],
        'filterKarakter' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterFirma(): void
    {
        $this->filterIndeks = '';
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

    public function updatingFilterKarakter(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
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

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search', 'filterFirma', 'filterGodina', 'filterMesec',
            'filterIndeks', 'filterKarakter', 'filterStatus',
        ]);
        $this->resetPage();
    }

    public function getFirmeOptionsProperty(): Collection
    {
        return Team::orderBy('name')->pluck('name', 'id');
    }

    public function getGodinaOptionsProperty(): Collection
    {
        $godine = DnevnaEvidencija::query()
            ->select('godina')
            ->distinct()
            ->orderByDesc('godina')
            ->pluck('godina');

        if ($godine->isEmpty()) {
            return collect([now()->year]);
        }

        return $godine;
    }

    public function getIndeksOptionsProperty(): Collection
    {
        return DnevnaEvidencija::query()
            ->when($this->filterFirma !== '', fn ($q) => $q->where('team_id', (int) $this->filterFirma))
            ->select('indeksni_broj')
            ->distinct()
            ->orderBy('indeksni_broj')
            ->pluck('indeksni_broj');
    }

    protected function baseQuery()
    {
        return DnevnaEvidencija::query()
            ->when($this->filterFirma !== '', fn ($q) => $q->where('team_id', (int) $this->filterFirma))
            ->when($this->filterGodina !== '', fn ($q) => $q->where('godina', (int) $this->filterGodina))
            ->when($this->filterMesec !== '', fn ($q) => $q->where('mesec', (int) $this->filterMesec))
            ->when($this->filterIndeks !== '', fn ($q) => $q->where('indeksni_broj', $this->filterIndeks))
            ->when($this->filterKarakter !== '', fn ($q) => $q->where('karakter_otpada', $this->filterKarakter))
            ->when($this->filterStatus === 'predato', fn ($q) => $q->where('predat_operateru', true))
            ->when($this->filterStatus === 'ceka', fn ($q) => $q->where('predat_operateru', false))
            ->when($this->search !== '', fn ($q) => $q->where(function ($q) {
                $q->where('naziv_otpada', 'like', '%'.$this->search.'%')
                    ->orWhere('indeksni_broj', 'like', '%'.$this->search.'%')
                    ->orWhere('naziv_primaoca', 'like', '%'.$this->search.'%')
                    ->orWhereHas('team', fn ($t) => $t->where('name', 'like', '%'.$this->search.'%'));
            }));
    }

    public function getEvidencijeProperty()
    {
        $sortable = ['datum', 'godina', 'indeksni_broj', 'naziv_otpada', 'karakter_otpada', 'proizvedena_kolicina', 'predata_kolicina', 'stanje_na_skladistu'];
        $sortColumn = in_array($this->sortBy, $sortable, true) ? $this->sortBy : 'datum';

        return $this->baseQuery()
            ->with(['team:id,name', 'user:id,name', 'dokumentKretanja'])
            ->orderBy($sortColumn, $this->sortDir === 'asc' ? 'asc' : 'desc')
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    public function getTotalsProperty(): array
    {
        return [
            'broj_unosa' => (int) (clone $this->baseQuery())->count(),
            'proizvedena' => (float) (clone $this->baseQuery())->sum('proizvedena_kolicina'),
            'predata' => (float) (clone $this->baseQuery())->where('predat_operateru', true)->sum('predata_kolicina'),
        ];
    }

    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->filterFirma !== ''
            || $this->filterGodina !== ''
            || $this->filterMesec !== ''
            || $this->filterIndeks !== ''
            || $this->filterKarakter !== ''
            || $this->filterStatus !== '';
    }

    public function render()
    {
        return view('livewire.admin.admin-evidencije-table', [
            'evidencije' => $this->evidencije,
            'totals' => $this->totals,
            'firmeOptions' => $this->firmeOptions,
            'godinaOptions' => $this->godinaOptions,
            'indeksOptions' => $this->indeksOptions,
        ]);
    }
}
