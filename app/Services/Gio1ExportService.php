<?php

namespace App\Services;

use App\Models\DnevnaEvidencija;
use App\Models\DokumentKretanja;
use App\Models\GodisnjIzvestaj;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Gio1ExportService
{
    private const TEMPLATE_PATH = 'templates/GIO1.xlsx';

    /** @var array<int> Donji deo predaja tabele — svaki red ima nezavisne merge-ove (110–118) */
    private const PREDAJA_ROWS = [110, 111, 112, 113, 114, 115, 116, 117, 118];

    public function download(GodisnjIzvestaj $izvestaj): StreamedResponse
    {
        $spreadsheet = $this->buildWorkbook($izvestaj);

        $team = $izvestaj->team;
        $filename = sprintf(
            'GIO1_%s_%d.xlsx',
            preg_replace('/[^A-Za-z0-9_]/', '_', $team->name),
            $izvestaj->godina
        );

        return response()->streamDownload(function () use ($spreadsheet) {
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function buildWorkbook(GodisnjIzvestaj $izvestaj): Spreadsheet
    {
        $path = storage_path('app/'.self::TEMPLATE_PATH);
        abort_unless(is_file($path), 500, 'GIO1 template nije pronađen.');

        $team = $izvestaj->team;
        $godina = $izvestaj->godina;

        $sviDoko = DokumentKretanja::where('team_id', $team->id)
            ->whereYear('datum_predaje', $godina)
            ->with('dnevneEvidencije')
            ->orderBy('datum_predaje')
            ->orderBy('id')
            ->get();

        $indeksni = DnevnaEvidencija::where('team_id', $team->id)
            ->where('godina', $godina)
            ->distinct()
            ->orderBy('indeksni_broj')
            ->value('indeksni_broj');

        $template = IOFactory::load($path);
        $sheet = $template->getActiveSheet();
        $sheet->setTitle('GIO1');

        $this->fillCompanyAndContacts($sheet, $team, $godina, $izvestaj);

        if ($indeksni) {
            $deoAgregat = DnevnaEvidencija::where('team_id', $team->id)
                ->where('indeksni_broj', $indeksni)
                ->where('godina', $godina)
                ->selectRaw('
                    MAX(naziv_otpada) as naziv_otpada,
                    MAX(karakter_otpada) as karakter_otpada,
                    MAX(fizicko_stanje) as fizicko_stanje,
                    SUM(proizvedena_kolicina) as ukupno_proizvedeno,
                    MIN(nacin_odredjivanja) as nacin_odredjivanja
                ')
                ->first();

            $prvaEv = DnevnaEvidencija::where('team_id', $team->id)
                ->where('indeksni_broj', $indeksni)
                ->where('godina', $godina)
                ->orderBy('datum')
                ->orderBy('id')
                ->first();

            $poslednjaEv = DnevnaEvidencija::where('team_id', $team->id)
                ->where('indeksni_broj', $indeksni)
                ->where('godina', $godina)
                ->orderByDesc('datum')
                ->orderByDesc('id')
                ->first();

            $prviDoko = $sviDoko->firstWhere('indeksni_broj', $indeksni) ?? $sviDoko->first();

            $this->fillClassification(
                $sheet,
                $team,
                $indeksni,
                $deoAgregat,
                $prvaEv,
                $poslednjaEv,
                $prviDoko
            );
        }

        $this->fillPredajaTable($sheet, $sviDoko);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);
        $spreadsheet->addExternalSheet($sheet);

        return $spreadsheet;
    }

    private function fillClassification(
        Worksheet $sheet,
        Team $team,
        string $indeksni,
        ?object $deoAgregat,
        ?DnevnaEvidencija $prvaEv,
        ?DnevnaEvidencija $poslednjaEv,
        ?DokumentKretanja $prviDoko
    ): void {
        $sheet->getCell('E47')->setValue(trim(($team->adresa ?? '').', '.($team->grad ?? ''), ', '));

        $karakter = $deoAgregat?->karakter_otpada ?? 'neopasan';
        $sheet->getCell('D52')->setValue(match ($karakter) {
            'opasan' => 'Opasan otpad',
            'inertan' => 'Inertan otpad',
            default => 'Neopasan otpad',
        });
        $sheet->getCell('D54')->setValue($prvaEv?->opis_otpada ?? '');
        $sheet->getCell('D57')->setValue($deoAgregat?->naziv_otpada ?? '');

        $this->writeCharBoxes($sheet, 60, 'I', $this->normalizeQLista($prviDoko?->q_lista ?? ''));
        $this->writeCharBoxes($sheet, 61, 'D', preg_replace('/\s+/', '', $indeksni));

        $this->clearRow($sheet, 62, 'E', 'Q');
        $this->clearRow($sheet, 63, 'E', 'Q');
        $this->clearRow($sheet, 64, 'E', 'Q');
        $karakterCell = match ($karakter) {
            'inertan' => 'E62',
            'opasan' => 'E64',
            default => 'E63',
        };
        $sheet->getCell($karakterCell)->setValue('x');

        $sheet->getCell('H65')->setValue($prviDoko?->izvestaj_broj ?? '');
        $sheet->getCell('H66')->setValue(
            $prviDoko?->izvestaj_datum
                ? Carbon::parse($prviDoko->izvestaj_datum)->format('d.m.Y.')
                : ''
        );

        $fizickoMap = [
            69 => 'cvrsta-prah',
            70 => 'cvrsta-komadi',
            71 => 'viskozna-pasta',
            72 => 'tecna',
            73 => 'talog',
        ];
        $fizicko = $deoAgregat?->fizicko_stanje ?? '';
        foreach ($fizickoMap as $row => $value) {
            $sheet->getCell("J{$row}")->setValue($fizicko === $value ? 'x' : '');
        }

        $sheet->getCell('J86')->setValue($this->formatKolicina($deoAgregat?->ukupno_proizvedeno));
        $sheet->getCell('J87')->setValue($this->formatKolicina($prvaEv?->stanje_na_skladistu ?? 0));
        $sheet->getCell('J88')->setValue($this->formatKolicina($poslednjaEv?->stanje_na_skladistu ?? 0));
        $sheet->getCell('J89')->setValue($deoAgregat?->nacin_odredjivanja ?? '1');
    }

    private function fillPredajaTable(Worksheet $sheet, Collection $dokoDokumenti): void
    {
        foreach (self::PREDAJA_ROWS as $row) {
            $this->clearPredajaRow($sheet, $row);
        }

        foreach ($dokoDokumenti->take(count(self::PREDAJA_ROWS))->values() as $idx => $doko) {
            $this->fillPredajaRow($sheet, self::PREDAJA_ROWS[$idx], $doko);
        }
    }

    private function fillPredajaRow(Worksheet $sheet, int $row, DokumentKretanja $doko): void
    {
        $naziv = $doko->primalac_naziv ?? '';
        $dozvola = $doko->primalac_dozvola_broj ?? '';
        $kolicina = $this->formatKolicina($doko->masa_ukupno);

        // SEKCIJA 3: Izvoz (kolone 11–14 → R, T, V, W)
        if ($this->isIzvozDokument($doko)) {
            $this->setPredajaCell($sheet, "R{$row}", $doko->odrediste ?? '');
            $this->setPredajaCell($sheet, "T{$row}", $kolicina);
            $this->setPredajaCell($sheet, "V{$row}", $doko->r_oznaka ?? $doko->d_oznaka ?? '');
            $this->setPredajaCell($sheet, "W{$row}", trim(
                $naziv.', '.($doko->primalac_ulica ?? '').', '.($doko->primalac_mesto ?? ''),
                ', '
            ));

            return;
        }

        // SEKCIJA 2: R oznaka → ponovno iskorišćenje (kolone 5,6,9,10)
        if (! empty($doko->r_oznaka)) {
            $this->setPredajaCell($sheet, "G{$row}", $naziv);
            $this->setTextCell($sheet, "J{$row}", $dozvola);
            $this->setPredajaCell($sheet, "O{$row}", $kolicina);
            $this->setPredajaCell($sheet, "Q{$row}", $doko->r_oznaka);

            return;
        }

        // SEKCIJA 2: D oznaka → odlaganje (kolone 5,6,7,8)
        if (! empty($doko->d_oznaka)) {
            $this->setPredajaCell($sheet, "G{$row}", $naziv);
            $this->setTextCell($sheet, "J{$row}", $dozvola);
            $this->setPredajaCell($sheet, "L{$row}", $kolicina);
            $this->setPredajaCell($sheet, "N{$row}", $doko->d_oznaka);

            return;
        }

        // SEKCIJA 1: Skladištenje (kolone 1–4) — samo bez R/D oznake
        $this->setPredajaCell($sheet, "A{$row}", $naziv);
        $this->setTextCell($sheet, "C{$row}", $dozvola);
        $this->setPredajaCell($sheet, "D{$row}", $kolicina);
        $this->setPredajaCell($sheet, "F{$row}", '');
    }

    private function isIzvozDokument(DokumentKretanja $doko): bool
    {
        return $doko->dnevneEvidencije->contains(fn ($ev) => (bool) $ev->izvoz);
    }

    private function setPredajaCell(Worksheet $sheet, string $coordinate, mixed $value): void
    {
        $sheet->getCell($coordinate)->setValue($value ?? '');
        $sheet->getStyle($coordinate)->getAlignment()
            ->setTextRotation(0)
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    private function clearPredajaRow(Worksheet $sheet, int $row): void
    {
        foreach (['A', 'C', 'D', 'F', 'G', 'J', 'L', 'N', 'O', 'Q', 'R', 'T', 'V', 'W'] as $col) {
            $sheet->getCell("{$col}{$row}")->setValue('');
        }
    }

    private function fillCompanyAndContacts(
        Worksheet $sheet,
        Team $team,
        int $godina,
        GodisnjIzvestaj $izvestaj
    ): void {
        $this->writeYearDigits($sheet, $godina);

        $this->setTextCell($sheet, 'I4', $team->pib ?? '');
        $this->setTextCell($sheet, 'I5', $team->maticni_broj ?? '');
        $sheet->getCell('I6')->setValue($team->name ?? '');
        $sheet->getCell('I7')->setValue($team->grad ?? '');
        $this->setTextCell($sheet, 'I9', $team->postanski_broj ?? '');
        $sheet->getCell('I10')->setValue($team->adresa ?? '');
        $sheet->getCell('I11')->setValue($team->kontakt_telefon ?? '');
        $sheet->getCell('I14')->setValue($team->grad ?? '');
        $sheet->getCell('I16')->setValue($team->delatnost ?? '');

        $sheet->getCell('I19')->setValue($izvestaj->odgovorno_lice_ime ?? '');
        $sheet->getCell('I20')->setValue($izvestaj->odgovorno_lice_funkcija ?? '');
        $sheet->getCell('I21')->setValue($izvestaj->odgovorno_lice_telefon ?? '');

        $sheet->getCell('I24')->setValue($izvestaj->lice_otpad_ime ?? '');
        $sheet->getCell('I25')->setValue($izvestaj->lice_otpad_funkcija ?? '');
        $sheet->getCell('I26')->setValue($izvestaj->lice_otpad_telefon ?? '');
        $sheet->getCell('I27')->setValue($izvestaj->lice_otpad_email ?? '');

        $sheet->getCell('G38')->setValue($izvestaj->odgovorno_lice_ime ?? '');
        $sheet->getCell('G40')->setValue(now()->format('d.m.Y.'));
    }

    private function writeYearDigits(Worksheet $sheet, int $godina): void
    {
        $digits = str_pad((string) $godina, 4, '0', STR_PAD_LEFT);
        $cols = ['E', 'F', 'G', 'H'];

        foreach ($cols as $i => $col) {
            $sheet->getCell("{$col}2")->setValue($digits[$i] ?? '');
        }

        $sheet->getCell('I2')->setValue('godinu');
    }

    private function writeCharBoxes(Worksheet $sheet, int $row, string $startCol, string $value, int $maxBoxes = 12): void
    {
        $startIndex = Coordinate::columnIndexFromString($startCol);

        for ($i = 0; $i < $maxBoxes; $i++) {
            $col = Coordinate::stringFromColumnIndex($startIndex + $i);
            $sheet->getCell("{$col}{$row}")->setValue('');
        }

        $chars = mb_str_split($value);

        foreach ($chars as $i => $char) {
            if ($i >= $maxBoxes) {
                break;
            }
            $col = Coordinate::stringFromColumnIndex($startIndex + $i);
            $sheet->getCell("{$col}{$row}")->setValue($char);
        }
    }

    private function clearRow(Worksheet $sheet, int $row, string $startCol, string $endCol): void
    {
        $start = Coordinate::columnIndexFromString($startCol);
        $end = Coordinate::columnIndexFromString($endCol);

        for ($i = $start; $i <= $end; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getCell("{$col}{$row}")->setValue('');
        }
    }

    private function normalizeQLista(string $qLista): string
    {
        $qLista = strtoupper(trim($qLista));

        if ($qLista === '') {
            return '';
        }

        return str_starts_with($qLista, 'Q') ? mb_substr($qLista, 1) : $qLista;
    }

    private function setTextCell(Worksheet $sheet, string $coordinate, mixed $value): void
    {
        if ($value === null || $value === '') {
            $sheet->getCell($coordinate)->setValue('');

            return;
        }

        $sheet->getCell($coordinate)->setValueExplicit((string) $value, DataType::TYPE_STRING);
    }

    private function formatKolicina(mixed $val): string
    {
        if ($val === null || (float) $val == 0) {
            return '';
        }

        $num = (float) $val;

        return $num < 10
            ? number_format($num, 1, ',', '.')
            : number_format($num, 0, ',', '.');
    }
}
