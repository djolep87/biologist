<?php

namespace App\Livewire;

use App\Models\DnevnaEvidencija;
use App\Models\DokumentKretanja;
use App\Models\Operater;
use App\Models\Team;
use App\Models\ZahtevPredaje;
use App\Notifications\ZahtevObradjeni;
use App\Services\BrojIzvestajaService;
use App\Support\AdminTeamContext;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class KreirajDokumentKretanja extends Component
{
    public bool $showModal = false;

    public int $currentStep = 1;

    /** @var array<int> */
    public array $izabraniIds = [];

    public string $filterIndeks = '';

    public ?string $lockedIndeksniBroj = null;

    // DEO A
    public string $indeksni_broj = '';

    public string $vrsta_otpada = '';

    public string $q_lista = '';

    public string $nacin_pakovanja = '';

    public string $fizicko_stanje = '';

    public string $izvestaj_broj = '';

    public string $izvestaj_datum = '';

    // Automatski broj izveštaja (jedinstveni broj DOKO dokumenta)
    public string $brojIzvestajaPreview = '';

    public string $formatBroja = 'osnovni';

    public string $brojIzvestajaLokacija = '';

    /** @var array<int, array{oznaka: string, naziv: string}> */
    public array $lokacijeOpcije = [];

    public string $odrediste = '';

    public string $vid_prevoza = '';

    public string $posebne_napomene = '';

    // DEO B
    public string $proizvodjac_pib = '';

    public string $proizvodjac_maticni = '';

    public string $proizvodjac_naziv = '';

    public string $proizvodjac_opstina = '';

    public string $proizvodjac_mesto = '';

    public string $proizvodjac_postanski = '';

    public string $proizvodjac_ulica = '';

    public string $proizvodjac_telefon = '';

    public string $proizvodjac_faks = '';

    public string $proizvodjac_email = '';

    public string $vlasnik_tip = 'proizvodjac';

    public string $r_oznaka = '';

    public string $d_oznaka = '';

    public string $dozvola_broj = '';

    public string $dozvola_datum = '';

    public string $datum_predaje = '';

    public string $odgovorno_lice_b = '';

    public string $telefon_lica_b = '';

    // DEO C
    public string $prevoznik_pib = '';

    public string $prevoznik_maticni = '';

    public string $prevoznik_naziv = '';

    public string $prevoznik_opstina = '';

    public string $prevoznik_mesto = '';

    public string $prevoznik_postanski = '';

    public string $prevoznik_ulica = '';

    public string $prevoznik_telefon = '';

    public string $prevoznik_faks = '';

    public string $prevoznik_email = '';

    public string $vrsta_prevoznog_sredstva = '';

    public string $registarski_broj = '';

    public string $lokacija_utovara = '';

    public string $ruta_via_1 = '';

    public string $ruta_via_2 = '';

    public string $ruta_via_3 = '';

    public string $lokacija_istovara = '';

    public string $prevoznik_dozvola_broj = '';

    public string $prevoznik_dozvola_datum = '';

    public string $prevoznik_datum_prijema = '';

    public string $prevoznik_odgovorno_lice_prijem = '';

    public string $prevoznik_telefon_lica_prijem = '';

    public string $prevoznik_datum_predaje = '';

    public string $prevoznik_odgovorno_lice_predaja = '';

    public string $prevoznik_telefon_lica_predaja = '';

    // DEO D
    public string $primalac_pib = '';

    public string $primalac_maticni = '';

    public string $primalac_naziv = '';

    public string $primalac_opstina = '';

    public string $primalac_mesto = '';

    public string $primalac_postanski = '';

    public string $primalac_ulica = '';

    public string $primalac_telefon = '';

    public string $primalac_faks = '';

    public string $primalac_email = '';

    public string $primalac_tip = '';

    public string $primalac_dozvola_broj = '';

    public string $primalac_dozvola_datum = '';

    public string $primalac_datum_prijema = '';

    public string $primalac_odgovorno_lice = '';

    public string $primalac_telefon_lica = '';

    public string $operaterSearch = '';

    public ?int $selectedOperaterId = null;

    public string $selectedOperaterName = '';

    /** @var array<int, array<string, mixed>> */
    public array $operaterRezultati = [];

    public string $prevoznikOperaterSearch = '';

    public ?int $selectedPrevoznikOperaterId = null;

    public string $selectedPrevoznikOperaterName = '';

    /** @var array<int, array<string, mixed>> */
    public array $prevoznikOperaterRezultati = [];

    public ?int $teamId = null;

    public ?int $zahtevId = null;

    #[On('openDokoForm')]
    public function openModal(): void
    {
        $this->ensureCanManageDoko();
        $this->resetForm();
        $this->fillFromTeam();
        $this->loadNumeracija();
        $this->datum_predaje = now()->toDateString();
        $this->showModal = true;
    }

    #[On('openDokoFormFromZahtev')]
    public function openFromZahtev(int $zahtevId): void
    {
        $this->ensureCanManageDoko();

        $zahtev = ZahtevPredaje::with('evidencije')->findOrFail($zahtevId);
        abort_if(in_array($zahtev->status, ['zavrseno', 'odbijeno'], true), 422, 'Zahtev je već obrađen.');

        $this->zahtevId = $zahtev->id;
        $this->teamId = $zahtev->team_id;
        $this->resetForm();
        $this->zahtevId = $zahtev->id;
        $this->teamId = $zahtev->team_id;
        $this->fillFromTeam();
        $this->loadNumeracija();
        $this->datum_predaje = now()->toDateString();

        $this->izabraniIds = $zahtev->evidencije->pluck('id')->all();
        $this->lockedIndeksniBroj = $zahtev->indeksni_broj;
        $this->indeksni_broj = $zahtev->indeksni_broj;
        $this->vrsta_otpada = $zahtev->naziv_otpada;
        $this->filterIndeks = $zahtev->indeksni_broj;
        $this->posebne_napomene = $zahtev->napomena_klijenta ?? '';

        $first = $zahtev->evidencije->first();
        if ($first) {
            $this->fizicko_stanje = $first->fizicko_stanje ?? '';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Učitava podešavanja numeracije za aktivnog klijenta i osvežava preview broja.
     */
    private function loadNumeracija(): void
    {
        $teamId = $this->actingTeamId();

        if (! $teamId) {
            $this->formatBroja = 'osnovni';
            $this->lokacijeOpcije = [];
            $this->brojIzvestajaLokacija = '';
            $this->brojIzvestajaPreview = '';

            return;
        }

        $settings = app(BrojIzvestajaService::class)->settingsForTeam(Team::find($teamId));

        $this->formatBroja = $settings['format'];
        $this->lokacijeOpcije = $settings['lokacije'];
        $this->brojIzvestajaLokacija = $settings['lokacije'][0]['oznaka'] ?? '';

        $this->refreshBrojPreview();
    }

    /**
     * Osvežava preview broja izveštaja (dugme 🔄 i promena lokacije).
     */
    public function refreshBrojPreview(): void
    {
        $teamId = $this->actingTeamId();

        if (! $teamId) {
            $this->brojIzvestajaPreview = '';

            return;
        }

        $this->brojIzvestajaPreview = app(BrojIzvestajaService::class)
            ->previewBroj($teamId, $this->brojIzvestajaLokacija ?: null);
    }

    public function updatedBrojIzvestajaLokacija(): void
    {
        $this->refreshBrojPreview();
    }

    public function updatedOperaterSearch(string $value): void
    {
        $this->operaterRezultati = $this->pretraziOperatere($value, $this->selectedOperaterName);
    }

    public function updatedPrevoznikOperaterSearch(string $value): void
    {
        $this->prevoznikOperaterRezultati = $this->pretraziOperatere($value, $this->selectedPrevoznikOperaterName);
    }

    public function updatedPrevoznikPib(string $value): void
    {
        $this->tryAutoFillOperaterByPib($value, 'prevoznik');
    }

    public function updatedPrimalacPib(string $value): void
    {
        $this->tryAutoFillOperaterByPib($value, 'primalac');
    }

    public function izaberiOperatera(int $id): void
    {
        $op = Operater::find($id);

        if (! $op) {
            return;
        }

        $this->selectedOperaterId = $op->id;
        $this->selectedOperaterName = $op->kratki_naziv ?? $op->naziv;
        $this->operaterSearch = $this->selectedOperaterName;
        $this->operaterRezultati = [];

        $this->primeniOperateraNaPrimaoca($op);
    }

    public function izaberiPrevoznikOperatera(int $id): void
    {
        $op = Operater::find($id);

        if (! $op) {
            return;
        }

        $this->primeniOperateraNaPrevoznika($op);
    }

    public function kopirajPrevoznikaOdPrimaoca(): void
    {
        if ($this->selectedOperaterId) {
            $op = Operater::find($this->selectedOperaterId);

            if ($op) {
                $this->primeniOperateraNaPrevoznika($op);

                return;
            }
        }

        if ($this->primalac_pib === '') {
            return;
        }

        $this->prevoznik_pib = $this->primalac_pib;
        $this->prevoznik_maticni = $this->primalac_maticni;
        $this->prevoznik_naziv = $this->primalac_naziv;
        $this->prevoznik_opstina = $this->primalac_opstina;
        $this->prevoznik_mesto = $this->primalac_mesto;
        $this->prevoznik_postanski = $this->primalac_postanski;
        $this->prevoznik_ulica = $this->primalac_ulica;
        $this->prevoznik_telefon = $this->primalac_telefon;
        $this->prevoznik_faks = $this->primalac_faks;
        $this->prevoznik_email = $this->primalac_email;
        $this->prevoznik_dozvola_broj = $this->primalac_dozvola_broj;
        $this->prevoznik_dozvola_datum = $this->primalac_dozvola_datum;
        $this->prevoznik_odgovorno_lice_prijem = $this->primalac_odgovorno_lice;
        $this->prevoznik_odgovorno_lice_predaja = $this->primalac_odgovorno_lice;
        $this->selectedPrevoznikOperaterId = $this->selectedOperaterId;
        $this->selectedPrevoznikOperaterName = $this->selectedOperaterName;
        $this->prevoznikOperaterSearch = $this->selectedPrevoznikOperaterName;
        $this->prevoznikOperaterRezultati = [];
    }

    public function clearOperater(): void
    {
        $this->selectedOperaterId = null;
        $this->selectedOperaterName = '';
        $this->operaterSearch = '';
        $this->operaterRezultati = [];

        foreach ([
            'primalac_pib', 'primalac_maticni', 'primalac_naziv', 'primalac_opstina',
            'primalac_mesto', 'primalac_postanski', 'primalac_ulica', 'primalac_telefon',
            'primalac_faks', 'primalac_email', 'primalac_dozvola_broj', 'primalac_dozvola_datum',
            'primalac_odgovorno_lice', 'primalac_telefon_lica', 'primalac_tip',
            'primalac_datum_prijema',
        ] as $field) {
            $this->{$field} = '';
        }
    }

    public function clearPrevoznikOperater(): void
    {
        $this->selectedPrevoznikOperaterId = null;
        $this->selectedPrevoznikOperaterName = '';
        $this->prevoznikOperaterSearch = '';
        $this->prevoznikOperaterRezultati = [];

        foreach ([
            'prevoznik_pib', 'prevoznik_maticni', 'prevoznik_naziv', 'prevoznik_opstina',
            'prevoznik_mesto', 'prevoznik_postanski', 'prevoznik_ulica', 'prevoznik_telefon',
            'prevoznik_faks', 'prevoznik_email', 'prevoznik_dozvola_broj', 'prevoznik_dozvola_datum',
            'prevoznik_odgovorno_lice_prijem', 'prevoznik_telefon_lica_prijem',
            'prevoznik_odgovorno_lice_predaja', 'prevoznik_telefon_lica_predaja',
        ] as $field) {
            $this->{$field} = '';
        }
    }

    public function toggleEvidencija(int $id): void
    {
        if (in_array($id, $this->izabraniIds, true)) {
            $this->izabraniIds = array_values(array_filter(
                $this->izabraniIds,
                fn (int $i) => $i !== $id
            ));

            if ($this->izabraniIds === []) {
                $this->lockedIndeksniBroj = null;
            }

            return;
        }

        $evidencija = $this->findAvailableEvidencija($id);

        if (! $evidencija) {
            return;
        }

        if ($this->lockedIndeksniBroj === null) {
            $this->lockedIndeksniBroj = $evidencija->indeksni_broj;
            $this->indeksni_broj = $evidencija->indeksni_broj;
            $this->vrsta_otpada = $evidencija->naziv_otpada;
            $this->fizicko_stanje = $evidencija->fizicko_stanje;
        } elseif ($evidencija->indeksni_broj !== $this->lockedIndeksniBroj) {
            $this->addError('izabraniIds', 'Svi izveštaji moraju biti istog indeksnog broja.');

            return;
        }

        $this->resetErrorBag('izabraniIds');
        $this->izabraniIds[] = $id;
    }

    public function nextStep(): void
    {
        $this->validateStep($this->currentStep);

        if ($this->currentStep === 1) {
            $this->syncDeoAFromSelection();
            $this->tryAutoFillPrimalac();
        }

        $this->currentStep = min($this->currentStep + 1, 6);
    }

    public function prevStep(): void
    {
        $this->currentStep = max($this->currentStep - 1, 1);
    }

    public function save(): void
    {
        $this->ensureCanManageDoko();

        for ($step = 1; $step <= 5; $step++) {
            $this->validateStep($step);
        }

        $teamId = $this->actingTeamId();
        abort_unless($teamId, 403);

        $evidencije = DnevnaEvidencija::forTeam($teamId)
            ->whereIn('id', $this->izabraniIds)
            ->where('predat_operateru', false)
            ->get();

        abort_if($evidencije->count() !== count($this->izabraniIds), 422, 'Neki izveštaji više nisu dostupni za predaju.');

        $indeksi = $evidencije->pluck('indeksni_broj')->unique();
        abort_if($indeksi->count() !== 1, 422, 'Svi izveštaji moraju biti istog indeksnog broja.');

        $masa = (float) $evidencije->sum('stanje_na_skladistu');

        $zahtevId = $this->zahtevId;

        $lokacija = $this->brojIzvestajaLokacija ?: null;
        $dokument = null;
        $pokusaj = 0;

        // Retry na duplicate key (edge case pri ekstremnom load-u uprkos lockForUpdate).
        do {
            $pokusaj++;

            try {
                $dokument = DB::transaction(function () use ($teamId, $masa, $evidencije, $zahtevId, $lokacija) {
                    $brojPodaci = app(BrojIzvestajaService::class)->generateBroj($teamId, $lokacija);

                    $dokument = DokumentKretanja::create(
                        array_merge($this->dokumentPayload($teamId, $masa), $brojPodaci)
                    );

                    foreach ($evidencije as $evidencija) {
                        $evidencija->update([
                            'predat_operateru' => true,
                            'dokument_kretanja_id' => $dokument->id,
                            'datum_predaje_operateru' => $this->datum_predaje,
                            'operater_naziv' => $this->primalac_naziv,
                            'operater_dozvola_broj' => $this->primalac_dozvola_broj,
                            'predat_operateru_r' => $this->r_oznaka !== '',
                            'predat_operateru_d' => $this->d_oznaka !== '',
                            'r_oznaka' => $this->r_oznaka ?: null,
                            'd_oznaka' => $this->d_oznaka ?: null,
                            'naziv_primaoca' => $this->primalac_naziv,
                            'broj_dozvole_primaoca' => $this->primalac_dozvola_broj,
                            'predata_kolicina' => $evidencija->stanje_na_skladistu,
                            'stanje_na_skladistu' => 0,
                        ]);
                    }

                    if ($zahtevId) {
                        $zahtev = ZahtevPredaje::with('user')->find($zahtevId);

                        if ($zahtev && ! in_array($zahtev->status, ['zavrseno', 'odbijeno'], true)) {
                            $zahtev->update([
                                'status' => 'zavrseno',
                                'dokument_kretanja_id' => $dokument->id,
                                'admin_id' => auth()->id(),
                                'admin_odgovorio_at' => now(),
                            ]);

                            $zahtev->user->notify(new ZahtevObradjeni($zahtev, $dokument));
                        }
                    }

                    return $dokument;
                });

                break;
            } catch (QueryException $e) {
                if ($pokusaj >= 3 || $e->getCode() !== '23000') {
                    throw $e;
                }

                usleep(50_000 * $pokusaj);
            }
        } while ($pokusaj < 3);

        abort_unless($dokument, 500, 'Nije moguće generisati jedinstveni broj izveštaja. Pokušajte ponovo.');

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('dokumentKreiran');
        $this->dispatch('evidencijaUpdated');
        $this->dispatch('zahtevPoslat');
        $this->dispatch('notify', message: "✅ DOKO dokument kreiran — Broj izveštaja: {$dokument->broj_izvestaja}. Preuzimanje DOKO.xlsx...", type: 'success');
        $this->dispatch('download-file', url: route('doko.download', $dokument));

        if ($zahtevId) {
            session()->flash('success', "DOKO dokument kreiran — Broj izveštaja: {$dokument->broj_izvestaja} (interni: {$dokument->broj_dokumenta}). Klijent je obavešten.");
            $this->redirect(route('admin.zahtevi.show', $zahtevId), navigate: true);

            return;
        }
    }

    public function getAvailableEvidencijeProperty(): Collection
    {
        return DnevnaEvidencija::forTeam($this->actingTeamId())
            ->where('predat_operateru', false)
            ->when($this->filterIndeks !== '', fn ($q) => $q->where('indeksni_broj', $this->filterIndeks))
            ->when($this->lockedIndeksniBroj, fn ($q) => $q->where('indeksni_broj', $this->lockedIndeksniBroj))
            ->orderBy('indeksni_broj')
            ->orderBy('datum')
            ->get();
    }

    public function getIndeksOptionsProperty(): Collection
    {
        return DnevnaEvidencija::forTeam($this->actingTeamId())
            ->where('predat_operateru', false)
            ->select('indeksni_broj')
            ->distinct()
            ->orderBy('indeksni_broj')
            ->pluck('indeksni_broj');
    }

    public function getSelectedMasaProperty(): float
    {
        if ($this->izabraniIds === []) {
            return 0;
        }

        return (float) DnevnaEvidencija::forTeam($this->actingTeamId())
            ->whereIn('id', $this->izabraniIds)
            ->sum('stanje_na_skladistu');
    }

    public function getSelectedEvidencijeProperty(): Collection
    {
        if ($this->izabraniIds === []) {
            return collect();
        }

        return DnevnaEvidencija::forTeam($this->actingTeamId())
            ->whereIn('id', $this->izabraniIds)
            ->orderBy('datum')
            ->get();
    }

    public function render()
    {
        return view('livewire.kreiraj-dokument-kretanja', [
            'availableEvidencije' => $this->availableEvidencije,
            'indeksOptions' => $this->indeksOptions,
            'selectedMasa' => $this->selectedMasa,
            'selectedEvidencije' => $this->selectedEvidencije,
            'rOznake' => $this->oznake('R', 13),
            'dOznake' => $this->oznake('D', 15),
        ]);
    }

    private function validateStep(int $step): void
    {
        match ($step) {
            1 => $this->validate([
                'izabraniIds' => ['required', 'array', 'min:1'],
            ], [
                'izabraniIds.required' => 'Izaberite bar jedan dnevni izveštaj.',
                'izabraniIds.min' => 'Izaberite bar jedan dnevni izveštaj.',
            ]),
            2 => $this->validate([
                'indeksni_broj' => ['required', 'string'],
                'vrsta_otpada' => ['required', 'string'],
                'fizicko_stanje' => ['nullable', Rule::in(['cvrsta-prah', 'cvrsta-komadi', 'viskozna-pasta', 'tecna', 'talog'])],
                'vid_prevoza' => ['nullable', Rule::in(['Drumski', 'Železnički', 'Brodski', 'Vazdušni', ''])],
            ]),
            3 => $this->validate([
                'proizvodjac_naziv' => ['required', 'string', 'max:255'],
                'datum_predaje' => ['required', 'date'],
                'vlasnik_tip' => ['required', Rule::in(['proizvodjac', 'vlasnik', 'operater'])],
                'r_oznaka' => ['nullable', Rule::in(array_merge([''], $this->oznake('R', 13)))],
                'd_oznaka' => ['nullable', Rule::in(array_merge([''], $this->oznake('D', 15)))],
            ]),
            4, 5 => null,
            default => null,
        };
    }

    /** @return array<string, mixed> */
    private function dokumentPayload(int $teamId, float $masa): array
    {
        return [
            'team_id' => $teamId,
            'user_id' => auth()->id(),
            'indeksni_broj' => $this->indeksni_broj,
            'vrsta_otpada' => $this->vrsta_otpada,
            'q_lista' => $this->q_lista ?: null,
            'masa_ukupno' => $masa,
            'nacin_pakovanja' => $this->nacin_pakovanja ?: null,
            'fizicko_stanje' => $this->fizicko_stanje ?: null,
            'izvestaj_broj' => $this->izvestaj_broj ?: null,
            'izvestaj_datum' => $this->izvestaj_datum ?: null,
            'odrediste' => $this->odrediste ?: null,
            'vid_prevoza' => $this->vid_prevoza ?: null,
            'posebne_napomene' => $this->posebne_napomene ?: null,
            'proizvodjac_pib' => $this->proizvodjac_pib ?: null,
            'proizvodjac_maticni' => $this->proizvodjac_maticni ?: null,
            'proizvodjac_naziv' => $this->proizvodjac_naziv,
            'proizvodjac_opstina' => $this->proizvodjac_opstina ?: null,
            'proizvodjac_mesto' => $this->proizvodjac_mesto ?: null,
            'proizvodjac_postanski' => $this->proizvodjac_postanski ?: null,
            'proizvodjac_ulica' => $this->proizvodjac_ulica ?: null,
            'proizvodjac_telefon' => $this->proizvodjac_telefon ?: null,
            'proizvodjac_faks' => $this->proizvodjac_faks ?: null,
            'proizvodjac_email' => $this->proizvodjac_email ?: null,
            'vlasnik_tip' => $this->vlasnik_tip,
            'r_oznaka' => $this->r_oznaka ?: null,
            'd_oznaka' => $this->d_oznaka ?: null,
            'dozvola_broj' => $this->dozvola_broj ?: null,
            'dozvola_datum' => $this->dozvola_datum ?: null,
            'datum_predaje' => $this->datum_predaje,
            'odgovorno_lice_b' => $this->odgovorno_lice_b ?: null,
            'telefon_lica_b' => $this->telefon_lica_b ?: null,
            'prevoznik_pib' => $this->prevoznik_pib ?: null,
            'prevoznik_maticni' => $this->prevoznik_maticni ?: null,
            'prevoznik_naziv' => $this->prevoznik_naziv ?: null,
            'prevoznik_opstina' => $this->prevoznik_opstina ?: null,
            'prevoznik_mesto' => $this->prevoznik_mesto ?: null,
            'prevoznik_postanski' => $this->prevoznik_postanski ?: null,
            'prevoznik_ulica' => $this->prevoznik_ulica ?: null,
            'prevoznik_telefon' => $this->prevoznik_telefon ?: null,
            'prevoznik_faks' => $this->prevoznik_faks ?: null,
            'prevoznik_email' => $this->prevoznik_email ?: null,
            'vrsta_prevoznog_sredstva' => $this->vrsta_prevoznog_sredstva ?: null,
            'registarski_broj' => $this->registarski_broj ?: null,
            'lokacija_utovara' => $this->lokacija_utovara ?: null,
            'ruta_via_1' => $this->ruta_via_1 ?: null,
            'ruta_via_2' => $this->ruta_via_2 ?: null,
            'ruta_via_3' => $this->ruta_via_3 ?: null,
            'lokacija_istovara' => $this->lokacija_istovara ?: null,
            'prevoznik_dozvola_broj' => $this->prevoznik_dozvola_broj ?: null,
            'prevoznik_dozvola_datum' => $this->prevoznik_dozvola_datum ?: null,
            'prevoznik_datum_prijema' => $this->prevoznik_datum_prijema ?: null,
            'prevoznik_odgovorno_lice_prijem' => $this->prevoznik_odgovorno_lice_prijem ?: null,
            'prevoznik_telefon_lica_prijem' => $this->prevoznik_telefon_lica_prijem ?: null,
            'prevoznik_datum_predaje' => $this->prevoznik_datum_predaje ?: null,
            'prevoznik_odgovorno_lice_predaja' => $this->prevoznik_odgovorno_lice_predaja ?: null,
            'prevoznik_telefon_lica_predaja' => $this->prevoznik_telefon_lica_predaja ?: null,
            'primalac_pib' => $this->primalac_pib ?: null,
            'primalac_maticni' => $this->primalac_maticni ?: null,
            'primalac_naziv' => $this->primalac_naziv ?: null,
            'primalac_opstina' => $this->primalac_opstina ?: null,
            'primalac_mesto' => $this->primalac_mesto ?: null,
            'primalac_postanski' => $this->primalac_postanski ?: null,
            'primalac_ulica' => $this->primalac_ulica ?: null,
            'primalac_telefon' => $this->primalac_telefon ?: null,
            'primalac_faks' => $this->primalac_faks ?: null,
            'primalac_email' => $this->primalac_email ?: null,
            'primalac_tip' => $this->primalac_tip ?: null,
            'primalac_dozvola_broj' => $this->primalac_dozvola_broj ?: null,
            'primalac_dozvola_datum' => $this->primalac_dozvola_datum ?: null,
            'primalac_datum_prijema' => $this->primalac_datum_prijema ?: null,
            'primalac_odgovorno_lice' => $this->primalac_odgovorno_lice ?: null,
            'primalac_telefon_lica' => $this->primalac_telefon_lica ?: null,
        ];
    }

    private function syncDeoAFromSelection(): void
    {
        $first = $this->selectedEvidencije->first();

        if (! $first) {
            return;
        }

        $this->indeksni_broj = $first->indeksni_broj;
        $this->vrsta_otpada = $first->naziv_otpada;

        if ($this->fizicko_stanje === '') {
            $this->fizicko_stanje = $first->fizicko_stanje;
        }
    }

    private function fillFromTeam(): void
    {
        $team = Team::find($this->actingTeamId());

        if (! $team) {
            return;
        }

        $podaci = $team->dokoProizvodjacPodaci();

        $this->proizvodjac_pib = $podaci['pib'];
        $this->proizvodjac_maticni = $podaci['maticni'];
        $this->proizvodjac_naziv = $podaci['naziv'];
        $this->proizvodjac_opstina = $podaci['opstina'];
        $this->proizvodjac_mesto = $podaci['mesto'];
        $this->proizvodjac_postanski = $podaci['postanski'];
        $this->proizvodjac_ulica = $podaci['ulica'];
        $this->proizvodjac_telefon = $podaci['telefon'];
        $this->proizvodjac_faks = $podaci['faks'];
        $this->proizvodjac_email = $podaci['email'];
        $this->dozvola_broj = $podaci['dozvola_broj'];
        $this->dozvola_datum = $podaci['dozvola_datum'];
        $this->odgovorno_lice_b = $podaci['odgovorno_lice'] ?: (auth()->user()->name ?? '');
        $this->telefon_lica_b = $podaci['telefon_lica'];
        $this->lokacija_utovara = $podaci['lokacija_utovara'];
    }

    private function tryAutoFillPrimalac(): void
    {
        if ($this->primalac_naziv !== '') {
            return;
        }

        foreach ($this->selectedEvidencije as $evidencija) {
            if ($evidencija->broj_dozvole_primaoca) {
                $operater = Operater::aktivni()
                    ->where('dozvola_broj', $evidencija->broj_dozvole_primaoca)
                    ->first();

                if ($operater) {
                    $this->izaberiOperatera($operater->id);

                    return;
                }
            }

            if ($evidencija->naziv_primaoca) {
                $operater = Operater::aktivni()
                    ->where(function ($q) use ($evidencija) {
                        $q->where('naziv', $evidencija->naziv_primaoca)
                            ->orWhere('kratki_naziv', $evidencija->naziv_primaoca);
                    })
                    ->first();

                if ($operater) {
                    $this->izaberiOperatera($operater->id);

                    return;
                }
            }
        }

        if ($this->indeksni_broj === '') {
            return;
        }

        $lastDoko = DokumentKretanja::query()
            ->where('team_id', $this->actingTeamId())
            ->where('indeksni_broj', $this->indeksni_broj)
            ->whereNotNull('primalac_pib')
            ->latest('id')
            ->first();

        if (! $lastDoko?->primalac_pib) {
            return;
        }

        $operater = Operater::aktivni()->where('pib', $lastDoko->primalac_pib)->first();

        if ($operater) {
            $this->izaberiOperatera($operater->id);
        }
    }

    private function tryAutoFillOperaterByPib(string $value, string $target): void
    {
        $pib = preg_replace('/\D/', '', $value);

        if (strlen($pib) !== 9) {
            return;
        }

        if ($target === 'prevoznik' && $this->selectedPrevoznikOperaterId && $this->prevoznik_pib === $pib) {
            return;
        }

        if ($target === 'primalac' && $this->selectedOperaterId && $this->primalac_pib === $pib) {
            return;
        }

        $operater = Operater::aktivni()->where('pib', $pib)->first();

        if (! $operater) {
            return;
        }

        if ($target === 'prevoznik') {
            $this->primeniOperateraNaPrevoznika($operater);
        } else {
            $this->izaberiOperatera($operater->id);
        }
    }

    private function findAvailableEvidencija(int $id): ?DnevnaEvidencija
    {
        return DnevnaEvidencija::forTeam($this->actingTeamId())
            ->where('predat_operateru', false)
            ->find($id);
    }

    private function actingTeamId(): ?int
    {
        if ($this->teamId) {
            return $this->teamId;
        }

        if (auth()->user()?->is_super_admin) {
            return AdminTeamContext::selectedTeamId();
        }

        return auth()->user()?->currentTeam?->id;
    }

    private function ensureCanManageDoko(): void
    {
        abort_unless(auth()->user()?->is_super_admin, 403, 'Kreiranje DOKO dokumenta obavlja samo administrator.');
        abort_unless($this->actingTeamId(), 422, 'Izaberite firmu pre kreiranja DOKO dokumenta.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'currentStep', 'izabraniIds', 'filterIndeks', 'lockedIndeksniBroj',
            'indeksni_broj', 'vrsta_otpada', 'q_lista', 'nacin_pakovanja', 'fizicko_stanje',
            'izvestaj_broj', 'izvestaj_datum', 'odrediste', 'vid_prevoza', 'posebne_napomene',
            'brojIzvestajaPreview', 'brojIzvestajaLokacija', 'formatBroja', 'lokacijeOpcije',
            'proizvodjac_pib', 'proizvodjac_maticni', 'proizvodjac_naziv', 'proizvodjac_opstina',
            'proizvodjac_mesto', 'proizvodjac_postanski', 'proizvodjac_ulica', 'proizvodjac_telefon',
            'proizvodjac_faks', 'proizvodjac_email', 'vlasnik_tip', 'r_oznaka', 'd_oznaka',
            'dozvola_broj', 'dozvola_datum', 'datum_predaje', 'odgovorno_lice_b', 'telefon_lica_b',
            'prevoznik_pib', 'prevoznik_maticni', 'prevoznik_naziv', 'prevoznik_opstina',
            'prevoznik_mesto', 'prevoznik_postanski', 'prevoznik_ulica', 'prevoznik_telefon',
            'prevoznik_faks', 'prevoznik_email', 'vrsta_prevoznog_sredstva', 'registarski_broj',
            'lokacija_utovara', 'ruta_via_1', 'ruta_via_2', 'ruta_via_3', 'lokacija_istovara',
            'prevoznik_dozvola_broj', 'prevoznik_dozvola_datum', 'prevoznik_datum_prijema',
            'prevoznik_odgovorno_lice_prijem', 'prevoznik_telefon_lica_prijem',
            'prevoznik_datum_predaje', 'prevoznik_odgovorno_lice_predaja', 'prevoznik_telefon_lica_predaja',
            'primalac_pib', 'primalac_maticni', 'primalac_naziv', 'primalac_opstina',
            'primalac_mesto', 'primalac_postanski', 'primalac_ulica', 'primalac_telefon',
            'primalac_faks', 'primalac_email', 'primalac_tip', 'primalac_dozvola_broj',
            'primalac_dozvola_datum', 'primalac_datum_prijema', 'primalac_odgovorno_lice',
            'primalac_telefon_lica', 'operaterSearch', 'selectedOperaterId',
            'selectedOperaterName', 'operaterRezultati',
            'prevoznikOperaterSearch', 'selectedPrevoznikOperaterId',
            'selectedPrevoznikOperaterName', 'prevoznikOperaterRezultati',
        ]);

        $this->currentStep = 1;
        $this->vlasnik_tip = 'proizvodjac';
        $this->izabraniIds = [];
    }

    /** @return array<int, string> */
    private function oznake(string $prefix, int $count): array
    {
        return collect(range(1, $count))
            ->map(fn (int $n) => $prefix.$n)
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function pretraziOperatere(string $value, string $selectedName): array
    {
        if (strlen($value) < 2) {
            return [];
        }

        if ($value === $selectedName) {
            return [];
        }

        $query = Operater::aktivni()
            ->where(function ($q) use ($value) {
                $q->where('naziv', 'like', "%{$value}%")
                    ->orWhere('kratki_naziv', 'like', "%{$value}%")
                    ->orWhere('pib', 'like', "%{$value}%");
            });

        if ($this->lockedIndeksniBroj) {
            $indeks = $this->lockedIndeksniBroj;
            $query->where(function ($q) use ($indeks) {
                $q->whereNull('prihvata_indeksne_brojeve')
                    ->orWhereJsonContains('prihvata_indeksne_brojeve', $indeks);
            });
        }

        return $query
            ->orderBy('kratki_naziv')
            ->take(8)
            ->get(['id', 'kratki_naziv', 'naziv', 'pib', 'dozvola_vazi_do'])
            ->toArray();
    }

    private function primeniOperateraNaPrimaoca(Operater $op): void
    {
        $podaci = $op->dokoPodaci();

        $this->primalac_pib = $podaci['pib'];
        $this->primalac_maticni = $podaci['maticni'];
        $this->primalac_naziv = $podaci['naziv'];
        $this->primalac_opstina = $podaci['opstina'];
        $this->primalac_mesto = $podaci['mesto'];
        $this->primalac_postanski = $podaci['postanski'];
        $this->primalac_ulica = $podaci['ulica'];
        $this->primalac_telefon = $podaci['telefon'];
        $this->primalac_faks = $podaci['faks'];
        $this->primalac_email = $podaci['email'];
        $this->primalac_dozvola_broj = $podaci['dozvola_broj'];
        $this->primalac_dozvola_datum = $podaci['dozvola_datum'];
        $this->primalac_odgovorno_lice = $podaci['odgovorno_lice'];
        $this->primalac_telefon_lica = $podaci['telefon_lica'];
        $this->lokacija_istovara = $podaci['lokacija'];
        $this->odrediste = $podaci['mesto'] ?: $this->odrediste;
        $this->primalac_datum_prijema = $this->datum_predaje ?: now()->toDateString();

        if ($op->r_oznaka) {
            $this->r_oznaka = $op->r_oznaka;
        }
        if ($op->d_oznaka) {
            $this->d_oznaka = $op->d_oznaka;
        }

        $tip = collect($op->tip ?? [])->first();
        $this->primalac_tip = match ($tip) {
            'odlaganje' => 'odlaganje',
            'tretman', 'reciklaza' => 'tretman',
            'sakupljac', 'izvoz' => 'skladiste',
            default => $this->primalac_tip,
        };
    }

    private function primeniOperateraNaPrevoznika(Operater $op): void
    {
        $this->selectedPrevoznikOperaterId = $op->id;
        $this->selectedPrevoznikOperaterName = $op->kratki_naziv ?? $op->naziv;
        $this->prevoznikOperaterSearch = $this->selectedPrevoznikOperaterName;
        $this->prevoznikOperaterRezultati = [];

        $podaci = $op->dokoPodaci();
        $datum = $this->datum_predaje ?: now()->toDateString();

        $this->prevoznik_pib = $podaci['pib'];
        $this->prevoznik_maticni = $podaci['maticni'];
        $this->prevoznik_naziv = $podaci['naziv'];
        $this->prevoznik_opstina = $podaci['opstina'];
        $this->prevoznik_mesto = $podaci['mesto'];
        $this->prevoznik_postanski = $podaci['postanski'];
        $this->prevoznik_ulica = $podaci['ulica'];
        $this->prevoznik_telefon = $podaci['telefon'];
        $this->prevoznik_faks = $podaci['faks'];
        $this->prevoznik_email = $podaci['email'];
        $this->prevoznik_dozvola_broj = $podaci['dozvola_broj'];
        $this->prevoznik_dozvola_datum = $podaci['dozvola_datum'];
        $this->prevoznik_odgovorno_lice_prijem = $podaci['odgovorno_lice'];
        $this->prevoznik_odgovorno_lice_predaja = $podaci['odgovorno_lice'];
        $this->prevoznik_telefon_lica_prijem = $podaci['telefon_lica'];
        $this->prevoznik_telefon_lica_predaja = $podaci['telefon_lica'];
        $this->prevoznik_datum_prijema = $datum;
        $this->prevoznik_datum_predaje = $datum;
    }
}
