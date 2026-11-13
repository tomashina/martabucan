<?php
$f = fn ($label, $bind, $type = 'text', $extra = []) => array_merge(['label' => $label, 'bind' => $bind, 'type' => $type], $extra);
$heading = fn ($prefix) => [
    'eyebrow' => $f('Nadnaslov', 'intro.'.$prefix.'_eyebrow'),
    'title' => $f('Naslov', 'intro.'.$prefix.'_title', 'text', ['required' => true]),
    'accent' => $f('Naglašeni dio naslova', 'intro.'.$prefix.'_accent'),
];
$photos = ['image' => $f('Fotografija', '', 'image', ['required' => true]), 'alt' => $f('Opis fotografije', '', 'text', ['required' => true])];
$icons = ['shield-check'=>'Zaštita','sparkles'=>'Sjaj','face-smile'=>'Osmijeh','tooth'=>'Zub','teeth-open'=>'Protetika','kit-medical'=>'Medicina','teeth'=>'Zubi','heart-pulse'=>'Zdravlje','heart'=>'Srce','user-doctor'=>'Doktor','badge-check'=>'Kvaliteta'];
$block = fn ($label, $description, $fields, $anchor = '') => compact('label','description','fields','anchor');
$pages = [
 'home' => ['label'=>'Naslovnica','url'=>'/','icon'=>'house','description'=>'Hero, predstavljanje ordinacije, usluge, fotografije i kontakt.','blocks'=>[
  'hero'=>$block('Hero','Uvodni naslov, gumbi i fotografije koje se izmjenjuju.',[
   'eyebrow'=>$f('Nadnaslov','intro.eyebrow'), 'title'=>$f('Naslov','intro.title','text',['required'=>true]), 'accent'=>$f('Završetak naslova','intro.accent'),
   'primary'=>$f('Glavni gumb','intro.hero_primary'), 'secondary'=>$f('Drugi gumb','intro.hero_secondary'),
   'slides'=>$f('Fotografije u hero bloku','media.hero','repeater',['fields'=>$photos,'add'=>'Dodaj fotografiju','min'=>1,'max'=>6,'item'=>'Fotografija']),
  ]),
  'about'=>$block('O nama','Cijeli opis uređujete u jednom editoru, uz naslov i fotografiju.',[
   'eyebrow'=>$f('Nadnaslov','intro.about_eyebrow'),'title'=>$f('Naslov','intro.about_title','text',['required'=>true]),
   'description'=>$f('Opis sekcije','intro.about_html','rich'), 'image'=>$f('Fotografija tima','media.about','image',['required'=>true]), 'alt'=>$f('Opis fotografije','media.about_alt'),
  ],'o-nama'),
  'values'=>$block('Naš pristup','Tri kartice koje predstavljaju ordinaciju.',[
   'expertise_title'=>$f('Naslov prve kartice','intro.expertise_title'),'expertise'=>$f('Stručnost — opis','intro.expertise','rich'),
   'quality_title'=>$f('Naslov druge kartice','intro.quality_title'),'quality'=>$f('Kvaliteta — opis','intro.quality','rich'),
   'dedication_title'=>$f('Naslov treće kartice','intro.dedication_title'),'dedication'=>$f('Predanost — opis','intro.dedication','rich'),
  ],'pristup'),
  'services'=>$block('Usluge','Uredite uvod te dodajte, poredajte ili uklonite usluge.',array_merge($heading('services'),[
   'description'=>$f('Opis sekcije','intro.services_text','rich'), 'note'=>$f('Kratka napomena','intro.services_note'),
   'items'=>$f('Usluge','services','repeater',['add'=>'Dodaj uslugu','item'=>'Usluga','min'=>0,'max'=>50,'fields'=>[
    'title'=>$f('Naziv usluge','','text',['required'=>true]),'icon'=>$f('Ikona','','icon',['options'=>$icons]),'description'=>$f('Opis usluge','','rich',['required'=>true]),
   ]]),
  ]),'usluge'),
  'spaces'=>$block('Prostor ordinacije','Naslov, opis i fotografije prostora.',array_merge($heading('spaces'),[
   'description'=>$f('Opis sekcije','intro.spaces_text','rich'), 'photos'=>$f('Fotografije prostora','media.spaces','repeater',['fields'=>$photos,'add'=>'Dodaj fotografiju','item'=>'Fotografija','min'=>1,'max'=>6]),
  ]),'prostor'),
  'team'=>$block('Naš tim','Fotografije, imena i predstavljanje članova tima.',array_merge($heading('team'),[
   'description'=>$f('Opis sekcije','intro.team_text','rich'),'items'=>$f('Članovi tima','team','repeater',['add'=>'Dodaj člana tima','item'=>'Član tima','min'=>0,'max'=>20,'fields'=>[
    'name'=>$f('Ime i prezime','','text',['required'=>true]),'role'=>$f('Stručni naziv','','text'),'image'=>$f('Portret','','image',['required'=>true]),'bio'=>$f('Biografija','','rich'),
   ]]),
  ]),'tim'),
  'results'=>$block('Prije i poslije','Opis galerije i fotografije rezultata terapije.',array_merge($heading('results'),[
   'description'=>$f('Opis sekcije','intro.results_html','rich'),'note'=>$f('Napomena ispod galerije','intro.results_note'),
   'photos'=>$f('Fotografije rezultata','media.results','repeater',['fields'=>array_merge($photos,['caption'=>$f('Naslov u povećanom prikazu','','text')]),'add'=>'Dodaj fotografiju','item'=>'Fotografija','min'=>0,'max'=>20]),
  ]),'rezultati'),
  'faq'=>$block('Česta pitanja','Svako pitanje ima svoj odgovor u HTML editoru.',array_merge($heading('faq'),[
   'items'=>$f('Pitanja i odgovori','faq','repeater',['add'=>'Dodaj pitanje','item'=>'Pitanje','min'=>0,'max'=>50,'fields'=>[
    'question'=>$f('Pitanje','','text',['required'=>true]),'answer'=>$f('Odgovor','','rich',['required'=>true]),
   ]]),
  ]),'pitanja'),
  'contact'=>$block('Kontakt','Kontaktni podaci koriste se i na ostalim stranicama.',array_merge($heading('contact'),[
   'description'=>$f('Uvodni tekst','intro.original_heading','rich'),
   'phone'=>$f('Telefon','contact.phone','phone',['required'=>true]),'email'=>$f('E-mail','contact.email','email',['required'=>true]),
   'address'=>$f('Ulica i broj','contact.address','text',['required'=>true]),'city'=>$f('Poštanski broj, grad i država','contact.city','text',['required'=>true]),
   'afternoon_days'=>$f('Dani — poslijepodne','contact.hours_afternoon'),'afternoon_time'=>$f('Radno vrijeme — poslijepodne','contact.afternoon_time'),
   'morning_days'=>$f('Dani — ujutro','contact.hours_morning'),'morning_time'=>$f('Radno vrijeme — ujutro','contact.morning_time'),
   'hours_note'=>$f('Napomena uz radno vrijeme','contact.hours_note'),'payment_note'=>$f('Plaćanje i obroci','contact.payment_note'),
  ]),'kontakt'),
 ]],
 'pricing'=>['label'=>'Cjenik','url'=>'/cjenik','icon'=>'tags','description'=>'Uvod, popis usluga, iznosi i uvjeti plaćanja.','blocks'=>[
  'content'=>$block('Cjenik usluga','Prazan iznos prikazuje se kao „Na upit”.',[
   'eyebrow'=>$f('Nadnaslov','prices.eyebrow'),'title'=>$f('Naslov','prices.title','text',['required'=>true]),'accent'=>$f('Naglašeni dio naslova','prices.accent'),
   'description'=>$f('Uvodni tekst','prices.note','rich'),'aside_title'=>$f('Naslov uz cjenik','prices.aside_title'),'aside_text'=>$f('Opis uz cjenik','prices.aside_text','rich'),
   'items'=>$f('Stavke cjenika','prices.items','repeater',['add'=>'Dodaj stavku','item'=>'Stavka','min'=>0,'max'=>100,'fields'=>[
    'name'=>$f('Naziv usluge','','text',['required'=>true]),'detail'=>$f('Kratki opis','','text'),'amount'=>$f('Aktualna cijena (EUR)','','number'), 'anchor_amount'=>$f('Sidrena cijena (EUR)','','number',['placeholder'=>'Nije postavljena','help'=>'Opcionalna referentna cijena. Na stranici se prikazuje precrtano iznad aktualne cijene.']),'from'=>$f('Vrsta cijene','','boolean'),
   ]]),'tax_note'=>$f('Napomena o porezu i troškovima','prices.tax_note','textarea'),'valid_from'=>$f('Cjenik vrijedi od','prices.valid_from','date'),
  ]),
 ]],
 'business'=>['label'=>'Podaci o ordinaciji','url'=>'/podaci-o-ordinaciji','icon'=>'address-card','description'=>'Naziv, identifikacijski i profesionalni podaci.','blocks'=>[]],
];
$businessFields=['name'=>'Naziv ordinacije','oib'=>'OIB','register'=>'Registar i broj upisa','authorization'=>'Odobrenje za obavljanje djelatnosti','chamber'=>'Strukovna komora','professional_title'=>'Profesionalni naziv','qualification_country'=>'Država stjecanja kvalifikacije','professional_rules'=>'Profesionalna pravila','supervisory_authority'=>'Nadležno tijelo','complaints'=>'Pisani prigovori','hosting_provider'=>'Pružatelj hostinga','privacy_retention'=>'Rokovi čuvanja podataka'];
$fields=['title'=>$f('Naslov stranice','page_meta.business.title','text',['required'=>true]),'intro'=>$f('Uvodni tekst','page_meta.business.intro','rich')];
foreach($businessFields as $key=>$label) $fields[$key]=$f($label,'business.'.$key,in_array($key,['complaints','professional_rules','privacy_retention'])?'textarea':($key==='oib'?'oib':'text'));
$pages['business']['blocks']['content']=$block('Podaci ordinacije','Upišite provjerene podatke. Prazna polja nisu prikazana posjetiteljima.',$fields);
foreach(['privacy'=>['Privatnost','/privatnost'],'cookies'=>['Kolačići','/kolacici'],'terms'=>['Uvjeti korištenja','/uvjeti-koristenja']] as $key=>[$label,$url]) $pages[$key]=[
 'label'=>$label,'url'=>$url,'icon'=>'file-lines','description'=>'Naslov, uvod i tekst stranice u jednom editoru.','blocks'=>['content'=>$block($label,'Cijeli sadržaj stranice uređujete u jednom editoru.',[
  'title'=>$f('Naslov stranice','page_meta.'.$key.'.title','text',['required'=>true]),'intro'=>$f('Uvodni tekst','page_meta.'.$key.'.intro','rich'),'body'=>$f('Sadržaj stranice','legal.'.$key,'legal',['required'=>true]),
 ])],
];
$pages['general']=['label'=>'Zajednički sadržaj','url'=>'/','icon'=>'sliders','description'=>'Tekstovi koji se pojavljuju na svim stranicama.','blocks'=>['footer'=>$block('Podnožje','Zajednički opis u podnožju svih stranica.',['description'=>$f('Tekst podnožja','intro.footer_text')])]];
$pages['settings']=['label'=>'Postavke','url'=>'/','icon'=>'gear','description'=>'SEO, OpenGraph, Google i društvene mreže.','blocks'=>[
 'seo'=>$block('SEO','Naslovi i opisi za tražilice te adresa web-stranice.',[
  'site_name'=>$f('Naziv web-stranice','settings.site_name','text',['required'=>true]),
  'meta_title'=>$f('SEO naslov','settings.meta_title','text',['required'=>true]),
  'meta_description'=>$f('Meta opis','settings.meta_description','textarea'),
  'site_url'=>$f('Glavna domena','settings.site_url','origin',['placeholder'=>'https://www.martabucan.com','help'=>'Prazno polje koristi trenutačnu domenu. Unesite samo domenu, bez putanje.']),
  'indexable'=>$f('Prikaz u tražilicama','settings.indexable','boolean',['options'=>[0=>'Isključeno — noindex',1=>'Uključeno — index'],'help'=>'Na .test i localhost domenama indeksiranje ostaje isključeno.']),
 ]),
 'sharing'=>$block('OpenGraph','Izgled poveznice pri dijeljenju na Facebooku i drugim aplikacijama.',[
  'og_title'=>$f('Naslov za dijeljenje','settings.og_title'),
  'og_description'=>$f('Opis za dijeljenje','settings.og_description','textarea'),
  'og_image'=>$f('Slika za dijeljenje','settings.og_image','image',['help'=>'Preporučeni format: 1200 × 630 px.']),
  'og_image_alt'=>$f('Opis slike','settings.og_image_alt'),
 ]),
 'google'=>$block('Google','Google Analytics, Search Console i karta ordinacije.',[
  'ga4_id'=>$f('Google Analytics 4 — Measurement ID','settings.ga4_id','ga4',['placeholder'=>'G-XXXXXXXXXX','help'=>'Unesite samo G-ID. Prazno polje isključuje analitiku. Mjerenje počinje tek nakon privole posjetitelja.']),
  'google_verification'=>$f('Google Search Console — verifikacijski kod','settings.google_verification','token',['help'=>'Samo vrijednost content iz oznake google-site-verification, bez HTML koda.']),
  'maps_url'=>$f('Google Maps — URL ugrađene karte','settings.maps_url','maps',['required'=>true,'help'=>'URL iz src atributa Google Maps iframe koda. Karta se učitava tek nakon privole.']),
 ]),
 'social'=>$block('Društvene mreže','Poveznice profila u podnožju i potvrda domene za Facebook.',[
  'facebook_url'=>$f('Facebook stranica','settings.facebook_url','url',['placeholder'=>'https://www.facebook.com/...']),
  'instagram_url'=>$f('Instagram profil','settings.instagram_url','url',['placeholder'=>'https://www.instagram.com/...']),
  'facebook_verification'=>$f('Facebook / Meta — verifikacija domene','settings.facebook_verification','token',['help'=>'Samo vrijednost content iz oznake facebook-domain-verification.']),
 ]),
]];
foreach (require __DIR__.'/interface.php' as $groupKey => $group) {
    $fields = [];
    foreach ($group['fields'] as $key => $text) {
        $fields[$key] = $f($text['label'], 'ui.'.$key, max(mb_strlen($text['hr']), mb_strlen($text['de'])) > 150 ? 'textarea' : 'text', ['required' => true]);
    }
    $pages['general']['blocks']['ui-'.$groupKey] = $block($group['label'], 'Tekstovi gumba, oznaka i obavijesti za odabrani jezik.', $fields);
}
return ['pages'=>$pages,'icons'=>$icons];
