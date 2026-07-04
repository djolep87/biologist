<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DnevnaEvidencija;
use App\Services\EvidencijaExportService;
use App\Support\AdminTeamContext;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminExportController extends Controller
{
    public function __construct(
        private readonly EvidencijaExportService $exportService
    ) {}

    public function deo1(Request $request): BinaryFileResponse
    {
        $teamId = AdminTeamContext::selectedTeamId();

        abort_unless($teamId, 422, 'Izaberite firmu u admin panelu pre preuzimanja DEO1 obrasca.');

        $validated = $request->validate([
            'godina' => ['required', 'integer', 'min:2000', 'max:2100'],
            'mesec' => ['required', 'integer', 'between:1,12'],
            'indeksni_broj' => ['required', 'string', 'max:50'],
        ]);

        return $this->exportService->generate(
            $teamId,
            $validated['godina'],
            $validated['mesec'],
            $validated['indeksni_broj']
        );
    }

    public function deo1ByRecord(DnevnaEvidencija $evidencija): BinaryFileResponse
    {
        $teamId = AdminTeamContext::selectedTeamId();

        abort_unless($teamId, 422, 'Izaberite firmu u admin panelu.');
        abort_unless($evidencija->team_id === $teamId, 403, 'Evidencija ne pripada izabranoj firmi.');

        return $this->exportService->generate(
            $evidencija->team_id,
            $evidencija->godina,
            $evidencija->mesec,
            $evidencija->indeksni_broj
        );
    }
}
