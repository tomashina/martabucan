# Uređivanje sadržaja

Otvorite `/admin` i odaberite stranicu. Naslovnica je podijeljena na devet sekcija; cjenik, podaci ordinacije i pravne stranice imaju zasebne obrasce. Sprema se samo trenutačna sekcija, a poveznica „Pogledaj na stranici” otvara odgovarajući dio javne stranice.

Na vrhu odaberite „Jezik sadržaja”: Hrvatski ili Njemački. Odabrani jezik ostaje aktivan pri prelasku između sekcija i otvaranju pregleda. Sadržaj, cjenik, pravne stranice, opisi fotografija i SEO tekstovi spremaju se zasebno za svaki jezik. „Općenito” sadrži i prijevode navigacije, gumba, oznaka i obavijesti o kolačićima. Administratorsko sučelje ostaje na hrvatskom.

Hrvatska naslovnica ostaje na `/`, a njemačka je na `/de`; stara poveznica `/de.html` preusmjerava na `/de`. HR/DE na javnoj stranici vodi na odgovarajuću jezičnu verziju otvorene stranice i zadržava odabranu sekciju naslovnice. Google Analytics, verifikacijske oznake, Google Maps i društvene poveznice zajedničke su postavke za oba jezika.

Njemački sadržaj ima početne prijevode u konfiguraciji i može se odmah uređivati; postojeći hrvatski zapisi ostaju sačuvani. Za ovu nadogradnju nije potrebna migracija ni ponovno pokretanje seedera. Nakon povlačenja koda na poslužitelj osvježite Laravelove cacheve (`php artisan optimize:clear`, zatim `php artisan view:cache`, uz PHP 8.4 na ovom hostingu).

Opisi koriste lokalno instalirani Trix 2.1.19, s vizualnim i HTML načinom. HTMLPurifier na poslužitelju uklanja skripte, umetnute medije, stilove i nesigurne poveznice. Javna stranica primjenjuje vlastitu tipografiju na spremljeni sadržaj.

Usluge, FAQ, članovi tima, fotografije i cjenik mogu se dodavati, uklanjati i poredati. Uklanjanje se može poništiti tipkom „Vrati” prije spremanja. „Odustani” vraća posljednju spremljenu verziju. Nespremljene promjene upozoravaju pri odlasku, a zastarjeli obrazac ne može prebrisati novije izmjene iste sekcije.

Usluge imaju vizualni odabir ikona s pretragom. Svaka stavka cjenika ima polje „Kategorija”; stavke s istim nazivom prikazuju se zajedno, prema redoslijedu prve stavke svake kategorije. Sidrena cijena prikazuje se uz aktualnu, bez precrtavanja, i kada je jednaka ili niža. „Zajednički datum sidrenih cijena” odvojen je od datuma važenja aktualnog cjenika. Za novu uslugu može se unijeti poseban referentni datum u njezinoj stavci.

Cjenik od 16. 11. 2026. prepisan je iz datoteke `CJENIK-PDF.pdf` koju je ordinacija dostavila isti dan: 36 stavki u sedam kategorija, na hrvatskom i njemačkom. Iznosi i sidrene cijene su jednaki, sve su cijene točne (bez oznake „od”). Za jednokratni unos u postojeću bazu nakon deploya pokrenuti `php artisan db:seed --class=PriceListSeeder --force`. Naredba zamjenjuje stavke, uvod i datum cjenika u oba jezika; ostali sadržaj ostaje sačuvan. Ne pokretati je kod budućih deploya ako su cijene naknadno uređivane u adminu. Izvor podataka je `database/data/prices-2026-11-16.php`.

## XML i povijest cjenika

Nadogradnja za XML zahtijeva `php artisan migrate --force` (na hostingu `ea-php84 artisan migrate --force`). Ne ponavljati unos seederom. Migracija zadržava unesene iznose i prenosi njihov postojeći datum u zaseban datum sidrenja; ne pretpostavlja da su cijene od 16. 11. vrijedile i 10. 9. 2026. Za zakonski referentni datum vlasnik mora provjeriti tadašnje cijene te urediti datum i eventualno iznose u oba jezika.

Na javnom cjeniku dostupni su „Preuzmi cjenik (XML)” i „Arhiva cjenika”. Aktualni XML dohvaća se putem `/cjenik.xml` i `/de/preise.xml`, a prethodne verzije putem `/cjenik/arhiva` i `/de/preise/archiv`. Nije potrebna prijava. Robots.txt izričito dopušta dohvat tih putanja i datoteka i kada je ostatak razvojne stranice označen za neindeksiranje.

