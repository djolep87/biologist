<x-layouts.dashboard :title="'DEO1 unos — '.$site->naziv_gradilista">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('gradilista.deo1.index', $site) }}" class="text-sm text-gray-500 hover:text-gray-800">← DEO1 dnevnik</a>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mt-2">Pregled unosa</h2>
        </div>

        <div class="bio-section space-y-4 text-sm">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Datum</p>
                    <p class="mt-1 text-gray-900">{{ $zapis->datum?->format('d.m.Y.') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Šifra otpada</p>
                    <p class="mt-1 font-mono font-semibold text-gray-900">{{ $zapis->indeksni_broj }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Naziv</p>
                    <p class="mt-1 text-gray-900">{{ $zapis->naziv_otpada }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Količina</p>
                    <p class="mt-1 text-gray-900 font-semibold">{{ number_format((float) $zapis->proizvedena_kolicina, 3, ',', '.') }} t</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 font-semibold">Karakter</p>
                    <p class="mt-1 text-gray-900">{{ $zapis->karakter_otpada }}</p>
                </div>
                @if ($zapis->nacin_nastanka)
                    <div class="sm:col-span-2">
                        <p class="text-xs uppercase text-gray-500 font-semibold">Način nastanka</p>
                        <p class="mt-1 text-gray-900">{{ $zapis->nacin_nastanka }}</p>
                    </div>
                @endif
                @if ($zapis->napomena)
                    <div class="sm:col-span-2">
                        <p class="text-xs uppercase text-gray-500 font-semibold">Napomena</p>
                        <p class="mt-1 text-gray-900">{{ $zapis->napomena }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.dashboard>
