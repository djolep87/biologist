# CLAUDE.md

Ovaj fajl daje uputstva Claude Code-u (claude.ai/code) za rad sa kodom u ovom repozitorijumu.

## Projekat

Biologist je Laravel + Livewire web aplikacija za autsors upravljanje industrijskim otpadom. Firme, fabrike, prodavnice, zanatske radnje i gradilišta u Srbiji koji su zakonski obavezni da tretiraju otpad je koriste da digitalizuju svoju papirologiju oko otpada. Klijentski timovi unose dnevne evidencije otpada ("dnevna evidencija") i šalju zahteve za odvoz ("zahtevi"); super-admin (operater aplikacije) obrađuje te zahteve, povezuje ih sa operaterima otpada ("operateri") i generiše zvanična regulatorna dokumenta (DKO, GIO1, Deo1) propisana zakonom o upravljanju otpadom u Srbiji. Vidi [moj-projekat.md](moj-projekat.md) za viziju proizvoda u rečima osnivača.

Domen i UI su u potpunosti na srpskom jeziku (latinica) — imena modela/metoda/ruta, validacione poruke i PDF dokumenti prate ovu konvenciju; novi kod treba da bude u skladu sa tim, a ne prevođen na engleski.

## Komande

```bash
composer setup          # prva instalacija: composer install, .env, key:generate, migrate, npm install/build
composer dev             # pokreće server + queue listener + pail logove + vite, paralelno (glavna komanda za lokalni razvoj)
php artisan serve        # samo app server
npm run dev               # samo vite dev server
npm run build              # produkcioni build frontenda

composer test             # config:clear + php artisan test (ceo test suite)
php artisan test --filter=TestName        # pojedinačni test
php artisan test tests/Feature/SomeTest.php

vendor/bin/pint            # formatiranje PHP koda (Laravel Pint, bez custom pint.json)
```

Testovi se izvršavaju nad in-memory SQLite bazom (vidi `phpunit.xml`), a ne nad MySQL bazom za razvoj.

## Arhitektura

**Dve paralelne "strane" aplikacije, razdvojene middleware-om, a ne odvojene aplikacije:**
- Klijentska strana (`routes/web.php`, skopirana po timu): dashboard, `evidencija` (dnevna evidencija otpada), `gradilista` (građevinska gradilišta), `dko-zahtevi` (zahtevi za odvoz građevinskog otpada). Zahteva `auth`, `verified`, `ensure.has.team`.
- Admin strana (`admin.*` rute, `App\Http\Controllers\Admin\*`): njom upravlja vlasnik aplikacije/super-admin, ne klijent. Zahteva `super.admin` middleware (`App\Http\Middleware\SuperAdmin`, gate `super-admin`). Obuhvata upravljanje klijentima, upravljanje operaterima, obradu zahteva i generisanje dokumenata/izveštaja (DKO, GIO1, planovi upravljanja otpadom).

**Multi-tenancy je rešen preko Jetstream Teams, jedan tim po klijentskoj firmi.** `App\Models\Team` nasleđuje `JetstreamTeam`. Skoro svi domenski modeli (`DnevnaEvidencija`, `DokumentKretanja`, `ConstructionSite`, `GradjevinskiDkoZahtev`, `ZahtevPredaje`, `GodisnjIzvestaj`) pripadaju timu (`Team`) i imaju `scopeForTeam()`. Upiti skopirani po timu treba da idu preko tog scope-a, a ne preko proizvoljnog `where('team_id', ...)`.

**Kontrola pristupa se nalazi u `App\Support\TeamAccess`**, ne samo u policy klasama — centralizuje proveru uloga (`ROLE_ADMIN`, `ROLE_EVIDENCIAR`) i dozvola (npr. `evidencija:create`) iznad Jetstream-ovih timskih uloga, plus super-admin bypass. Koristiti njene statičke metode (`canAccessTeam`, `hasFullTeamAccess`, `canManageEvidencija`, `canCreateEvidencija`) umesto ponovnog implementiranja logike uloga. `EnsureFullTeamAccess` middleware koristi `hasFullTeamAccess()` da blokira korisnike koji su samo "evidenciar" na fiksnoj listi ograničenih route-name pattern-a (exportovi, PDF-ovi, podešavanja tima).

**`App\Support\AdminTeamContext`** čuva klijentski tim koji super-admin trenutno "gleda kao" u session state-u, odvojeno od `currentTeam` prijavljenog korisnika. Admin Livewire komponente/kontroleri kojima treba "kog klijenta trenutno gledam" čitaju odavde, a ne preko `auth()->user()->currentTeam`.

**Dva domena praćenja otpada koji dele infrastrukturu, ali se razlikuju u toku rada (workflow):**
- Obična ("obična") otpad: `DnevnaEvidencija` zapisi → grupišu se u `ZahtevPredaje` (zahtev za odvoz) koji šalje klijent → admin ga obrađuje u `DokumentKretanja` (stvarni dokument o kretanju otpada/DKO).
- Građevinski ("gradjevinski") otpad: vezan za `ConstructionSite`, prati se preko `GradjevinskiDeo1` zapisa → zahteva se preko `GradjevinskiDkoZahtev` → admin odobrava i generiše DKO. I `DnevnaEvidencija` i `DokumentKretanja` imaju `isGradjevinski()`/scope-ove (`scopeObicna`/`scopeGradjevinska`) koji razdvajaju ta dva slučaja, pošto dele iste tabele.

**Generisanje regulatornih dokumenata je zasnovano na template-ima**, a ne generisano od nule: `DokoExportService`, `EvidencijaExportService` i `Gio1ExportService` popunjavaju unapred pripremljene `.xlsx` template-e u `resources/templates/` (preko PhpSpreadsheet-a) koristeći fiksne koordinate ćelija — proveriti `TEMPLATE_PATH` i konstante ćelija na vrhu svakog servisa pre menjanja rasporeda. `PdfController` i `DokoController` obmotavaju ove servise kao download-e.

**Planovi upravljanja otpadom se generišu preko LLM-a**: `WastePlanGeneratorService` poziva OpenAI Chat Completions API (`config('services.openai.*')`, ključ `OPENAI_API_KEY`) sa dugačkim srpskim system prompt-om iz `WastePlanPromptBuilder`, tražeći dugačak pravni dokument (cilj 6000+ reči / 15 strana) pisan na srpskom jeziku. Pošto izlaz modela može biti kraći ili preskočiti strane, `WastePlanContentParser` proverava broj reči i koje `[Strana N od 15]` oznake strana postoje, a servis šalje dodatne (continuation) pozive da popuni praznine — pogledati petlju u `generate()` pre menjanja prompt-a ili logike parsiranja.

**Livewire, a ne JS SPA, pokreće interaktivnost**: komponente u `app/Livewire/` (klijent) i `app/Livewire/Admin/` (admin) su osnova za većinu interaktivnih formi i tabela (`DnevnaEvidencijaForm`, `AdminEvidencijeTable`, `Gio1Generator`, `WastePlanGenerator`, itd.). Kontroleri uglavnom renderuju obmotavajući Blade view; Livewire komponente drže stvarno stanje/ponašanje.

**Notifikacije** (`app/Notifications/`) pokreću komunikaciju klijent↔admin — npr. `NoviZahtevPredaje`/`ZahtevObradjeni`/`ZahtevOdbijen` za obične zahteve i njihovi `GradjevinskiDkoZahtev*` ekvivalenti — prikazuju se u aplikaciji preko `NotificationBell`.
