@php $badge = $zahtev->status_badge; @endphp

<x-layouts.admin :title="$zahtev->broj_zahteva" :header="'DKO zahtev '.$zahtev->broj_zahteva">
    <div
        x-data="{ notifyMessage: null, notifyType: 'success' }"
        @notify.window="notifyMessage = $event.detail.message; notifyType = $event.detail.type || 'success'; setTimeout(() => notifyMessage = null, 4000)"
        @download-file.window="window.location = $event.detail.url"
    >
        <div x-show="notifyMessage" x-cloak x-transition
            class="fixed top-4 right-4 z-[60] max-w-sm rounded-xl shadow-lg px-4 py-3 text-sm font-medium text-white"
            :class="notifyType === 'error' ? 'bg-red-600' : 'bg-green-600'"
            x-text="notifyMessage"></div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('admin.dko-zahtevi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← DKO Zahtevi</a>
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $badge['class'] }}">{{ $badge['label'] }}</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            <div class="xl:col-span-3 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900 mb-4">{{ $zahtev->broj_zahteva }}</h2>
                    <dl class="space-y-3 text-sm">
                        <div><dt class="text-xs uppercase text-gray-500">Klijent</dt><dd class="font-medium mt-0.5">{{ $zahtev->team->name }} (PIB: {{ $zahtev->team->pib ?? '—' }})</dd></div>
                        <div><dt class="text-xs uppercase text-gray-500">Gradilište</dt><dd class="font-medium mt-0.5">{{ $zahtev->constructionSite?->naziv_gradilista }}</dd></div>
                        <div><dt class="text-xs uppercase text-gray-500">Dozvola</dt><dd class="font-semibold text-indigo-700 mt-0.5">{{ $zahtev->constructionSite?->broj_gradevinske_dozvole }}</dd></div>
                        <div><dt class="text-xs uppercase text-gray-500">Adresa</dt><dd class="mt-0.5">{{ $zahtev->constructionSite?->puna_adresa }}</dd></div>
                        <div><dt class="text-xs uppercase text-gray-500">Poslao</dt><dd class="mt-0.5">{{ $zahtev->kreirao?->name }} ({{ $zahtev->kreirao?->email }}) — {{ $zahtev->poslato_at?->format('d.m.Y. \u\ H:i') }}</dd></div>
                        @if ($zahtev->napomena_klijenta)
                            <div><dt class="text-xs uppercase text-gray-500">Napomena</dt><dd class="mt-0.5 italic">„{{ $zahtev->napomena_klijenta }}”</dd></div>
                        @endif
                    </dl>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">DEO1 zapisi u zahtevu</h3></div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Datum</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Vrsta otpada</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Količina</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Način nastanka</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($zahtev->evidencije as $ev)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $ev->datum?->format('d.m.Y.') }}</td>
                                        <td class="px-4 py-3"><span class="font-mono text-xs">{{ $ev->indeksni_broj }}</span> – {{ $ev->naziv_otpada }}</td>
                                        <td class="px-4 py-3 text-right font-medium">{{ number_format((float) $ev->proizvedena_kolicina, 3, ',', '.') }} t</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $ev->nacin_nastanka ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-right font-semibold">UKUPNO:</td>
                                    <td class="px-4 py-3 text-right font-bold">{{ number_format((float) $zahtev->masa_ukupno, 3, ',', '.') }} t</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-2 space-y-4">
                @if ($zahtev->status === 'zavrseno' && $zahtev->dokumentKretanja)
                    <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
                        <h3 class="font-semibold text-green-900 mb-2">DOKO kreiran</h3>
                        <p class="text-sm text-green-800 mb-3 font-mono">{{ $zahtev->dokumentKretanja->broj_izvestaja ?: $zahtev->dokumentKretanja->broj_dokumenta }}</p>
                        <a href="{{ route('doko.download', $zahtev->dokumentKretanja) }}" class="inline-flex px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">📥 DOKO</a>
                    </div>
                @elseif ($zahtev->status === 'odbijeno')
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                        <h3 class="font-semibold text-red-900 mb-2">Odbijeno</h3>
                        <p class="text-sm text-red-800">{{ $zahtev->razlog_odbijanja }}</p>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                        <h3 class="font-semibold text-gray-900">AKCIJE</h3>

                        @if ($zahtev->status === 'na_cekanju')
                            <form method="POST" action="{{ route('admin.dko-zahtevi.preuzmi', $zahtev) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bio-btn-primary">✅ Preuzmi u obradu</button>
                            </form>
                            <p class="text-xs text-gray-500">Klikni da označiš da radiš na ovom zahtevu.</p>
                        @endif

                        @if ($zahtev->status === 'u_obradi')
                            <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 text-sm text-blue-900">
                                🔄 U OBRADI<br>
                                <span class="text-xs">Preuzeto: {{ $zahtev->preuzeto_admin_at?->format('d.m.Y. \u\ H:i') }}</span>
                            </div>
                            <a href="{{ route('admin.dko-zahtevi.generate-form', $zahtev) }}" class="w-full bio-btn-primary inline-flex justify-center">📄 Generiši DKO dokument</a>
                            <p class="text-xs text-gray-500">Otvara DOKO wizard pre-popunjen podacima gradilišta i DEO1 zapisa.</p>
                            <button type="button"
                                onclick="Livewire.dispatch('openDokoFormFromGradjevinskiZahtev', { zahtevId: {{ $zahtev->id }} })"
                                class="w-full bio-btn-secondary text-sm">Otvori DOKO wizard ovde</button>
                        @endif

                        <div class="border-t border-gray-100 pt-4">
                            <form method="POST" action="{{ route('admin.dko-zahtevi.odbij', $zahtev) }}" class="space-y-3"
                                onsubmit="return confirm('Odbijiti zahtev? DEO1 zapisi će ponovo biti slobodni.')">
                                @csrf
                                @method('PATCH')
                                <label class="bio-label">❌ Odbij zahtev — razlog</label>
                                <textarea name="razlog_odbijanja" rows="3" required minlength="10" maxlength="500" class="bio-input"
                                    placeholder="Obavezno obrazložite odbijanje (min. 10 karaktera)"></textarea>
                                <button type="submit" class="w-full px-4 py-2 rounded-lg border border-red-200 text-red-700 text-sm font-semibold hover:bg-red-50">Odbij zahtev</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <livewire:kreiraj-dokument-kretanja />
    </div>
</x-layouts.admin>