XML sadrži naziv usluge, aktualnu i sidrenu cijenu, datum sidrenja, informaciju o posebnom obliku prodaje i njegov naziv, kategoriju te podatke ordinacije. Naziv preuzete datoteke sadrži vrstu objekta, adresu, oznaku objekta, broj pohrane i vrijeme objave u vremenskoj zoni Europe/Zagreb. Vrsta i oznaka objekta uređuju se u cjeniku; za jednu ordinaciju bez interne oznake početna oznaka je `01`. Adresa i podaci pružatelja usluga preuzimaju se iz hrvatskog kontakta i poslovnih podataka.

Pri spremanju promijenjenih podataka nastaje zapis u tablici `price_list_versions`. Svaki zapis čuva vlastite iznose, nazive, adresu i datum objave. Preuzimanje ili izmjena uvodnog teksta ne stvara novu verziju. Povratak na ranije iznose stvara novu verziju. Verzije se ne brišu automatski, pa ostaju dostupne najmanje 30 dana. U backup baze uključiti i ovu tablicu. XML se objavljuje samo kada sve usluge imaju aktualnu i sidrenu cijenu te datum sidrenja; CMS sprječava uklanjanje tih podataka iz već objavljenog cjenika.

Pravna osnova provjerena 16. 11. 2026.: [NN 101/2026-1212](https://narodne-novine.nn.hr/clanci/sluzbeni/2026_09_101_1212.html) (datum 10. 9. 2026.), [NN 101/2026-1213](https://narodne-novine.nn.hr/clanci/sluzbeni/2026_09_101_1213.html) (objava XML/CSV, sadržaj, naziv i dostupnost 30 dana) te [NN 110/2026-1309](https://narodne-novine.nn.hr/clanci/sluzbeni/2026_09_110_1309.html) i [NN 110/2026-1310](https://narodne-novine.nn.hr/clanci/sluzbeni/full/2026_09_110_1310.html) (primjena od 17. 11. 2026.). Tehnička funkcionalnost ne potvrđuje povijesnu točnost unesenih iznosa.

„Postavke” u odjeljku Administracija sadrže SEO, OpenGraph sliku i tekstove, GA4 Measurement ID, Google Search Console potvrdu, Google Maps adresu, društvene poveznice i Meta potvrdu domene. Analitika ostaje isključena dok nije unesen valjan GA4 ID i posjetitelj nije dao privolu. Lokalna domena `.test` uvijek ostaje izvan indeksiranja.

„Korisnici” prikazuju administratore. Zaštićeni master račun može dodavati administratore, mijenjati njihove lozinke i brisati njihove račune. Promjenu tuđe lozinke ili trajno brisanje potvrđuje svojom trenutačnom lozinkom. Master račun nije moguće obrisati ili mu mijenjati lozinku kroz tuđi račun. Sadržaj stranice ostaje sačuvan nakon brisanja korisnika.

Svaki administrator svoju lozinku mijenja u „Moj račun”, dostupnom i klikom na ime u gornjem desnom kutu. Potrebni su trenutačna lozinka i dva jednaka unosa nove lozinke od najmanje osam znakova. Lozinke se pohranjuju kao hash i ne vraćaju se u obrazac nakon pogreške. Promjena lozinke poništava ostale postojeće prijave pri njihovu sljedećem pristupu CMS-u. Pokušaji promjene i brisanja ograničeni su na pet u minuti po prijavljenom korisniku.

Migracija za ulogu mastera štiti postojeći vlasnički račun `tomislav@agmedia.hr`. Za novu instalaciju vlasnički račun može se izraditi naredbom `php artisan cms:create-admin --master`; web-obrazac uvijek stvara običnog administratora.

Admin CSS i JavaScript automatski dobivaju novu oznaku verzije pri promjeni datoteke, pa osvježavanje stranice učitava aktualne stilove i kontrole.

Fotografije: JPG/PNG/WebP, najviše 10 MB i 24 megapiksela. Spremanje stvara lokalne WebP varijante do 480, 960 i 1920 px bez povećavanja izvornika. Front koristi `srcset`, postojeće omjere, efekte i lazy loading. Upload ne mijenja izvorne fotografije. Ranije datoteke nisu automatski obrisane jer ih druga sekcija može koristiti.

Za sigurnosnu kopiju sadržaja uključite bazu i `public/assets/uploads`. PHP mora imati GD s WebP podrškom, DOM i Fileinfo; direktorij uploads mora biti zapisiv. Na produkciji uskladite PHP `upload_max_filesize`, `post_max_size` i `max_file_uploads` s navedenim limitima.

Provjere: `php artisan test --compact`, `node tests/js/consent.test.js`, `node --check public/assets/js/admin.js`.
