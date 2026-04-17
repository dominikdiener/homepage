<?php
/**
 * Admin – Dashboard (News-Übersicht)
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/revisions.php';
requireLogin();

$entries = loadNews();
$meta = loadRevisionMeta();
$draftActive = $meta['draftActive'] ?? false;
usort($entries, fn($a, $b) => strcmp($b['datum'], $a['datum']));

$flash = getFlash();
$csrf  = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-wrap">

    <div class="admin-header">
        <h1>Admin-Panel</h1>
        <div>
            <a href="../index.php" target="_blank" style="margin-right:1rem;">↗ Homepage</a>
            <a href="logout.php">Abmelden</a>
        </div>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="admin-nav-item active">News</a>
        <a href="section-edit.php?section=how" class="admin-nav-item">So funktioniert's</a>
        <a href="section-edit.php?section=value" class="admin-nav-item">Ihr Nutzen</a>
        <a href="section-edit.php?section=audiences" class="admin-nav-item">Zielgruppen</a>
        <a href="section-edit.php?section=specs" class="admin-nav-item">Techn. Daten</a>
        <a href="section-edit.php?section=chart" class="admin-nav-item">Verlaufsdaten</a>
        <a href="section-edit.php?section=kontakt" class="admin-nav-item">Kontakt</a>
        <a href="section-edit.php?section=impressum" class="admin-nav-item">Impressum</a>
        <a href="section-edit.php?section=datenschutz" class="admin-nav-item">Datenschutz</a>
        <a href="section-edit.php?section=ui" class="admin-nav-item">UI-Texte</a>
        <a href="section-edit.php?section=styling" class="admin-nav-item">Formatierung</a>
        <a href="images.php" class="admin-nav-item">Bilder</a>
        <a href="languages.php" class="admin-nav-item">Sprachen</a>
        <a href="revision-manage.php" class="admin-nav-item">Versionen</a>
        <a href="hilfe.php" class="admin-nav-item" style="margin-left:auto;">❓ Hilfe</a>
    </nav>

    <!-- Revisions-Statusbar -->
    <div style="background:<?= $draftActive ? '#FF6B1A22' : '#1A7A6E22' ?>;border:1px solid <?= $draftActive ? '#FF6B1A' : '#1A7A6E' ?>;border-radius:8px;padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
            <?php if ($draftActive): ?>
                <span style="color:#FF6B1A;font-weight:600;">✏️ Entwurf aktiv</span>
                <span style="color:#666;margin-left:8px;">
                    erstellt von <?= htmlspecialchars($meta['draftCreatedBy'] ?? '–') ?>
                    am <?= !empty($meta['draftCreatedAt']) ? date('d.m.Y H:i', strtotime($meta['draftCreatedAt'])) : '–' ?>
                </span>
            <?php else: ?>
                <span style="color:#1A7A6E;font-weight:600;">● Live: Revision <?= (int)($meta['currentRevision'] ?? 0) ?></span>
                <?php if (($meta['currentRevision'] ?? 0) === 0): ?>
                    <span style="color:#666;margin-left:8px;">(Ausgangszustand)</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <?php if ($draftActive): ?>
                <a href="/?rev=draft" target="_blank" class="btn btn-secondary" style="font-size:13px;">Vorschau</a>
                <a href="revision-action.php?action=publish" class="btn btn-primary" style="font-size:13px;">Veröffentlichen</a>
                <form method="post" action="revision-action.php" style="display:inline;" onsubmit="return confirm('Entwurf verwerfen? Alle Änderungen gehen verloren.')">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="discard_draft">
                    <button type="submit" class="btn btn-secondary" style="font-size:13px;color:#c33;">Verwerfen</button>
                </form>
            <?php else: ?>
                <form method="post" action="revision-action.php" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="create_draft">
                    <input type="hidden" name="created_by" value="Admin">
                    <button type="submit" class="btn btn-primary" style="font-size:13px;">Neuen Entwurf erstellen</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <div style="margin-bottom:1.5rem;">
        <a href="edit.php" class="btn btn-primary">+ Neuer Beitrag</a>
    </div>

    <?php if (empty($entries)): ?>
        <div class="empty-state">
            <p>Noch keine News vorhanden.</p>
            <p style="margin-top:.5rem;">Erstelle deinen ersten Beitrag mit dem Button oben.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Überschrift</th>
                    <th>Ersteller</th>
                    <th>Datum</th>
                    <th>PDFs</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($entries as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['nummer']) ?></td>
                    <td><?= htmlspecialchars($e['ueberschrift']) ?></td>
                    <td><?= htmlspecialchars($e['ersteller']) ?></td>
                    <td><?= $e['datum'] ? date('d.m.Y', strtotime($e['datum'])) : '–' ?></td>
                    <td><?= count(listPdfs($e['nummer'])) ?></td>
                    <td class="actions">
                        <a href="edit.php?nr=<?= urlencode($e['nummer']) ?>" class="btn btn-edit">Bearbeiten</a>
                        <a href="delete.php?nr=<?= urlencode($e['nummer']) ?>&token=<?= $csrf ?>"
                           class="btn btn-danger"
                           onclick="return confirm('Beitrag #<?= $e['nummer'] ?> wirklich löschen?')">Löschen</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
