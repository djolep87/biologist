<?php

namespace App\Http\Controllers;

use App\Models\DnevnaEvidencija;
use App\Services\EvidencijaExportService;
use App\Support\AdminTeamContext;
use App\Support\TeamAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EvidencijaOtpadaController extends Controller
{
    public function __construct(
        private readonly EvidencijaExportService $exportService
    ) {}

    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $teamId = $this->currentTeamId();
        $validated = $this->validatedExportParams($request);

        return $this->exportService->generate(
            $teamId,
            $validated['godina'],
            $validated['mesec'],
            $validated['indeksni_broj']
        );
    }

    public function exportByRecord(DnevnaEvidencija $evidencija): BinaryFileResponse
    {
        abort_unless(
            TeamAccess::canAccessEvidencija(auth()->user(), $evidencija->team_id),
            403,
            'Nemate pristup ovom dokumentu.'
        );

        return $this->exportService->generate(
            $evidencija->team_id,
            $evidencija->godina,
            $evidencija->mesec,
            $evidencija->indeksni_broj
        );
    }

    public function print(Request $request): View
    {
        $validated = $this->validatedExportParams($request);

        return view('evidencija.print', [
            'exportUrl' => route('evidencija.export', $validated),
        ]);
    }

    private function currentTeamId(): int
    {
        $teamId = auth()->user()->currentTeam?->id;

        abort_unless($teamId, 403, 'Izaberite firmu pre preuzimanja obrasca.');

        return $teamId;
    }

    /**
     * @return array{godina: int, mesec: int, indeksni_broj: string}
     */
    private function validatedExportParams(Request $request): array
    {
        return $request->validate([
            'godina' => ['required', 'integer', 'min:2000', 'max:2100'],
            'mesec' => ['required', 'integer', 'between:1,12'],
            'indeksni_broj' => ['required', 'string', 'max:50'],
        ]);
    }
}
