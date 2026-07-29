<?php

namespace App\Http\Requests;

use App\Models\ConstructionSite;
use App\Models\DnevnaEvidencija;
use App\Models\GradjevinskiDkoZahtev;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GradjevinskiDkoZahtevRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', GradjevinskiDkoZahtev::class);
    }

    public function rules(): array
    {
        return [
            'construction_site_id' => ['required', 'exists:construction_sites,id'],
            'deo1_zapisi' => ['required', 'array', 'min:1'],
            'deo1_zapisi.*' => ['integer', 'exists:dnevne_evidencije,id'],
            'napomena_klijenta' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'deo1_zapisi.required' => 'Selektujte bar jedan DEO1 zapis.',
            'deo1_zapisi.min' => 'Selektujte bar jedan DEO1 zapis.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = $this->user();
            $teamId = $user->currentTeam?->id;
            $siteId = (int) $this->input('construction_site_id');
            $ids = array_map('intval', $this->input('deo1_zapisi', []));

            $site = ConstructionSite::query()->find($siteId);

            if (! $site || (int) $site->team_id !== (int) $teamId) {
                $validator->errors()->add('construction_site_id', 'Gradilište ne pripada vašoj firmi.');

                return;
            }

            if ($site->status !== ConstructionSite::STATUS_AKTIVNO) {
                $validator->errors()->add('construction_site_id', 'Gradilište mora biti aktivno da biste slali zahtev.');

                return;
            }

            $zapisi = DnevnaEvidencija::query()
                ->whereIn('id', $ids)
                ->get();

            if ($zapisi->count() !== count($ids)) {
                $validator->errors()->add('deo1_zapisi', 'Neki DEO1 zapisi više nisu dostupni.');

                return;
            }

            foreach ($zapisi as $zapis) {
                if ((int) $zapis->team_id !== (int) $teamId) {
                    $validator->errors()->add('deo1_zapisi', 'Svi zapisi moraju pripadati vašoj firmi.');

                    return;
                }

                if ((int) $zapis->construction_site_id !== $siteId) {
                    $validator->errors()->add('deo1_zapisi', 'Svi zapisi moraju biti sa izabranog gradilišta.');

                    return;
                }

                if ($zapis->dko_status !== 'slobodan' || $zapis->predat_operateru) {
                    $validator->errors()->add('deo1_zapisi', "Zapis {$zapis->indeksni_broj} ({$zapis->datum?->format('d.m.Y.')}) nije slobodan.");

                    return;
                }
            }
        });
    }
}
