<x-layouts.dashboard :title="'DEO1 — '.$site->naziv_gradilista">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('gradilista.show', $site) }}" class="text-sm text-gray-500 hover:text-gray-800">← {{ $site->naziv_gradilista }}</a>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mt-1">DEO1 Dnevnik gradilišta</h2>
                <p class="text-sm text-indigo-700 font-medium mt-1">Dozvola: {{ $site->broj_gradevinske_dozvole }}</p>
            </div>
            @if (! $site->isZavrseno())
                <a href="{{ route('gradilista.deo1.create', $site) }}" class="bio-btn-primary self-start">+ Novi unos</a>
                <a href="{{ route('dko-zahtevi.create', ['gradiliste_id' => $site->id]) }}" class="bio-btn-accent self-start text-sm">Pošalji adminu za DKO</a>
            @else
                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">Gradilište završeno — dnevnik zaključan</span>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($zapisi->isEmpty())
                <div class="px-6 py-16 text-center text-gray-500">
                    <p class="mb-3">Nema DEO1 zapisa za ovo gradilište.</p>
                    @if (! $site->isZavrseno())
                        <a href="{{ route('gradilista.deo1.create', $site) }}" class="bio-btn-primary inline-flex">+ Novi unos</a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Datum</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Šifra otpada</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Naziv</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Karakter</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Proizvedeno (t)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Predato (t)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Stanje (t)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Akcije</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($zapisi as $zapis)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $zapis->datum?->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-3 font-mono text-xs font-semibold">{{ $zapis->indeksni_broj }}</td>
                                    <td class="px-4 py-3">{{ $zapis->naziv_otpada }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs px-2 py-0.5 rounded-full
                                            {{ $zapis->karakter_otpada === 'opasan' ? 'bg-red-100 text-red-800' : ($zapis->karakter_otpada === 'inertan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $zapis->karakter_otpada }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium">{{ number_format((float) $zapis->proizvedena_kolicina, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format((float) $zapis->predata_kolicina, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format((float) $zapis->stanje_na_skladistu, 3, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        @if ($zapis->predat_operateru)
                                            <span class="text-xs text-green-700 font-medium">Predato</span>
                                        @else
                                            <span class="text-xs text-amber-700 font-medium">Na čekanju</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('gradilista.deo1.show', [$site, $zapis]) }}" class="text-xs text-gray-600 hover:underline">Pregled</a>
                                        @if (! $zapis->predat_operateru && (! $site->isZavrseno() || auth()->user()->is_super_admin))
                                            <form method="POST" action="{{ route('gradilista.deo1.destroy', [$site, $zapis]) }}" class="inline" onsubmit="return confirm('Obrisati unos?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:underline ml-2">Obriši</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">{{ $zapisi->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
