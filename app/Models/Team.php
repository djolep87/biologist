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
