<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ConstructionSite extends Model
{
    use SoftDeletes;

    public const STATUS_AKTIVNO = 'aktivno';

    public const STATUS_ZAVRSENO = 'završeno';

    public const STATUS_PAUZIRANO = 'pauzirano';

    public const FAKTORI_TONAZE = [
        'rusenje' => 0.45,
        'gradnja' => 0.30,
        'rekonstrukcija' => 0.35,
        'sanacija' => 0.20,
    ];

    protected $fillable = [
        'team_id',
        'naziv_gradilista',
        'broj_gradevinske_dozvole',
        'adresa_gradilista',
        'mesto',
        'opstina',
        'katastarska_parcela',
        'investitor_naziv',
        'izvodjac_naziv',
        'status',
        'datum_pocetka',
        'planirani_zavrsetak',
        'datum_zavrsetka',
        'kvadratura_objekta',
        'tip_radova',
        'procijenjena_kolicina_otpada',
        'operater_naziv',
        'operater_pib',
        'operater_adresa',
        'operater_dozvola_broj',
    ];

    protected function casts(): array
    {
        return [
            'datum_pocetka' => 'date',
            'planirani_zavrsetak' => 'date',
            'datum_zavrsetka' => 'date',
            'kvadratura_objekta' => 'decimal:2',
            'procijenjena_kolicina_otpada' => 'decimal:3',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function dnevneEvidencije(): HasMany
    {
        return $this->hasMany(DnevnaEvidencija::class, 'construction_site_id');
    }

    public function dokumentiKretanja(): HasMany
    {
        return $this->hasMany(DokumentKretanja::class, 'construction_site_id');
    }

    public function scopeAktivna(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AKTIVNO);
    }

    public function scopeZavrsena(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ZAVRSENO);
    }

    public function scopeForTeam(Builder $query, ?int $teamId = null): Builder
    {
        $teamId ??= Auth::user()?->currentTeam?->id;

        return $query->when($teamId, fn (Builder $q) => $q->where('team_id', $teamId));
    }

    public function getProcijenjenaTonazaAttribute(): ?float
    {
        if ($this->kvadratura_objekta === null || $this->tip_radova === null) {
            return $this->procijenjena_kolicina_otpada !== null
                ? (float) $this->procijenjena_kolicina_otpada
                : null;
        }

        $faktor = self::FAKTORI_TONAZE[$this->tip_radova] ?? null;

        if ($faktor === null) {
            return null;
        }

        return round((float) $this->kvadratura_objekta * $faktor, 3);
    }

    public static function izracunajProcenu(float $kvadratura, string $tipRadova): ?float
    {
        $faktor = self::FAKTORI_TONAZE[$tipRadova] ?? null;

        if ($faktor === null || $kvadratura <= 0) {
            return null;
        }

        return round($kvadratura * $faktor, 3);
    }

    public function isZavrseno(): bool
    {
        return $this->status === self::STATUS_ZAVRSENO;
    }

    public function isAktivno(): bool
    {
        return $this->status === self::STATUS_AKTIVNO;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AKTIVNO => 'green',
            self::STATUS_ZAVRSENO => 'gray',
            self::STATUS_PAUZIRANO => 'orange',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AKTIVNO => 'AKTIVNO',
            self::STATUS_ZAVRSENO => 'ZAVRŠENO',
            self::STATUS_PAUZIRANO => 'PAUZIRANO',
            default => strtoupper($this->status),
        };
    }

    public function getTipRadovaLabelAttribute(): string
    {
        return match ($this->tip_radova) {
            'rusenje' => 'Rušenje',
            'gradnja' => 'Gradnja',
            'rekonstrukcija' => 'Rekonstrukcija',
            'sanacija' => 'Sanacija',
            default => $this->tip_radova ?? '—',
        };
    }

    public function getPunaAdresaAttribute(): string
    {
        return collect([$this->adresa_gradilista, $this->mesto, $this->opstina])
            ->filter()
            ->implode(', ');
    }

    public function getLokacijaNastankaSaDozvolomAttribute(): string
    {
        $lokacija = $this->puna_adresa;
        $dozvola = trim((string) $this->broj_gradevinske_dozvole);

        if ($dozvola === '') {
            return $lokacija;
        }

        return $lokacija."\nBroj građevinske dozvole: ".$dozvola;
    }

    public function ukupnoProizvedeno(): float
    {
        return (float) $this->dnevneEvidencije()->sum('proizvedena_kolicina');
    }

    public function ukupnoPredato(): float
    {
        return (float) $this->dnevneEvidencije()
            ->where('predat_operateru', true)
            ->sum('predata_kolicina');
    }

    public function naGradilistu(): float
    {
        return round($this->ukupnoProizvedeno() - $this->ukupnoPredato(), 3);
    }
}
