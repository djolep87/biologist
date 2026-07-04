<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-4 border-b border-gray-100">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Sve evidencije</h2>
            <p class="text-sm text-gray-500">Read-only pregled evidencija svih klijenata.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700">
                {{ number_format($totals['broj_unosa'], 0, ',', '.') }} <span class="text-gray-400 font-normal">rezultata</span>
            </span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="px-6 py-4 bg-[#f8f9f4] border-b border-gray-100">
        <div class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative min-w-[180px]">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Pretraži firmu, otpad, indeks, primaoca..."
                    class="w-full pl-9 rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
            </div>

            <select wire:model.live="filterFirma" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Sve firme</option>
                @foreach ($firmeOptions as $id => $naziv)
                    <option value="{{ $id }}">{{ $naziv }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterGodina" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Sve godine</option>
                @foreach ($godinaOptions as $g)
                    <option value="{{ $g }}">{{ $g }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterMesec" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Svi meseci</option>
                @foreach ([1=>'Januar',2=>'Februar',3=>'Mart',4=>'April',5=>'Maj',6=>'Jun',7=>'Jul',8=>'Avgust',9=>'Septembar',10=>'Oktobar',11=>'Novembar',12=>'Decembar'] as $m => $label)
                    <option value="{{ $m }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterIndeks" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500 max-w-[160px]">
                <option value="">Svi indeksi</option>
                @foreach ($indeksOptions as $indeks)
                    <option value="{{ $indeks }}">{{ $indeks }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterKarakter" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Svi karakteri</option>
                <option value="inertan">Inertan</option>
                <option value="neopasan">Neopasan</option>
                <option value="opasan">Opasan</option>
            </select>

            <select wire:model.live="filterStatus" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Svi statusi</option>
                <option value="ceka">⏳ Čeka predaju</option>
                <option value="predato">✅ Predato</option>
            </select>

            @if ($this->hasActiveFilters())
                <button type="button" wire:click="resetFilters"
                    class="shrink-0 inline-flex items-center gap-1 text-sm text-gray-600 hover:text-green-600 font-medium px-3 py-2 rounded-lg hover:bg-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Reset
                </button>
            @endif
        </div>
    </div>

    {{-- Summary --}}
    @if ($totals['broj_unosa'] > 0)
        <div class="px-6 py-3 bg-green-50/50 border-b border-green-100 text-sm text-gray-700 flex flex-wrap gap-x-4 gap-y-1">
            <span><span class="font-medium">Ukupno unosa:</span> <strong>{{ number_format($totals['broj_unosa'], 0, ',', '.') }}</strong></span>
            <span class="text-gray-300">|</span>
            <span>Proizvedeno: <strong>{{ \App\Models\DnevnaEvidencija::formatKolicina($totals['proizvedena']) }}</strong></span>
            <span class="text-gray-300">|</span>
            <span>Predato: <strong>{{ \App\Models\DnevnaEvidencija::formatKolicina($totals['predata']) }}</strong></span>
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
        @if ($evidencije->isEmpty())
            <div class="text-center py-16 px-6">
                <span class="text-5xl mb-4 block">🔍</span>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Nema rezultata</h3>
                <p class="text-gray-500 text-sm mb-6">
                    @if ($this->hasActiveFilters())
                        Nijedna evidencija ne odgovara zadatim filterima.
                    @else
                        Još nema unetih evidencija.
                    @endif
                </p>
                @if ($this->hasActiveFilters())
                    <button type="button" wire:click="resetFilters" class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">
                        Poništi filtere
                    </button>
                @endif
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-[#f8f9f4]">
                    <tr>
                        @php
                            $th = 'px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider select-none';
                            $sortIcon = fn ($col) => $sortBy === $col ? ($sortDir === 'asc' ? '▲' : '▼') : '';
                        @endphp
                        <th class="{{ $th }} cursor-pointer hover:text-gray-700" wire:click="sort('datum')">Datum <span class="text-green-600">{{ $sortIcon('datum') }}</span></th>
                        <th class="{{ $th }}">Firma</th>
                        <th class="{{ $th }} cursor-pointer hover:text-gray-700" wire:click="sort('indeksni_broj')">Indeks <span class="text-green-600">{{ $sortIcon('indeksni_broj') }}</span></th>
                        <th class="{{ $th }} cursor-pointer hover:text-gray-700" wire:click="sort('naziv_otpada')">Otpad <span class="text-green-600">{{ $sortIcon('naziv_otpada') }}</span></th>
                        <th class="{{ $th }}">Karakter</th>
                        <th class="{{ $th }} text-right cursor-pointer hover:text-gray-700" wire:click="sort('proizvedena_kolicina')">Proizv. <span class="text-green-600">{{ $sortIcon('proizvedena_kolicina') }}</span></th>
                        <th class="{{ $th }} text-right cursor-pointer hover:text-gray-700" wire:click="sort('predata_kolicina')">Predato <span class="text-green-600">{{ $sortIcon('predata_kolicina') }}</span></th>
                        <th class="{{ $th }} text-right cursor-pointer hover:text-gray-700" wire:click="sort('stanje_na_skladistu')">Stanje <span class="text-green-600">{{ $sortIcon('stanje_na_skladistu') }}</span></th>
                        <th class="{{ $th }}">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($evidencije as $ev)
                        <tr class="hover:bg-gray-50/80" wire:key="ev-{{ $ev->id }}">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $ev->datum->format('d.m.Y.') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 max-w-[160px] truncate">{{ $ev->team?->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 whitespace-nowrap">{{ $ev->indeksni_broj }}</td>
                            <td class="px-4 py-3 max-w-[200px] truncate">{{ $ev->naziv_otpada }}</td>
                            <td class="px-4 py-3">
                                @php $kc = ['inertan'=>'bg-blue-100 text-blue-800','neopasan'=>'bg-green-100 text-green-800','opasan'=>'bg-red-100 text-red-800'][$ev->karakter_otpada] ?? 'bg-gray-100 text-gray-700'; @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium capitalize {{ $kc }}">{{ $ev->karakter_otpada }}</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap text-gray-700">{{ \App\Models\DnevnaEvidencija::formatKolicina($ev->proizvedena_kolicina) }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap text-gray-700">{{ \App\Models\DnevnaEvidencija::formatKolicina($ev->predata_kolicina) }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap font-medium text-gray-900">{{ \App\Models\DnevnaEvidencija::formatKolicina($ev->stanje_na_skladistu) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($ev->predat_operateru)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Predato</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">⏳ Čeka</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Footer / pagination --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-6 py-4 border-t border-gray-100">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span>Prikaži</span>
            <select wire:model.live="perPage" class="rounded-lg border-gray-300 text-sm py-1 focus:border-green-500 focus:ring-green-500">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>po strani</span>
        </div>
        <div>
            {{ $evidencije->links() }}
        </div>
    </div>
</div>
