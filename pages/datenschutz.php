<?php require_once __DIR__ . '/../includes/content.php'; $lang = getCurrentLang(); $ui = loadUI($lang); $datenschutz = loadContent('datenschutz', $lang); ?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/svg+xml" href="../favicon.svg">
<title>Datenschutz – Estrich Digital</title>
<link rel="stylesheet" href="../assets/css/main.css">
<?= renderCustomCSS($lang ?? null) ?>
</head>
<body>
<?= renderPreviewBanner() ?>

<nav>
  <a href="../index.php" class="nav-logo">
    <img src="../assets/images/logo.png" alt="Estrich Digital Logo"/>
  </a>
  <button class="nav-hamburger" onclick="toggleNav(this)" aria-label="Menü">
    <span></span><span></span><span></span>
  </button>
  <ul class="nav-links">
    <li><a href="../index.php#how" onclick="closeNav()"><?= e($ui['nav']['how'] ?? "So funktioniert's") ?></a></li>
    <li><a href="../index.php#value" onclick="closeNav()"><?= e($ui['nav']['value'] ?? 'Ihr Nutzen') ?></a></li>
    <li><a href="../index.php#technik" onclick="closeNav()"><?= e($ui['nav']['technik'] ?? 'Technik') ?></a></li>
    <li><a href="news.php" onclick="closeNav()"><?= e($ui['nav']['news'] ?? 'News') ?></a></li>
    <?= renderLangSwitcher() ?>
    <li><a href="kontakt.php" class="nav-cta"><?= e($ui['nav']['kontakt'] ?? 'Kontakt aufnehmen') ?></a></li>
  </ul>
</nav>

<div class="page-hero">
  <div class="page-hero-content">
    <div class="section-eyebrow"><?= e($datenschutz['heroEyebrow'] ?? 'Rechtliches') ?></div>
    <h1 class="section-title" style="font-size:clamp(32px,4vw,52px);"><?= e($datenschutz['heroTitle'] ?? 'Datenschutzerklärung') ?></h1>
    <p class="section-sub"><?= e($datenschutz['heroSub'] ?? '') ?></p>
  </div>
</div>

<div class="page-content">

<?php
// Headings after which a divider should appear (matching original HTML layout)
$dividerAfter = [
  '2. Verantwortliche Stelle',
  'Server-Log-Dateien',
  '6. Widerspruchsrecht',
  '8. Externe Dienste',
];
?>

<?php if (!empty($datenschutz['sections'])): ?>
  <?php foreach ($datenschutz['sections'] as $section): ?>
    <h2><?= htmlspecialchars($section['heading'], ENT_QUOTES, 'UTF-8') ?></h2>
    <?= $section['html'] ?>

    <?php if (in_array($section['heading'], $dividerAfter, true)): ?>
      <div class="divider"></div>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>

  <p style="font-size:13px; color:var(--grey);">Stand: Januar 2025 · Bei Fragen wenden Sie sich an <a href="mailto:info@estrich-digital.de">info@estrich-digital.de</a></p>

</div>

<footer>
  <div class="footer-left">
    <div class="footer-wordmark">ESTRICH DIGITAL</div>
    <div class="footer-meta"><?= e($ui['footer']['copyright'] ?? '') ?></div>
  </div>
  <div class="footer-right">
    <a href="impressum.php"><?= e($ui['footer']['impressum'] ?? 'Impressum') ?></a>
    <a href="datenschutz.php"><?= e($ui['footer']['datenschutz'] ?? 'Datenschutz') ?></a>
    <a href="kontakt.php"><?= e($ui['footer']['kontakt'] ?? 'Kontakt') ?></a>
  </div>
</footer>

<script src="../assets/js/main.js"></script>
</body>
</html>
