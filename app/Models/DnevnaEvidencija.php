<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DnevnaEvidencija extends Model
{
    use SoftDeletes;

    protected $table = 'dnevne_evidencije';

    protected $fillable = [
        'team_id',
        'user_id',
        'construction_site_id',
        'godina',
        'mesec',
        'indeksni_broj',
        'naziv_otpada',
        'opis_otpada',
        'nacin_nastanka',
        'napomena',
        'karakter_otpada',
        'fizicko_stanje',
        'lice_koje_vodi',
        'datum',
        'proizvedena_kolicina',
        'predata_kolicina',
        'stanje_na_skladistu',
        'predat_sakupljacu',
        'predat_operateru_r',
        'predat_operateru_d',
        'r_oznaka',
        'd_oznaka',
        'izvoz',
        'predat_operateru',
        'dokument_kretanja_id',
        'datum_predaje_operateru',
        'operater_naziv',
        'operater_dozvola_broj',
        'naziv_primaoca',
        'broj_dozvole_primaoca',
        'nacin_odredjivanja',
        'dko_status',
        'gradjevinski_dko_zahtev_id',
    ];

    protected function casts(): array
    {
        return [
            'datum' => 'date',
            'proizvedena_kolicina' => 'decimal:2',
            'predata_kolicina' => 'decimal:2',
            'stanje_na_skladistu' => 'decimal:2',
            'predat_sakupljacu' => 'boolean',
            'predat_operateru_r' => 'boolean',
            'predat_operateru_d' => 'boolean',
            'izvoz' => 'boolean',
            'predat_operateru' => 'boolean',
            'datum_predaje_operateru' => 'date',
        ];
    }

    public function dokumentKretanja(): BelongsTo
    {
        return $this->belongsTo(DokumentKretanja::class, 'dokument_kretanja_id');
    }

    public function getStatusPredajeAttribute(): string
    {
        if ($this->predat_operateru && $this->dokument_kretanja_id) {
            return 'predato';
        }

        return 'ceka';
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function constructionSite(): BelongsTo
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }

    public function gradjevinskiDkoZahtev(): BelongsTo
    {
        return $this->belongsTo(GradjevinskiDkoZahtev::class, 'gradjevinski_dko_zahtev_id');
    }

    public function isGradjevinski(): bool
    {
        return ! is_null($this->construction_site_id);
    }

    public function scopeObicna(Builder $query): Builder
    {
        return $query->whereNull('construction_site_id');
    }

    public function scopeGradjevinska(Builder $query): Builder
    {
        return $query->whereNotNull('construction_site_id');
    }

    public function scopeForConstructionSite(Builder $query, int $siteId): Builder
    {
        return $query->where('construction_site_id', $siteId);
    }

    public function scopeSlobodan(Builder $query): Builder
    {
        return $query->where('dko_status', 'slobodan');
    }

    public function scopeUZahtevu(Builder $query): Builder
    {
        return $query->where('dko_status', 'u_zahtevu');
    }

    public function zahtevi(): BelongsToMany
    {
        return $this->belongsToMany(
            ZahtevPredaje::class,
            'zahtev_evidencija',
            'dnevna_evidencija_id',
            'zahtev_predaje_id'
        );
    }

    public function getUAktivnomZahtevu(): bool
    {
        return $this->zahtevi()
            ->whereIn('status', ['na_cekanju', 'u_obradi'])
            ->exists();
    }

    public function getOdbijenZahtev(): ?ZahtevPredaje
    {
        return $this->zahtevi()
            ->where('status', 'odbijeno')
            ->latest('zahtevi_predaje.id')
            ->first();
    }

    /**
     * Preračunava kumulativno stanje_na_skladistu za sve zapise jednog indeksa (hronološki).
     */
    public static function recalculateStanjeZaIndeks(?int $teamId, string $indeksniBroj, ?int $constructionSiteId = null): void
    {
        $records = static::forTeam($teamId)
            ->where('indeksni_broj', $indeksniBroj)
            ->when(
                $constructionSiteId !== null,
                fn (Builder $q) => $q->where('construction_site_id', $constructionSiteId),
                fn (Builder $q) => $q->whereNull('construction_site_id')
            )
            ->orderBy('datum')
            ->orderBy('id')
            ->get();

        $stanje = 0.0;

        foreach ($records as $record) {
            if ($record->predat_operateru) {
                $stanje = 0.0;

                if ((float) $record->stanje_na_skladistu !== 0.0) {
                    $record->updateQuietly(['stanje_na_skladistu' => 0]);
                }

                continue;
            }

            $stanje = round(
                $stanje + (float) $record->proizvedena_kolicina - (float) $record->predata_kolicina,
                3
            );

            if ((float) $record->stanje_na_skladistu !== $stanje) {
                $record->updateQuietly(['stanje_na_skladistu' => $stanje]);
            }
        }
    }

    public function scopeForTeam(Builder $query, ?int $teamId = null): Builder
    {
        $teamId ??= Auth::user()?->currentTeam?->id;

        return $query->when($teamId, fn (Builder $q) => $q->where('team_id', $teamId));
    }

    public function scopeForMonth(Builder $query, int $godina, int $mesec): Builder
    {
        return $query->where('godina', $godina)->where('mesec', $mesec);
    }

    public function getKarakterBadgeColorAttribute(): string
    {
        return match ($this->karakter_otpada) {
            'inertan' => 'blue',
            'neopasan' => 'green',
            'opasan' => 'red',
            default => 'gray',
        };
    }

    /**
     * Katalog otpada za brzi izbor u obrascu, grupisan po vrsti otpada.
     *
     * Nazivi su preuzeti iz Kataloga otpada (Pravilnik o kategorijama, ispitivanju
     * i klasifikaciji otpada) i upisuju se u obrazac takvi kakvi jesu. „Napomena" je
     * samo pomoć pri izboru — prikazuje se u listi, ali se ne upisuje u evidenciju.
     * Više šifara deli isti zvanični naziv (npr. „materijali nepodobni za potrošnju
     * ili obradu"), pa napomena govori na koju delatnost se šifra odnosi.
     *
     * Polje i dalje prima i šifre kojih nema u listi — katalog je pomoć, ne ograničenje.
     *
     * @return array<string, array<string, array{naziv: string, napomena?: string, opasan?: bool}>>
     */
    public static function katalogOtpadaGrupisano(): array
    {
        return [
            'Ambalažni otpad' => [
                '15 01 01' => ['naziv' => 'Papirna i kartonska ambalaža'],
                '15 01 02' => ['naziv' => 'Plastična ambalaža'],
                '15 01 04' => ['naziv' => 'Metalna ambalaža'],
                '15 01 07' => ['naziv' => 'Staklena ambalaža'],
            ],
            'Otpad od hrane i pića' => [
                '02 01 02' => ['naziv' => 'Otpad od životinjskog tkiva', 'napomena' => 'poljoprivreda, stočarstvo, ribolov'],
                '02 01 03' => ['naziv' => 'Otpad od biljnog tkiva', 'napomena' => 'poljoprivreda, hortikultura'],
                '02 02 02' => ['naziv' => 'Otpad od životinjskog tkiva', 'napomena' => 'priprema i obrada mesa i ribe'],
                '02 02 03' => ['naziv' => 'Materijali nepodobni za potrošnju ili obradu', 'napomena' => 'meso, riba, hrana životinjskog porekla'],
                '02 03 01' => ['naziv' => 'Muljevi od pranja, čišćenja, ljuštenja, centrifugiranja i separacije', 'napomena' => 'voće, povrće, žitarice'],
                '02 03 04' => ['naziv' => 'Materijali nepodobni za potrošnju ili obradu', 'napomena' => 'voće, povrće, žitarice, jestiva ulja, kafa, čaj'],
                '02 04 01' => ['naziv' => 'Zemlja od čišćenja i pranja šećerne repe', 'napomena' => 'prerada šećera'],
                '02 05 01' => ['naziv' => 'Materijali nepodobni za potrošnju ili obradu', 'napomena' => 'mlekare i mlečni proizvodi'],
                '02 06 01' => ['naziv' => 'Materijali nepodobni za potrošnju ili obradu', 'napomena' => 'pekare i konditorska industrija'],
                '02 07 01' => ['naziv' => 'Otpadi od pranja, čišćenja i mehaničkog tretmana sirovog materijala', 'napomena' => 'proizvodnja pića'],
                '02 07 02' => ['naziv' => 'Otpadi od destilacije alkohola'],
                '02 07 04' => ['naziv' => 'Materijali nepodobni za potrošnju ili obradu', 'napomena' => 'alkoholna i bezalkoholna pića'],
                '20 01 08' => ['naziv' => 'Biorazgradivi kuhinjski i otpad iz restorana', 'napomena' => 'kuhinje, kantine, ugostiteljstvo'],
                '20 01 25' => ['naziv' => 'Jestiva ulja i masti', 'napomena' => 'korišćeno ulje iz kuhinje'],
                '20 03 02' => ['naziv' => 'Otpad sa pijaca'],
            ],
            'Roba van specifikacije i sa isteklim rokom' => [
                '16 03 06' => ['naziv' => 'Organski otpadi drugačiji od onih navedenih u 16 03 05', 'napomena' => 'hrana i piće sa isteklim rokom, bez opasnih supstanci'],
                '16 03 04' => ['naziv' => 'Neorganski otpadi drugačiji od onih navedenih u 16 03 03', 'napomena' => 'neorganska roba van roka, bez opasnih supstanci'],
                '16 03 05' => ['naziv' => 'Organski otpadi koji sadrže opasne supstance', 'napomena' => 'organska roba van roka, sa opasnim supstancama', 'opasan' => true],
                '16 03 03' => ['naziv' => 'Neorganski otpadi koji sadrže opasne supstance', 'napomena' => 'neorganska roba van roka, sa opasnim supstancama', 'opasan' => true],
            ],
            'Komunalni otpad' => [
                '20 01 01' => ['naziv' => 'Papir i karton'],
                '20 01 02' => ['naziv' => 'Staklo'],
                '20 01 21' => ['naziv' => 'Fluorescentne cevi i drugi otpad koji sadrži živu', 'opasan' => true],
                '20 01 33' => ['naziv' => 'Baterije i akumulatori', 'opasan' => true],
                '20 02 01' => ['naziv' => 'Biodegradabilni otpad', 'napomena' => 'bašte i parkovi'],
                '20 03 01' => ['naziv' => 'Mešani komunalni otpad'],
            ],
            'Ulja, gume i ostalo' => [
                '08 01 11' => ['naziv' => 'Otpadna boja i lak koji sadrže organske rastvarače ili druge opasne supstance', 'opasan' => true],
                '13 02 05' => ['naziv' => 'Mineralna nehlorovana motorna ulja, ulja za menjače i podmazivanje', 'opasan' => true],
                '13 02 06' => ['naziv' => 'Sintetička motorna ulja, ulja za menjače i podmazivanje', 'opasan' => true],
                '16 01 03' => ['naziv' => 'Otpadne gume'],
                '17 04 05' => ['naziv' => 'Gvožđe i čelik'],
            ],
        ];
    }

    /**
     * Ravna mapa šifra => naziv (koristi se za automatsko popunjavanje naziva otpada).
     *
     * @return array<string, string>
     */
    public static function katalogOtpada(): array
    {
        $katalog = [];

        foreach (static::katalogOtpadaGrupisano() as $stavke) {
            foreach ($stavke as $sifra => $stavka) {
                $katalog[$sifra] = $stavka['naziv'];
            }
        }

        ksort($katalog);

        return $katalog;
    }

    /**
     * Jedna stavka kataloga po indeksnom broju — null ako šifre nema u listi.
     *
     * @return array{naziv: string, napomena?: string, opasan?: bool}|null
     */
    public static function katalogStavka(string $sifra): ?array
    {
        foreach (static::katalogOtpadaGrupisano() as $stavke) {
            if (isset($stavke[$sifra])) {
                return $stavke[$sifra];
            }
        }

        return null;
    }

    public static function ukupnoNaSkladistu(?int $teamId = null, ?int $godina = null, ?int $mesec = null): float
    {
        return (float) static::summariesByIndeks($teamId, $godina, $mesec)->sum('stanje');
    }

    /**
     * Stanje na skladištu = zbir poslednjeg stanje_na_skladistu po indeksnom broju.
     * Uključuje otpad na čekanju, u odbijenom zahtevu itd. (predat_operateru = false).
     */
    public static function calculateSkladisteStanje(Builder $query): float
    {
        $records = (clone $query)
            ->orderBy('indeksni_broj')
            ->orderBy('datum')
            ->orderBy('id')
            ->get(['indeksni_broj', 'stanje_na_skladistu']);

        return (float) $records
            ->groupBy('indeksni_broj')
            ->sum(fn (Collection $group) => (float) $group->last()->stanje_na_skladistu);
    }

    /**
     * Agregat za dashboard kartice — jedan izvor istine za period.
     *
     * - proizvedena: SUM(proizvedena_kolicina) u aktivnom periodu (godina / mesec)
     * - predata:     SUM(predata_kolicina) samo gde je predat_operateru = true
     *                (stvarna predaja sa DOKO; NE uključuje čeka / u zahtevu / odbijeno)
     * - stanje:      zbir poslednjeg stanje_na_skladistu po indeksu na kraj perioda
     *                (snapshot; uključuje odbijene zahteve i nenpredatu proizvodnju)
     * - broj_unosa:  COUNT(*) u aktivnom periodu
     *
     * Napomena: stanje ≠ proizvedena − predata kada se predaje akumulirano skladište
     * ili postoji početno stanje iz prethodnog perioda.
     */
    public static function calculatePeriodTotals(Builder $activityQuery, Builder $skladisteQuery): array
    {
        $proizvedena = (float) (clone $activityQuery)->sum('proizvedena_kolicina');

        // Samo potvrđene predaje (DOKO kreiran). Odbijeni / na čekanju / u zahtevu = isključeni.
        $predata = (float) (clone $activityQuery)
            ->where('predat_operateru', true)
            ->sum('predata_kolicina');

        $stanje = static::calculateSkladisteStanje($skladisteQuery);
        $broj_unosa = (int) (clone $activityQuery)->count();

        return [
            'proizvedena' => $proizvedena,
            'predata' => $predata,
            'stanje' => $stanje,
            'broj_unosa' => $broj_unosa,
        ];
    }

    /**
     * @return Collection<int, object{indeksni_broj: string, naziv_otpada: string, karakter_otpada: string, broj_unosa: int, proizvedeno: float, predato: float, stanje: float}>
     */
    public static function summariesByIndeks(?int $teamId = null, ?int $godina = null, ?int $mesec = null): Collection
    {
        $activityQuery = static::forTeam($teamId)->obicna();

        if ($godina !== null) {
            $activityQuery->where('godina', $godina);
        }

        if ($mesec !== null) {
            $activityQuery->where('mesec', $mesec);
        }

        $activityRecords = $activityQuery
            ->orderBy('indeksni_broj')
            ->orderBy('datum')
            ->orderBy('id')
            ->get();

        // Snapshot skladišta na kraj perioda (YTD ako je izabran mesec)
        $skladisteQuery = static::forTeam($teamId)->obicna();

        if ($godina !== null) {
            $skladisteQuery->where('godina', $godina);
        }

        if ($mesec !== null) {
            $skladisteQuery->where('mesec', '<=', $mesec);
        }

        $stanjePoIndeksu = $skladisteQuery
            ->orderBy('indeksni_broj')
            ->orderBy('datum')
            ->orderBy('id')
            ->get()
            ->groupBy('indeksni_broj')
            ->map(fn (Collection $group) => (float) $group->last()->stanje_na_skladistu);

        return $activityRecords->groupBy('indeksni_broj')->map(function (Collection $group) use ($stanjePoIndeksu) {
            $first = $group->first();
            $indeks = $first->indeksni_broj;

            return (object) [
                'indeksni_broj' => $indeks,
                'naziv_otpada' => $first->naziv_otpada,
                'karakter_otpada' => $first->karakter_otpada,
                'broj_unosa' => $group->count(),
                'proizvedeno' => (float) $group->sum('proizvedena_kolicina'),
                // Samo potvrđene predaje (predat_operateru); odbijeno/čeka se ne računa
                'predato' => (float) $group->where('predat_operateru', true)->sum('predata_kolicina'),
                // Poslednje kumulativno stanje na kraj perioda (uključuje odbijene zahteve)
                'stanje' => $stanjePoIndeksu->get($indeks, 0.0),
            ];
        })->sortBy('indeksni_broj')->values();
    }

    public static function formatKolicina(float|string $value): string
    {
        $value = (float) $value;
        $formatted = $value >= 10
            ? number_format($value, 0, ',', '.')
            : number_format($value, 1, ',', '.');

        return $formatted.' t';
    }
}
