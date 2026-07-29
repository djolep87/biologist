<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DokumentKretanja extends Model
{
    use SoftDeletes;

    protected $table = 'dokumenti_kretanja';

    protected $fillable = [
        'team_id',
        'user_id',
        'construction_site_id',
        'broj_gradevinske_dozvole_dko',
        'broj_dokumenta',
        'broj_izvestaja',
        'redni_broj',
        'format_broja',
        'lokacija_oznaka',
        'indeksni_broj',
        'vrsta_otpada',
        'q_lista',
        'masa_ukupno',
        'nacin_pakovanja',
        'fizicko_stanje',
        'izvestaj_broj',
        'izvestaj_datum',
        'odrediste',
        'vid_prevoza',
        'posebne_napomene',
        'proizvodjac_pib',
        'proizvodjac_maticni',
        'proizvodjac_naziv',
        'proizvodjac_opstina',
        'proizvodjac_mesto',
        'proizvodjac_postanski',
        'proizvodjac_ulica',
        'proizvodjac_telefon',
        'proizvodjac_faks',
        'proizvodjac_email',
        'vlasnik_tip',
        'r_oznaka',
        'd_oznaka',
        'dozvola_broj',
        'dozvola_datum',
        'datum_predaje',
        'odgovorno_lice_b',
        'telefon_lica_b',
        'prevoznik_pib',
        'prevoznik_maticni',
        'prevoznik_naziv',
        'prevoznik_opstina',
        'prevoznik_mesto',
        'prevoznik_postanski',
        'prevoznik_ulica',
        'prevoznik_telefon',
        'prevoznik_faks',
        'prevoznik_email',
        'vrsta_prevoznog_sredstva',
        'registarski_broj',
        'lokacija_utovara',
        'ruta_via_1',
        'ruta_via_2',
        'ruta_via_3',
        'lokacija_istovara',
        'prevoznik_dozvola_broj',
        'prevoznik_dozvola_datum',
        'prevoznik_datum_prijema',
        'prevoznik_odgovorno_lice_prijem',
        'prevoznik_telefon_lica_prijem',
        'prevoznik_datum_predaje',
        'prevoznik_odgovorno_lice_predaja',
        'prevoznik_telefon_lica_predaja',
        'primalac_pib',
        'primalac_maticni',
        'primalac_naziv',
        'primalac_opstina',
        'primalac_mesto',
        'primalac_postanski',
        'primalac_ulica',
        'primalac_telefon',
        'primalac_faks',
        'primalac_email',
        'primalac_tip',
        'primalac_dozvola_broj',
        'primalac_dozvola_datum',
        'primalac_datum_prijema',
        'primalac_odgovorno_lice',
        'primalac_telefon_lica',
    ];

    protected function casts(): array
    {
        return [
            'datum_predaje' => 'date',
            'izvestaj_datum' => 'date',
            'dozvola_datum' => 'date',
            'prevoznik_dozvola_datum' => 'date',
            'prevoznik_datum_prijema' => 'date',
            'prevoznik_datum_predaje' => 'date',
            'primalac_dozvola_datum' => 'date',
            'primalac_datum_prijema' => 'date',
            'masa_ukupno' => 'decimal:3',
            'redni_broj' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DokumentKretanja $model) {
            if ($model->broj_dokumenta) {
                return;
            }

            $year = now()->year;
            $count = static::withTrashed()
                ->where('team_id', $model->team_id)
                ->whereYear('created_at', $year)
                ->count() + 1;

            $model->broj_dokumenta = sprintf('DOKO-%d-%05d', $year, $count);
        });
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

    public function dnevneEvidencije(): HasMany
    {
        return $this->hasMany(DnevnaEvidencija::class, 'dokument_kretanja_id');
    }

    public function isGradjevinski(): bool
    {
        return ! is_null($this->construction_site_id);
    }

    public function scopeObicni(Builder $query): Builder
    {
        return $query->whereNull('construction_site_id');
    }

    public function scopeGradjevinski(Builder $query): Builder
    {
        return $query->whereNotNull('construction_site_id');
    }

    public function scopeForTeam(Builder $query, ?int $teamId = null): Builder
    {
        $teamId ??= Auth::user()?->currentTeam?->id;

        return $query->when($teamId, fn (Builder $q) => $q->where('team_id', $teamId));
    }

    public function getLokacijaNastankaZaExportAttribute(): string
    {
        $lokacija = trim((string) ($this->lokacija_utovara ?: $this->proizvodjac_ulica));
        $dozvola = trim((string) ($this->broj_gradevinske_dozvole_dko ?? ''));

        if (! $this->isGradjevinski() || $dozvola === '') {
            return $lokacija;
        }

        return $lokacija."\nBroj građevinske dozvole: ".$dozvola;
    }
}
