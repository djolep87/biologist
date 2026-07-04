<?php

namespace App\Livewire\Admin;

use App\Models\DnevnaEvidencija;
use App\Support\AdminTeamContext;
use Livewire\Attributes\On;
use Livewire\Component;

class AdminKlijentRad extends Component
{
    public string $activeTab = 'evidencije';

    public int $filterGodina;

    public int $filterMesec;

    public string $filterIndeks = '';

    public function mount(): void
    {
        $this->filterGodina = now()->year;
        $this->filterMesec = now()->month;
    }

    #[On('admin-team-changed')]
    public function onTeamChanged(): void
    {
        $this->activeTab = 'evidencije';
    }

    #[On('dokumentKreiran')]
    #[On('evidencijaUpdated')]
    public function refresh(): void
    {
        //
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['evidencije', 'dnevna', 'dokumenti'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function getSelectedTeamIdProperty(): ?int
    {
        return AdminTeamContext::selectedTeamId();
    }

    public function getCekaPredajuCountProperty(): int
    {
        $teamId = $this->selectedTeamId;

        if (! $teamId) {
            return 0;
        }

        return DnevnaEvidencija::forTeam($teamId)
            ->where('predat_operateru', false)
            ->where('stanje_na_skladistu', '>', 0)
            ->count();
    }

    public function getIndeksOptionsProperty()
    {
        $teamId = $this->selectedTeamId;

        if (! $teamId) {
            return collect();
        }

        return DnevnaEvidencija::forTeam($teamId)
            ->where('predat_operateru', false)
            ->where('stanje_na_skladistu', '>', 0)
            ->select('indeksni_broj')
            ->distinct()
            ->orderBy('indeksni_broj')
            ->pluck('indeksni_broj');
    }

    public function render()
    {
        return view('livewire.admin.admin-klijent-rad', [
            'selectedTeam' => AdminTeamContext::selectedTeam(),
            'cekaPredajuCount' => $this->cekaPredajuCount,
            'indeksOptions' => $this->indeksOptions,
        ]);
    }
}
