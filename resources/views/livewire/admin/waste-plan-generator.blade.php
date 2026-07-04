<div>
    @if ($errorMessage)
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            {{ $errorMessage }}
        </div>
    @endif

    @if ($successMessage)
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ $successMessage }}
        </div>
    @endif

    @if ($warningMessage)
        <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
            {{ $warningMessage }}
        </div>
    @endif

    {{-- LOADING OVERLAY --}}
    @if ($generating)
        <div class="fixed inset-0 z-[70] bg-black/40 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8">
                <h3 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900 mb-6 text-center">
                    Generisanje plana u toku
                </h3>
                <div class="space-y-4 mb-6">
                    @php
                        $steps = [
                            1 => 'Korak 1/4: Priprema podataka o firmi...',
                            2 => 'Korak 2/4: Generisanje plana (ovo može trajati 45-90 sekundi)...',
                            3 => 'Korak 3/4: Formatiranje dokumenta...',
                            4 => 'Korak 4/4: Priprema PDF-a...',
                        ];
                    @endphp
                    @foreach ($steps as $num => $label)
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $generationStep >= $num ? 'bg-emerald-500 text-white' : ($generationStep + 1 === $num ? 'bg-indigo-500 text-white animate-pulse' : 'bg-gray-200 text-gray-500') }}">
                                @if ($generationStep >= $num) ✓ @else {{ $num }} @endif
                            </span>
                            <span class="text-sm {{ $generationStep >= $num ? 'text-emerald-700 font-medium' : 'text-gray-600' }}">
                                {{ $label }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                        style="width: {{ max(10, ($generationStep / 4) * 100) }}%"></div>
                </div>
            </div>
        </div>
    @endif

  @if (! $showPreview)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900 mb-1">
            Novi plan upravljanja otpadom
        </h2>
        <p class="text-sm text-gray-500 mb-6">Popunite podatke o firmi i otpadu. Polja označena sa * su obavezna.</p>

        {{-- PODACI O FIRMI --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-indigo-600 mb-4 pb-2 border-b border-indigo-100">
                Podaci o firmi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Naziv firme *</label>
                    <input type="text" wire:model.live="naziv_firme"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PIB *</label>
                    <input type="text" wire:model.live="pib"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matični broj *</label>
                    <input type="text" wire:model.live="maticni_broj"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresa sedišta *</label>
                    <input type="text" wire:model.live="adresa_sedista"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mesto i opština *</label>
                    <input type="text" wire:model.live="mesto_opstina"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Odgovorno lice / Direktor *</label>
                    <input type="text" wire:model.live="odgovorno_lice"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontakt osoba za zaštitu životne sredine</label>
                    <input type="text" wire:model="kontakt_eko"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon i email</label>
                    <input type="text" wire:model="telefon_email"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pretežna delatnost / šifra delatnosti *</label>
                    <input type="text" wire:model.live="delatnost"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Broj zaposlenih</label>
                    <input type="number" wire:model="broj_zaposlenih" min="0"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- PODACI O OTPADU --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-indigo-600 mb-4 pb-2 border-b border-indigo-100">
                Podaci o otpadu
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vrste otpada koje firma generiše</label>
                    <textarea wire:model="vrste_otpada" rows="3" placeholder="npr. ambalažni otpad, elektronski otpad, opasni otpad – ulja"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Indeksni/katalog brojevi otpada po evropskoj listi</label>
                    <textarea wire:model="indeksni_brojevi" rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Procenjena godišnja količina po vrsti otpada</label>
                    <textarea wire:model="procenjene_kolicine" rows="3" placeholder="npr. ambalažni: 2t, elektronski: 0.5t"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Način trenutnog postupanja sa otpadom</label>
                    <textarea wire:model="nacin_postupanja" rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postojeći ugovori sa operaterima (opciono)</label>
                    <textarea wire:model="ugovori_operateri" rows="2"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>
        </div>

        {{-- LOKACIJA --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-indigo-600 mb-4 pb-2 border-b border-indigo-100">
                Lokacija i infrastruktura
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mesto nastanka otpada – lokacija/pogon</label>
                    <input type="text" wire:model="lokacija_pogon"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Površina objekta / pogona</label>
                    <input type="text" wire:model="povrsina_objekta" placeholder="npr. 2000 m²"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="mt-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <button type="button" wire:click="$toggle('ima_skladiste')"
                        class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition-colors duration-200 {{ $ima_skladiste ? 'bg-indigo-600' : 'bg-gray-200' }}">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200 {{ $ima_skladiste ? 'translate-x-5' : 'translate-x-0.5' }} mt-0.5"></span>
                    </button>
                    <span class="text-sm font-medium text-gray-700">Da li postoji interno skladište otpada?</span>
                </label>
            </div>
            @if ($ima_skladiste)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opis internog skladišta</label>
                    <textarea wire:model="opis_skladista" rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            @endif
        </div>

        {{-- PRAVNI OKVIR --}}
        <div class="mb-8">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-indigo-600 mb-4 pb-2 border-b border-indigo-100">
                Pravni okvir i ciljevi
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relevantni zakoni i propisi</label>
                    <textarea wire:model="zakoni_propisi" rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rok važenja plana *</label>
                    <select wire:model.live="rok_vazenja"
                        class="w-full md:w-1/2 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1 godina">1 godina</option>
                        <option value="2 godine">2 godine</option>
                        <option value="3 godine">3 godine</option>
                        <option value="5 godina">5 godina</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciljevi smanjenja otpada</label>
                    <textarea wire:model="ciljevi_smanjenja" rows="3" placeholder="npr. smanjiti količinu za 20% u narednoj godini"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Posebni zahtevi ili napomene (opciono)</label>
                    <textarea wire:model="posebne_napomene" rows="2"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100">
            <button type="button"
                wire:click="generatePlan"
                wire:loading.attr="disabled"
                @disabled(! $this->canGenerate || $generating)
                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                📄 Generiši plan
            </button>
            @unless ($this->canGenerate)
                <p class="text-sm text-gray-500">Popunite sva obavezna polja (*) da biste generisali plan.</p>
            @endunless
        </div>
    </div>
  @endif

    {{-- FULL-SCREEN PREVIEW --}}
    @if ($showPreview)
        <div class="fixed inset-0 z-50 bg-[#f0f2eb] flex flex-col" x-data x-cloak>
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3 shadow-sm">
                <div>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900">
                        {{ $naziv_firme }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Plan upravljanja otpadom — pregled
                        @if ($planWordCount > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                {{ number_format($planWordCount, 0, ',', '.') }} reči
                            </span>
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($needsRegeneration)
                        <button type="button" wire:click="generatePlan"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600">
                            🔄 Regeneriši plan
                        </button>
                    @endif
                    @if ($currentPlanId)
                        <a href="{{ route('admin.waste-plans.pdf', $currentPlanId) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50">
                            ⬇ Preuzmi kao PDF
                        </a>
                    @endif
                    <button type="button" wire:click="savePlan"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                        💾 Sačuvaj
                    </button>
                    <button type="button" wire:click="resetForm"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        ✨ Generiši novo
                    </button>
                    <button type="button" wire:click="closePreview"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                        ✕ Zatvori
                    </button>
                </div>
            </div>

            @if ($needsRegeneration)
                <div class="bg-amber-50 border-b border-amber-200 px-4 sm:px-6 py-3 text-sm text-amber-900">
                    <strong>Upozorenje:</strong>
                    @if ($planWordCount < 5500)
                        Plan ima {{ number_format($planWordCount, 0, ',', '.') }} reči (minimum je 5500).
                    @endif
                    @if ($missingPages !== [])
                        Nedostaju strane: {{ implode(', ', $missingPages) }}.
                    @endif
                    Kliknite „Regeneriši plan” za ponovni pokušaj.
                </div>
            @endif

            <div class="flex-1 overflow-y-auto p-4 sm:p-8">
                <div class="max-w-4xl mx-auto space-y-8">
                    @foreach ($this->planPages as $page)
                        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden page-break-inside-avoid">
                            @unless ($page['is_cover'] || $page['is_toc'])
                                <div class="bg-gray-50 border-b border-gray-200 px-6 py-2.5 flex items-center justify-between text-xs text-gray-600">
                                    <span class="font-medium truncate">{{ $naziv_firme }}</span>
                                    <span>Plan upravljanja otpadom {{ now()->year }}</span>
                                    @if ($page['number'] > 0)
                                        <span>Strana {{ $page['number'] }} od 15</span>
                                    @endif
                                </div>
                            @endunless
                            <div class="px-6 sm:px-10 py-8 waste-plan-content">
                                @if ($page['is_cover'])
                                    <div class="text-center mb-8">
                                        <h1 class="text-2xl font-bold uppercase tracking-wide text-gray-900 mb-2">{{ $naziv_firme }}</h1>
                                        <p class="text-sm text-gray-600">PIB: {{ $pib }} | MB: {{ $maticni_broj }}</p>
                                        <p class="text-sm text-gray-600">{{ $adresa_sedista }}, {{ $mesto_opstina }}</p>
                                        <h2 class="text-xl font-bold uppercase mt-8 py-4 border-y-2 border-gray-800">Plan upravljanja otpadom</h2>
                                        <p class="text-sm text-gray-600 mt-4">Za period: {{ $rok_vazenja }}</p>
                                    </div>
                                @endif
                                <div class="prose prose-sm max-w-none text-gray-800 leading-relaxed
                                    [&_.section-heading]:text-base [&_.section-heading]:font-bold [&_.section-heading]:uppercase [&_.section-heading]:text-gray-900 [&_.section-heading]:mt-6 [&_.section-heading]:mb-3
                                    [&_.sub-heading]:text-sm [&_.sub-heading]:font-semibold [&_.sub-heading]:text-gray-800 [&_.sub-heading]:mt-4 [&_.sub-heading]:mb-2
                                    [&_p]:mb-3 [&_p]:text-justify
                                    [&_.data-table]:w-full [&_.data-table]:text-xs [&_.data-table]:border-collapse [&_.data-table]:my-4
                                    [&_.data-table_th]:bg-gray-200 [&_.data-table_th]:border [&_.data-table_th]:border-gray-400 [&_.data-table_th]:px-3 [&_.data-table_th]:py-2 [&_.data-table_th]:text-left
                                    [&_.data-table_td]:border [&_.data-table_td]:border-gray-300 [&_.data-table_td]:px-3 [&_.data-table_td]:py-2
                                    [&_.toc-list]:space-y-2 [&_.toc-list_li]:border-b [&_.toc-list_li]:border-dotted [&_.toc-list_li]:border-gray-300 [&_.toc-list_li]:py-2">
                                    {!! $page['content_html'] !!}
                                </div>
                            </div>
                            @unless ($page['is_cover'] || $page['is_toc'])
                                <div class="bg-gray-50 border-t border-gray-200 px-6 py-2 flex justify-between text-xs text-gray-500">
                                    <span>Poverljivo – interni dokument</span>
                                    @if ($page['number'] > 0)
                                        <span>Strana {{ $page['number'] }} od 15</span>
                                    @endif
                                </div>
                            @endunless
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ISTORIJA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900 mb-4">
            Istorija generisanih planova
        </h2>

        @if ($plans->isEmpty())
            <p class="text-sm text-gray-500 py-8 text-center">Još nema generisanih planova.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#f8f9f4]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Naziv firme</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Datum generisanja</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kreirao</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($plans as $plan)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $plan->company_name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $plan->generated_at->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $plan->createdBy?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                    <button type="button" wire:click="viewPlan({{ $plan->id }})"
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">Pregledaj</button>
                                    <a href="{{ route('admin.waste-plans.pdf', $plan) }}"
                                        class="text-gray-600 hover:text-gray-800 font-medium">Preuzmi PDF</a>
                                    <button type="button" wire:click="confirmDelete({{ $plan->id }})"
                                        class="text-red-600 hover:text-red-800 font-medium">Obriši</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $plans->links() }}</div>
        @endif
    </div>

    {{-- DELETE CONFIRM --}}
    @if ($confirmingDeleteId)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50" x-cloak>
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <h3 class="font-['Plus_Jakarta_Sans',sans-serif] text-lg font-bold text-gray-900 mb-2">Obriši plan?</h3>
                <p class="text-sm text-gray-600 mb-6">Ova akcija je nepovratna. Plan će biti trajno obrisan iz baze.</p>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Otkaži
                    </button>
                    <button type="button" wire:click="deletePlan"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Obriši
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
