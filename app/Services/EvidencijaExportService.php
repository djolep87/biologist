<?php

namespace App\Services;

use App\Models\DnevnaEvidencija;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EvidencijaExportService
{
    private const TEMPLATE_PATH = 'templates/Deo1.xlsx';

    private const HEADER_GODINA = 'F6';

    private const HEADER_MESEC = 'F7';

    private const HEADER_INDEKSNI_BROJ = 'F8';

    private const HEADER_NAZIV_OTPADA = 'F9';

    private const HEADER_OPIS_OTPADA = 'F10';

    private const HEADER_EVIDENCIJU_VODI = 'F11';

    private const DATA_START_ROW = 15;

    private const DATA_END_ROW = 35;

    private const UKUPNO_ROW = 36;

    public function generate(int $teamId, int $godina, int $mesec, string $indeksniBroj): BinaryFileResponse
    {
        $records = DnevnaEvidencija::query()
            ->where('team_id', $teamId)
            ->where('godina', $godina)
            ->where('mesec', $mesec)
            ->where('indeksni_broj', $indeksniBroj)
            ->orderBy('datum')
            ->orderBy('id')
            ->get();

        abort_if($records->isEmpty(), 404, 'Nema dnevne evidencije za izabrani period i indeksni broj.');

        $spreadsheet = IOFactory::load(storage_path('app/'.self::TEMPLATE_PATH));
        $sheet = $spreadsheet->getActiveSheet();

        $first = $records->first();

        $this->fillHeader($sheet, $godina, $mesec, $first);

        $lastDataRow = $this->fillDataRows($sheet, $records);

        $this->fillUkupnoRow($sheet, $lastDataRow);

        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir.'/'.uniqid('evidencija_', true).'.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempPath);

        $safeIndeks = preg_replace('/[^\p{L}\p{N}\-_]+/u', '_', $indeksniBroj) ?: 'otpada';
        $filename = "Evidencija_Otpada_{$godina}_{$mesec}_{$safeIndeks}.xlsx";

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    private function fillHeader(Worksheet $sheet, int $godina, int $mesec, DnevnaEvidencija $first): void
    {
        $sheet->setCellValue(self::HEADER_GODINA, $godina);
        $sheet->setCellValue(self::HEADER_MESEC, $this->mesecNaziv($mesec));
        $sheet->setCellValue(self::HEADER_INDEKSNI_BROJ, $first->indeksni_broj);
        $sheet->setCellValue(self::HEADER_NAZIV_OTPADA, $first->naziv_otpada);
        $sheet->setCellValue(self::HEADER_OPIS_OTPADA, $first->opis_otpada ?? '');
        $sheet->setCellValue(self::HEADER_EVIDENCIJU_VODI, $first->lice_koje_vodi);
    }

    private function fillDataRows(Worksheet $sheet, Collection $records): int
    {
        $row = self::DATA_START_ROW;

        foreach ($records as $record) {
            if ($row > self::DATA_END_ROW) {
                break;
            }

            $sheet->setCellValue("B{$row}", $record->datum->format('d.m.Y.'));
            $this->setDecimalCell($sheet, "C{$row}", $record->proizvedena_kolicina);
            $this->setDecimalCell($sheet, "D{$row}", $record->predata_kolicina);
            $this->setDecimalCell($sheet, "E{$row}", $record->stanje_na_skladistu);
            $sheet->setCellValue("F{$row}", $record->predat_sakupljacu ? 'X' : '');
            $sheet->setCellValue("G{$row}", $record->predat_operateru_r ? 'X' : '');
            $sheet->setCellValue("H{$row}", $record->r_oznaka ?? '');
            $sheet->setCellValue("I{$row}", $record->predat_operateru_d ? 'X' : '');
            $sheet->setCellValue("J{$row}", $record->d_oznaka ?? '');
            $sheet->setCellValue("K{$row}", $record->izvoz ? 'X' : '');
            $sheet->setCellValue("L{$row}", $record->naziv_primaoca ?? '');
            $sheet->setCellValue("M{$row}", $record->broj_dozvole_primaoca ?? '');

            $row++;
        }

        return max($row - 1, self::DATA_START_ROW);
    }

    private function fillUkupnoRow(Worksheet $sheet, int $lastDataRow): void
    {
        $start = self::DATA_START_ROW;
        $end = max($lastDataRow, $start);

        foreach (['C', 'D', 'E'] as $column) {
            $sheet->setCellValue(
                "{$column}".self::UKUPNO_ROW,
                "=SUM({$column}{$start}:{$column}{$end})"
            );
        }
    }

    private function setDecimalCell(Worksheet $sheet, string $cell, mixed $value): void
    {
        if ($value === null) {
            return;
        }

        $sheet->setCellValue($cell, (float) $value);
    }

    private function mesecNaziv(int $mesec): string
    {
        return [
            1 => 'Јануар', 2 => 'Фебруар', 3 => 'Март',
            4 => 'Април', 5 => 'Мај', 6 => 'Јун',
            7 => 'Јул', 8 => 'Август', 9 => 'Септембар',
            10 => 'Октобар', 11 => 'Новембар', 12 => 'Децембар',
        ][$mesec] ?? '';
    }
}
