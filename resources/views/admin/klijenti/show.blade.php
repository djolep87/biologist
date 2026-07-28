<x-layouts.admin :title="$team->name" :header="'Klijent: ' . $team->name">
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.klijenti.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Nazad na listu klijenata</a>
        <a href="{{ route('admin.klijenti.radi', $team) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">
            📋 Rad sa ovim klijentom (DOKO / DEO1)
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900 mb-4">Podaci o firmi</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500 text-xs uppercase tracking-wide">Naziv</dt><dd class="font-medium text-gray-900 mt-0.5">{{ $team->name }}</dd></div>
                <div><dt class="text-gray-500 text-xs uppercase tracking-wide">PIB</dt><dd class="mt-0.5">{{ $team->pib ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 text-xs uppercase tracking-wide">Matični</dt><dd class="mt-0.5">{{ $team->maticni_broj ?? '—' }}</dd></div>
                <div><dt class="text-gray-500 text-xs uppercase tracking-wide">Tip subjekta</dt><dd class="mt-0.5">{{ $team->tip_subjekta ?? 'DEO1' }}</dd></div>
                <div><dt class="text-gray-500 text-xs uppercase tracking-wide">Adresa</dt><dd class="mt-0.5">{{ $team->adresa ?? '—' }}{{ $team->grad ? ', ' . $team->grad : '' }}</dd></div>
                <div><dt class="text-gray-500 text-xs uppercase tracking-wide">Vlasnik</dt><dd class="mt-0.5">{{ $team->owner?->name }}<br><span class="text-gray-500">{{ $team->owner?->email }}</span></dd></div>
            </dl>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">Poslednje evidencije</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#f8f9f4]"><tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Datum</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Indeks</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Otpad</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($evidencije as $ev)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $ev->datum->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $ev->indeksni_broj }}</td>
                                    <td class="px-4 py-3">{{ $ev->naziv_otpada }}</td>
                                    <td class="px-4 py-3">
                                        @if ($ev->predat_operateru)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Predato</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">⏳ Čeka</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Nema evidencija.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900">DOKO dokumenti</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-[#f8f9f4]"><tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Broj</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Datum</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500">Indeks</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500">Download</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($dokumenti as $dok)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 font-mono text-xs font-medium">{{ $dok->broj_dokumenta }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $dok->datum_predaje?->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $dok->indeksni_broj }}</td>
                                    <td class="px-4 py-3 text-right space-x-3">
                                        <a href="{{ route('doko.download', $dok) }}" class="text-emerald-600 hover:underline text-xs font-medium">DOKO</a>
                                        <a href="{{ route('doko.deo1', $dok) }}" class="text-indigo-600 hover:underline text-xs font-medium">DEO1</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Nema dokumenata.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <livewire:admin.dko-numeracija-settings :team="$team" :key="'dko-num-'.$team->id" />
    </div>
</x-layouts.admin>
