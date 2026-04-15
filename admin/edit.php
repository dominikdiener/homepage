<?php
/**
 * Admin – News erstellen / bearbeiten + PDF-Upload
 */
require_once __DIR__ . '/auth.php';
requireLogin();

$nr      = $_GET['nr'] ?? '';
$entry   = $nr ? findByNummer($nr) : null;
$isEdit  = (bool) $entry;
$error   = '';
$csrf    = getCsrfToken();

// Formularwerte
$form = [
    'datum'             => $entry['datum']             ?? date('Y-m-d'),
    'ersteller'         => $entry['ersteller']         ?? '',
    'ueberschrift'      => $entry['ueberschrift']      ?? '',
    'unterueberschrift' => $entry['unterueberschrift'] ?? '',
    'langtext'          => $entry['langtext']          ?? '',
];

/* ── POST: Beitrag speichern ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Ungültige Anfrage.';
    } else {
        $form['datum']             = trim($_POST['datum'] ?? '');
        $form['ersteller']         = trim($_POST['ersteller'] ?? '');
        $form['ueberschrift']      = trim($_POST['ueberschrift'] ?? '');
        $form['unterueberschrift'] = trim($_POST['unterueberschrift'] ?? '');
        $form['langtext']          = trim($_POST['langtext'] ?? '');

        if (!$form['ueberschrift']) {
            $error = 'Bitte eine Überschrift eingeben.';
        }

        if (!$error) {
            $entries = loadNews();

            if ($isEdit) {
                foreach ($entries as &$e) {
                    if ($e['nummer'] === $nr) {
                        $e['datum']             = $form['datum'];
                        $e['ersteller']         = $form['ersteller'];
                        $e['ueberschrift']      = $form['ueberschrift'];
                        $e['unterueberschrift'] = $form['unterueberschrift'];
                        $e['langtext']          = $form['langtext'];
                        break;
                    }
                }
                unset($e);
                flashMessage('Beitrag #' . $nr . ' aktualisiert.');
            } else {
                $nr = (string) nextNummer();
                $entries[] = [
                    'nummer'            => $nr,
                    'datum'             => $form['datum'],
                    'ersteller'         => $form['ersteller'],
                    'ueberschrift'      => $form['ueberschrift'],
                    'unterueberschrift' => $form['unterueberschrift'],
                    'langtext'          => $form['langtext'],
                ];
                // Ordner anlegen
                $dir = NEWS_DIR . '/' . $nr;
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                updateFilesJson($nr);
                flashMessage('Beitrag #' . $nr . ' erstellt.');
            }

            saveNews($entries);
            header('Location: edit.php?nr=' . urlencode($nr));
            exit;
        }
    }
}

/* ── POST: PDF hochladen ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_pdf' && $isEdit) {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Ungültige Anfrage.';
    } elseif (empty($_FILES['pdf']['name'])) {
        $error = 'Keine Datei ausgewählt.';
    } else {
        $file = $_FILES['pdf'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Fehler beim Upload.';
        } elseif ($file['size'] > MAX_PDF_SIZE) {
            $error = 'Datei zu groß (max. 10 MB).';
        } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'pdf') {
            $error = 'Nur PDF-Dateien erlaubt.';
        } else {
            $dir = NEWS_DIR . '/' . $nr;
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename = preg_replace('/[^a-zA-Z0-9._\-äöüÄÖÜß]/', '_', $file['name']);
            if (move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
                updateFilesJson($nr);
                flashMessage('PDF "' . $filename . '" hochgeladen.');
                header('Location: edit.php?nr=' . urlencode($nr));
                exit;
            } else {
                $error = 'Datei konnte nicht gespeichert werden.';
            }
        }
    }
}

/* ── POST: PDF löschen ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_pdf' && $isEdit) {
    if (verifyCsrf($_POST['csrf'] ?? '')) {
        $pdfName = basename($_POST['pdf_name'] ?? '');
        $path = NEWS_DIR . '/' . $nr . '/' . $pdfName;
        if ($pdfName && file_exists($path)) {
            unlink($path);
            updateFilesJson($nr);
            flashMessage('PDF "' . $pdfName . '" gelöscht.');
        }
        header('Location: edit.php?nr=' . urlencode($nr));
        exit;
    }
}

$pdfs = $isEdit ? listPdfs($nr) : [];
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Bearbeiten #' . htmlspecialchars($nr) : 'Neuer Beitrag' ?> – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-wrap">

    <div class="admin-header">
        <h1><?= $isEdit ? 'Beitrag #' . htmlspecialchars($nr) . ' bearbeiten' : 'Neuer Beitrag' ?></h1>
        <a href="dashboard.php">← Zurück</a>
    </div>

    <?php if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Beitrag-Formular -->
    <form method="post" style="background:#fff;padding:2rem;border-radius:8px;box-shadow:0 1px 6px rgba(0,0,0,.06);margin-bottom:2rem;">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="save">

        <label for="ueberschrift">Überschrift *</label>
        <input type="text" id="ueberschrift" name="ueberschrift" value="<?= htmlspecialchars($form['ueberschrift']) ?>" required>

        <label for="unterueberschrift">Unterüberschrift</label>
        <input type="text" id="unterueberschrift" name="unterueberschrift" value="<?= htmlspecialchars($form['unterueberschrift']) ?>">

        <label for="datum">Veröffentlichungsdatum</label>
        <input type="date" id="datum" name="datum" value="<?= htmlspecialchars($form['datum']) ?>">

        <label for="ersteller">Ersteller</label>
        <input type="text" id="ersteller" name="ersteller" value="<?= htmlspecialchars($form['ersteller']) ?>">

        <label for="langtext">Langtext</label>
        <textarea id="langtext" name="langtext" rows="8"><?= htmlspecialchars($form['langtext']) ?></textarea>

        <div style="margin-top:1rem;display:flex;gap:.8rem;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Speichern' : 'Erstellen' ?></button>
            <a href="dashboard.php" class="btn btn-secondary">Abbrechen</a>
        </div>
    </form>

    <?php if ($isEdit): ?>
    <!-- PDF-Verwaltung -->
    <div style="background:#fff;padding:2rem;border-radius:8px;box-shadow:0 1px 6px rgba(0,0,0,.06);">
        <h2 style="margin-bottom:1rem;font-size:1.2rem;">PDF-Dateien (Ordner: data/news/<?= htmlspecialchars($nr) ?>/)</h2>

        <?php if (!empty($pdfs)): ?>
            <table style="margin-bottom:1.5rem;">
                <thead>
                    <tr><th>Datei</th><th>Aktion</th></tr>
                </thead>
                <tbody>
                <?php foreach ($pdfs as $pdf): ?>
                    <tr>
                        <td>
                            <a href="../data/news/<?= htmlspecialchars($nr) ?>/<?= htmlspecialchars($pdf) ?>" target="_blank">
                                📄 <?= htmlspecialchars($pdf) ?>
                            </a>
                        </td>
                        <td>
                            <form method="post" style="display:inline;padding:0;box-shadow:none;background:none;">
                                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                <input type="hidden" name="action" value="delete_pdf">
                                <input type="hidden" name="pdf_name" value="<?= htmlspecialchars($pdf) ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('PDF löschen?')" style="padding:.3rem .6rem;font-size:.8rem;">Löschen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color:#999;margin-bottom:1rem;">Noch keine PDFs hochgeladen.</p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" style="padding:0;box-shadow:none;background:none;">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">
            <input type="hidden" name="action" value="upload_pdf">
            <label for="pdf">PDF hochladen <small style="font-weight:normal;color:#999;">(max. 10 MB)</small></label>
            <div style="display:flex;gap:.8rem;align-items:end;">
                <input type="file" id="pdf" name="pdf" accept=".pdf" required>
                <button type="submit" class="btn btn-primary">Hochladen</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
