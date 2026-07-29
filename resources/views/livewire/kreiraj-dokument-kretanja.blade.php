<div x-data x-effect="document.body.classList.toggle('overflow-hidden', $wire.showModal)">
    @if ($showModal)
        <div class="fixed inset-0 z-[100] flex justify-end" role="dialog" aria-modal="true" @keydown.escape.window="$wire.closeModal()">
            <button type="button" class="absolute inset-0 cursor-default" style="background-color: rgba(17, 24, 39, 0.55);" wire:click="closeModal" aria-label="Zatvori"></button>

            <div class="bio-drawer-panel relative z-10 ml-auto flex h-svh w-full max-w-3xl flex-col bg-white shadow-2xl" wire:click.stop>
                <div class="flex shrink-0 items-center justify-between px-6 py-4" style="background-color: #1e2430;">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">DOKO</p>
                        <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-semibold text-white">
                            Predaja otpada operateru — korak {{ $currentStep }} / 6
                        </h2>
                    </div>
                    <button type="button" wire:click="closeModal" class="rounded-lg p-2 text-gray-400 hover:bg-white/10 hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="shrink-0 px-6 py-3 bg-gray-50 border-b border-gray-200 flex gap-1 overflow-x-auto text-xs">
                    @foreach ([1 => 'Izbor izveštaja', 2 => 'DEO A', 3 => 'DEO B', 4 => 'DEO C', 5 => 'DEO D', 6 => 'Pregled'] as $num => $label)
                        <span class="px-2 py-1 rounded-full whitespace-nowrap {{ $currentStep === $num ? 'bg-blue-600 text-white' : ($currentStep > $num ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600') }}">
                            {{ $num }}. {{ $label }}
                        </span>
                    @endforeach
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5">
                    {{-- Broj izveštaja (automatski dodeljen) --}}
                    <div class="mb-5 rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-4">
                        <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                            @if ($formatBroja === 'lokacija')
                                <div class="sm:w-52">
                                    <label class="block text-xs font-semibold uppercase tracking-wide text-indigo-700 mb-1">Lokacija / pogon</label>
                                    <select wire:model.live="brojIzvestajaLokacija"
                                        class="w-full rounded-lg border-indigo-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @forelse ($lokacijeOpcije as $lok)
                                            <option value="{{ $lok['oznaka'] }}">{{ $lok['oznaka'] }}{{ $lok['naziv'] ? ' — '.$lok['naziv'] : '' }}</option>
                                        @empty
                                            <option value="">Nema definisanih lokacija</option>
                                        @endforelse
                                    </select>
                                </div>
                            @endif
                            <div class="flex-1">
                                <label class="block text-xs font-semibold uppercase tracking-wide text-indigo-700 mb-1">Broj izveštaja</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" readonly value="{{ $brojIzvestajaPreview }}" placeholder="—"
                                        class="flex-1 rounded-lg border-indigo-200 bg-white font-mono text-base font-semibold text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                                    <button type="button" wire:click="refreshBrojPreview" title="Osveži broj" wire:loading.attr="disabled" wire:target="refreshBrojPreview"
                                        class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg border border-indigo-200 bg-white text-indigo-600 hover:bg-indigo-100 transition-colors disabled:opacity-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            class="w-5 h-5" wire:loading.class="animate-spin" wire:target="refreshBrojPreview">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-indigo-400 shrink-0">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                            </svg>
                            Broj se zvanično dodeljuje u trenutku čuvanja dokumenta.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- STEP 1 --}}
                    @if ($currentStep === 1)
                        <h3 class="font-semibold text-gray-900 mb-4">Izaberite dnevne izveštaje za predaju operateru</h3>

                        <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                            <p class="text-sm font-semibold text-gray-800">Tip evidencije</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" wire:model.live="tipEvidencije" value="obicna" class="text-green-600 focus:ring-green-500">
                                    Obična firma (postojeća logika)
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="radio" wire:model.live="tipEvidencije" value="gradjevinska" class="text-green-600 focus:ring-green-500">
                                    Građevinsko gradilište
                                </label>
                            </div>

                            @if ($tipEvidencije === 'gradjevinska')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gradilište</label>
                                    <select wire:model.live="constructionSiteId" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="">Izaberite gradilište…</option>
                                        @foreach ($gradilistaOpcije as $g)
                                            <option value="{{ $g['id'] }}">
                                                {{ $g['naziv_gradilista'] }} – {{ $g['broj_gradevinske_dozvole'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($brojGradevinskeDozvole)
                                        <p class="mt-2 text-xs text-indigo-700 font-medium">
                                            Broj građevinske dozvole: {{ $brojGradevinskeDozvole }} — automatski na DKO obrascu
                                        </p>
                                    @endif
                                    @if ($gradilistaOpcije === [])
                                        <p class="mt-2 text-xs text-amber-700">Firma nema unetih gradilišta.</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter po indeksnom broju</label>
                            <select wire:model.live="filterIndeks" class="w-full rounded-lg border-gray-300 text-sm" @disabled($lockedIndeksniBroj !== null)>
                                <option value="">Svi indeksi</option>
                                @foreach ($indeksOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($availableEvidencije->isEmpty())
                            <p class="text-gray-500 text-sm">Nema izveštaja koji čekaju predaju.</p>
                        @else
                            <div class="space-y-2 mb-4">
                                @foreach ($availableEvidencije as $ev)
                                    <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors
                                        {{ in_array($ev->id, $izabraniIds) ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}">
                                        <input type="checkbox" class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                            wire:click="toggleEvidencija({{ $ev->id }})"
                                            @checked(in_array($ev->id, $izabraniIds)) />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-sm font-bold">{{ $ev->indeksni_broj }}</span>
                                                <span class="text-sm text-gray-800">{{ $ev->naziv_otpada }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Datum: {{ $ev->datum->format('d.m.Y.') }} |
                                                Stanje: {{ \App\Models\DnevnaEvidencija::formatKolicina($ev->stanje_na_skladistu) }} |
                                                <span class="text-amber-700">Čeka predaju</span>
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900 mb-4">
                            ⚠ Možete birati izveštaje samo jednog indeksnog broja po dokumentu.
                        </div>

                        <p class="text-sm text-gray-700">
                            Izabrano: <strong>{{ count($izabraniIds) }}</strong> izveštaja |
                            Ukupno: <strong>{{ number_format($selectedMasa, 3, ',', '.') }} t</strong>
                        </p>
                    @endif

                    {{-- STEP 2 --}}
                    @if ($currentStep === 2)
                        <h3 class="font-semibold text-gray-900 mb-4">DEO A — Podaci o otpadu</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><label class="text-sm text-gray-600">Indeksni broj</label><input type="text" readonly wire:model="indeksni_broj" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Vrsta otpada</label><input type="text" readonly wire:model="vrsta_otpada" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Masa otpada (t)</label><input type="text" readonly value="{{ number_format($selectedMasa, 3, ',', '.') }}" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Oznaka Q liste</label><input type="text" wire:model="q_lista" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Način pakovanja</label><input type="text" wire:model="nacin_pakovanja" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div>
                                <label class="text-sm text-gray-600">Fizičko stanje</label>
                                <select wire:model="fizicko_stanje" class="mt-1 w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">—</option>
                                    <option value="cvrsta-prah">Čvrsta-prah</option>
                                    <option value="cvrsta-komadi">Čvrsta-komadi</option>
                                    <option value="viskozna-pasta">Viskozna pasta</option>
                                    <option value="tecna">Tečna</option>
                                    <option value="talog">Talog</option>
                                </select>
                            </div>
                            <div><label class="text-sm text-gray-600">Izveštaj — broj</label><input type="text" wire:model="izvestaj_broj" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Izveštaj — datum</label><input type="date" wire:model="izvestaj_datum" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Odredište</label><input type="text" wire:model="odrediste" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div>
                                <label class="text-sm text-gray-600">Vid prevoza</label>
                                <select wire:model="vid_prevoza" class="mt-1 w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">—</option>
                                    <option value="Drumski">Drumski</option>
                                    <option value="Železnički">Železnički</option>
                                    <option value="Brodski">Brodski</option>
                                    <option value="Vazdušni">Vazdušni</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2"><label class="text-sm text-gray-600">Posebne napomene</label><textarea wire:model="posebne_napomene" rows="3" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></textarea></div>
                        </div>
                    @endif

                    {{-- STEP 3 --}}
                    @if ($currentStep === 3)
                        <h3 class="font-semibold text-gray-900 mb-2">DEO B — Proizvođač</h3>
                        <p class="text-sm text-green-800 bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-4">
                            Podaci se automatski preuzimaju iz profila firme klijenta. Ako nešto nedostaje ili je pogrešno, možete dopuniti/izmeniti direktno ovde — izmene se čuvaju samo na ovom dokumentu.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><label class="text-sm text-gray-600">PIB</label><input type="text" wire:model="proizvodjac_pib" maxlength="9" inputmode="numeric" class="mt-1 w-full rounded-lg border-gray-300 text-sm font-mono" /></div>
                            <div><label class="text-sm text-gray-600">Matični broj</label><input type="text" wire:model="proizvodjac_maticni" maxlength="8" inputmode="numeric" class="mt-1 w-full rounded-lg border-gray-300 text-sm font-mono" /></div>
                            <div class="sm:col-span-2"><label class="text-sm text-gray-600">Naziv *</label><input type="text" wire:model="proizvodjac_naziv" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Opština</label><input type="text" wire:model="proizvodjac_opstina" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Mesto</label><input type="text" wire:model="proizvodjac_mesto" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Poštanski broj</label><input type="text" wire:model="proizvodjac_postanski" maxlength="5" inputmode="numeric" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Ulica</label><input type="text" wire:model="proizvodjac_ulica" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Telefon</label><input type="text" wire:model="proizvodjac_telefon" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Faks</label><input type="text" wire:model="proizvodjac_faks" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Email</label><input type="email" wire:model="proizvodjac_email" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div class="sm:col-span-2">
                                <label class="text-sm text-gray-600 block mb-2">Vlasnik</label>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <label><input type="radio" wire:model="vlasnik_tip" value="proizvodjac" class="mr-1" /> Proizvođač</label>
                                    <label><input type="radio" wire:model="vlasnik_tip" value="vlasnik" class="mr-1" /> Vlasnik</label>
                                    <label><input type="radio" wire:model="vlasnik_tip" value="operater" class="mr-1" /> Operater</label>
                                </div>
                            </div>
                            <div><label class="text-sm text-gray-600">R oznaka</label><select wire:model="r_oznaka" class="mt-1 w-full rounded-lg border-gray-300 text-sm"><option value="">—</option>@foreach($rOznake as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select></div>
                            <div><label class="text-sm text-gray-600">D oznaka</label><select wire:model="d_oznaka" class="mt-1 w-full rounded-lg border-gray-300 text-sm"><option value="">—</option>@foreach($dOznake as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select></div>
                            <div><label class="text-sm text-gray-600">Dozvola — broj</label><input type="text" wire:model="dozvola_broj" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Dozvola — datum izdavanja</label><input type="date" wire:model="dozvola_datum" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Datum predaje otpada *</label><input type="date" wire:model="datum_predaje" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Odgovorno lice</label><input type="text" wire:model="odgovorno_lice_b" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Telefon lica</label><input type="text" wire:model="telefon_lica_b" class="mt-1 w-full rounded-lg border-gray-300 text-sm" /></div>
                        </div>
                    @endif

                    {{-- STEP 4 --}}
                    @if ($currentStep === 4)
                        <h3 class="font-semibold text-gray-900 mb-4">DEO C — Prevoznik</h3>

                        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl space-y-3">
                            <label class="block text-sm font-semibold text-indigo-900">
                                🔍 Brzo popunjavanje — izaberite operatera iz baze
                            </label>
                            <p class="text-xs text-indigo-700/80">Operateri obično preuzimaju otpad — unesite PIB ili naziv da se podaci automatski popune.</p>
                            <div class="flex gap-3">
                                <input type="text" wire:model.live.debounce.300ms="prevoznikOperaterSearch"
                                    placeholder="Unesite PIB ili naziv operatera..."
                                    class="flex-1 rounded-lg border-indigo-300 text-sm focus:ring-indigo-500" />
                                <button type="button" wire:click="clearPrevoznikOperater"
                                    class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                                    Resetuj
                                </button>
                            </div>

                            @if (! empty($prevoznikOperaterSearch) && $prevoznikOperaterSearch !== $selectedPrevoznikOperaterName)
                                <div class="border border-indigo-200 rounded-lg bg-white shadow-lg max-h-48 overflow-y-auto">
                                    @forelse ($prevoznikOperaterRezultati as $op)
                                        <button type="button" wire:click="izaberiPrevoznikOperatera({{ $op['id'] }})"
                                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 border-b last:border-0">
                                            <div class="font-medium text-sm">{{ $op['kratki_naziv'] ?? $op['naziv'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $op['naziv'] }} · PIB: {{ $op['pib'] }}</div>
                                            @if (! empty($op['dozvola_vazi_do']) && \Carbon\Carbon::parse($op['dozvola_vazi_do'])->isPast())
                                                <span class="text-xs text-red-600">⚠ Dozvola istekla</span>
                                            @endif
                                        </button>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-gray-500">Nema rezultata</div>
                                    @endforelse
                                </div>
                            @endif

                            @if ($selectedPrevoznikOperaterName)
                                <div class="flex items-center gap-2 text-sm text-indigo-700">
                                    ✅ Izabrano: <strong>{{ $selectedPrevoznikOperaterName }}</strong>
                                    — podaci prevoznika su popunjeni
                                </div>
                            @endif

                            @if ($selectedOperaterName || $primalac_naziv)
                                <button type="button" wire:click="kopirajPrevoznikaOdPrimaoca"
                                    class="text-sm font-medium text-indigo-700 hover:text-indigo-900 underline">
                                    ↳ Isti operater kao primalac ({{ $selectedOperaterName ?: $primalac_naziv }})
                                </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-600">PIB</label>
                                <input type="text" wire:model.live.debounce.400ms="prevoznik_pib" maxlength="9" inputmode="numeric"
                                    placeholder="Unesite PIB operatera"
                                    class="mt-1 w-full rounded-lg border-gray-300 text-sm font-mono" />
                                <p class="mt-1 text-xs text-gray-500">Automatsko popunjavanje nakon unosa 9 cifara PIB-a</p>
                            </div>
                            @foreach ([['prevoznik_maticni','Matični'],['prevoznik_naziv','Naziv'],['prevoznik_opstina','Opština'],['prevoznik_mesto','Mesto'],['prevoznik_postanski','Poštanski'],['prevoznik_ulica','Ulica'],['prevoznik_telefon','Telefon'],['prevoznik_faks','Faks'],['prevoznik_email','Email'],['prevoznik_dozvola_broj','Dozvola broj'],['prevoznik_odgovorno_lice_prijem','Odg. lice prijem'],['prevoznik_telefon_lica_prijem','Tel. lica prijem'],['prevoznik_odgovorno_lice_predaja','Odg. lice predaja'],['prevoznik_telefon_lica_predaja','Tel. lica predaja']] as [$field, $label])
                                <div>
                                    <label class="text-sm text-gray-600">{{ $label }}</label>
                                    <input type="text" readonly wire:model="{{ $field }}" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" />
                                </div>
                            @endforeach
                            @foreach ([['vrsta_prevoznog_sredstva','Vrsta sredstva'],['registarski_broj','Reg. broj'],['ruta_via_1','Ruta via 1'],['ruta_via_2','Ruta via 2'],['ruta_via_3','Ruta via 3']] as [$field, $label])
                                <div>
                                    <label class="text-sm text-gray-600">{{ $label }}</label>
                                    <input type="text" wire:model="{{ $field }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm" />
                                </div>
                            @endforeach
                            <div>
                                <label class="text-sm text-gray-600">Lokacija utovara</label>
                                <input type="text" readonly wire:model="lokacija_utovara" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" />
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Lokacija istovara</label>
                                <input type="text" readonly wire:model="lokacija_istovara" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" />
                            </div>
                            <div><label class="text-sm text-gray-600">Dozvola datum izdavanja</label><input type="date" readonly wire:model="prevoznik_dozvola_datum" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Datum prijema</label><input type="date" readonly wire:model="prevoznik_datum_prijema" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Datum predaje</label><input type="date" readonly wire:model="prevoznik_datum_predaje" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                        </div>
                    @endif

                    {{-- STEP 5 --}}
                    @if ($currentStep === 5)
                        <h3 class="font-semibold text-gray-900 mb-4">DEO D — Primalac</h3>

                        @if ($tipEvidencije === 'gradjevinska')
                            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                Unesite naziv deponije ili drobilice gde se šut istovara. Prevoznik je automatski popunjen iz podataka gradilišta (ako postoji).
                            </div>
                        @endif

                        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl">
                            <label class="block text-sm font-semibold text-indigo-900 mb-2">
                                🔍 Brzo popunjavanje — izaberite operatera iz baze
                            </label>
                            <div class="flex gap-3">
                                <input type="text" wire:model.live.debounce.300ms="operaterSearch"
                                    placeholder="{{ $tipEvidencije === 'gradjevinska' ? 'Unesite naziv deponije ili drobilice...' : 'Unesite naziv ili PIB operatera...' }}"
                                    class="flex-1 rounded-lg border-indigo-300 text-sm focus:ring-indigo-500" />
                                <button type="button" wire:click="clearOperater"
                                    class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                                    Resetuj
                                </button>
                            </div>

                            @if (! empty($operaterSearch) && $operaterSearch !== $selectedOperaterName)
                                <div class="mt-2 border border-indigo-200 rounded-lg bg-white shadow-lg max-h-48 overflow-y-auto">
                                    @forelse ($operaterRezultati as $op)
                                        <button type="button" wire:click="izaberiOperatera({{ $op['id'] }})"
                                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 border-b last:border-0">
                                            <div class="font-medium text-sm">{{ $op['kratki_naziv'] ?? $op['naziv'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $op['naziv'] }} · PIB: {{ $op['pib'] }}</div>
                                            @if (! empty($op['dozvola_vazi_do']) && \Carbon\Carbon::parse($op['dozvola_vazi_do'])->isPast())
                                                <span class="text-xs text-red-600">⚠ Dozvola istekla</span>
                                            @endif
                                        </button>
                                    @empty
                                        <div class="px-4 py-3 text-sm text-gray-500">Nema rezultata</div>
                                    @endforelse
                                </div>
                            @endif

                            @if ($selectedOperaterName)
                                <div class="mt-2 flex items-center gap-2 text-sm text-indigo-700">
                                    ✅ Izabrano: <strong>{{ $selectedOperaterName }}</strong>
                                    — sva polja su popunjena automatski
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm text-gray-600">PIB</label>
                                <input type="text" wire:model.live.debounce.400ms="primalac_pib" maxlength="9" inputmode="numeric"
                                    placeholder="Unesite PIB operatera"
                                    class="mt-1 w-full rounded-lg border-gray-300 text-sm font-mono" />
                                <p class="mt-1 text-xs text-gray-500">Automatsko popunjavanje iz registra operatera</p>
                            </div>
                            @foreach ([['primalac_maticni','Matični'],['primalac_naziv','Naziv *'],['primalac_opstina','Opština'],['primalac_mesto','Mesto'],['primalac_postanski','Poštanski'],['primalac_ulica','Ulica'],['primalac_telefon','Telefon'],['primalac_faks','Faks'],['primalac_email','Email'],['primalac_dozvola_broj','Dozvola broj'],['primalac_odgovorno_lice','Odgovorno lice'],['primalac_telefon_lica','Telefon lica']] as [$field, $label])
                                <div>
                                    <label class="text-sm text-gray-600">{{ $label }}</label>
                                    <input type="text" readonly wire:model="{{ $field }}" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" />
                                </div>
                            @endforeach
                            <div>
                                <label class="text-sm text-gray-600">Tip primaoca</label>
                                <select wire:model="primalac_tip" class="mt-1 w-full rounded-lg border-gray-300 text-sm">
                                    <option value="">—</option>
                                    <option value="skladiste">Skladište</option>
                                    <option value="tretman">Tretman</option>
                                    <option value="odlaganje">Odlaganje</option>
                                </select>
                            </div>
                            <div><label class="text-sm text-gray-600">Dozvola datum izdavanja</label><input type="date" readonly wire:model="primalac_dozvola_datum" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                            <div><label class="text-sm text-gray-600">Datum prijema</label><input type="date" readonly wire:model="primalac_datum_prijema" class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 text-sm" /></div>
                        </div>
                    @endif

                    {{-- STEP 6 --}}
                    @if ($currentStep === 6)
                        <h3 class="font-semibold text-gray-900 mb-4">Pregled pre čuvanja</h3>
                        <div class="space-y-4 text-sm">
                            <div class="rounded-lg border border-gray-200 p-4">
                                <p class="font-semibold text-gray-900 mb-2">Izabrani izveštaji ({{ count($izabraniIds) }})</p>
                                @foreach ($selectedEvidencije as $ev)
                                    <p class="text-gray-600">{{ $ev->datum->format('d.m.Y.') }} — {{ $ev->indeksni_broj }} — {{ \App\Models\DnevnaEvidencija::formatKolicina($ev->stanje_na_skladistu) }}</p>
                                @endforeach
                                <p class="mt-2 font-medium">Ukupna masa: {{ number_format($selectedMasa, 3, ',', '.') }} t</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 p-4 grid grid-cols-2 gap-2">
                                <p><span class="text-gray-500">Indeks:</span> {{ $indeksni_broj }}</p>
                                <p><span class="text-gray-500">Datum predaje:</span> {{ $datum_predaje ? \Carbon\Carbon::parse($datum_predaje)->format('d.m.Y.') : '—' }}</p>
                                <p class="col-span-2"><span class="text-gray-500">Proizvođač:</span> {{ $proizvodjac_naziv }}</p>
                                <p class="col-span-2"><span class="text-gray-500">Prevoznik:</span> {{ $prevoznik_naziv ?: '—' }}</p>
                                <p class="col-span-2"><span class="text-gray-500">Primalac:</span> {{ $primalac_naziv ?: '—' }}</p>
                                <p><span class="text-gray-500">R oznaka:</span> {{ $r_oznaka ?: '—' }}</p>
                                <p><span class="text-gray-500">D oznaka:</span> {{ $d_oznaka ?: '—' }}</p>
                            </div>
                            <p class="text-gray-600">Nakon čuvanja automatski se preuzima popunjen DOKO.xlsx fajl. DEO1 možete preuzeti kasnije sa taba Dokumenti kretanja.</p>
                        </div>
                    @endif
                </div>

                <div class="shrink-0 flex items-center justify-between gap-3 border-t border-gray-200 px-6 py-4 bg-white">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="prevStep" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">← Nazad</button>
                    @else
                        <span></span>
                    @endif

                    @if ($currentStep < 6)
                        <button type="button" wire:click="nextStep" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
                            Dalje →
                        </button>
                    @else
                        <button type="button" wire:click="save" wire:loading.attr="disabled" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                            💾 Sačuvaj i generiši dokument
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
