<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Contracts\UpdatesTeamNames;
use Livewire\Component;

class UpdateTeamCompanyForm extends Component
{
    public Team $team;

    /** @var array<string, mixed> */
    public array $state = [];

    public function mount(Team $team): void
    {
        $this->team = $team;
        $this->state = [
            'name' => $team->name,
            'pib' => $team->pib ?? '',
            'maticni_broj' => $team->maticni_broj ?? '',
            'adresa' => $team->adresa ?? '',
            'grad' => $team->grad ?? '',
            'opstina' => $team->opstina ?? '',
            'postanski_broj' => $team->postanski_broj ?? '',
            'tip_subjekta' => $team->tip_subjekta ?? 'DEO1',
            'delatnost' => $team->delatnost ?? '',
            'kontakt_telefon' => $team->kontakt_telefon ?? '',
            'email' => $team->email ?? '',
            'faks' => $team->faks ?? '',
            'kontakt_osoba' => $team->kontakt_osoba ?? '',
            'dozvola_broj' => $team->dozvola_broj ?? '',
            'dozvola_datum_izdavanja' => $team->dozvola_datum_izdavanja?->format('Y-m-d') ?? '',
            'dozvola_vazi_do' => $team->dozvola_vazi_do?->format('Y-m-d') ?? '',
        ];
    }

    public function updateTeamCompany(UpdatesTeamNames $updater): void
    {
        $this->resetErrorBag();
        $updater->update(Auth::user(), $this->team, $this->state);
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('teams.update-team-company-form');
    }
}
