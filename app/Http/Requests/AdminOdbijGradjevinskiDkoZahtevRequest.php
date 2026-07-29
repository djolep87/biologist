<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminOdbijGradjevinskiDkoZahtevRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_super_admin === true;
    }

    public function rules(): array
    {
        return [
            'razlog_odbijanja' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'razlog_odbijanja.required' => 'Unesite razlog odbijanja.',
            'razlog_odbijanja.min' => 'Razlog mora imati najmanje 10 karaktera.',
        ];
    }
}
