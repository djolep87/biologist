<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operater extends Model
{
    use SoftDeletes;

    protected $table = 'operateri';

    protected $fillable = [
        'created_by',
        'naziv',
        'pib',
        'maticni_broj',
        'kratki_naziv',
        'opstina',
        'mesto',
        'postanski_broj',
        'ulica',
        'telefon',
        'faks',
        'email',
        'kontakt_osoba',
        'dozvola_broj',
        'dozvola_datum_izdavanja',
        'dozvola_vazi_do',
        'prihvata_indeksne_brojeve',
        'tip',
        'r_oznaka',
        'd_oznaka',
        'aktivan',
        'napomena',
    ];

    protected function casts(): array
    {
        return [
            'dozvola_datum_izdavanja' => 'date',
            'dozvola_vazi_do' => 'date',
            'prihvata_indeksne_brojeve' => 'array',
            'tip' => 'array',
            'aktivan' => 'boolean',
        ];
    }

    public function getDozvalaIsticeAttribute(): bool
    {
        return $this->dozvola_vazi_do
            && $this->dozvola_vazi_do->isFuture()
            && $this->dozvola_vazi_do->diffInDays(now()) <= 30;
    }

    public function getDozvalaIsteklaAttribute(): bool
    {
        return $this->dozvola_vazi_do
            && $this->dozvola_vazi_do->isPast();
    }

    public function scopeAktivni($query)
    {
        return $query->where('aktivan', true);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function tipLabels(): array
    {
        return [
            'sakupljac' => 'Sakupljač',
            'reciklaza' => 'Reciklaža',
            'odlaganje' => 'Odlaganje',
            'tretman' => 'Tretman',
            'izvoz' => 'Izvoz',
        ];
    }

    public function tipDisplay(): string
    {
        $labels = static::tipLabels();

        return collect($this->tip ?? [])
            ->map(fn (string $t) => $labels[$t] ?? $t)
            ->implode(', ');
    }

    /**
     * Podaci za DOKO formu (primalac / prevoznik).
     *
     * @return array<string, string>
     */
    public function dokoPodaci(): array
    {
        return [
            'pib' => $this->pib ?? '',
            'maticni' => $this->maticni_broj ?? '',
            'naziv' => $this->naziv,
            'opstina' => $this->opstina ?? '',
            'mesto' => $this->mesto ?? '',
            'postanski' => $this->postanski_broj ?? '',
            'ulica' => $this->ulica ?? '',
            'telefon' => $this->telefon ?? '',
            'faks' => $this->faks ?? '',
            'email' => $this->email ?? '',
            'dozvola_broj' => $this->dozvola_broj ?? '',
            'dozvola_datum' => $this->dozvola_datum_izdavanja?->format('Y-m-d') ?? '',
            'odgovorno_lice' => $this->kontakt_osoba ?? '',
            'telefon_lica' => $this->telefon ?? '',
            'lokacija' => static::formatAdresa($this->ulica, $this->postanski_broj, $this->mesto, $this->opstina),
        ];
    }

    public static function formatAdresa(?string $ulica, ?string $postanski, ?string $mesto, ?string $opstina): string
    {
        return collect([
            $ulica,
            trim(trim((string) $postanski).' '.trim((string) $mesto)),
            $opstina,
        ])->filter(fn ($part) => $part !== null && trim($part) !== '')->implode(', ');
    }
}
