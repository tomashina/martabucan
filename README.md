# Marta Bućan — web i CMS

Lokalni projekt: **http://martabucan.test**. Laravel 12, PHP 8.2+, SQLite. Poslužiteljski Blade prikaz, običan CSS i JavaScript, bez obaveznog Node builda.

## Stranice

- `/` — responzivna naslovnica, svih devet usluga, predstavljanje ordinacije i tima, fotografije interijera, četiri primjera prije/poslije, CTA za cjenik, pitanja i kontakt.
- `/cjenik` — zaseban cjenik, iznosi u EUR, opcionalna oznaka „od”, datum primjene, napomena o porezu i plaćanju.
- `/privatnost`, `/kolacici`, `/uvjeti-koristenja`, `/podaci-o-ordinaciji` — radni pravni tekstovi i službeni podaci za dopunu.
- `/admin` — uređivanje sadržaja, usluga, tima, cijena, kontakta i pravnih tekstova.

## Pokretanje

U ovoj lokalnoj kopiji ovisnosti, aplikacijski ključ i SQLite baza već su pripremljeni. Herd prepoznaje Laravelov `public/` direktorij. Alternativno: `php artisan serve`.

Za novu instalaciju pokrenuti `composer run setup`. Za kreiranje administratora:

```sh
php artisan cms:create-admin
```

Naredba traži ime, e-mail i lozinku od najmanje 12 znakova. Nema zadanih pristupnih podataka ni javne registracije. Prijava je na `/admin/prijava`. Korisnici bez administratorske oznake nemaju pristup. Lozinke su hashirane, obrasci imaju CSRF zaštitu, a prijava ograničenje pokušaja. Spremanje sadržaja koristi validaciju i HTML escaping.

## Dizajn i sadržaj

- Sora za naslove, Instrument Sans za tekst, lokalni WOFF2 fontovi.
- Lokalni **Font Awesome Pro 7.3.1 Light**, uključujući zubić u logotipu i pozadinskim motivima. Komercijalni resursi iz korisnikova postojećeg lokalnog projekta; nisu za javnu redistribuciju kao zaseban paket.
- Svijetla lavanda, topla bijela i čitljiv tamni tekst. Bez dekorativnih strelica i bez ručnih prijeloma tekstova na naslovnici.
- Hero prema kompozicijskom smjeru reference https://dentia.on3-step.com/: fotografija preko cijele širine, tekst i CTA preko svijetlog lavanda prijelaza, navigacija iznad fotografije te kontaktna traka ispod. Vlastita fotografija, stvarni tim i postojeći sadržaj; bez preuzetih ocjena, tvrdnji ili resursa predloška. Prikaz je definiran u `public/assets/css/hero.css`, samo za naslovnicu.
- Fotografije su lokalne WebP datoteke. Hero ima `srcset`, prioritetno učitavanje i optimizirane veličine; ostale fotografije imaju odgođeno učitavanje.
- Izvorni sadržaj i fotografije: https://martabucan.com/index.html, pregledano 12. 11. 2026. Izvorne fotografije sačuvane su u `resources/reference-images/`.
- Uvodna fotografija, grupni portret, čekaonica, četiri pojedinačna portreta i sve tri dodatne fotografije ordinacije imaju AI-potpomognute obrađene verzije za dizajnerski prijedlog: ujednačeno svjetlo, balans bijele, prirodnije boje i manje zamućenja. Izvorne PNG obrade ostaju u `generated_images/`, a posljednja serija i izvozne veličine zabilježene su u `resources/photo-edits.json`. Portreti imaju WebP verzije od 240 i 480 px, a interijer od 640 i 1280 px; preglednik bira veličinu prema prikazu. Prije javne objave treba provjeriti vjernost detalja, osobito portreta rekonstruiranih iz izvora od 215 px; izvorne fotografije ostaju dostupne za zamjenu. Klinički primjeri prije/poslije nisu generativno obrađivani. Fotografija opreme (`ordinacija3.webp`) obrađena je kao ambijentalna fotografija; nije namijenjena prikazu ili tumačenju dijagnostičkog nalaza.
- Tekstovi sa stare stranice preneseni su sadržajno u cijelosti, uz pravopisne ispravke. Navod „10 godina” prenesen je kao „više od deset godina”; povijesni sastav tima, stručne nazive i načine plaćanja treba potvrditi.

Početni tekstovi su u `config/content.php`. CMS sprema izmjene u tablicu `site_contents`; naknadne izmjene zadanog sadržaja ne prepisuju spremljene administratorske vrijednosti. Fotografije i ikone zasad se mijenjaju kroz projekt, ne kroz CMS.

## Privatnost

Google Maps se ne stvara prije izričite privole. Dostupni su prihvat, odbijanje, postavke i povlačenje privole. Izbor se sprema u `localStorage` najdulje 180 dana; blokirana pohrana ne ruši stranicu. Povlačenje uklanja iframe. GA4 je pripremljen iza zasebne privole i ostaje isključen dok vlasnik ne dostavi novi G-ID. Nema marketinških skripti ni vanjskih fontova. Detalji su u `docs/integrations-and-data.md`.

## Podaci potrebni prije objave

- Potvrđeni iznosi cjenika, točan opseg usluga, porezni tretman i datum važenja. Nepoznate cijene namjerno su „Na upit”; nepotvrđeni iznosi nisu izmišljeni.
- Registar i broj upisa, odobrenje, komora, nadležno tijelo i stručna pravila. OIB je dopunjen iz javnog poslovnog imenika; izvori i status su u `docs/integrations-and-data.md`.
- Točno radno vrijeme, aktualni sastav tima, status stažiranja, uvjeti plaćanja i važeće kartice.
- Pružatelj hostinga, stvarni rokovi čuvanja i provjera pravnih tekstova prema stvarnom poslovanju.
- Provjera prava uporabe fotografija, vjernosti obrađenih fotografija i aktualnih licenci fontova / Font Awesome Pro.
- Produkcijski HTTPS, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, sigurne sesije, backup baze i tek nakon potvrde sadržaja uklanjanje `noindex` i izmjena `/robots.txt`. Ova verzija je namjerno lokalni prijedlog, bez indeksiranja.

Korišteni pravni izvori: [AZOP — kolačići](https://azop.hr/kolacici-gdpr/), [AZOP — vodič za kolačiće](https://azop.hr/wp-content/uploads/2022/01/Vodic-za-kolacice.pdf), [Zakon o elektroničkoj trgovini](https://www.zakon.hr/z/199/zakon-o-elektronickoj-trgovini). Pravne stranice su priprema za potvrdu, ne izjava o završenoj usklađenosti.

## Provjere

```sh
php artisan test --compact
node --check public/assets/js/site.js
composer validate --no-check-publish
```

Feature testovi pokrivaju javne i pravne stranice, zaštitu CMS-a, prijavu i odjavu, spremanje, escaping, validaciju cijena i kontakta, oznaku „od”, prazne cijene i zaštitu putanja fotografija. Browserom se provjeravaju desktop i mobilni prikaz, izbornik, proširivanje usluga, galerija te privola i povlačenje privole za Google Maps.
