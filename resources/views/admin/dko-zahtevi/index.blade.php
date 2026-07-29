<x-layouts.admin title="DKO Zahtevi" header="Građevinski DKO zahtevi">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <div class="grid sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-amber-100 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">⏳ Na čekanju</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $counts['na_cekanju'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-blue-100 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">🔄 U obradi</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $counts['u_obradi'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-green-100 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-green-700">✅ Završeno</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $counts['zavrseno'] }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach (['' => 'Svi', 'na_cekanju' => 'Na čekanju', 'u_obradi' => 'U obradi', 'zavrseno' => 'Završeni', 'odbijeno' => 'Odbijeni'] as $value => $label)
                <a href="{{ route('admin.dko-zahtevi.index', array_filter(['status' => $value ?: null])) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ ($filter ?: '') === $value ? 'bg-[#1e2430] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <form method="GET" class="max-w-md">
                    @if ($filter)<input type="hidden" name="status" value="{{ $filter }}">@endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pretraži broj, klijenta, gradilište..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </form>
            </div>

            @if ($zahtevi->isEmpty())
                <div class="px-6 py-14 text-center text-gray-500">Nema zahteva za prikaz.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700"># Zahteva</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Klijent</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Gradilište</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Poslato</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Količina</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Akcija</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($zahtevi as $zahtev)
                                @php $badge = $zahtev->status_badge; @endphp
                                <tr class="{{ $zahtev->status === 'na_cekanju' ? 'bg-amber-50/50' : 'hover:bg-gray-50' }}">
                                    <td class="px-4 py-3 font-mono font-semibold text-indigo-700">{{ $zahtev->broj_zahteva }}</td>
                                    <td class="px-4 py-3">{{ $zahtev->team?->name }}</td>
                                    <td class="px-4 py-3">{{ $zahtev->constructionSite?->naziv_gradilista }}</td>
                                    <td class="px-4 py-3">{{ $zahtev->poslato_at?->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ number_format((float) $zahtev->masa_ukupno, 3, ',', '.') }} t</td>
                                    <td class="px-4 py-3"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge['class'] }}">{{ $badge['label'] }}</span></td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.dko-zahtevi.show', $zahtev) }}" class="text-xs font-semibold text-indigo-700 hover:underline">
                                            {{ $zahtev->status === 'u_obradi' ? 'Nastavi' : 'Otvori' }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100">{{ $zahtevi->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.admin>
