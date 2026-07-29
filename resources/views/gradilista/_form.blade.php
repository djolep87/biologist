@php
    $isEdit = isset($site);
    $action = $isEdit ? route('gradilista.update', $site) : route('gradilista.store');
    $old = fn (string $key, $default = '') => old($key, $isEdit ? ($site->{$key} ?? $default) : $default);
@endphp

@if ($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ $action }}" class="space-y-6" x-data="procenaKalkulator()">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <section class="bio-section space-y-4">
        <h3 class="bio-section-title">A — Osnovni podaci</h3>
        <div>
            <label class="bio-label">Naziv gradilišta / projekta *</label>
            <input type="text" name="naziv_gradilista" value="{{ $old('naziv_gradilista') }}" required class="bio-input" placeholder="npr. Stambena zgrada Lamela A">
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="bio-label">Tip radova *</label>
                <select name="tip_radova" x-model="tipRadova" required class="bio-input">
                    <option value="">Izaberite…</option>
                    <option value="rusenje" @selected($old('tip_radova') === 'rusenje')>Rušenje</option>
                    <option value="gradnja" @selected($old('tip_radova') === 'gradnja')>Gradnja</option>
                    <option value="rekonstrukcija" @selected($old('tip_radova') === 'rekonstrukcija')>Rekonstrukcija</option>
                    <option value="sanacija" @selected($old('tip_radova') === 'sanacija')>Sanacija</option>
                </select>
            </div>
            <div>
                <label class="bio-label">Status</label>
                <select name="status" class="bio-input" @disabled($isEdit && $site->isZavrseno())>
                    <option value="aktivno" @selected($old('status', 'aktivno') === 'aktivno')>Aktivno</option>
                    <option value="pauzirano" @selected($old('status') === 'pauzirano')>Pauzirano</option>
                </select>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="bio-label">Datum početka</label>
                <input type="date" name="datum_pocetka" value="{{ $old('datum_pocetka', $isEdit ? $site->datum_pocetka?->format('Y-m-d') : '') }}" class="bio-input">
            </div>
            <div>
                <label class="bio-label">Planirani završetak</label>
                <input type="date" name="planirani_zavrsetak" value="{{ $old('planirani_zavrsetak', $isEdit ? $site->planirani_zavrsetak?->format('Y-m-d') : '') }}" class="bio-input">
            </div>
        </div>
    </section>

    <section class="bio-section space-y-4">
        <h3 class="bio-section-title">B — Lokacija i dozvola</h3>
        <div>
            <label class="bio-label">Adresa gradilišta *</label>
            <input type="text" name="adresa_gradilista" value="{{ $old('adresa_gradilista') }}" required class="bio-input">
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="bio-label">Mesto *</label>
                <input type="text" name="mesto" value="{{ $old('mesto') }}" required class="bio-input">
            </div>
            <div>
                <label class="bio-label">Opština *</label>
                <input type="text" name="opstina" value="{{ $old('opstina') }}" required class="bio-input">
            </div>
        </div>
        <div>
            <label class="bio-label">Katastarska parcela</label>
            <input type="text" name="katastarska_parcela" value="{{ $old('katastarska_parcela') }}" class="bio-input">
        </div>
        <div>
            <label class="bio-label">Broj građevinske dozvole *</label>
            <input type="text" name="broj_gradevinske_dozvole" value="{{ $old('broj_gradevinske_dozvole') }}" required class="bio-input" placeholder="ROP-BG-XXXXX-CPI-X/2026">
            <p class="mt-1 text-xs text-gray-500">Ovaj broj se automatski dodaje na svaki DKO obrazac generisan sa ovog gradilišta.</p>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="bio-label">Investitor naziv</label>
                <input type="text" name="investitor_naziv" value="{{ $old('investitor_naziv') }}" class="bio-input" placeholder="Ako se razlikuje od firme">
            </div>
            <div>
                <label class="bio-label">Izvođač naziv</label>
                <input type="text" name="izvodjac_naziv" value="{{ $old('izvodjac_naziv') }}" class="bio-input" placeholder="Ako se razlikuje od firme">
            </div>
        </div>
    </section>

    <section class="bio-section space-y-4">
        <h3 class="bio-section-title">📊 Procena količine otpada (opciono)</h3>
        <p class="text-sm text-gray-500">Popunite kvadraturu i sistem će automatski proceniti količinu otpada.</p>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="bio-label">Kvadratura objekta (m²)</label>
                <input type="number" step="0.01" min="1" name="kvadratura_objekta" x-model="kvadratura" value="{{ $old('kvadratura_objekta') }}" class="bio-input">
            </div>
            <div>
                <label class="bio-label">Tip radova za kalkulator</label>
                <input type="text" :value="tipLabel" readonly class="bio-input bg-gray-50">
            </div>
        </div>
        <button type="button" @click="izracunaj()" class="bio-btn-accent text-sm">IZRAČUNAJ PROCENU</button>
        <input type="hidden" name="procijenjena_kolicina_otpada" :value="procena ?? ''">

        <div x-show="prikaziRezultat" x-cloak class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">
            <p class="font-semibold text-base">Procenjena količina otpada: ~<span x-text="procenaFormatted"></span> t</p>
            <p class="mt-1" x-text="objasnjenje"></p>
            <p class="mt-2 text-amber-800/80">Ova procena se čuva uz gradilište kao referentna vrednost za Plan upravljanja.</p>
        </div>
    </section>

    <section class="bio-section space-y-4">
        <h3 class="bio-section-title">Operater za odvoz (opciono)</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="bio-label">Operater naziv</label>
                <input type="text" name="operater_naziv" value="{{ $old('operater_naziv') }}" class="bio-input">
            </div>
            <div>
                <label class="bio-label">Operater PIB</label>
                <input type="text" name="operater_pib" value="{{ $old('operater_pib') }}" class="bio-input">
            </div>
            <div>
                <label class="bio-label">Operater adresa</label>
                <input type="text" name="operater_adresa" value="{{ $old('operater_adresa') }}" class="bio-input">
            </div>
            <div>
                <label class="bio-label">Operater dozvola broj</label>
                <input type="text" name="operater_dozvola_broj" value="{{ $old('operater_dozvola_broj') }}" class="bio-input">
            </div>
        </div>
    </section>

    <div class="flex items-center gap-3">
        <button type="submit" class="bio-btn-primary">{{ $isEdit ? 'Sačuvaj izmene' : 'Kreiraj gradilište' }}</button>
        <a href="{{ $isEdit ? route('gradilista.show', $site) : route('gradilista.index') }}" class="bio-btn-secondary">Otkaži</a>
    </div>
