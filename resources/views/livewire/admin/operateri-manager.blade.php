<div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">Operateri</h2>
                <p class="text-sm text-gray-500 mt-0.5">Globalna lista operatera za DOKO dokumente</p>
            </div>
            <button type="button" wire:click="openCreate" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shrink-0">
                + Novi operater
            </button>
        </div>

        <div class="px-6 py-4 bg-[#f8f9f4] border-b border-gray-100 flex flex-wrap gap-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Pretraži naziv, PIB..."
                class="flex-1 min-w-[200px] rounded-lg border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
            <select wire:model.live="filterTip" class="rounded-lg border-gray-200 text-sm">
                <option value="">Svi tipovi</option>
                @foreach ($tipOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterStatus" class="rounded-lg border-gray-200 text-sm">
                <option value="">Svi statusi</option>
                <option value="aktivan">Aktivni</option>
                <option value="neaktivan">Neaktivni</option>
            </select>
            <button type="button" wire:click="resetFilters" class="text-sm text-gray-600 hover:text-indigo-600">Reset</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-[#f8f9f4]">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Naziv</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">PIB</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Tip</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Dozvola do</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($operateri as $op)
                        <tr class="hover:bg-gray-50" wire:key="operater-{{ $op->id }}">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $op->kratki_naziv ?? $op->naziv }}</p>
                                <p class="text-xs text-gray-500">{{ $op->naziv }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $op->pib }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-[140px]">{{ $op->tipDisplay() }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($op->dozvola_vazi_do)
                                    @if ($op->dozvala_istekla)
                                        <span class="text-red-600 font-medium">🔴 Istekla</span>
                                        <span class="block text-xs text-red-500">{{ $op->dozvola_vazi_do->format('d.m.Y.') }}</span>
                                    @elseif ($op->dozvala_istice)
                                        <span class="text-amber-600 font-medium">🟡 {{ $op->dozvola_vazi_do->format('d.m.Y.') }}</span>
                                    @else
                                        <span class="text-green-700">{{ $op->dozvola_vazi_do->format('d.m.Y.') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($op->aktivan)
                                    <span class="text-green-600">✅</span>
                                @else
                                    <span class="text-gray-400">⏸</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if ($confirmingDeleteId === $op->id)
                                    <span class="text-xs text-gray-600 mr-2">Obrisati?</span>
                                    <button type="button" wire:click="delete({{ $op->id }})" class="text-red-600 text-xs font-semibold mr-2">Da</button>
                                    <button type="button" wire:click="cancelDelete" class="text-gray-500 text-xs">Ne</button>
                                @else
                                    <button type="button" wire:click="openEdit({{ $op->id }})" class="p-1.5 text-gray-500 hover:text-indigo-600" title="Izmeni">✏️</button>
                                    <button type="button" wire:click="confirmDelete({{ $op->id }})" class="p-1.5 text-gray-500 hover:text-red-600" title="Obriši">🗑</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Nema operatera. Dodajte prvog.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($operateri->hasPages())
            <div class="px-6 py-4 border-t">{{ $operateri->links() }}</div>
        @endif
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-[100] flex justify-end" @keydown.escape.window="$wire.closeModal()">
            <button type="button" class="absolute inset-0 bg-black/50" wire:click="closeModal"></button>
            <div class="relative z-10 ml-auto w-full max-w-2xl h-svh bg-white shadow-2xl flex flex-col" wire:click.stop>
                <div class="px-6 py-4 bg-[#1e2430] text-white shrink-0">
                    <h3 class="font-semibold">{{ $operaterId ? 'Izmena operatera' : 'Novi operater' }}</h3>
                </div>
                <form wire:submit="save" class="flex flex-col flex-1 min-h-0">
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        @if ($errors->any())
                            <div class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
                                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                            </div>
                        @endif

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Osnovni podaci</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2"><label class="text-sm text-gray-600">Naziv firme *</label><input type="text" wire:model="naziv" class="mt-1 w-full rounded-lg border-gray-200 text-sm" /></div>
                                <div><label class="text-sm text-gray-600">Kratki naziv *</label><input type="text" wire:model="kratki_naziv" maxlength="30" class="mt-1 w-full rounded-lg border-gray-200 text-sm" /><p class="text-xs text-gray-400 mt-1">Prikazuje se u dropdown listama</p></div>
                                <div><label class="text-sm text-gray-600">PIB *</label><input type="text" wire:model="pib" maxlength="9" class="mt-1 w-full rounded-lg border-gray-200 text-sm font-mono" /></div>
                                <div><label class="text-sm text-gray-600">Matični broj *</label><input type="text" wire:model="maticni_broj" maxlength="8" class="mt-1 w-full rounded-lg border-gray-200 text-sm font-mono" /></div>
                                <div class="sm:col-span-2">
                                    <label class="text-sm text-gray-600 block mb-2">Tip operatera *</label>
                                    <div class="flex flex-wrap gap-3 text-sm">
                                        @foreach ($tipOptions as $key => $label)
                                            <label class="inline-flex items-center gap-1">
                                                <input type="checkbox" wire:model="tip" value="{{ $key }}" class="rounded border-gray-200 text-indigo-600" />
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Adresa i kontakt</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ([['opstina','Opština'],['mesto','Mesto'],['postanski_broj','Poštanski broj'],['ulica','Ulica i broj'],['telefon','Telefon'],['faks','Faks'],['email','Email'],['kontakt_osoba','Kontakt osoba']] as [$f, $l])
                                    <div><label class="text-sm text-gray-600">{{ $l }}</label><input type="text" wire:model="{{ $f }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm" @if($f==='email') type="email" @endif /></div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Dozvola za upravljanje otpadom</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div><label class="text-sm text-gray-600">Broj dozvole</label><input type="text" wire:model="dozvola_broj" class="mt-1 w-full rounded-lg border-gray-200 text-sm" /></div>
                                <div><label class="text-sm text-gray-600">Datum izdavanja</label><input type="date" wire:model="dozvola_datum_izdavanja" class="mt-1 w-full rounded-lg border-gray-200 text-sm" /></div>
                                <div>
                                    <label class="text-sm text-gray-600">Važi do</label>
                                    <input type="date" wire:model="dozvola_vazi_do" class="mt-1 w-full rounded-lg border-gray-200 text-sm" />
                                    @if ($dozvola_vazi_do)
                                        @if (\Carbon\Carbon::parse($dozvola_vazi_do)->isPast())
                                            <p class="text-red-600 text-sm mt-1">⚠ Dozvola je istekla!</p>
                                        @elseif (\Carbon\Carbon::parse($dozvola_vazi_do)->diffInDays(now()) <= 30)
                                            <p class="text-amber-600 text-sm mt-1">⚠ Dozvola ističe za manje od 30 dana!</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Operativni podaci</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div><label class="text-sm text-gray-600">Default R oznaka</label><select wire:model="r_oznaka" class="mt-1 w-full rounded-lg border-gray-200 text-sm"><option value="">—</option>@foreach($rOznake as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select></div>
                                <div><label class="text-sm text-gray-600">Default D oznaka</label><select wire:model="d_oznaka" class="mt-1 w-full rounded-lg border-gray-200 text-sm"><option value="">—</option>@foreach($dOznake as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach</select></div>
                            </div>
                            <label class="text-sm text-gray-600 block mb-2">Prihvata indeksne brojeve</label>
                            <div class="flex gap-2 mb-2">
                                <input type="text" wire:model="indeksInput" wire:keydown.enter.prevent="addIndeksTag"
                                    placeholder="npr. 15 01 02 — Enter za dodavanje"
                                    class="flex-1 rounded-lg border-gray-200 text-sm" />
                                <button type="button" wire:click="addIndeksTag" class="px-3 py-2 bg-gray-100 rounded-lg text-sm">Dodaj</button>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-2">
                                @foreach ($prihvata_indeksne_brojeve as $i => $tag)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-mono">
                                        {{ $tag }}
                                        <button type="button" wire:click="removeIndeksTag({{ $i }})" class="text-indigo-600 hover:text-indigo-900">&times;</button>
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500">Unesite indeksne brojeve otpada koje ovaj operater prihvata. Koristite za filtriranje u DOKO obrascu.</p>
                            <div class="mt-4"><label class="text-sm text-gray-600">Napomena</label><textarea wire:model="napomena" rows="2" class="mt-1 w-full rounded-lg border-gray-200 text-sm"></textarea></div>
                            <label class="inline-flex items-center gap-2 mt-4 text-sm"><input type="checkbox" wire:model="aktivan" class="rounded border-gray-200 text-indigo-600" /> Aktivan</label>
                        </div>
                    </div>
                    <div class="shrink-0 px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm text-gray-600">Otkaži</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">Sačuvaj</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
