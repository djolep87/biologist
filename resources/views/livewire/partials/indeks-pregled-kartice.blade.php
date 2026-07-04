@if ($indeksPregled->isNotEmpty())
    <div class="mb-6">
        <div class="flex items-center justify-between gap-3 mb-3">
            <h3 class="font-['Plus_Jakarta_Sans',sans-serif] text-base font-bold text-gray-900">
                Otpad po indeksnom broju
            </h3>
            @if ($filterIndeksniBroj !== '')
                <button type="button" wire:click="clearIndeksFilter"
                    class="text-sm font-medium text-green-600 hover:text-green-700">
                    Prikaži sve indekse
                </button>
            @endif
        </div>

        <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
            @php
                $sviTotals = [
                    'proizvedena' => $indeksPregled->sum('proizvedeno'),
                    'predata' => $indeksPregled->sum('predato'),
                    'stanje' => $indeksPregled->sum('stanje'),
                ];
            @endphp

            <button type="button" wire:click="clearIndeksFilter"
                class="snap-start shrink-0 w-[200px] text-left rounded-2xl border-2 p-4 transition-all
                    {{ $filterIndeksniBroj === '' ? 'border-green-500 bg-green-50 shadow-sm' : 'border-gray-200 bg-white hover:border-green-300 hover:shadow-sm' }}">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Svi otpadi</p>
                <p class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-gray-900 text-sm mb-3">
                    {{ $indeksPregled->count() }} {{ $indeksPregled->count() === 1 ? 'indeks' : 'indeksa' }}
                </p>
                <div class="space-y-1 text-xs text-gray-600">
                    <p>Proizv.: <span class="font-semibold text-gray-900">{{ \App\Models\DnevnaEvidencija::formatKolicina($sviTotals['proizvedena']) }}</span></p>
                    <p>Pred.: <span class="font-semibold text-gray-900">{{ \App\Models\DnevnaEvidencija::formatKolicina($sviTotals['predata']) }}</span></p>
                    <p>Skladište: <span class="font-semibold text-green-700">{{ \App\Models\DnevnaEvidencija::formatKolicina($sviTotals['stanje']) }}</span></p>
                </div>
            </button>

            @foreach ($indeksPregled as $indeks)
                @php
                    $isActive = $filterIndeksniBroj === $indeks->indeksni_broj;
                    $karakterBadge = match($indeks->karakter_otpada) {
                        'inertan' => 'bg-blue-100 text-blue-800',
                        'neopasan' => 'bg-green-100 text-green-800',
                        'opasan' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800',
                    };
                @endphp
                <button type="button" wire:click="selectIndeks(@js($indeks->indeksni_broj))"
                    class="snap-start shrink-0 w-[220px] text-left rounded-2xl border-2 p-4 transition-all
                        {{ $isActive ? 'border-green-500 bg-green-50 shadow-sm ring-2 ring-green-200' : 'border-gray-200 bg-white hover:border-green-300 hover:shadow-sm' }}">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <p class="font-mono text-sm font-bold text-gray-900">{{ $indeks->indeksni_broj }}</p>
                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $karakterBadge }}">
                            {{ strtoupper($indeks->karakter_otpada) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 line-clamp-2 mb-3 min-h-[2rem]" title="{{ $indeks->naziv_otpada }}">
                        {{ $indeks->naziv_otpada }}
                    </p>
                    <div class="grid grid-cols-3 gap-1 text-center">
                        <div class="rounded-lg bg-gray-50 px-1 py-1.5">
                            <p class="text-[10px] text-gray-500">Proizv.</p>
                            <p class="text-xs font-bold text-gray-900">{{ \App\Models\DnevnaEvidencija::formatKolicina($indeks->proizvedeno) }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-1 py-1.5">
                            <p class="text-[10px] text-gray-500">Pred.</p>
                            <p class="text-xs font-bold text-gray-900">{{ \App\Models\DnevnaEvidencija::formatKolicina($indeks->predato) }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 px-1 py-1.5">
                            <p class="text-[10px] text-green-700">Skladište</p>
                            <p class="text-xs font-bold text-green-800">{{ \App\Models\DnevnaEvidencija::formatKolicina($indeks->stanje) }}</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2">{{ $indeks->broj_unosa }} {{ $indeks->broj_unosa === 1 ? 'unos' : 'unosa' }}</p>
                    @if ($filterMesec !== '' && Auth::user()->currentTeam)
                        <a href="{{ route('evidencija.export', ['godina' => (int) $filterGodina, 'mesec' => (int) $filterMesec, 'indeksni_broj' => $indeks->indeksni_broj]) }}"
                            @click.stop
                            class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 hover:text-emerald-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12M12 16.5V3" />
                            </svg>
                            Preuzmi Excel
                        </a>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
@endif
