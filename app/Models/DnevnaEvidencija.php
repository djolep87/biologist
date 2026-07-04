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
        'godina',
        'mesec',
        'indeksni_broj',
        'naziv_otpada',
        'opis_otpada',
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
    public static function recalculateStanjeZaIndeks(?int $teamId, string $indeksniBroj): void
    {
        $records = static::forTeam($teamId)
            ->where('indeksni_broj', $indeksniBroj)
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
                2
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

    public static function katalogOtpada(): array
    {
        return [
            '15 01 01' => 'Papirna i kartonska ambalaža',
            '15 01 02' => 'Plastična ambalaža',
            '15 01 04' => 'Metalna ambalaža',
            '15 01 07' => 'Staklena ambalaža',
            '20 01 01' => 'Papir i karton',
            '20 01 02' => 'Staklo',
            '20 01 08' => 'Biorazgradivi otpad iz kuhinja',
            '20 01 21' => 'Fluorescentne cevi (sadrže živu)',
            '20 01 33' => 'Baterije i akumulatori',
            '13 02 05' => 'Mineralna neklorisana motorna ulja',
            '13 02 06' => 'Sintetička motorna ulja',
            '16 01 03' => 'Istrošene gume',
            '17 04 05' => 'Gvožđe i čelik',
            '20 03 01' => 'Mešani komunalni otpad',
            '08 01 11' => 'Otpadne boje i lakovi',
        ];
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
        $activityQuery = static::forTeam($teamId);

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
        $skladisteQuery = static::forTeam($teamId);

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
