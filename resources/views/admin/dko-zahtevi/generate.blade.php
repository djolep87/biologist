@php
    $site = $zahtev->constructionSite;
    $team = $zahtev->team;
@endphp

<x-layouts.admin :title="'Generiši DKO — '.$zahtev->broj_zahteva" :header="'Generisanje DKO – '.$zahtev->broj_zahteva">
    <div
        x-data
        @notify.window="setTimeout(() => {}, 0)"
        @download-file.window="window.location = $event.detail.url"
    >
        <div class="mb-6">
            <a href="{{ route('admin.dko-zahtevi.show', $zahtev) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Nazad na zahtev</a>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mt-2">Generisanje DKO – na osnovu zahteva {{ $zahtev->broj_zahteva }}</h2>
        </div>

        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900 mb-6">
            Sledeća polja su automatski popunjena podacima gradilišta i DEO1 zapisa.
            Proverite podatke u DOKO wizardu i po potrebi ispravite pre generisanja.
        </div>

        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm space-y-2">
                <h3 class="font-semibold text-gray-900 mb-3">I — Proizvođač / lokacija</h3>
                <p><span class="text-gray-500">Naziv:</span> {{ $team->name }}</p>
                <p><span class="text-gray-500">PIB:</span> {{ $team->pib }}</p>
                <p><span class="text-gray-500">Adresa sedišta:</span> {{ $team->adresa }}</p>
                <p><span class="text-gray-500">Lokacija nastanka:</span> {{ $site->puna_adresa }}</p>
                <p class="font-semibold text-indigo-700">Broj građevinske dozvole: {{ $site->broj_gradevinske_dozvole }} <span class="text-xs font-normal text-gray-500">(readonly)</span></p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-sm space-y-2">
                <h3 class="font-semibold text-gray-900 mb-3">III — Prevoznik (sa gradilišta)</h3>
                <p><span class="text-gray-500">Naziv:</span> {{ $site->operater_naziv ?: '— (unesite u wizardu)' }}</p>
                <p><span class="text-gray-500">PIB:</span> {{ $site->operater_pib ?: '—' }}</p>
                <p><span class="text-gray-500">Dozvola:</span> {{ $site->operater_dozvola_broj ?: '—' }}</p>
                <p class="text-xs text-gray-500 mt-2">Primalac (deponija/drobilica) se unosi u wizardu.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-900">II — Otpad iz zahteva</h3></div>
            <table class="min-w-full text-sm divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Indeksni broj</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Naziv otpada</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Količina (t)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($zahtev->evidencije as $ev)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $ev->indeksni_broj }}</td>
                            <td class="px-4 py-3">{{ $ev->naziv_otpada }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format((float) $ev->proizvedena_kolicina, 3, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-right font-semibold">UKUPNO:</td>
                        <td class="px-4 py-3 text-right font-bold">{{ number_format((float) $zahtev->masa_ukupno, 3, ',', '.') }} t</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.dko-zahtevi.show', $zahtev) }}" class="bio-btn-secondary">Otkaži</a>
            <button type="button"
                onclick="Livewire.dispatch('openDokoFormFromGradjevinskiZahtev', { zahtevId: {{ $zahtev->id }} })"
                class="bio-btn-primary">
                Generiši i pošalji klijentu →
            </button>
        </div>

        <livewire:kreiraj-dokument-kretanja />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                Livewire.dispatch('openDokoFormFromGradjevinskiZahtev', { zahtevId: {{ $zahtev->id }} });
            }, 300);
        });
    </script>
</x-layouts.admin>
