<x-layouts.dashboard title="Moja gradilišta">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900">Građevinski otpad</h2>
                <p class="text-sm text-gray-500 mt-1">Evidencija po gradilištu — DEO1 i DKO vezani za građevinsku dozvolu.</p>
            </div>
            <a href="{{ route('gradilista.create') }}" class="bio-btn-primary inline-flex items-center gap-2 self-start">
                <span>+</span> Novo gradilište
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap gap-2">
            @php
                $filters = ['' => 'Sva', 'aktivno' => 'Aktivna', 'završeno' => 'Završena', 'pauzirano' => 'Pauzirana'];
            @endphp
            @foreach ($filters as $value => $label)
                <a href="{{ route('gradilista.index', array_filter(['status' => $value ?: null])) }}"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ ($filter ?: '') === $value ? 'bg-[#1e2430] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($sites->isEmpty())
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 px-8 py-16 text-center">
                <div class="text-4xl mb-4">🏗️</div>
                <h3 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-semibold text-gray-900 mb-2">Nema gradilišta</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Dodajte prvo gradilište da biste vodili odvojenu evidenciju građevinskog otpada sa brojem građevinske dozvole.</p>
                <a href="{{ route('gradilista.create') }}" class="bio-btn-primary inline-flex items-center gap-2">+ Novo gradilište</a>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($sites as $site)
                    @php
                        $badge = match ($site->status_badge_color) {
                            'green' => 'bg-green-100 text-green-800',
                            'orange' => 'bg-orange-100 text-orange-800',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-gray-900 leading-snug">
                                🏗️ {{ $site->naziv_gradilista }}
                            </h3>
                            <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full {{ $badge }}">{{ $site->status_label }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Adresa: {{ $site->puna_adresa }}</p>
                        <p class="text-sm font-medium text-indigo-700 mb-3">Dozvola: {{ $site->broj_gradevinske_dozvole }}</p>
                        <p class="text-sm text-gray-800 mb-1">Otpad unesen: <strong>{{ number_format((float) ($site->otpad_unesen ?? 0), 2, ',', '.') }} t</strong></p>
                        <p class="text-sm text-gray-500 mb-5">DEO1 zapisa: {{ $site->dnevne_evidencije_count }} &nbsp;|&nbsp; DKO dokumenata: {{ $site->dokumenti_kretanja_count }}</p>
                        <div class="mt-auto flex flex-wrap gap-2">
                            <a href="{{ route('gradilista.show', $site) }}" class="bio-btn-secondary text-sm px-3 py-2">Otvori</a>
                            <a href="{{ route('gradilista.deo1.index', $site) }}" class="bio-btn-primary text-sm px-3 py-2">DEO1 Dnevnik</a>
                            <a href="{{ route('gradilista.edit', $site) }}" class="text-sm px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">Uredi</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.dashboard>
