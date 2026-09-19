<?php

namespace App\Actions\Admin;

use App\Models\DnevnaEvidencija;
use App\Models\DokumentKretanja;
use App\Models\GradjevinskiDkoZahtev;
use App\Models\ZahtevPredaje;
use Illuminate\Support\Facades\DB;

class DeleteDokumentKretanja
{
    public function delete(DokumentKretanja $dokument): void
    {
        DB::transaction(function () use ($dokument) {
            $teamId = $dokument->team_id;
            $grupe = [];

            foreach ($dokument->dnevneEvidencije as $evidencija) {
                $kljuc = ($evidencija->construction_site_id ?? 'null').'|'.$evidencija->indeksni_broj;
                $grupe[$kljuc] = [
                    'construction_site_id' => $evidencija->construction_site_id,
                    'indeksni_broj' => $evidencija->indeksni_broj,
                ];

                $evidencija->update([
                    'predat_operateru' => false,
                    'dokument_kretanja_id' => null,
                    'datum_predaje_operateru' => null,
                    'operater_naziv' => null,
                    'operater_dozvola_broj' => null,
                    'predat_operateru_r' => false,
                    'predat_operateru_d' => false,
                    'predat_sakupljacu' => false,
                    'izvoz' => false,
                    'r_oznaka' => null,
                    'd_oznaka' => null,
                    'predata_kolicina' => 0,
                    'naziv_primaoca' => null,
                    'broj_dozvole_primaoca' => null,
                ]);
            }

            ZahtevPredaje::where('dokument_kretanja_id', $dokument->id)->update([
                'dokument_kretanja_id' => null,
                'status' => 'u_obradi',
            ]);

            $gradjevinskiZahtev = GradjevinskiDkoZahtev::where('dokument_kretanja_id', $dokument->id)->first();

            if ($gradjevinskiZahtev) {
                $gradjevinskiZahtev->update([
                    'dokument_kretanja_id' => null,
                    'status' => GradjevinskiDkoZahtev::STATUS_U_OBRADI,
                    'zavrseno_at' => null,
                ]);

                $gradjevinskiZahtev->evidencije()->update([
                    'dko_status' => 'u_zahtevu',
                ]);
            }

            $dokument->delete();

            foreach ($grupe as $grupa) {
                DnevnaEvidencija::recalculateStanjeZaIndeks($teamId, $grupa['indeksni_broj'], $grupa['construction_site_id']);
            }
        });
    }
}
