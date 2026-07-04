<?php

namespace App\Livewire\Teams;

use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\RedirectsActions;
use Livewire\Component;

class CreateTeamForm extends Component
{
    use RedirectsActions;

    /**
     * The component's state.
     *
     * @var array<string, mixed>
     */
    public $state = [];

    public function mount(): void
    {
        $this->state = [
            'name' => '',
            'pib' => '',
            'maticni_broj' => '',
            'adresa' => '',
            'grad' => '',
            'postanski_broj' => '',
            'tip_subjekta' => 'DEO1',
            'delatnost' => '',
            'kontakt_telefon' => '',
            'opstina' => '',
            'email' => '',
            'faks' => '',
            'kontakt_osoba' => '',
            'dozvola_broj' => '',
            'dozvola_datum_izdavanja' => '',
            'dozvola_vazi_do' => '',
        ];
    }

    /**
     * Create a new team (firma).
     */
    public function createTeam(CreatesTeams $creator): mixed
    {
        $this->resetErrorBag();

        $creator->create(Auth::user(), $this->state);

        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function getUserProperty(): mixed
    {
        return Auth::user();
    }

    public function render()
    {
        return view('teams.create-team-form');
    }
}
