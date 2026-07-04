@props(['title' => 'Pregled'])

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
<body class="font-['Inter',sans-serif] antialiased bg-[#f8f9f4]" x-data="{ sidebarOpen: false, firmMenuOpen: false }">
    <x-banner />

    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    <aside class="fixed inset-y-0 left-0 z-50 w-[260px] bg-[#1e2430] text-white flex flex-col transform transition-transform duration-200 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="p-5 border-b border-white/10">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <svg class="w-7 h-7 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1.1-2.5c.43 1.05 1.18 2.15 2.65 2.15 1.55 0 2.73-1.35 3.9-2.7.9-1.05 1.85-2.15 3.35-2.15 1.25 0 2.05.75 2.9 1.6.75.75 1.55 1.55 2.75 1.55 2.05 0 3.35-2.05 4.7-4.05C22.5 13.5 21.5 8 17 8z"/></svg>
                <span class="font-['Plus_Jakarta_Sans',sans-serif] font-bold">Biologist</span>
            </a>
        </div>

        @if (Auth::user()->currentTeam)
            <div class="px-4 py-3 border-b border-white/10 relative">
                <button @click="firmMenuOpen = !firmMenuOpen" type="button" class="w-full flex items-center justify-between gap-2 bg-white/5 hover:bg-white/10 rounded-lg px-3 py-2.5 text-left text-sm transition-colors">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium truncate">{{ Auth::user()->currentTeam->name }}</p>
                        <span class="inline-block mt-1 text-xs bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">{{ Auth::user()->currentTeam->tip_subjekta ?? 'DEO1' }}</span>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="firmMenuOpen" @click.outside="firmMenuOpen = false" x-cloak class="absolute left-4 right-4 top-full mt-1 bg-[#2a3140] rounded-lg shadow-xl border border-white/10 py-1 z-50 max-h-48 overflow-y-auto">
                    @foreach (Auth::user()->allTeams() as $team)
                        <form method="POST" action="{{ route('current-team.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="team_id" value="{{ $team->id }}">
                            <button type="submit" class="w-full text-left px-3 py-2 text-sm hover:bg-white/5 {{ Auth::user()->isCurrentTeam($team) ? 'text-green-400' : 'text-gray-300' }}">
                                {{ $team->name }}
                            </button>
                        </form>
                    @endforeach
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <a href="{{ route('teams.create') }}" class="block px-3 py-2 text-sm text-green-400 hover:bg-white/5 border-t border-white/10 mt-1">+ Dodaj firmu</a>
                    @endcan
                </div>
            </div>
        @endif

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-green-500/20 text-green-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📝</span> Dnevna Evidencija
            </a>
            <a href="{{ route('dashboard') }}?tab=gio1" wire:navigate
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->get('tab') === 'gio1' ? 'bg-green-500/20 text-green-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📄</span> Godišnji izveštaj
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-white/5 hover:text-white">
                <span>🚛</span> Kretanje Otpada
            </a>
            <a href="{{ Auth::user()->currentTeam ? route('teams.show', Auth::user()->currentTeam) : route('teams.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('teams.*') ? 'bg-green-500/20 text-green-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>🏭</span> Moje Firme
            </a>
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('profile.show') ? 'bg-green-500/20 text-green-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>⚙️</span> Podešavanja
            </a>
        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <img class="size-9 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="">
                @else
                    <div class="size-9 rounded-full bg-green-500/30 flex items-center justify-center text-green-400 text-sm font-semibold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-gray-400 hover:text-white px-3 py-2 rounded-lg hover:bg-white/5 transition-colors">
                    Odjava
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-[260px] min-h-screen flex flex-col">
        <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-100">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" type="button" class="lg:hidden p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-lg text-gray-900">{{ $title }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    @if (Auth::user()->currentTeam)
                        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ Auth::user()->currentTeam->name }}
                        </span>
                    @endif
                    @livewire('notification-bell', ['variant' => 'client'])
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="">
                    @else
                        <div class="size-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

    @stack('modals')
    @livewireScripts
</body>
</html>
