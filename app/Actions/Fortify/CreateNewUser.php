<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $this->passwordRules(),
            'terms' => ['accepted'],
        ], [
            'first_name.required' => 'Polje je obavezno',
            'last_name.required' => 'Polje je obavezno',
            'email.required' => 'Polje je obavezno',
            'email.email' => 'Unesite ispravnu email adresu',
            'email.unique' => 'Email adresa je već registrovana',
            'password.required' => 'Polje je obavezno',
            'terms.accepted' => 'Morate prihvatiti uslove korišćenja',
        ])->validate();

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'name' => trim($input['first_name'].' '.$input['last_name']),
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => Hash::make($input['password']),
        ]);
    }
}
