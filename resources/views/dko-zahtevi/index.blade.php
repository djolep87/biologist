<x-layouts.dashboard title="DKO Zahtevi">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900">DKO Zahtevi</h2>
                <p class="text-sm text-gray-500 mt-1">Zahtevi za generisanje DKO dokumenata za građevinski otpad.</p>
            </div>
            <a href="{{ route('dko-zahtevi.create') }}" class="bio-btn-primary self-start">+ Novi zahtev</a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($zahtevi->isEmpty())
                <div class="px-6 py-16 text-center text-gray-500">
                    <p class="mb-3">Nemate poslatih DKO zahteva.</p>
                    <a href="{{ route('dko-zahtevi.create') }}" class="bio-btn-primary inline-flex">Pošalji prvi zahtev</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Broj zahteva</th>
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
                                <tr class="hover:bg-gray-50 {{ $zahtev->status === 'na_cekanju' ? 'bg-amber-50/40' : '' }}">
                                    <td class="px-4 py-3 font-mono font-semibold text-indigo-700">{{ $zahtev->broj_zahteva }}</td>
                                    <td class="px-4 py-3">{{ $zahtev->constructionSite?->naziv_gradilista }}</td>
                                    <td class="px-4 py-3">{{ $zahtev->poslato_at?->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ number_format((float) $zahtev->masa_ukupno, 3, ',', '.') }} t</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge['class'] }}" @if($zahtev->status === 'odbijeno' && $zahtev->razlog_odbijanja) title="{{ $zahtev->razlog_odbijanja }}" @endif>
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        @if ($zahtev->status === 'zavrseno' && $zahtev->dokument_kretanja_id)
                                            <a href="{{ route('dko-zahtevi.preuzmi-dko', $zahtev) }}" class="text-xs font-semibold text-emerald-700 hover:underline">Preuzmi DKO</a>
                                        @endif
                                        <a href="{{ route('dko-zahtevi.show', $zahtev) }}" class="text-xs font-semibold text-gray-700 hover:underline ml-2">Vidi</a>
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
</x-layouts.dashboard>
