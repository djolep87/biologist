<?php

namespace App\Http\Controllers;

use App\Models\DokumentKretanja;
use App\Services\DokoExportService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokoController extends Controller
{
    public function __construct(
        private DokoExportService $dokoExportService
    ) {}

    public function downloadDoko(DokumentKretanja $dokument): StreamedResponse
    {
        $this->authorizeDownload($dokument);

        return $this->dokoExportService->download($dokument);
    }

    public function downloadDeo1(DokumentKretanja $dokument): StreamedResponse
    {
        $this->authorizeDownload($dokument);

        $evidencije = $dokument->dnevneEvidencije()
            ->orderBy('datum')
            ->orderBy('id')
            ->get();

        abort_if($evidencije->isEmpty(), 404, 'Nema vezanih izveštaja.');

        $prva = $evidencije->first();

        $spreadsheet = IOFactory::load(storage_path('app/templates/Deo1.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getCell('F6')->setValue($prva->godina);
        $sheet->getCell('F7')->setValue($this->mesecNaziv($prva->mesec));
        $sheet->getCell('F8')->setValue($prva->indeksni_broj);
        $sheet->getCell('F9')->setValue($prva->naziv_otpada);
        $sheet->getCell('F10')->setValue($prva->opis_otpada ?? '');
        $sheet->getCell('F11')->setValue($prva->lice_koje_vodi);

        $startRow = 15;
        foreach ($evidencije->take(21) as $i => $ev) {
            $row = $startRow + $i;

            $sheet->getCell("B{$row}")->setValue(Carbon::parse($ev->datum)->format('d.m.Y.'));
            $sheet->getCell("C{$row}")->setValue($ev->proizvedena_kolicina > 0 ? (float) $ev->proizvedena_kolicina : '');
            $sheet->getCell("D{$row}")->setValue($ev->predata_kolicina > 0 ? (float) $ev->predata_kolicina : '');
            $sheet->getCell("E{$row}")->setValue((float) $ev->stanje_na_skladistu);
            $sheet->getCell("F{$row}")->setValue($ev->predat_sakupljacu ? 'X' : '');
            $sheet->getCell("G{$row}")->setValue($ev->predat_operateru_r ? 'X' : '');
            $sheet->getCell("H{$row}")->setValue($ev->r_oznaka ?? '');
            $sheet->getCell("I{$row}")->setValue($ev->predat_operateru_d ? 'X' : '');
            $sheet->getCell("J{$row}")->setValue($ev->d_oznaka ?? '');
            $sheet->getCell("K{$row}")->setValue($ev->izvoz ? 'X' : '');
            $sheet->getCell("L{$row}")->setValue($ev->naziv_primaoca ?? '');
            $sheet->getCell("M{$row}")->setValue($ev->broj_dozvole_primaoca ?? '');
        }

        $lastRow = min($startRow + $evidencije->count() - 1, 35);
        $endRow = max($lastRow, $startRow);

        $sheet->getCell('C36')->setValue("=SUM(C{$startRow}:C{$endRow})");
        $sheet->getCell('D36')->setValue("=SUM(D{$startRow}:D{$endRow})");
        $sheet->getCell('E36')->setValue("=SUM(E{$startRow}:E{$endRow})");

        $safeIndeks = preg_replace('/[^\p{L}\p{N}\-_]+/u', '_', $dokument->indeksni_broj) ?: 'otpada';

        return response()->streamDownload(function () use ($spreadsheet) {
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        }, "DEO1_{$safeIndeks}_{$dokument->broj_dokumenta}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function authorizeDownload(DokumentKretanja $dokument): void
    {
        $user = auth()->user();

        abort_unless($user, 403);

        if ($user->is_super_admin) {
            return;
        }

        abort_unless($user->belongsToTeam($dokument->team), 403);
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
