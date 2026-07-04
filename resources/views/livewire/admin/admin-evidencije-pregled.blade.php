<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-5 border-b border-gray-100">
        <div>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900">Dnevna evidencija</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pregled svih unosa — uključujući predati otpad. Preuzmite popunjene DEO1 obrasce.</p>
        </div>
        @if ($teamId && $evidencije->total() > 0)
            <div class="flex flex-wrap gap-2">
                @if ($filterMesec !== '' && $filterIndeks !== '')
                    <a href="{{ route('admin.export.deo1', ['godina' => $filterGodina, 'mesec' => (int) $filterMesec, 'indeksni_broj' => $filterIndeks]) }}"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg">
                        📋 DEO1 Excel (mesec + indeks)
                    </a>
                @endif
            </div>
        @endif
    </div>

    <div class="px-6 py-4 bg-[#f8f9f4] border-b border-gray-100 flex flex-wrap gap-3">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pretraži indeks, naziv..."
            class="flex-1 min-w-[160px] rounded-lg border-gray-300 text-sm" />
        <select wire:model.live="filterGodina" class="rounded-lg border-gray-300 text-sm">
            @for ($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
        <select wire:model.live="filterMesec" class="rounded-lg border-gray-300 text-sm">
            <option value="">Svi meseci</option>
            @foreach (['Januar','Februar','Mart','April','Maj','Jun','Jul','Avgust','Septembar','Oktobar','Novembar','Decembar'] as $i => $m)
                <option value="{{ $i + 1 }}">{{ $m }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterIndeks" class="rounded-lg border-gray-300 text-sm min-w-[130px]">
            <option value="">Svi indeksi</option>
            @foreach ($indeksOptions as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="rounded-lg border-gray-300 text-sm">
            <option value="">Svi statusi</option>
            <option value="predato">✅ Predato</option>
            <option value="ceka">⏳ Čeka predaju</option>
        </select>
    </div>

    @if ($evidencije->isEmpty())
        <div class="px-6 py-12 text-center text-gray-500 text-sm">Nema evidencija za izabrane filtere.</div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#f8f9f4]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Datum</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Indeks</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Otpad</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Proiz.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Pred.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Stanje</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Preuzimanje</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($evidencije as $row)
                        <tr class="hover:bg-gray-50/80" wire:key="admin-ev-{{ $row->id }}">
                            <td class="px-4 py-3 whitespace-nowrap">{{ $row->datum->format('d.m.Y.') }}</td>
                            <td class="px-4 py-3 font-mono text-xs font-semibold">{{ $row->indeksni_broj }}</td>
                            <td class="px-4 py-3 max-w-[160px] truncate">{{ $row->naziv_otpada }}</td>
                            <td class="px-4 py-3">{{ \App\Models\DnevnaEvidencija::formatKolicina($row->proizvedena_kolicina) }}</td>
                            <td class="px-4 py-3">{{ \App\Models\DnevnaEvidencija::formatKolicina($row->predata_kolicina) }}</td>
                            <td class="px-4 py-3">{{ \App\Models\DnevnaEvidencija::formatKolicina($row->stanje_na_skladistu) }}</td>
                            <td class="px-4 py-3">
                                @if ($row->predat_operateru)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Predato</span>
                                    @if ($row->dokumentKretanja)
                                        <div class="text-xs text-gray-400 mt-1">{{ $row->dokumentKretanja->broj_dokumenta }}</div>
                                    @endif
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">⏳ Čeka</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex flex-wrap justify-end gap-1">
                                    <a href="{{ route('admin.export.deo1-record', $row->id) }}" title="DEO1 Excel (ceo mesec za indeks)"
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100">Excel</a>
                                    @if ($row->dokumentKretanja)
                                        <a href="{{ route('doko.deo1', $row->dokumentKretanja) }}" title="DEO1 iz DOKO dokumenta"
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">DEO1 DOKO</a>
                                        <a href="{{ route('doko.download', $row->dokumentKretanja) }}" title="DOKO Excel"
                                            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100">DOKO</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($evidencije->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $evidencije->links() }}</div>
        @endif
    @endif
</div>
