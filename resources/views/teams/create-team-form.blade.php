<div>
    <x-form-section submit="createTeam">
        <x-slot name="title">
            Podaci o firmi
        </x-slot>

        <x-slot name="description">
            Unesite zakonske podatke vaše firme za evidenciju otpada.
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <x-label for="name" value="Naziv firme *" />
                <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" autofocus />
                <x-input-error for="name" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="pib" value="PIB *" />
                <x-input id="pib" type="text" class="mt-1 block w-full" wire:model="state.pib" maxlength="9" inputmode="numeric" placeholder="123456789" />
                <x-input-error for="pib" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="maticni_broj" value="Matični broj *" />
                <x-input id="maticni_broj" type="text" class="mt-1 block w-full" wire:model="state.maticni_broj" maxlength="8" inputmode="numeric" />
                <x-input-error for="maticni_broj" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="adresa" value="Adresa *" />
                <x-input id="adresa" type="text" class="mt-1 block w-full" wire:model="state.adresa" />
                <x-input-error for="adresa" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-label for="grad" value="Grad *" />
                <x-input id="grad" type="text" class="mt-1 block w-full" wire:model="state.grad" />
                <x-input-error for="grad" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-label for="opstina" value="Opština" />
                <x-input id="opstina" type="text" class="mt-1 block w-full" wire:model="state.opstina" />
                <x-input-error for="opstina" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-label for="postanski_broj" value="Poštanski broj *" />
                <x-input id="postanski_broj" type="text" class="mt-1 block w-full" wire:model="state.postanski_broj" maxlength="5" inputmode="numeric" />
                <x-input-error for="postanski_broj" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="flex items-center gap-2">
                    <x-label for="tip_subjekta" value="Tip subjekta *" />
                    <span class="text-gray-400 cursor-help" title="Određuje koji zakonski obrazac popunjavate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <select id="tip_subjekta" wire:model="state.tip_subjekta" class="mt-1 block w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                    <option value="DEO1">DEO1 - Proizvođač otpada</option>
                    <option value="DEO2">DEO2 - Operater deponije</option>
                    <option value="DEO3">DEO3 - Operater postrojenja za ponovnu upotrebu</option>
                    <option value="DEO4">DEO4 - Izvoznik otpada</option>
                    <option value="DEO5">DEO5 - Uvoznik otpada</option>
                    <option value="DEO6">DEO6 - Sakupljač / vlasnik otpada</option>
                </select>
                <x-input-error for="tip_subjekta" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="delatnost" value="Delatnost (šifra)" />
                <x-input id="delatnost" type="text" class="mt-1 block w-full" wire:model="state.delatnost" maxlength="4" />
                <x-input-error for="delatnost" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="kontakt_telefon" value="Kontakt telefon" />
                <x-input id="kontakt_telefon" type="tel" class="mt-1 block w-full" wire:model="state.kontakt_telefon" />
                <x-input-error for="kontakt_telefon" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="email" value="Email firme" />
                <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" />
                <x-input-error for="email" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="kontakt_osoba" value="Odgovorno lice" />
                <x-input id="kontakt_osoba" type="text" class="mt-1 block w-full" wire:model="state.kontakt_osoba" />
                <x-input-error for="kontakt_osoba" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6">
                <p class="text-sm font-semibold text-gray-700 border-b border-gray-200 pb-2 mb-2">Dozvola za upravljanje otpadom</p>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="dozvola_broj" value="Broj dozvole" />
                <x-input id="dozvola_broj" type="text" class="mt-1 block w-full" wire:model="state.dozvola_broj" />
                <x-input-error for="dozvola_broj" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="dozvola_datum_izdavanja" value="Datum izdavanja dozvole" />
                <x-input id="dozvola_datum_izdavanja" type="date" class="mt-1 block w-full" wire:model="state.dozvola_datum_izdavanja" />
                <x-input-error for="dozvola_datum_izdavanja" class="mt-2" bag="createTeam" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-label for="dozvola_vazi_do" value="Dozvola važi do" />
                <x-input id="dozvola_vazi_do" type="date" class="mt-1 block w-full" wire:model="state.dozvola_vazi_do" />
                <x-input-error for="dozvola_vazi_do" class="mt-2" bag="createTeam" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-button class="bg-green-500 hover:bg-green-600">
                Dodaj firmu
            </x-button>
        </x-slot>
    </x-form-section>
</div>
