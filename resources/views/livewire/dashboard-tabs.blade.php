<div>
    <div class="flex border-b border-gray-200 mb-6">
        <button type="button" wire:click="setTab('evidencija')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors
                {{ $activeTab === 'evidencija' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            📝 Dnevna Evidencija
        </button>
        <button type="button" wire:click="setTab('dokumenti')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors
                {{ $activeTab === 'dokumenti' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            🚛 Dokumenti Kretanja
        </button>
        <button type="button" wire:click="setTab('zahtevi')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors
                {{ $activeTab === 'zahtevi' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            📨 Moji zahtevi
            @if ($aktivniZahteviCount > 0)
                <span class="ml-2 px-2 py-0.5 text-xs bg-amber-100 text-amber-800 rounded-full">
                    {{ $aktivniZahteviCount }}
                </span>
            @endif
        </button>
        <button type="button" wire:click="setTab('gio1')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors
                {{ $activeTab === 'gio1' ? 'border-green-600 text-green-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            📊 GIO1
        </button>
    </div>

    @if ($activeTab === 'evidencija')
        <livewire:dnevna-evidencija-table />
    @elseif ($activeTab === 'dokumenti')
        <livewire:dokument-kretanja-table />
    @elseif ($activeTab === 'zahtevi')
        <livewire:moji-zahtevi />
    @else
        <livewire:moji-gio1-izvestaji />
    @endif

    <livewire:posalji-zahtev-predaje />
</div>
