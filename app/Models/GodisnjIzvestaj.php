<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class GodisnjIzvestaj extends Model
{
    protected $table = 'godisnji_izvestaji';

    protected $fillable = [
        'team_id',
        'created_by',
        'godina',
        'status',
        'odgovorno_lice_ime',
        'odgovorno_lice_funkcija',
        'odgovorno_lice_telefon',
        'lice_otpad_ime',
        'lice_otpad_funkcija',
        'lice_otpad_telefon',
        'lice_otpad_email',
        'generisan_at',
    ];

    protected function casts(): array
    {
        return [
            'godina' => 'integer',
            'generisan_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForTeam(Builder $query, ?int $teamId = null): Builder
    {
        $teamId ??= Auth::user()?->currentTeam?->id;

        return $query->when($teamId, fn (Builder $q) => $q->where('team_id', $teamId));
    }

    public function getBrVrstaOtpadaAttribute(): int
    {
        return (int) DnevnaEvidencija::where('team_id', $this->team_id)
            ->where('godina', $this->godina)
            ->distinct()
            ->count('indeksni_broj');
    }

    public function getBrDokoAttribute(): int
    {
        return (int) DokumentKretanja::where('team_id', $this->team_id)
            ->whereYear('datum_predaje', $this->godina)
            ->count();
    }
}
