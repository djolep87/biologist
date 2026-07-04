@props(['title' => 'Dnevna evidencija'])

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Biologist</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-['Inter',sans-serif] antialiased bg-[#f8f9f4]">
    <x-banner />

    <div class="min-h-screen flex flex-col">
        <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-100">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
                <div class="flex items-center gap-3">
                    <svg class="w-7 h-7 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1.1-2.5c.43 1.05 1.18 2.15 2.65 2.15 1.55 0 2.73-1.35 3.9-2.7.9-1.05 1.85-2.15 3.35-2.15 1.25 0 2.05.75 2.9 1.6.75.75 1.55 1.55 2.75 1.55 2.05 0 3.35-2.05 4.7-4.05C22.5 13.5 21.5 8 17 8z"/></svg>
                    <div>
                        <h1 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-lg text-gray-900">{{ $title }}</h1>
                        @if (Auth::user()->currentTeam)
                            <p class="text-xs text-gray-500">{{ Auth::user()->currentTeam->name }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Evidenciar
                    </span>
                    <div class="size-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-50">
                            Odjava
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
            @if (session('notify'))
                <div class="mb-4 rounded-xl border px-4 py-3 text-sm {{ session('notify.type') === 'error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' }}">
                    {{ session('notify.message') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @stack('modals')
    @livewireScripts
</body>
</html>
