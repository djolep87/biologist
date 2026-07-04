<?php

namespace App\Http\Controllers;

use App\Models\Operater;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperaterApiController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $indeksni = $request->get('indeksni_broj', '');

        $operateri = Operater::aktivni()
            ->when($query, fn ($q) => $q->where(function ($q) use ($query) {
                $q->where('naziv', 'like', "%{$query}%")
                    ->orWhere('kratki_naziv', 'like', "%{$query}%")
                    ->orWhere('pib', 'like', "%{$query}%");
            }))
            ->when($indeksni, fn ($q) => $q->where(function ($q) use ($indeksni) {
                $q->whereNull('prihvata_indeksne_brojeve')
                    ->orWhereJsonContains('prihvata_indeksne_brojeve', $indeksni);
            }))
            ->orderBy('kratki_naziv')
            ->get(['id', 'kratki_naziv', 'naziv', 'pib', 'dozvola_vazi_do', 'dozvola_broj']);

        return response()->json($operateri);
    }

    public function show(Operater $operater): JsonResponse
    {
        return response()->json([
            'id' => $operater->id,
            'naziv' => $operater->naziv,
            'pib' => $operater->pib,
            'maticni_broj' => $operater->maticni_broj,
            'opstina' => $operater->opstina,
            'mesto' => $operater->mesto,
            'postanski_broj' => $operater->postanski_broj,
            'ulica' => $operater->ulica,
            'telefon' => $operater->telefon,
            'faks' => $operater->faks,
            'email' => $operater->email,
            'dozvola_broj' => $operater->dozvola_broj,
            'dozvola_datum_izdavanja' => $operater->dozvola_datum_izdavanja?->format('Y-m-d'),
            'dozvola_vazi_do' => $operater->dozvola_vazi_do?->format('Y-m-d'),
            'r_oznaka' => $operater->r_oznaka,
            'd_oznaka' => $operater->d_oznaka,
        ]);
    }
}
