<x-layouts.admin title="Zahtevi za predaju" header="Zahtevi za predaju otpada">
    <form method="GET" action="{{ route('admin.zahtevi.index') }}"
        class="mb-6 flex flex-col lg:flex-row gap-3 bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
        <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Svi statusi</option>
            <option value="na_cekanju" @selected(request('status') === 'na_cekanju')>Na čekanju</option>
            <option value="u_obradi" @selected(request('status') === 'u_obradi')>U obradi</option>
            <option value="zavrseno" @selected(request('status') === 'zavrseno')>Završeno</option>
            <option value="odbijeno" @selected(request('status') === 'odbijeno')>Odbijeno</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Pretraži firmu..."
            class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <input type="date" name="datum_od" value="{{ request('datum_od') }}"
            class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" title="Datum od">
        <input type="date" name="datum_do" value="{{ request('datum_do') }}"
            class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" title="Datum do">
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">
            Filtriraj
        </button>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#f8f9f4]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Firma</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Otpad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Masa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Izv.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Primljeno</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($zahtevi as $zahtev)
                        @php
                            $badge = $zahtev->status_badge;
                            $isNew = $zahtev->status === 'na_cekanju';
                        @endphp
                        <tr class="hover:bg-gray-50/80 {{ $isNew ? 'bg-amber-50/60 border-l-4 border-l-amber-400' : '' }}">
                            <td class="px-4 py-3 font-medium">{{ $zahtev->id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $zahtev->team->name }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $zahtev->naziv_otpada }}</p>
                                <p class="text-xs font-mono text-gray-500">{{ $zahtev->indeksni_broj }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ \App\Models\DnevnaEvidencija::formatKolicina($zahtev->masa_ukupno) }}</td>
                            <td class="px-4 py-3">{{ $zahtev->evidencije->count() }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                {{ $zahtev->created_at->format('d.m.Y.') }}<br>
                                <span class="text-xs text-gray-400">{{ $zahtev->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.zahtevi.show', $zahtev) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 hover:bg-indigo-50"
                                    title="Pregled">
                                    👁
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">Nema zahteva za prikaz.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($zahtevi->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $zahtevi->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
