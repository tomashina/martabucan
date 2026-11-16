# Uređivanje sadržaja

Otvorite `/admin` i odaberite stranicu. Naslovnica je podijeljena na devet sekcija; cjenik, podaci ordinacije i pravne stranice imaju zasebne obrasce. Sprema se samo trenutačna sekcija, a poveznica „Pogledaj na stranici” otvara odgovarajući dio javne stranice.

Na vrhu odaberite „Jezik sadržaja”: Hrvatski ili Njemački. Odabrani jezik ostaje aktivan pri prelasku između sekcija i otvaranju pregleda. Sadržaj, cjenik, pravne stranice, opisi fotografija i SEO tekstovi spremaju se zasebno za svaki jezik. „Općenito” sadrži i prijevode navigacije, gumba, oznaka i obavijesti o kolačićima. Administratorsko sučelje ostaje na hrvatskom.

Hrvatska naslovnica ostaje na `/`, a njemačka je na `/de`; stara poveznica `/de.html` preusmjerava na `/de`. HR/DE na javnoj stranici vodi na odgovarajuću jezičnu verziju otvorene stranice i zadržava odabranu sekciju naslovnice. Google Analytics, verifikacijske oznake, Google Maps i društvene poveznice zajedničke su postavke za oba jezika.

Njemački sadržaj ima početne prijevode u konfiguraciji i može se odmah uređivati; postojeći hrvatski zapisi ostaju sačuvani. Za ovu nadogradnju nije potrebna migracija ni ponovno pokretanje seedera. Nakon povlačenja koda na poslužitelj osvježite Laravelove cacheve (`php artisan optimize:clear`, zatim `php artisan view:cache`, uz PHP 8.4 na ovom hostingu).

Opisi koriste lokalno instalirani Trix 2.1.19, s vizualnim i HTML načinom. HTMLPurifier na poslužitelju uklanja skripte, umetnute medije, stilove i nesigurne poveznice. Javna stranica primjenjuje vlastitu tipografiju na spremljeni sadržaj.

Usluge, FAQ, članovi tima, fotografije i cjenik mogu se dodavati, uklanjati i poredati. Uklanjanje se može poništiti tipkom „Vrati” prije spremanja. „Odustani” vraća posljednju spremljenu verziju. Nespremljene promjene upozoravaju pri odlasku, a zastarjeli obrazac ne može prebrisati novije izmjene iste sekcije.

Usluge imaju vizualni odabir ikona s pretragom. Svaka stavka cjenika ima polje „Kategorija”; stavke s istim nazivom prikazuju se zajedno, prema redoslijedu prve stavke svake kategorije. Sidrena cijena je neobavezna referentna cijena i može biti jednaka aktualnoj. Prikazuje se prekriženo iznad aktualne samo kada je viša.

Cjenik od 16. 11. 2026. prepisan je iz datoteke `CJENIK-PDF.pdf` koju je ordinacija dostavila isti dan: 36 stavki u sedam kategorija, na hrvatskom i njemačkom. Iznosi i sidrene cijene su jednaki, sve su cijene točne (bez oznake „od”). Za jednokratni unos u postojeću bazu nakon deploya pokrenuti `php artisan db:seed --class=PriceListSeeder --force`. Naredba zamjenjuje stavke, uvod i datum cjenika u oba jezika; ostali sadržaj ostaje sačuvan. Ne pokretati je kod budućih deploya ako su cijene naknadno uređivane u adminu. Izvor podataka je `database/data/prices-2026-11-16.php`.

„Postavke” u odjeljku Administracija sadrže SEO, OpenGraph sliku i tekstove, GA4 Measurement ID, Google Search Console potvrdu, Google Maps adresu, društvene poveznice i Meta potvrdu domene. Analitika ostaje isključena dok nije unesen valjan GA4 ID i posjetitelj nije dao privolu. Lokalna domena `.test` uvijek ostaje izvan indeksiranja.

„Korisnici” prikazuju administratore. Zaštićeni master račun može dodavati administratore, mijenjati njihove lozinke i brisati njihove račune. Promjenu tuđe lozinke ili trajno brisanje potvrđuje svojom trenutačnom lozinkom. Master račun nije moguće obrisati ili mu mijenjati lozinku kroz tuđi račun. Sadržaj stranice ostaje sačuvan nakon brisanja korisnika.

Svaki administrator svoju lozinku mijenja u „Moj račun”, dostupnom i klikom na ime u gornjem desnom kutu. Potrebni su trenutačna lozinka i dva jednaka unosa nove lozinke od najmanje osam znakova. Lozinke se pohranjuju kao hash i ne vraćaju se u obrazac nakon pogreške. Promjena lozinke poništava ostale postojeće prijave pri njihovu sljedećem pristupu CMS-u. Pokušaji promjene i brisanja ograničeni su na pet u minuti po prijavljenom korisniku.

Migracija za ulogu mastera štiti postojeći vlasnički račun `tomislav@agmedia.hr`. Za novu instalaciju vlasnički račun može se izraditi naredbom `php artisan cms:create-admin --master`; web-obrazac uvijek stvara običnog administratora.

Admin CSS i JavaScript automatski dobivaju novu oznaku verzije pri promjeni datoteke, pa osvježavanje stranice učitava aktualne stilove i kontrole.

Fotografije: JPG/PNG/WebP, najviše 10 MB i 24 megapiksela. Spremanje stvara lokalne WebP varijante do 480, 960 i 1920 px bez povećavanja izvornika. Front koristi `srcset`, postojeće omjere, efekte i lazy loading. Upload ne mijenja izvorne fotografije. Ranije datoteke nisu automatski obrisane jer ih druga sekcija može koristiti.

Za sigurnosnu kopiju sadržaja uključite bazu i `public/assets/uploads`. PHP mora imati GD s WebP podrškom, DOM i Fileinfo; direktorij uploads mora biti zapisiv. Na produkciji uskladite PHP `upload_max_filesize`, `post_max_size` i `max_file_uploads` s navedenim limitima.

Provjere: `php artisan test --compact`, `node tests/js/consent.test.js`, `node --check public/assets/js/admin.js`.
