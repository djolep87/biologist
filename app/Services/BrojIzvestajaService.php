<?php

namespace App\Services;

use App\Models\DokumentKretanja;
use App\Models\Team;

/**
 * Generisanje jedinstvenog broja izveštaja za DOKO dokument.
 *
 * Podržani formati (per klijent, podešava superadmin):
 *  - osnovni:   001/2026            (RedniBroj/Godina, reset 1. januara)
 *  - lokacija:  BG01-001/2026       (Oznaka-RedniBroj/Godina, reset 1. januara po lokaciji)
 *  - vremenski: 20260704-01         (YYYYMMDD-RedniBroj, dnevni inkrement, bez godišnjeg reseta)
 *
 * Opcioni prefix (npr. "DKO-") i broj cifara (3 ili 4) primenjuju se na osnovni/lokacija format.
 */
class BrojIzvestajaService
{
    /**
     * Podešavanja numeracije za klijenta (sa fallback vrednostima).
     *
     * @return array{format: string, broj_cifara: int, prefix: string, lokacije: array<int, array{oznaka: string, naziv: string}>}
     */
    public function settingsForTeam(?Team $team): array
    {
        if (! $team) {
            return ['format' => 'osnovni', 'broj_cifara' => 3, 'prefix' => '', 'lokacije' => []];
        }

        return $team->dkoNumeracija();
    }

    /**
     * Preview broja koji će verovatno biti dodeljen — BEZ upisa u bazu i bez zaključavanja.
     * Koristi se samo za prikaz u formi (stvarni broj se dodeljuje pri čuvanju).
     */
    public function previewBroj(int $teamId, ?string $lokacijaOznaka = null): string
    {
        $team = Team::find($teamId);
        $settings = $this->settingsForTeam($team);
        $lokacija = $this->resolveLokacija($settings, $lokacijaOznaka);

        $redniBroj = $this->nextRedniBroj($teamId, $settings['format'], $lokacija, lock: false);

        return $this->formatiraj($redniBroj, $settings['format'], $lokacija, $settings['broj_cifara'], $settings['prefix']);
    }

    /**
     * Generiše finalni broj — MORA se pozvati unutar DB::transaction() zbog lockForUpdate().
     *
     * @return array{broj_izvestaja: string, redni_broj: int, format_broja: string, lokacija_oznaka: ?string}
     */
    public function generateBroj(int $teamId, ?string $lokacijaOznaka = null): array
    {
        $team = Team::find($teamId);
        $settings = $this->settingsForTeam($team);
        $lokacija = $this->resolveLokacija($settings, $lokacijaOznaka);

        $redniBroj = $this->nextRedniBroj($teamId, $settings['format'], $lokacija, lock: true);
        $broj = $this->formatiraj($redniBroj, $settings['format'], $lokacija, $settings['broj_cifara'], $settings['prefix']);

        return [
            'broj_izvestaja' => $broj,
            'redni_broj' => $redniBroj,
            'format_broja' => $settings['format'],
            'lokacija_oznaka' => $settings['format'] === 'lokacija' ? $lokacija : null,
        ];
    }

    /**
     * Sledeći redni broj u odgovarajućem opsegu (godišnji ili dnevni).
     *
     * Uvek uključuje soft-deleted zapise (withTrashed) da se brojevi obrisanih
     * dokumenata NE bi ponovo koristili — dozvoljene su "rupe" u numeraciji.
     */
    protected function nextRedniBroj(int $teamId, string $format, ?string $lokacija, bool $lock): int
    {
        $query = DokumentKretanja::withTrashed()
            ->where('team_id', $teamId)
            ->where('format_broja', $format);

        if ($format === 'vremenski') {
            $query->whereDate('created_at', today());
        } else {
            $query->whereYear('created_at', (int) date('Y'));
        }

        if ($format === 'lokacija') {
            $query->where('lokacija_oznaka', $lokacija);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        return (int) $query->max('redni_broj') + 1;
    }

    protected function formatiraj(int $redniBroj, string $format, ?string $lokacija, int $brojCifara, string $prefix): string
    {
        return match ($format) {
            'vremenski' => $prefix.date('Ymd').'-'.str_pad((string) $redniBroj, 2, '0', STR_PAD_LEFT),
            'lokacija' => $prefix.($lokacija !== null && $lokacija !== '' ? $lokacija.'-' : '')
                .str_pad((string) $redniBroj, $brojCifara, '0', STR_PAD_LEFT).'/'.date('Y'),
            default => $prefix.str_pad((string) $redniBroj, $brojCifara, '0', STR_PAD_LEFT).'/'.date('Y'),
        };
    }

    /**
     * @param  array{format: string, broj_cifara: int, prefix: string, lokacije: array<int, array{oznaka: string, naziv: string}>}  $settings
     */
    protected function resolveLokacija(array $settings, ?string $lokacijaOznaka): ?string
    {
        if ($settings['format'] !== 'lokacija') {
            return null;
        }

        $dostupne = collect($settings['lokacije'])->pluck('oznaka')->all();

        if ($lokacijaOznaka !== null && $lokacijaOznaka !== '' && in_array($lokacijaOznaka, $dostupne, true)) {
            return $lokacijaOznaka;
        }

        return $dostupne[0] ?? ($lokacijaOznaka ?: null);
    }
}
