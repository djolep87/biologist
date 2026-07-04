<?php

namespace App\Actions\Admin;

use App\Models\DnevnaEvidencija;
use App\Models\DokumentKretanja;
use App\Models\ZahtevPredaje;
use Illuminate\Support\Facades\DB;

class DeleteDokumentKretanja
{
    public function delete(DokumentKretanja $dokument): void
    {
        DB::transaction(function () use ($dokument) {
            $teamId = $dokument->team_id;
            $indeksi = [];

            foreach ($dokument->dnevneEvidencije as $evidencija) {
                $indeksi[$evidencija->indeksni_broj] = true;

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

            $dokument->delete();

            foreach (array_keys($indeksi) as $indeksniBroj) {
                DnevnaEvidencija::recalculateStanjeZaIndeks($teamId, $indeksniBroj);
            }
        });
    }
}
