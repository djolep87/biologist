<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-cloak>
            <div class="absolute inset-0 bg-black/50" wire:click="closeModal"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between rounded-t-2xl z-10">
                    <h3 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900">
                        Zahtev za predaju otpada operateru
                    </h3>
                    <button type="button" wire:click="closeModal" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5">
                    @if ($step === 1)
                        <p class="text-sm font-semibold text-gray-700 mb-1">Korak 1 od 2 — Izaberi izveštaje</p>
                        <div class="h-0.5 bg-green-500 w-1/2 mb-5 rounded"></div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter po indeksnom broju:</label>
                        <select wire:model.live="filterIndeksni"
                            class="w-full mb-4 rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Svi indeksni brojevi</option>
                            @foreach ($indeksniBrojevi as $indeks)
                                <option value="{{ $indeks->indeksni_broj }}">{{ $indeks->indeksni_broj }} — {{ $indeks->naziv_otpada }}</option>
                            @endforeach
                        </select>

                        <div class="border border-gray-200 rounded-xl divide-y divide-gray-100 max-h-64 overflow-y-auto">
                            @forelse ($evidencije as $ev)
                                @php
                                    $izabran = in_array($ev->id, $izabraniIds, true);
                                    $disabled = $zakljucaniIndeksni && $zakljucaniIndeksni !== $ev->indeksni_broj && ! $izabran;
                                @endphp
                                <label class="flex items-start gap-3 px-4 py-3 {{ $disabled ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:bg-gray-50 cursor-pointer' }}">
                                    <input type="checkbox"
                                        class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                        @if ($izabran) checked @endif
                                        @if ($disabled) disabled @endif
                                        wire:click="toggleIzbor({{ $ev->id }}, @js($ev->indeksni_broj))">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900">
                                            <span class="font-mono">{{ $ev->indeksni_broj }}</span> — {{ $ev->naziv_otpada }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $ev->datum->format('d.m.Y.') }}
                                            | Stanje: {{ \App\Models\DnevnaEvidencija::formatKolicina($ev->stanje_na_skladistu) }}
                                            | <span class="text-amber-600">⏳ Čeka predaju</span>
                                        </p>
                                    </div>
                                </label>
                            @empty
                                <div class="px-4 py-8 text-center text-sm text-gray-500">
                                    Nema dostupnih izveštaja za predaju.
                                </div>
                            @endforelse
                        </div>

                        @error('izbor')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                            ⚠ Možete birati izveštaje samo jednog indeksnog broja
                        </p>

                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                Izabrano: <strong>{{ $brojIzabranih }}</strong> izveštaja
                                | Ukupno: <strong>{{ \App\Models\DnevnaEvidencija::formatKolicina($ukupnaMasa) }}</strong>
                            </p>
                            <button type="button" wire:click="nextStep"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                                Dalje →
                            </button>
                        </div>
                    @else
                        <p class="text-sm font-semibold text-gray-700 mb-1">Korak 2 od 2 — Napomena (opciono)</p>
                        <div class="h-0.5 bg-green-500 w-full mb-5 rounded"></div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">Napomena administratoru</label>
                        <textarea wire:model="napomenaKlijenta" rows="3"
                            placeholder="npr. hitno, posebni uslovi prevoza, kontakt..."
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500 mb-4"></textarea>

                        <div class="bg-[#f8f9f4] border border-gray-200 rounded-xl p-4 mb-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Pregled zahteva</p>
                            @php $prva = \App\Models\DnevnaEvidencija::find($izabraniIds[0] ?? null); @endphp
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Vrsta otpada:</dt>
                                    <dd class="font-medium text-gray-900 text-right">{{ $prva?->naziv_otpada }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Indeksni br.:</dt>
                                    <dd class="font-mono font-medium text-gray-900">{{ $prva?->indeksni_broj }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Broj izveštaja:</dt>
                                    <dd class="font-medium text-gray-900">{{ $brojIzabranih }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Ukupna masa:</dt>
                                    <dd class="font-medium text-gray-900">{{ \App\Models\DnevnaEvidencija::formatKolicina($ukupnaMasa) }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Firma:</dt>
                                    <dd class="font-medium text-gray-900">{{ Auth::user()->currentTeam?->name }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <button type="button" wire:click="prevStep"
                                class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                                ← Nazad
                            </button>
                            <button type="button" wire:click="posaljiZahtev" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg disabled:opacity-50">
                                <span wire:loading.remove wire:target="posaljiZahtev">📤 Pošalji zahtev</span>
                                <span wire:loading wire:target="posaljiZahtev">Šaljem...</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
