@if (\App\Support\TeamAccess::isEvidenciarOnly(auth()->user()))
    <x-layouts.evidenciar title="Dnevna evidencija">
        <livewire:dnevna-evidencija-table :evidenciar-mode="true" />
    </x-layouts.evidenciar>
@else
    <x-layouts.dashboard title="Pregled">
        <livewire:dashboard-tabs />
    </x-layouts.dashboard>
@endif
