<div
    x-data="{ notifyMessage: null, notifyType: 'success' }"
    @notify.window="notifyMessage = $event.detail.message; notifyType = $event.detail.type || 'success'; setTimeout(() => notifyMessage = null, 3000)"
>
    <div
        x-show="notifyMessage"
        x-cloak
        x-transition
        class="fixed top-4 right-4 z-[60] max-w-sm rounded-xl shadow-lg px-4 py-3 text-sm font-medium text-white"
        :class="notifyType === 'error' ? 'bg-red-600' : 'bg-green-600'"
        x-text="notifyMessage"
    ></div>

    @unless ($evidenciarMode)
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">📝 Evidencija za period</p>
            <p class="text-xs text-gray-400 mb-2 capitalize">{{ $periodLabel }}</p>
            <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif]">
                {{ $totals['broj_unosa'] }}
                <span class="text-base font-normal text-gray-500">unosa</span>
            </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">🏭 Na skladištu</p>
            <p class="text-xs text-gray-400 mb-2 capitalize">{{ $periodLabel }}</p>
            <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif]">
                {{ \App\Models\DnevnaEvidencija::formatKolicina($totals['stanje']) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">📦 Proizvedeno</p>
            <p class="text-xs text-gray-400 mb-2 capitalize">{{ $periodLabel }}</p>
            <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif]">
                {{ \App\Models\DnevnaEvidencija::formatKolicina($totals['proizvedena']) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm text-gray-500 mb-1">🚛 Predato</p>
            <p class="text-xs text-gray-400 mb-2 capitalize">{{ $periodLabel }}</p>
            <p class="text-2xl font-bold text-gray-900 font-['Plus_Jakarta_Sans',sans-serif]">
                {{ \App\Models\DnevnaEvidencija::formatKolicina($totals['predata']) }}
            </p>
        </div>
    </div>
    @endunless

    @unless ($evidenciarMode)
        @include('livewire.partials.indeks-pregled-kartice')
    @endunless

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900">Dnevna evidencija</h2>
                @if ($filterIndeksniBroj !== '')
                    <p class="text-sm text-green-700 mt-1">
                        Filtrirano: <span class="font-mono font-semibold">{{ $filterIndeksniBroj }}</span>
                    </p>
                @endif
            </div>
            <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2 shrink-0">
                @unless ($evidenciarMode)
                <button type="button"
                    wire:click="$dispatch('openZahtevModal')"
                    class="bio-btn-accent w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.25A2.25 2.25 0 0 1 4.5 6h15a2.25 2.25 0 0 1 2.25 2.25v8.25Z" />
                    </svg>
                    Zahtev za predaju
                </button>
                @endunless
                <button type="button" wire:click="openCreate" class="bio-btn-primary w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nova evidencija
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="px-6 py-4 bg-[#f8f9f4] border-b border-gray-100 flex flex-col lg:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pretraži..."
                    class="w-full pl-9 rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
            </div>
            <select wire:model.live="filterMesec" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Svi meseci</option>
                @foreach ([1=>'Januar',2=>'Februar',3=>'Mart',4=>'April',5=>'Maj',6=>'Jun',7=>'Jul',8=>'Avgust',9=>'Septembar',10=>'Oktobar',11=>'Novembar',12=>'Decembar'] as $m => $label)
                    <option value="{{ $m }}">{{ $label }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterGodina" class="rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
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
            <button type="button" wire:click="resetFilters" class="text-sm text-gray-600 hover:text-green-600 font-medium px-2">
                Reset
            </button>
        </div>

        {{-- Summary --}}
        @if ($evidencije->total() > 0 && ! $evidenciarMode)
            <div class="px-6 py-3 bg-green-50/50 border-b border-green-100 text-sm text-gray-700">
                <span class="font-medium">Ukupno za period:</span>
                Proizvedeno: <strong>{{ \App\Models\DnevnaEvidencija::formatKolicina($totals['proizvedena']) }}</strong>
                <span class="text-gray-300 mx-2">|</span>
                Predato: <strong>{{ \App\Models\DnevnaEvidencija::formatKolicina($totals['predata']) }}</strong>
                <span class="text-gray-300 mx-2">|</span>
                Na skladištu: <strong>{{ \App\Models\DnevnaEvidencija::formatKolicina($totals['stanje']) }}</strong>
            </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if ($evidencije->isEmpty())
                <div class="text-center py-16 px-6">
                    <span class="text-5xl mb-4 block">📋</span>
                    <h3 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-semibold text-gray-900 mb-2">Nema evidencija</h3>
                    <p class="text-gray-500 text-sm mb-6">Dodajte prvu dnevnu evidenciju klikom na dugme iznad.</p>
                    <button type="button" wire:click="openCreate" class="bio-btn-primary">
                        + Nova evidencija
                    </button>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach ([
                                'datum' => 'Datum',
                                'indeksni_broj' => 'Indeksni br.',
                                'naziv_otpada' => 'Naziv otpada',
                                'karakter_otpada' => 'Karakter',
                                'proizvedena_kolicina' => 'Proiz.',
                                'predata_kolicina' => 'Pred.',
                                'stanje_na_skladistu' => 'Stanje',
                            ] as $col => $label)
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:text-green-600 whitespace-nowrap"
                                    wire:click="sort('{{ $col }}')">
                                    {{ $label }}
                                    @if ($sortBy === $col)
                                        <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </th>
                            @endforeach
                            @unless ($evidenciarMode)
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Primalac</th>
                            @endunless
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($evidencije as $row)
                            <tr class="hover:bg-gray-50/80 transition-colors" wire:key="evidencija-{{ $row->id }}">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-900">{{ $row->datum->format('d.m.Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-gray-600">{{ $row->indeksni_broj }}</td>
                                <td class="px-4 py-3 max-w-[180px] truncate text-gray-900" title="{{ $row->naziv_otpada }}">{{ $row->naziv_otpada }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $badge = match($row->karakter_otpada) {
                                            'inertan' => 'bg-blue-100 text-blue-800',
                                            'neopasan' => 'bg-green-100 text-green-800',
                                            'opasan' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                        $label = match($row->karakter_otpada) {
                                            'inertan' => 'INERTAN',
                                            'neopasan' => 'NEOPASAN',
                                            'opasan' => 'OPASAN',
                                            default => strtoupper($row->karakter_otpada),
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ \App\Models\DnevnaEvidencija::formatKolicina($row->proizvedena_kolicina) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ \App\Models\DnevnaEvidencija::formatKolicina($row->predata_kolicina) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap font-medium
                                    @if ($row->stanje_na_skladistu > 0) text-green-600
                                    @elseif ($row->stanje_na_skladistu == 0) text-amber-600
                                    @else text-red-600 @endif">
                                    {{ \App\Models\DnevnaEvidencija::formatKolicina($row->stanje_na_skladistu) }}
                                </td>
                                @unless ($evidenciarMode)
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($row->predat_operateru)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ Predato
                                        </span>
                                        @if ($row->operater_naziv)
                                            <div class="text-xs text-gray-500 mt-1">{{ $row->operater_naziv }}</div>
                                        @endif
                                        @if ($row->dokumentKretanja)
                                            <div class="flex flex-wrap items-center gap-2 text-xs mt-1.5">
                                                <span class="text-gray-400">{{ $row->dokumentKretanja->broj_dokumenta }}</span>
                                                <a href="{{ route('doko.download', $row->dokumentKretanja) }}"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-medium">
                                                    📥 DOKO
                                                </a>
                                                <a href="{{ route('doko.deo1', $row->dokumentKretanja) }}"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium">
                                                    📋 DEO1
                                                </a>
                                            </div>
                                        @elseif ($row->stanje_na_skladistu > 0 && ! $row->predat_operateru)
                                            <p class="text-xs text-amber-600 mt-1">Čeka DOKO od administratora</p>
                                        @endif
                                    @elseif ($row->getUAktivnomZahtevu())
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            📨 U zahtevu
                                        </span>
                                    @elseif ($odbijen = $row->getOdbijenZahtev())
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ❌ Zahtev odbijen
                                        </span>
                                        <p class="text-xs text-red-600 mt-1 max-w-[200px]">
                                            Obrišite zahtev #{{ $odbijen->id }} da biste ponovo poslali otpad.
                                        </p>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            ⏳ Čeka predaju
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 max-w-[120px] truncate text-gray-500">{{ $row->naziv_primaoca ?: '—' }}</td>
                                @endunless
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if (! $evidenciarMode && $confirmingDeleteId === $row->id)
                                        <div class="inline-flex items-center gap-2 text-xs">
                                            <span class="text-gray-600">Jeste li sigurni?</span>
                                            <button type="button" wire:click="delete({{ $row->id }})" wire:loading.attr="disabled"
                                                class="text-red-600 font-semibold hover:text-red-800">Da, obriši</button>
                                            <button type="button" wire:click="cancelDelete" class="text-gray-500 hover:text-gray-700">Otkaži</button>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1">
                                            @if ((! $evidenciarMode || $row->user_id === auth()->id()) && ! $row->dokument_kretanja_id)
                                                <button type="button" wire:click="openEdit({{ $row->id }})" title="Izmeni"
                                                    class="p-1.5 text-gray-400 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                            @endif
                                            @unless ($evidenciarMode)
                                            <button type="button" wire:click="confirmDelete({{ $row->id }})" title="Obriši"
                                                class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                            @endunless
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if ($evidencije->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
                <span>Prikazano {{ $evidencije->firstItem() }}–{{ $evidencije->lastItem() }} od {{ $evidencije->total() }} zapisa</span>
                {{ $evidencije->links() }}
            </div>
        @elseif ($evidencije->total() > 0)
            <div class="px-6 py-3 border-t border-gray-100 text-sm text-gray-500">
                Prikazano {{ $evidencije->total() }} od {{ $evidencije->total() }} zapisa
            </div>
        @endif
    </div>

    <livewire:dnevna-evidencija-form />
</div>
