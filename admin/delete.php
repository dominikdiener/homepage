<?php
/**
 * Admin – Beitrag löschen
 */
require_once __DIR__ . '/auth.php';
requireLogin();

$nr    = $_GET['nr'] ?? '';
$token = $_GET['token'] ?? '';

if ($nr && verifyCsrf($token)) {
    $entries = loadNews();
    $entries = array_filter($entries, fn($e) => $e['nummer'] !== $nr);
    saveNews(array_values($entries));

    // Ordner löschen
    $dir = NEWS_DIR . '/' . $nr;
    if (is_dir($dir)) {
        foreach (scandir($dir) as $f) {
            if ($f !== '.' && $f !== '..') unlink($dir . '/' . $f);
        }
        rmdir($dir);
    }

    flashMessage('Beitrag #' . $nr . ' gelöscht.');
} else {
    flashMessage('Ungültige Anfrage.', 'error');
}

header('Location: dashboard.php');
exit;
