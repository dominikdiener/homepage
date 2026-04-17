<?php
/**
 * Admin – Bildverwaltung & Thumbnail-Generator
 *
 * Listet alle Bilder in /assets/images/ auf und zeigt pro Datei, ob
 * ein Thumbnail existiert und aktuell ist. Über den Button unten lassen
 * sich fehlende oder veraltete Thumbnails neu erzeugen.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/thumbs.php';
requireLogin();

$csrf  = getCsrfToken();
$flash = null;

/* ═══════════════════════════════════════════════════════════
   POST: Thumbnails (neu) generieren
   ═══════════════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'regenerate') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        flashMessage('Ungültiges CSRF-Token.', 'error');
        header('Location: images.php');
        exit;
    }

    $r = regenerateThumbnails();
    $created = count($r['created']);
    $skipped = count($r['skipped']);
    $errors  = count($r['errors']);
    $saved   = $r['stats']['saved_pct'] ?? 0;

    if ($errors > 0) {
        flashMessage(
            "Erledigt: $created neu erzeugt, $skipped aktuell, $errors Fehler – " .
            implode(' | ', $r['errors']),
            'error'
        );
    } else {
        flashMessage(
            "Erledigt: $created neu erzeugt, $skipped bereits aktuell. " .
            "Thumbnails sind ca. {$saved}% kleiner als die Originale.",
            'success'
        );
    }
    header('Location: images.php');
    exit;
}

$flash = getFlash();

// Für die Anzeige: aktuellen Stand der Bilder laden
$images = listImagesWithThumbs();
$gdAvailable = function_exists('imagecreatefromjpeg');

// Summen für die Statusleiste
$origBytes  = 0;
$thumbBytes = 0;
$needsWork  = 0;
foreach ($images as $i) {
    $origBytes  += $i['orig_size'];
    $thumbBytes += $i['thumb_size'];
    if (!$i['thumb_exists'] || $i['thumb_stale']) $needsWork++;
}

function fmtSize(int $b): string {
    if ($b >= 1024 * 1024) return number_format($b / 1024 / 1024, 1, ',', '.') . ' MB';
    if ($b >= 1024)        return number_format($b / 1024, 0, ',', '.') . ' KB';
    return $b . ' B';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilder – Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .img-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .img-table th, .img-table td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        .img-table th { background: #f7f7f7; font-weight: 600; }
        .img-table tr:last-child td { border-bottom: 0; }
        .img-preview { width: 56px; height: 56px; object-fit: cover; border-radius: 4px; background: #eee; display: block; }
        .img-badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 12px; font-weight: 600; }
        .img-badge-ok    { background: #1A7A6E22; color: #1A7A6E; }
        .img-badge-miss  { background: #FF6B1A22; color: #FF6B1A; }
        .img-badge-stale { background: #FFD16622; color: #a77b00; }
        .info-card { background: #fff; border-radius: 8px; padding: 18px 22px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-top: 12px; }
        .info-stat { background: #f7f7f7; padding: 12px 14px; border-radius: 6px; }
        .info-stat-label { font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: .5px; }
        .info-stat-value { font-size: 18px; font-weight: 600; margin-top: 2px; }
    </style>
</head>
<body>
<div class="admin-wrap">

    <div class="admin-header">
        <h1>Bilder &amp; Thumbnails</h1>
        <div><a href="dashboard.php" class="btn btn-secondary">&larr; Zurück</a></div>
    </div>

    <?php if ($flash): ?>
        <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <?php if (!$gdAvailable): ?>
        <div class="flash error">
            <strong>PHP-GD-Extension nicht aktiv.</strong>
            Der Container muss mit der aktuellen Dockerfile neu gebaut werden:
            <code>./deploy.sh</code> vom Mac aus ausführen.
        </div>
    <?php endif; ?>

    <!-- Info-Box -->
    <div class="info-card">
        <h2 style="margin:0 0 6px; font-size:18px;">So funktioniert's</h2>
        <p style="margin:0; color:#555; line-height:1.55;">
            Auf der Startseite werden <strong>Thumbnails</strong> (max. 800&nbsp;px, JPEG Q&nbsp;82)
            angezeigt statt der Originale. Das spart ca. 90&nbsp;% Ladezeit. Beim Klick auf ein Bild
            wird das Original in der Lightbox geöffnet.
            Lege neue Bilder in <code>assets/images/</code> ab und klicke hier auf
            <em>„Thumbnails generieren"</em> – fehlende oder veraltete Thumbnails werden neu erzeugt,
            aktuelle werden übersprungen.
        </p>

        <div class="info-grid">
            <div class="info-stat">
                <div class="info-stat-label">Bilder</div>
                <div class="info-stat-value"><?= count($images) ?></div>
            </div>
            <div class="info-stat">
                <div class="info-stat-label">Thumbs nötig</div>
                <div class="info-stat-value" style="color:<?= $needsWork > 0 ? '#FF6B1A' : '#1A7A6E' ?>;">
                    <?= $needsWork ?>
                </div>
            </div>
            <div class="info-stat">
                <div class="info-stat-label">Originale gesamt</div>
                <div class="info-stat-value"><?= fmtSize($origBytes) ?></div>
            </div>
            <div class="info-stat">
                <div class="info-stat-label">Thumbs gesamt</div>
                <div class="info-stat-value"><?= fmtSize($thumbBytes) ?></div>
            </div>
        </div>
    </div>

    <!-- Aktions-Button -->
    <form method="post" action="images.php" style="margin-bottom:20px;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="action" value="regenerate">
        <button type="submit" class="btn btn-primary" <?= !$gdAvailable ? 'disabled' : '' ?>>
            🔄 Thumbnails generieren
        </button>
        <span style="color:#888; font-size:13px; margin-left:10px;">
            <?php if ($needsWork > 0): ?>
                <?= $needsWork ?> Bild<?= $needsWork === 1 ? '' : 'er' ?> benötig<?= $needsWork === 1 ? 't' : 'en' ?> einen Thumbnail.
            <?php else: ?>
                Alle Thumbnails sind aktuell.
            <?php endif; ?>
        </span>
    </form>

    <!-- Liste -->
    <table class="img-table">
        <thead>
            <tr>
                <th></th>
                <th>Dateiname</th>
                <th>Original</th>
                <th>Thumbnail</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($images)): ?>
                <tr><td colspan="5" style="text-align:center; color:#888; padding:30px;">
                    Keine Bilder in <code>assets/images/</code> gefunden.
                </td></tr>
            <?php else: foreach ($images as $i):
                $thumbUrl = $i['thumb_exists']
                    ? '../assets/images/thumbs/' . pathinfo($i['name'], PATHINFO_FILENAME) . '-thumb.jpg?v=' . time()
                    : '../assets/images/' . rawurlencode($i['name']);
            ?>
                <tr>
                    <td><img class="img-preview" src="<?= htmlspecialchars($thumbUrl) ?>" alt=""></td>
                    <td style="font-family:monospace; font-size:13px;"><?= htmlspecialchars($i['name']) ?></td>
                    <td><?= fmtSize($i['orig_size']) ?></td>
                    <td><?= $i['thumb_exists'] ? fmtSize($i['thumb_size']) : '–' ?></td>
                    <td>
                        <?php if (!$i['thumb_exists']): ?>
                            <span class="img-badge img-badge-miss">fehlt</span>
                        <?php elseif ($i['thumb_stale']): ?>
                            <span class="img-badge img-badge-stale">veraltet</span>
                        <?php else: ?>
                            <span class="img-badge img-badge-ok">aktuell</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>
