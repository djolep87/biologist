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

    @if (! $selectedTeam)
        <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-8 text-center">
            <p class="text-lg font-semibold text-gray-900 mb-2">Izaberite firmu</p>
            <p class="text-sm text-gray-500">U levom meniju izaberite klijenta (firmu) da biste videli evidencije i kreirali DOKO / DEO1 dokumente.</p>
        </div>
    @else
        <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">Aktivni klijent</p>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900">{{ $selectedTeam->name }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        PIB: {{ $selectedTeam->pib ?? '—' }} · {{ $selectedTeam->tip_subjekta ?? 'DEO1' }}
                    </p>
                </div>
                <button type="button" wire:click="$dispatch('openDokoForm')"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shrink-0">
                    🚛 Kreiraj DOKO dokument
                </button>
            </div>
        </div>

        <div class="flex border-b border-gray-200 mb-6">
            <button type="button" wire:click="setTab('evidencije')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'evidencije' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                ⏳ Za predaju
                @if ($cekaPredajuCount > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs bg-amber-100 text-amber-800 rounded-full">{{ $cekaPredajuCount }}</span>
                @endif
            </button>
            <button type="button" wire:click="setTab('dnevna')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'dnevna' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                📝 Dnevna evidencija
            </button>
            <button type="button" wire:click="setTab('dokumenti')"
                class="px-6 py-3 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'dokumenti' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                🚛 DOKO dokumenti
            </button>
        </div>

        @if ($activeTab === 'evidencije')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Godina</label>
                        <select wire:model.live="filterGodina" class="rounded-lg border-gray-300 text-sm">
                            @for ($y = now()->year; $y >= now()->year - 3; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Mesec</label>
                        <select wire:model.live="filterMesec" class="rounded-lg border-gray-300 text-sm">
                            @foreach (['Januar','Februar','Mart','April','Maj','Jun','Jul','Avgust','Septembar','Oktobar','Novembar','Decembar'] as $i => $m)
                                <option value="{{ $i + 1 }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Indeks</label>
                        <select wire:model.live="filterIndeks" class="rounded-lg border-gray-300 text-sm min-w-[140px]">
                            <option value="">Svi</option>
                            @foreach ($indeksOptions as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @php
                    $evidencije = \App\Models\DnevnaEvidencija::forTeam($selectedTeam->id)
                        ->where('predat_operateru', false)
                        ->where('stanje_na_skladistu', '>', 0)
                        ->when($filterGodina, fn ($q) => $q->where('godina', $filterGodina))
                        ->when($filterMesec, fn ($q) => $q->where('mesec', $filterMesec))
                        ->when($filterIndeks !== '', fn ($q) => $q->where('indeksni_broj', $filterIndeks))
                        ->orderByDesc('datum')
                        ->limit(50)
                        ->get();
                @endphp

                @if ($evidencije->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500 text-sm">Nema evidencija koje čekaju predaju operateru.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-[#f8f9f4]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Datum</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Indeks</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Otpad</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Stanje</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">DEO1 Excel</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($evidencije as $ev)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $ev->datum->format('d.m.Y.') }}</td>
                                        <td class="px-4 py-3 font-mono text-xs font-semibold">{{ $ev->indeksni_broj }}</td>
                                        <td class="px-4 py-3 max-w-[180px] truncate">{{ $ev->naziv_otpada }}</td>
                                        <td class="px-4 py-3 font-medium text-amber-700">{{ \App\Models\DnevnaEvidencija::formatKolicina($ev->stanje_na_skladistu) }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.export.deo1', ['godina' => $ev->godina, 'mesec' => $ev->mesec, 'indeksni_broj' => $ev->indeksni_broj]) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                                                📋 DEO1
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3 text-sm text-indigo-900">
                Izaberite evidencije u wizardu „Kreiraj DOKO dokument” i popunite obrazac. DEO1 Excel možete preuzeti pojedinačno iz tabele iznad.
            </div>
        @elseif ($activeTab === 'dnevna')
            <livewire:admin.admin-evidencije-pregled :key="'ev-pregled-'.$selectedTeam->id" />
        @else
            <livewire:dokument-kretanja-table :team-id="$selectedTeam->id" :allow-delete="true" :key="'dok-'.$selectedTeam->id" />
        @endif

        <livewire:kreiraj-dokument-kretanja :team-id="$selectedTeam->id" :key="'doko-'.$selectedTeam->id" />
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('download-file', ({ url }) => {
                if (url) {
                    window.location.assign(url);
                }
            });
        });
    </script>
</div>
