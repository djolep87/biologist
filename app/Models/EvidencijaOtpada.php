<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidencijaOtpada extends Model
{
    protected $table = 'evidencija_otpada';

    protected $fillable = [
        'godina',
        'mesec',
        'indeksni_broj',
        'naziv_otpada',
        'opis_otpada',
        'evidenciju_vodi',
        'datum',
        'proizvedena_kolicina',
        'predata_kolicina',
        'stanje_privremeno_skladiste',
        'sakupljacu',
        'operateru_ponovnog_iskoriscenja',
        'r_oznaka',
        'operateru_odlaganja',
        'd_oznaka',
        'izvoz',
        'naziv_preduzeca',
        'broj_dozvole',
    ];

    protected function casts(): array
    {
        return [
            'datum' => 'date',
            'proizvedena_kolicina' => 'decimal:3',
            'predata_kolicina' => 'decimal:3',
            'stanje_privremeno_skladiste' => 'decimal:3',
            'sakupljacu' => 'boolean',
            'operateru_ponovnog_iskoriscenja' => 'boolean',
            'operateru_odlaganja' => 'boolean',
            'izvoz' => 'boolean',
        ];
    }
}
