<?php

namespace App\Livewire\Admin;

use App\Models\WastePlan;
use App\Services\WastePlanContentParser;
use App\Services\WastePlanGeneratorService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class WastePlanGenerator extends Component
{
    use WithPagination;

    // Podaci o firmi
    public string $naziv_firme = '';

    public string $pib = '';

    public string $maticni_broj = '';

    public string $adresa_sedista = '';

    public string $mesto_opstina = '';

    public string $odgovorno_lice = '';

    public string $kontakt_eko = '';

    public string $telefon_email = '';

    public string $delatnost = '';

    public ?int $broj_zaposlenih = null;

    // Podaci o otpadu
    public string $vrste_otpada = '';

    public string $indeksni_brojevi = '';

    public string $procenjene_kolicine = '';

    public string $nacin_postupanja = '';

    public string $ugovori_operateri = '';

    // Lokacija
    public string $lokacija_pogon = '';

    public string $povrsina_objekta = '';

    public bool $ima_skladiste = false;

    public string $opis_skladista = '';

    // Pravni okvir
    public string $zakoni_propisi = 'Zakon o upravljanju otpadom RS, Uredba o kategorijama otpada, Pravilnik o načinu vođenja evidencije o otpadu';

    public string $rok_vazenja = '3 godine';

    public string $ciljevi_smanjenja = '';

    public string $posebne_napomene = '';

    // State
    public bool $generating = false;

    public int $generationStep = 0;

    public ?int $currentPlanId = null;

    public string $planContent = '';

    public int $planWordCount = 0;

    /** @var array<int, int> */
    public array $missingPages = [];

    public bool $needsRegeneration = false;

    public bool $showPreview = false;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    public ?string $warningMessage = null;

    public ?int $confirmingDeleteId = null;

    #[Computed]
    public function canGenerate(): bool
    {
        return filled($this->naziv_firme)
            && filled($this->pib)
            && filled($this->maticni_broj)
            && filled($this->adresa_sedista)
            && filled($this->mesto_opstina)
            && filled($this->odgovorno_lice)
            && filled($this->delatnost)
            && filled($this->rok_vazenja);
    }

    #[Computed]
    public function planPages(): array
    {
        if ($this->planContent === '') {
            return [];
        }

        return app(WastePlanContentParser::class)->parsePages($this->planContent);
    }

    public function generatePlan(WastePlanGeneratorService $generator): void
    {
        if (! $this->canGenerate || $this->generating) {
            return;
        }

        $this->errorMessage = null;
        $this->successMessage = null;
        $this->warningMessage = null;
        $this->generating = true;
        $this->generationStep = 1;

        try {
            $result = $generator->generate(
                $this->formData(),
                auth()->user(),
                fn (int $step) => $this->generationStep = $step
            );

            $plan = $result['plan'];
            $this->currentPlanId = $plan->id;
            $this->planContent = $plan->plan_content;
            $this->planWordCount = $result['word_count'];
            $this->missingPages = $result['missing_pages'];
            $this->needsRegeneration = $result['needs_regeneration'];
            $this->showPreview = true;

            $this->successMessage = "Plan je generisan i sačuvan ({$this->planWordCount} reči).";

            if ($this->needsRegeneration) {
                $warnings = [];
                if ($this->planWordCount < WastePlanContentParser::MIN_WORD_COUNT) {
                    $warnings[] = 'broj reči je ispod preporučenog minimuma od 5500';
                }
                if ($this->missingPages !== []) {
                    $warnings[] = 'nedostaju strane: '.implode(', ', $this->missingPages);
                }
                $this->warningMessage = 'Plan možda nije potpun ('.implode('; ', $warnings).'). Preporučujemo regenerisanje.';
            }
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->generating = false;
            $this->generationStep = 0;
        }
    }

    public function viewPlan(int $planId): void
    {
        $plan = WastePlan::findOrFail($planId);
        $parser = app(WastePlanContentParser::class);

        $this->currentPlanId = $plan->id;
        $this->planContent = $plan->plan_content;
        $this->naziv_firme = $plan->company_name;
        $this->planWordCount = $parser->countWords($plan->plan_content);
        $this->missingPages = $parser->getMissingPages($plan->plan_content);
        $this->needsRegeneration = $this->missingPages !== []
            || $this->planWordCount < WastePlanContentParser::MIN_WORD_COUNT;

        $this->hydrateFormFromPlan($plan);

        $this->showPreview = true;
        $this->errorMessage = null;
        $this->successMessage = null;
        $this->warningMessage = null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function hydrateField(string $field, array $data, mixed $default = ''): void
    {
        if (! array_key_exists($field, $data) || $data[$field] === '—') {
            return;
        }

        $value = $data[$field];

        if ($field === 'broj_zaposlenih') {
            $this->broj_zaposlenih = is_numeric($value) ? (int) $value : null;

            return;
        }

        if ($field === 'ima_skladiste') {
            $this->ima_skladiste = (bool) $value;

            return;
        }

        if (property_exists($this, $field)) {
            $this->{$field} = (string) $value;
        }
    }

    private function hydrateFormFromPlan(WastePlan $plan): void
    {
        $data = $plan->form_data ?? [];

        foreach ([
            'pib', 'maticni_broj', 'adresa_sedista', 'mesto_opstina', 'odgovorno_lice',
            'kontakt_eko', 'telefon_email', 'delatnost', 'vrste_otpada', 'indeksni_brojevi',
            'procenjene_kolicine', 'nacin_postupanja', 'ugovori_operateri', 'lokacija_pogon',
            'povrsina_objekta', 'opis_skladista', 'zakoni_propisi', 'rok_vazenja',
            'ciljevi_smanjenja', 'posebne_napomene',
        ] as $field) {
            $this->hydrateField($field, $data);
        }

        $this->hydrateField('broj_zaposlenih', $data);
        $this->hydrateField('ima_skladiste', $data);
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    public function savePlan(): void
    {
        if ($this->currentPlanId) {
            $this->successMessage = 'Plan je već sačuvan u bazi podataka.';
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'naziv_firme', 'pib', 'maticni_broj', 'adresa_sedista', 'mesto_opstina',
            'odgovorno_lice', 'kontakt_eko', 'telefon_email', 'delatnost', 'broj_zaposlenih',
            'vrste_otpada', 'indeksni_brojevi', 'procenjene_kolicine', 'nacin_postupanja',
            'ugovori_operateri', 'lokacija_pogon', 'povrsina_objekta', 'opis_skladista',
            'ciljevi_smanjenja', 'posebne_napomene',
            'currentPlanId', 'planContent', 'planWordCount', 'missingPages', 'needsRegeneration',
            'showPreview', 'errorMessage', 'successMessage', 'warningMessage',
        ]);

        $this->ima_skladiste = false;
        $this->zakoni_propisi = 'Zakon o upravljanju otpadom RS, Uredba o kategorijama otpada, Pravilnik o načinu vođenja evidencije o otpadu';
        $this->rok_vazenja = '3 godine';
    }

    public function confirmDelete(int $planId): void
    {
        $this->confirmingDeleteId = $planId;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function deletePlan(): void
    {
        if (! $this->confirmingDeleteId) {
            return;
        }

        $planId = $this->confirmingDeleteId;
        WastePlan::findOrFail($planId)->delete();

        if ($this->currentPlanId === $planId) {
            $this->resetForm();
        }

        $this->confirmingDeleteId = null;
        $this->successMessage = 'Plan je obrisan.';
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'naziv_firme' => $this->naziv_firme,
            'pib' => $this->pib,
            'maticni_broj' => $this->maticni_broj,
            'adresa_sedista' => $this->adresa_sedista,
            'mesto_opstina' => $this->mesto_opstina,
            'odgovorno_lice' => $this->odgovorno_lice,
            'kontakt_eko' => $this->kontakt_eko ?: '—',
            'telefon_email' => $this->telefon_email ?: '—',
            'delatnost' => $this->delatnost,
            'broj_zaposlenih' => $this->broj_zaposlenih ?? '—',
            'vrste_otpada' => $this->vrste_otpada ?: '—',
            'indeksni_brojevi' => $this->indeksni_brojevi ?: '—',
            'procenjene_kolicine' => $this->procenjene_kolicine ?: '—',
            'nacin_postupanja' => $this->nacin_postupanja ?: '—',
            'ugovori_operateri' => $this->ugovori_operateri ?: '—',
            'lokacija_pogon' => $this->lokacija_pogon ?: '—',
            'povrsina_objekta' => $this->povrsina_objekta ?: '—',
            'ima_skladiste' => $this->ima_skladiste,
            'opis_skladista' => $this->ima_skladiste ? ($this->opis_skladista ?: '—') : '—',
            'zakoni_propisi' => $this->zakoni_propisi,
            'rok_vazenja' => $this->rok_vazenja,
            'ciljevi_smanjenja' => $this->ciljevi_smanjenja ?: '—',
            'posebne_napomene' => $this->posebne_napomene ?: '—',
        ];
    }

    public function render()
    {
        return view('livewire.admin.waste-plan-generator', [
            'plans' => WastePlan::query()
                ->with('createdBy')
                ->orderByDesc('generated_at')
                ->paginate(10),
        ]);
    }
}
