# Estrich Digital – Website

## Ordnerstruktur

```
estrich-digital/
├── index.html                  ← Startseite (Homepage)
├── .htaccess                   ← Apache-Konfiguration (Weiterleitungen, Fehlerseiten)
│
├── assets/
│   ├── css/
│   │   └── main.css            ← Gemeinsames Stylesheet für alle Seiten
│   ├── js/
│   │   └── main.js             ← Gemeinsames JavaScript (Scroll-Reveal, Formular)
│   ├── images/
│   │   └── logo.png            ← Estrich Digital Logo
│   ├── fonts/                  ← (optional) Lokale Web-Fonts ablegen
│   └── icons/                  ← (optional) Favicons, App-Icons
│
├── pages/
│   ├── kontakt.html            ← Kontaktseite mit Formular
│   ├── impressum.html          ← Impressum (§ 5 TMG)
│   └── datenschutz.html        ← Datenschutzerklärung (DSGVO)
│
├── downloads/                  ← Downloadbereich (PDFs, Datenblätter etc.)
│   └── (z.B. datenblatt-sensor.pdf)
│
└── docs/                       ← Interne Dokumente / Dokumentation
    └── (z.B. api-docs.md)
```

## Deployment

### Apache / cPanel / klassisches Webhosting

1. Alle Dateien via FTP/SFTP in das **public_html** Verzeichnis hochladen
2. Sicherstellen dass `.htaccess` hochgeladen ist (versteckte Datei!)
3. Domain auf das Verzeichnis zeigen lassen

### NGINX

In der NGINX-Konfiguration den `root` auf das Verzeichnis setzen:

```nginx
server {
    listen 80;
    server_name estrich-digital.de www.estrich-digital.de;
    root /var/www/estrich-digital;
    index index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    # Gzip-Komprimierung
    gzip on;
    gzip_types text/css application/javascript image/svg+xml;
}
```

### Kontaktformular

Das Formular sendet aktuell keine echte E-Mail (Simulation in `assets/js/main.js`).
Für echten E-Mail-Versand eine der folgenden Optionen einbauen:

- **PHP-Mailer**: `pages/kontakt.php` als Backend anlegen
- **Formspree**: `action="https://formspree.io/f/DEIN_ID"` im Formular
- **Netlify Forms**: `netlify`-Attribut zum `<form>`-Tag hinzufügen (bei Netlify-Hosting)

### Favicons

Favicons in `assets/icons/` ablegen und in alle HTML-Seiten im `<head>` verlinken:

```html
<link rel="icon" type="image/png" href="/assets/icons/favicon-32x32.png" sizes="32x32">
<link rel="apple-touch-icon" href="/assets/icons/apple-touch-icon.png">
```

## Seiten im Überblick

| Seite | URL | Beschreibung |
|---|---|---|
| Homepage | `/` oder `/index.html` | Hero, Funktionsweise, Nutzen, Zielgruppen, Technik, Chart |
| Kontakt | `/pages/kontakt.html` | Kontaktformular + Unternehmensdaten |
| Impressum | `/pages/impressum.html` | Pflichtangaben § 5 TMG |
| Datenschutz | `/pages/datenschutz.html` | DSGVO-Erklärung |
