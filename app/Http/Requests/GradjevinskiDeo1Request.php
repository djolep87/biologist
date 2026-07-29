<?php

namespace App\Http\Requests;

use App\Models\ConstructionSite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradjevinskiDeo1Request extends FormRequest
{
    public function authorize(): bool
    {
        $site = $this->route('site');

        if (! $site instanceof ConstructionSite) {
            return false;
        }

        return $this->user()->can('view', $site);
    }

    public function rules(): array
    {
        $site = $this->route('site');

        return [
            'construction_site_id' => [
                'required',
                Rule::exists('construction_sites', 'id')->where(fn ($q) => $q->where('id', $site->id)),
            ],
            'datum_unosa' => ['required', 'date', 'before_or_equal:today'],
            'katalog_sifra' => ['required', 'exists:katalog_otpada_grupa17,sifra'],
            'kolicina_t' => ['required', 'numeric', 'min:0.001', 'max:9999.999'],
            'nacin_nastanka' => ['nullable', 'string', 'max:500'],
            'napomena' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'katalog_sifra.required' => 'Izaberite vrstu otpada iz grupe 17.',
            'katalog_sifra.exists' => 'Izabrana šifra otpada nije validna.',
            'kolicina_t.required' => 'Unesite količinu u tonama.',
            'kolicina_t.min' => 'Minimalna količina je 0,001 t.',
            'datum_unosa.before_or_equal' => 'Datum unosa ne može biti u budućnosti.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $site = $this->route('site');

        if ($site instanceof ConstructionSite) {
            $this->merge(['construction_site_id' => $site->id]);
        }
    }
}
