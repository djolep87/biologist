<x-layouts.dashboard :title="'Novi DKO zahtev — '.$site->naziv_gradilista">
    <div class="max-w-4xl mx-auto space-y-6"
        x-data="{
            selected: @js(old('deo1_zapisi', [])),
            zapisi: @js($zapisi->map(fn($z) => [
                'id' => $z->id,
                'slobodan' => $z->dko_status === 'slobodan',
                'kolicina' => (float) ($z->stanje_na_skladistu ?: $z->proizvedena_kolicina),
                'sifra' => $z->indeksni_broj,
            ])->values()),
            get selektovani() {
                return this.zapisi.filter(z => this.selected.map(Number).includes(z.id) && z.slobodan);
            },
            get ukupno() {
                return this.selektovani.reduce((s, z) => s + z.kolicina, 0);
            },
            get vrste() {
                return [...new Set(this.selektovani.map(z => z.sifra))].join(', ');
            },
            toggleAll() {
                const free = this.zapisi.filter(z => z.slobodan).map(z => z.id);
                this.selected = this.selected.length === free.length ? [] : free;
            }
        }">
        <div>
            <a href="{{ route('dko-zahtevi.create') }}" class="text-sm text-gray-500 hover:text-gray-800">← Izbor gradilišta</a>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mt-2">Korak 2 — Selekcija DEO1 zapisa</h2>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900">🏗️ {{ $site->naziv_gradilista }}</h3>
            <p class="text-sm text-indigo-700 font-medium mt-1">Dozvola: {{ $site->broj_gradevinske_dozvole }}</p>
            <p class="text-sm text-gray-600 mt-1">Adresa: {{ $site->puna_adresa }}</p>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
            Selektujte DEO1 zapise koje želite da operater preuzme. Na osnovu vaše selekcije, administrator će generisati DKO dokument.
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('dko-zahtevi.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="construction_site_id" value="{{ $site->id }}">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                @if ($zapisi->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500 text-sm">
                        Nema dostupnih DEO1 zapisa za ovo gradilište.
                        <a href="{{ route('gradilista.deo1.create', $site) }}" class="text-green-700 font-medium hover:underline">Dodaj unos</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 w-10">
                                        <input type="checkbox" @click="toggleAll()" class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                            :checked="selektovani.length > 0 && selektovani.length === zapisi.filter(z => z.slobodan).length">
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Datum</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Vrsta otpada</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Količina</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($zapisi as $zapis)
                                    @php $slobodan = $zapis->dko_status === 'slobodan'; @endphp
                                    <tr class="{{ $slobodan ? 'hover:bg-gray-50' : 'bg-gray-50 opacity-70' }}">
                                        <td class="px-4 py-3">
                                            @if ($slobodan)
                                                <input type="checkbox" name="deo1_zapisi[]" value="{{ $zapis->id }}"
                                                    x-model="selected" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                            @else
                                                <input type="checkbox" disabled class="rounded border-gray-300">
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $zapis->datum?->format('d.m.Y.') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="font-mono text-xs">{{ $zapis->indeksni_broj }}</span>
                                            – {{ $zapis->naziv_otpada }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium">
                                            {{ number_format((float) ($zapis->stanje_na_skladistu ?: $zapis->proizvedena_kolicina), 3, ',', '.') }} t
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($slobodan)
                                                <span class="text-xs font-medium text-green-700">✅ Slo</span>
                                            @else
                                                <span class="text-xs font-medium text-amber-700">U zahtevu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white px-4 py-4 text-sm">
                <p>Selektovano zapisa: <strong x-text="selektovani.length"></strong></p>
                <p class="mt-1">Ukupna količina: <strong x-text="ukupno.toFixed(3).replace('.', ',')"></strong> t</p>
                <p class="mt-1">Vrste otpada: <strong x-text="vrste || '—'"></strong></p>
            </div>

            <div class="bio-section">
                <label class="bio-label">Napomena za administratora (opciono)</label>
                <textarea name="napomena_klijenta" rows="3" class="bio-input" placeholder='npr. "Hitno, gradilište se zatvara 01.08."'>{{ old('napomena_klijenta') }}</textarea>
            </div>

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('dko-zahtevi.index') }}" class="bio-btn-secondary">Otkaži</a>
                <button type="submit" class="bio-btn-primary" :disabled="selektovani.length === 0"
                    :class="selektovani.length === 0 && 'opacity-50 cursor-not-allowed'">
                    Pošalji adminu za DKO →
                </button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
