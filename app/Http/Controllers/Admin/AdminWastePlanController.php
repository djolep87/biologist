<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WastePlan;
use App\Services\WastePlanContentParser;
use App\Services\WastePlanGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminWastePlanController extends Controller
{
    public function index()
    {
        return view('admin.waste-plans.index');
    }

    public function generate(Request $request, WastePlanGeneratorService $generator, WastePlanContentParser $parser): JsonResponse
    {
        $validated = $this->validateForm($request);

        try {
            $result = $generator->generate($validated, $request->user());

            return response()->json([
                'success' => true,
                'plan' => [
                    'id' => $result['plan']->id,
                    'company_name' => $result['plan']->company_name,
                    'plan_content' => $result['plan']->plan_content,
                    'generated_at' => $result['plan']->generated_at->toIso8601String(),
                    'word_count' => $result['word_count'],
                    'missing_pages' => $result['missing_pages'],
                    'needs_regeneration' => $result['needs_regeneration'],
                ],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function pdf(WastePlan $wastePlan, WastePlanContentParser $parser): Response
    {
        $pages = $parser->parsePages($wastePlan->plan_content);
        $year = $wastePlan->generated_at->year;

        $pdf = Pdf::loadView('pdf.waste-plan', [
            'plan' => $wastePlan,
            'pages' => $pages,
            'year' => $year,
        ])->setPaper('a4', 'portrait');

        $filename = 'plan-upravljanja-otpadom-'.Str::slug($wastePlan->company_name).'.pdf';

        return $pdf->download($filename);
    }

    public function destroy(WastePlan $wastePlan): JsonResponse
    {
        $wastePlan->delete();

        return response()->json(['success' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateForm(Request $request): array
    {
        return $request->validate([
            'naziv_firme' => ['required', 'string', 'max:255'],
            'pib' => ['required', 'string', 'max:20'],
            'maticni_broj' => ['required', 'string', 'max:20'],
            'adresa_sedista' => ['required', 'string', 'max:500'],
            'mesto_opstina' => ['required', 'string', 'max:255'],
            'odgovorno_lice' => ['required', 'string', 'max:255'],
            'kontakt_eko' => ['nullable', 'string', 'max:255'],
            'telefon_email' => ['nullable', 'string', 'max:255'],
            'delatnost' => ['required', 'string', 'max:500'],
            'broj_zaposlenih' => ['nullable', 'integer', 'min:0'],
            'vrste_otpada' => ['nullable', 'string', 'max:5000'],
            'indeksni_brojevi' => ['nullable', 'string', 'max:5000'],
            'procenjene_kolicine' => ['nullable', 'string', 'max:5000'],
            'nacin_postupanja' => ['nullable', 'string', 'max:5000'],
            'ugovori_operateri' => ['nullable', 'string', 'max:5000'],
            'lokacija_pogon' => ['nullable', 'string', 'max:500'],
            'povrsina_objekta' => ['nullable', 'string', 'max:255'],
            'ima_skladiste' => ['boolean'],
            'opis_skladista' => ['nullable', 'string', 'max:5000'],
            'zakoni_propisi' => ['nullable', 'string', 'max:5000'],
            'rok_vazenja' => ['required', Rule::in(['1 godina', '2 godine', '3 godine', '5 godina'])],
            'ciljevi_smanjenja' => ['nullable', 'string', 'max:5000'],
            'posebne_napomene' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
