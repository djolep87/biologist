<x-auth-split-layout title="Kreirajte nalog" subtitle="Registracija je besplatna. Počnite sa digitalnom evidencijom otpada za vašu firmu.">
    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mb-2">Registracija</h2>
    <p class="text-gray-500 text-sm mb-6">Popunite podatke za kreiranje naloga</p>

    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Ime</label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autofocus autocomplete="given-name"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Prezime</label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email adresa</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon <span class="text-gray-400 font-normal">(opciono)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Lozinka</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Potvrda lozinke</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
        </div>

        <div class="flex items-start gap-2">
            <input id="terms" name="terms" type="checkbox" required
                class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500" />
            <label for="terms" class="text-sm text-gray-600">
                Slažem se sa <a href="#" class="text-green-600 hover:text-green-700 underline">uslovima korišćenja</a>
            </label>
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-full transition-colors">
            Registrujte se
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Već imate nalog?
        <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-medium">Prijavite se</a>
    </p>
</x-auth-split-layout>
