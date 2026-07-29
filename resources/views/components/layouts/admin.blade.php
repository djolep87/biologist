@props(['title' => 'Admin', 'header' => null])

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Biologist Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-['Inter',sans-serif] antialiased bg-[#f8f9f4]" x-data="{ sidebarOpen: false }">
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-50 w-[260px] bg-[#1e2430] text-white flex flex-col transform transition-transform duration-200 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
        <div class="p-5 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                <div>
                    <span class="font-['Plus_Jakarta_Sans',sans-serif] font-bold block leading-tight">Biologist</span>
                    <span class="text-[10px] uppercase tracking-wider text-indigo-400/90 font-semibold">Admin panel</span>
                </div>
            </a>
        </div>

        <div class="px-4 py-3 border-b border-white/10 space-y-3">
            @livewire('admin.admin-team-selector')
            <div class="rounded-lg bg-indigo-500/15 border border-indigo-400/20 px-3 py-2.5">
                <p class="text-xs text-indigo-300/80 uppercase tracking-wide font-medium">Ulogovani kao</p>
                <p class="text-sm font-medium truncate mt-0.5">{{ Auth::user()->name }}</p>
                <span class="inline-block mt-1.5 text-[10px] bg-indigo-500/25 text-indigo-300 px-2 py-0.5 rounded-full font-semibold">Super Admin</span>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-wider text-gray-500">Pregled</p>
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📊</span> Dashboard
            </a>

            <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-wider text-gray-500">Upravljanje</p>
            <a href="{{ route('admin.operateri.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.operateri.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>🏭</span> Operateri
            </a>
            <a href="{{ route('admin.klijenti.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.klijenti.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>👥</span> Klijenti (Firme)
            </a>
            <a href="{{ route('admin.evidencije.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.evidencije.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📝</span> Evidencije
            </a>
            <a href="{{ route('admin.klijent-rad') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.klijent-rad') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📋</span> Rad sa klijentom
            </a>
            <a href="{{ route('admin.zahtevi.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.zahtevi.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📨</span> Zahtevi za predaju
                @if (($naCekanjuCount ?? 0) > 0)
                    <span class="ml-auto min-w-[1.25rem] h-5 px-1.5 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                        {{ $naCekanjuCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.dko-zahtevi.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dko-zahtevi.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📋</span> DKO Zahtevi (građ.)
                @if (($gradjevinskiDkoNaCekanjuCount ?? 0) > 0)
                    <span class="ml-auto min-w-[1.25rem] h-5 px-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                        {{ $gradjevinskiDkoNaCekanjuCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.dokumenti.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dokumenti.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>🚛</span> Svi DOKO dokumenti
            </a>

            <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-wider text-gray-500">Izveštaji</p>
            <a href="{{ route('admin.gio1.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.gio1.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📊</span> GIO1 Godišnji izveštaj
            </a>
            <a href="{{ route('admin.waste-plans.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.waste-plans.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <span>📋</span> Plan upravljanja otpadom
            </a>
        </nav>

        <div class="p-4 border-t border-white/10 space-y-2">
            <a href="{{ route('dashboard') }}"
                class="flex items-center justify-center gap-2 w-full text-sm text-gray-300 hover:text-white px-3 py-2 rounded-lg hover:bg-white/5 transition-colors border border-white/10">
                ← Klijentski dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-gray-400 hover:text-white px-3 py-2 rounded-lg hover:bg-white/5 transition-colors">
                    Odjava
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="lg:pl-[260px] min-h-screen flex flex-col w-full min-w-0">
        <header class="sticky top-0 z-30 bg-white shadow-sm border-b border-gray-100">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4 min-w-0">
                    <button @click="sidebarOpen = true" type="button" class="lg:hidden p-2 text-gray-500 hover:text-gray-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="font-['Plus_Jakarta_Sans',sans-serif] font-semibold text-lg text-gray-900 truncate">
                        {{ $header ?? $title }}
                    </h1>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @livewire('notification-bell', ['variant' => 'admin'])
                    @if (($dozvoleAlertCount ?? 0) > 0)
                        <a href="{{ route('admin.operateri.index') }}" class="relative p-2 text-gray-400 hover:text-indigo-600" title="Dozvole operatera">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="absolute -top-0.5 -right-0.5 min-w-[1rem] h-4 px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $dozvoleAlertCount }}</span>
                        </a>
                    @endif
                    <a href="{{ url('/') }}" target="_blank" rel="noopener" class="hidden sm:inline text-sm text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap">
                        Posetite sajt ↗
                    </a>
                    <div class="size-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm font-semibold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full min-w-0">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
</body>
</html>
