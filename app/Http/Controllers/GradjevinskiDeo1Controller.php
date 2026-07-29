<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradjevinskiDeo1Request;
use App\Models\ConstructionSite;
use App\Models\DnevnaEvidencija;
use App\Models\KatalogOtpadaGrupa17;
use App\Support\TeamAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GradjevinskiDeo1Controller extends Controller
{
    public function index(ConstructionSite $site): View
    {
        $this->authorize('view', $site);

        $zapisi = $site->dnevneEvidencije()
            ->orderByDesc('datum')
            ->orderByDesc('id')
            ->paginate(20);

        return view('gradilista.deo1.index', [
            'site' => $site,
            'zapisi' => $zapisi,
        ]);
    }

    public function create(ConstructionSite $site): View|RedirectResponse
    {
        $this->authorize('view', $site);
        abort_unless(TeamAccess::canCreateEvidencija(auth()->user(), $site->team), 403);

        if ($site->isZavrseno()) {
            return redirect()
                ->route('gradilista.deo1.index', $site)
                ->withErrors(['error' => 'Gradilište je završeno. Nije moguće dodavati nove unose.']);
        }

        return view('gradilista.deo1.create', [
            'site' => $site,
            'katalog' => KatalogOtpadaGrupa17::grupisaniZaSelect(),
        ]);
    }

    public function store(GradjevinskiDeo1Request $request, ConstructionSite $site): RedirectResponse
    {
        abort_unless(TeamAccess::canCreateEvidencija($request->user(), $site->team), 403);

        if ($site->status === ConstructionSite::STATUS_ZAVRSENO) {
            return back()->withErrors([
                'error' => 'Gradilište je završeno. Nije moguće dodavati nove unose. Ako je greška, kontaktirajte administratora.',
            ]);
        }

        $data = $request->validated();
        $katalog = KatalogOtpadaGrupa17::where('sifra', $data['katalog_sifra'])->firstOrFail();
        $datum = $data['datum_unosa'];
        $kolicina = round((float) $data['kolicina_t'], 3);

        $zapis = DnevnaEvidencija::create([
            'team_id' => $site->team_id,
            'user_id' => $request->user()->id,
            'construction_site_id' => $site->id,
            'godina' => (int) date('Y', strtotime($datum)),
            'mesec' => (int) date('n', strtotime($datum)),
            'indeksni_broj' => $katalog->sifra,
            'naziv_otpada' => $katalog->naziv,
            'opis_otpada' => $data['nacin_nastanka'] ?? null,
            'nacin_nastanka' => $data['nacin_nastanka'] ?? null,
            'napomena' => $data['napomena'] ?? null,
            'karakter_otpada' => $katalog->karakter_otpada,
            'fizicko_stanje' => 'cvrsta-komadi',
            'lice_koje_vodi' => $request->user()->name,
            'datum' => $datum,
            'proizvedena_kolicina' => $kolicina,
            'predata_kolicina' => 0,
            'stanje_na_skladistu' => $kolicina,
            'nacin_odredjivanja' => '1',
        ]);

        DnevnaEvidencija::recalculateStanjeZaIndeks($site->team_id, $katalog->sifra, $site->id);

        $formatted = number_format($kolicina, 3, ',', '.');

        return redirect()
            ->route('gradilista.deo1.index', $site)
            ->with('success', "✅ Unos sačuvan – {$formatted} t ({$katalog->sifra}) dodato u dnevnik gradilišta");
    }

    public function show(ConstructionSite $site, DnevnaEvidencija $zapis): View
    {
        $this->authorize('view', $site);
        $this->assertBelongsToSite($site, $zapis);

        return view('gradilista.deo1.show', [
            'site' => $site,
            'zapis' => $zapis,
        ]);
    }

    public function destroy(ConstructionSite $site, DnevnaEvidencija $zapis): RedirectResponse
    {
        $this->authorize('view', $site);
        abort_unless(TeamAccess::canCreateEvidencija(auth()->user(), $site->team), 403);
        $this->assertBelongsToSite($site, $zapis);

        if ($site->isZavrseno() && ! auth()->user()->is_super_admin) {
            return back()->withErrors(['error' => 'Gradilište je završeno. Brisanje unosa nije dozvoljeno.']);
        }

        if ($zapis->predat_operateru) {
            return back()->withErrors(['error' => 'Unos je već predato operateru i ne može se obrisati.']);
        }

        $indeks = $zapis->indeksni_broj;
        $teamId = $zapis->team_id;
        $zapis->delete();

        DnevnaEvidencija::recalculateStanjeZaIndeks($teamId, $indeks, $site->id);

        return redirect()
            ->route('gradilista.deo1.index', $site)
            ->with('success', 'Unos je obrisan iz dnevnika gradilišta.');
    }

    private function assertBelongsToSite(ConstructionSite $site, DnevnaEvidencija $zapis): void
    {
        abort_unless(
            (int) $zapis->construction_site_id === (int) $site->id,
            404
        );
    }
}
