<?php

namespace App\Livewire;

use App\Models\DnevnaEvidencija;
use App\Models\User;
use App\Models\ZahtevPredaje;
use App\Notifications\NoviZahtevPredaje;
use App\Support\TeamAccess;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class PosaljiZahtevPredaje extends Component
{
    public bool $showModal = false;

    public int $step = 1;

    public array $izabraniIds = [];

    public string $filterIndeksni = '';

    public string $napomenaKlijenta = '';

    public ?string $zakljucaniIndeksni = null;

    #[On('openZahtevModal')]
    public function openModal(): void
    {
        if (! TeamAccess::hasFullTeamAccess(auth()->user())) {
            $this->dispatch('notify', message: 'Nemate dozvolu za slanje zahteva.', type: 'error');

            return;
        }

        $this->reset(['step', 'izabraniIds', 'filterIndeksni', 'napomenaKlijenta', 'zakljucaniIndeksni']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function nextStep(): void
    {
        if (empty($this->izabraniIds)) {
            $this->addError('izbor', 'Izaberite bar jedan izveštaj.');

            return;
        }

        $this->step = 2;
    }

    public function prevStep(): void
    {
        $this->step = 1;
    }

    public function toggleIzbor(int $id, string $indeksni): void
    {
        if (in_array($id, $this->izabraniIds, true)) {
            $this->izabraniIds = array_values(array_filter(
                $this->izabraniIds,
                fn ($i) => $i !== $id
            ));

            if (empty($this->izabraniIds)) {
                $this->zakljucaniIndeksni = null;
            }
        } else {
            if ($this->zakljucaniIndeksni && $this->zakljucaniIndeksni !== $indeksni) {
                $this->addError('izbor', 'Možete birati izveštaje samo jednog indeksnog broja.');

                return;
            }

            $this->zakljucaniIndeksni = $indeksni;
            $this->izabraniIds[] = $id;
        }

        $this->resetErrorBag('izbor');
    }

    public function ukupnaMasa(): float
    {
        if (empty($this->izabraniIds)) {
            return 0;
        }

        return (float) DnevnaEvidencija::whereIn('id', $this->izabraniIds)
            ->sum('stanje_na_skladistu');
    }

    public function posaljiZahtev(): void
    {
        if (empty($this->izabraniIds)) {
            $this->addError('izbor', 'Izaberite bar jedan izveštaj.');

            return;
        }

        $teamId = auth()->user()->currentTeam?->id;

        if (! $teamId) {
            $this->addError('izbor', 'Niste povezani sa firmom.');

            return;
        }

        $evidencije = DnevnaEvidencija::forTeam($teamId)
            ->whereIn('id', $this->izabraniIds)
            ->where('predat_operateru', false)
            ->whereDoesntHave('zahtevi', fn ($q) => $q->whereIn('status', ['na_cekanju', 'u_obradi', 'odbijeno']))
            ->get();

        if ($evidencije->count() !== count($this->izabraniIds)) {
            $this->addError('izbor', 'Neki izveštaji više nisu dostupni za predaju.');

            return;
        }

        $indeksi = $evidencije->pluck('indeksni_broj')->unique();
        if ($indeksi->count() !== 1) {
            $this->addError('izbor', 'Svi izveštaji moraju biti istog indeksnog broja.');

            return;
        }

        $evidencija = $evidencije->first();

        $zahtev = ZahtevPredaje::create([
            'team_id' => $teamId,
            'user_id' => auth()->id(),
            'status' => 'na_cekanju',
            'indeksni_broj' => $evidencija->indeksni_broj,
            'naziv_otpada' => $evidencija->naziv_otpada,
            'masa_ukupno' => $this->ukupnaMasa(),
            'napomena_klijenta' => $this->napomenaKlijenta ?: null,
        ]);

        $zahtev->evidencije()->attach($this->izabraniIds);

        User::where('is_super_admin', true)->each(
            fn (User $admin) => $admin->notify(new NoviZahtevPredaje($zahtev))
        );

        $this->showModal = false;
        $this->reset(['step', 'izabraniIds', 'filterIndeksni', 'napomenaKlijenta', 'zakljucaniIndeksni']);
        $this->dispatch('zahtevPoslat');
        $this->dispatch('notify', message: 'Zahtev je poslat. Administrator će vas kontaktirati.', type: 'success');
    }

    public function getEvidencijeProperty(): Collection
    {
        return DnevnaEvidencija::forTeam()
            ->where('predat_operateru', false)
            ->whereDoesntHave('zahtevi', fn ($q) => $q->whereIn('status', ['na_cekanju', 'u_obradi', 'odbijeno']))
            ->when($this->filterIndeksni, fn ($q) => $q->where('indeksni_broj', $this->filterIndeksni))
            ->when($this->zakljucaniIndeksni, fn ($q) => $q->where('indeksni_broj', $this->zakljucaniIndeksni))
            ->orderBy('datum', 'desc')
            ->get();
    }

    public function getIndeksniBrojeviProperty(): Collection
    {
        return DnevnaEvidencija::forTeam()
            ->where('predat_operateru', false)
            ->whereDoesntHave('zahtevi', fn ($q) => $q->whereIn('status', ['na_cekanju', 'u_obradi', 'odbijeno']))
            ->select('indeksni_broj', 'naziv_otpada')
            ->distinct()
            ->orderBy('indeksni_broj')
            ->get();
    }

    public function render()
    {
        return view('livewire.posalji-zahtev-predaje', [
            'evidencije' => $this->evidencije,
            'indeksniBrojevi' => $this->indeksniBrojevi,
            'ukupnaMasa' => $this->ukupnaMasa(),
            'brojIzabranih' => count($this->izabraniIds),
        ]);
    }
}
