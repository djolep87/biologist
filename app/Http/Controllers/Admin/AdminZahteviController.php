<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZahtevPredaje;
use App\Notifications\ZahtevOdbijen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminZahteviController extends Controller
{
    public function index(Request $request): View
    {
        $zahtevi = ZahtevPredaje::with(['team', 'user', 'evidencije'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('team', fn ($tq) => $tq->where('name', 'like', "%{$search}%"));
            })
            ->when($request->filled('datum_od'), fn ($q) => $q->whereDate('created_at', '>=', $request->datum_od))
            ->when($request->filled('datum_do'), fn ($q) => $q->whereDate('created_at', '<=', $request->datum_do))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.zahtevi.index', compact('zahtevi'));
    }

    public function show(ZahtevPredaje $zahtev): View
    {
        if ($zahtev->status === 'na_cekanju') {
            $zahtev->update([
                'status' => 'u_obradi',
                'admin_id' => auth()->id(),
                'admin_odgovorio_at' => now(),
            ]);
        }

        $zahtev->load(['team.owner', 'user', 'evidencije', 'dokumentKretanja', 'admin']);

        return view('admin.zahtevi.show', compact('zahtev'));
    }

    public function odbij(Request $request, ZahtevPredaje $zahtev): RedirectResponse
    {
        abort_if(in_array($zahtev->status, ['zavrseno', 'odbijeno'], true), 422, 'Zahtev je već obrađen.');

        $request->validate([
            'napomena_admina' => 'required|string|max:500',
        ], [
            'napomena_admina.required' => 'Unesite razlog odbijanja.',
        ]);

        $zahtev->update([
            'status' => 'odbijeno',
            'napomena_admina' => $request->napomena_admina,
            'admin_id' => auth()->id(),
            'admin_odgovorio_at' => now(),
        ]);

        $zahtev->user->notify(new ZahtevOdbijen($zahtev));

        return redirect()
            ->route('admin.zahtevi.index')
            ->with('success', 'Zahtev je odbijen i klijent je obavešten.');
    }
}
