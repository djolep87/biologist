<div>
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900 mb-4">
            Generiši novi izveštaj
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Firma *</label>
                <select wire:model.live="selectedTeamId"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Izaberite firmu</option>
                    @foreach ($timovi as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
                @error('selectedTeamId')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Godina *</label>
                <select wire:model.live="selectedGodina"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @for ($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <button type="button" wire:click="ucitajPodatke" wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg disabled:opacity-50">
            <span wire:loading.remove wire:target="ucitajPodatke">🔍 Učitaj podatke</span>
            <span wire:loading wire:target="ucitajPodatke">Učitavam...</span>
        </button>
        @error('podaci')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

        @if ($podaciUcitani)
            <div class="mt-6 rounded-xl border border-indigo-100 bg-indigo-50/50 p-5">
                <p class="font-semibold text-indigo-900 mb-4">
                    ✅ Pronađeni podaci za {{ $pregled['team_name'] ?? '' }} — {{ $selectedGodina }}:
                </p>

                @if (! empty($pregled['vrste_otpada']))
                    <p class="text-sm font-medium text-gray-700 mb-2">Vrste otpada i DKO dokumenti:</p>
                    <div class="overflow-x-auto rounded-lg border border-indigo-100 bg-white mb-5">
                        <table class="min-w-full text-sm">
                            <thead class="bg-[#f8f9f4]">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Ind.br.</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Naziv otpada</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">DKO dok.</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Ukupno</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Provera</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($pregled['vrste_otpada'] as $vrsta)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-4 py-2.5 font-mono font-semibold">{{ $vrsta['indeksni_broj'] }}</td>
                                        <td class="px-4 py-2.5">{{ $vrsta['naziv_otpada'] }}</td>
                                        <td class="px-4 py-2.5">{{ $vrsta['br_doko'] }}</td>
                                        <td class="px-4 py-2.5 whitespace-nowrap">
                                            {{ \App\Models\DnevnaEvidencija::formatKolicina($vrsta['ukupno_predato']) }}
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @if ($vrsta['zbir_ok'])
                                                <span class="text-emerald-600" title="DOKO zbir = DEO1 predato">✅</span>
                                            @else
                                                <span class="text-amber-600"
                                                    title="DOKO: {{ $vrsta['ukupno_predato'] }} t, DEO1: {{ $vrsta['ukupno_predato_deo'] }} t">⚠</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p class="text-sm font-medium text-gray-700 mb-3">Proverite podatke za svaki DKO:</p>
                    <div class="space-y-2 mb-5">
                        @foreach ($pregled['vrste_otpada'] as $vrsta)
                            <div class="rounded-lg border border-indigo-100 bg-white overflow-hidden">
                                <button type="button" wire:click="toggleVrsta('{{ $vrsta['indeksni_broj'] }}')"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left text-sm font-medium text-gray-900 hover:bg-indigo-50/50">
                                    <span>
                                        {{ $otvorenaVrsta === $vrsta['indeksni_broj'] ? '▼' : '▶' }}
                                        <span class="font-mono">{{ $vrsta['indeksni_broj'] }}</span>
                                        — {{ $vrsta['naziv_otpada'] }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $vrsta['br_doko'] }} DKO</span>
                                </button>

                                @if ($otvorenaVrsta === $vrsta['indeksni_broj'])
                                    <div class="border-t border-indigo-50 px-4 py-3">
                                        @if (empty($vrsta['doko_dokumenti']))
                                            <p class="text-sm text-gray-500">Nema DKO dokumenata za ovu vrstu otpada.</p>
                                        @else
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-sm">
                                                    <thead>
                                                        <tr class="text-xs text-gray-500 uppercase">
                                                            <th class="py-2 pr-4 text-left">DKO br.</th>
                                                            <th class="py-2 pr-4 text-left">Datum</th>
                                                            <th class="py-2 pr-4 text-left">Operater</th>
                                                            <th class="py-2 text-right">Kol. (t)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-50">
                                                        @foreach ($vrsta['doko_dokumenti'] as $doko)
                                                            <tr>
                                                                <td class="py-2 pr-4 font-mono text-xs">{{ $doko['broj_dokumenta'] }}</td>
                                                                <td class="py-2 pr-4 whitespace-nowrap">{{ $doko['datum_predaje'] ?? '—' }}</td>
                                                                <td class="py-2 pr-4">{{ $doko['primalac_naziv'] ?? '—' }}</td>
                                                                <td class="py-2 text-right">
                                                                    {{ \App\Models\DnevnaEvidencija::formatKolicina($doko['masa_ukupno']) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr class="font-semibold bg-indigo-50/30">
                                                            <td colspan="3" class="py-2 pr-4 text-right text-gray-600">UKUPNO:</td>
                                                            <td class="py-2 text-right">
                                                                {{ \App\Models\DnevnaEvidencija::formatKolicina($vrsta['ukupno_predato']) }}
                                                                @if ($vrsta['zbir_ok'])
                                                                    <span class="text-emerald-600 ml-1">✅</span>
                                                                @else
                                                                    <span class="text-amber-600 ml-1">⚠</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <p class="text-xs text-indigo-700 mb-4">
                        Generiše se jedan GIO1 fajl sa jednim sheet-om.
                        Predaja tabela: R oznaka → kol. 5–6, 9–10 | D oznaka → kol. 5–6, 7–8 | Skladištenje → kol. 1–4 | Izvoz → kol. 11–14. Max 9 DKO (redovi 110–118).
                    </p>
                @else
                    <p class="text-sm text-amber-700 mb-4">Nema evidencija za ovu godinu — generisaće se prazan GIO1 sa podacima firme.</p>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <p class="sm:col-span-2 text-xs font-semibold uppercase text-gray-500">Odgovorno lice</p>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Ime i prezime *</label>
                        <input type="text" wire:model="odgovornoLiceIme" class="w-full rounded-lg border-gray-300 text-sm">
                        @error('odgovornoLiceIme')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Funkcija</label>
                        <input type="text" wire:model="odgovornoLiceFunkcija" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Telefon</label>
                        <input type="text" wire:model="odgovornoLiceTelefon" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>

                    <p class="sm:col-span-2 text-xs font-semibold uppercase text-gray-500 pt-2">Lice za upravljanje otpadom</p>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Ime i prezime *</label>
                        <input type="text" wire:model="liceOtpadIme" class="w-full rounded-lg border-gray-300 text-sm">
                        @error('liceOtpadIme')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Funkcija</label>
                        <input type="text" wire:model="liceOtpadFunkcija" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Telefon</label>
                        <input type="text" wire:model="liceOtpadTelefon" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Email</label>
                        <input type="email" wire:model="liceOtpadEmail" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                </div>

                <button type="button" wire:click="generisi" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg disabled:opacity-50">
                    <span wire:loading.remove wire:target="generisi">💾 Sačuvaj i generiši GIO1.xlsx</span>
                    <span wire:loading wire:target="generisi">Čuvam...</span>
                </button>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900">Generisani izveštaji</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#f8f9f4]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Firma</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">God.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vrste</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">DKO</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Generisan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($izvestaji as $izv)
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-3 font-medium">{{ $izv->team->name }}</td>
                            <td class="px-4 py-3">{{ $izv->godina }}</td>
                            <td class="px-4 py-3">{{ $izv->br_vrsta_otpada }}</td>
                            <td class="px-4 py-3">{{ $izv->br_doko }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                {{ ($izv->generisan_at ?? $izv->created_at)->format('d.m.Y.') }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('gio1.download', $izv) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg">
                                        ⬇ GIO1
                                    </a>
                                    @if ($confirmingDeleteId === $izv->id)
                                        <div class="inline-flex items-center gap-2 text-xs">
                                            <span class="text-gray-600">Obrisati?</span>
                                            <button type="button" wire:click="obrisi({{ $izv->id }})"
                                                class="text-red-600 font-semibold hover:text-red-800">Da</button>
                                            <button type="button" wire:click="cancelDelete"
                                                class="text-gray-500 hover:text-gray-700">Ne</button>
                                        </div>
                                    @else
                                        <button type="button" wire:click="confirmDelete({{ $izv->id }})"
                                            class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" title="Obriši">🗑</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">Još nema generisanih GIO1 izveštaja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($izvestaji->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $izvestaji->links() }}</div>
        @endif
    </div>
</div>
