<?php

namespace App\Livewire\Admin;

use App\Actions\Admin\DeleteGodisnjiIzvestaj;
use App\Models\DnevnaEvidencija;
use App\Models\DokumentKretanja;
use App\Models\GodisnjIzvestaj;
use App\Models\Team;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Gio1Generator extends Component
{
    use WithPagination;

    public ?int $selectedTeamId = null;

    public int $selectedGodina;

    public bool $podaciUcitani = false;

    /** @var array<string, mixed> */
    public array $pregled = [];

    public ?string $otvorenaVrsta = null;

    public string $odgovornoLiceIme = '';

    public string $odgovornoLiceFunkcija = '';

    public string $odgovornoLiceTelefon = '';

    public string $liceOtpadIme = '';

    public string $liceOtpadFunkcija = '';

    public string $liceOtpadTelefon = '';

    public string $liceOtpadEmail = '';

    public ?int $confirmingDeleteId = null;

    public function mount(): void
    {
        $this->selectedGodina = now()->year - 1;
    }

    public function updatedSelectedTeamId(): void
    {
        $this->resetPregled();
    }

    public function updatedSelectedGodina(): void
    {
        $this->resetPregled();
    }

    public function toggleVrsta(string $indeksni): void
    {
        $this->otvorenaVrsta = $this->otvorenaVrsta === $indeksni ? null : $indeksni;
    }

    public function ucitajPodatke(): void
    {
        $this->validate([
            'selectedTeamId' => 'required|exists:teams,id',
            'selectedGodina' => 'required|integer|min:2020|max:2030',
        ], [
            'selectedTeamId.required' => 'Izaberite firmu.',
        ]);

        $indeksni = DnevnaEvidencija::where('team_id', $this->selectedTeamId)
            ->where('godina', $this->selectedGodina)
            ->distinct()
            ->orderBy('indeksni_broj')
            ->pluck('indeksni_broj');

        $vrsteOtpada = [];

        foreach ($indeksni as $ind) {
            $deoAgregat = DnevnaEvidencija::where('team_id', $this->selectedTeamId)
                ->where('indeksni_broj', $ind)
                ->where('godina', $this->selectedGodina)
                ->selectRaw('
                    MAX(naziv_otpada) as naziv_otpada,
                    MAX(karakter_otpada) as karakter_otpada,
                    MAX(fizicko_stanje) as fizicko_stanje,
                    SUM(proizvedena_kolicina) as ukupno_proizvedeno,
                    SUM(predata_kolicina) as ukupno_predato_deo,
                    MIN(nacin_odredjivanja) as nacin_odredjivanja
                ')
                ->first();

            $prvaEv = DnevnaEvidencija::where('team_id', $this->selectedTeamId)
                ->where('indeksni_broj', $ind)
                ->where('godina', $this->selectedGodina)
                ->orderBy('datum')
                ->orderBy('id')
                ->first();

            $poslednjaEv = DnevnaEvidencija::where('team_id', $this->selectedTeamId)
                ->where('indeksni_broj', $ind)
                ->where('godina', $this->selectedGodina)
                ->orderByDesc('datum')
                ->orderByDesc('id')
                ->first();

            $dokoDokumenti = DokumentKretanja::where('team_id', $this->selectedTeamId)
                ->where('indeksni_broj', $ind)
                ->whereYear('datum_predaje', $this->selectedGodina)
                ->orderBy('datum_predaje')
                ->orderBy('id')
                ->get([
                    'id', 'broj_dokumenta', 'datum_predaje', 'masa_ukupno',
                    'primalac_naziv', 'primalac_maticni', 'primalac_pib',
                    'primalac_dozvola_broj', 'r_oznaka', 'd_oznaka',
                ]);

            $ukupnoPredatoDoko = (float) $dokoDokumenti->sum('masa_ukupno');
            $ukupnoPredatoDeo = (float) ($deoAgregat?->ukupno_predato_deo ?? 0);

            $poOperateru = $dokoDokumenti
                ->groupBy(fn ($d) => $d->primalac_naziv ?: 'Nepoznat operater')
                ->map(fn ($group) => [
                    'naziv' => $group->first()->primalac_naziv ?? '',
                    'maticni' => $group->first()->primalac_maticni ?? '',
                    'pib' => $group->first()->primalac_pib ?? '',
                    'dozvola_broj' => $group->first()->primalac_dozvola_broj ?? '',
                    'r_oznaka' => $group->first()->r_oznaka ?? '',
                    'd_oznaka' => $group->first()->d_oznaka ?? '',
                    'ukupno_kolicina' => (float) $group->sum('masa_ukupno'),
                    'br_doko' => $group->count(),
                ])
                ->values()
                ->all();

            $vrsteOtpada[] = [
                'indeksni_broj' => $ind,
                'naziv_otpada' => $deoAgregat?->naziv_otpada ?? '',
                'karakter_otpada' => $deoAgregat?->karakter_otpada ?? 'neopasan',
                'fizicko_stanje' => $deoAgregat?->fizicko_stanje ?? '',
                'ukupno_proizvedeno' => (float) ($deoAgregat?->ukupno_proizvedeno ?? 0),
                'stanje_01_01' => (float) ($prvaEv?->stanje_na_skladistu ?? 0),
                'stanje_31_12' => (float) ($poslednjaEv?->stanje_na_skladistu ?? 0),
                'nacin_odredjivanja' => $deoAgregat?->nacin_odredjivanja ?? '1',
                'opis_otpada' => $prvaEv?->opis_otpada ?? '',
                'br_doko' => $dokoDokumenti->count(),
                'ukupno_predato' => $ukupnoPredatoDoko,
                'ukupno_predato_deo' => $ukupnoPredatoDeo,
                'zbir_ok' => abs($ukupnoPredatoDoko - $ukupnoPredatoDeo) < 0.05,
                'doko_dokumenti' => $dokoDokumenti->map(fn ($d) => [
                    'broj_dokumenta' => $d->broj_dokumenta,
                    'datum_predaje' => $d->datum_predaje?->format('d.m.Y.'),
                    'primalac_naziv' => $d->primalac_naziv,
                    'masa_ukupno' => (float) $d->masa_ukupno,
                ])->all(),
                'po_operateru' => $poOperateru,
            ];
        }

        $team = Team::find($this->selectedTeamId);

        $this->pregled = [
            'team_name' => $team?->name ?? '',
            'vrste_otpada' => $vrsteOtpada,
            'ukupno_doko' => collect($vrsteOtpada)->sum('br_doko'),
            'br_vrsta' => count($vrsteOtpada),
        ];

        if ($this->odgovornoLiceIme === '' && $team?->owner) {
            $this->odgovornoLiceIme = $team->owner->name ?? '';
        }

        $this->otvorenaVrsta = $vrsteOtpada[0]['indeksni_broj'] ?? null;
        $this->podaciUcitani = true;
    }

    public function generisi(): void
    {
        if (! $this->podaciUcitani) {
            $this->addError('podaci', 'Prvo učitajte podatke za izabranu firmu i godinu.');

            return;
        }

        $this->validate([
            'selectedTeamId' => 'required|exists:teams,id',
            'selectedGodina' => 'required|integer',
            'odgovornoLiceIme' => 'required|string|max:255',
            'liceOtpadIme' => 'required|string|max:255',
        ], [
            'odgovornoLiceIme.required' => 'Unesite ime odgovornog lica.',
            'liceOtpadIme.required' => 'Unesite ime lica za upravljanje otpadom.',
        ]);

        GodisnjIzvestaj::updateOrCreate(
            [
                'team_id' => $this->selectedTeamId,
                'godina' => $this->selectedGodina,
            ],
            [
                'created_by' => auth()->id(),
                'status' => 'generisan',
                'generisan_at' => now(),
                'odgovorno_lice_ime' => $this->odgovornoLiceIme,
                'odgovorno_lice_funkcija' => $this->odgovornoLiceFunkcija ?: null,
                'odgovorno_lice_telefon' => $this->odgovornoLiceTelefon ?: null,
                'lice_otpad_ime' => $this->liceOtpadIme,
                'lice_otpad_funkcija' => $this->liceOtpadFunkcija ?: null,
                'lice_otpad_telefon' => $this->liceOtpadTelefon ?: null,
                'lice_otpad_email' => $this->liceOtpadEmail ?: null,
            ]
        );

        session()->flash(
            'success',
            "GIO1 za {$this->pregled['br_vrsta']} vrst(e) otpada je sačuvan."
        );
        $this->resetPage();
        $this->dispatch('izvestajGenerisan');
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function obrisi(int $id, DeleteGodisnjiIzvestaj $deleter): void
    {
        abort_unless(auth()->user()?->is_super_admin, 403);

        $deleter->delete(GodisnjIzvestaj::findOrFail($id));

        $this->confirmingDeleteId = null;
        session()->flash('success', 'GIO1 izveštaj je obrisan.');
        $this->resetPage();
    }

    #[On('izvestajGenerisan')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    private function resetPregled(): void
    {
        $this->podaciUcitani = false;
        $this->pregled = [];
        $this->otvorenaVrsta = null;
    }

    public function render()
    {
        return view('livewire.admin.gio1-generator', [
            'timovi' => Team::orderBy('name')->get(['id', 'name', 'pib']),
            'izvestaji' => GodisnjIzvestaj::with('team')->latest('generisan_at')->latest()->paginate(15),
        ]);
    }
}
