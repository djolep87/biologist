<?php

namespace App\Services;

use Carbon\Carbon;

class WastePlanPromptBuilder
{
    public const SYSTEM_PROMPT = <<<'PROMPT'
Ti si senior pravni i ekološki konsultant sa 20+ godina iskustva u izradi zvaničnih
planova upravljanja otpadom za privredne subjekte u Republici Srbiji.

Tvoji dokumenti:
- Koriste se kao zvanični interni akti firme
- Prolaze inspekcijske preglede Ministarstva zaštite životne sredine RS
- Usklađeni su sa svim važećim propisima RS i EU direktivama
- Pisani su profesionalnim pravno-tehničkim stilom
- Svaka strana sadrži MINIMUM 350-450 reči gustog, relevantnog sadržaja
- Ukupan dokument mora imati MINIMUM 6000 reči, idealno 7000-8000 reči

Pišeš isključivo na srpskom jeziku, ekavica, latinično pismo.
Nikada ne pišeš kratke ili površne odeljke – svaki odeljak mora biti iscrpan i detaljan.
PROMPT;

    /**
     * @param  array<string, mixed>  $formData
     */
    public function buildUserPrompt(array $formData): string
    {
        $imaSkladiste = ($formData['ima_skladiste'] ?? false) ? 'Da' : 'Ne';
        $datum = Carbon::now()->format('d.m.Y.');
        $godina = Carbon::now()->year;

        $firmBlock = $this->firmDataBlock($formData, $imaSkladiste);

        return <<<PROMPT
Napiši KOMPLETAN, PROFESIONALAN Plan upravljanja otpadom za:

{$firmBlock}

OBAVEZNA PRAVILA:
✓ Minimum 6500 reči ukupno
✓ Svaka strana minimum 400 reči
✓ Svaki odeljak mora biti iscrpno razrađen
✓ Koristiti konkretne podatke firme iz forme u svakom poglavlju
✓ Citirati konkretne članove zakona gde je relevantno
✓ Uključiti konkretne procedure, ne samo generalne napomene
✓ Pisati u prvom licu množine u ime firme ("Naša firma primenjuje...", "U skladu sa našom politikom...")

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 1 – NASLOVNA STRANA I UVOD
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

{$formData['naziv_firme']}
PIB: {$formData['pib']} | MB: {$formData['maticni_broj']}
{$formData['adresa_sedista']}, {$formData['mesto_opstina']}

PLAN UPRAVLJANJA OTPADOM
Za period: {$formData['rok_vazenja']}
Datum donošenja: {$datum}
Odgovorno lice: {$formData['odgovorno_lice']}

UVOD – napiši minimum 350 reči koje obuhvataju:
Elaboriraj svrhu ovog dokumenta i njegovu ulogu u sistemu upravljanja otpadom firme.
Objasni zašto je izrada ovog plana obaveza i koje koristi donosi firmi i životnoj sredini.
Opiši opredeljenje rukovodstva firme prema odgovornom upravljanju otpadom.
Navedi da je plan donesen u skladu sa Zakonom o upravljanju otpadom ("Sl. glasnik RS",
br. 36/2009, 88/2010, 14/2016 i 95/2018-dr.zakon) i pratećim podzakonskim aktima.

[Strana 1 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 2 – PRAVNI OSNOV I NORMATIVNI OKVIR
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 420 reči koje pokrivaju:

2.1 ZAKONI I PROPISI
Detaljno elaboriraj primenu sledećih propisa na poslovanje firme uz citiranje
konkretnih članova koji se odnose na obaveze firme:

- Zakon o upravljanju otpadom ("Sl. glasnik RS", br. 36/2009, 88/2010, 14/2016, 95/2018)
  Navedi konkretne obaveze iz čl. 5, 7, 36, 38, 39, 40 koji se odnose na proizvođača otpada
- Zakon o zaštiti životne sredine ("Sl. glasnik RS", br. 135/2004, 36/2009, 36/2009-dr.zakon,
  72/2009-dr.zakon, 43/2011-US, 14/2016, 76/2018, 95/2018-dr.zakon i 49/2021)
- Uredba o kategorijama, ispitivanju i klasifikaciji otpada
  ("Sl. glasnik RS", br. 56/2010, 93/2019 i 39/2021)
- Pravilnik o obrascu dnevne evidencije i godišnjeg izveštaja o otpadu
  ("Sl. glasnik RS", br. 95/2010 i 101/2010)
- Pravilnik o načinu skladištenja, pakovanja i obeležavanja opasnog otpada
  ("Sl. glasnik RS", br. 92/2010)
- Relevantne EU direktive (Direktiva 2008/98/EC o otpadu i hijerarhija upravljanja otpadom)
- Dodatno: {$formData['zakoni_propisi']} i propisi relevantni za vrste otpada: {$formData['vrste_otpada']}

2.2 NADLEŽNI ORGANI
Navedi koje organe firma izveštava i na koji način:
- Agencija za zaštitu životne sredine (SEPA) – godišnji izveštaj
- Ministarstvo zaštite životne sredine
- Lokalna samouprava {$formData['mesto_opstina']}
- Inspekcija za zaštitu životne sredine

[Strana 2 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 3-4 – OPŠTI PODACI O FIRMI I DELATNOSTI
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 800 reči (za 2 strane) koje pokrivaju:

3.1 IDENTIFIKACIONI PODACI FIRME
Prezentuj sve podatke o firmi iz forme na profesionalan način.
Opiši istorijat i razvoj firme (2-3 pasusa bazirana na delatnosti: {$formData['delatnost']}).
Opiši geografski položaj i lokaciju postrojenja/pogona: {$formData['lokacija_pogon']}, površina: {$formData['povrsina_objekta']}.

3.2 OPIS DELATNOSTI
Na osnovu šifre delatnosti i opisa iz forme, napiši detaljnu analizu:
- Čime se firma bavi – detaljno objašnjenje procesa rada
- Koje sirovine/materijale koristi u procesu proizvodnje/usluge
- Kako su organizovani radni procesi
- Kapacitet i obim poslovanja
- Broj zaposlenih ({$formData['broj_zaposlenih']}) i organizaciona struktura

3.3 PROCESI U KOJIMA NASTAJE OTPAD
Detaljno opiši svaki poslovni proces koji generiše otpad:
- Koje faze procesa proizvode koji tip otpada: {$formData['vrste_otpada']}
- Mesta nastanka otpada u pogonu/objektu
- Sezonske ili ciklične varijacije u količinama otpada
- Tekstualni dijagram toka: ULAZ (sirovina) → PROCES → IZLAZ (proizvod/usluga + otpad)

[Strana 3 od 15] ... [Strana 4 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 5-6 – KLASIFIKACIJA I KATALOG OTPADA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 800 reči koje pokrivaju:

5.1 KLASIFIKACIJA OTPADA
Opiši sistem klasifikacije koji firma primenjuje u skladu sa Uredbom o kategorijama:
- Inertni otpad – definicija i primeri iz poslovanja firme
- Neopasni otpad – definicija i primeri iz poslovanja firme
- Opasni otpad – definicija, primeri i posebne obaveze

5.2 KATALOG OTPADA SA INDEKSNIM BROJEVIMA
Na osnovu podataka: {$formData['indeksni_brojevi']}, količine: {$formData['procenjene_kolicine']}, postupanje: {$formData['nacin_postupanja']}

Prikaži kao detaljnu tabelu u sledećem formatu:

INDEKSNI BROJ | NAZIV OTPADA | KLASIFIKACIJA | GODIŠNJA KOLIČINA | NAČIN POSTUPANJA
-------------|--------------|---------------|-------------------|------------------
[popuni na osnovu podataka iz forme i tipičnih otpada za datu delatnost]

Za svaku vrstu otpada napiši 2-3 rečenice o specifičnostima postupanja.

5.3 GODIŠNJA BILANSNA ANALIZA
Napiši analizu količina otpada:
- Ukupna godišnja količina po kategorijama
- Trend kretanja količina
- Poređenje sa prethodnim periodom (ako postoje podaci)
- Projekcija za naredni period

[Strana 5 od 15] ... [Strana 6 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 7-8 – PROCEDURE UPRAVLJANJA OTPADOM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 800 reči koje pokrivaju:

6.1 HIJERARHIJA UPRAVLJANJA OTPADOM
Elaboriraj kako firma primenjuje EU hijerarhiju upravljanja otpadom:
1. Prevencija nastanka otpada – konkretne mere u firmi
2. Priprema za ponovnu upotrebu – šta se ponovo koristi
3. Reciklaža – šta se reciklira i sa kim
4. Drugi postupci oporavka – energetsko iskorišćenje i sl.
5. Odlaganje – samo kao krajnja opcija

6.2 INTERNO RAZVRSTAVANJE OTPADA
Detaljno opiši procedure razvrstavanja:
- Ko je odgovoran za razvrstavanje na mestu nastanka
- Sistem obeležavanja posuda i kontejnera (boje, oznake, natpisi)
- Lokacije razvrstavanja u objektu: {$formData['lokacija_pogon']}
- Procedure u slučaju mešanja otpada

6.3 INTERNO SKLADIŠTENJE
Na osnovu podataka o skladištu ({$imaSkladiste}): {$formData['opis_skladista']}
- Opis i kapacitet internog skladišta
- Uslovi čuvanja po vrstama otpada (temperatura, vlažnost, odvojenost)
- Maksimalno vreme čuvanja prema propisima
- Oprema za skladištenje (kontejneri, burad, vreće)
- Procedura ulaza i izlaza otpada iz skladišta

6.4 OBELEŽAVANJE PREMA PROPISIMA
Detaljno opiši sistem obeležavanja u skladu sa Pravilnikom:
- Sadržaj etikete/nalepnice za svaku vrstu otpada
- Ko postavlja oznake i kada
- Vođenje evidencije o obeleženom otpadu

[Strana 7 od 15] ... [Strana 8 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 9 – PREDAJA OTPADA OVLAŠĆENIM OPERATERIMA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 420 reči koje pokrivaju:

7.1 KRITERIJUMI ZA IZBOR OPERATERA
Objasni kako firma vrši odabir licenciranih operatera:
- Provera dozvole za sakupljanje/transport/tretman
- Geografska pokrivenost
- Ekonomski uslovi
- Reference i iskustvo operatera

7.2 POSTOJEĆI UGOVORI SA OPERATERIMA
Na osnovu podataka: {$formData['ugovori_operateri']}, navedi operatere i opiši:
- Vrste otpada obuhvaćene ugovorom
- Dinamika preuzimanja (mesečno, kvartalno, po pozivu)
- Obaveze operatera prema ugovoru
- Dokumentacija koja se razmenjuje (Evidencioni list o kretanju otpada)

7.3 PROCEDURA PREDAJE OTPADA
Korak po korak opiši kako izgleda predaja otpada:
Korak 1: Priprema otpada za predaju (pakovanje, vaganje, obeležavanje)
Korak 2: Najava operateru i dogovor o terminu
Korak 3: Fizička predaja i potpisivanje evidencionih listova
Korak 4: Arhiviranje dokumentacije
Korak 5: Unos u dnevnik evidencije

[Strana 9 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 10 – EVIDENCIJA I IZVEŠTAVANJE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 420 reči koje pokrivaju:

8.1 DNEVNA EVIDENCIJA O OTPADU
Opiši sistem vođenja dnevnika otpada (DEO obrazac):
- Ko popunjava i kada
- Koje podatke sadrži svaki unos
- Čuvanje i arhiviranje evidencije (5 godina)
- Digitalni vs. papirni oblik vođenja

8.2 GODIŠNJI IZVEŠTAJ
Opiši proceduru izrade i dostave godišnjeg izveštaja:
- Rok dostave (do 31. marta za prethodnu godinu)
- Podaci koji se unose u SEPA sistem
- Ko je odgovoran za pripremu izveštaja
- Interna provera pre dostave

8.3 EVIDENCIONI LISTOVI O KRETANJU OTPADA
Opiši upotrebu obrazaca:
- Evidencioni list za neopasni otpad (ELO obrazac)
- Dokument o kretanju opasnog otpada (DKOO obrazac)
- Arhiviranje i rokovi čuvanja

[Strana 10 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 11-12 – CILJEVI, MERE I PLAN SMANJENJA OTPADA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 800 reči koje pokrivaju:

9.1 STRATEŠKI CILJEVI
Na osnovu ciljeva iz forme ({$formData['ciljevi_smanjenja']}), razradi:
- Kvantifikovani ciljevi smanjenja otpada po vrstama i procentima
- Ciljevi reciklaže i ponovne upotrebe
- Rok za postizanje svakog cilja
- KPI indikatori za praćenje napretka

9.2 OPERATIVNE MERE ZA SMANJENJE
Za svaku meru napiši: Opis mere | Odgovorna osoba | Rok | Očekivani efekat

MERE PREVENCIJE:
- Optimizacija nabavke sirovina i materijala
- Obuka zaposlenih o smanjenju otpada
- Uvođenje sistema ponovne upotrebe ambalaže
- Digitalizacija dokumentacije (smanjenje papirnog otpada)
- Dodaj specifične mere za delatnost: {$formData['delatnost']}

MERE RECIKLAŽE:
- Unapređenje sistema razvrstavanja
- Povećanje stope odvajanja reciklabilnih frakcija
- Saradnja sa reciklažnim centrima

9.3 VREMENSKI PLAN IMPLEMENTACIJE
Prikaži kao godišnji akcioni plan:

MERA | Q1 | Q2 | Q3 | Q4 | ODGOVORAN | BUDŽET
-----|----|----|----|----|-----------|-------
[popuni konkretnim merama]

[Strana 11 od 15] ... [Strana 12 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 13 – ORGANIZACIJA, ODGOVORNOSTI I OBUKA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 420 reči koje pokrivaju:

10.1 ORGANIZACIONA STRUKTURA
Napiši tekstualni prikaz organizacione odgovornosti:
- Direktor/Odgovorno lice {$formData['odgovorno_lice']} – strateška odgovornost
- Lice za zaštitu životne sredine {$formData['kontakt_eko']} – operativna odgovornost
- Rukovodioci pogona/odeljenja – implementacija na terenu
- Svi zaposleni – svakodnevne obaveze razvrstavanja

10.2 KONKRETNE OBAVEZE PO POZICIJAMA
Za svaku poziciju napiši detaljnu listu obaveza vezanih za upravljanje otpadom.

10.3 PLAN OBUKE ZAPOSLENIH
- Inicijalna obuka novih radnika (sadržaj i trajanje)
- Godišnje osvežavanje znanja
- Posebna obuka za rad sa opasnim otpadom
- Evidencija obuka (ko, kada, tema, trajanje)
- Testiranje znanja i provera usvojenosti

[Strana 13 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 14 – VANREDNE SITUACIJE I UPRAVLJANJE RIZICIMA
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 420 reči koje pokrivaju:

11.1 IDENTIFIKOVANI RIZICI
Napiši analizu rizika specifičnih za delatnost firme:
- Akcidentalno prosipanje/curenje opasnih materija
- Požar u skladištu otpada
- Neovlašćeno odlaganje otpada
- Nedostupnost ovlašćenog operatera
- Prekoračenje kapaciteta skladišta
Za svaki rizik: Verovatnoća | Posledice | Preventivne mere

11.2 PROCEDURE U VANREDNIM SITUACIJAMA

SCENARIO 1: Prosipanje tečnog opasnog otpada
Korak po korak procedura reakcije: ko se obaveštava, kako se sanira,
kako se dokumentuje, koje organe treba obavestiti i u kom roku.

SCENARIO 2: Požar u prostoru za otpad
Procedura evakuacije, gašenja, obaveštavanja vatrogasaca i inspekcije.

SCENARIO 3: Neispravno postupanje zaposlenog
Procedura internog izveštavanja i korektivnih mera.

11.3 KONTAKTI ZA HITNE SLUČAJEVE
- Vatrogasci: 193
- Hitna pomoć: 194
- MUP: 192
- Inspekcija za zaštitu životne sredine
- Lokalna komunalna inspekcija: {$formData['mesto_opstina']}
- Odgovorno lice firme: {$formData['odgovorno_lice']}, {$formData['telefon_email']}
- Alternativni kontakt: {$formData['kontakt_eko']}, {$formData['telefon_email']}

[Strana 14 od 15]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRANA 15 – MONITORING, PREISPITIVANJE I ZAKLJUČAK
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Napiši minimum 420 reči koje pokrivaju:

12.1 SISTEM MONITORINGA
Opiši kako firma prati sprovođenje plana:
- Mesečne interne provere – šta se proverava i ko proverava
- Kvartalni pregled ostvarenja ciljeva
- Godišnja revizija celokupnog plana
- Interni audit sistema upravljanja otpadom

12.2 KOREKTIVNE MERE
Procedura kada se utvrdi odstupanje od plana:
- Ko donosi odluku o korektivnoj meri
- Rok za implementaciju
- Praćenje efikasnosti mere
- Dokumentovanje

12.3 PREISPITIVANJE I AŽURIRANJE PLANA
Navedi uslove pod kojima se plan obavezno revidira:
- Promena delatnosti ili procesa
- Promena propisa
- Rezultati internih audita
- Zahtev nadležnog organa
- Istekom perioda važenja {$formData['rok_vazenja']}

12.4 ZAKLJUČNA IZJAVA
Napiši formalni zaključak (minimum 150 reči) koji potvrđuje:
- Opredeljenost firme ka odgovornom upravljanju otpadom
- Obavezujući karakter ovog dokumenta za sve zaposlene
- Poziv na primenu u svakodnevnom radu

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
POTPISNA STRANA

Ovaj Plan upravljanja otpadom donosi se na osnovu Zakona o upravljanju otpadom
i stupa na snagu danom potpisivanja.

U {$formData['mesto_opstina']}, {$datum}

Odgovorno lice:                          Lice za zaštitu životne sredine:

_______________________                  _______________________
{$formData['odgovorno_lice']}                          {$formData['kontakt_eko']}
Direktor

M.P.

[Strana 15 od 15]

Posebne napomene iz forme (uključi gde je relevantno): {$formData['posebne_napomene']}
PROMPT;
    }

    public function buildContinuationPrompt(string $existingContent, int $wordCount, array $missingPages): string
    {
        $missing = $missingPages === [] ? 'nema eksplicitno označenih' : implode(', ', $missingPages);
        $needed = max(0, WastePlanContentParser::MIN_WORD_COUNT - $wordCount);

        return <<<PROMPT
Dokument koji si generisao ima samo {$wordCount} reči, što je ispod obaveznog minimuma od 5500 reči.
Nedostaju ili nisu potpuno razrađene strane: {$missing}.

ZADATAK: Dopuni i proširi postojeći plan. NE ponavljaj već napisano.
Dodaj minimum {$needed} novih reči.
Svaka nedostajuća strana mora imati minimum 400 reči gustog sadržaja.
Na kraju svakog dopunjenog poglavlja obavezno stavi oznaku [Strana X od 15].
Nastavi tačno od mesta gde je dokument prekinut ili gde su strane nedovoljno razrađene.
PROMPT;
    }

    /**
     * @param  array<string, mixed>  $formData
     */
    private function firmDataBlock(array $formData, string $imaSkladiste): string
    {
        return <<<DATA
=== PODACI O FIRMI ===
Naziv firme: {$formData['naziv_firme']}
PIB: {$formData['pib']}
Matični broj: {$formData['maticni_broj']}
Adresa sedišta: {$formData['adresa_sedista']}
Mesto i opština: {$formData['mesto_opstina']}
Odgovorno lice / Direktor: {$formData['odgovorno_lice']}
Kontakt osoba za zaštitu životne sredine: {$formData['kontakt_eko']}
Telefon i email: {$formData['telefon_email']}
Pretežna delatnost / šifra delatnosti: {$formData['delatnost']}
Broj zaposlenih: {$formData['broj_zaposlenih']}

=== PODACI O OTPADU ===
Vrste otpada: {$formData['vrste_otpada']}
Indeksni/katalog brojevi: {$formData['indeksni_brojevi']}
Procenjene godišnje količine: {$formData['procenjene_kolicine']}
Način postupanja: {$formData['nacin_postupanja']}
Ugovori sa operaterima: {$formData['ugovori_operateri']}

=== LOKACIJA I INFRASTRUKTURA ===
Lokacija/pogon: {$formData['lokacija_pogon']}
Površina objekta: {$formData['povrsina_objekta']}
Interno skladište: {$imaSkladiste}
Opis skladišta: {$formData['opis_skladista']}

=== PRAVNI OKVIR I CILJEVI ===
Zakoni i propisi: {$formData['zakoni_propisi']}
Rok važenja plana: {$formData['rok_vazenja']}
Ciljevi smanjenja: {$formData['ciljevi_smanjenja']}
Posebne napomene: {$formData['posebne_napomene']}
DATA;
    }
}
