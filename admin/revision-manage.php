<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/revisions.php';

requireLogin();

$meta = loadRevisionMeta();
$currentRevision = $meta['current'] ?? 0;
$log = $meta['log'] ?? [];
$draftActive = isDraftActive();
$flash = getFlash();
$csrfToken = getCsrfToken();

$sectionLabels = [
    'how' => "So funktioniert's",
    'value' => 'Ihr Nutzen',
    'audiences' => 'Zielgruppen',
    'specs' => 'Technische Daten',
    'chart' => 'Verlaufsdaten',
    'impressum' => 'Impressum',
    'datenschutz' => 'Datenschutz',
    'kontakt' => 'Kontakt',
    'ui' => 'UI-Texte',
];

function formatDateDE(string $isoDate): string {
    $dt = new DateTime($isoDate);
    return $dt->format('d.m.Y H:i');
}

function getSectionLabels(array $sections, array $labels): string {
    $names = [];
    foreach ($sections as $key) {
        $names[] = $labels[$key] ?? $key;
    }
    return implode(', ', $names);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versionshistorie – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Versionshistorie</h1>
            <a href="dashboard.php" class="btn btn-secondary">&larr; Zur&uuml;ck</a>
        </header>

        <?php if ($flash): ?>
            <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Aktueller Status</h2>
            <p>
                <strong>Aktuelle Live-Version:</strong>
                <?php if ($currentRevision === 0): ?>
                    Ausgangszustand
                <?php else: ?>
                    Revision <?= (int)$currentRevision ?>
                <?php endif; ?>
            </p>
            <?php if ($draftActive): ?>
                <p>
                    <span style="display:inline-block;width:10px;height:10px;background:#e67e22;border-radius:50%;margin-right:6px;"></span>
                    Entwurf aktiv
                    <?php
                    $draftMeta = $meta['draft'] ?? [];
                    if (!empty($draftMeta['created_by'])): ?>
                        (erstellt von <?= htmlspecialchars($draftMeta['created_by']) ?>
                        <?php if (!empty($draftMeta['created_at'])): ?>
                            am <?= formatDateDE($draftMeta['created_at']) ?>
                        <?php endif; ?>)
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>&Auml;nderungsprotokoll</h2>
            <?php if (empty($log)): ?>
                <p>Noch keine &Auml;nderungen protokolliert.</p>
            <?php else: ?>
                <table class="admin-table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Rev. Nr.</th>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Datum</th>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Typ</th>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Ge&auml;ndert von</th>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Beauftragt von</th>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Ge&auml;nderte Bereiche</th>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Kommentar</th>
                            <th style="padding:10px 12px;border-bottom:1px solid #eee;text-align:left;">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($log) as $entry): ?>
                            <tr>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <?= (int)($entry['revision'] ?? 0) ?>
                                </td>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <?= !empty($entry['date']) ? formatDateDE($entry['date']) : '–' ?>
                                </td>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <?php if (($entry['type'] ?? '') === 'restore'): ?>
                                        Wiederherstellung (aus Rev. <?= (int)($entry['from_revision'] ?? 0) ?>)
                                    <?php else: ?>
                                        Ver&ouml;ffentlichung
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <?= htmlspecialchars($entry['changed_by'] ?? '–') ?>
                                </td>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <?= htmlspecialchars($entry['requested_by'] ?? '–') ?>
                                </td>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <?php if (!empty($entry['sections'])): ?>
                                        <?= htmlspecialchars(getSectionLabels($entry['sections'], $sectionLabels)) ?>
                                    <?php else: ?>
                                        –
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <?= htmlspecialchars($entry['comment'] ?? '–') ?>
                                </td>
                                <td style="padding:10px 12px;border-bottom:1px solid #eee;">
                                    <a href="/?rev=<?= (int)($entry['revision'] ?? 0) ?>" target="_blank">Vorschau</a>
                                    <form method="post" action="revision-action.php" style="display:inline;margin-left:8px;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="revision" value="<?= (int)($entry['revision'] ?? 0) ?>">
                                        <button type="submit" class="btn btn-secondary" onclick="return confirm('Revision <?= (int)($entry['revision'] ?? 0) ?> wirklich wiederherstellen?')">Wiederherstellen</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
