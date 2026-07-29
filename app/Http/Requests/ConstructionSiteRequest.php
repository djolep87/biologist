<?php

namespace App\Http\Requests;

use App\Models\ConstructionSite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConstructionSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $site = $this->route('site');

        if ($site instanceof ConstructionSite) {
            return $this->user()->can('update', $site);
        }

        return $this->user()->can('create', ConstructionSite::class);
    }

    public function rules(): array
    {
        return [
            'naziv_gradilista' => ['required', 'string', 'max:255'],
            'broj_gradevinske_dozvole' => ['required', 'string', 'max:100'],
            'adresa_gradilista' => ['required', 'string', 'max:255'],
            'mesto' => ['required', 'string', 'max:255'],
            'opstina' => ['required', 'string', 'max:255'],
            'katastarska_parcela' => ['nullable', 'string', 'max:255'],
            'investitor_naziv' => ['nullable', 'string', 'max:255'],
            'izvodjac_naziv' => ['nullable', 'string', 'max:255'],
            'tip_radova' => ['required', Rule::in(['rusenje', 'gradnja', 'rekonstrukcija', 'sanacija'])],
            'status' => ['nullable', Rule::in(['aktivno', 'pauzirano'])],
            'datum_pocetka' => ['nullable', 'date'],
            'planirani_zavrsetak' => ['nullable', 'date', 'after:datum_pocetka'],
            'kvadratura_objekta' => ['nullable', 'numeric', 'min:1', 'max:999999'],
            'procijenjena_kolicina_otpada' => ['nullable', 'numeric', 'min:0', 'max:999999.999'],
            'operater_naziv' => ['nullable', 'string', 'max:255'],
            'operater_pib' => ['nullable', 'string', 'max:20'],
            'operater_adresa' => ['nullable', 'string', 'max:255'],
            'operater_dozvola_broj' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'broj_gradevinske_dozvole.required' => 'Broj građevinske dozvole je obavezan.',
            'tip_radova.required' => 'Izaberite tip radova.',
            'planirani_zavrsetak.after' => 'Planirani završetak mora biti posle datuma početka.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('kvadratura_objekta') && $this->filled('tip_radova') && ! $this->filled('procijenjena_kolicina_otpada')) {
            $procena = ConstructionSite::izracunajProcenu(
                (float) $this->input('kvadratura_objekta'),
                (string) $this->input('tip_radova')
            );

            if ($procena !== null) {
                $this->merge(['procijenjena_kolicina_otpada' => $procena]);
            }
        }
    }
}
