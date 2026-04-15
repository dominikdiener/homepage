<?php
/**
 * POST-Handler fuer alle Revisions-Aktionen
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/revisions.php';
requireLogin();

$action = $_REQUEST['action'] ?? '';

$sectionLabels = [
    'how'          => "So funktioniert's",
    'value'        => 'Ihr Nutzen',
    'audiences'    => 'Zielgruppen',
    'specs'        => 'Technische Daten',
    'chart'        => 'Verlaufsdaten',
    'impressum'    => 'Impressum',
    'datenschutz'  => 'Datenschutz',
    'kontakt'      => 'Kontakt',
    'ui'           => 'UI-Texte',
];

/* ══════════════════════════════════════════
   create_draft – Neuen Entwurf erstellen
   ══════════════════════════════════════════ */
if ($action === 'create_draft') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        flashMessage('Ungültiges CSRF-Token.', 'error');
        header('Location: dashboard.php');
        exit;
    }

    $createdBy = trim($_POST['created_by'] ?? '');
    if ($createdBy === '') {
        flashMessage('Bitte geben Sie einen Namen an.', 'error');
        header('Location: dashboard.php');
        exit;
    }

    if (createDraft($createdBy)) {
        flashMessage('Entwurf wurde erstellt.');
    } else {
        flashMessage('Es existiert bereits ein aktiver Entwurf.', 'warning');
    }

    header('Location: dashboard.php');
    exit;
}

/* ══════════════════════════════════════════
   discard_draft – Entwurf verwerfen
   ══════════════════════════════════════════ */
if ($action === 'discard_draft') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        flashMessage('Ungültiges CSRF-Token.', 'error');
        header('Location: dashboard.php');
        exit;
    }

    discardDraft();
    flashMessage('Entwurf wurde verworfen.');
    header('Location: dashboard.php');
    exit;
}

/* ══════════════════════════════════════════
   publish – Veröffentlichungsformular (GET)
   ══════════════════════════════════════════ */
if ($action === 'publish' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isDraftActive()) {
        flashMessage('Kein aktiver Entwurf vorhanden.', 'error');
        header('Location: dashboard.php');
        exit;
    }

    $changedSections = detectChangedSections();
    $csrf = getCsrfToken();
    $today = date('d.m.Y');
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entwurf veröffentlichen – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Entwurf veröffentlichen</h1>
            <div class="admin-header-actions">
                <a href="dashboard.php" class="btn btn-secondary">&larr; Zurück</a>
            </div>
        </div>

        <?php if (empty($changedSections)): ?>
            <div class="flash flash-warning">Keine Änderungen im Entwurf.</div>
            <p><a href="dashboard.php" class="btn btn-secondary">&larr; Zurück zum Dashboard</a></p>
        <?php else: ?>
            <form method="post" action="revision-action.php" class="section-form">
                <input type="hidden" name="action" value="do_publish">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-group">
                    <label class="form-label">Geänderte Sektionen</label>
                    <?php foreach ($changedSections as $sec): ?>
                        <div>
                            <label>
                                <input type="checkbox" checked disabled>
                                <?= htmlspecialchars($sectionLabels[$sec] ?? $sec) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="changed_by">Änderung durchgeführt von *</label>
                    <input type="text" id="changed_by" name="changed_by" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="requested_by">Änderung beauftragt von *</label>
                    <input type="text" id="requested_by" name="requested_by" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="comment">Kommentar</label>
                    <textarea id="comment" name="comment" class="form-input" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Datum</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($today) ?>" readonly>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Veröffentlichen</button>
                    <a href="dashboard.php" class="btn btn-secondary">Abbrechen</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
    <?php
    exit;
}

/* ══════════════════════════════════════════
   do_publish – Veröffentlichung ausführen (POST)
   ══════════════════════════════════════════ */
if ($action === 'do_publish') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        flashMessage('Ungültiges CSRF-Token.', 'error');
        header('Location: dashboard.php');
        exit;
    }

    $changedBy   = trim($_POST['changed_by'] ?? '');
    $requestedBy = trim($_POST['requested_by'] ?? '');
    $comment     = trim($_POST['comment'] ?? '');

    if ($changedBy === '' || $requestedBy === '') {
        flashMessage('Bitte füllen Sie alle Pflichtfelder aus.', 'error');
        header('Location: revision-action.php?action=publish');
        exit;
    }

    if (!isDraftActive()) {
        flashMessage('Kein aktiver Entwurf vorhanden.', 'error');
        header('Location: dashboard.php');
        exit;
    }

    publishDraft($changedBy, $requestedBy, $comment);
    flashMessage('Entwurf wurde erfolgreich veröffentlicht.');
    header('Location: revision-manage.php');
    exit;
}

