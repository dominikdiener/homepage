# Estrich Digital - Marketing Homepage

## Projekt

Statische Marketing-Website fuer Estrich Digital - ein IoT-basiertes Produkt zur Feuchtigkeitsmessung in Estrich. Die Zielgruppe ist der DACH-Markt (Deutschland, Oesterreich, Schweiz). Alle Inhalte sind auf Deutsch.

## Tech Stack

- **Kein Framework** - Reines HTML5, CSS3, Vanilla JavaScript (ES6+)
- **Kein Build-Prozess** - Keine Transpilation, kein Bundling, kein npm
- **Server**: Apache (`.htaccess` fuer Redirects, Caching, Security Headers)
- **Fonts**: Google Fonts (DM Sans, Space Mono)
- **Grafiken**: Inline SVGs, Logo als PNG

## Projektstruktur

```
index.html              # Hauptseite (Hero, Features, Technik, CTA)
.htaccess               # Apache-Konfiguration
assets/
  css/main.css          # Gemeinsame Styles, CSS-Variablen, Design-System
  js/main.js            # Scroll-Reveal, Nav-Effekte, Formular-Handling
  images/logo.png       # Logo
pages/
  kontakt.html          # Kontaktformular (aktuell simuliert, kein Backend)
  impressum.html        # Impressum (§ 5 TMG)
  datenschutz.html      # Datenschutzerklaerung (DSGVO)
downloads/              # Platzhalter fuer PDFs/Datenblaetter
docs/                   # Platzhalter fuer interne Dokumentation
```

## Design-System (CSS-Variablen)

```css
--orange: #FF6B1A     /* Primaerfarbe / CTA */
--teal: #1A7A6E       /* Sekundaerfarbe */
--dark: #0F1A1C       /* Hintergrund */
--mid: #1C2E32        /* Card-Hintergrund */
--light: #E8EEF0      /* Text */
--grey: #8BA5AB       /* Sekundaerer Text */
--accent: #FFD166     /* Highlights */
```

Dark-Theme-Design mit Glassmorphism-Navigation.

## Lokal starten

```bash
python3 -m http.server 8000
# oder
npx http-server
```

## Konventionen

- Seitenspezifisches CSS wird inline in `<style>`-Tags der jeweiligen Seite geschrieben
- Gemeinsame Styles liegen in `assets/css/main.css`
- Scroll-Animationen werden ueber die CSS-Klasse `.reveal` gesteuert (IntersectionObserver)
- Semantisches HTML mit korrekter Heading-Hierarchie
- Mobile-First mit `clamp()` fuer fluide Typografie

## Bekannte Einschraenkungen

- Kontaktformular ist aktuell eine Simulation (kein Backend-Endpoint)
- `downloads/`, `docs/`, `assets/fonts/`, `assets/icons/` sind noch leere Platzhalter-Verzeichnisse
