<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class KatalogOtpadaGrupa17 extends Model
{
    protected $table = 'katalog_otpada_grupa17';

    protected $fillable = [
        'sifra',
        'naziv',
        'kategorija',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Najčešće korišćene šifre — prikazuju se prve u selectu. */
    public const NAJCESCE = [
        '17 01 07',
        '17 05 04',
        '17 04 05',
        '17 01 01',
        '17 01 02',
    ];

    public function scopeAktivni(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getKarakterOtpadaAttribute(): string
    {
        return match ($this->kategorija) {
            'inertni' => 'inertan',
            'opasni' => 'opasan',
            default => 'neopasan',
        };
    }

    public function getLabelAttribute(): string
    {
        return $this->sifra.' – '.$this->naziv;
    }

    /**
     * @return array{najcesci: Collection<int, self>, ostali: Collection<int, self>}
     */
    public static function grupisaniZaSelect(): array
    {
        $svi = static::aktivni()->orderBy('sifra')->get();

        $najcesci = $svi->filter(fn (self $item) => in_array($item->sifra, self::NAJCESCE, true))
            ->sortBy(fn (self $item) => array_search($item->sifra, self::NAJCESCE, true))
            ->values();

        $ostali = $svi->reject(fn (self $item) => in_array($item->sifra, self::NAJCESCE, true))->values();

        return [
            'najcesci' => $najcesci,
            'ostali' => $ostali,
        ];
    }

    public static function mapaSifraNaziv(): array
    {
        return static::aktivni()
            ->orderBy('sifra')
            ->pluck('naziv', 'sifra')
            ->all();
    }
}
