<x-layouts.admin :title="'Zahtev #' . $zahtev->id" :header="'Zahtev #' . $zahtev->id . ' — ' . $zahtev->team->name">
    @php $badge = $zahtev->status_badge; @endphp

    <div
        x-data="{ notifyMessage: null, notifyType: 'success' }"
        @notify.window="notifyMessage = $event.detail.message; notifyType = $event.detail.type || 'success'; setTimeout(() => notifyMessage = null, 4000)"
    >
        <div
            x-show="notifyMessage"
            x-cloak
            x-transition
            class="fixed top-4 right-4 z-[60] max-w-sm rounded-xl shadow-lg px-4 py-3 text-sm font-medium text-white"
            :class="notifyType === 'error' ? 'bg-red-600' : 'bg-green-600'"
            x-text="notifyMessage"
        ></div>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('admin.zahtevi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Zahtevi</a>
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $badge['class'] }}">{{ $badge['label'] }}</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900 mb-4">Informacije o zahtevu</h2>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500 text-xs uppercase">Firma</dt><dd class="font-medium mt-0.5">{{ $zahtev->team->name }}</dd></div>
                    <div><dt class="text-gray-500 text-xs uppercase">PIB</dt><dd class="mt-0.5">{{ $zahtev->team->pib ?? '—' }}</dd></div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase">Kontakt</dt>
                        <dd class="mt-0.5">
                            {{ $zahtev->user->email }}
                            @if ($zahtev->team->kontakt_telefon)
                                | {{ $zahtev->team->kontakt_telefon }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase">Poslao</dt>
                        <dd class="mt-0.5">{{ $zahtev->user->name }} — {{ $zahtev->created_at->format('d.m.Y. \u\ H:i') }}</dd>
                    </div>
                    @if ($zahtev->napomena_klijenta)
                        <div>
                            <dt class="text-gray-500 text-xs uppercase">Napomena klijenta</dt>
                            <dd class="mt-0.5 text-gray-800 italic">"{{ $zahtev->napomena_klijenta }}"</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900 mb-4">Otpad za predaju</h2>
                <dl class="space-y-3 text-sm mb-4">
                    <div><dt class="text-gray-500 text-xs uppercase">Indeksni broj</dt><dd class="font-mono font-medium mt-0.5">{{ $zahtev->indeksni_broj }}</dd></div>
                    <div><dt class="text-gray-500 text-xs uppercase">Vrsta otpada</dt><dd class="font-medium mt-0.5">{{ $zahtev->naziv_otpada }}</dd></div>
                    <div><dt class="text-gray-500 text-xs uppercase">Ukupna masa</dt><dd class="font-medium mt-0.5">{{ \App\Models\DnevnaEvidencija::formatKolicina($zahtev->masa_ukupno) }}</dd></div>
                </dl>

                <p class="text-xs font-semibold uppercase text-gray-500 mb-2">Izabrani dnevni izveštaji</p>
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="min-w-full text-xs">
                        <thead class="bg-[#f8f9f4]">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-500">Datum</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-500">Proiz.</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-500">Predata</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-500">Stanje</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($zahtev->evidencije as $ev)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $ev->datum->format('d.m.Y.') }}</td>
                                    <td class="px-3 py-2">{{ \App\Models\DnevnaEvidencija::formatKolicina($ev->proizvedena_kolicina) }}</td>
                                    <td class="px-3 py-2">{{ \App\Models\DnevnaEvidencija::formatKolicina($ev->predata_kolicina) }}</td>
                                    <td class="px-3 py-2 font-medium">{{ \App\Models\DnevnaEvidencija::formatKolicina($ev->stanje_na_skladistu) }}</td>
                                    <td class="px-3 py-2">
                                        @if ($ev->predat_operateru)
                                            <span class="text-green-700">✅ Predato</span>
                                        @else
                                            <span class="text-amber-700">⏳ Čeka</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($zahtev->status === 'zavrseno' && $zahtev->dokumentKretanja)
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-6">
                <h3 class="font-semibold text-green-900 mb-2">DOKO dokument kreiran</h3>
                <p class="text-sm text-green-800 mb-3">
                    Broj dokumenta: <strong class="font-mono">{{ $zahtev->dokumentKretanja->broj_dokumenta }}</strong>
                </p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('doko.download', $zahtev->dokumentKretanja) }}"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">📥 DOKO</a>
                    <a href="{{ route('doko.deo1', $zahtev->dokumentKretanja) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">📋 DEO1</a>
                </div>
                @if ($zahtev->napomena_admina)
                    <p class="text-sm text-green-800 mt-3">Napomena admina: {{ $zahtev->napomena_admina }}</p>
                @endif
            </div>
        @elseif ($zahtev->status === 'odbijeno')
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
                <h3 class="font-semibold text-red-900 mb-2">Zahtev odbijen</h3>
                <p class="text-sm text-red-800">{{ $zahtev->napomena_admina }}</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <h2 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">Obrada zahteva</h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Otvorite pun DOKO wizard — isti kao na stranici „Rad sa klijentom”. Evidencije iz zahteva su već izabrane.
                        </p>
                    </div>
                    <button type="button"
                        x-on:click="$dispatch('openDokoFormFromZahtev', { zahtevId: {{ $zahtev->id }} })"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shrink-0">
                        🚛 Kreiraj DOKO dokument
                    </button>
                </div>

                <div class="rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3 text-sm text-indigo-900">
                    Kliknite dugme iznad da popunite kompletan DOKO obrazac (DEO A–D): operater, prevoznik, primalac i ostala polja.
                    Nakon čuvanja, zahtev se automatski označava kao završen i klijent dobija obaveštenje.
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Odbijanje zahteva</h3>
                <form method="POST" action="{{ route('admin.zahtevi.odbij', $zahtev) }}" class="space-y-3">
                    @csrf
                    <label class="block text-sm font-medium text-red-700">Razlog odbijanja *</label>
                    <textarea name="napomena_admina" rows="3" required
                        class="w-full rounded-lg border-red-200 text-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="Unesite razlog odbijanja zahteva...">{{ old('napomena_admina') }}</textarea>
                    @error('napomena_admina')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg">
                        ❌ Odbij zahtev
                    </button>
                </form>
            </div>
        @endif

        <livewire:kreiraj-dokument-kretanja
            :team-id="$zahtev->team_id"
            :zahtev-id="$zahtev->id"
            :key="'doko-zahtev-'.$zahtev->id" />
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('download-file', ({ url }) => {
                    if (url) {
                        window.location.assign(url);
                    }
                });
            });
        </script>
    @endpush
</x-layouts.admin>
