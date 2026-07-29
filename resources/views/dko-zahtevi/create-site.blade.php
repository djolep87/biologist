<x-layouts.dashboard title="Novi DKO zahtev — izbor gradilišta">
    <div class="space-y-6">
        <div>
            <a href="{{ route('dko-zahtevi.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← DKO Zahtevi</a>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mt-2">Korak 1 — Izaberite gradilište</h2>
            <p class="text-sm text-gray-500 mt-1">Zahtev za DKO se šalje za jedno aktivno gradilište.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        @if ($sites->isEmpty())
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 px-8 py-14 text-center">
                <p class="text-gray-500 mb-4">Nemate aktivnih gradilišta.</p>
                <a href="{{ route('gradilista.create') }}" class="bio-btn-primary inline-flex">+ Novo gradilište</a>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($sites as $site)
                    <a href="{{ route('dko-zahtevi.create', ['gradiliste_id' => $site->id]) }}"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-green-400 hover:shadow-md transition-all">
                        <h3 class="font-semibold text-gray-900">🏗️ {{ $site->naziv_gradilista }}</h3>
                        <p class="text-sm text-indigo-700 mt-2 font-medium">{{ $site->broj_gradevinske_dozvole }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $site->puna_adresa }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.dashboard>
