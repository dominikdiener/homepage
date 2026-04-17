<?php require_once __DIR__ . '/../includes/content.php'; $lang = getCurrentLang(); $ui = loadUI($lang); $impressum = loadContent('impressum', $lang); ?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/svg+xml" href="../favicon.svg">
<title>Impressum – Estrich Digital</title>
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
    <li><a href="/news" onclick="closeNav()"><?= e($ui['nav']['news'] ?? 'News') ?></a></li>
    <?= renderLangSwitcher() ?>
    <li><a href="kontakt.php" class="nav-cta"><?= e($ui['nav']['kontakt'] ?? 'Kontakt aufnehmen') ?></a></li>
  </ul>
</nav>

<div class="page-hero">
  <div class="page-hero-content">
    <div class="section-eyebrow"><?= e($impressum['heroEyebrow'] ?? 'Rechtliches') ?></div>
    <h1 class="section-title" style="font-size:clamp(32px,4vw,52px);"><?= e($impressum['heroTitle'] ?? 'Impressum') ?></h1>
    <p class="section-sub"><?= e($impressum['heroSub'] ?? '') ?></p>
  </div>
</div>

<div class="page-content">

<?php if (!empty($impressum['sections'])): ?>
  <?php foreach ($impressum['sections'] as $i => $section): ?>
    <h2><?= htmlspecialchars($section['heading'], ENT_QUOTES, 'UTF-8') ?></h2>
    <?= $section['html'] ?>

    <?php
    // Insert dividers after specific sections to match original layout
    $heading = $section['heading'];
    $nextHeading = $impressum['sections'][$i + 1]['heading'] ?? '';
    if ($heading === 'Umsatzsteuer-ID' || $heading === 'Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV'):
    ?>
      <div class="divider"></div>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>

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
