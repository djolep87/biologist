<div
    x-data="{ notifyMessage: null, notifyType: 'success' }"
    @notify.window="notifyMessage = $event.detail.message; notifyType = $event.detail.type || 'success'; setTimeout(() => notifyMessage = null, 3000)"
    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
>
    <div
        x-show="notifyMessage"
        x-cloak
        x-transition
        class="fixed top-4 right-4 z-[60] max-w-sm rounded-xl shadow-lg px-4 py-3 text-sm font-medium text-white"
        :class="notifyType === 'error' ? 'bg-red-600' : 'bg-green-600'"
        x-text="notifyMessage"
    ></div>

    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
        <span class="text-lg">🔢</span>
        <div>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">Podešavanja numeracije DKO</h2>
            <p class="text-sm text-gray-500">Format jedinstvenog broja izveštaja za DOKO dokumente ovog klijenta.</p>
        </div>
    </div>

    @php
        $godina = now()->format('Y');
        $cifre = in_array($brojCifara, [3, 4], true) ? $brojCifara : 3;
        $prviLok = collect($lokacije)->pluck('oznaka')->filter()->first() ?: 'BG01';
        $primer = match ($format) {
            'lokacija' => $prefix.$prviLok.'-'.str_pad('1', $cifre, '0', STR_PAD_LEFT).'/'.$godina,
            'vremenski' => $prefix.now()->format('Ymd').'-01',
            default => $prefix.str_pad('1', $cifre, '0', STR_PAD_LEFT).'/'.$godina,
        };
    @endphp

    <div class="p-6 space-y-6">
        {{-- Live primer --}}
        <div class="rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3 flex items-center justify-between">
            <span class="text-sm text-indigo-700 font-medium">Primer broja</span>
            <span class="font-mono text-lg font-bold text-indigo-900">{{ $primer }}</span>
        </div>

        {{-- Format --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">Format broja izveštaja</label>
            <div class="space-y-2">
                <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ $format === 'osnovni' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                    <input type="radio" wire:model.live="format" value="osnovni" class="mt-1 text-indigo-600 focus:ring-indigo-500" />
                    <span class="text-sm">
                        <span class="font-mono font-semibold text-gray-900">001/{{ $godina }}</span>
                        <span class="text-gray-500"> — Osnovni</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Redni broj/godina. Reset svakog 1. januara.</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ $format === 'lokacija' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                    <input type="radio" wire:model.live="format" value="lokacija" class="mt-1 text-indigo-600 focus:ring-indigo-500" />
                    <span class="text-sm">
                        <span class="font-mono font-semibold text-gray-900">BG01-001/{{ $godina }}</span>
                        <span class="text-gray-500"> — Sa oznakom lokacije</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Redni broj po lokaciji/pogonu. Reset svakog 1. januara.</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors {{ $format === 'vremenski' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                    <input type="radio" wire:model.live="format" value="vremenski" class="mt-1 text-indigo-600 focus:ring-indigo-500" />
                    <span class="text-sm">
                        <span class="font-mono font-semibold text-gray-900">{{ now()->format('Ymd') }}-01</span>
                        <span class="text-gray-500"> — Vremenski</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Datum + dnevni inkrement. Bez godišnjeg reseta.</span>
                    </span>
                </label>
            </div>
            @error('format') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Broj cifara --}}
        @if ($format !== 'vremenski')
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Broj cifara u rednom broju</label>
                <div class="flex gap-2">
                    <label class="flex-1 flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition-colors {{ $brojCifara === 3 ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                        <input type="radio" wire:model.live="brojCifara" value="3" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-sm text-gray-700">3 cifre <span class="font-mono text-gray-400">(001…999)</span></span>
                    </label>
                    <label class="flex-1 flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition-colors {{ $brojCifara === 4 ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                        <input type="radio" wire:model.live="brojCifara" value="4" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-sm text-gray-700">4 cifre <span class="font-mono text-gray-400">(0001…9999)</span></span>
                    </label>
                </div>
                @error('brojCifara') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        {{-- Prefix --}}
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-1">Prefiks <span class="font-normal text-gray-400">(opciono)</span></label>
            <input type="text" wire:model.live.debounce.300ms="prefix" maxlength="20" placeholder="npr. DKO-"
                class="w-full max-w-xs rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
            <p class="mt-1 text-xs text-gray-400">Dodaje se na početak broja. Ostavite prazno za bez prefiksa.</p>
            @error('prefix') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Lokacije (samo za format 'lokacija') --}}
        @if ($format === 'lokacija')
            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Lokacije / pogoni</label>
                <div class="space-y-2">
                    @forelse ($lokacije as $i => $lok)
                        <div class="flex items-center gap-2" wire:key="lok-{{ $i }}">
                            <input type="text" wire:model.live="lokacije.{{ $i }}.oznaka" maxlength="10" placeholder="Oznaka (npr. BG01)"
                                class="w-40 rounded-lg border-gray-300 text-sm font-mono focus:border-indigo-500 focus:ring-indigo-500" />
                            <input type="text" wire:model="lokacije.{{ $i }}.naziv" maxlength="100" placeholder="Naziv lokacije (opciono)"
                                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <button type="button" wire:click="removeLokacija({{ $i }})" title="Ukloni"
                                class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:bg-red-50">🗑</button>
                        </div>
                        @error('lokacije.'.$i.'.oznaka') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    @empty
                        <p class="text-sm text-gray-400">Još nema definisanih lokacija.</p>
                    @endforelse
                </div>
                <button type="button" wire:click="addLokacija"
                    class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    + Dodaj lokaciju
                </button>
                @error('lokacije') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div class="pt-2 border-t border-gray-100 flex justify-end">
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg disabled:opacity-50">
                <span wire:loading.remove wire:target="save">💾 Sačuvaj podešavanja</span>
                <span wire:loading wire:target="save">Čuvanje…</span>
            </button>
        </div>
    </div>
</div>
