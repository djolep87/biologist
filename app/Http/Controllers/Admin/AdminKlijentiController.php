<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DnevnaEvidencija;
use App\Models\DokumentKretanja;
use App\Models\Team;
use App\Support\AdminTeamContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminKlijentiController extends Controller
{
    public function index(): View
    {
        $klijenti = Team::query()
            ->with('owner')
            ->latest()
            ->get()
            ->map(function (Team $team) {
                $team->evidencije_count = DnevnaEvidencija::where('team_id', $team->id)->count();

                return $team;
            });

        return view('admin.klijenti.index', compact('klijenti'));
    }

    public function show(Team $team): View
    {
        $team->load('owner');

        $evidencije = DnevnaEvidencija::where('team_id', $team->id)
            ->orderByDesc('datum')
            ->take(10)
            ->get();

        $dokumenti = DokumentKretanja::where('team_id', $team->id)
            ->withCount('dnevneEvidencije')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.klijenti.show', compact('team', 'evidencije', 'dokumenti'));
    }

    public function radiSa(Team $team): RedirectResponse
    {
        AdminTeamContext::select($team->id);

        return redirect()->route('admin.klijent-rad');
    }
}
