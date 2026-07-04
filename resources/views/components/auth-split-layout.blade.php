@props(['title', 'subtitle' => null])

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name', 'Biologist') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-['Inter',sans-serif] antialiased">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <div class="hidden lg:flex lg:w-1/2 xl:w-[45%] bg-gradient-to-br from-[#0a1a0a] via-[#0f2a0f] to-[#1a3a1a] text-white flex-col justify-between p-12 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(74,222,128,0.12)_0%,_transparent_60%)]"></div>
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1.1-2.5c.43 1.05 1.18 2.15 2.65 2.15 1.55 0 2.73-1.35 3.9-2.7.9-1.05 1.85-2.15 3.35-2.15 1.25 0 2.05.75 2.9 1.6.75.75 1.55 1.55 2.75 1.55 2.05 0 3.35-2.05 4.7-4.05C22.5 13.5 21.5 8 17 8z"/></svg>
                    <span class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-xl">{{ config('app.name', 'Biologist') }}</span>
                </a>
            </div>
            <div class="relative z-10 space-y-6">
                <p class="text-green-400 text-sm font-medium uppercase tracking-wider">Digitalna evidencija otpada</p>
                <h1 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl xl:text-4xl font-bold leading-tight">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="text-green-100/80 text-lg leading-relaxed max-w-md">{{ $subtitle }}</p>
                @endif
                <ul class="space-y-4 pt-4">
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-400/20 flex items-center justify-center text-green-400 text-sm">✓</span>
                        <span class="text-green-50/90">Automatski DEO1–DEO6 obrasci</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-400/20 flex items-center justify-center text-green-400 text-sm">✓</span>
                        <span class="text-green-50/90">PDF izveštaji spremni za inspekciju</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-400/20 flex items-center justify-center text-green-400 text-sm">✓</span>
                        <span class="text-green-50/90">100% usklađeno sa zakonom</span>
                    </li>
                </ul>
            </div>
            <p class="relative z-10 text-green-200/50 text-sm">© {{ date('Y') }} {{ config('app.name', 'Biologist') }}</p>
        </div>

        <div class="flex-1 flex items-center justify-center bg-white px-6 py-12 lg:px-16">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 text-[#0a1a0a]">
                        <svg class="w-7 h-7 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1.1-2.5c.43 1.05 1.18 2.15 2.65 2.15 1.55 0 2.73-1.35 3.9-2.7.9-1.05 1.85-2.15 3.35-2.15 1.25 0 2.05.75 2.9 1.6.75.75 1.55 1.55 2.75 1.55 2.05 0 3.35-2.05 4.7-4.05C22.5 13.5 21.5 8 17 8z"/></svg>
                        <span class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-lg">{{ config('app.name', 'Biologist') }}</span>
                    </a>
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
