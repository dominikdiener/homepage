<?php
/**
 * News-Helpers für die öffentliche Seite.
 * - Liest data/news/<nr>/article.json
 * - Generiert SEO-freundliche Slugs (YYYYMMDD-titel)
 * - Stellt Artikel-Lookup nach Slug bereit
 */

define('NEWS_PUBLIC_DIR', __DIR__ . '/../data/news');

/**
 * Slug aus Datum + Titel erzeugen.
 * Beispiel: 2026-04-16 + "Das Problem – Warum herkömmliche Estrichmessung …"
 *        → 20260416-das-problem-warum-herkoemmliche-estrichmessung
 */
function newsSlug(string $datum, string $title): string {
    // Datum robust parsen (unterstützt YYYY-MM-DD und DD.MM.YYYY)
    $ts = strtotime(str_replace('.', '-', $datum)) ?: 0;
    $datePart = $ts ? date('Ymd', $ts) : '';

    // Umlaute und Sonderzeichen transliterieren
    $t = $title;
    $t = strtr($t, [
        'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
        'Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue',
        'é' => 'e', 'è' => 'e', 'ê' => 'e',
        'á' => 'a', 'à' => 'a', 'â' => 'a',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u',
        'ñ' => 'n', 'ç' => 'c',
    ]);
    $t = strtolower($t);

    // Alles, was kein Buchstabe/Zahl ist → Bindestrich
    $t = preg_replace('/[^a-z0-9]+/u', '-', $t);
    $t = trim($t, '-');

    // Länge begrenzen (SEO-Empfehlung: ~60–80 Zeichen)
    if (strlen($t) > 80) {
        $t = substr($t, 0, 80);
        $t = trim($t, '-');
    }

    return $datePart && $t ? $datePart . '-' . $t : ($datePart ?: $t);
}

/**
 * Alle News-Artikel für die öffentliche Ausgabe laden
 * (inkl. Slug, PDF-Liste, Vorschaubilder).
 *
 * Artikel mit Datum in der Zukunft werden ausgeblendet (Scheduling):
 * Redakteure können Beiträge vorbereiten; sie erscheinen erst ab dem
 * angegebenen Datumstag auf der öffentlichen Seite.
 */
function loadPublicNews(): array {
    $articles = [];
    if (!is_dir(NEWS_PUBLIC_DIR)) return $articles;

    // Alle Daten bis einschließlich heute (Ende Tag, Serverzeit) gelten als sichtbar.
    $visibleUntil = strtotime(date('Y-m-d') . ' 23:59:59') ?: time();

    foreach (scandir(NEWS_PUBLIC_DIR) as $f) {
        if ($f === '.' || $f === '..') continue;
        $dir = NEWS_PUBLIC_DIR . '/' . $f;
        if (!is_dir($dir)) continue;
        $jsonPath = $dir . '/article.json';
        if (!file_exists($jsonPath)) continue;

        $entry = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($entry)) continue;

        $nr = (string) ($entry['nummer'] ?? $f);

        // Verknüpfte PDFs
        $files = [];
        $filesJson = $dir . '/files.json';
        if (file_exists($filesJson)) {
            $files = json_decode(file_get_contents($filesJson), true) ?: [];
        }

        // Vorschaubilder
        $previewDesktop = null;
        $previewMobile  = null;
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (!$previewDesktop && file_exists("$dir/preview-desktop.$ext")) $previewDesktop = "preview-desktop.$ext";
            if (!$previewMobile  && file_exists("$dir/preview-mobile.$ext"))  $previewMobile  = "preview-mobile.$ext";
        }

        $title = (string) ($entry['ueberschrift'] ?? '');
        $datum = (string) ($entry['datum'] ?? '');

        // Zukünftige Artikel (Datum > heute) werden NICHT ausgeliefert.
        // Artikel ohne/mit ungültigem Datum werden sicherheitshalber gezeigt
        // (damit ein leeres Datum nicht alles ausblendet).
        $articleTs = strtotime(str_replace('.', '-', $datum));
        if ($articleTs !== false && $articleTs > $visibleUntil) {
            continue;
        }

        $articles[] = [
            'nummer'         => $nr,
            'slug'           => newsSlug($datum, $title),
            'date'           => $datum,
            'author'         => (string) ($entry['ersteller'] ?? ''),
            'category'       => strtolower((string) ($entry['kategorie'] ?? '')),
            'title'          => $title,
            'subtitle'       => (string) ($entry['unterueberschrift'] ?? ''),
            'content'        => (string) ($entry['langtext'] ?? ''),
            'files'          => $files,
            'previewDesktop' => $previewDesktop,
            'previewMobile'  => $previewMobile,
        ];
    }

    // Neueste zuerst
    usort($articles, function ($a, $b) {
        $da = strtotime(str_replace('.', '-', $a['date'])) ?: 0;
        $db = strtotime(str_replace('.', '-', $b['date'])) ?: 0;
        return $db - $da;
    });

    return $articles;
}

/**
 * Einen Artikel per Slug finden.
 * Akzeptiert den vollen Slug (YYYYMMDD-titel) – zur Fehlertoleranz
 * wird auch der reine Datumsteil (YYYYMMDD) als Fallback geprüft.
 */
function findArticleBySlug(string $slug): ?array {
    $slug = trim($slug, "/ \t\n\r\0\x0B");
    if ($slug === '') return null;

    $articles = loadPublicNews();

    // 1) Exakter Slug
    foreach ($articles as $a) {
        if ($a['slug'] === $slug) return $a;
    }

    // 2) Fallback: nur Datumsteil (z.B. wenn Titel sich geändert hat → 301 beim Caller möglich)
    $datePart = explode('-', $slug, 2)[0] ?? '';
    if (preg_match('/^\d{8}$/', $datePart)) {
        foreach ($articles as $a) {
            $aDate = explode('-', $a['slug'], 2)[0] ?? '';
            if ($aDate === $datePart) return $a;
        }
    }

    return null;
}

/**
 * Öffentliche URL eines Artikels (relativ zur Domainwurzel)
 */
function newsUrl(array $article): string {
    return '/news/' . $article['slug'];
}
