<?php

namespace App\Actions\Jetstream;

use App\Models\Team;
use App\Models\User;
use App\Support\TeamAccess;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\AddingTeamMember;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class CreateTeamMember
{
    /**
     * @param  array{name: string, email: string, role?: string|null, password?: string|null}  $input
     * @return array{user: User, generated_password: ?string, was_new_user: bool}
     */
    public function create(User $inviter, Team $team, array $input): array
    {
        Gate::forUser($inviter)->authorize('addTeamMember', $team);

        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => Jetstream::hasRoles()
                ? ['required', 'string', new Role]
                : ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
        ], [
            'name.required' => 'Ime i prezime je obavezno.',
            'email.required' => 'Email adresa je obavezna.',
            'email.email' => 'Unesite ispravnu email adresu.',
            'password.min' => 'Lozinka mora imati najmanje 8 karaktera.',
        ])->validateWithBag('addTeamMember');

        if ($team->hasUserWithEmail($validated['email'])) {
            Validator::make([], [])->after(function ($validator) {
                $validator->errors()->add('email', 'Korisnik sa ovom email adresom već pripada firmi.');
            })->validateWithBag('addTeamMember');
        }

        $role = $validated['role'] ?? TeamAccess::ROLE_EVIDENCIAR;
        $generatedPassword = null;
        $wasNewUser = false;
        $member = User::where('email', $validated['email'])->first();

        if ($member) {
            AddingTeamMember::dispatch($team, $member);
            $team->users()->attach($member, ['role' => $role]);
            $this->activateForImmediateLogin($member);
        } else {
            $wasNewUser = true;
            $generatedPassword = filled($validated['password'] ?? null)
                ? null
                : Str::password(12);

            $plainPassword = $validated['password'] ?? $generatedPassword;

            $member = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($plainPassword),
            ]);

            AddingTeamMember::dispatch($team, $member);
            $team->users()->attach($member, ['role' => $role]);
            $this->activateForImmediateLogin($member);
        }

        if (! $member->currentTeam) {
            $member->switchTeam($team);
        }

        TeamMemberAdded::dispatch($team, $member);

        return [
            'user' => $member,
            'generated_password' => $generatedPassword,
            'was_new_user' => $wasNewUser,
        ];
    }

    /**
     * Član firme može odmah da se uloguje — bez aktivacionog emaila.
     */
    protected function activateForImmediateLogin(User $member): void
    {
        if ($member->hasVerifiedEmail()) {
            return;
        }

        $member->markEmailAsVerified();
    }
}
