<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminOdbijGradjevinskiDkoZahtevRequest;
use App\Models\GradjevinskiDkoZahtev;
use App\Notifications\GradjevinskiDkoZahtevOdbijen;
use App\Support\AdminTeamContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminGradjevinskiDkoZahtevController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->is_super_admin, 403);

        $filter = $request->string('status')->toString();

        $counts = [
            'na_cekanju' => GradjevinskiDkoZahtev::naCekanju()->count(),
            'u_obradi' => GradjevinskiDkoZahtev::uObradi()->count(),
            'zavrseno' => GradjevinskiDkoZahtev::zavrseni()->count(),
            'odbijeno' => GradjevinskiDkoZahtev::where('status', GradjevinskiDkoZahtev::STATUS_ODBIJENO)->count(),
        ];

        $zahtevi = GradjevinskiDkoZahtev::query()
            ->with(['team', 'constructionSite', 'kreirao'])
            ->withCount('evidencije')
            ->when($filter !== '', fn ($q) => $q->where('status', $filter))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(function ($q) use ($s) {
                    $q->where('broj_zahteva', 'like', "%{$s}%")
                        ->orWhereHas('team', fn ($t) => $t->where('name', 'like', "%{$s}%"))
                        ->orWhereHas('constructionSite', fn ($c) => $c->where('naziv_gradilista', 'like', "%{$s}%"));
                });
            })
            ->orderByDesc('poslato_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.dko-zahtevi.index', compact('zahtevi', 'counts', 'filter'));
    }

    public function show(GradjevinskiDkoZahtev $zahtev): View
    {
        abort_unless(auth()->user()?->is_super_admin, 403);

        if ($zahtev->status === GradjevinskiDkoZahtev::STATUS_NA_CEKANJU) {
            $zahtev->update([
                'status' => GradjevinskiDkoZahtev::STATUS_U_OBRADI,
                'obradio_admin_id' => auth()->id(),
                'preuzeto_admin_at' => $zahtev->preuzeto_admin_at ?? now(),
            ]);
            $zahtev->refresh();
        }

        AdminTeamContext::select($zahtev->team_id);

        $zahtev->load([
            'team',
            'constructionSite',
            'kreirao',
            'admin',
            'evidencije',
            'dokumentKretanja',
        ]);

        return view('admin.dko-zahtevi.show', compact('zahtev'));
    }

    public function preuzmi(GradjevinskiDkoZahtev $zahtev): RedirectResponse
    {
        abort_unless(auth()->user()?->is_super_admin, 403);
        abort_unless($zahtev->status === GradjevinskiDkoZahtev::STATUS_NA_CEKANJU, 422);

        $zahtev->update([
            'status' => GradjevinskiDkoZahtev::STATUS_U_OBRADI,
            'obradio_admin_id' => auth()->id(),
            'preuzeto_admin_at' => now(),
        ]);

        AdminTeamContext::select($zahtev->team_id);

        return redirect()
            ->route('admin.dko-zahtevi.show', $zahtev)
            ->with('success', 'Zahtev je preuzet u obradu.');
    }

    public function generateForm(GradjevinskiDkoZahtev $zahtev): View|RedirectResponse
    {
        abort_unless(auth()->user()?->is_super_admin, 403);

        if (! in_array($zahtev->status, [
            GradjevinskiDkoZahtev::STATUS_NA_CEKANJU,
            GradjevinskiDkoZahtev::STATUS_U_OBRADI,
        ], true)) {
            return redirect()
                ->route('admin.dko-zahtevi.show', $zahtev)
                ->withErrors(['error' => 'Zahtev nije u statusu za generisanje DKO.']);
        }

        if ($zahtev->status === GradjevinskiDkoZahtev::STATUS_NA_CEKANJU) {
            $zahtev->update([
                'status' => GradjevinskiDkoZahtev::STATUS_U_OBRADI,
                'obradio_admin_id' => auth()->id(),
                'preuzeto_admin_at' => now(),
            ]);
        }

        AdminTeamContext::select($zahtev->team_id);

        $zahtev->load(['team', 'constructionSite', 'evidencije', 'kreirao']);

        $site = $zahtev->constructionSite;
        if (! $site || trim((string) $site->broj_gradevinske_dozvole) === '') {
            return redirect()
                ->route('admin.dko-zahtevi.show', $zahtev)
                ->withErrors(['error' => '❌ Nije moguće generisati DKO – gradilište nema unesen broj građevinske dozvole.']);
        }

        return view('admin.dko-zahtevi.generate', compact('zahtev'));
    }

    public function odbij(AdminOdbijGradjevinskiDkoZahtevRequest $request, GradjevinskiDkoZahtev $zahtev): RedirectResponse
    {
        abort_unless(in_array($zahtev->status, [
            GradjevinskiDkoZahtev::STATUS_NA_CEKANJU,
            GradjevinskiDkoZahtev::STATUS_U_OBRADI,
        ], true), 422);

        DB::transaction(function () use ($request, $zahtev) {
            $zahtev->oslobodiEvidencije();

            $zahtev->update([
                'status' => GradjevinskiDkoZahtev::STATUS_ODBIJENO,
                'razlog_odbijanja' => $request->validated('razlog_odbijanja'),
                'napomena_admina' => $request->validated('razlog_odbijanja'),
                'obradio_admin_id' => auth()->id(),
                'zavrseno_at' => now(),
            ]);
        });

        $zahtev->kreirao?->notify(new GradjevinskiDkoZahtevOdbijen($zahtev->fresh()));

        return redirect()
            ->route('admin.dko-zahtevi.index')
            ->with('success', "Zahtev {$zahtev->broj_zahteva} je odbijen. Klijent je obavešten.");
    }
}
