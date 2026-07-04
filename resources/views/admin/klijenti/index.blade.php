<x-layouts.admin title="Klijenti" header="Klijenti (Firme)">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-[#f8f9f4]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Naziv firme</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PIB</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tip</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vlasnik</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrovan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Evidencije</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($klijenti as $team)
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $team->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $team->pib ?? '—' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ $team->tip_subjekta ?? 'DEO1' }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $team->owner?->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $team->created_at->format('d.m.Y.') }}</td>
                            <td class="px-4 py-3 text-center text-gray-900 font-medium">{{ $team->evidencije_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.klijenti.show', $team) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 hover:bg-indigo-50" title="Pregled">👁</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Nema registrovanih firmi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
