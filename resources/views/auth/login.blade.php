<x-auth-split-layout title="Dobrodošli nazad" subtitle="Prijavite se i nastavite sa evidencijom otpada za vašu firmu.">
    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mb-2">Prijava</h2>
    <p class="text-gray-500 text-sm mb-6">Unesite podatke za pristup nalogu</p>

    <x-validation-errors class="mb-4" />

    @session('status')
        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
            {{ $value }}
        </div>
    @endsession

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email adresa</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Lozinka</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2">
                <input id="remember_me" name="remember" type="checkbox"
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
                <span class="text-sm text-gray-600">Zapamti me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700">Zaboravili ste lozinku?</a>
            @endif
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-full transition-colors">
            Prijavite se
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Nemate nalog?
        <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-medium inline-flex items-center gap-1">
            Registrujte se
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </p>
</x-auth-split-layout>
