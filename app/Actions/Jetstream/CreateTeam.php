<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Events\AddingTeam;
use Laravel\Jetstream\Jetstream;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team (firma) for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(User $user, array $input): Team
    {
        Gate::forUser($user)->authorize('create', Jetstream::newTeamModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'pib' => ['required', 'digits:9'],
            'maticni_broj' => ['required', 'digits:8'],
            'adresa' => ['required', 'string', 'max:255'],
            'grad' => ['required', 'string', 'max:255'],
            'postanski_broj' => ['required', 'digits:5'],
            'tip_subjekta' => ['required', 'in:DEO1,DEO2,DEO3,DEO4,DEO5,DEO6'],
            'delatnost' => ['nullable', 'string', 'max:4'],
            'kontakt_telefon' => ['nullable', 'string', 'max:20'],
            'opstina' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'faks' => ['nullable', 'string', 'max:20'],
            'kontakt_osoba' => ['nullable', 'string', 'max:255'],
            'dozvola_broj' => ['nullable', 'string', 'max:255'],
            'dozvola_datum_izdavanja' => ['nullable', 'date'],
            'dozvola_vazi_do' => ['nullable', 'date'],
        ], [
            'name.required' => 'Polje je obavezno',
            'pib.required' => 'Polje je obavezno',
            'pib.digits' => 'PIB mora imati tačno 9 cifara',
            'maticni_broj.required' => 'Polje je obavezno',
            'maticni_broj.digits' => 'Matični broj mora imati tačno 8 cifara',
            'adresa.required' => 'Polje je obavezno',
            'grad.required' => 'Polje je obavezno',
            'postanski_broj.required' => 'Polje je obavezno',
            'postanski_broj.digits' => 'Poštanski broj mora imati 5 cifara',
            'tip_subjekta.required' => 'Polje je obavezno',
        ])->validateWithBag('createTeam');

        AddingTeam::dispatch($user);

        $user->switchTeam($team = $user->ownedTeams()->create([
            'name' => $input['name'],
            'personal_team' => false,
            'pib' => $input['pib'],
            'maticni_broj' => $input['maticni_broj'],
            'adresa' => $input['adresa'],
            'grad' => $input['grad'],
            'postanski_broj' => $input['postanski_broj'],
            'tip_subjekta' => $input['tip_subjekta'],
            'delatnost' => $input['delatnost'] ?? null,
            'kontakt_telefon' => $input['kontakt_telefon'] ?? null,
            'opstina' => $input['opstina'] ?? null,
            'email' => $input['email'] ?? null,
            'faks' => $input['faks'] ?? null,
            'kontakt_osoba' => $input['kontakt_osoba'] ?? null,
            'dozvola_broj' => $input['dozvola_broj'] ?? null,
            'dozvola_datum_izdavanja' => $input['dozvola_datum_izdavanja'] ?? null,
            'dozvola_vazi_do' => $input['dozvola_vazi_do'] ?? null,
        ]));

        return $team;
    }
}
