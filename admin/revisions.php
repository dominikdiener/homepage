<?php
/**
 * Revisionsverwaltung – Hilfsfunktionen
 */
require_once __DIR__ . '/config.php';

/* ══════════════════════════════════════════
   Hilfsfunktionen
   ══════════════════════════════════════════ */

/**
 * Alle Sprach-Codes aus languages.json laden
 */
function getLanguageCodes(): array {
    if (!file_exists(LANGUAGES_FILE)) return ['de'];
    $config = json_decode(file_get_contents(LANGUAGES_FILE), true) ?: [];
    return array_column($config['languages'] ?? [], 'code') ?: ['de'];
}

/**
 * Verzeichnis rekursiv loeschen
 */
function recursiveDelete(string $dir): void {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            recursiveDelete($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

/**
 * Verzeichnis rekursiv kopieren
 */
function copyDirectory(string $src, string $dst): void {
    if (!is_dir($src)) return;
    if (!is_dir($dst)) mkdir($dst, 0775, true);
    $items = scandir($src);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $srcPath = $src . '/' . $item;
        $dstPath = $dst . '/' . $item;
        if (is_dir($srcPath)) {
            copyDirectory($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
}

/* ══════════════════════════════════════════
   Meta-Verwaltung
   ══════════════════════════════════════════ */

/**
 * Revision-Metadaten laden
 */
function loadRevisionMeta(): array {
    $defaults = [
        'currentRevision'    => 0,
        'draftActive'        => false,
        'draftCreatedAt'     => null,
        'draftCreatedBy'     => null,
        'nextRevisionNumber' => 1,
        'log'                => [],
    ];
    if (!file_exists(REVISION_META_FILE)) {
        return $defaults;
    }
    $data = json_decode(file_get_contents(REVISION_META_FILE), true);
    return $data ?: $defaults;
}

/**
 * Revision-Metadaten speichern
 */
function saveRevisionMeta(array $meta): void {
    $dir = dirname(REVISION_META_FILE);
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    file_put_contents(
        REVISION_META_FILE,
        json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

/**
 * Pruefen, ob ein Entwurf aktiv ist
 */
function isDraftActive(): bool {
    $meta = loadRevisionMeta();
    return !empty($meta['draftActive']);
}

/* ══════════════════════════════════════════
   Draft-Verwaltung
   ══════════════════════════════════════════ */

/**
 * Neuen Entwurf erstellen: Live-Inhalte in Draft kopieren
 *
 * @return bool false wenn bereits ein Draft existiert
 */
function createDraft(string $createdBy): bool {
    $meta = loadRevisionMeta();

    if (!empty($meta['draftActive'])) {
        return false;
    }

    $languages = getLanguageCodes();

    foreach ($languages as $lang) {
        $srcDir = SECTIONS_DIR . '/' . $lang;
        $dstDir = DRAFT_DIR . '/' . $lang;
        if (!is_dir($srcDir)) continue;
        if (!is_dir($dstDir)) mkdir($dstDir, 0775, true);

        foreach (VALID_SECTIONS as $section) {
            $srcFile = $srcDir . '/' . $section . '.json';
            if (file_exists($srcFile)) {
                copy($srcFile, $dstDir . '/' . $section . '.json');
            }
        }
    }

    $meta['draftActive']    = true;
    $meta['draftCreatedAt'] = date('c');
    $meta['draftCreatedBy'] = $createdBy;
    saveRevisionMeta($meta);

    return true;
}

/**
 * Entwurf verwerfen
 */
function discardDraft(): void {
    recursiveDelete(DRAFT_DIR);

    $meta = loadRevisionMeta();
    $meta['draftActive']    = false;
    $meta['draftCreatedAt'] = null;
    $meta['draftCreatedBy'] = null;
    saveRevisionMeta($meta);
}

/**
 * Geaenderte Sektionen erkennen: Draft vs. Live vergleichen
 *
 * @return array Liste der Sektionsnamen, die sich unterscheiden
 */
function detectChangedSections(): array {
    $changed   = [];
    $languages = getLanguageCodes();

    foreach (VALID_SECTIONS as $section) {
        foreach ($languages as $lang) {
            $liveFile  = SECTIONS_DIR . '/' . $lang . '/' . $section . '.json';
            $draftFile = DRAFT_DIR . '/' . $lang . '/' . $section . '.json';

            $liveContent  = file_exists($liveFile) ? file_get_contents($liveFile) : '';
            $draftContent = file_exists($draftFile) ? file_get_contents($draftFile) : '';

            if ($liveContent !== $draftContent) {
                $changed[] = $section;
                break; // Sektion ist geaendert, naechste pruefen
            }
        }
    }

    return $changed;
}

/* ══════════════════════════════════════════
   Publish / Restore
   ══════════════════════════════════════════ */

/**
 * Entwurf veroeffentlichen
 *
 * @return array Log-Eintrag der Veroeffentlichung
 */
function publishDraft(string $changedBy, string $requestedBy, string $comment = ''): array {
    $meta      = loadRevisionMeta();
    $revNumber = $meta['nextRevisionNumber'];
    $languages = getLanguageCodes();

    // a) Aktuelle Live-Version archivieren
    $archiveDir = ARCHIVE_DIR . '/' . $revNumber;
    foreach ($languages as $lang) {
        $srcDir = SECTIONS_DIR . '/' . $lang;
        $dstDir = $archiveDir . '/' . $lang;
        if (is_dir($srcDir)) {
            copyDirectory($srcDir, $dstDir);
        }
    }

    // b) Geaenderte Sektionen erkennen (vor dem Kopieren)
    $changedSections = detectChangedSections();

    // c) Draft nach Live kopieren
    foreach ($languages as $lang) {
        $srcDir = DRAFT_DIR . '/' . $lang;
        $dstDir = SECTIONS_DIR . '/' . $lang;
        if (!is_dir($srcDir)) continue;
        if (!is_dir($dstDir)) mkdir($dstDir, 0775, true);

        foreach (VALID_SECTIONS as $section) {
            $srcFile = $srcDir . '/' . $section . '.json';
            if (file_exists($srcFile)) {
                copy($srcFile, $dstDir . '/' . $section . '.json');
            }
        }
    }

    // d) Log-Eintrag erstellen
    $logEntry = [
        'revision'        => $revNumber,
        'type'            => 'publish',
        'publishedAt'     => date('c'),
        'changedBy'       => $changedBy,
        'requestedBy'     => $requestedBy,
        'changedSections' => $changedSections,
        'comment'         => $comment,
    ];

    // e) Meta aktualisieren
    array_unshift($meta['log'], $logEntry);
    $meta['currentRevision']    = $revNumber;
    $meta['nextRevisionNumber'] = $revNumber + 1;
    $meta['draftActive']        = false;
    $meta['draftCreatedAt']     = null;
    $meta['draftCreatedBy']     = null;

    saveRevisionMeta($meta);

    // f) Draft-Verzeichnis loeschen
    recursiveDelete(DRAFT_DIR);

    return $logEntry;
}

/**
 * Frueheren Stand wiederherstellen
 *
 * @return array Log-Eintrag der Wiederherstellung
 */
function restoreRevision(int $revNumber, string $changedBy, string $requestedBy, string $comment = ''): array {
    $meta      = loadRevisionMeta();
    $languages = getLanguageCodes();

    // a) Aktiven Draft verwerfen
    if (!empty($meta['draftActive'])) {
        discardDraft();
        $meta = loadRevisionMeta();
    }

    // b) Aktuelle Live-Version archivieren
    $currentRev    = $meta['currentRevision'];
    $newRevNumber  = $meta['nextRevisionNumber'];

    if ($currentRev > 0) {
        foreach ($languages as $lang) {
            $srcDir = SECTIONS_DIR . '/' . $lang;
            $dstDir = ARCHIVE_DIR . '/' . $currentRev . '/' . $lang;
            if (is_dir($srcDir)) {
                copyDirectory($srcDir, $dstDir);
            }
        }
    }

    // c) Archiv-Version nach Live kopieren
    foreach ($languages as $lang) {
        $srcDir = ARCHIVE_DIR . '/' . $revNumber . '/' . $lang;
        $dstDir = SECTIONS_DIR . '/' . $lang;
        if (!is_dir($srcDir)) continue;
        if (!is_dir($dstDir)) mkdir($dstDir, 0775, true);

        foreach (VALID_SECTIONS as $section) {
            $srcFile = $srcDir . '/' . $section . '.json';
            if (file_exists($srcFile)) {
                copy($srcFile, $dstDir . '/' . $section . '.json');
            }
        }
    }

    // d) Geaenderte Sektionen erkennen
    $changedSections = [];
    foreach (VALID_SECTIONS as $section) {
        foreach ($languages as $lang) {
            $oldFile = ARCHIVE_DIR . '/' . $currentRev . '/' . $lang . '/' . $section . '.json';
            $newFile = SECTIONS_DIR . '/' . $lang . '/' . $section . '.json';

            $oldContent = file_exists($oldFile) ? file_get_contents($oldFile) : '';
            $newContent = file_exists($newFile) ? file_get_contents($newFile) : '';

            if ($oldContent !== $newContent) {
                $changedSections[] = $section;
                break;
            }
        }
    }

    // e) Log-Eintrag erstellen
    $logEntry = [
        'revision'        => $newRevNumber,
        'type'            => 'restore',
        'publishedAt'     => date('c'),
        'changedBy'       => $changedBy,
        'requestedBy'     => $requestedBy,
        'restoredFrom'    => $revNumber,
        'changedSections' => $changedSections,
        'comment'         => $comment,
    ];

    // f) Meta aktualisieren
    array_unshift($meta['log'], $logEntry);
    $meta['currentRevision']    = $newRevNumber;
    $meta['nextRevisionNumber'] = $newRevNumber + 1;
    $meta['draftActive']        = false;
    $meta['draftCreatedAt']     = null;
    $meta['draftCreatedBy']     = null;

    saveRevisionMeta($meta);

    return $logEntry;
}

/* ══════════════════════════════════════════
   Sektions-Zugriff (Draft / Archiv)
   ══════════════════════════════════════════ */

/**
 * Sektion aus dem Entwurf laden, Fallback auf Live
 */
function loadSectionFromDraft(string $name, string $lang = 'de'): array {
    $draftFile = DRAFT_DIR . '/' . $lang . '/' . $name . '.json';
    if (file_exists($draftFile)) {
        return json_decode(file_get_contents($draftFile), true) ?: [];
    }
    return loadSection($name, $lang);
}

/**
 * Sektion im Entwurf speichern
 */
function saveSectionToDraft(string $name, array $data, string $lang = 'de'): void {
    $dir = DRAFT_DIR . '/' . $lang;
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $file = $dir . '/' . $name . '.json';
    file_put_contents(
        $file,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

/**
 * Sektion aus einer archivierten Revision laden
 */
function loadSectionFromArchive(int $rev, string $name, string $lang = 'de'): array {
    $file = ARCHIVE_DIR . '/' . $rev . '/' . $lang . '/' . $name . '.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}
