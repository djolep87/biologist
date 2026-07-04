<x-form-section submit="updateTeamCompany">
    <x-slot name="title">
        Podaci o firmi
    </x-slot>

    <x-slot name="description">
        Ovi podaci se automatski koriste pri popunjavanju DOKO dokumenata. Ažurirajte ih kada se promene dozvole ili kontakt podaci.
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="Naziv firme *" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="name" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="pib" value="PIB *" />
            <x-input id="pib" type="text" class="mt-1 block w-full" wire:model="state.pib" maxlength="9" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="pib" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="maticni_broj" value="Matični broj *" />
            <x-input id="maticni_broj" type="text" class="mt-1 block w-full" wire:model="state.maticni_broj" maxlength="8" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="maticni_broj" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="adresa" value="Adresa *" />
            <x-input id="adresa" type="text" class="mt-1 block w-full" wire:model="state.adresa" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="adresa" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="grad" value="Grad *" />
            <x-input id="grad" type="text" class="mt-1 block w-full" wire:model="state.grad" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="grad" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="opstina" value="Opština" />
            <x-input id="opstina" type="text" class="mt-1 block w-full" wire:model="state.opstina" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="opstina" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-3">
            <x-label for="postanski_broj" value="Poštanski broj *" />
            <x-input id="postanski_broj" type="text" class="mt-1 block w-full" wire:model="state.postanski_broj" maxlength="5" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="postanski_broj" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="tip_subjekta" value="Tip subjekta *" />
            <select id="tip_subjekta" wire:model="state.tip_subjekta" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" @disabled(! Gate::check('update', $team))>
                @foreach (['DEO1' => 'DEO1 - Proizvođač', 'DEO2' => 'DEO2 - Operater deponije', 'DEO3' => 'DEO3 - Ponovna upotreba', 'DEO4' => 'DEO4 - Izvoznik', 'DEO5' => 'DEO5 - Uvoznik', 'DEO6' => 'DEO6 - Sakupljač'] as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error for="tip_subjekta" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="kontakt_telefon" value="Kontakt telefon" />
            <x-input id="kontakt_telefon" type="tel" class="mt-1 block w-full" wire:model="state.kontakt_telefon" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="kontakt_telefon" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="Email" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="email" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="kontakt_osoba" value="Odgovorno lice" />
            <x-input id="kontakt_osoba" type="text" class="mt-1 block w-full" wire:model="state.kontakt_osoba" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="kontakt_osoba" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6">
            <p class="text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2">Dozvola za upravljanje otpadom</p>
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="dozvola_broj" value="Broj dozvole" />
            <x-input id="dozvola_broj" type="text" class="mt-1 block w-full" wire:model="state.dozvola_broj" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="dozvola_broj" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="dozvola_datum_izdavanja" value="Datum izdavanja" />
            <x-input id="dozvola_datum_izdavanja" type="date" class="mt-1 block w-full" wire:model="state.dozvola_datum_izdavanja" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="dozvola_datum_izdavanja" class="mt-2" bag="updateTeamCompany" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="dozvola_vazi_do" value="Važi do" />
            <x-input id="dozvola_vazi_do" type="date" class="mt-1 block w-full" wire:model="state.dozvola_vazi_do" :disabled="! Gate::check('update', $team)" />
            <x-input-error for="dozvola_vazi_do" class="mt-2" bag="updateTeamCompany" />
        </div>
    </x-slot>

    @if (Gate::check('update', $team))
        <x-slot name="actions">
            <x-action-message class="me-3" on="saved">Sačuvano.</x-action-message>
            <x-button>Sačuvaj</x-button>
        </x-slot>
    @endif
</x-form-section>
