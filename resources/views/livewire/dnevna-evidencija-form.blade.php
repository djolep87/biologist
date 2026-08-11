<div x-data x-effect="document.body.classList.toggle('overflow-hidden', $wire.showModal)">
    @if ($showModal)
        <div
            class="fixed inset-0 z-[100] flex justify-end"
            role="dialog"
            aria-modal="true"
            aria-labelledby="evidencija-form-title"
            wire:key="evidencija-modal"
            @keydown.escape.window="$wire.closeModal()"
        >
            <button
                type="button"
                class="absolute inset-0 cursor-default"
                style="background-color: rgba(17, 24, 39, 0.55);"
                wire:click="closeModal"
                aria-label="Zatvori"
            ></button>

            <div
                class="bio-drawer-panel relative z-10 ml-auto flex h-svh flex-col bg-white shadow-2xl"
                wire:click.stop
            >
                {{-- Header --}}
                <div class="flex shrink-0 items-center justify-between px-6 py-4" style="background-color: #1e2430;">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">DEO1</p>
                        <h2 id="evidencija-form-title" class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-semibold text-white">
                            {{ $isEdit ? 'Izmena evidencije' : 'Nova dnevna evidencija' }}
                        </h2>
                    </div>
                    <button type="button" wire:click="closeModal" class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-white/10 hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="flex min-h-0 flex-1 flex-col">
                {{-- Scrollable body --}}
                <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain bg-white px-5 py-5 sm:px-6">
                    @if ($errors->any())
                        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            <p class="font-semibold mb-1">Ispravite sledeće greške:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="space-y-5">
                        {{-- Zaglavlje --}}
                        <div class="bio-section">
                            <h3 class="bio-section-title">Zaglavlje evidencije</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="bio-label">Godina</label>
                                    <input type="number" wire:model.live="godina" min="2020" max="2030" class="bio-input" />
                                    @error('godina') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="bio-label">Mesec</label>
                                    <select wire:model.live="mesec" class="bio-input">
                                        @foreach ([1=>'Januar',2=>'Februar',3=>'Mart',4=>'April',5=>'Maj',6=>'Jun',7=>'Jul',8=>'Avgust',9=>'Septembar',10=>'Oktobar',11=>'Novembar',12=>'Decembar'] as $m => $label)
                                            <option value="{{ $m }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('mesec') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="bio-label">Indeksni broj</label>
                                <input type="text" wire:model.live="indeksni_broj" list="katalog-otpada" maxlength="20" placeholder="npr. 15 01 02" class="bio-input font-mono" />
                                <datalist id="katalog-otpada">
                                    @foreach ($katalog as $grupa => $stavke)
                                        @foreach ($stavke as $kod => $stavka)
                                            <option value="{{ $kod }}">{{ $stavka['naziv'] }}@isset($stavka['napomena']) — {{ $stavka['napomena'] }}@endisset · {{ $grupa }}</option>
                                        @endforeach
                                    @endforeach
                                </datalist>
                                <p class="mt-1 text-xs text-gray-500">
                                    Kucajte šifru ili naziv — npr. „hrana", „istekl", „ambalaža". Možete uneti i šifru koje nema u listi.
                                </p>
                                @error('indeksni_broj') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="bio-label">Naziv otpada</label>
                                <input type="text" wire:model="naziv_otpada" placeholder="Naziv prema katalogu" class="bio-input" />
                                @error('naziv_otpada') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="bio-label">Opis otpada <span class="font-normal text-gray-400">(opciono)</span></label>
                                <textarea wire:model="opis_otpada" rows="2" placeholder="Dodatni opis..." class="bio-input resize-none"></textarea>
                            </div>

                            <div>
                                <label class="bio-label">Karakter otpada</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model.live="karakter_otpada" value="inertan" class="peer sr-only" />
                                        <span class="flex items-center justify-center rounded-lg border-2 border-gray-200 bg-white px-2 py-2.5 text-center text-xs font-semibold text-gray-600 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-800 sm:text-sm">Inertan</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model.live="karakter_otpada" value="neopasan" class="peer sr-only" />
                                        <span class="flex items-center justify-center rounded-lg border-2 border-gray-200 bg-white px-2 py-2.5 text-center text-xs font-semibold text-gray-600 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-800 sm:text-sm">Neopasan</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model.live="karakter_otpada" value="opasan" class="peer sr-only" />
                                        <span class="flex items-center justify-center rounded-lg border-2 border-gray-200 bg-white px-2 py-2.5 text-center text-xs font-semibold text-gray-600 transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-800 sm:text-sm">Opasan</span>
                                    </label>
                                </div>
                                @error('karakter_otpada') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            @if ($karakter_otpada === 'opasan')
                                <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                    ⚠️ Opasan otpad — obavezno priložite Dokument o kretanju opasnog otpada
                                </div>
                            @endif

                            <div>
                                <label class="bio-label">Fizičko stanje</label>
                                <select wire:model="fizicko_stanje" class="bio-input">
                                    <option value="cvrsta-prah">Čvrsta — prah</option>
                                    <option value="cvrsta-komadi">Čvrsta — komadi</option>
                                    <option value="viskozna-pasta">Viskozna pasta</option>
                                    <option value="tecna">Tečna</option>
                                    <option value="talog">Talog</option>
                                </select>
                                @error('fizicko_stanje') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="bio-label">Lice koje vodi evidenciju</label>
                                <input type="text" wire:model="lice_koje_vodi" class="bio-input" />
                                @error('lice_koje_vodi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Dnevni podaci --}}
                        <div class="bio-section">
                            <h3 class="bio-section-title">Dnevni podaci</h3>

                            <div>
                                <label class="bio-label">Datum</label>
                                <input type="date" wire:model.live="datum" class="bio-input" />
                                @error('datum') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="bio-label">Proizvedeno (t)</label>
                                    <input type="text" inputmode="decimal" placeholder="0.00" wire:model.live.debounce.300ms="proizvedena_kolicina" class="bio-input" />
                                    @error('proizvedena_kolicina') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="bio-label">Predato (t)</label>
                                    <input type="text" inputmode="decimal" placeholder="0.00" wire:model.live.debounce.300ms="predata_kolicina" class="bio-input" />
                                    @error('predata_kolicina') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-4 py-3">
                                <span class="text-sm font-medium text-green-800">Stanje na skladištu</span>
                                <span class="text-lg font-bold text-green-700">
                                    {{ \App\Models\DnevnaEvidencija::formatKolicina($stanje_na_skladistu) }}
                                </span>
                            </div>

                            <div>
                                <p class="bio-label mb-2">Otpad predat</p>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach ([
                                        'predat_sakupljacu' => 'Sakupljaču',
                                        'predat_operateru_r' => 'Operateru (R)',
                                        'predat_operateru_d' => 'Operateru (D)',
                                        'izvoz' => 'Izvoz',
                                    ] as $field => $label)
                                        <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                            <input type="checkbox" wire:model.live="{{ $field }}" class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500" />
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            @if ($predat_sakupljacu || $predat_operateru_r || $predat_operateru_d || $izvoz)
                                <div class="space-y-4 rounded-lg border border-dashed border-gray-300 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Podaci o primaocu</p>
                                    <div>
                                        <label class="bio-label">Naziv primaoca</label>
                                        <input type="text" wire:model="naziv_primaoca" class="bio-input" />
                                        @error('naziv_primaoca') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="bio-label">Broj dozvole primaoca</label>
                                        <input type="text" wire:model="broj_dozvole_primaoca" class="bio-input" />
                                        @error('broj_dozvole_primaoca') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="bio-label">Način određivanja količine</label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach (['1' => 'Merenje', '2' => 'Proračun', '3' => 'Procena'] as $value => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="nacin_odredjivanja" value="{{ $value }}" class="peer sr-only" />
                                            <span class="flex items-center justify-center rounded-lg border-2 border-gray-200 bg-white px-2 py-2 text-center text-xs font-medium text-gray-600 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-800 sm:text-sm">
                                                {{ $label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex shrink-0 items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:px-6">
                    <button type="button" wire:click="closeModal" class="bio-btn-secondary">
                        Otkaži
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="bio-btn-primary">
                        <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">Sačuvaj</span>
                        <span wire:loading wire:target="save">Čuvanje...</span>
                    </button>
                </div>
                </form>
            </div>
        </div>
    @endif
</div>
