<?php
/**
 * Admin – Hilfe & Anleitung
 */
require_once __DIR__ . '/auth.php';
requireLogin();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilfe – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-wrap">

    <div class="admin-header">
        <h1>Hilfe &amp; Anleitung</h1>
        <div><a href="dashboard.php" class="btn btn-secondary">&larr; Zur&uuml;ck</a></div>
    </div>

    <?php if ($flash): ?>
        <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Inhaltsverzeichnis -->
    <div style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">&Uuml;bersicht</h2>
        <ol style="line-height:1.8;">
            <li><a href="#uebersicht">&Uuml;bersicht</a></li>
            <li><a href="#news">News verwalten</a></li>
            <li><a href="#sektionen">Sektionen bearbeiten</a></li>
            <li><a href="#revisionen">Revisionssystem (Entw&uuml;rfe &amp; Versionen)</a></li>
            <li><a href="#sprachen">Sprachen</a></li>
            <li><a href="#bilder">Bilder &amp; Dateien</a></li>
            <li><a href="#deployment">Deployment (Änderungen hochladen)</a></li>
            <li><a href="#kontaktformular">Kontaktformular</a></li>
            <li><a href="#tipps">Tipps &amp; H&auml;ufige Probleme</a></li>
        </ol>
    </div>

    <!-- 1. Übersicht -->
    <div id="uebersicht" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">1. &Uuml;bersicht</h2>
        <p>Das Admin-Panel verwaltet alle Inhalte der <strong>Estrich Digital Homepage</strong>. Hier k&ouml;nnen News-Eintr&auml;ge erstellt, Seitenbereiche bearbeitet, Sprachen konfiguriert und Versionen verwaltet werden.</p>
        <p>Die <strong>Tabs oben</strong> f&uuml;hren zu den einzelnen Bereichen: Dashboard (News), Sektionen, Versionen, Sprachen und Hilfe.</p>
    </div>

    <!-- 2. News verwalten -->
    <div id="news" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">2. News verwalten</h2>

        <h3>Neuen Eintrag erstellen</h3>
        <p>Im Dashboard auf <strong>&bdquo;Neuer Eintrag&ldquo;</strong> klicken. Es &ouml;ffnet sich das Formular zum Anlegen einer News.</p>

        <h3>CSV-Struktur</h3>
        <p>Die News-Daten werden als CSV gespeichert. Die Felder sind:</p>
        <ul>
            <li><strong>Nummer</strong> &ndash; Eindeutige ID des Eintrags</li>
            <li><strong>Datum</strong> &ndash; Format: TT.MM.JJJJ</li>
            <li><strong>Ersteller</strong> &ndash; Name des Autors</li>
            <li><strong>Kategorie</strong> &ndash; Wird als Filter-Button auf der News-Seite angezeigt</li>
            <li><strong>&Uuml;berschrift</strong></li>
            <li><strong>Unter&uuml;berschrift</strong></li>
            <li><strong>Langtext</strong></li>
        </ul>

        <h3>PDFs hochladen</h3>
        <p>Im Bearbeitungsmodus eines Eintrags k&ouml;nnen PDF-Dateien angeh&auml;ngt werden.</p>

        <h3>Kategorien</h3>
        <p>Kategorien werden <strong>automatisch</strong> als Filter-Buttons auf der &ouml;ffentlichen News-Seite angezeigt. Neue Kategorien entstehen einfach durch Eingabe eines neuen Namens.</p>

        <h3>Vorausplanung</h3>
        <p>Ein <strong>Datum in der Zukunft</strong> bewirkt, dass die News erst ab diesem Datum auf der Webseite sichtbar wird. So k&ouml;nnen Eintr&auml;ge im Voraus vorbereitet werden.</p>

        <h3>Direktlink</h3>
        <p>Jede News kann direkt verlinkt werden: <code>news.html#1</code> (wobei <code>1</code> die Nummer des Eintrags ist).</p>
    </div>

    <!-- 3. Sektionen bearbeiten -->
    <div id="sektionen" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">3. Sektionen bearbeiten</h2>
        <p>Unter dem Tab <strong>&bdquo;Sektionen&ldquo;</strong> k&ouml;nnen die verschiedenen Bereiche der Homepage bearbeitet werden:</p>

        <h3>So funktioniert&rsquo;s</h3>
        <p>4 Schritte mit Bildern. Der Modus (Accordion oder statisch) kann umgeschaltet werden. Bildpfade relativ eingeben:</p>
        <p><code>assets/images/step-01a.jpg</code></p>

        <h3>Ihr Nutzen</h3>
        <p>Benefit-Karten mit <strong>Icon-Auswahl</strong> (Dropdown) und Freitext-Beschreibung.</p>

        <h3>Zielgruppen</h3>
        <p>Karten mit Vorteils-Listen. Jeder Vorteil kommt in eine <strong>eigene Zeile</strong>.</p>

        <h3>Technische Daten</h3>
        <p>Wert + Label Paare (z.&nbsp;B. &bdquo;0,1&nbsp;mm&ldquo; &ndash; &bdquo;Messgenauigkeit&ldquo;).</p>

        <h3>Verlaufsdaten</h3>
        <p>Hier k&ouml;nnen nur <strong>&Uuml;berschrift und Legende</strong> bearbeitet werden. Das Diagramm selbst ist fest eingebaut.</p>

        <h3>Kontakt</h3>
        <p>Firmendaten, Adresse, E-Mail und Telefonnummer.</p>

        <h3>Impressum &amp; Datenschutz</h3>
        <p>Abschnitte mit &Uuml;berschrift und <strong>HTML-Inhalt</strong>. Hier kann direkt HTML eingegeben werden.</p>

        <h3>UI-Texte</h3>
        <p>Navigation und Footer-Beschriftungen der Webseite.</p>
    </div>

    <!-- 4. Revisionssystem -->
    <div id="revisionen" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">4. Revisionssystem (Entw&uuml;rfe &amp; Versionen)</h2>
        <p>Das Revisionssystem ist das Herzst&uuml;ck des Admin-Panels. Es sch&uuml;tzt vor versehentlichen &Auml;nderungen und erm&ouml;glicht eine kontrollierte Ver&ouml;ffentlichung.</p>

        <h3>Ohne Entwurf (Standardmodus)</h3>
        <ul>
            <li>Jede &Auml;nderung geht <strong>sofort live</strong></li>
            <li>Geeignet f&uuml;r kleine, schnelle Korrekturen</li>
        </ul>

        <h3>Mit Entwurf (empfohlen f&uuml;r gr&ouml;&szlig;ere &Auml;nderungen)</h3>
        <p>F&uuml;r umfangreichere &Auml;nderungen wird ein Entwurf empfohlen. So geht&rsquo;s:</p>
        <ol>
            <li><strong>&bdquo;Neuen Entwurf erstellen&ldquo;</strong> klicken</li>
            <li>Alle &Auml;nderungen werden nur im Entwurf gespeichert</li>
            <li>Besucher der Webseite sehen <strong>weiterhin die alte Version</strong></li>
            <li><strong>Vorschau:</strong> &Uuml;ber den &bdquo;Vorschau&ldquo;-Button oder <code>?rev=draft</code> an jede URL anh&auml;ngen</li>
            <li><strong>Ver&ouml;ffentlichen:</strong> Wenn alles passt &rarr; &bdquo;Ver&ouml;ffentlichen&ldquo; &rarr; Pflichtfelder ausf&uuml;llen &rarr; geht live</li>
            <li><strong>Verwerfen:</strong> Wenn es nicht passt &rarr; &bdquo;Verwerfen&ldquo; &rarr; alle &Auml;nderungen werden gel&ouml;scht, der alte Stand bleibt erhalten</li>
        </ol>

        <h3>Versionshistorie</h3>
        <ul>
            <li>Unter dem Tab <strong>&bdquo;Versionen&ldquo;</strong> sieht man alle bisherigen Ver&ouml;ffentlichungen</li>
            <li>Jede Version kann &uuml;ber <strong>&bdquo;Vorschau&ldquo;</strong> angesehen werden</li>
            <li>&Uuml;ber <strong>&bdquo;Wiederherstellen&ldquo;</strong> kann eine alte Version wieder live geschaltet werden</li>
            <li>Beim Wiederherstellen wird die aktuelle Version <strong>automatisch gesichert</strong></li>
        </ul>

        <h3>&Auml;nderungsprotokoll</h3>
        <p>Bei jeder Ver&ouml;ffentlichung wird automatisch protokolliert:</p>
        <ul>
            <li><strong>Wer</strong> die &Auml;nderung durchgef&uuml;hrt hat</li>
            <li><strong>Wer</strong> die &Auml;nderung beauftragt hat</li>
            <li><strong>Welche Bereiche</strong> ge&auml;ndert wurden (automatisch erkannt)</li>
            <li><strong>Datum</strong> und optionaler Kommentar</li>
        </ul>
    </div>

    <!-- 5. Sprachen -->
    <div id="sprachen" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">5. Sprachen</h2>
        <ul>
            <li><strong>Neue Sprache hinzuf&uuml;gen:</strong> Tab &bdquo;Sprachen&ldquo; &rarr; Sprachcode eingeben (z.&nbsp;B. <code>en</code>), Label und Flag-Emoji w&auml;hlen</li>
            <li><strong>Standardsprache festlegen:</strong> Eine Sprache als Standard markieren</li>
            <li><strong>Sektionen &uuml;bersetzen:</strong> Beim Bearbeiten einer Sektion oben die gew&uuml;nschte Sprache w&auml;hlen</li>
            <li><strong>Fallback:</strong> Wenn eine &Uuml;bersetzung fehlt, wird automatisch die Standardsprache (Deutsch) angezeigt</li>
        </ul>
    </div>

    <!-- 6. Bilder & Dateien -->
    <div id="bilder" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">6. Bilder &amp; Dateien</h2>
        <ul>
            <li>Bilder lokal auf dem Computer vorbereiten</li>
            <li>&Uuml;ber das Terminal bzw. Deploy-Script auf den Server hochladen</li>
            <li>Im Admin den <strong>Pfad relativ zum Root</strong> eingeben: <code>assets/images/dateiname.jpg</code></li>
        </ul>

        <h3>Empfohlene Formate</h3>
        <ul>
            <li><strong>JPG</strong> &ndash; F&uuml;r Fotos</li>
            <li><strong>PNG</strong> &ndash; F&uuml;r Grafiken mit Transparenz</li>
            <li><strong>SVG</strong> &ndash; F&uuml;r Icons</li>
        </ul>

        <h3>Lightbox</h3>
        <p>Bilder bei &bdquo;So funktioniert&rsquo;s&ldquo; werden als Thumbnails angezeigt und &ouml;ffnen sich bei Klick im Vollbild (Lightbox).</p>
    </div>

    <!-- 7. Deployment -->
    <div id="deployment" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">7. Deployment (&Auml;nderungen hochladen)</h2>
        <ul>
            <li>Code-&Auml;nderungen werden &uuml;ber das Deploy-Script hochgeladen: <code>./deploy.sh</code></li>
            <li>Das Script l&auml;dt nur <strong>Code</strong> hoch, <strong>nicht</strong> die Daten (Texte, News etc.)</li>
            <li>Daten, die &uuml;ber das Admin-Panel ge&auml;ndert wurden, <strong>bleiben auf dem Server erhalten</strong></li>
            <li>Bei Bild-Uploads: Dateien in <code>assets/images/</code> ablegen und per Deploy hochladen</li>
        </ul>
    </div>

    <!-- 8. Kontaktformular -->
    <div id="kontaktformular" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">8. Kontaktformular</h2>
        <ul>
            <li>Nachrichten werden per E-Mail an <strong>Dominik.diener@estrich-digital.de</strong> gesendet</li>
            <li>Verwendet <strong>Microsoft 365 SMTP</strong></li>
            <li>Die SMTP-Konfiguration liegt in der Datei <code>msmtprc</code> im Projektordner</li>
        </ul>
    </div>

    <!-- 9. Tipps & Häufige Probleme -->
    <div id="tipps" style="background:#fff; border-radius:8px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0;">9. Tipps &amp; H&auml;ufige Probleme</h2>

        <h3>Browser-Cache</h3>
        <p>&Auml;nderungen werden nicht sichtbar? Den <strong>Browser-Cache leeren</strong> oder <code>?v=2</code> an die URL anh&auml;ngen.</p>

        <h3>Bilder nicht sichtbar</h3>
        <p>Den Pfad pr&uuml;fen &ndash; er muss <strong>relativ</strong> sein, z.&nbsp;B. <code>assets/images/foto.jpg</code> (kein f&uuml;hrender Slash).</p>

        <h3>Berechtigungsfehler auf dem Server</h3>
        <p>Falls Dateien nicht geschrieben werden k&ouml;nnen:</p>
        <p><code>docker exec -it homepage-web-1 chmod -R 755 /var/www/html/assets/</code></p>

        <h3>Seite lokal testen</h3>
        <p>Im Homepage-Ordner folgenden Befehl ausf&uuml;hren:</p>
        <p><code>python3 -m http.server 8080</code></p>
        <p>Dann im Browser <code>http://localhost:8080</code> &ouml;ffnen.</p>
    </div>

</div>
</body>
</html>
