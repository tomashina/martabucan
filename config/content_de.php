<?php
// German clinic copy migrated from https://martabucan.com/de.html.
// New interface, pricing, FAQ and information pages follow the Croatian site.
$service = static fn ($title, $description, $icon) => compact('title', 'description', 'icon');
$member = static fn ($name, $role, $bio, $image) => compact('name', 'role', 'bio', 'image');
$price = static fn ($name, $detail, $from = false) => ['name' => $name, 'detail' => $detail, 'amount' => null, 'from' => $from];
return [
    'settings' => [
        'site_name' => 'Marta Bućan',
        'meta_title' => 'Marta Bućan · Zahnarztpraxis in Zagreb',
        'meta_description' => 'Zahnmedizin für Sie und Ihre Familie. Zahnarztpraxis Marta Bućan in Zagreb-Borovje — Vorsorge, Ästhetik, Prothetik und Implantologie. Rufen Sie uns an: +385 1 6131 472.',
        'og_title' => 'Marta Bućan — Ihr Lächeln. Unsere Hingabe.',
        'og_description' => 'Zahnarztpraxis in Zagreb-Borovje. Kompetenz, der Sie vertrauen. Zuwendung, die Sie spüren.',
        'og_image_alt' => 'Zahnarztpraxis Marta Bućan in Zagreb',
    ],
    'intro' => [
        'eyebrow' => 'Zahnarztpraxis · Zagreb',
        'title' => 'Weil Sie und Ihre Familie',
        'accent' => 'nur das Beste verdienen!',
        'description' => 'Kompetenz, der Sie vertrauen. Zuwendung, die Sie spüren. Zahnmedizin für Sie und Ihre Familie in der angenehmen Atmosphäre unserer Praxis in Borovje.',
        'hero_primary' => 'Vereinbaren Sie Ihren Besuch',
        'hero_secondary' => 'Unsere Leistungen entdecken',
        'about_eyebrow' => 'WILLKOMMEN',
        'about_title' => 'Ein gutes Gefühl beginnt mit Vertrauen.',
        'about_text' => 'Unsere Aufgabe sehen wir darin, durch einen liebevollen und professionellen Zugang die Zahngesundheit zu verbessern und zu erhalten sowie jedem Einzelnen ein perfektes Lächeln zu schenken.',
        'about_detail' => 'Hinter uns liegen zehn Jahre Arbeitserfahrung. Unsere Ausbildung und berufliche Fortbildung haben wir in Kroatien und im Ausland absolviert. Durch kontinuierliche Weiterbildung, neueste wissenschaftliche Erkenntnisse und technischen Fortschritt sowie hochwertige Materialien, moderne Behandlungsmethoden und Instrumente erzielen wir Ergebnisse, die höchsten fachlichen Ansprüchen an Funktionalität und Ästhetik gerecht werden.',
        'about_html' => null,
        'original_heading' => 'Weil Sie und Ihre Familie nur das Beste verdienen!',
        'expertise_title' => 'Kompetenz.',
        'expertise' => 'In unserer Zahnarztpraxis erwartet Sie ein spezialisiertes Team von Fachleuten, das sein Wissen und seine Erfahrung in den Dienst Ihrer Gesundheit stellt.',
        'quality_title' => 'Qualität.',
        'quality' => 'Für alle Kontrollen, Eingriffe und Behandlungen verwenden wir hochwertige Technologien, Ausrüstung und Materialien.',
        'dedication_title' => 'Hingabe.',
        'dedication' => 'Vom ersten Treffen an kümmern wir uns um Sie und Ihre Familie, sodass Sie sich in jedem Moment bei uns wohlfühlen.',
        'services_eyebrow' => 'ZAHNGESUNDHEIT',
        'services_title' => 'Alles für Ihr',
        'services_accent' => 'Lächeln.',
        'services_text' => 'Brauchen Sie einen Zahnarzt, bei dem Sie und die Gesundheit Ihrer Zähne an erster Stelle stehen? Mit dem vielfältigen Leistungsangebot unserer Zahnarztpraxis Marta Bućan helfen wir Ihnen und Ihrer Familie, gesunde Zähne zu erhalten, sich in unseren Händen wohl und entspannt zu fühlen und mit einem breiten Lächeln zu strahlen.',
        'services_note' => 'Sorgfältig gewählte Therapie. Persönliche Betreuung. Bei jedem Schritt.',
        'spaces_eyebrow' => 'RAUM ZUM WOHLFÜHLEN',
        'spaces_title' => 'Fühlen Sie sich',
        'spaces_accent' => 'bei uns wohl.',
        'spaces_text' => 'Eine angenehme Umgebung, aufmerksame Betreuung und Zeit für Sie.',
        'team_eyebrow' => 'DIE MENSCHEN HINTER IHREM LÄCHELN',
        'team_title' => 'Menschen, denen Sie',
        'team_accent' => 'vertrauen können.',
        'team_text' => 'Wissen, Erfahrung und menschliche Nähe. Lernen Sie das Team kennen, das sich Ihnen und Ihrer Zahngesundheit widmet.',
        'results_eyebrow' => 'EIN LÄCHELN AUS UNSERER PRAXIS',
        'results_title' => 'Vorher. Nachher.',
        'results_accent' => 'Ein Lächeln.',
        'results_intro' => 'Hingabe, Wissen und Kompetenz unseres Teams stehen im Dienst Ihrer Zahngesundheit und Ihres perfekten Lächelns.',
        'results_text' => 'Unser Team strebt die größtmögliche Zufriedenheit unserer Patienten an: durch kontinuierliche Fortbildung, fachliche Qualifikation, innovative Behandlungsmethoden, moderne Technologien und hochwertige Materialien. Gerne zeigen wir Ihnen einige Ergebnisse.',
        'results_html' => null,
        'results_note' => 'Gezeigt werden einzelne Behandlungsbeispiele. Das Ergebnis hängt von der Ausgangssituation und dem individuellen Therapieplan ab.',
        'faq_eyebrow' => 'GUT ZU WISSEN',
        'faq_title' => 'Vor Ihrem',
        'faq_accent' => 'ersten Besuch.',
        'contact_eyebrow' => 'WIR SEHEN UNS IN BOROVJE',
        'contact_title' => 'Ihr neues Lächeln beginnt mit',
        'contact_accent' => 'einem Gespräch.',
        'footer_text' => 'Kompetenz, der Sie vertrauen. Zuwendung, die Sie spüren.',
    ],
    'contact' => [
        'phone' => '+385 1 6131 472',
        'city' => '10000 Zagreb, Kroatien',
        'hours_afternoon' => 'Montag, Dienstag und Donnerstag',
        'hours_morning' => 'Mittwoch und Freitag',
        'afternoon_time' => 'Nachmittags',
        'morning_time' => 'Vormittags',
        'hours_note' => 'Besuche nach vorheriger Vereinbarung. Rufen Sie uns für einen freien Termin an.',
        'payment_note' => 'Ratenzahlung möglich! Zu den Bedingungen und Zahlungsmöglichkeiten beraten wir Sie gerne in der Praxis.',
    ],
    'services' => [
        $service('Vorsorge und Prophylaxe', 'In unserer Praxis führen wir regelmäßige Zahnkontrollen bei Vorschul- und Schulkindern, Fissurenversiegelungen an bleibenden Backenzähnen sowie die Fluoridierung von Milchzähnen und bleibenden Zähnen durch. Außerdem beraten wir Schwangere und stillende Mütter zur Vorsorge.', 'shield-check'),
        $service('Ästhetische Zahnmedizin', 'Ein einfacher Weg zu einem strahlenden Lächeln ist die Zahnaufhellung. Wir führen sie direkt in der Praxis mithilfe einer LED-Lampe durch. Außerdem fertigen wir moderne Zahnersatzlösungen aus Vollglaskeramik oder Zirkon an: Veneers, Kronen, Brücken und Inlays. Je nach Situation können Veneers auch direkt aus ästhetischen Kompositmaterialien hergestellt werden.', 'sparkles'),
        $service('Kinderzahnheilkunde', 'Wir behandeln Karies und Milchzähne mit verschiedenfarbigen Füllungen; bei jungen bleibenden Zähnen kommen weiße Füllungen zum Einsatz. Die Behandlung erfolgt in Absprache mit den Eltern. Stellen wir bei einem Kind eine kieferorthopädische Fehlstellung fest, empfehlen wir regelmäßige Kontrollen beim Kieferorthopäden.', 'face-smile'),
        $service('Konservierende Zahnheilkunde', 'Karies behandeln wir mit weißen Kompositfüllungen. Bei umfangreichem Verlust der Zahnkrone setzen wir Inlays, Onlays, Overlays und Kompositkronen ein.', 'tooth'),
        $service('Herausnehmbarer und festsitzender Zahnersatz', 'Wir bieten moderne herausnehmbare und festsitzende Zahnersatzlösungen zur Wiederherstellung von Ästhetik und Funktion: Kronen, Brücken, kombinierte Versorgungen sowie Teil- und Vollprothesen.', 'teeth-open'),
        $service('Oralchirurgie und Implantologie', 'Unser Angebot umfasst komplizierte Zahnentfernungen einschließlich Weisheitszähnen, Operationen verlagerter Weisheitszähne, Wurzelspitzenresektionen sowie die Entfernung von Zysten oder Granulomen. Moderne Zahnimplantate ersetzen einzelne oder mehrere fehlende Zähne und können auch bei einem vollständig zahnlosen Kiefer eingesetzt werden. Verlorene Knochensubstanz kann mit Knochenersatzmaterial und körpereigenem Plasma ergänzt werden.', 'kit-medical'),
        $service('Kieferorthopädie', 'Wir bieten herausnehmbare und festsitzende kieferorthopädische Behandlungen für Kinder und Erwachsene an. Bei impaktierten und retinierten Zähnen arbeiten wir im Rahmen einer multidisziplinären Therapie mit einem Chirurgen zusammen.', 'teeth'),
        $service('Parodontologie', 'Die Vorbeugung und Behandlung von Erkrankungen des Zahnhalteapparats umfasst regelmäßige Kontrollen, Ultraschall-Zahnsteinentfernung, Politur, Kürettage, Kontrolle und Sondierung der Zahnfleischtaschen sowie regelmäßige Erhaltungstherapie (Recall). Bei einer parodontalen Erkrankung kombinieren wir verschiedene Behandlungen, um Ihre Zähne möglichst lange zu erhalten: herausnehmbaren und festsitzenden Zahnersatz, Bisskorrektur, Kürettage sowie die Stabilisierung der Zähne mit Glasfaser.', 'heart-pulse'),
        $service('Wurzelkanalbehandlung — Endodontie', 'Die Wurzelkanalbehandlung erfolgt mit modernen Methoden und Instrumenten. Die Wurzellänge wird mit einem Apexlokator bestimmt; vor und nach der Behandlung erfolgt eine RVG-Kontrolle (digitales Röntgen).', 'tooth'),
    ],
    'team' => [
        $member('Marta Bućan', 'Dr. med. dent.', 'Abschluss des Studiums der Zahnmedizin an der Universität Zagreb im Jahr 2003.', 'marta.webp'),
        $member('Nataša Stojanović Hindić', 'Dr. med. dent.', 'Abschluss des Studiums der Zahnmedizin an der Universität Zagreb im Jahr 2002.', 'natasa.webp'),
        $member('Petra Đurđević', 'Dr. med. dent. — in Ausbildung', 'Abschluss des Studiums der Zahnmedizin an der Universität Zagreb im Jahr 2012.', 'petra.webp'),
        $member('Gabrijela Markolović', 'Zahntechnikerin — Assistenz', 'Abschluss der berufsbildenden Schule in Koblenz, Fachbereich Zahntechnik, im Jahr 2000.', 'gabrijela.webp'),
    ],
    'media' => [
        'hero' => [
            ['image' => '/assets/images/hero-1280.webp', 'alt' => 'Das Team der Zahnarztpraxis Marta Bućan bei der Behandlung einer Patientin'],
            ['image' => '/assets/images/hero-room-1280.webp', 'alt' => 'Behandlungsraum mit Zahnarztstuhl und moderner Ausstattung'],
            ['image' => '/assets/images/hero-detail-1280.webp', 'alt' => 'Zahnärztliche Instrumente und Röntgenaufnahme in der Praxis'],
        ],
        'about_alt' => 'Das Team der Zahnarztpraxis Marta Bućan',
        'spaces' => [
            ['image' => '/assets/images/ordinacija.webp', 'alt' => 'Innenraum der Zahnarztpraxis Marta Bućan'],
            ['image' => '/assets/images/cekaona.webp', 'alt' => 'Wartezimmer der Zahnarztpraxis Marta Bućan'],
        ],
        'results' => array_map(static fn ($number) => ['image' => '/assets/images/rezultati-'.$number.'-1920.webp', 'alt' => 'Fotografischer Vergleich vor und nach der Zahnbehandlung', 'caption' => 'Vor und nach der Behandlung'], ['2393', '2491', '2694']),
    ],
    'faq' => [
        ['question' => 'Wie vereinbare ich einen ersten Termin?', 'answer' => 'Rufen Sie uns unter {phone} an oder schreiben Sie an {email}. Gemeinsam finden wir einen passenden Termin. Der Termin gilt erst nach Absprache mit der Praxis als bestätigt.'],
        ['question' => 'Ich habe Angst vor dem Zahnarzt. Wie gehen Sie damit um?', 'answer' => 'Teilen Sie uns schon bei der Terminvereinbarung mit, wie Sie sich fühlen. Wir nehmen uns Zeit, sprechen über Ihre Sorgen und erklären die geplanten Schritte vor Beginn der Behandlung.'],
        ['question' => 'Behandeln Sie auch Kinder?', 'answer' => 'Ja, wir kümmern uns um die Zahngesundheit der ganzen Familie. Wir bieten Vorsorgeuntersuchungen und kinderzahnärztliche Leistungen an und beraten Eltern zur richtigen Mundhygiene.'],
        ['question' => 'Wie komme ich zur Praxis?', 'answer' => 'Unsere Praxis befindet sich in {address}, {city}. Der Link „Anfahrt“ öffnet Google Maps, wo Sie Ihr Verkehrsmittel auswählen können.'],
    ],
    'prices' => [
        'eyebrow' => 'UNSERE PREISE',
        'title' => 'Klarheit vom ersten',
        'accent' => 'Schritt an.',
        'note' => 'Für die Kosten einer einzelnen Leistung und einen individuellen Therapieplan wenden Sie sich bitte an unsere Praxis. Die Behandlungskosten vereinbaren wir vor Beginn der Therapie.',
        'aside_title' => 'Ihr Lächeln verdient einen guten Plan.',
        'aside_text' => 'Jedes Lächeln ist anders. Nach der Untersuchung besprechen wir die Möglichkeiten und vereinbaren gemeinsam die Therapie und ihre Kosten.',
        'items' => [
            $price('Untersuchung und Therapieplanung', 'Der erste Schritt zu einem gesunden Lächeln'),
            $price('Zahnsteinentfernung und Politur', 'Professionelle Mundhygiene'),
            $price('Kompositfüllung', 'Wiederherstellung des Zahns mit einer weißen Füllung', true),
            $price('Zahnaufhellung', 'Behandlung in der Praxis'),
            $price('Keramikkrone', 'Ästhetik und Funktion, pro Zahn', true),
            $price('Zahnimplantat', 'Nach individuellem Therapieplan', true),
        ],
    ],
    'business' => [
        'name' => 'Zahnarztpraxis Marta Bućan, Dr. med. dent.',
        'professional_title' => 'Doktorin der Zahnmedizin',
        'qualification_country' => 'Kroatien',
        'complaints' => 'Schriftliche Beschwerden richten Sie bitte an ordinacija@martabucan.com oder per Post an die Praxis: Ulica grada Chicaga 18, 10000 Zagreb, Kroatien.',
    ],
    'page_meta' => [
        'business' => ['title' => 'Impressum', 'intro' => 'Angaben zu unserer Praxis und Möglichkeiten, mit uns Kontakt aufzunehmen.'],
        'privacy' => ['title' => 'Datenschutzerklärung', 'intro' => 'Wie wir Ihre Daten verwenden und wie Sie Ihre Rechte wahrnehmen können.'],
        'cookies' => ['title' => 'Cookie-Richtlinie', 'intro' => 'Klare Informationen zur Datenspeicherung und zu den Einstellungen, die Sie selbst wählen.'],
        'terms' => ['title' => 'Nutzungsbedingungen', 'intro' => 'Informationen zu den Inhalten der Website, zur Terminvereinbarung und zur Nutzung unserer Leistungen.'],
    ],
    'legal' => [
        'privacy' => <<<'TEXT'
Über diese Erklärung
Diese Erklärung beschreibt die Verarbeitung von Daten bei der Nutzung unserer Website und bei Anfragen an die Praxis. Bei Fragen zum Datenschutz oder zur Ausübung Ihrer Rechte wenden Sie sich bitte an die angegebene E-Mail-Adresse.

Besuch der Website
Der Server kann Ihre IP-Adresse, den Zeitpunkt des Zugriffs, die angeforderte Seite und technische Browserdaten verarbeiten, um Inhalte bereitzustellen, Fehler zu beheben und Missbrauch zu verhindern. Rechtsgrundlage ist das berechtigte Interesse an einem sicheren und zuverlässigen Betrieb der Website. Die Daten werden nicht für Werbung verwendet.

Ihre Anfragen und Terminvereinbarungen
Daten, die Sie uns telefonisch oder per E-Mail mitteilen, verwenden wir zur Beantwortung Ihrer Anfrage und zur Terminvereinbarung beziehungsweise zur Bearbeitung Ihrer Anfrage vor Erbringung einer Leistung. Übermitteln Sie nur die für Ihre Anfrage erforderlichen Informationen. Senden Sie keine medizinischen Unterlagen per unverschlüsselter E-Mail. Diese Website bietet kein Formular zur Erfassung medizinischer Unterlagen.

Google Maps
Die interaktive Karte laden wir nur mit Ihrer Einwilligung. Google erhält dann Ihre IP-Adresse sowie technische Daten, die zur Kartenanzeige erforderlich sind, und kann eigene Cookies und Speichertechnologien verwenden. Sie können Ihre Einwilligung über die runde Schaltfläche für Cookie-Einstellungen unten links widerrufen. Beim Widerruf wird die eingebettete Karte entfernt. Der externe Anfahrtslink öffnet Google Maps als eigenständige Website.

Empfänger und Datenübermittlungen
Zugriff auf Daten können autorisierte Personen der Praxis sowie vertraglich gebundene Dienstleister erhalten, die für den Betrieb der Website und die Kommunikation erforderlich sind. Anbieter der Google-Dienste im Europäischen Wirtschaftsraum ist Google Ireland Limited. Ihre Nutzung kann eine Verarbeitung außerhalb des Europäischen Wirtschaftsraums nach den Richtlinien von Google und den anwendbaren Schutzmechanismen für Datenübermittlungen umfassen. Einzelheiten finden Sie in der Datenschutzerklärung von Google.

Speicherdauer
Ihre Cookie-Auswahl speichern wir im Browser für 180 Tage. Daten aus Anfragen bewahren wir so lange auf, wie es für die Beantwortung und Bearbeitung erforderlich ist, oder länger, wenn gesetzliche Verpflichtungen beziehungsweise die Geltendmachung oder Verteidigung rechtlicher Ansprüche dies erfordern. Die Dauer richtet sich nach der Art der Kommunikation und dem Verarbeitungszweck. Daten, die bei einer medizinischen Behandlung entstehen, unterliegen gesonderten Vorschriften für die medizinische Dokumentation.

Ihre Rechte
Je nach Rechtsgrundlage und den geltenden Voraussetzungen können Sie Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung oder Datenübertragbarkeit verlangen und Widerspruch gegen die Verarbeitung einlegen. Eine Einwilligung können Sie jederzeit widerrufen; die Rechtmäßigkeit der Verarbeitung vor dem Widerruf bleibt unberührt. Sie können sich bei der kroatischen Datenschutzbehörde AZOP beschweren. Auf dieser Website treffen wir keine automatisierten Entscheidungen mit rechtlicher oder ähnlich erheblicher Wirkung.
TEXT,
        'cookies' => <<<'TEXT'
Ihre Wahl
Notwendige Speicherung dient dem Betrieb und der Sicherheit der Website. Optionale Inhalte bleiben deaktiviert, bis Sie sie selbst aktivieren. Im ersten Hinweis können Sie optionale Kategorien gleichermaßen einfach akzeptieren oder ablehnen. In den Einstellungen lässt sich jede verfügbare Kategorie einzeln auswählen. Das Schließen der Einstellungen oder die weitere Nutzung der Website gilt nicht als Einwilligung.

Speicherung Ihrer Einstellungen
Der Eintrag mb_consent_v2 im lokalen Browserspeicher enthält die gewählten Kategorien, den Auswahlzeitpunkt und die Version des Hinweises. Er wird höchstens 180 Tage gespeichert und dient nicht der Nachverfolgung. Nach Ablauf oder einer Änderung des Zwecks bitten wir um eine neue Auswahl. Ist der lokale Speicher blockiert, gilt Ihre Auswahl nur während des aktuellen Besuchs.

Notwendige Laravel-Cookies
martabucan_session hält die Sitzung aufrecht, XSRF-TOKEN schützt Formulare vor gefälschten Anfragen. Die Sitzung läuft nach bis zu 120 Minuten Inaktivität ab und wird bei weiterer Nutzung verlängert. Die Anmeldung im Verwaltungsbereich benötigt diese Sicherheitsmechanismen. Die Ablehnung optionaler Cookies löscht keine Sicherheits-Cookies und meldet Administratoren nicht ab.

Google Maps
Die optionale Karte verbindet sich erst mit Google, wenn Sie die Kategorie Google Maps aktivieren oder die interaktive Karte anzeigen lassen. Google erhält Ihre IP-Adresse und technische Daten und kann eigene Cookies verwenden. Deren Namen und Speicherdauer legt Google fest; Einzelheiten finden Sie in der Cookie-Richtlinie von Google. Die Aktivierung der Karte aktiviert keine Analyse.

Änderung und Widerruf der Einwilligung
Die Einstellungen sind jederzeit über die runde Schaltfläche unten links und die Schaltfläche auf dieser Seite erreichbar. Das Deaktivieren einer Kategorie verhindert deren weiteres Laden. Die Karte wird entfernt. Wenn Sie die Einwilligung für eine aktive Analyse widerrufen, wird die Seite neu geladen, um bereits geladenen Code zu stoppen. Unsere Analyse-Cookies werden aus dem Browser entfernt. Cookies auf Google-Domains können Sie in den Einstellungen Ihres Browsers löschen.

Externe Inhalte
Schriftarten, Symbole und Fotos werden lokal bereitgestellt. Wir verwenden keine Marketing-Werkzeuge. Beim Öffnen eines externen Links, beispielsweise der Anfahrtsbeschreibung, wechseln Sie zu einer anderen Website, für die die Regeln des jeweiligen Anbieters gelten.
TEXT,
        'terms' => <<<'TEXT'
Inhalte der Website
Die Website bietet allgemeine Informationen über die Praxis und ihre Leistungen. Die Inhalte ersetzen keine zahnärztliche Untersuchung, Diagnose oder individuelle Therapieplanung. Leistungsbeschreibungen und gezeigte Beispiele stellen keine Garantie für ein individuelles Ergebnis dar.

Terminvereinbarung
Termine vereinbaren Sie direkt mit der Praxis telefonisch oder per E-Mail. Ein Klick auf einen Kontaktlink stellt keine Buchungsbestätigung dar. Ein Termin ist erst nach Absprache mit der Praxis bestätigt.

Preise und Therapieplan
Veröffentlichte Preise sind in Euro angegeben. Die Kennzeichnung „ab“ bezeichnet den Einstiegspreis für den beschriebenen Leistungsumfang. Der endgültige Preis und der Therapieplan werden nach der Untersuchung und vor Behandlungsbeginn festgelegt. Ist kein Preis angegeben, fordern Sie bitte ein individuelles Angebot der Praxis an.

Fotos, Texte und externe Links
Fotos und Texte der Praxis dürfen nicht ohne Genehmigung übernommen werden. Externe Links dienen der Unterstützung der Besucher; für Inhalte und Datenverarbeitung auf den Zielseiten sind die jeweiligen Anbieter verantwortlich.
TEXT,
    ],
];
