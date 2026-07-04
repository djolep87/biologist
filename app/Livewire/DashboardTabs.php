<?php

namespace App\Livewire;

use App\Models\DnevnaEvidencija;
use App\Models\ZahtevPredaje;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardTabs extends Component
{
    public string $activeTab = 'evidencija';

    public function mount(): void
    {
        if (request('tab') === 'zahtevi') {
            $this->activeTab = 'zahtevi';
        }
        if (request('tab') === 'gio1') {
            $this->activeTab = 'gio1';
        }
    }

    #[On('setTab')]
    public function setTab(string $tab): void
    {
        if (in_array($tab, ['evidencija', 'dokumenti', 'zahtevi', 'gio1'], true)) {
            $this->activeTab = $tab;
        }
    }

    #[On('zahtevPoslat')]
    public function onZahtevPoslat(): void
    {
        $this->activeTab = 'zahtevi';
    }

    public function getAktivniZahteviCountProperty(): int
    {
        $teamId = auth()->user()->currentTeam?->id;

        if (! $teamId) {
            return 0;
        }

        return ZahtevPredaje::forTeam($teamId)
            ->whereIn('status', ['na_cekanju', 'u_obradi'])
            ->count();
    }

    public function getCekaPredajuCountProperty(): int
    {
        $teamId = auth()->user()->currentTeam?->id;

        if (! $teamId) {
            return 0;
        }

        return DnevnaEvidencija::forTeam($teamId)
            ->where('predat_operateru', false)
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard-tabs', [
            'cekaPredajuCount' => $this->cekaPredajuCount,
            'aktivniZahteviCount' => $this->aktivniZahteviCount,
        ]);
    }
}