/* ══════════════════════════════════════════
   restore – Bestätigungsseite (POST, zeigt Formular)
   ══════════════════════════════════════════ */
if ($action === 'restore') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        flashMessage('Ungültiges CSRF-Token.', 'error');
        header('Location: revision-manage.php');
        exit;
    }

    $revision = (int) ($_POST['revision'] ?? 0);
    if ($revision < 1) {
        flashMessage('Ungültige Revisionsnummer.', 'error');
        header('Location: revision-manage.php');
        exit;
    }

    $meta = loadRevisionMeta();
    $logEntry = null;
    foreach ($meta['log'] as $entry) {
        if (($entry['revision'] ?? 0) === $revision) {
            $logEntry = $entry;
            break;
        }
    }

    $csrf = getCsrfToken();
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revision wiederherstellen – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Revision #<?= $revision ?> wiederherstellen</h1>
            <div class="admin-header-actions">
                <a href="revision-manage.php" class="btn btn-secondary">&larr; Zurück</a>
            </div>
        </div>

        <?php if ($logEntry): ?>
            <div class="form-group">
                <p><strong>Revision:</strong> #<?= $revision ?></p>
                <p><strong>Typ:</strong> <?= $logEntry['type'] === 'publish' ? 'Veröffentlichung' : 'Wiederherstellung' ?></p>
                <p><strong>Datum:</strong> <?= htmlspecialchars(date('d.m.Y H:i', strtotime($logEntry['publishedAt'] ?? ''))) ?></p>
                <p><strong>Durchgeführt von:</strong> <?= htmlspecialchars($logEntry['changedBy'] ?? '–') ?></p>
                <p><strong>Beauftragt von:</strong> <?= htmlspecialchars($logEntry['requestedBy'] ?? '–') ?></p>
                <?php if (!empty($logEntry['changedSections'])): ?>
                    <p><strong>Geänderte Sektionen:</strong>
                        <?= htmlspecialchars(implode(', ', array_map(fn($s) => $sectionLabels[$s] ?? $s, $logEntry['changedSections']))) ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($logEntry['comment'])): ?>
                    <p><strong>Kommentar:</strong> <?= htmlspecialchars($logEntry['comment']) ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="flash flash-warning">Keine Metadaten zu dieser Revision gefunden.</div>
        <?php endif; ?>

        <form method="post" action="revision-action.php" class="section-form">
            <input type="hidden" name="action" value="do_restore">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="revision" value="<?= $revision ?>">

            <div class="form-group">
                <label class="form-label" for="changed_by">Wiederherstellung durchgeführt von *</label>
                <input type="text" id="changed_by" name="changed_by" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="requested_by">Wiederherstellung beauftragt von *</label>
                <input type="text" id="requested_by" name="requested_by" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="comment">Kommentar</label>
                <textarea id="comment" name="comment" class="form-input" rows="3"></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Wiederherstellen</button>
                <a href="revision-manage.php" class="btn btn-secondary">Abbrechen</a>
            </div>
        </form>
    </div>
</body>
</html>
    <?php
    exit;
}

/* ══════════════════════════════════════════
   do_restore – Wiederherstellung ausführen (POST)
   ══════════════════════════════════════════ */
if ($action === 'do_restore') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        flashMessage('Ungültiges CSRF-Token.', 'error');
        header('Location: revision-manage.php');
        exit;
    }

    $revision    = (int) ($_POST['revision'] ?? 0);
    $changedBy   = trim($_POST['changed_by'] ?? '');
    $requestedBy = trim($_POST['requested_by'] ?? '');
    $comment     = trim($_POST['comment'] ?? '');

    if ($revision < 1) {
        flashMessage('Ungültige Revisionsnummer.', 'error');
        header('Location: revision-manage.php');
        exit;
    }

    if ($changedBy === '' || $requestedBy === '') {
        flashMessage('Bitte füllen Sie alle Pflichtfelder aus.', 'error');
        header('Location: revision-manage.php');
        exit;
    }

    $archiveDir = ARCHIVE_DIR . '/' . $revision;
    if (!is_dir($archiveDir)) {
        flashMessage('Revision #' . $revision . ' wurde nicht im Archiv gefunden.', 'error');
        header('Location: revision-manage.php');
        exit;
    }

    restoreRevision($revision, $changedBy, $requestedBy, $comment);
    flashMessage('Revision #' . $revision . ' wurde erfolgreich wiederhergestellt.');
    header('Location: revision-manage.php');
    exit;
}

/* ══════════════════════════════════════════
   Unbekannte Aktion
   ══════════════════════════════════════════ */
flashMessage('Unbekannte Aktion.', 'error');
header('Location: dashboard.php');
exit;
