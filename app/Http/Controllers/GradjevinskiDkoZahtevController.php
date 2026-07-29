<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradjevinskiDkoZahtevRequest;
use App\Models\ConstructionSite;
use App\Models\DnevnaEvidencija;
use App\Models\GradjevinskiDkoZahtev;
use App\Models\User;
use App\Notifications\NoviGradjevinskiDkoZahtev;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GradjevinskiDkoZahtevController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GradjevinskiDkoZahtev::class);

        $teamId = $request->user()->currentTeam?->id;
        abort_unless($teamId, 422, 'Izaberite firmu.');

        $zahtevi = GradjevinskiDkoZahtev::forTeam($teamId)
            ->with(['constructionSite', 'dokumentKretanja'])
            ->withCount('evidencije')
            ->latest('poslato_at')
            ->paginate(20);

        return view('dko-zahtevi.index', compact('zahtevi'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $this->authorize('create', GradjevinskiDkoZahtev::class);

        $teamId = $request->user()->currentTeam?->id;
        abort_unless($teamId, 422, 'Izaberite firmu.');

        $siteId = $request->integer('gradiliste_id') ?: null;

        if (! $siteId) {
            $sites = ConstructionSite::forTeam($teamId)
                ->aktivna()
                ->orderBy('naziv_gradilista')
                ->get();

            return view('dko-zahtevi.create-site', compact('sites'));
        }

        $site = ConstructionSite::forTeam($teamId)->findOrFail($siteId);

        if ($site->status !== ConstructionSite::STATUS_AKTIVNO) {
            return redirect()
                ->route('dko-zahtevi.create')
                ->withErrors(['error' => 'Zahtev možete slati samo za aktivna gradilišta.']);
        }

        $zapisi = $site->dnevneEvidencije()
            ->where('predat_operateru', false)
            ->whereIn('dko_status', ['slobodan', 'u_zahtevu'])
            ->orderByDesc('datum')
            ->orderByDesc('id')
            ->get();

        return view('dko-zahtevi.create', compact('site', 'zapisi'));
    }

    public function store(GradjevinskiDkoZahtevRequest $request): RedirectResponse
    {
        $teamId = $request->user()->currentTeam->id;
        $data = $request->validated();
        $ids = array_map('intval', $data['deo1_zapisi']);

        $zahtev = DB::transaction(function () use ($request, $teamId, $data, $ids) {
            $broj = GradjevinskiDkoZahtev::generateBrojZahteva($teamId);

            $zapisi = DnevnaEvidencija::query()
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get();

            $masa = round((float) $zapisi->sum(fn ($z) => (float) $z->stanje_na_skladistu ?: (float) $z->proizvedena_kolicina), 3);

            $zahtev = GradjevinskiDkoZahtev::create([
                'team_id' => $teamId,
                'construction_site_id' => $data['construction_site_id'],
                'kreirao_korisnik_id' => $request->user()->id,
                'broj_zahteva' => $broj['broj_zahteva'],
                'redni_broj' => $broj['redni_broj'],
                'status' => GradjevinskiDkoZahtev::STATUS_NA_CEKANJU,
                'masa_ukupno' => $masa,
                'napomena_klijenta' => $data['napomena_klijenta'] ?? null,
                'poslato_at' => now(),
            ]);

            $zahtev->evidencije()->attach($ids);

            DnevnaEvidencija::whereIn('id', $ids)->update([
                'dko_status' => 'u_zahtevu',
                'gradjevinski_dko_zahtev_id' => $zahtev->id,
            ]);

            return $zahtev;
        });

        User::where('is_super_admin', true)->each(
            fn (User $admin) => $admin->notify(new NoviGradjevinskiDkoZahtev($zahtev))
        );

        return redirect()
            ->route('dko-zahtevi.show', $zahtev)
            ->with('success', "✅ Zahtev {$zahtev->broj_zahteva} je poslat administratoru. Bićete obavešteni kada DKO dokument bude spreman.");
    }

    public function show(GradjevinskiDkoZahtev $zahtev): View
    {
        $this->authorize('view', $zahtev);

        $zahtev->load(['constructionSite', 'evidencije', 'dokumentKretanja', 'admin', 'kreirao']);

        return view('dko-zahtevi.show', compact('zahtev'));
    }

    public function preuzmiDko(GradjevinskiDkoZahtev $zahtev): StreamedResponse|RedirectResponse
    {
        $this->authorize('view', $zahtev);

        abort_unless(
            $zahtev->status === GradjevinskiDkoZahtev::STATUS_ZAVRSENO && $zahtev->dokument_kretanja_id,
            404,
            'DKO dokument još nije spreman.'
        );

        return redirect()->route('doko.download', $zahtev->dokument_kretanja_id);
    }

    public function destroy(GradjevinskiDkoZahtev $zahtev): RedirectResponse
    {
        $this->authorize('delete', $zahtev);

        DB::transaction(function () use ($zahtev) {
            $zahtev->oslobodiEvidencije();
            $zahtev->delete();
        });

        return redirect()
            ->route('dko-zahtevi.index')
            ->with('success', 'Zahtev je otkazan. DEO1 zapisi su ponovo slobodni.');
    }
}
