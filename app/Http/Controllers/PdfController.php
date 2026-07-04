<?php

namespace App\Http\Controllers;

use App\Models\DnevnaEvidencija;
use App\Models\Team;
use App\Support\TeamAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class PdfController extends Controller
{
    /**
     * Obrazac DEO 1 — jedan indeksni broj za mesec (preuzimanje PDF).
     */
    public function deo1(DnevnaEvidencija $evidencija): Response
    {
        abort_unless(
            TeamAccess::canAccessTeam(auth()->user(), $evidencija->team),
            403,
            'Nemate pristup ovom dokumentu.'
        );

        $evidencija->load('team');

        $zapisi = DnevnaEvidencija::query()
            ->where('team_id', $evidencija->team_id)
            ->where('indeksni_broj', $evidencija->indeksni_broj)
            ->where('godina', $evidencija->godina)
            ->where('mesec', $evidencija->mesec)
            ->orderBy('datum')
            ->orderBy('id')
            ->get();

        $data = $this->podaciZaObrazac(
            $evidencija->team,
            $evidencija,
            $zapisi,
            [$this->sekcija($evidencija, $zapisi)]
        );

        return $this->preuzmiPdf($data, $this->imeFajla($evidencija->team->name, $evidencija));
    }

    /**
     * Mesečni obrazac — po jedna stranica po indeksnom broju otpada.
     */
    public function deo1Mesecni(Team $team, Request $request): Response
    {
        abort_unless(
            TeamAccess::canAccessTeam(auth()->user(), $team),
            403,
            'Nemate pristup ovom dokumentu.'
        );

        $mesec = (int) $request->query('mesec', now()->month);
        $godina = (int) $request->query('godina', now()->year);

        $zapisi = DnevnaEvidencija::query()
            ->where('team_id', $team->id)
            ->where('mesec', $mesec)
            ->where('godina', $godina)
            ->orderBy('indeksni_broj')
            ->orderBy('datum')
            ->orderBy('id')
            ->get();

        abort_if($zapisi->isEmpty(), 404, 'Nema evidencija za izabrani period.');

        $sekcije = $zapisi
            ->groupBy('indeksni_broj')
            ->map(function (Collection $grupa) {
                $prvi = $grupa->first();

                return $this->sekcija($prvi, $grupa->values());
            })
            ->values()
            ->all();

        $referenca = $zapisi->first();

        $data = $this->podaciZaObrazac($team, $referenca, $zapisi, $sekcije, true);

        $filename = sprintf(
            'DEO1_MESECNI_%s_%s_%d.pdf',
            $this->sanitizeFilename($team->name),
            $this->sanitizeFilename($this->mesecNaziv($mesec)),
            $godina
        );

        return $this->preuzmiPdf($data, $filename);
    }

    private function podaciZaObrazac(
        Team $firma,
        DnevnaEvidencija $referenca,
        Collection $sviZapisi,
        array $sekcije,
        bool $mesecni = false
    ): array {
        return [
            'firma' => $firma,
            'generisano' => now()->format('d.m.Y H:i'),
            'mesecNaziv' => $this->mesecNaziv($referenca->mesec),
            'godina' => $referenca->godina,
            'mesec' => $referenca->mesec,
            'sekcije' => $sekcije,
            'mesecni' => $mesecni,
        ];
    }

    private function sekcija(DnevnaEvidencija $evidencija, Collection $zapisi): array
    {
        $poDanu = $zapisi->keyBy(fn (DnevnaEvidencija $z) => $z->datum->day);
        $brojDana = Carbon::create($evidencija->godina, $evidencija->mesec, 1)->daysInMonth;

        $redovi = [];
        for ($dan = 1; $dan <= 31; $dan++) {
            $redovi[$dan] = $poDanu->get($dan);
        }

        return [
            'evidencija' => $evidencija,
            'redovi' => $redovi,
            'brojDanaUMesecu' => $brojDana,
            'ukupnoProizvedeno' => (float) $zapisi->sum('proizvedena_kolicina'),
            'ukupnoPredato' => (float) $zapisi->sum('predata_kolicina'),
            'krajnjeStanje' => (float) ($zapisi->last()?->stanje_na_skladistu ?? 0),
        ];
    }

    private function preuzmiPdf(array $data, string $filename): Response
    {
        $pdf = Pdf::loadView('pdf.deo1', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    private function imeFajla(string $firmaNaziv, DnevnaEvidencija $evidencija): string
    {
        return sprintf(
            'DEO1_%s_%s_%02d_%d.pdf',
            $this->sanitizeFilename($firmaNaziv),
            $this->sanitizeFilename($evidencija->indeksni_broj),
            $evidencija->mesec,
            $evidencija->godina
        );
    }

    private function sanitizeFilename(string $value): string
    {
        $value = str_replace(['/', '\\', ' '], '-', $value);

        return preg_replace('/[^A-Za-z0-9\-_čćžšđČĆŽŠĐ]/u', '', $value) ?: 'dokument';
    }

    private function mesecNaziv(int $mesec): string
    {
        $meseci = [
            1 => 'Januar', 2 => 'Februar', 3 => 'Mart', 4 => 'April',
            5 => 'Maj', 6 => 'Jun', 7 => 'Jul', 8 => 'Avgust',
            9 => 'Septembar', 10 => 'Oktobar', 11 => 'Novembar', 12 => 'Decembar',
        ];

        return $meseci[$mesec] ?? '';
    }
}
