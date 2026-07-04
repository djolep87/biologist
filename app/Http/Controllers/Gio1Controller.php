<?php

namespace App\Http\Controllers;

use App\Models\GodisnjIzvestaj;
use App\Services\Gio1ExportService;
use App\Support\TeamAccess;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Gio1Controller extends Controller
{
    public function __construct(
        private readonly Gio1ExportService $exportService
    ) {}

    public function download(GodisnjIzvestaj $izvestaj): StreamedResponse
    {
        abort_unless(
            TeamAccess::canAccessTeam(auth()->user(), $izvestaj->team_id),
            403
        );

        $izvestaj->load('team');

        return $this->exportService->download($izvestaj);
    }
}
