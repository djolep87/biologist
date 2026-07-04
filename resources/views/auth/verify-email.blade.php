<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Potvrda emaila — {{ config('app.name', 'Biologist') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Inter',sans-serif] antialiased bg-[#f8f9f4] min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 text-center" x-data="{ cooldown: 0 }" x-init="
        if (localStorage.getItem('verify_cooldown')) {
            const until = parseInt(localStorage.getItem('verify_cooldown'));
            if (until > Date.now()) cooldown = Math.ceil((until - Date.now()) / 1000);
        }
        setInterval(() => { if (cooldown > 0) cooldown--; }, 1000);
    ">
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
        </div>

        <h1 class="font-['Plus_Jakarta_Sans',sans-serif] text-xl font-bold text-gray-900 mb-3">Potvrdite email adresu</h1>

        <p class="text-gray-600 text-sm leading-relaxed mb-6">
            Poslali smo vam email na potvrdu naloga. Kliknite na link u emailu da biste aktivirali nalog i nastavili sa registracijom firme.
        </p>

        @error('email')
            <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-left">
                {{ $message }}
            </div>
        @enderror

        @if (config('mail.default') === 'smtp' && str_contains((string) config('mail.mailers.smtp.host'), 'mailtrap'))
            <div class="mb-4 text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-left">
                <strong>Mailtrap sandbox:</strong> email ne stiže u pravi inbox (Gmail itd.), već u
                <a href="https://mailtrap.io/inboxes" target="_blank" rel="noopener" class="underline font-medium">Mailtrap → Email Testing → Inbox</a>.
                Proverite da je inbox povezan sa istim SMTP kredencijalima kao u <code class="text-xs">.env</code>.
            </div>
        @endif

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                Novi link za potvrdu je poslat na vašu email adresu.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-6"
            @submit="localStorage.setItem('verify_cooldown', Date.now() + 60000); cooldown = 60;">
            @csrf
            <button type="submit"
                :disabled="cooldown > 0"
                class="w-full inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-full transition-colors text-sm">
                <span x-show="cooldown === 0">Pošalji ponovo</span>
                <span x-show="cooldown > 0" x-cloak>Pošalji ponovo (<span x-text="cooldown"></span>s)</span>
            </button>
        </form>

        <div class="flex items-center justify-center gap-4 text-sm">
            <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-gray-700">Izmeni profil</a>
            <span class="text-gray-300">|</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-gray-700">Odjava</button>
            </form>
        </div>
    </div>
</body>
</html>
