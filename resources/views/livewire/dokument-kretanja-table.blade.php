<div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900">DOKO dokumenti kretanja</h2>
            <p class="text-sm text-gray-500 mt-1">Pregled, preuzimanje i brisanje DOKO i DEO1 obrazaca.</p>
        </div>

        <div class="px-6 py-4 bg-[#f8f9f4] border-b border-gray-100 space-y-3">
            <div class="flex flex-wrap gap-2">
                @foreach (['' => 'Svi DKO', 'obicni' => 'Komunalni/Industrijski', 'gradjevinski' => 'Građevinski'] as $value => $label)
                    <button type="button" wire:click="setFilterTip('{{ $value }}')"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $filterTip === $value ? 'bg-[#1e2430] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pretraži po broju izveštaja, broju dokumenta, indeksu, primaocu..."
                class="w-full max-w-md rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
        </div>

        @if ($dokumenti->isEmpty())
            <div class="px-6 py-12 text-center text-gray-500">
                <p class="mb-2">Nema kreiranih DOKO dokumenata.</p>
                <p class="text-sm">DOKO dokumente kreira administrator. Ovde možete pregledati i preuzeti generisane obrasce.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    @php
                        $sortIcon = fn ($col) => $sortBy === $col ? ($sortDir === 'asc' ? '▲' : '▼') : '';
                    @endphp
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none hover:text-gray-900" wire:click="sort('broj_izvestaja')">
                                Broj izveštaja <span class="text-green-600">{{ $sortIcon('broj_izvestaja') }}</span>
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none hover:text-gray-900" wire:click="sort('broj_dokumenta')">
                                Br. dokumenta <span class="text-green-600">{{ $sortIcon('broj_dokumenta') }}</span>
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none hover:text-gray-900" wire:click="sort('datum_predaje')">
                                Datum predaje <span class="text-green-600">{{ $sortIcon('datum_predaje') }}</span>
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Indeksni br.</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Vrsta otpada</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Tip</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Masa (t)</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Primalac</th>
                            @if ($showAllTeams)
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Firma</th>
                            @endif
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Br. ev.</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($dokumenti as $dok)
                            <tr class="hover:bg-gray-50/80" wire:key="dokument-{{ $dok->id }}">
                                <td class="px-4 py-3 font-mono text-sm font-bold text-indigo-700">{{ $dok->broj_izvestaja ?: '—' }}</td>
                                <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-900">{{ $dok->broj_dokumenta }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $dok->datum_predaje?->format('d.m.Y.') }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $dok->indeksni_broj }}</td>
                                <td class="px-4 py-3 text-gray-700 max-w-[160px] truncate" title="{{ $dok->vrsta_otpada }}">{{ $dok->vrsta_otpada }}</td>
                                <td class="px-4 py-3">
                                    @if ($dok->isGradjevinski())
                                        <span class="inline-flex text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full bg-amber-100 text-amber-800" title="{{ $dok->broj_gradevinske_dozvole_dko }}">Građ.</span>
                                    @else
                                        <span class="inline-flex text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">Obični</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ number_format((float) $dok->masa_ukupno, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-700 max-w-[140px] truncate">{{ $dok->primalac_naziv ?: '—' }}</td>
                                @if ($showAllTeams)
                                    <td class="px-4 py-3 text-gray-700 max-w-[140px] truncate">{{ $dok->team?->name ?? '—' }}</td>
                                @endif
                                <td class="px-4 py-3 text-center text-gray-700">{{ $dok->dnevne_evidencije_count }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if ($confirmingDeleteId === $dok->id)
                                        <div class="inline-flex items-center gap-2 text-xs">
                                            <span class="text-gray-600">Obrisati?</span>
                                            <button type="button" wire:click="delete({{ $dok->id }})" class="text-red-600 font-semibold">Da</button>
                                            <button type="button" wire:click="cancelDelete" class="text-gray-500">Ne</button>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('doko.download', $dok) }}" title="Preuzmi DOKO.xlsx"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
                                                📥 DOKO
                                            </a>
                                            <a href="{{ route('doko.deo1', $dok) }}" title="Preuzmi DEO1.xlsx"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                                                📋 DEO1
                                            </a>
                                            <button type="button" wire:click="openPregled({{ $dok->id }})" title="Pregled"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 hover:bg-gray-100">🔍</button>
                                            @if ($allowDelete)
                                                <button type="button" wire:click="confirmDelete({{ $dok->id }})" title="Obriši"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50">🗑</button>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($dokumenti->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $dokumenti->links() }}
                </div>
            @endif
        @endif
    </div>

    @if ($pregledDokument)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closePregled">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[85vh] overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900">
                            @if ($pregledDokument->broj_izvestaja)
                                <span class="font-mono text-indigo-700">{{ $pregledDokument->broj_izvestaja }}</span>
                                <span class="text-gray-400 font-normal text-sm">· {{ $pregledDokument->broj_dokumenta }}</span>
                            @else
                                {{ $pregledDokument->broj_dokumenta }}
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">Vezani dnevni izveštaji ({{ $pregledDokument->dnevneEvidencije->count() }})</p>
                    </div>
                    <button type="button" wire:click="closePregled" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <div class="overflow-y-auto p-6 space-y-3">
                    @foreach ($pregledDokument->dnevneEvidencije as $ev)
                        <div class="rounded-lg border border-gray-200 p-3 text-sm">
                            <div class="flex justify-between gap-2">
                                <span class="font-mono font-semibold">{{ $ev->indeksni_broj }}</span>
                                <span class="text-gray-600">{{ $ev->datum->format('d.m.Y.') }}</span>
                            </div>
                            <p class="text-gray-700 mt-1">{{ $ev->naziv_otpada }}</p>
                            <p class="text-xs text-gray-500 mt-1">Stanje predato: {{ \App\Models\DnevnaEvidencija::formatKolicina($ev->predata_kolicina) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex gap-2 justify-end">
                    <a href="{{ route('doko.download', $pregledDokument) }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">📥 DOKO</a>
                    <a href="{{ route('doko.deo1', $pregledDokument) }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">📋 DEO1</a>
                </div>
            </div>
        </div>
    @endif
</div>
