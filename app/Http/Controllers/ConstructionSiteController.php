<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConstructionSiteRequest;
use App\Models\ConstructionSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConstructionSiteController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ConstructionSite::class);

        $filter = $request->string('status')->toString();
        $teamId = $request->user()->is_super_admin
            ? ($request->integer('team_id') ?: $request->user()->currentTeam?->id)
            : $request->user()->currentTeam?->id;

        abort_unless($teamId, 422, 'Izaberite firmu pre rada sa gradilištima.');

        $sites = ConstructionSite::query()
            ->forTeam($teamId)
            ->withCount(['dnevneEvidencije', 'dokumentiKretanja'])
            ->withSum('dnevneEvidencije as otpad_unesen', 'proizvedena_kolicina')
            ->when($filter === 'aktivno', fn ($q) => $q->aktivna())
            ->when($filter === 'završeno', fn ($q) => $q->zavrsena())
            ->when($filter === 'pauzirano', fn ($q) => $q->where('status', ConstructionSite::STATUS_PAUZIRANO))
            ->latest()
            ->get();

        return view('gradilista.index', [
            'sites' => $sites,
            'filter' => $filter,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ConstructionSite::class);

        return view('gradilista.create');
    }

    public function store(ConstructionSiteRequest $request): RedirectResponse
    {
        $team = $request->user()->currentTeam;
        abort_unless($team, 422, 'Izaberite firmu.');

        $data = $request->validated();
        $data['team_id'] = $team->id;
        $data['status'] = $data['status'] ?? ConstructionSite::STATUS_AKTIVNO;

        if (! empty($data['kvadratura_objekta']) && ! empty($data['tip_radova'])) {
            $data['procijenjena_kolicina_otpada'] = ConstructionSite::izracunajProcenu(
                (float) $data['kvadratura_objekta'],
                $data['tip_radova']
            );
        }

        $site = ConstructionSite::create($data);

        return redirect()
            ->route('gradilista.show', $site)
            ->with('success', 'Gradilište „'.$site->naziv_gradilista.'” je kreirano.');
    }

    public function show(ConstructionSite $site): View
    {
        $this->authorize('view', $site);

        $site->loadCount('dnevneEvidencije');

        $poslednjiZapisi = $site->dnevneEvidencije()
            ->orderByDesc('datum')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $dokumenti = $site->dokumentiKretanja()
            ->orderByDesc('datum_predaje')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('gradilista.show', [
            'site' => $site,
            'poslednjiZapisi' => $poslednjiZapisi,
            'dokumenti' => $dokumenti,
            'ukupnoUneseno' => $site->ukupnoProizvedeno(),
            'ukupnoPredato' => $site->ukupnoPredato(),
            'naGradilistu' => $site->naGradilistu(),
        ]);
    }

    public function edit(ConstructionSite $site): View
    {
        $this->authorize('update', $site);

        return view('gradilista.edit', [
            'site' => $site,
        ]);
    }

    public function update(ConstructionSiteRequest $request, ConstructionSite $site): RedirectResponse
    {
        $data = $request->validated();

        if ($site->isZavrseno()) {
            unset($data['status']);
        }

        if (! empty($data['kvadratura_objekta']) && ! empty($data['tip_radova'])) {
            $data['procijenjena_kolicina_otpada'] = ConstructionSite::izracunajProcenu(
                (float) $data['kvadratura_objekta'],
                $data['tip_radova']
            );
        }

        $site->update($data);

        return redirect()
            ->route('gradilista.show', $site)
            ->with('success', 'Gradilište je ažurirano.');
    }

    public function zavrsi(ConstructionSite $site): RedirectResponse
    {
        $this->authorize('zavrsi', $site);

        if ($site->isZavrseno()) {
            return back()->with('info', 'Gradilište je već završeno.');
        }

        $site->update([
            'status' => ConstructionSite::STATUS_ZAVRSENO,
            'datum_zavrsetka' => now()->toDateString(),
        ]);

        return redirect()
            ->route('gradilista.show', $site)
            ->with('success', 'Gradilište je završeno. DEO1 dnevnik je zaključan — novi unosi nisu mogući.');
    }
}
