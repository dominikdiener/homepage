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
    'kategorie'         => $entry['kategorie']         ?? 'news',
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
        $form['kategorie']         = trim($_POST['kategorie'] ?? 'news');
        $form['ueberschrift']      = trim($_POST['ueberschrift'] ?? '');
        $form['unterueberschrift'] = trim($_POST['unterueberschrift'] ?? '');
        $form['langtext']          = trim($_POST['langtext'] ?? '');

        if (!$form['ueberschrift']) {
            $error = 'Bitte eine Überschrift eingeben.';
        }

        if (!$error) {
            if (!$isEdit) {
                $nr = (string) nextNummer();
                $dir = NEWS_DIR . '/' . $nr;
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                updateFilesJson($nr);
                flashMessage('Beitrag #' . $nr . ' erstellt.');
            } else {
                flashMessage('Beitrag #' . $nr . ' aktualisiert.');
            }

            saveNewsEntry([
                'nummer'            => $nr,
                'datum'             => $form['datum'],
                'ersteller'         => $form['ersteller'],
                'kategorie'         => $form['kategorie'],
                'ueberschrift'      => $form['ueberschrift'],
                'unterueberschrift' => $form['unterueberschrift'],
                'langtext'          => $form['langtext'],
            ]);

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

/* ── POST: Vorschaubild hochladen ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_preview' && $isEdit) {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Ungültige Anfrage.';
    } else {
        $type = in_array($_POST['preview_type'] ?? '', ['desktop', 'mobile']) ? $_POST['preview_type'] : '';
        if (!$type) {
            $error = 'Ungültiger Bildtyp.';
        } elseif (empty($_FILES['preview_img']['name'])) {
            $error = 'Keine Datei ausgewählt.';
        } else {
            $file = $_FILES['preview_img'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Fehler beim Upload.';
            } elseif ($file['size'] > MAX_IMG_SIZE) {
                $error = 'Datei zu groß (max. 5 MB).';
            } elseif (!in_array($ext, ALLOWED_IMG_EXT)) {
                $error = 'Nur JPG, PNG oder WebP erlaubt.';
            } else {
                $dir = NEWS_DIR . '/' . $nr;
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                // Alte Vorschaubilder dieses Typs löschen
                deletePreviewImage($nr, $type);
                $filename = "preview-{$type}.{$ext}";
                if (move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
                    $label = $type === 'desktop' ? 'Desktop' : 'Mobile';
                    flashMessage("Vorschaubild ({$label}) hochgeladen.");
                    header('Location: edit.php?nr=' . urlencode($nr));
                    exit;
                } else {
                    $error = 'Datei konnte nicht gespeichert werden.';
                }
            }
        }
    }
}

/* ── POST: Vorschaubild löschen ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_preview' && $isEdit) {
    if (verifyCsrf($_POST['csrf'] ?? '')) {
        $type = in_array($_POST['preview_type'] ?? '', ['desktop', 'mobile']) ? $_POST['preview_type'] : '';
        if ($type) {
            deletePreviewImage($nr, $type);
            $label = $type === 'desktop' ? 'Desktop' : 'Mobile';
            flashMessage("Vorschaubild ({$label}) gelöscht.");
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
    <!-- EasyMDE (Markdown-Editor mit Werkzeugleiste) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>
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

        <label for="kategorie">Kategorie</label>
        <select id="kategorie" name="kategorie">
            <?php
            $cats = [
                'news'              => 'News',
                'kampagne'          => 'Kampagne',
                'erfahrungsbericht' => 'Erfahrungsbericht',
                'messe'             => 'Messe',
            ];
            $selectedCat = strtolower($form['kategorie']);
            foreach ($cats as $val => $label):
            ?>
                <option value="<?= htmlspecialchars($val) ?>"<?= $selectedCat === $val ? ' selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="langtext">Langtext
            <small style="font-weight:normal;color:#999;">(Markdown: **fett**, *kursiv*, - Aufzählung, [Link](url))</small>
        </label>
        <textarea id="langtext" name="langtext" rows="12"><?= htmlspecialchars($form['langtext']) ?></textarea>

        <div style="margin-top:1rem;display:flex;gap:.8rem;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Speichern' : 'Erstellen' ?></button>
            <a href="dashboard.php" class="btn btn-secondary">Abbrechen</a>
        </div>
    </form>

    <!-- EasyMDE auf das Langtext-Feld anwenden -->
    <script>
        (function () {
            var textarea = document.getElementById('langtext');
            if (!textarea || typeof EasyMDE === 'undefined') return;
            var mde = new EasyMDE({
                element: textarea,
                spellChecker: false,
                autoDownloadFontAwesome: true,
                status: ['lines', 'words'],
                minHeight: '240px',
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'quote', 'unordered-list', 'ordered-list', '|',
                    'link', 'horizontal-rule', '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
            });
            // Vor dem Absenden Wert zurück in die Textarea schreiben
            textarea.form.addEventListener('submit', function () {
                mde.codemirror.save();
            });
        })();
    </script>

    <?php if ($isEdit):
        $previewDesktop = findPreviewImage($nr, 'desktop');
        $previewMobile  = findPreviewImage($nr, 'mobile');
    ?>
    <!-- Vorschaubilder -->
    <div style="background:#fff;padding:2rem;border-radius:8px;box-shadow:0 1px 6px rgba(0,0,0,.06);margin-bottom:2rem;">
        <h2 style="margin-bottom:1rem;font-size:1.2rem;">Vorschaubilder</h2>
        <p style="color:#666;font-size:.85rem;margin-bottom:1.5rem;">Optionale Vorschaubilder für die News-Seite. Desktop-Bild wird ab 768px angezeigt (statt PDF-Vorschau), Mobile-Bild darunter.</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
            <!-- Desktop -->
            <div style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
                <h3 style="font-size:.9rem;margin-bottom:.8rem;">🖥 Desktop (ab 768px)</h3>
                <?php if ($previewDesktop): ?>
                    <img src="../data/news/<?= htmlspecialchars($nr) ?>/<?= htmlspecialchars($previewDesktop) ?>" style="width:100%;border-radius:6px;margin-bottom:.8rem;" alt="Desktop-Vorschau">
                    <form method="post" style="padding:0;box-shadow:none;background:none;">
                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="delete_preview">
                        <input type="hidden" name="preview_type" value="desktop">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Desktop-Vorschaubild löschen?')" style="padding:.3rem .6rem;font-size:.8rem;">Löschen</button>
                    </form>
                <?php else: ?>
                    <div style="background:#f5f5f5;border-radius:6px;padding:2rem;text-align:center;color:#999;margin-bottom:.8rem;">Kein Bild</div>
                    <form method="post" enctype="multipart/form-data" style="padding:0;box-shadow:none;background:none;">
                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="upload_preview">
                        <input type="hidden" name="preview_type" value="desktop">
                        <input type="file" name="preview_img" accept=".jpg,.jpeg,.png,.webp" required style="font-size:.85rem;">
                        <button type="submit" class="btn btn-primary" style="margin-top:.5rem;padding:.3rem .8rem;font-size:.8rem;">Hochladen</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Mobile -->
            <div style="border:1px solid #e0e0e0;border-radius:8px;padding:1rem;">
                <h3 style="font-size:.9rem;margin-bottom:.8rem;">📱 Mobile (unter 768px)</h3>
                <?php if ($previewMobile): ?>
                    <img src="../data/news/<?= htmlspecialchars($nr) ?>/<?= htmlspecialchars($previewMobile) ?>" style="width:100%;border-radius:6px;margin-bottom:.8rem;" alt="Mobile-Vorschau">
                    <form method="post" style="padding:0;box-shadow:none;background:none;">
                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="delete_preview">
                        <input type="hidden" name="preview_type" value="mobile">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Mobile-Vorschaubild löschen?')" style="padding:.3rem .6rem;font-size:.8rem;">Löschen</button>
                    </form>
                <?php else: ?>
                    <div style="background:#f5f5f5;border-radius:6px;padding:2rem;text-align:center;color:#999;margin-bottom:.8rem;">Kein Bild</div>
                    <form method="post" enctype="multipart/form-data" style="padding:0;box-shadow:none;background:none;">
                        <input type="hidden" name="csrf" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="upload_preview">
                        <input type="hidden" name="preview_type" value="mobile">
                        <input type="file" name="preview_img" accept=".jpg,.jpeg,.png,.webp" required style="font-size:.85rem;">
                        <button type="submit" class="btn btn-primary" style="margin-top:.5rem;padding:.3rem .8rem;font-size:.8rem;">Hochladen</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

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
