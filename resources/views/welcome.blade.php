<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Biologist') }} — Digitalna evidencija otpada</title>
    <meta name="description" content="Digitalna evidencija otpada za pravna lica u Srbiji — DEO obrasci, godišnji izveštaji, DOKO dokumenti. Usklađeno sa SEPA propisima.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Inter',sans-serif] text-on-light antialiased bg-white">

    {{-- Navigation — CSS klase (bez Alpine); scroll menja .landing-nav--light --}}
    <nav id="landing-nav" class="landing-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-[4.5rem]">
                <a href="#pocetna" class="flex items-center gap-2.5">
                    <svg class="w-8 h-8 shrink-0 landing-nav__logo-icon" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1.1-2.5c.43 1.05 1.18 2.15 2.65 2.15 1.55 0 2.73-1.35 3.9-2.7.9-1.05 1.85-2.15 3.35-2.15 1.25 0 2.05.75 2.9 1.6.75.75 1.55 1.55 2.75 1.55 2.05 0 3.35-2.05 4.7-4.05C22.5 13.5 21.5 8 17 8z"/></svg>
                    <span class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-lg landing-nav__logo-text">Biologist</span>
                </a>

                <div class="hidden lg:flex items-center gap-8">
                    @foreach (['#pocetna' => 'Početna', '#o-nama' => 'O aplikaciji', '#usluge' => 'Usluge', '#cene' => 'Cene', '#faq' => 'FAQ'] as $href => $label)
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

                <button id="landing-nav-toggle" type="button" class="landing-nav__menu-btn lg:hidden p-2 rounded-lg" aria-label="Meni" aria-expanded="false" aria-controls="landing-nav-mobile">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div id="landing-nav-mobile" class="landing-nav__mobile lg:hidden bg-white border-t border-gray-200 shadow-lg">
            <div class="px-4 py-4 space-y-1">
                @foreach (['#pocetna' => 'Početna', '#o-nama' => 'O aplikaciji', '#usluge' => 'Usluge', '#cene' => 'Cene', '#faq' => 'FAQ'] as $href => $label)
                    <a href="{{ $href }}" class="block text-on-light-muted py-2.5 font-medium hover:text-green-700 transition-colors">{{ $label }}</a>
                @endforeach
                <div class="pt-4 border-t border-gray-100 flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="text-center py-2.5 text-on-light-muted font-medium hover:text-green-700 transition-colors">Prijava</a>
                    <a href="{{ route('register') }}" class="landing-cta w-full text-center">Počnite besplatno</a>
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
                    <p class="text-on-dark-accent text-sm font-semibold uppercase tracking-[0.12em] mb-4">Digitalna evidencija otpada za pravna lica</p>
                    <h1 class="font-['Plus_Jakarta_Sans',sans-serif] text-4xl sm:text-5xl lg:text-[3.25rem] font-bold leading-[1.1] mb-6 text-on-dark">
                        Usklađenost sa zakonom<br>
                        <span class="text-on-dark-accent">bez papirne birokratije</span>
                    </h1>
                    <p class="text-lg text-on-dark-muted leading-relaxed mb-8 max-w-xl">
                        Vodite DEO1–DEO6 obrasce, generišite GIO1 godišnje izveštaje i DOKO dokumente —
                        sve na jednom mestu, uvek ispravno prema SEPA propisima.
                    </p>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-10">
                        <a href="{{ route('register') }}" class="landing-cta landing-cta--hero">
                            Počnite besplatno
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="#kako-radi" class="landing-cta landing-cta--outline justify-center">
                            Pogledajte kako radi
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach ([['5.000+', 'Dokumenata'], ['1.200+', 'Firmi'], ['100%', 'Usklađenost'], ['24/7', 'Pristup']] as [$num, $lbl])
                            <div class="landing-stat-card">
                                <p class="font-['Plus_Jakarta_Sans',sans-serif] text-xl sm:text-2xl font-bold text-on-dark">{{ $num }}</p>
                                <p class="text-xs font-medium text-on-dark-muted mt-0.5">{{ $lbl }}</p>
                            </div>
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
                                    <span class="px-3 py-1.5 rounded-lg bg-white text-gray-500 text-xs border">DOKO</span>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl">
                @foreach ([
                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Automatski DEO obrasci'],
                    ['icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'title' => 'PDF i Excel izveštaji'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Usklađeno sa SEPA propisima'],
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

    {{-- O aplikaciji --}}
    <section id="o-nama" class="landing-section bg-[#f9fafb]">
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
                    <div class="absolute -bottom-4 -right-2 sm:right-4 bg-white rounded-xl shadow-lg border border-gray-100 px-4 py-3">
                        <p class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-green-600">98%</p>
                        <p class="text-xs text-on-light-muted">Tačnost dokumenata</p>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <p class="landing-eyebrow">O aplikaciji</p>
                    <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light leading-tight mb-6">
                        Jedina platforma prilagođena propisima o otpadu u Srbiji
                    </h2>
                    <p class="text-on-light-muted leading-relaxed mb-8">
                        Razvijena u saradnji sa ekolozima i pravnicima, u skladu sa Zakonom o upravljanju otpadom,
                        pravilnicima o evidenciji i zahtevima SEPA inspekcije.
                    </p>
                    <ul class="space-y-4 mb-8">
                        @foreach ([
                            'Evidencija za sve tipove subjekata (DEO1–DEO6)',
                            'Automatsko generisanje GIO1 i DOKO dokumenata',
                            'Obrasci se ažuriraju kada se menja regulativa',
                        ] as $item)
                            <li class="flex items-start gap-3 text-on-light-muted">
                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="landing-cta">Počnite besplatno</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Usluge --}}
    <section id="usluge" class="landing-section bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="landing-eyebrow">Naše usluge</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light">
                    Sve što vam treba za zakonitu evidenciju otpada
                </h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach ([
                    ['path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Dnevna evidencija (DEO)', 'desc' => 'Unosite podatke o otpadu kroz intuitivan interfejs. Automatski se kreira DEO1–DEO6 obrazac.'],
                    ['path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Godišnji izveštaj (GIO1)', 'desc' => 'Na osnovu dnevnih unosa generišite GIO1 spreman za dostavljanje SEPA — do 31. marta.'],
                    ['path' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'title' => 'DOKO — kretanje otpada', 'desc' => 'Pratite svaki transport opasnog otpada, generišite DOKO dokumente i vodite kompletnu evidenciju predaje.'],
                ] as $card)
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md hover:border-green-100 transition-all">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600 mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $card['path'] }}"/></svg>
                        </span>
                        <h3 class="font-['Plus_Jakarta_Sans',sans-serif] font-bold text-lg text-on-light mb-2">{{ $card['title'] }}</h3>
                        <p class="text-sm text-on-light-muted leading-relaxed">{{ $card['desc'] }}</p>
                    </div>
                @endforeach
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
                        Tri koraka do potpune usklađenosti
                    </h2>
                    <div class="space-y-6 mb-8">
                        @foreach ([
                            ['1', 'Registrujte firmu', 'Unesite PIB, matični broj i podatke o preduzeću. Pozovite kolege u tim.'],
                            ['2', 'Unosite otpad dnevno', 'Svaki unos traje manje od 2 minuta. Stanje skladišta se računa automatski.'],
                            ['3', 'Generišite izveštaje', 'GIO1, DOKO i PDF dokumenti — jednim klikom, spremni za inspekciju.'],
                        ] as [$step, $title, $desc])
                            <div class="flex gap-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-600 text-white font-bold text-sm">{{ $step }}</span>
                                <div>
                                    <p class="font-semibold text-on-light">{{ $title }}</p>
                                    <p class="text-sm text-on-light-muted mt-1">{{ $desc }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('register') }}" class="landing-cta">Počnite besplatno</a>
                </div>
                <div class="landing-mockup">
                    <div class="landing-mockup__chrome">
                        <span class="text-xs text-gray-500 font-medium">Korak po korak</span>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach ([['✓', 'Firma registrovana', 'bg-green-600 text-white'], ['2', 'Dnevni unos otpada', 'bg-green-100 text-green-800 border-2 border-green-500'], ['3', 'GIO1 generisan', 'bg-gray-100 text-gray-400']] as [$badge, $label, $cls])
                            <div class="flex items-center gap-4 rounded-xl px-4 py-3 {{ $cls }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/30 font-bold text-sm">{{ $badge }}</span>
                                <span class="font-medium text-sm">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="landing-section bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="landing-eyebrow">Iskustva korisnika</p>
                <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl lg:text-4xl font-bold text-on-light">
                    Firme koje su pojednostavile evidenciju
                </h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach ([
                    ['M', 'Marija Jovanović', 'EkoLog d.o.o.', '„Pre Biologist-a smo trošili dane na ručno popunjavanje DEO obrazaca. Sada generišemo GIO1 za ceo tim za manje od sat vremena."', 'bg-emerald-600'],
                    ['P', 'Petar Nikolić', 'MetalPro AD', '„Inspekcija je prošla bez primedbi. Svi DOKO dokumenti i dnevna evidencija bili su na jednom mestu, uredno arhivirani."', 'bg-green-700'],
                    ['A', 'Ana Stojanović', 'HemLab d.o.o.', '„Konačno alat koji razume srpske propise — indeksni brojevi, R/D oznake, sve je već ugrađeno u sistem."', 'bg-teal-600'],
                ] as [$initial, $name, $company, $quote, $color])
                    <blockquote class="rounded-2xl border border-gray-100 bg-[#f9fafb] p-6 flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full {{ $color }} text-white font-bold text-sm">{{ $initial }}</span>
                            <div>
                                <p class="font-semibold text-on-light text-sm">{{ $name }}</p>
                                <p class="text-xs text-on-light-subtle">{{ $company }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-on-light-muted leading-relaxed flex-1">{{ $quote }}</p>
                        <div class="flex gap-0.5 mt-4 text-amber-400" aria-label="5 zvezdica">
                            @for ($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </blockquote>
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
                <p class="text-on-light-muted">Počnite besplatno. Nadogradite kada vam zatreba više timova ili naprednih funkcija.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                @foreach ([
                    ['Starter', '0', 'Besplatno', '1 firma, do 3 korisnika', ['Dnevna evidencija', 'DOKO dokumenti', 'PDF izvoz'], false],
                    ['Poslovni', '4.900', 'RSD / mesec', 'Do 5 korisnika po firmi', ['Sve iz Starter paketa', 'GIO1 godišnji izveštaj', 'Prioritetna podrška'], true],
                    ['Enterprise', 'Po dogovoru', '', 'Neograničeno korisnika', ['Više firmi u jednom nalogu', 'Posvećen account manager', 'SLA i integracije'], false],
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
                                <li class="flex items-center gap-2 text-sm text-on-light-muted">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $f }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="{{ $featured ? 'landing-cta w-full text-center' : 'inline-flex items-center justify-center w-full rounded-full border-2 border-green-600 text-green-700 font-semibold py-3 text-sm hover:bg-green-50 transition-colors' }}">
                            Počnite besplatno
                        </a>
                    </div>
                @endforeach
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
                    ['Šta su DEO obrasci i ko ih mora voditi?', 'DEO (Dnevna Evidencija Otpada) obrazac je zakonska obaveza za proizvođače otpada u Srbiji. Biologist automatski generiše DEO1–DEO6 u skladu sa tipom vašeg subjekta.'],
                    ['Da li je GIO1 izveštaj usklađen sa SEPA zahtevima?', 'Da. GIO1 se generiše prema zvaničnom obrascu sa svim sekcijama — klasifikacija otpada, predaja operaterima i podaci o firmi. Rok za dostavu je 31. mart tekuće godine.'],
                    ['Mogu li koristiti aplikaciju besplatno?', 'Starter paket je potpuno besplatan za jednu firmu i do 3 korisnika. Uključuje dnevnu evidenciju, DOKO dokumente i PDF izvoz.'],
                    ['Kako se štite podaci moje firme?', 'Podaci su izolovani po timovima (firmama). Svaki korisnik vidi samo podatke svoje organizacije. Komunikacija je šifrovana HTTPS protokolom.'],
                    ['Da li podržavate opasan otpad i DOKO?', 'Da. Aplikacija pokriva kompletan tok — od dnevne evidencije, preko zahteva za predaju, do generisanja DOKO dokumenta o kretanju opasnog otpada.'],
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
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="landing-on-dark py-20 lg:py-24 bg-gradient-to-br from-green-700 to-green-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-3xl sm:text-4xl font-bold text-on-dark mb-4">
                Spremni da pojednostavite evidenciju otpada?
            </h2>
            <p class="text-on-dark-muted text-lg mb-8 max-w-xl mx-auto">
                Registrujte se besplatno i počnite sa dnevnom evidencijom već danas. Bez kreditne kartice.
            </p>
            <a href="{{ route('register') }}" class="landing-cta landing-cta--hero text-base px-10 py-4">
                Počnite besplatno
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
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
                <p class="text-sm leading-relaxed mb-4">Digitalna platforma za evidenciju otpada u skladu sa srpskim propisima. Jednostavno, sigurno i uvek ažurno.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Brzi linkovi</h4>
                <ul class="space-y-2.5 text-sm">
                    @foreach (['#pocetna' => 'Početna', '#o-nama' => 'O aplikaciji', '#cene' => 'Cene', '#faq' => 'FAQ', '#kontakt' => 'Kontakt'] as $href => $label)
                        <li><a href="{{ $href }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Oblasti</h4>
                <ul class="space-y-2.5 text-sm">
                    <li>Dnevna evidencija (DEO)</li>
                    <li>Godišnji izveštaji (GIO1)</li>
                    <li>DOKO dokumenti</li>
                    <li>Opasan otpad</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Kontakt</h4>
                <ul class="space-y-2.5 text-sm mb-4">
                    <li>Beograd, Srbija</li>
                    <li>Pon–Pet: 09:00–17:00</li>
                </ul>
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
