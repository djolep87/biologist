<x-layouts.dashboard title="Evidencija otpada — Excel">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900">
                Dnevna evidencija (Obrazac DEO 1)
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Preuzmite ili štampajte popunjeni zvanični Excel obrazac po godini, mesecu i indeksnom broju.
            </p>
        </div>

        @if ($groups->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">Nema grupa evidencija za izvoz.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Godina</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Mesec</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Naziv otpada</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Indeksni broj</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($groups as $group)
                            @php
                                $params = [
                                    'godina' => $group->godina,
                                    'mesec' => $group->mesec,
                                    'indeksni_broj' => $group->indeksni_broj,
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 text-gray-900">{{ $group->godina }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ str_pad($group->mesec, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $group->naziv_otpada }}</td>
                                <td class="px-4 py-3 text-gray-700 font-mono text-xs">{{ $group->indeksni_broj }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                    <a
                                        href="{{ route('evidencija.export', $params) }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-medium hover:bg-green-700 transition-colors"
                                    >
                                        Preuzmi Excel
                                    </a>
                                    <a
                                        href="{{ route('evidencija.print', $params) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-50 transition-colors"
                                    >
                                        Štampaj
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.dashboard>
