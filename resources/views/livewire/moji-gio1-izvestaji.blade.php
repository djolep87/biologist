<div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900">Godišnji izveštaji (GIO1)</h2>
            <p class="text-sm text-gray-500 mt-0.5">Zakonski godišnji izveštaj o otpadu proizvođača</p>
        </div>

        @if ($izvestaji->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-4xl mb-3">📊</p>
                <p class="text-gray-700 font-medium mb-1">Nemate godišnjih izveštaja.</p>
                <p class="text-sm text-gray-500 max-w-md mx-auto">
                    Administrator priprema GIO1 izveštaj za vašu firmu.
                    Rok za dostavljanje SEPA je 31. mart tekuće godine.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#f8f9f4]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Godina</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Generisan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Akcija</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($izvestaji as $izvestaj)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $izvestaj->godina }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ ($izvestaj->generisan_at ?? $izvestaj->created_at)->format('d.m.Y.') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Spreman
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('gio1.download', $izvestaj) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                                        ⬇ GIO1 {{ $izvestaj->godina }}.xlsx
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
