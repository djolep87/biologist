<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradjevinskiDkoZahtev extends Model
{
    use SoftDeletes;

    protected $table = 'gradjevinski_dko_zahtevi';

    public const STATUS_NA_CEKANJU = 'na_cekanju';

    public const STATUS_U_OBRADI = 'u_obradi';

    public const STATUS_ZAVRSENO = 'zavrseno';

    public const STATUS_ODBIJENO = 'odbijeno';

    protected $fillable = [
        'team_id',
        'construction_site_id',
        'kreirao_korisnik_id',
        'obradio_admin_id',
        'dokument_kretanja_id',
        'broj_zahteva',
        'redni_broj',
        'status',
        'masa_ukupno',
        'napomena_klijenta',
        'napomena_admina',
        'razlog_odbijanja',
        'poslato_at',
        'preuzeto_admin_at',
        'zavrseno_at',
    ];

    protected function casts(): array
    {
        return [
            'masa_ukupno' => 'decimal:3',
            'redni_broj' => 'integer',
            'poslato_at' => 'datetime',
            'preuzeto_admin_at' => 'datetime',
            'zavrseno_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function constructionSite(): BelongsTo
    {
        return $this->belongsTo(ConstructionSite::class);
    }

    public function kreirao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kreirao_korisnik_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'obradio_admin_id');
    }

    public function dokumentKretanja(): BelongsTo
    {
        return $this->belongsTo(DokumentKretanja::class, 'dokument_kretanja_id');
    }

    public function evidencije(): BelongsToMany
    {
        return $this->belongsToMany(
            DnevnaEvidencija::class,
            'gradjevinski_dko_zahtev_evidencija',
            'gradjevinski_dko_zahtev_id',
            'dnevna_evidencija_id'
        )->withTimestamps();
    }

    public function scopeForTeam(Builder $query, ?int $teamId = null): Builder
    {
        $teamId ??= Auth::user()?->currentTeam?->id;

        return $query->when($teamId, fn (Builder $q) => $q->where('team_id', $teamId));
    }

    public function scopeNaCekanju(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_NA_CEKANJU);
    }

    public function scopeUObradi(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_U_OBRADI);
    }

    public function scopeZavrseni(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ZAVRSENO);
    }

    public function scopeAktivni(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_NA_CEKANJU, self::STATUS_U_OBRADI]);
    }

    public function getUkupnaKolicinaAttribute(): float
    {
        if ($this->relationLoaded('evidencije')) {
            return round((float) $this->evidencije->sum('proizvedena_kolicina'), 3);
        }

        return (float) $this->masa_ukupno;
    }

    public function getBrojVrstaOtpadaAttribute(): int
    {
        return $this->evidencije->pluck('indeksni_broj')->unique()->count();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NA_CEKANJU => '⏳ Na čekanju',
            self::STATUS_U_OBRADI => '🔄 U obradi',
            self::STATUS_ZAVRSENO => '✅ Završeno',
            self::STATUS_ODBIJENO => '❌ Odbijeno',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_NA_CEKANJU => ['label' => '⏳ Na čekanju', 'class' => 'bg-amber-100 text-amber-800'],
            self::STATUS_U_OBRADI => ['label' => '🔄 U obradi', 'class' => 'bg-blue-100 text-blue-800 animate-pulse'],
            self::STATUS_ZAVRSENO => ['label' => '✅ Završeno', 'class' => 'bg-green-100 text-green-800'],
            self::STATUS_ODBIJENO => ['label' => '❌ Odbijeno', 'class' => 'bg-red-100 text-red-800'],
            default => ['label' => $this->status, 'class' => 'bg-gray-100 text-gray-800'],
        };
    }

    public static function generateBrojZahteva(int $teamId): array
    {
        $year = now()->year;

        $last = static::withTrashed()
            ->where('team_id', $teamId)
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->orderByDesc('redni_broj')
            ->first();

        $redni = ((int) ($last?->redni_broj ?? 0)) + 1;
        $broj = sprintf('ZHT-%03d/%d', $redni, $year);

        return [
            'broj_zahteva' => $broj,
            'redni_broj' => $redni,
        ];
    }

    public function oslobodiEvidencije(): void
    {
        $ids = $this->evidencije()->pluck('dnevne_evidencije.id');

        DnevnaEvidencija::whereIn('id', $ids)->update([
            'dko_status' => 'slobodan',
            'gradjevinski_dko_zahtev_id' => null,
        ]);

        $this->evidencije()->detach();
    }

    public function oznaciZavrseno(DokumentKretanja $dokument, ?string $napomenaAdmina = null): void
    {
        DB::transaction(function () use ($dokument, $napomenaAdmina) {
            $this->update([
                'status' => self::STATUS_ZAVRSENO,
                'dokument_kretanja_id' => $dokument->id,
                'obradio_admin_id' => auth()->id(),
                'napomena_admina' => $napomenaAdmina ?: $this->napomena_admina,
                'zavrseno_at' => now(),
            ]);

            $this->evidencije()->update([
                'dko_status' => 'dko_kreiran',
            ]);
        });
    }
}
