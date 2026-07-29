<x-layouts.dashboard :title="'Novi DEO1 unos — '.$site->naziv_gradilista">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('gradilista.deo1.index', $site) }}" class="text-sm text-gray-500 hover:text-gray-800">← DEO1 dnevnik</a>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mt-2">Novi DEO1 unos</h2>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
            <p>Unosite podatke za gradilište: <strong>{{ $site->naziv_gradilista }}</strong></p>
            <p class="mt-1">Broj dozvole: <strong>{{ $site->broj_gradevinske_dozvole }}</strong> – automatski na DKO obrascu</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('gradilista.deo1.store', $site) }}" class="bio-section space-y-5">
            @csrf
            <input type="hidden" name="construction_site_id" value="{{ $site->id }}">

            <div>
                <label class="bio-label">Datum unosa *</label>
                <input type="date" name="datum_unosa" value="{{ old('datum_unosa', now()->toDateString()) }}" required max="{{ now()->toDateString() }}" class="bio-input">
            </div>

            <div>
                <label class="bio-label">Vrsta otpada *</label>
                <select name="katalog_sifra" required class="bio-input">
                    <option value="">Izaberite šifru…</option>
                    <optgroup label="── Najčešći građevinski otpad ──">
                        @foreach ($katalog['najcesci'] as $item)
                            <option value="{{ $item->sifra }}" @selected(old('katalog_sifra') === $item->sifra)>
                                {{ $item->sifra }} – {{ $item->naziv }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="── Ostale šifre grupe 17 ──">
                        @foreach ($katalog['ostali'] as $item)
                            <option value="{{ $item->sifra }}" @selected(old('katalog_sifra') === $item->sifra)>
                                {{ $item->sifra }} – {{ $item->naziv }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div>
                <label class="bio-label">Količina (t) *</label>
                <input type="number" name="kolicina_t" value="{{ old('kolicina_t') }}" required step="0.001" min="0.001" max="9999.999" class="bio-input" placeholder="npr. 12.450">
                <p class="mt-1 text-xs text-gray-500">Unesite količinu u tonama (t)</p>
            </div>

            <div>
                <label class="bio-label">Način nastanka otpada</label>
                <textarea name="nacin_nastanka" rows="3" class="bio-input" placeholder="npr. rušenje AB ploče na 3. spratu">{{ old('nacin_nastanka') }}</textarea>
            </div>

            <div>
                <label class="bio-label">Napomena</label>
                <textarea name="napomena" rows="3" class="bio-input">{{ old('napomena') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bio-btn-primary">SAČUVAJ UNOS</button>
                <a href="{{ route('gradilista.deo1.index', $site) }}" class="bio-btn-secondary">Otkaži</a>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
