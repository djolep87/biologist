<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
        'pib',
        'maticni_broj',
        'adresa',
        'grad',
        'opstina',
        'postanski_broj',
        'tip_subjekta',
        'delatnost',
        'kontakt_telefon',
        'email',
        'faks',
        'kontakt_osoba',
        'dozvola_broj',
        'dozvola_datum_izdavanja',
        'dozvola_vazi_do',
        'dko_format_broja',
        'dko_broj_cifara',
        'dko_prefix',
        'dko_lokacije',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'dozvola_datum_izdavanja' => 'date',
            'dozvola_vazi_do' => 'date',
            'dko_broj_cifara' => 'integer',
            'dko_lokacije' => 'array',
        ];
    }

    /**
     * Podešavanja numeracije DKO broja izveštaja za ovog klijenta.
     *
     * @return array{format: string, broj_cifara: int, prefix: string, lokacije: array<int, array{oznaka: string, naziv: string}>}
     */
    public function dkoNumeracija(): array
    {
        $format = in_array($this->dko_format_broja, ['osnovni', 'lokacija', 'vremenski'], true)
            ? $this->dko_format_broja
            : 'osnovni';

        $brojCifara = (int) ($this->dko_broj_cifara ?: 3);
        $brojCifara = max(2, min(6, $brojCifara));

        $lokacije = collect($this->dko_lokacije ?? [])
            ->map(fn ($l) => [
                'oznaka' => trim((string) ($l['oznaka'] ?? '')),
                'naziv' => trim((string) ($l['naziv'] ?? '')),
            ])
            ->filter(fn ($l) => $l['oznaka'] !== '')
            ->values()
            ->all();

        return [
            'format' => $format,
            'broj_cifara' => $brojCifara,
            'prefix' => trim((string) ($this->dko_prefix ?? '')),
            'lokacije' => $lokacije,
        ];
    }

    /**
     * Podaci proizvođača za DOKO (DEO B) iz profila firme.
     *
     * @return array<string, string>
     */
    public function dokoProizvodjacPodaci(): array
    {
        return [
            'pib' => $this->pib ?? '',
            'maticni' => $this->maticni_broj ?? '',
            'naziv' => $this->name ?? '',
            'opstina' => $this->opstina ?? '',
            'mesto' => $this->grad ?? '',
            'postanski' => $this->postanski_broj ?? '',
            'ulica' => $this->adresa ?? '',
            'telefon' => $this->kontakt_telefon ?? '',
            'faks' => $this->faks ?? '',
            'email' => $this->email ?? '',
            'dozvola_broj' => $this->dozvola_broj ?? '',
            'dozvola_datum' => $this->dozvola_datum_izdavanja?->format('Y-m-d') ?? '',
            'odgovorno_lice' => $this->kontakt_osoba ?? '',
            'telefon_lica' => $this->kontakt_telefon ?? '',
            'lokacija_utovara' => Operater::formatAdresa($this->adresa, $this->postanski_broj, $this->grad, $this->opstina),
        ];
    }
}
