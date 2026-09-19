<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Biologist') }} — Digitalna evidencija otpada i dokumentacija</title>
    <meta name="description" content="Platforma za evidenciju otpada u Srbiji — dnevna evidencija (DEO), DKO dokumenti o kretanju otpada, GIO1 godišnji izveštaj, građevinski otpad po gradilištima i plan upravljanja otpadom. Sve na jednom mestu, spremno za inspekciju.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Inter',sans-serif] text-on-light antialiased bg-white">

    @php
        $navLinks = [
            '#pocetna' => 'Početna',
            '#problemi' => 'Problemi',
            '#usluge' => 'Rešenja',
            '#gradjevinski' => 'Građevinski otpad',
            '#kako-radi' => 'Kako radi',
            '#cene' => 'Cene',
            '#faq' => 'FAQ',
        ];
    @endphp

    {{-- Navigation — CSS klase (bez Alpine); scroll menja .landing-nav--light --}}
    <nav id="landing-nav" class="landing-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-[4.5rem]">
                <a href="#pocetna" class="flex items-center gap-2.5">
                    <svg class="w-8 h-8 shrink-0 landing-nav__logo-icon" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1.1-2.5c.43 1.05 1.18 2.15 2.65 2.15 1.55 0 2.73-1.35 3.9-2.7.9-1.05 1.85-2.15 3.35-2.15 1.25 0 2.05.75 2.9 1.6.75.75 1.55 1.55 2.75 1.55 2.05 0 3.35-2.05 4.7-4.05C22.5 13.5 21.5 8 17 8z"/></svg>
                    <span class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-lg landing-nav__logo-text">Biologist</span>
                </a>

                <div class="hidden xl:flex items-center gap-7">
                    @foreach ($navLinks as $href => $label)
                        <a href="{{ $href }}" class="landing-nav__link text-sm font-medium">{{ $label }}</a>
                    @endforeach
                </div>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="landing-nav__link text-sm font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="landing-nav__link text-sm font-medium">Prijava</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="landing-nav__btn">Počnite besplatno</a>
                        @endif
                    @endauth
                </div>

                <button id="landing-nav-toggle" type="button" class="landing-nav__menu-btn xl:hidden p-2 rounded-lg" aria-label="Meni" aria-expanded="false" aria-controls="landing-nav-mobile">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div id="landing-nav-mobile" class="landing-nav__mobile xl:hidden bg-white border-t border-gray-200 shadow-lg">
            <div class="px-4 py-4 space-y-1">
                @foreach ($navLinks as $href => $label)
                    <a href="{{ $href }}" class="block text-on-light-muted py-2.5 font-medium hover:text-green-700 transition-colors">{{ $label }}</a>
                @endforeach
                <div class="pt-4 border-t border-gray-100 flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="text-center py-2.5 text-on-light-muted font-medium hover:text-green-700 transition-colors">Prijava</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="landing-cta w-full text-center">Počnite besplatno</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section id="pocetna" class="landing-on-dark relative bg-gradient-to-br from-green-950 via-green-900 to-green-800 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_20%_0%,_rgba(74,222,128,0.18)_0%,_transparent_55%)]" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-green-950/40" aria-hidden="true"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16 lg:pt-32 lg:pb-20">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <p class="text-on-dark-accent text-sm font-semibold uppercase tracking-[0.12em] mb-4">Evidencija otpada i dokumentacija za pravna lica</p>
                    <h1 class="font-['Plus_Jakarta_Sans',sans-serif] text-4xl sm:text-5xl lg:text-[3.25rem] font-bold leading-[1.1] mb-6 text-on-dark">
                        Vaš otpad je pod kontrolom.<br>
                        <span class="text-on-dark-accent">Papirologija takođe.</span>
                    </h1>
                    <p class="text-lg text-on-dark-muted leading-relaxed mb-6 max-w-xl">
                        Biologist objedinjuje dnevnu evidenciju otpada, DKO dokumente o kretanju otpada,
                        GIO1 godišnji izveštaj, građevinski otpad po gradilištima i plan upravljanja otpadom —
                        u jednom nalogu, sa dokumentima koji su uvek spremni za inspekciju.
                    </p>

                    <ul class="space-y-2.5 mb-8">
                        @foreach ([
                            'Unos otpada za manje od 2 minuta — stanje skladišta se računa samo',
                            'DKO i GIO1 se popunjavaju iz vaših podataka, bez prekucavanja',
                            'Naš stručni tim preuzima zahteve i izdaje dokumente umesto vas',
                        ] as $benefit)
                            <li class="flex items-start gap-2.5 text-on-dark-muted">
                                <svg class="w-5 h-5 text-on-dark-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-[0.95rem]">{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-6">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="landing-cta landing-cta--hero">
                                Počnite besplatno
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        @endif
                        <a href="#kako-radi" class="landing-cta landing-cta--outline justify-center">
                            Pogledajte kako radi
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-10">
                        @foreach (['Bez kreditne kartice', 'Nalog za 2 minuta', 'Podrška na srpskom jeziku'] as $pill)
                            <span class="landing-pill">
                                <svg class="w-3.5 h-3.5 text-on-dark-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $pill }}
                            </span>
                        @endforeach
                    </div>

                </div>

                {{-- Dashboard mockup --}}
                <div class="relative hidden sm:block">
                    <div class="landing-mockup shadow-green-900/20">
                        <div class="landing-mockup__chrome">
                            <span class="landing-mockup__dot bg-red-400"></span>
                            <span class="landing-mockup__dot bg-amber-400"></span>
                            <span class="landing-mockup__dot bg-green-500"></span>
                            <span class="text-xs text-gray-500 ml-2 font-medium">Biologist — Dnevna evidencija</span>
                        </div>
                        <div class="flex min-h-[280px]">
                            <div class="w-14 bg-gray-900 shrink-0 py-4 px-2 space-y-3 hidden md:block">
                                <div class="h-2 w-full bg-green-500/80 rounded"></div>
                                <div class="h-2 w-3/4 bg-white/20 rounded"></div>
                                <div class="h-2 w-full bg-white/10 rounded"></div>
                                <div class="h-2 w-2/3 bg-white/10 rounded"></div>
                            </div>
                            <div class="flex-1 p-4 bg-gray-50">
                                <div class="flex gap-2 mb-4">
                                    <span class="px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-semibold">Dnevna evidencija</span>
                                    <span class="px-3 py-1.5 rounded-lg bg-white text-gray-500 text-xs border">DKO</span>
                                    <span class="px-3 py-1.5 rounded-lg bg-white text-gray-500 text-xs border">GIO1</span>
                                </div>
                                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden text-xs">
                                    <div class="grid grid-cols-4 gap-2 px-3 py-2 bg-[#f8f9f4] font-semibold text-gray-500 border-b">
                                        <span>Datum</span><span>Indeks</span><span>Proizv.</span><span>Stanje</span>
                                    </div>
                                    @foreach ([['12.03.', '15 01 02', '2,4 t', '18,2 t'], ['11.03.', '20 03 01', '0,8 t', '5,1 t'], ['10.03.', '13 02 08', '1,2 t', '3,0 t']] as $row)
                                        <div class="grid grid-cols-4 gap-2 px-3 py-2.5 border-b border-gray-50 text-gray-700">
                                            @foreach ($row as $cell)<span>{{ $cell }}</span>@endforeach
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 flex items-center justify-between rounded-lg bg-green-50 border border-green-100 px-3 py-2">
                                    <span class="text-[11px] font-medium text-green-800">Zahtev za DKO poslat</span>
                                    <span class="text-[10px] font-semibold text-green-700 bg-white border border-green-200 rounded-full px-2 py-0.5">U obradi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl">
                @foreach ([
                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Obrasci se popunjavaju automatski'],
                    ['icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'title' => 'PDF i Excel izveštaji jednim klikom'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Pripremljeno prema srpskim propisima'],
                ] as $feat)
                    <div class="landing-feature-card">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-500/30 text-on-dark-accent">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $feat['icon'] }}"/></svg>
                        </span>
                        <p class="font-medium text-sm text-on-dark-muted pt-2">{{ $feat['title'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="landing-hero-fade" aria-hidden="true"></div>
    </section>

    {{-- Problemi koje rešavamo --}}
    <section id="problemi" class="landing-section bg-[#f9fafb]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <p class="landing-eyebrow">Problemi koje rešavamo</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light mb-4">
                    Da li vam ovo zvuči poznato?
                </h2>
                <p class="text-on-light-muted leading-relaxed">
                    Evidencija otpada retko kome je glavni posao. Zato najčešće završi u tabelama, sveskama i
                    mejlovima — a onda dođe inspekcija ili rok za godišnji izveštaj.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                @foreach ([
                    ['Excel koji niko ne razume', 'Podaci su u tri tabele, kod dve osobe, u dve verzije. Stanje skladišta se ne slaže, a niko ne zna koja je verzija poslednja.'],
                    ['DKO se popunjava ručno', 'Svaki transport traži novi dokument o kretanju otpada — brojevi, indeksni brojevi, podaci o operateru i dozvoli, sve prepisano rukom.'],
                    ['GIO1 u poslednjem trenutku', 'Godišnji izveštaj se pravi za nekoliko dana u martu, sabiranjem cele godine unazad — uz realan rizik od greške u brojkama.'],
                    ['Inspekcija traži papire', 'Traži se evidencija za tačan datum i tačnu vrstu otpada. Traženje po fasciklama i folderima troši dane i stvara nervozu.'],
                ] as [$pTitle, $pDesc])
                    <div class="landing-problem-card">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-500 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        </span>
                        <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-base text-on-light mb-2">{{ $pTitle }}</h3>
                        <p class="text-sm text-on-light-muted leading-relaxed">{{ $pDesc }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Pre / posle --}}
            <div class="grid md:grid-cols-2 gap-6 max-w-5xl mx-auto">
                <div class="rounded-2xl border border-gray-200 bg-white p-7">
                    <p class="text-xs font-bold uppercase tracking-wider text-on-light-subtle mb-5">Bez Biologist-a</p>
                    <ul class="space-y-3">
                        @foreach ([
                            'Tabele i sveske, podaci na više mesta',
                            'Ručno prepisivanje istih podataka u svaki obrazac',
                            'Greške u zbiru koje se otkriju tek u martu',
                            'Dokumenti razbacani po mejlovima i folderima',
                            'Zavisnost od jedne osobe koja „zna gde je šta"',
                        ] as $bad)
                            <li class="flex items-start gap-3 text-sm text-on-light-muted">
                                <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                {{ $bad }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl border-2 border-green-500 bg-white p-7 shadow-lg ring-2 ring-green-500/10">
                    <p class="text-xs font-bold uppercase tracking-wider text-green-700 mb-5">Sa Biologist-om</p>
                    <ul class="space-y-3">
                        @foreach ([
                            'Jedna baza podataka za celu firmu i sve korisnike',
                            'Podatak se unosi jednom, koristi u svim dokumentima',
                            'Stanje skladišta i zbirovi se računaju automatski',
                            'Kompletna arhiva dostupna po datumu i vrsti otpada',
                            'Tim sa ulogama — svako vidi tačno ono što mu treba',
                        ] as $good)
                            <li class="flex items-start gap-3 text-sm text-on-light-muted">
                                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $good }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- O aplikaciji --}}
    <section id="o-nama" class="landing-section bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="relative order-2 lg:order-1">
                    <div class="landing-mockup">
                        <div class="landing-mockup__chrome">
                            <span class="landing-mockup__dot bg-red-400"></span>
                            <span class="landing-mockup__dot bg-amber-400"></span>
                            <span class="landing-mockup__dot bg-green-500"></span>
                            <span class="text-xs text-gray-500 ml-2">GIO1 — Godišnji izveštaj</span>
                        </div>
                        <div class="p-5 space-y-3 bg-white">
                            <div class="h-3 w-2/3 bg-gray-200 rounded"></div>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="h-8 bg-green-50 border border-green-100 rounded text-[10px] flex items-center justify-center text-green-700 font-mono">15 01 02</div>
                                <div class="h-8 bg-green-50 border border-green-100 rounded text-[10px] flex items-center justify-center text-green-700 font-mono">20 03 01</div>
                                <div class="h-8 bg-gray-50 border rounded text-[10px] flex items-center justify-center text-gray-400">+ još</div>
                            </div>
                            <div class="rounded-lg border border-gray-200 p-3 space-y-2">
                                <div class="flex justify-between text-[11px] text-gray-500 font-medium"><span>Operater</span><span>Količina</span><span>R/D</span></div>
                                <div class="flex justify-between text-xs text-gray-800"><span>EkoRec DOO</span><span>45,2 t</span><span class="text-green-600 font-medium">R5</span></div>
                                <div class="flex justify-between text-xs text-gray-800"><span>GreenWaste</span><span>12,0 t</span><span class="text-green-600 font-medium">R3</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <p class="landing-eyebrow">O aplikaciji</p>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light leading-tight mb-6">
                        Platforma napravljena za propise o otpadu u Srbiji
                    </h2>
                    <p class="text-on-light-muted leading-relaxed mb-6">
                        Biologist nije opšti alat za tabele prilagođen otpadu. Napravljen je oko stvarnog toka posla —
                        od nastanka otpada, preko predaje ovlašćenom operateru, do godišnjeg izveštaja —
                        u saradnji sa ljudima koji taj posao rade svakodnevno.
                    </p>
                    <p class="text-on-light-muted leading-relaxed mb-8">
                        Indeksni brojevi, R i D oznake, podaci o dozvolama operatera i format DKO brojeva
                        već su ugrađeni u sistem, pa ne morate da ih pamtite niti prekucavate.
                    </p>
                    <ul class="space-y-4 mb-8">
                        @foreach ([
                            ['Kompletan tok otpada', 'Od dnevnog unosa, preko zahteva za predaju, do izdatog DKO dokumenta.'],
                            ['Podaci se unose jednom', 'Isti unos koristi se za dnevnu evidenciju, DKO i godišnji izveštaj.'],
                            ['Vaša pravila numeracije', 'Format DKO broja, prefiks, broj cifara i lokacije podešavaju se po firmi.'],
                            ['Uvek spremna arhiva', 'Svaki dokument ostaje sačuvan i dostupan za preuzimanje u PDF-u.'],
                        ] as [$aTitle, $aDesc])
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>
                                    <span class="font-semibold text-on-light">{{ $aTitle }}</span>
                                    <span class="block text-sm text-on-light-muted mt-0.5">{{ $aDesc }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="landing-cta">Otvorite nalog besplatno</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Usluge / moduli --}}
    <section id="usluge" class="landing-section bg-[#f9fafb]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="landing-eyebrow">Šta sve dobijate</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light mb-4">
                    Svi moduli za evidenciju i dokumentaciju o otpadu
                </h2>
                <p class="text-on-light-muted">
                    Ne morate da birate između pet različitih alata. Svaki modul koristi iste podatke o vašoj firmi,
                    otpadu i operaterima.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                    [
                        'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                        'title' => 'Dnevna evidencija otpada',
                        'desc' => 'Unos nastanka i predaje otpada po datumu, indeksnom broju i vrsti. Stanje na skladištu se obračunava automatski posle svakog unosa.',
                        'points' => ['Karakter i fizičko stanje otpada', 'Automatski obračun stanja skladišta', 'Mesečni i godišnji pregledi'],
                    ],
                    [
                        'path' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                        'title' => 'DKO — kretanje otpada',
                        'desc' => 'Zahtev za predaju otpada šaljete iz aplikacije, a dokument o kretanju otpada dobijate popunjen — sa vašim brojem i podacima operatera.',
                        'points' => ['Sopstveni format i prefiks broja', 'Praćenje statusa zahteva', 'Preuzimanje DKO i Dela 1 u PDF-u'],
                    ],
                    [
                        'path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'title' => 'GIO1 godišnji izveštaj',
                        'desc' => 'Godišnji izveštaj se formira iz vaših dnevnih unosa — po vrstama otpada, količinama i operaterima kojima je otpad predat.',
                        'points' => ['Zbirovi po indeksnim brojevima', 'Podaci o odgovornim licima', 'Izvoz spreman za dostavljanje'],
                    ],
                    [
                        'path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'title' => 'Građevinski otpad i gradilišta',
                        'desc' => 'Posebna evidencija po gradilištu — sa brojem građevinske dozvole, investitorom, izvođačem i procenom količine otpada prema tipu radova.',
                        'points' => ['Katalog otpada grupe 17', 'Deo 1 po svakom gradilištu', 'DKO zahtevi vezani za gradilište'],
                    ],
                    [
                        'path' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                        'title' => 'Plan upravljanja otpadom',
                        'desc' => 'Na osnovu podataka o firmi, delatnosti i vrstama otpada dobijate strukturiran dokument koji možete preuzeti kao PDF i dalje dorađivati.',
                        'points' => ['Podaci o firmi i odgovornim licima', 'Vrste otpada i indeksni brojevi', 'Preuzimanje u PDF formatu'],
                    ],
                    [
                        'path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        'title' => 'Registar operatera i tim',
                        'desc' => 'Baza ovlašćenih operatera sa brojem i rokom važenja dozvole, R/D oznakama i indeksnim brojevima koje prihvataju — plus uloge za članove tima.',
                        'points' => ['Pretraga operatera po nazivu i PIB-u', 'Uloge: administrator firme i evidenciar', 'Podaci odvojeni po firmi'],
                    ],
                ] as $card)
                    <div class="landing-card flex flex-col">
                        <span class="landing-icon-tile mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $card['path'] }}"/></svg>
                        </span>
                        <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-lg text-on-light mb-2">{{ $card['title'] }}</h3>
                        <p class="text-sm text-on-light-muted leading-relaxed mb-4">{{ $card['desc'] }}</p>
                        <ul class="mt-auto space-y-2 pt-4 border-t border-gray-100">
                            @foreach ($card['points'] as $point)
                                <li class="flex items-start gap-2 text-xs text-on-light-subtle">
                                    <svg class="w-4 h-4 text-green-600 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Građevinski otpad --}}
    <section id="gradjevinski" class="landing-section bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <p class="landing-eyebrow">Za građevinsku delatnost</p>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light leading-tight mb-6">
                        Otpad sa gradilišta, evidentiran po gradilištu
                    </h2>
                    <p class="text-on-light-muted leading-relaxed mb-6">
                        Rušenje, gradnja, rekonstrukcija i sanacija ne proizvode isti otpad — ni po vrsti ni po količini.
                        Zato građevinski deo aplikacije prati svako gradilište posebno, sa svojom dozvolom,
                        investitorom, izvođačem i evidencijom.
                    </p>
                    <div class="space-y-4 mb-8">
                        @foreach ([
                            ['Kartoteka gradilišta', 'Naziv, broj građevinske dozvole, adresa, katastarska parcela, investitor i izvođač — na jednom mestu, sa statusom (aktivno, pauzirano, završeno).'],
                            ['Procena količine otpada', 'Na osnovu kvadrature i tipa radova dobijate okvirnu procenu tonaže, korisnu za planiranje odvoza i pripremu dokumentacije.'],
                            ['Katalog otpada grupe 17', 'Najčešće šifre građevinskog otpada su ponuđene prve, tako da unos traje nekoliko sekundi umesto listanja kataloga.'],
                            ['DKO zahtev sa gradilišta', 'Zahtev za dokument o kretanju otpada šaljete direktno iz gradilišta — sa podacima o operateru koji preuzima otpad.'],
                        ] as [$gTitle, $gDesc])
                            <div class="flex gap-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <div>
                                    <p class="font-semibold text-on-light">{{ $gTitle }}</p>
                                    <p class="text-sm text-on-light-muted mt-1 leading-relaxed">{{ $gDesc }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="landing-cta">Otvorite prvo gradilište</a>
                    @endif
                </div>

                <div class="landing-mockup">
                    <div class="landing-mockup__chrome">
                        <span class="landing-mockup__dot bg-red-400"></span>
                        <span class="landing-mockup__dot bg-amber-400"></span>
                        <span class="landing-mockup__dot bg-green-500"></span>
                        <span class="text-xs text-gray-500 ml-2">Gradilište — Blok 42, rušenje</span>
                    </div>
                    <div class="p-5 space-y-4 bg-white">
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ([['Status', 'Aktivno'], ['Kvadratura', '1.850 m²'], ['Procena', '832 t']] as [$k, $v])
                                <div class="rounded-lg border border-gray-200 px-3 py-2">
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">{{ $k }}</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $v }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="rounded-lg border border-gray-200 overflow-hidden">
                            <div class="grid grid-cols-3 gap-2 px-3 py-2 bg-[#f8f9f4] text-[11px] font-semibold text-gray-500 border-b">
                                <span>Šifra</span><span>Naziv</span><span class="text-right">Masa</span>
                            </div>
                            @foreach ([['17 01 07', 'Mešani beton i cigla', '412,0 t'], ['17 05 04', 'Zemlja i kamen', '286,5 t'], ['17 04 05', 'Gvožđe i čelik', '18,2 t']] as [$sifra, $naziv, $masa])
                                <div class="grid grid-cols-3 gap-2 px-3 py-2.5 border-b border-gray-50 text-xs text-gray-700 items-center">
                                    <span class="font-mono text-green-700">{{ $sifra }}</span>
                                    <span class="truncate">{{ $naziv }}</span>
                                    <span class="text-right font-medium">{{ $masa }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-green-50 border border-green-100 px-3 py-2.5">
                            <span class="text-xs font-medium text-green-800">DKO zahtev #128</span>
                            <span class="text-[10px] font-semibold text-white bg-green-600 rounded-full px-2.5 py-1">Završeno</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Kako funkcioniše --}}
    <section id="kako-radi" class="landing-section bg-[#f9fafb]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <p class="landing-eyebrow">Kako funkcioniše</p>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light leading-tight mb-6">
                        Četiri koraka do uredne dokumentacije
                    </h2>
                    <div class="space-y-6 mb-8">
                        @foreach ([
                            ['1', 'Registrujte firmu', 'Unesite PIB, matični broj, adresu i podatke o delatnosti. Pozovite kolege i dodelite im uloge — administrator firme ili evidenciar.'],
                            ['2', 'Unosite otpad dnevno', 'Svaki unos traje manje od 2 minuta. Birate indeksni broj, količinu i način nastanka — stanje skladišta se računa samo.'],
                            ['3', 'Pošaljite zahtev za predaju', 'Kada otpad predajete operateru, šaljete zahtev iz aplikacije. Naš tim ga preuzima i izdaje DKO dokument sa vašim brojem.'],
                            ['4', 'Preuzmite izveštaje', 'GIO1, Deo 1, DKO i mesečni pregledi — u PDF-u ili Excel-u, spremni za dostavljanje i za inspekciju.'],
                        ] as [$step, $title, $desc])
                            <div class="flex gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-600 text-white font-bold text-sm">{{ $step }}</span>
                                <div>
                                    <p class="font-semibold text-on-light">{{ $title }}</p>
                                    <p class="text-sm text-on-light-muted mt-1 leading-relaxed">{{ $desc }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="landing-cta">Počnite besplatno</a>
                    @endif
                </div>
                <div class="space-y-6">
                    <div class="landing-mockup">
                        <div class="landing-mockup__chrome">
                            <span class="text-xs text-gray-500 font-medium">Korak po korak</span>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach ([
                                ['✓', 'Firma registrovana', 'bg-green-600 text-white'],
                                ['✓', 'Dnevni unos otpada', 'bg-green-600 text-white'],
                                ['3', 'Zahtev za DKO poslat', 'bg-green-100 text-green-800 border-2 border-green-500'],
                                ['4', 'GIO1 generisan', 'bg-gray-100 text-gray-400'],
                            ] as [$badge, $label, $cls])
                                <div class="flex items-center gap-4 rounded-xl px-4 py-3 {{ $cls }}">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/30 font-bold text-sm">{{ $badge }}</span>
                                    <span class="font-medium text-sm">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-green-100 bg-white p-6">
                        <div class="flex items-start gap-4">
                            <span class="landing-icon-tile shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18.364 5.636a9 9 0 010 12.728m-3.536-3.536a4 4 0 010-5.656M8.464 8.464a4 4 0 000 5.656m-3.535 3.536a9 9 0 010-12.728"/></svg>
                            </span>
                            <div>
                                <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-lg text-on-light mb-2">Niste sami u ovome</h3>
                                <p class="text-sm text-on-light-muted leading-relaxed">
                                    Kada pošaljete zahtev za predaju otpada, on ne ostaje da čeka na vas.
                                    Naš tim ga preuzima, proverava podatke i vraća vam gotov dokument —
                                    a vi u svakom trenutku vidite u kojoj je fazi, uz obaveštenje kada bude završen.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Za koga je --}}
    <section id="za-koga" class="landing-section bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="landing-eyebrow">Za koga je Biologist</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light mb-4">
                    Ako vaša firma stvara otpad, ovo je vaše mesto
                </h2>
                <p class="text-on-light-muted">
                    Bez obzira na to da li imate jednu lokaciju ili deset gradilišta — sistem raste zajedno sa vama.
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    ['Proizvodne firme', 'Redovan nastanak otpada iz proizvodnog procesa, više vrsta otpada i redovna predaja operaterima.', ['Dnevna evidencija po vrstama', 'Predaja operaterima', 'Godišnji izveštaj']],
                    ['Građevinske firme', 'Rušenje, gradnja i rekonstrukcija — otpad se prati po gradilištu, sa dozvolom i izvođačem.', ['Evidencija po gradilištu', 'Katalog grupe 17', 'Procena tonaže']],
                    ['Trgovina i usluge', 'Ambalažni otpad, elektronski otpad i komunalni otpad iz redovnog poslovanja.', ['Brz unos manjih količina', 'Uredna arhiva', 'PDF izveštaji']],
                    ['Konsultanti za otpad', 'Vođenje evidencije za više klijenata, uz jasno razdvojene podatke svake firme.', ['Više firmi u nalogu', 'Uloge za saradnike', 'Kontrola pristupa']],
                ] as [$tTitle, $tDesc, $tPoints])
                    <div class="landing-card flex flex-col">
                        <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-lg text-on-light mb-2">{{ $tTitle }}</h3>
                        <p class="text-sm text-on-light-muted leading-relaxed mb-4">{{ $tDesc }}</p>
                        <div class="mt-auto flex flex-wrap gap-1.5">
                            @foreach ($tPoints as $tp)
                                <span class="landing-chip">{{ $tp }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Obaveze i rokovi --}}
    <section id="obaveze" class="landing-on-dark landing-section bg-gradient-to-br from-green-950 via-green-900 to-green-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">
                <div>
                    <p class="landing-eyebrow landing-eyebrow--on-dark">Obaveze i rokovi</p>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-dark leading-tight mb-6">
                        Rokovi vas neće iznenaditi
                    </h2>
                    <p class="text-on-dark-muted leading-relaxed mb-6">
                        Najveći deo problema sa evidencijom otpada nastaje zato što se posao odlaže —
                        pa se cela godina rekonstruiše u nekoliko dana. Kada se podaci unose usput,
                        svaki rok je samo pitanje jednog klika.
                    </p>
                    <p class="text-on-dark-subtle text-sm leading-relaxed">
                        Pregled je informativan i služi kao podsetnik. Konkretne obaveze zavise od delatnosti i vrste
                        otpada, pa uvek proverite aktuelne propise ili se posavetujte sa nama.
                    </p>
                </div>

                <div class="space-y-3">
                    @foreach ([
                        ['Svakodnevno', 'Dnevna evidencija otpada', 'Nastanak, predaja i stanje na skladištu evidentiraju se kako posao teče — bez naknadnog rekonstruisanja.'],
                        ['Pre svakog transporta', 'Dokument o kretanju otpada (DKO)', 'Otpad koji ide operateru prati dokument sa podacima o pošiljaocu, prevozniku, primaocu i vrsti otpada.'],
                        ['Do 31. marta', 'GIO1 za prethodnu godinu', 'Godišnji izveštaj o otpadu formira se iz vaših dnevnih unosa i zbirova po vrstama otpada.'],
                        ['Kontinuirano', 'Arhiva za inspekciju', 'Evidencija i izdati dokumenti ostaju dostupni za pretragu po datumu, vrsti otpada i gradilištu.'],
                    ] as [$when, $what, $why])
                        <div class="rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm p-5">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-on-dark-accent">{{ $when }}</span>
                                <span class="h-px flex-1 bg-white/20"></span>
                            </div>
                            <p class="font-semibold text-on-dark">{{ $what }}</p>
                            <p class="text-sm text-on-dark-muted mt-1 leading-relaxed">{{ $why }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Sigurnost i pristup --}}
    <section class="landing-section bg-[#f9fafb]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="landing-eyebrow">Sigurnost i pristup</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light mb-4">
                    Vaši podaci ostaju vaši
                </h2>
                <p class="text-on-light-muted">
                    Evidencija o otpadu govori dosta o vašem poslovanju. Zato je pristup podeljen po firmama i ulogama.
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    ['path' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Odvojeni podaci', 'desc' => 'Svaka firma ima svoj prostor. Korisnik vidi isključivo podatke firme kojoj pripada.'],
                    ['path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Uloge u timu', 'desc' => 'Administrator firme vodi sve, a evidenciar samo unosi dnevne izveštaje o otpadu.'],
                    ['path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Šifrovana veza', 'desc' => 'Komunikacija sa aplikacijom ide preko HTTPS-a, uz prijavu i potvrdu naloga.'],
                    ['path' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4', 'title' => 'Trajna arhiva', 'desc' => 'Zapisi i dokumenti se čuvaju i ostaju dostupni za preuzimanje kada zatrebaju.'],
                ] as $sec)
                    <div class="landing-card">
                        <span class="landing-icon-tile mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $sec['path'] }}"/></svg>
                        </span>
                        <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-base text-on-light mb-2">{{ $sec['title'] }}</h3>
                        <p class="text-sm text-on-light-muted leading-relaxed">{{ $sec['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="cene" class="landing-section bg-[#f9fafb]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="landing-eyebrow">Cene</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light mb-4">
                    Transparentno, bez skrivenih troškova
                </h2>
                <p class="text-on-light-muted">Počnite besplatno. Nadogradite kada vam zatreba više korisnika ili naprednih funkcija.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto mb-10">
                @foreach ([
                    ['Starter', '0', 'Besplatno', '1 firma, do 3 korisnika', ['Dnevna evidencija otpada', 'DKO dokumenti', 'PDF i Excel izvoz', 'Registar operatera'], false],
                    ['Poslovni', '4.900', 'RSD / mesec', 'Do 5 korisnika po firmi', ['Sve iz Starter paketa', 'GIO1 godišnji izveštaj', 'Građevinski otpad i gradilišta', 'Prioritetna podrška'], true],
                    ['Enterprise', 'Po dogovoru', '', 'Neograničeno korisnika', ['Više firmi u jednom nalogu', 'Plan upravljanja otpadom', 'Posvećen account manager', 'SLA i integracije'], false],
                ] as [$plan, $price, $suffix, $desc, $features, $featured])
                    <div class="rounded-2xl border {{ $featured ? 'border-green-500 ring-2 ring-green-500/20 bg-white shadow-lg' : 'border-gray-200 bg-white' }} p-6 flex flex-col">
                        @if ($featured)
                            <span class="inline-block self-start text-xs font-semibold uppercase tracking-wider text-green-700 bg-green-50 px-2.5 py-1 rounded-full mb-4">Najpopularnije</span>
                        @endif
                        <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-xl text-on-light">{{ $plan }}</h3>
                        <p class="mt-3 mb-1">
                            @if ($price === 'Po dogovoru')
                                <span class="text-2xl font-bold text-on-light">{{ $price }}</span>
                            @else
                                <span class="text-4xl font-bold text-on-light">{{ $price }}</span>
                                @if ($suffix)<span class="text-on-light-subtle text-sm ml-1">{{ $suffix }}</span>@endif
                            @endif
                        </p>
                        <p class="text-sm text-on-light-subtle mb-6">{{ $desc }}</p>
                        <ul class="space-y-2.5 mb-8 flex-1">
                            @foreach ($features as $f)
                                <li class="flex items-start gap-2 text-sm text-on-light-muted">
                                    <svg class="w-4 h-4 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $f }}
                                </li>
                            @endforeach
                        </ul>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="{{ $featured ? 'landing-cta w-full text-center' : 'inline-flex items-center justify-center w-full rounded-full border-2 border-green-600 text-green-700 font-semibold py-3 text-sm hover:bg-green-50 transition-colors' }}">
                                Počnite besplatno
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="max-w-5xl mx-auto rounded-2xl border border-gray-200 bg-white p-6">
                <p class="text-sm font-semibold text-on-light mb-4">U svakom paketu je uključeno:</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach (['Bez ugovorne obaveze', 'Bez naknade za postavljanje', 'Podrška na srpskom jeziku', 'Pomoć pri prvom unosu'] as $incl)
                        <div class="flex items-center gap-2 text-sm text-on-light-muted">
                            <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $incl }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="landing-section bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="landing-eyebrow">Česta pitanja</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light">
                    Imate pitanja pre registracije?
                </h2>
            </div>
            <div class="space-y-3">
                @foreach ([
                    ['Šta je dnevna evidencija otpada i ko je vodi?', 'Dnevna evidencija otpada je obaveza pravnih lica koja u svom poslovanju stvaraju otpad. U aplikaciji je vodite kroz jednostavan unos — datum, indeksni broj, vrsta i količina otpada — a stanje na skladištu obračunava se automatski, bez ručnog sabiranja.'],
                    ['Kako nastaje DKO dokument?', 'Kada otpad predajete ovlašćenom operateru, iz aplikacije šaljete zahtev za predaju sa podacima o vrsti i masi otpada. Naš tim preuzima zahtev, proverava podatke i izdaje dokument o kretanju otpada, koji preuzimate u PDF-u. Status zahteva vidite sve vreme.'],
                    ['Kako se pravi GIO1 godišnji izveštaj?', 'GIO1 se formira iz vaših dnevnih unosa za izabranu godinu — po vrstama otpada, količinama i operaterima kojima je otpad predat. Dodajete podatke o odgovornim licima i preuzimate izveštaj. Rok za dostavljanje je do 31. marta za prethodnu godinu.'],
                    ['Da li podržavate građevinski otpad?', 'Da, i to kao poseban modul. Svako gradilište ima svoju kartoteku sa brojem građevinske dozvole, investitorom i izvođačem, katalog otpada grupe 17 sa najčešćim šiframa, procenu tonaže prema tipu radova i zahteve za DKO vezane za to gradilište.'],
                    ['Mogu li da vodim više firmi u jednom nalogu?', 'Da. Nalog može da sadrži više firmi, a podaci svake firme su potpuno odvojeni. To je najčešći scenario kod konsultanata i knjigovodstvenih agencija koje evidenciju vode za više klijenata.'],
                    ['Mogu li kolege da unose podatke, a da ne vide sve ostalo?', 'Mogu. Postoje dve uloge — administrator firme, koji ima pun pristup, i evidenciar, koji može samo da unosi dnevne izveštaje o otpadu. Kolege pozivate mejlom iz aplikacije.'],
                    ['Mogu li da izvezem podatke iz aplikacije?', 'Da. Evidencija i izveštaji se preuzimaju u PDF-u i Excel-u — pojedinačno, mesečno ili za celu godinu. Dokumenti ostaju sačuvani u aplikaciji i kada ih preuzmete.'],
                    ['Šta ako sam do sada vodio evidenciju u Excel-u?', 'Nije problem — možete da nastavite od tekućeg perioda i da postojeće podatke unesete kada vam odgovara. Ako vam treba pomoć oko početnog unosa, javite nam se i provešćemo vas kroz prve korake.'],
                    ['Mogu li da koristim aplikaciju besplatno?', 'Starter paket je besplatan za jednu firmu i do 3 korisnika i uključuje dnevnu evidenciju, DKO dokumente, registar operatera i izvoz u PDF i Excel. Kreditna kartica nije potrebna.'],
                    ['Kako se štite podaci moje firme?', 'Podaci su odvojeni po firmama — korisnik vidi isključivo podatke svoje organizacije. Pristup zahteva prijavu i potvrđen nalog, a komunikacija sa aplikacijom ide preko šifrovane HTTPS veze.'],
                ] as $faqIndex => $faq)
                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <button type="button" data-faq-toggle aria-expanded="false"
                            class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left font-medium text-on-light hover:bg-gray-50 transition-colors">
                            {{ $faq[0] }}
                            <svg data-faq-icon class="landing-faq-icon w-5 h-5 text-on-light-subtle shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div data-faq-panel class="landing-faq-panel border-t border-gray-100">
                            <p class="px-5 py-4 text-sm text-on-light-muted leading-relaxed">{{ $faq[1] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="text-center text-sm text-on-light-muted mt-8">
                Niste našli odgovor? Pišite nam na
                <a href="mailto:info@ekoevidencija.rs" class="font-semibold text-green-700 hover:text-green-800">info@ekoevidencija.rs</a>
                — odgovaramo istog radnog dana.
            </p>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="landing-on-dark py-20 lg:py-24 bg-gradient-to-br from-green-700 to-green-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl sm:text-4xl font-bold text-on-dark mb-4">
                Spremni da pojednostavite evidenciju otpada?
            </h2>
            <p class="text-on-dark-muted text-lg mb-8 max-w-xl mx-auto">
                Otvorite nalog, unesite prvu vrstu otpada i vidite kako izgleda kada dokumentacija radi za vas.
                Bez kreditne kartice i bez obaveze.
            </p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="landing-cta landing-cta--hero text-base px-10 py-4">
                    Počnite besplatno
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            @endif
            <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-on-dark-subtle">
                @foreach (['Postavljanje za nekoliko minuta', 'Podrška na srpskom jeziku', 'Otkazivanje u svakom trenutku'] as $reassure)
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 text-on-dark-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $reassure }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer id="kontakt" class="landing-footer">
        <div class="border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                <a href="#pocetna" class="flex items-center gap-2 text-on-dark hover:text-on-dark">
                    <svg class="w-6 h-6 text-on-dark-accent" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1.1-2.5c.43 1.05 1.18 2.15 2.65 2.15 1.55 0 2.73-1.35 3.9-2.7.9-1.05 1.85-2.15 3.35-2.15 1.25 0 2.05.75 2.9 1.6.75.75 1.55 1.55 2.75 1.55 2.05 0 3.35-2.05 4.7-4.05C22.5 13.5 21.5 8 17 8z"/></svg>
                    <span class="font-bold">Biologist</span>
                </a>
                <div class="flex flex-wrap items-center justify-center gap-6">
                    <a href="mailto:info@ekoevidencija.rs">info@ekoevidencija.rs</a>
                    <a href="tel:+38111123456">+381 11 123 456</a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid md:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <p class="text-sm leading-relaxed mb-4">
                    Digitalna platforma za evidenciju otpada i prateću dokumentaciju, prilagođena propisima u Srbiji.
                    Dnevna evidencija, DKO dokumenti, godišnji izveštaj i građevinski otpad — na jednom mestu.
                </p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-on-dark-accent hover:text-on-dark transition-colors">
                        Otvorite besplatan nalog
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @endif
            </div>
            <div>
                <h4 class="font-semibold mb-4">Brzi linkovi</h4>
                <ul class="space-y-2.5 text-sm">
                    @foreach ([
                        '#pocetna' => 'Početna',
                        '#problemi' => 'Problemi koje rešavamo',
                        '#usluge' => 'Rešenja',
                        '#kako-radi' => 'Kako radi',
                        '#cene' => 'Cene',
                        '#faq' => 'FAQ',
                    ] as $href => $label)
                        <li><a href="{{ $href }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Oblasti</h4>
                <ul class="space-y-2.5 text-sm">
                    <li>Dnevna evidencija otpada</li>
                    <li>DKO — kretanje otpada</li>
                    <li>GIO1 godišnji izveštaj</li>
                    <li>Građevinski otpad i gradilišta</li>
                    <li>Plan upravljanja otpadom</li>
                    <li>Registar operatera</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Kontakt</h4>
                <ul class="space-y-2.5 text-sm mb-4">
                    <li>Beograd, Srbija</li>
                    <li>Pon–Pet: 09:00–17:00</li>
                    <li><a href="mailto:info@ekoevidencija.rs">info@ekoevidencija.rs</a></li>
                    <li><a href="tel:+38111123456">+381 11 123 456</a></li>
                </ul>
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-on-dark-accent hover:text-on-dark transition-colors">Idite na dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-on-dark-accent hover:text-on-dark transition-colors">Prijava za postojeće korisnike</a>
                @endauth
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex gap-4">
                    @foreach (['X' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'] as $label => $path)
                        <a href="#" class="text-on-dark-muted hover:text-on-dark transition-colors" aria-label="{{ $label }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
                        </a>
                    @endforeach
                </div>
                <p class="text-sm text-on-dark-muted">© {{ date('Y') }} Biologist / EkoEvidencija. Sva prava zadržana.</p>
            </div>
        </div>
    </footer>

    {{-- Sticky mobile CTA --}}
    @if (Route::has('register'))
        <div class="landing-sticky-cta">
            <a href="{{ route('register') }}" class="landing-cta w-full text-center">Počnite besplatno</a>
        </div>
    @endif
</body>
</html>
