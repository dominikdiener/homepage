<?php
/**
 * Content-Loader für das Frontend
 * Liest JSON-Dateien aus data/sections/{lang}/
 */

define('CONTENT_SECTIONS_DIR', __DIR__ . '/../data/sections');
define('CONTENT_LANGUAGES_FILE', CONTENT_SECTIONS_DIR . '/languages.json');

/**
 * Aktive Sprache ermitteln (URL-Parameter > Cookie > Standard)
 */
function getCurrentLang(): string {
    $languages = loadLanguages();
    $default = $languages['default'] ?? 'de';
    $validCodes = array_column($languages['languages'] ?? [], 'code');

    // URL-Parameter hat Vorrang
    if (!empty($_GET['lang']) && in_array($_GET['lang'], $validCodes)) {
        $lang = $_GET['lang'];
        setcookie('lang', $lang, time() + 365 * 24 * 3600, '/');
        return $lang;
    }

    // Cookie
    if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], $validCodes)) {
        return $_COOKIE['lang'];
    }

    return $default;
}

/**
 * Sprachen-Config laden
 */
function loadLanguages(): array {
    if (!file_exists(CONTENT_LANGUAGES_FILE)) {
        return ['default' => 'de', 'languages' => [['code' => 'de', 'label' => 'Deutsch', 'flag' => '🇩🇪']]];
    }
    return json_decode(file_get_contents(CONTENT_LANGUAGES_FILE), true) ?: [];
}

/**
 * Revisions-Vorschau ermitteln (nur für eingeloggte Admins)
 */
function getRevisionPreview(): ?string {
    if (!isset($_GET['rev'])) return null;

    // Session nur starten wenn rev-Parameter vorhanden
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Nur Admins dürfen Revisionen ansehen
    if (empty($_SESSION['admin_logged_in'])) return null;

    return $_GET['rev']; // 'draft' oder eine Nummer
}

/**
 * Sektions-Inhalt laden (mit Fallback auf Standardsprache)
 * Unterstützt Revisions-Vorschau: ?rev=draft oder ?rev=2
 */
function loadContent(string $section, ?string $lang = null): array {
    if ($lang === null) $lang = getCurrentLang();

    // Revisions-Vorschau prüfen
    $rev = getRevisionPreview();
    if ($rev !== null) {
        $revFile = null;
        $revDir = __DIR__ . '/../data/revisions';

        if ($rev === 'draft') {
            $revFile = $revDir . '/draft/' . $lang . '/' . $section . '.json';
        } elseif (is_numeric($rev)) {
            $revFile = $revDir . '/archive/' . intval($rev) . '/' . $lang . '/' . $section . '.json';
        }

        if ($revFile && file_exists($revFile)) {
            return json_decode(file_get_contents($revFile), true) ?: [];
        }
        // Fallback: wenn Datei in Revision nicht existiert, lade Live
    }

    $file = CONTENT_SECTIONS_DIR . '/' . $lang . '/' . $section . '.json';
    if (!file_exists($file)) {
        $languages = loadLanguages();
        $default = $languages['default'] ?? 'de';
        $file = CONTENT_SECTIONS_DIR . '/' . $default . '/' . $section . '.json';
    }
    if (!file_exists($file)) return [];

    return json_decode(file_get_contents($file), true) ?: [];
}

/**
 * Preview-Banner rendern (wenn Revision aktiv)
 */
function renderPreviewBanner(): string {
    $rev = getRevisionPreview();
    if ($rev === null) return '';

    $label = ($rev === 'draft') ? 'Entwurf' : 'Revision ' . intval($rev);
    $currentUrl = strtok($_SERVER['REQUEST_URI'], '?');

    return '<div style="position:fixed;top:0;left:0;right:0;background:#FF6B1A;color:#fff;text-align:center;padding:10px;z-index:999999;font-size:14px;font-family:sans-serif;">'
        . '⚠️ Vorschau: ' . htmlspecialchars($label) . ' (nicht live) &nbsp;|&nbsp; '
        . '<a href="' . htmlspecialchars($currentUrl) . '" style="color:#fff;text-decoration:underline;">Live-Version anzeigen</a>'
        . '</div>';
}

/**
 * UI-Texte laden (Navigation, Footer, etc.)
 */
function loadUI(?string $lang = null): array {
    return loadContent('ui', $lang);
}

/**
 * HTML-sicher ausgeben
 */
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Custom CSS aus styling.json rendern
 */
function renderCustomCSS(?string $lang = null): string {
    $styling = loadContent('styling', $lang);
    if (empty($styling['css'])) return '';
    return '<style id="custom-css">' . $styling['css'] . '</style>';
}

/**
 * Sprachmenü HTML generieren
 */
function renderLangSwitcher(): string {
    $languages = loadLanguages();
    $current = getCurrentLang();
    if (count($languages['languages'] ?? []) <= 1) return '';

    $html = '<div class="lang-switcher">';
    foreach ($languages['languages'] as $l) {
        $active = $l['code'] === $current ? ' class="lang-active"' : '';
        $html .= '<a href="?lang=' . e($l['code']) . '"' . $active . '>' . $l['flag'] . ' ' . strtoupper($l['code']) . '</a>';
    }
    $html .= '</div>';
    return $html;
}
