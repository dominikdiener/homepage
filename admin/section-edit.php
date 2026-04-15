<?php
/**
 * Admin – Generischer Sektions-Editor
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/revisions.php';
requireLogin();

$section = $_GET['section'] ?? '';
$lang = $_GET['lang'] ?? 'de';
$draftActive = isDraftActive();

if (!isValidSection($section)) {
    flashMessage('Ungültige Sektion.', 'error');
    header('Location: dashboard.php');
    exit;
}

$langConfig = loadLanguagesConfig();
$validLangs = array_column($langConfig['languages'], 'code');
if (!in_array($lang, $validLangs)) $lang = $langConfig['default'] ?? 'de';

// POST: Daten speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        flashMessage('Ungültige Anfrage.', 'error');
    } else {
        $data = [];

        switch ($section) {
            case 'how':
                $data = [
                    'displayMode' => !empty($_POST['displayMode']) ? 'accordion' : 'static',
                    'sectionEyebrow' => trim($_POST['sectionEyebrow'] ?? ''),
                    'sectionTitle' => trim($_POST['sectionTitle'] ?? ''),
                    'sectionSub' => trim($_POST['sectionSub'] ?? ''),
                    'steps' => [],
                ];
                foreach ($_POST['steps'] ?? [] as $s) {
                    $data['steps'][] = [
                        'number' => trim($s['number'] ?? ''),
                        'tag' => trim($s['tag'] ?? ''),
                        'title' => trim($s['title'] ?? ''),
                        'description' => trim($s['description'] ?? ''),
                        'detail' => trim($s['detail'] ?? ''),
                        'image1' => trim($s['image1'] ?? ''),
                        'image1Alt' => trim($s['image1Alt'] ?? ''),
                        'image2' => trim($s['image2'] ?? ''),
                        'image2Alt' => trim($s['image2Alt'] ?? ''),
                    ];
                }
                break;

            case 'value':
                $data = [
                    'sectionEyebrow' => trim($_POST['sectionEyebrow'] ?? ''),
                    'sectionTitle' => trim($_POST['sectionTitle'] ?? ''),
                    'benefits' => [],
                ];
                foreach ($_POST['cards'] ?? [] as $c) {
                    $data['benefits'][] = [
                        'iconSvg' => trim($c['iconSvg'] ?? ''),
                        'title' => trim($c['title'] ?? ''),
                        'description' => trim($c['description'] ?? ''),
                    ];
                }
                break;

            case 'audiences':
                $data = [
                    'sectionEyebrow' => trim($_POST['sectionEyebrow'] ?? ''),
                    'sectionTitle' => trim($_POST['sectionTitle'] ?? ''),
                    'audiences' => [],
                ];
                foreach ($_POST['cards'] ?? [] as $c) {
                    $benefits = array_filter(array_map('trim', explode("\n", $c['benefits'] ?? '')));
                    $data['audiences'][] = [
                        'cssClass' => trim($c['cssClass'] ?? ''),
                        'label' => trim($c['label'] ?? ''),
                        'title' => trim($c['title'] ?? ''),
                        'description' => trim($c['description'] ?? ''),
                        'benefits' => array_values($benefits),
                    ];
                }
                break;

            case 'specs':
                $data = [
                    'sectionEyebrow' => trim($_POST['sectionEyebrow'] ?? ''),
                    'sectionTitle' => trim($_POST['sectionTitle'] ?? ''),
                    'sectionSub' => trim($_POST['sectionSub'] ?? ''),
                    'specs' => [],
                ];
                foreach ($_POST['items'] ?? [] as $it) {
                    $data['specs'][] = [
                        'value' => trim($it['value'] ?? ''),
                        'label' => trim($it['label'] ?? ''),
                    ];
                }
                break;

            case 'chart':
                $data = [
                    'sectionEyebrow' => trim($_POST['sectionEyebrow'] ?? ''),
                    'headerTitle' => trim($_POST['headerTitle'] ?? ''),
                    'legend' => [],
                ];
                foreach ($_POST['legend'] ?? [] as $l) {
                    $data['legend'][] = [
                        'type' => trim($l['type'] ?? 'dot'),
                        'color' => trim($l['color'] ?? ''),
                        'label' => trim($l['label'] ?? ''),
                    ];
                }
                break;

            case 'kontakt':
                $data = [
                    'heroEyebrow' => trim($_POST['heroEyebrow'] ?? ''),
                    'heroTitle' => trim($_POST['heroTitle'] ?? ''),
                    'heroSub' => trim($_POST['heroSub'] ?? ''),
                    'companyName' => trim($_POST['companyName'] ?? ''),
                    'companyDescription' => trim($_POST['companyDescription'] ?? ''),
                    'address' => trim($_POST['address'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'phoneHref' => trim($_POST['phoneHref'] ?? ''),
                    'responseNote' => trim($_POST['responseNote'] ?? ''),
                ];
                break;

            case 'impressum':
            case 'datenschutz':
                $data = [
                    'heroEyebrow' => trim($_POST['heroEyebrow'] ?? ''),
                    'heroTitle' => trim($_POST['heroTitle'] ?? ''),
                    'heroSub' => trim($_POST['heroSub'] ?? ''),
                    'sections' => [],
                ];
                foreach ($_POST['sections'] ?? [] as $s) {
                    $data['sections'][] = [
                        'heading' => trim($s['heading'] ?? ''),
                        'html' => trim($s['html'] ?? ''),
                    ];
                }
                break;

            case 'styling':
                $data = [
                    'css' => trim($_POST['css'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                ];
                break;

            case 'ui':
                $data = [
                    'nav' => [
                        'how' => trim($_POST['nav_how'] ?? ''),
                        'value' => trim($_POST['nav_value'] ?? ''),
                        'technik' => trim($_POST['nav_technik'] ?? ''),
                        'news' => trim($_POST['nav_news'] ?? ''),
                        'kontakt' => trim($_POST['nav_kontakt'] ?? ''),
                    ],
                    'footer' => [
                        'impressum' => trim($_POST['footer_impressum'] ?? ''),
                        'datenschutz' => trim($_POST['footer_datenschutz'] ?? ''),
                        'kontakt' => trim($_POST['footer_kontakt'] ?? ''),
                        'copyright' => trim($_POST['footer_copyright'] ?? ''),
                    ],
                ];
                break;
        }

        if ($draftActive) {
            saveSectionToDraft($section, $data, $lang);
            flashMessage(getSectionLabel($section) . ' (' . strtoupper($lang) . ') im Entwurf gespeichert.');
        } else {
            saveSection($section, $data, $lang);
            flashMessage(getSectionLabel($section) . ' (' . strtoupper($lang) . ') gespeichert.');
        }
    }
}

// Daten laden (aus Draft wenn aktiv, sonst Live)
$data = $draftActive ? loadSectionFromDraft($section, $lang) : loadSection($section, $lang);
$flash = getFlash();
$csrf = getCsrfToken();
$sectionLabel = getSectionLabel($section);

// Formular-Template bestimmen
$templateFile = __DIR__ . '/templates/form-' . $section . '.php';
if (!file_exists($templateFile)) {
    flashMessage('Template nicht gefunden.', 'error');
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sectionLabel) ?> bearbeiten – Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><?= htmlspecialchars($sectionLabel) ?> bearbeiten</h1>
            <div class="admin-header-actions">
                <?php if (count($langConfig['languages']) > 1): ?>
                <div class="lang-select">
                    <?php foreach ($langConfig['languages'] as $l): ?>
                        <a href="?section=<?= $section ?>&lang=<?= $l['code'] ?>"
                           class="lang-btn <?= $l['code'] === $lang ? 'active' : '' ?>">
                            <?= $l['flag'] ?> <?= strtoupper($l['code']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <a href="dashboard.php" class="btn btn-secondary">← Zurück</a>
            </div>
        </div>

        <?php if ($draftActive): ?>
            <div class="flash flash-warning" style="background:#FF6B1A22;border-color:#FF6B1A;color:#FF6B1A;">
                ✏️ Sie bearbeiten den <strong>Entwurf</strong>. Änderungen werden erst nach Veröffentlichung auf der Live-Seite sichtbar.
                <a href="/?rev=draft" target="_blank" style="color:#FF6B1A;margin-left:8px;">Vorschau →</a>
            </div>
        <?php endif; ?>

        <?php if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <?php if ($section === 'styling' && !$draftActive): ?>
            <div class="flash flash-warning" style="background:#FFF3E0;border:1px solid #FF9800;color:#E65100;">
                🔒 CSS kann nur über das Revisionssystem bearbeitet werden.
                <a href="dashboard.php" style="color:#E65100;text-decoration:underline;">Erstellen Sie zuerst einen Entwurf.</a>
            </div>
        <?php else: ?>
        <form method="post" class="section-form">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">

            <?php include $templateFile; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Speichern</button>
                <a href="dashboard.php" class="btn btn-secondary">Abbrechen</a>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script src="admin.js"></script>
</body>
</html>