</form>

<script>
    function procenaKalkulator() {
        const faktori = { rusenje: 0.45, gradnja: 0.30, rekonstrukcija: 0.35, sanacija: 0.20 };
        const labels = { rusenje: 'Rušenje', gradnja: 'Gradnja', rekonstrukcija: 'Rekonstrukcija', sanacija: 'Sanacija' };
        return {
            kvadratura: @json((string) $old('kvadratura_objekta')),
            tipRadova: @json((string) $old('tip_radova')),
            procena: @json($old('procijenjena_kolicina_otpada') !== '' && $old('procijenjena_kolicina_otpada') !== null ? (float) $old('procijenjena_kolicina_otpada') : null),
            prikaziRezultat: @json((bool) $old('procijenjena_kolicina_otpada')),
            get tipLabel() {
                return labels[this.tipRadova] || '— (izaberite tip radova iznad)';
            },
            get procenaFormatted() {
                return this.procena !== null ? Number(this.procena).toFixed(2) : '—';
            },
            get objasnjenje() {
                const f = faktori[this.tipRadova];
                if (!f || !this.kvadratura) return '';
                return `(osnova: ${this.kvadratura} m² × ${f} t/m² za ${labels[this.tipRadova].toLowerCase()})`;
            },
            izracunaj() {
                const f = faktori[this.tipRadova];
                const m2 = parseFloat(this.kvadratura);
                if (!f || !m2 || m2 <= 0) {
                    alert('Unesite kvadraturu i tip radova.');
                    return;
                }
                this.procena = Math.round(m2 * f * 1000) / 1000;
                this.prikaziRezultat = true;
            }
        }
    }
</script>
