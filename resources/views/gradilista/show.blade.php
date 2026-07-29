@php
    $badge = match ($site->status_badge_color) {
        'green' => 'bg-green-100 text-green-800',
        'orange' => 'bg-orange-100 text-orange-800',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<x-layouts.dashboard :title="$site->naziv_gradilista">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <a href="{{ route('gradilista.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Moja gradilišta</a>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900">🏗️ {{ $site->naziv_gradilista }}</h2>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badge }}">{{ $site->status_label }}</span>
                    </div>
                    <p class="text-gray-600 mt-2">{{ $site->puna_adresa }}</p>
                    <p class="mt-2 text-base font-semibold text-indigo-700">Dozvola: {{ $site->broj_gradevinske_dozvole }}</p>
                    @if ($site->procijenjena_kolicina_otpada)
                        <p class="text-sm text-gray-500 mt-1">Procena otpada: {{ number_format((float) $site->procijenjena_kolicina_otpada, 2, ',', '.') }} t ({{ $site->tip_radova_label }})</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('gradilista.deo1.index', $site) }}" class="bio-btn-primary text-sm">DEO1 Dnevnik</a>
                    @if (! $site->isZavrseno())
                        <a href="{{ route('dko-zahtevi.create', ['gradiliste_id' => $site->id]) }}" class="bio-btn-accent text-sm">Pošalji adminu za DKO</a>
                    @endif
                    <a href="{{ route('gradilista.edit', $site) }}" class="bio-btn-secondary text-sm">Uredi</a>
                    @if (! $site->isZavrseno())
                        <form method="POST" action="{{ route('gradilista.zavrsi', $site) }}" onsubmit="return confirm('Završiti gradilište? DEO1 dnevnik će biti zaključan.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Završi gradilište</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Ukupno uneseno</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($ukupnoUneseno, 3, ',', '.') }} t</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Ukupno predato</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($ukupnoPredato, 3, ',', '.') }} t</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Na gradilištu</p>
                <p class="mt-2 text-2xl font-bold text-amber-700">{{ number_format($naGradilistu, 3, ',', '.') }} t</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">DEO1 zapisa</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $site->dnevne_evidencije_count }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Poslednji DEO1 zapisi</h3>
                <a href="{{ route('gradilista.deo1.index', $site) }}" class="text-sm text-green-700 font-medium hover:underline">Vidi sve →</a>
            </div>
            @if ($poslednjiZapisi->isEmpty())
                <div class="px-6 py-10 text-center text-gray-500 text-sm">
                    Još nema unosa.
                    @if (! $site->isZavrseno())
                        <a href="{{ route('gradilista.deo1.create', $site) }}" class="text-green-700 font-medium hover:underline">Dodaj prvi unos</a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Datum</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Šifra</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Vrsta otpada</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Količina (t)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($poslednjiZapisi as $zapis)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $zapis->datum?->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $zapis->indeksni_broj }}</td>
                                    <td class="px-4 py-3">{{ $zapis->naziv_otpada }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ number_format((float) $zapis->proizvedena_kolicina, 3, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">DKO dokumenti (pregled)</h3>
                <p class="text-xs text-gray-500 mt-1">DKO obrasce generiše administrator. Ovde možete pregledati i preuzeti postojeće.</p>
            </div>
            @if ($dokumenti->isEmpty())
                <div class="px-6 py-10 text-center text-gray-500 text-sm">Nema DKO dokumenata za ovo gradilište.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Broj izveštaja</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Datum</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Šifra</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Masa (t)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Akcije</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($dokumenti as $dok)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-indigo-700 font-semibold">{{ $dok->broj_izvestaja ?: $dok->broj_dokumenta }}</td>
                                    <td class="px-4 py-3">{{ $dok->datum_predaje?->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $dok->indeksni_broj }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format((float) $dok->masa_ukupno, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('doko.download', $dok) }}" class="text-emerald-700 text-xs font-medium hover:underline">📥 DOKO</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
