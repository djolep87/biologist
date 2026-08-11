<?php

namespace App\Livewire;

use App\Models\DnevnaEvidencija;
use App\Support\TeamAccess;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class DnevnaEvidencijaForm extends Component
{
    public ?DnevnaEvidencija $evidencija = null;

    public bool $isEdit = false;

    public bool $showModal = false;

    public int $godina;

    public int $mesec;

    public string $indeksni_broj = '';

    public string $naziv_otpada = '';

    public string $opis_otpada = '';

    public string $karakter_otpada = 'neopasan';

    public string $fizicko_stanje = 'cvrsta-komadi';

    public string $lice_koje_vodi = '';

    public string $datum = '';

    public string $proizvedena_kolicina = '';

    public string $predata_kolicina = '';

    public float $stanje_na_skladistu = 0;

    public bool $predat_sakupljacu = false;

    public bool $predat_operateru_r = false;

    public bool $predat_operateru_d = false;

    public bool $izvoz = false;

    public string $naziv_primaoca = '';

    public string $broj_dozvole_primaoca = '';

    public string $nacin_odredjivanja = '1';

    public function mount(): void
    {
        $this->godina = now()->year;
        $this->mesec = now()->month;
        $this->datum = now()->toDateString();
        $this->lice_koje_vodi = auth()->user()->name;
    }

    protected function rules(): array
    {
        $requiresRecipient = $this->predat_sakupljacu
            || $this->predat_operateru_r
            || $this->predat_operateru_d
            || $this->izvoz;

        return [
            'godina' => 'required|integer|min:2020|max:2030',
            'mesec' => 'required|integer|min:1|max:12',
            'indeksni_broj' => 'required|string|max:20',
            'naziv_otpada' => 'required|string|max:255',
            'opis_otpada' => 'nullable|string',
            'karakter_otpada' => 'required|in:inertan,neopasan,opasan',
            'fizicko_stanje' => 'required|in:cvrsta-prah,cvrsta-komadi,viskozna-pasta,tecna,talog',
            'lice_koje_vodi' => 'required|string|max:255',
            'datum' => 'required|date',
            'proizvedena_kolicina' => 'numeric|min:0',
            'predata_kolicina' => 'numeric|min:0',
            'naziv_primaoca' => [
                Rule::requiredIf($requiresRecipient),
                'nullable',
                'string',
                'max:255',
            ],
            'broj_dozvole_primaoca' => [
                Rule::requiredIf($requiresRecipient),
                'nullable',
                'string',
                'max:255',
            ],
            'nacin_odredjivanja' => 'required|in:1,2,3',
        ];
    }

    protected function messages(): array
    {
        return [
            'godina.required' => 'Godina je obavezna.',
            'mesec.required' => 'Mesec je obavezan.',
            'indeksni_broj.required' => 'Indeksni broj otpada je obavezan.',
            'naziv_otpada.required' => 'Naziv otpada je obavezan.',
            'karakter_otpada.required' => 'Karakter otpada je obavezan.',
            'fizicko_stanje.required' => 'Fizičko stanje je obavezno.',
            'lice_koje_vodi.required' => 'Lice koje vodi evidenciju je obavezno.',
            'datum.required' => 'Datum je obavezan.',
            'datum.date' => 'Datum mora biti ispravan.',
            'proizvedena_kolicina.numeric' => 'Proizvedena količina mora biti broj.',
            'proizvedena_kolicina.min' => 'Količina ne može biti negativna.',
            'predata_kolicina.numeric' => 'Predata količina mora biti broj.',
            'predata_kolicina.min' => 'Količina ne može biti negativna.',
            'naziv_primaoca.required' => 'Naziv primaoca je obavezan kada je otpad predat.',
            'broj_dozvole_primaoca.required' => 'Broj dozvole primaoca je obavezan kada je otpad predat.',
        ];
    }

    public function updatedIndeksniBroj($value): void
    {
        $stavka = DnevnaEvidencija::katalogStavka((string) $value);

        if ($stavka) {
            $this->naziv_otpada = $stavka['naziv'];

            // Opasne šifre iz kataloga (npr. 16 03 05) same postavljaju karakter otpada.
            // Obrnuto ne važi — ručno izabran karakter se ne poništava.
            if ($stavka['opasan'] ?? false) {
                $this->karakter_otpada = 'opasan';
            }
        }

        $this->calculateStanje();
    }

    public function updatedDatum(): void
    {
        $this->calculateStanje();
    }

    public function updatedProizvedenaKolicina(?string $value): void
    {
        if ($value !== null && str_contains($value, ',')) {
            $this->proizvedena_kolicina = str_replace(',', '.', $value);
        }
        $this->calculateStanje();
    }

    public function updatedPredataKolicina(?string $value): void
    {
        if ($value !== null && str_contains($value, ',')) {
            $this->predata_kolicina = str_replace(',', '.', $value);
        }
        $this->calculateStanje();
    }

    private function calculateStanje(): void
    {
        $proizvedeno = $this->numericValue($this->proizvedena_kolicina);
        $predato = $this->numericValue($this->predata_kolicina);

        if (! $this->indeksni_broj || ! $this->datum) {
            $this->stanje_na_skladistu = max(0, round($proizvedeno - $predato, 2));

            return;
        }

        $query = DnevnaEvidencija::forTeam()
            ->where('indeksni_broj', $this->indeksni_broj)
            ->where('datum', '<', $this->datum);

        if ($this->isEdit && $this->evidencija) {
            $query->where('id', '!=', $this->evidencija->id);
        }

        $lastStanje = $query->orderByDesc('datum')->orderByDesc('id')->value('stanje_na_skladistu') ?? 0;

        $this->stanje_na_skladistu = round(
            (float) $lastStanje + $proizvedeno - $predato,
            2
        );
    }

    private function numericValue(?string $value): float
    {
        if ($value === null || $value === '' || $value === '.') {
            return 0.0;
        }

        $normalized = str_replace(',', '.', trim($value));

        if (! is_numeric($normalized)) {
            return 0.0;
        }

        return (float) $normalized;
    }

    private function formatKolicinaInput(float|string|null $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $float = (float) $value;

        if ($float == 0.0) {
            return '';
        }

        return rtrim(rtrim(sprintf('%.4F', $float), '0'), '.');
    }

    #[On('openCreateForm')]
    public function openCreate(): void
    {
        if (! TeamAccess::canCreateEvidencija(auth()->user())) {
            $this->dispatch('notify', message: 'Nemate dozvolu za unos evidencije.', type: 'error');

            return;
        }

        $this->resetForm();
        $this->isEdit = false;
        $this->evidencija = null;
        $this->showModal = true;
    }

    #[On('openEditForm')]
    public function openEdit(int $evidencijaId): void
    {
        $evidencija = DnevnaEvidencija::forTeam()->findOrFail($evidencijaId);

        if ($evidencija->dokument_kretanja_id) {
            $this->dispatch('notify', message: 'Predati izveštaji povezani sa DOKO dokumentom ne mogu se menjati.', type: 'error');

            return;
        }

        $this->authorizeEvidencija($evidencija);

        $this->evidencija = $evidencija;
        $this->isEdit = true;
        $this->fillFromModel($evidencija);
        $this->showModal = true;
    }

    public function save(): void
    {
        if (! TeamAccess::canCreateEvidencija(auth()->user())) {
            $this->dispatch('notify', message: 'Nemate dozvolu za unos evidencije.', type: 'error');

            return;
        }

        $this->normalizeKolicine();
        $this->syncPeriodFromDatum();
        $this->calculateStanje();

        $this->validate();

        $teamId = auth()->user()->currentTeam?->id;
        if (! $teamId) {
            $this->dispatch('notify', message: 'Niste izabrali firmu.', type: 'error');

            return;
        }

        $proizvedeno = $this->numericValue($this->proizvedena_kolicina);
        $predato = $this->numericValue($this->predata_kolicina);

        if ($proizvedeno <= 0 && $predato <= 0) {
            $this->addError('proizvedena_kolicina', 'Unesite proizvedenu ili predatu količinu veću od nule.');

            return;
        }

        $data = [
            'team_id' => $teamId,
            'user_id' => auth()->id(),
            'godina' => $this->godina,
            'mesec' => $this->mesec,
            'indeksni_broj' => $this->indeksni_broj,
            'naziv_otpada' => $this->naziv_otpada,
            'opis_otpada' => $this->opis_otpada ?: null,
            'karakter_otpada' => $this->karakter_otpada,
            'fizicko_stanje' => $this->fizicko_stanje,
            'lice_koje_vodi' => $this->lice_koje_vodi,
            'datum' => $this->datum,
            'proizvedena_kolicina' => $proizvedeno,
            'predata_kolicina' => $predato,
            'stanje_na_skladistu' => $this->stanje_na_skladistu,
            'predat_sakupljacu' => $this->predat_sakupljacu,
            'predat_operateru_r' => $this->predat_operateru_r,
            'predat_operateru_d' => $this->predat_operateru_d,
            'izvoz' => $this->izvoz,
            'naziv_primaoca' => $this->naziv_primaoca ?: null,
            'broj_dozvole_primaoca' => $this->broj_dozvole_primaoca ?: null,
            'nacin_odredjivanja' => $this->nacin_odredjivanja,
        ];

        try {
            if ($this->isEdit && $this->evidencija) {
                if ($this->evidencija->dokument_kretanja_id) {
                    $this->dispatch('notify', message: 'Predati izveštaji povezani sa DOKO dokumentom ne mogu se menjati.', type: 'error');

                    return;
                }

                $this->authorizeEvidencija($this->evidencija);
                $this->evidencija->update($data);
                $message = 'Evidencija je uspešno izmenjena.';
            } else {
                DnevnaEvidencija::create($data);
                $message = 'Evidencija je uspešno sačuvana.';
            }
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', message: 'Greška pri čuvanju evidencije. Pokušajte ponovo.', type: 'error');

            return;
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('evidencijaUpdated');
        $this->dispatch('notify', message: $message, type: 'success');
    }

    private function normalizeKolicine(): void
    {
        $this->proizvedena_kolicina = $this->normalizeKolicinaField($this->proizvedena_kolicina);
        $this->predata_kolicina = $this->normalizeKolicinaField($this->predata_kolicina);
    }

    private function normalizeKolicinaField(?string $value): string
    {
        if ($value === null || trim($value) === '' || trim($value) === '.') {
            return '0';
        }

        return str_replace(',', '.', trim($value));
    }

    private function syncPeriodFromDatum(): void
    {
        if (! $this->datum) {
            return;
        }

        $date = Carbon::parse($this->datum);
        $this->godina = $date->year;
        $this->mesec = $date->month;
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function fillFromModel(DnevnaEvidencija $evidencija): void
    {
        $this->godina = $evidencija->godina;
        $this->mesec = $evidencija->mesec;
        $this->indeksni_broj = $evidencija->indeksni_broj;
        $this->naziv_otpada = $evidencija->naziv_otpada;
        $this->opis_otpada = $evidencija->opis_otpada ?? '';
        $this->karakter_otpada = $evidencija->karakter_otpada;
        $this->fizicko_stanje = $evidencija->fizicko_stanje;
        $this->lice_koje_vodi = $evidencija->lice_koje_vodi;
        $this->datum = $evidencija->datum->format('Y-m-d');
        $this->proizvedena_kolicina = $this->formatKolicinaInput($evidencija->proizvedena_kolicina);
        $this->predata_kolicina = $this->formatKolicinaInput($evidencija->predata_kolicina);
        $this->stanje_na_skladistu = (float) $evidencija->stanje_na_skladistu;
        $this->predat_sakupljacu = $evidencija->predat_sakupljacu;
        $this->predat_operateru_r = $evidencija->predat_operateru_r;
        $this->predat_operateru_d = $evidencija->predat_operateru_d;
        $this->izvoz = $evidencija->izvoz;
        $this->naziv_primaoca = $evidencija->naziv_primaoca ?? '';
        $this->broj_dozvole_primaoca = $evidencija->broj_dozvole_primaoca ?? '';
        $this->nacin_odredjivanja = $evidencija->nacin_odredjivanja;
    }

    private function resetForm(): void
    {
        $this->evidencija = null;
        $this->isEdit = false;
        $this->indeksni_broj = '';
        $this->naziv_otpada = '';
        $this->opis_otpada = '';
        $this->karakter_otpada = 'neopasan';
        $this->fizicko_stanje = 'cvrsta-komadi';
        $this->proizvedena_kolicina = '';
        $this->predata_kolicina = '';
        $this->stanje_na_skladistu = 0;
        $this->predat_sakupljacu = false;
        $this->predat_operateru_r = false;
        $this->predat_operateru_d = false;
        $this->izvoz = false;
        $this->naziv_primaoca = '';
        $this->broj_dozvole_primaoca = '';
        $this->nacin_odredjivanja = '1';
        $this->godina = now()->year;
        $this->mesec = now()->month;
        $this->datum = now()->toDateString();
        $this->lice_koje_vodi = auth()->user()->name;
    }

    private function authorizeEvidencija(DnevnaEvidencija $evidencija): void
    {
        $user = auth()->user();

        if ($evidencija->team_id !== $user->currentTeam?->id) {
            abort(403);
        }

        if (TeamAccess::isEvidenciarOnly($user) && $evidencija->user_id !== $user->id) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.dnevna-evidencija-form', [
            'katalog' => DnevnaEvidencija::katalogOtpadaGrupisano(),
        ]);
    }
}
