<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ZahtevPredaje extends Model
{
    use SoftDeletes;

    protected $table = 'zahtevi_predaje';

    protected $fillable = [
        'team_id',
        'user_id',
        'status',
        'dokument_kretanja_id',
        'indeksni_broj',
        'naziv_otpada',
        'masa_ukupno',
        'napomena_klijenta',
        'napomena_admina',
        'admin_id',
        'admin_odgovorio_at',
    ];

    protected function casts(): array
    {
        return [
            'masa_ukupno' => 'decimal:3',
            'admin_odgovorio_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function dokumentKretanja(): BelongsTo
    {
        return $this->belongsTo(DokumentKretanja::class, 'dokument_kretanja_id');
    }

    public function evidencije(): BelongsToMany
    {
        return $this->belongsToMany(
            DnevnaEvidencija::class,
            'zahtev_evidencija',
            'zahtev_predaje_id',
            'dnevna_evidencija_id'
        );
    }

    public function scopeForTeam(Builder $query, ?int $teamId = null): Builder
    {
        $teamId ??= Auth::user()?->currentTeam?->id;

        return $query->when($teamId, fn (Builder $q) => $q->where('team_id', $teamId));
    }

    public function scopeNaCekanju(Builder $query): Builder
    {
        return $query->where('status', 'na_cekanju');
    }

    /**
     * Klijent briše odbijeni zahtev — evidencije se oslobađaju i stanje se preračunava.
     */
    public function obrisiOdbijen(): void
    {
        if ($this->status !== 'odbijeno') {
            throw new \InvalidArgumentException('Samo odbijeni zahtevi mogu biti obrisani.');
        }

        $teamId = $this->team_id;
        $indeksniBroj = $this->indeksni_broj;

        DB::transaction(function () use ($teamId, $indeksniBroj) {
            $this->evidencije()->detach();
            $this->delete();

            DnevnaEvidencija::recalculateStanjeZaIndeks($teamId, $indeksniBroj);
        });
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'na_cekanju' => ['label' => '⏳ Na čekanju', 'class' => 'bg-amber-100 text-amber-800'],
            'u_obradi' => ['label' => '🔄 U obradi', 'class' => 'bg-blue-100 text-blue-800'],
            'zavrseno' => ['label' => '✅ Završeno', 'class' => 'bg-green-100 text-green-800'],
            'odbijeno' => ['label' => '❌ Odbijeno', 'class' => 'bg-red-100 text-red-800'],
            default => ['label' => $this->status, 'class' => 'bg-gray-100 text-gray-800'],
        };
    }
}
