# Integracije i podaci — 12. 11. 2026.

## Javni izvori

- Službena stranica https://martabucan.com/ potvrđuje naziv ordinacije, Ulicu grada Chicaga 18 u Zagrebu, telefon 01 6131 472 i ordinacija@martabucan.com. Postojeći kontakt ostao je glavni.
- OIB 96776052027 objavljen je u poslovnom imeniku Kompass: https://hr.kompass.com/c/marta-bucan-privatna-ord-dent-med-marta-bucan-dr-med-dent/hr594579/ . To je sekundarni poslovni izvor, ne izvadak nadležnog registra. Nije korišten kao broj upisa ili broj odobrenja. Kompass adresu navodi s dodatkom /1; za javnu adresu zadržan je broj 18 sa službene stranice i Google profila.
- Google Maps profil Marta Bućan dr.med.dent. potvrđuje lokaciju na 45.7885218, 16.005572. Iz njegova izbornika Share > Embed a map preuzet je ugrađeni prikaz s točnom oznakom ordinacije. URL je u config/integrations.php.
- Nisu pronađeni javno objavljeni iznosi cjenika. Prazni iznosi prikazuju „Na upit”.

## Privole i Laravel

Prva razina nudi jednako istaknute gumbe za prihvaćanje i odbijanje te postavke. Maps i Analytics imaju odvojene privole. Neobavezne usluge su isključene dok korisnik ne odabere kategoriju. Zatvaranje postavki ne sprema privolu. Karta se može uključiti zasebno, bez analitike.

Izbor se pohranjuje u lokalnu pohranu mb_consent_v2 (verzija, svrhe, vrijeme, oznaka politike) najviše 180 dana. Promjena GA4 identifikatora traži novi izbor. Obrađeni su istek, promjene u drugim karticama i blokirana pohrana. Povlačenje uklanja kartu; analitici se blokira mjerenje, uklanjaju se prvi analitički kolačići, a stranica se ponovno učitava radi uklanjanja već pokrenutog koda. Ne brišu se martabucan_session i XSRF-TOKEN, pa Laravel prijava i CSRF zaštita ostaju funkcionalni.

Implementirano prema AZOP-u https://azop.hr/kolacici-gdpr/ i Google Basic Consent Mode https://developers.google.com/tag-platform/security/guides/consent?consentmode=basic . Politike moraju odgovarati stvarnom poslovanju i konfiguraciji pružatelja.

## Google Analytics

Stari UA-172128-36 je umirovljeni Universal Analytics ID. analytics.js nije uključen. Novi GA4 ID treba upisati u GA4_MEASUREMENT_ID u okruženju, zatim očistiti/obnoviti Laravel config cache prema načinu deploya. Lozinke ne upisivati u izvorni kod ili dokumentaciju.

Dok ID nije postavljen, nema analitičkog koda ni analitičkog prekidača, a politika navodi da analitika nije aktivna. Kad se postavi valjani G-ID, kategorija se prikazuje, postojeći izbor se obnavlja i gtag.js se učitava isključivo nakon privole za analitiku. Google Signals i personalizacija oglasa isključeni su u kodu; ad_storage, ad_user_data i ad_personalization ostaju denied. Vijek prvih GA kolačića ograničen je na 180 dana. U page_location nema query parametara ni fragmenta, a referrer se ne šalje.

Prije aktivacije vlasnik treba potvrditi postavke novog GA4 entiteta: ugovorne uvjete obrade, međunarodne prijenose, primjenjivi rok čuvanja događaja, isključeno oglašavanje/Signals i Enhanced Measurement opcije (posebno obrasce i URL upite). Sam lokalni kod ne može postaviti ili potvrditi sve postavke Google računa.

## Podaci koje još treba potvrditi

Pružatelj produkcijskog hostinga i izvršitelji obrade; konkretna pravila čuvanja poslužiteljskih zapisa i komunikacije; registar/broj upisa, odobrenje i relevantni komorski podaci; porezni tretman i iznosi cjenika. Prazna poslovna polja nisu javno ispisana, ali ostaju u CMS-u. Zabilježena implementacija nije jamstvo potpune pravne usklađenosti bez ovih stvarnih podataka i provjere poslovnih postupaka.

## Provjera

- Laravel: 8 testova, 81 tvrdnja, prolaz.
- node tests/js/consent.test.js: blokada prije privole, odvojene kategorije, povlačenje, istek, sinkronizacija kartica, blokirana pohrana, očuvanje Laravel sigurnosnih kolačića.
- Preglednik: desktop cjenik i podaci; mobilna politika i dijalog bez horizontalnog prelijevanja; stvarna Google karta nakon privole i uklanjanje nakon odbijanja; uspješna prijava i odjava administratora.
