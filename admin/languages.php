<?php
/**
 * Admin – Sprachverwaltung
 */
require_once __DIR__ . '/auth.php';
requireLogin();

$langConfig = loadLanguagesConfig();
$flash = getFlash();
$csrf = getCsrfToken();

// POST: Sprachen speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        flashMessage('Ungültige Anfrage.', 'error');
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'save') {
            $languages = [];
            foreach ($_POST['lang'] ?? [] as $l) {
                $code = preg_replace('/[^a-z]/', '', strtolower(trim($l['code'] ?? '')));
                if (!$code) continue;
                $languages[] = [
                    'code' => $code,
                    'label' => trim($l['label'] ?? $code),
                    'flag' => trim($l['flag'] ?? ''),
                ];
            }
            if (empty($languages)) {
                $languages = [['code' => 'de', 'label' => 'Deutsch', 'flag' => '🇩🇪']];
            }

            $default = $_POST['default'] ?? 'de';
            $validCodes = array_column($languages, 'code');
            if (!in_array($default, $validCodes)) $default = $languages[0]['code'];

            // Ordner für neue Sprachen erstellen
            foreach ($languages as $l) {
                $dir = SECTIONS_DIR . '/' . $l['code'];
                if (!is_dir($dir)) mkdir($dir, 0775, true);
            }

            $langConfig = ['default' => $default, 'languages' => $languages];
            saveLanguagesConfig($langConfig);
            flashMessage('Sprachen gespeichert.');
        }
    }
    // Neu laden
    $langConfig = loadLanguagesConfig();
    $flash = getFlash();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprachen – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-container">
    <div class="admin-header">
        <h1>Sprachen verwalten</h1>
        <a href="dashboard.php" class="btn btn-secondary">← Zurück</a>
    </div>

    <?php if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="save">

        <fieldset>
            <legend>Standardsprache</legend>
            <div class="form-group">
                <select name="default">
                    <?php foreach ($langConfig['languages'] as $l): ?>
                        <option value="<?= htmlspecialchars($l['code']) ?>" <?= ($langConfig['default'] ?? 'de') === $l['code'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($l['flag'] . ' ' . $l['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </fieldset>

        <h3>Sprachen</h3>
        <div id="lang-container">
            <?php foreach ($langConfig['languages'] as $i => $l): ?>
            <div class="repeater-item repeater-inline">
                <div class="form-row">
                    <div class="form-group">
                        <label>Code (z.B. de, en)</label>
                        <input type="text" name="lang[<?= $i ?>][code]" value="<?= htmlspecialchars($l['code']) ?>" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="lang[<?= $i ?>][label]" value="<?= htmlspecialchars($l['label']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Flagge (Emoji)</label>
                        <input type="text" name="lang[<?= $i ?>][flag]" value="<?= htmlspecialchars($l['flag']) ?>" maxlength="4">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-secondary" onclick="addRepeaterItem('lang-container','lang-template')" style="margin-bottom:2rem;">+ Sprache hinzufügen</button>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Speichern</button>
        </div>
    </form>
</div>

<template id="lang-template">
    <div class="repeater-item repeater-inline">
        <div class="form-row">
            <div class="form-group">
                <label>Code (z.B. de, en)</label>
                <input type="text" name="lang[__INDEX__][code]" value="" maxlength="5">
            </div>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="lang[__INDEX__][label]" value="">
            </div>
            <div class="form-group">
                <label>Flagge (Emoji)</label>
                <input type="text" name="lang[__INDEX__][flag]" value="" maxlength="4">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRepeaterItem(this)">✕</button>
        </div>
    </div>
</template>

<script src="admin.js"></script>
</body>
</html>
