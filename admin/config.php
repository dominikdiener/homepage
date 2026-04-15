<?php
/**
 * Admin-Konfiguration
 *
 * Standard-Passwort: "estrich2026"
 * Passwort ändern: ADMIN_DEFAULT_PASSWORD ändern, data/admin_hash.php löschen, neu einloggen.
 */

define('ADMIN_DEFAULT_PASSWORD', 'estrich2026');

// Pfade
define('DATA_DIR', __DIR__ . '/../data');
define('NEWS_DIR', DATA_DIR . '/news');
define('CSV_FILE', DATA_DIR . '/news.csv');
define('HASH_FILE', DATA_DIR . '/admin_hash.php');

// Sektionen
define('SECTIONS_DIR', DATA_DIR . '/sections');
define('LANGUAGES_FILE', SECTIONS_DIR . '/languages.json');

// Revisionen
define('REVISIONS_DIR', DATA_DIR . '/revisions');
define('DRAFT_DIR', REVISIONS_DIR . '/draft');
define('ARCHIVE_DIR', REVISIONS_DIR . '/archive');
define('REVISION_META_FILE', REVISIONS_DIR . '/meta.json');

// CSV-Trennzeichen
define('CSV_SEPARATOR', '|');

// Gültige Sektionen
define('VALID_SECTIONS', ['how', 'value', 'audiences', 'specs', 'chart', 'impressum', 'datenschutz', 'kontakt', 'ui', 'styling']);

// Upload-Limits
define('MAX_PDF_SIZE', 10 * 1024 * 1024); // 10 MB

/**
 * Passwort-Hash laden oder erstmalig generieren
 */
function getPasswordHash(): string {
    if (file_exists(HASH_FILE)) {
        return trim(file_get_contents(HASH_FILE));
    }
    $hash = password_hash(ADMIN_DEFAULT_PASSWORD, PASSWORD_DEFAULT);
    file_put_contents(HASH_FILE, $hash, LOCK_EX);
    return $hash;
}

/**
 * Alle News aus CSV laden
 */
function loadNews(): array {
    if (!file_exists(CSV_FILE)) return [];
    $lines = file(CSV_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (count($lines) < 2) return [];

    $entries = [];
    for ($i = 1; $i < count($lines); $i++) {
        $cols = explode(CSV_SEPARATOR, $lines[$i]);
        if (count($cols) < 7) continue;
        $entries[] = [
            'nummer'            => trim($cols[0]),
            'datum'             => trim($cols[1]),
            'ersteller'         => trim($cols[2]),
            'kategorie'         => trim($cols[3] ?? ''),
            'ueberschrift'      => trim($cols[4] ?? ''),
            'unterueberschrift' => trim($cols[5] ?? ''),
            'langtext'          => trim($cols[6] ?? ''),
        ];
    }
    return $entries;
}

/**
 * News in CSV speichern
 */
function saveNews(array $entries): void {
    $header = 'nummer' . CSV_SEPARATOR . 'datum' . CSV_SEPARATOR . 'ersteller' . CSV_SEPARATOR . 'kategorie' . CSV_SEPARATOR . 'ueberschrift' . CSV_SEPARATOR . 'unterueberschrift' . CSV_SEPARATOR . 'langtext';
    $lines = [$header];
    foreach ($entries as $e) {
        // Pipes im Text durch Ersatzzeichen ersetzen
        $lines[] = implode(CSV_SEPARATOR, [
            $e['nummer'],
            $e['datum'],
            str_replace('|', '/', $e['ersteller']),
            str_replace('|', '/', $e['kategorie'] ?? ''),
            str_replace('|', '/', $e['ueberschrift']),
            str_replace('|', '/', $e['unterueberschrift']),
            str_replace(["|", "\n", "\r"], ['/', ' ', ''], $e['langtext']),
        ]);
    }
    file_put_contents(CSV_FILE, implode("\n", $lines) . "\n", LOCK_EX);
}

/**
 * Nächste freie Nummer ermitteln
 */
function nextNummer(): int {
    $entries = loadNews();
    $max = 0;
    foreach ($entries as $e) {
        $n = (int) $e['nummer'];
        if ($n > $max) $max = $n;
    }
    return $max + 1;
}

/**
 * Eintrag nach Nummer finden
 */
function findByNummer(string $nummer): ?array {
    foreach (loadNews() as $e) {
        if ($e['nummer'] === $nummer) return $e;
    }
    return null;
}

/**
 * PDF-Dateien eines Ordners listen
 */
function listPdfs(string $nummer): array {
    $dir = NEWS_DIR . '/' . $nummer;
    if (!is_dir($dir)) return [];
    $files = [];
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..' || $f === 'files.json') continue;
        if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf') {
            $files[] = $f;
        }
    }
    sort($files);
    return $files;
}

/**
 * files.json aktualisieren (wird von news.html gelesen)
 */
function updateFilesJson(string $nummer): void {
    $dir = NEWS_DIR . '/' . $nummer;
    if (!is_dir($dir)) return;
    $pdfs = listPdfs($nummer);
    file_put_contents($dir . '/files.json', json_encode($pdfs, JSON_UNESCAPED_UNICODE), LOCK_EX);
}

/* ══════════════════════════════════════════
   Sektions-Verwaltung (JSON)
   ══════════════════════════════════════════ */

function isValidSection(string $name): bool {
    return in_array($name, VALID_SECTIONS);
}

function loadLanguagesConfig(): array {
    if (!file_exists(LANGUAGES_FILE)) {
        return ['default' => 'de', 'languages' => [['code' => 'de', 'label' => 'Deutsch', 'flag' => '🇩🇪']]];
    }
    return json_decode(file_get_contents(LANGUAGES_FILE), true) ?: [];
}

function saveLanguagesConfig(array $data): void {
    file_put_contents(LANGUAGES_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function loadSection(string $name, string $lang = 'de'): array {
    $file = SECTIONS_DIR . '/' . $lang . '/' . $name . '.json';
    if (!file_exists($file)) {
        $config = loadLanguagesConfig();
        $default = $config['default'] ?? 'de';
        $file = SECTIONS_DIR . '/' . $default . '/' . $name . '.json';
    }
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function saveSection(string $name, array $data, string $lang = 'de'): void {
    $dir = SECTIONS_DIR . '/' . $lang;
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $file = $dir . '/' . $name . '.json';
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function getSectionLabel(string $name): string {
    $labels = [
        'how' => "So funktioniert's",
        'value' => 'Ihr Nutzen',
        'audiences' => 'Zielgruppen',
        'specs' => 'Techn. Daten',
        'chart' => 'Verlaufsdaten',
        'kontakt' => 'Kontakt',
        'impressum' => 'Impressum',
        'datenschutz' => 'Datenschutz',
        'styling' => 'Formatierung',
    ];
    return $labels[$name] ?? $name;
}
