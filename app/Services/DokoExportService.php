<?php

namespace App\Services;

use App\Models\DokumentKretanja;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokoExportService
{
    private const TEMPLATE_PATH = 'templates/Doko.xlsx';

    public function download(DokumentKretanja $dokument): StreamedResponse
    {
        $spreadsheet = $this->fillTemplate($dokument);

        $filename = 'DOKO_'.$this->safeFilename($dokument->broj_dokumenta).'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function fillTemplate(DokumentKretanja $dokument): Spreadsheet
    {
        $path = storage_path('app/'.self::TEMPLATE_PATH);

        abort_unless(is_file($path), 500, 'DOKO šablon nije pronađen (storage/app/templates/Doko.xlsx).');

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        // DEO A
        $sheet->getCell('B5')->setValue($dokument->vrsta_otpada);
        $sheet->getCell('D6')->setValue($dokument->indeksni_broj);
        $sheet->getCell('D7')->setValue($dokument->q_lista ?? '');
        $sheet->getCell('B8')->setValue($dokument->masa_ukupno);
        $sheet->getCell('B9')->setValue($dokument->nacin_pakovanja ?? '');
        $sheet->getCell('B10')->setValue($this->fizickoStanjeLabel($dokument->fizicko_stanje));
        $sheet->getCell('C11')->setValue($dokument->izvestaj_broj ?? '');
        $sheet->getCell('C12')->setValue($this->formatDatum($dokument->izvestaj_datum));
        $sheet->getCell('B13')->setValue($dokument->odrediste ?? '');
        $sheet->getCell('B14')->setValue($dokument->vid_prevoza ?? '');
        $sheet->getCell('B15')->setValue($dokument->posebne_napomene ?? '');

        // DEO B
        $sheet->getCell('C18')->setValue($dokument->proizvodjac_pib ?? '');
        $sheet->getCell('C19')->setValue($dokument->proizvodjac_maticni ?? '');
        $sheet->getCell('C20')->setValue($dokument->proizvodjac_naziv ?? '');
        $sheet->getCell('C21')->setValue($dokument->proizvodjac_opstina ?? '');
        $sheet->getCell('C22')->setValue($dokument->proizvodjac_mesto ?? '');
        $sheet->getCell('C23')->setValue($dokument->proizvodjac_postanski ?? '');
        $sheet->getCell('C24')->setValue($dokument->proizvodjac_ulica ?? '');
        $sheet->getCell('C25')->setValue($dokument->proizvodjac_telefon ?? '');
        $sheet->getCell('C26')->setValue($dokument->proizvodjac_faks ?? '');
        $sheet->getCell('C27')->setValue($dokument->proizvodjac_email ?? '');
        $sheet->getCell('D28')->setValue($dokument->vlasnik_tip === 'proizvodjac' ? 'X' : '');
        $sheet->getCell('D29')->setValue($dokument->vlasnik_tip === 'vlasnik' ? 'X' : '');
        $sheet->getCell('D30')->setValue($dokument->vlasnik_tip === 'operater' ? 'X' : '');
        $sheet->getCell('D31')->setValue($dokument->r_oznaka ?? '');
        $sheet->getCell('D32')->setValue($dokument->d_oznaka ?? '');
        $sheet->getCell('D33')->setValue($dokument->dozvola_broj ?? '');
        $sheet->getCell('D34')->setValue($this->formatDatum($dokument->dozvola_datum));
        $sheet->getCell('D37')->setValue($this->formatDatum($dokument->datum_predaje));
        $sheet->getCell('D38')->setValue($dokument->odgovorno_lice_b ?? '');
        $sheet->getCell('D39')->setValue($dokument->telefon_lica_b ?? '');

        // DEO C
        $sheet->getCell('C43')->setValue($dokument->prevoznik_pib ?? '');
        $sheet->getCell('C44')->setValue($dokument->prevoznik_maticni ?? '');
        $sheet->getCell('C45')->setValue($dokument->prevoznik_naziv ?? '');
        $sheet->getCell('C46')->setValue($dokument->prevoznik_opstina ?? '');
        $sheet->getCell('C47')->setValue($dokument->prevoznik_mesto ?? '');
        $sheet->getCell('C48')->setValue($dokument->prevoznik_postanski ?? '');
        $sheet->getCell('C49')->setValue($dokument->prevoznik_ulica ?? '');
        $sheet->getCell('C50')->setValue($dokument->prevoznik_telefon ?? '');
        $sheet->getCell('C51')->setValue($dokument->prevoznik_faks ?? '');
        $sheet->getCell('C52')->setValue($dokument->prevoznik_email ?? '');
        $sheet->getCell('C53')->setValue($dokument->vrsta_prevoznog_sredstva ?? '');
        $sheet->getCell('C54')->setValue($dokument->registarski_broj ?? '');
        $sheet->getCell('D55')->setValue($dokument->lokacija_utovara ?? '');
        $sheet->getCell('D56')->setValue($dokument->ruta_via_1 ?? '');
        $sheet->getCell('D57')->setValue($dokument->ruta_via_2 ?? '');
        $sheet->getCell('D58')->setValue($dokument->ruta_via_3 ?? '');
        $sheet->getCell('D59')->setValue($dokument->lokacija_istovara ?? '');
        $sheet->getCell('D62')->setValue($dokument->prevoznik_dozvola_broj ?? '');
        $sheet->getCell('D63')->setValue($this->formatDatum($dokument->prevoznik_dozvola_datum));
        $sheet->getCell('D64')->setValue($this->formatDatum($dokument->prevoznik_datum_prijema));
        $sheet->getCell('D65')->setValue($dokument->prevoznik_odgovorno_lice_prijem ?? '');
        $sheet->getCell('D66')->setValue($dokument->prevoznik_telefon_lica_prijem ?? '');
        $sheet->getCell('D68')->setValue($this->formatDatum($dokument->prevoznik_datum_predaje));
        $sheet->getCell('D69')->setValue($dokument->prevoznik_odgovorno_lice_predaja ?? '');
        $sheet->getCell('D70')->setValue($dokument->prevoznik_telefon_lica_predaja ?? '');

        // DEO D
        $sheet->getCell('C74')->setValue($dokument->primalac_pib ?? '');
        $sheet->getCell('C75')->setValue($dokument->primalac_maticni ?? '');
        $sheet->getCell('C76')->setValue($dokument->primalac_naziv ?? '');
        $sheet->getCell('C77')->setValue($dokument->primalac_opstina ?? '');
        $sheet->getCell('C78')->setValue($dokument->primalac_mesto ?? '');
        $sheet->getCell('C79')->setValue($dokument->primalac_postanski ?? '');
        $sheet->getCell('C80')->setValue($dokument->primalac_ulica ?? '');
        $sheet->getCell('C81')->setValue($dokument->primalac_telefon ?? '');
        $sheet->getCell('C82')->setValue($dokument->primalac_faks ?? '');
        $sheet->getCell('C83')->setValue($dokument->primalac_email ?? '');
        $sheet->getCell('D85')->setValue($dokument->primalac_tip === 'skladiste' ? 'X' : '');
        $sheet->getCell('D86')->setValue($dokument->primalac_tip === 'tretman' ? 'X' : '');
        $sheet->getCell('D87')->setValue($dokument->primalac_tip === 'odlaganje' ? 'X' : '');
        $sheet->getCell('D88')->setValue($dokument->primalac_dozvola_broj ?? '');
        $sheet->getCell('D89')->setValue($this->formatDatum($dokument->primalac_dozvola_datum));
        $sheet->getCell('D92')->setValue($this->formatDatum($dokument->primalac_datum_prijema));
        $sheet->getCell('D93')->setValue($dokument->primalac_odgovorno_lice ?? '');
        $sheet->getCell('D94')->setValue($dokument->primalac_telefon_lica ?? '');

        return $spreadsheet;
    }

    private function formatDatum(mixed $datum): string
    {
        if (! $datum) {
            return '';
        }

        return Carbon::parse($datum)->format('d.m.Y.');
    }

    private function fizickoStanjeLabel(?string $value): string
    {
        return match ($value) {
            'cvrsta-prah' => 'Čvrsta-prah',
            'cvrsta-komadi' => 'Čvrsta-komadi',
            'viskozna-pasta' => 'Viskozna pasta',
            'tecna' => 'Tečna',
            'talog' => 'Talog',
            default => $value ?? '',
        };
    }

    private function safeFilename(string $value): string
    {
        return preg_replace('/[^\p{L}\p{N}\-_]+/u', '_', $value) ?: 'dokument';
    }
}
