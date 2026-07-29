@php $badge = $zahtev->status_badge; @endphp

<x-layouts.dashboard :title="'Zahtev '.$zahtev->broj_zahteva">
    <div class="max-w-4xl mx-auto space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <div>
            <a href="{{ route('dko-zahtevi.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← DKO Zahtevi</a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900">Zahtev {{ $zahtev->broj_zahteva }}</h2>
                    <p class="text-gray-600 mt-2">Gradilište: {{ $zahtev->constructionSite?->naziv_gradilista }}</p>
                    <p class="text-sm text-gray-500 mt-1">Poslato: {{ $zahtev->poslato_at?->format('d.m.Y. \u\ H:i') }}</p>
                    @if ($zahtev->napomena_klijenta)
                        <p class="text-sm text-gray-700 mt-2 italic">Napomena: „{{ $zahtev->napomena_klijenta }}”</p>
                    @endif
                </div>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
            </div>
        </div>

        @if ($zahtev->status === 'zavrseno' && $zahtev->dokumentKretanja)
            <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-5">
                <h3 class="font-semibold text-green-900 text-lg">✅ DKO dokument je spreman!</h3>
                <p class="text-sm text-green-800 mt-2">Generisao: {{ $zahtev->admin?->name ?? 'Administrator' }}</p>
                <p class="text-sm text-green-800">Datum: {{ $zahtev->zavrseno_at?->format('d.m.Y. \u\ H:i') }}</p>
                @if ($zahtev->napomena_admina)
                    <p class="text-sm text-green-800 mt-1">Napomena admina: {{ $zahtev->napomena_admina }}</p>
                @endif
                <a href="{{ route('dko-zahtevi.preuzmi-dko', $zahtev) }}" class="inline-flex mt-4 bio-btn-primary">⬇ Preuzmi DKO</a>
            </div>
        @elseif ($zahtev->status === 'odbijeno')
            <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-5">
                <h3 class="font-semibold text-red-900 text-lg">❌ Zahtev je odbijen</h3>
                <p class="text-sm text-red-800 mt-2">Razlog: {{ $zahtev->razlog_odbijanja }}</p>
                <a href="{{ route('dko-zahtevi.create', ['gradiliste_id' => $zahtev->construction_site_id]) }}" class="inline-flex mt-4 bio-btn-primary">Pošalji novi zahtev</a>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">DEO1 zapisi u zahtevu</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Datum</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Vrsta otpada</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Količina</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($zahtev->evidencije as $zapis)
                            <tr>
                                <td class="px-4 py-3">{{ $zapis->datum?->format('d.m.Y.') }}</td>
                                <td class="px-4 py-3"><span class="font-mono text-xs">{{ $zapis->indeksni_broj }}</span> – {{ $zapis->naziv_otpada }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format((float) $zapis->proizvedena_kolicina, 3, ',', '.') }} t</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-semibold text-right">UKUPNO</td>
                            <td class="px-4 py-3 text-right font-bold">{{ number_format((float) $zahtev->masa_ukupno, 3, ',', '.') }} t</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if ($zahtev->status === 'na_cekanju')
            <form method="POST" action="{{ route('dko-zahtevi.destroy', $zahtev) }}" onsubmit="return confirm('Otkazati zahtev?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:underline">Otkaži zahtev</button>
            </form>
        @endif
    </div>
</x-layouts.dashboard>
