<div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900">Moji zahtevi za predaju</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pratite status zahteva za predaju otpada operateru</p>
            </div>
            <button type="button"
                wire:click="$dispatch('openZahtevModal')"
                class="bio-btn-accent w-full sm:w-auto shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Novi zahtev
            </button>
        </div>

        <div class="overflow-x-auto">
            @if ($zahtevi->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="text-gray-500 text-sm mb-4">Još niste poslali nijedan zahtev za predaju.</p>
                    <button type="button"
                        wire:click="$dispatch('openZahtevModal')"
                        class="bio-btn-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.25A2.25 2.25 0 0 1 4.5 6h15a2.25 2.25 0 0 1 2.25 2.25v8.25Z" />
                        </svg>
                        Pošalji prvi zahtev
                    </button>
                </div>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-[#f8f9f4]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Otpad</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Masa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Izv.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Poslato</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($zahtevi as $zahtev)
                            @php $badge = $zahtev->status_badge; @endphp
                            <tr class="hover:bg-gray-50/80" x-data="{ showRazlog: false }">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $zahtev->id }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $zahtev->naziv_otpada }}</p>
                                    <p class="text-xs font-mono text-gray-500">{{ $zahtev->indeksni_broj }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ \App\Models\DnevnaEvidencija::formatKolicina($zahtev->masa_ukupno) }}</td>
                                <td class="px-4 py-3">{{ $zahtev->evidencije->count() }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                    {{ $zahtev->created_at->format('d.m.Y.') }}<br>
                                    <span class="text-xs text-gray-400">{{ $zahtev->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                    @if ($zahtev->status === 'zavrseno' && $zahtev->dokumentKretanja)
                                        <p class="text-xs text-gray-500 mt-1 font-mono">{{ $zahtev->dokumentKretanja->broj_dokumenta }}</p>
                                    @endif
                                    @if ($zahtev->status === 'odbijeno')
                                        <button type="button" @click="showRazlog = !showRazlog"
                                            class="text-xs text-red-600 hover:underline mt-1 block">
                                            Razlog odbijanja
                                        </button>
                                        <p x-show="showRazlog" x-cloak class="text-xs text-red-700 mt-1 max-w-xs">
                                            {{ $zahtev->napomena_admina }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if ($zahtev->status === 'zavrseno' && $zahtev->dokumentKretanja)
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('doko.download', $zahtev->dokumentKretanja) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-medium">
                                                📥 DOKO
                                            </a>
                                            <a href="{{ route('doko.deo1', $zahtev->dokumentKretanja) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-medium">
                                                📋 DEO1
                                            </a>
                                        </div>
                                    @elseif ($zahtev->status === 'odbijeno')
                                        @if ($confirmingDeleteId === $zahtev->id)
                                            <div class="inline-flex flex-col items-end gap-2 text-xs">
                                                <span class="text-gray-600">Otpad se vraća na stanje.</span>
                                                <div class="flex gap-2">
                                                    <button type="button" wire:click="cancelDelete"
                                                        class="px-2.5 py-1 rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50">
                                                        Otkaži
                                                    </button>
                                                    <button type="button" wire:click="obrisiOdbijenZahtev({{ $zahtev->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="px-2.5 py-1 rounded-md bg-red-600 text-white hover:bg-red-700 font-medium">
                                                        Obriši zahtev
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <button type="button" wire:click="confirmDelete({{ $zahtev->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 hover:bg-red-100 text-xs font-medium">
                                                🗑 Obriši i oslobodi otpad
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($zahtevi->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $zahtevi->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
