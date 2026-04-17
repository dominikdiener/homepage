<?php
require_once __DIR__ . '/includes/content.php';
$lang = getCurrentLang();
$ui   = loadUI($lang);

/**
 * Aus einem Bildpfad in assets/images/ den dazugehörigen Thumbnail-Pfad
 * in assets/images/thumbs/<name>-thumb.jpg ableiten.
 * Existiert der Thumbnail nicht, wird das Original zurückgegeben.
 */
function thumbPath(string $orig): string {
    if (!preg_match('#^(.*assets/images/)([^/]+)\.(jpe?g|png|webp)$#i', $orig, $m)) {
        return $orig;
    }
    $thumbRel = $m[1] . 'thumbs/' . pathinfo($m[2], PATHINFO_FILENAME) . '-thumb.jpg';
    $thumbAbs = __DIR__ . '/' . ltrim($thumbRel, '/');
    return file_exists($thumbAbs) ? $thumbRel : $orig;
}
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/svg+xml" href="favicon.svg">
<meta name="description" content="Estrich Digital – Der digitale Nachweis für trockenen Estrich. IoT-Feuchtemessung direkt im Estrich, manipulationssicher und rechtssicher dokumentiert.">
<title>Estrich Digital – Der digitale Nachweis für trockenen Estrich</title>
<link rel="stylesheet" href="assets/css/main.css">
<style>
  /* ===== HERO ===== */
  .hero {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    padding: 120px 64px 80px;
    position: relative;
    overflow: hidden;
    gap: 48px;
  }

  .hero::before {
    content: '';
    position: absolute;
    top: -200px; right: -200px;
    width: 700px; height: 700px;
    background: radial-gradient(circle, rgba(255,107,26,0.07) 0%, transparent 65%);
    pointer-events: none;
  }

  .hero::after {
    content: '';
    position: absolute;
    bottom: -100px; left: 30%;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(26,122,110,0.06) 0%, transparent 65%);
    pointer-events: none;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--orange-dim);
    border: 1px solid rgba(255,107,26,0.3);
    border-radius: 20px;
    padding: 6px 16px;
    font-family: var(--font-mono);
    font-size: var(--fs-eyebrow);
    font-weight: 700;
    color: var(--orange);
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 28px;
    opacity: 0;
    animation: fadeUp 0.6s 0.1s forwards;
  }

  .hero-badge-dot {
    width: 6px; height: 6px;
    background: var(--orange);
    border-radius: 50%;
    animation: pulse 2s infinite;
  }

  .hero-h1 {
    font-size: var(--fs-hero-title);
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -1px;
    margin-bottom: 24px;
    opacity: 0;
    animation: fadeUp 0.6s 0.25s forwards;
  }

  .hero-h1 em { font-style: normal; color: var(--orange); }

  .hero-sub {
    font-size: var(--fs-hero-sub);
    font-weight: 300;
    line-height: 1.7;
    color: var(--grey);
    margin-bottom: 40px;
    opacity: 0;
    animation: fadeUp 0.6s 0.4s forwards;
  }

  .hero-actions {
    display: flex;
    gap: 16px;
    align-items: center;
    opacity: 0;
    animation: fadeUp 0.6s 0.55s forwards;
  }

  .hero-visual {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    animation: fadeIn 1s 0.6s forwards;
  }

  /* ===== HOW IT WORKS ===== */
  .how { padding: 96px 64px; background: rgba(0,0,0,0.15); }

  .how-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 56px;
    gap: 32px;
    flex-wrap: wrap;
  }

  .how-steps {
    display: flex;
    flex-direction: column;
    gap: 2px;
    background: rgba(255,255,255,0.05);
    border-radius: 20px;
    overflow: hidden;
  }

  .how-step {
    background: var(--mid);
    padding: 40px 36px;
    position: relative;
    transition: background 0.3s;
  }

  .how-step:hover { background: var(--mid2); }

  .how-step-num {
    font-family: var(--font-mono);
    font-size: 64px;
    font-weight: 700;
    color: rgba(255,107,26,0.07);
    position: absolute;
    top: 12px; right: 20px;
    line-height: 1;
    user-select: none;
  }

  .how-step-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--orange-dim);
    border: 1px solid rgba(255,107,26,0.25);
    border-radius: 16px;
    padding: 3px 12px;
    font-family: var(--font-mono);
    font-size: var(--fs-label);
    font-weight: 700;
    color: var(--orange);
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 18px;
  }

  .how-step h3 {
    font-size: var(--fs-card-title);
    font-weight: 700;
    margin-bottom: 8px;
  }

  .how-step p {
    font-size: var(--fs-card-text);
    color: var(--grey);
    line-height: 1.65;
  }

  /* Accordion */
  .how-step {
    cursor: pointer;
    user-select: none;
  }

  .how-step-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }

  .how-step-header-left {
    flex: 1;
    min-width: 0;
  }

  .how-step-toggle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--orange-dim);
    border: 1px solid rgba(255,107,26,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: transform 0.35s ease, background 0.3s;
  }

  .how-step-toggle svg {
    width: 16px;
    height: 16px;
    stroke: var(--orange);
    stroke-width: 2;
    fill: none;
  }

  .how-step.active .how-step-toggle {
    transform: rotate(180deg);
    background: rgba(255,107,26,0.2);
  }

  .how-step-detail {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.45s ease, opacity 0.35s ease;
    opacity: 0;
  }

  .how-step.active .how-step-detail {
    opacity: 1;
  }

  .how-step-detail-inner {
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.06);
    margin-top: 20px;
  }

  .how-step-detail-text {
    font-size: var(--fs-card-text);
    color: var(--grey);
    line-height: 1.7;
    margin-bottom: 20px;
  }

  .how-step-images {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
  }

  .how-step-images img {
    width: 100%;
    max-width: 320px;
    border-radius: 12px;
    object-fit: cover;
    aspect-ratio: 4/3;
    background: var(--mid2);
    border: 1px solid rgba(255,255,255,0.06);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .how-step-images img:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
  }

  /* ===== LIGHTBOX ===== */
  .lightbox-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.9);
    z-index: 99999;
    justify-content: center;
    align-items: center;
    cursor: zoom-out;
  }

  .lightbox-overlay.active {
    display: flex;
  }

  .lightbox-overlay img {
    max-width: 90vw;
    max-height: 90vh;
    border-radius: 8px;
    object-fit: contain;
    box-shadow: 0 0 40px rgba(0,0,0,0.6);
  }

  .lightbox-close {
    position: fixed;
    top: 20px;
    right: 24px;
    font-size: 36px;
    color: #fff;
    cursor: pointer;
    z-index: 100000;
    background: none;
    border: none;
    font-family: sans-serif;
    line-height: 1;
  }

  /* ===== VALUE GRID ===== */
  .value-section { padding: 96px 64px; }

  .value-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2px;
    margin-top: 56px;
    background: rgba(255,255,255,0.04);
    border-radius: 20px;
    overflow: hidden;
  }

  .value-card {
    background: var(--mid);
    padding: 48px;
    transition: background 0.3s;
    position: relative;
    overflow: hidden;
  }

  .value-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 3px; height: 100%;
    background: var(--orange);
    opacity: 0;
    transition: opacity 0.3s;
  }

  .value-card:hover { background: var(--mid2); }
  .value-card:hover::before { opacity: 1; }

  .value-icon {
    width: 46px; height: 46px;
    background: var(--orange-dim);
    border: 1px solid rgba(255,107,26,0.25);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 22px;
  }

  .value-card h3 {
    font-size: var(--fs-card-title);
    font-weight: 700;
    margin-bottom: 10px;
  }

  .value-card p {
    font-size: var(--fs-card-text);
    color: var(--grey);
    line-height: 1.7;
  }

  /* ===== AUDIENCES ===== */
  .audiences-section { padding: 96px 64px; background: rgba(0,0,0,0.15); }

  .audience-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-top: 56px;
  }

  .audience-card {
    background: var(--mid);
    border-radius: 20px;
    padding: 48px;
    border: 1px solid rgba(255,255,255,0.05);
    transition: border-color 0.3s, transform 0.3s;
    position: relative;
    overflow: hidden;
  }

  .audience-card::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    opacity: 0.06;
    transition: opacity 0.3s;
  }

  .audience-card.gu::after  { background: var(--orange); }
  .audience-card.her::after { background: var(--teal); }

  .audience-card:hover { border-color: rgba(255,255,255,0.12); transform: translateY(-4px); }
  .audience-card:hover::after { opacity: 0.12; }

  .audience-label {
    font-family: var(--font-mono);
    font-size: var(--fs-label);
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 14px;
    font-weight: 700;
  }

  .audience-card.gu  .audience-label { color: var(--orange); }
  .audience-card.her .audience-label { color: var(--teal); }

  .audience-card h3 { font-size: var(--fs-card-title); font-weight: 700; margin-bottom: 14px; line-height: 1.25; }
  .audience-card p  { font-size: var(--fs-card-text); color: var(--grey); line-height: 1.7; margin-bottom: 28px; }

  .benefits { list-style: none; display: flex; flex-direction: column; gap: 10px; }

  .benefits li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: var(--fs-card-text);
    color: var(--light);
    opacity: 0.85;
  }

  .benefits li span.check {
    font-family: var(--font-mono);
    font-size: var(--fs-label);
    margin-top: 2px;
    flex-shrink: 0;
  }

  .audience-card.gu  .check { color: var(--orange); }
  .audience-card.her .check { color: var(--teal); }

  /* ===== SPECS ===== */
  .specs-section { padding: 96px 64px; }

  .specs-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2px;
    background: rgba(255,255,255,0.04);
    border-radius: 20px;
    overflow: hidden;
    margin-top: 56px;
  }

  .spec-item {
    background: var(--mid);
    padding: 36px 28px;
    text-align: center;
    transition: background 0.3s;
  }

  .spec-item:hover { background: var(--mid2); }

  .spec-value {
    font-family: var(--font-mono);
    font-size: var(--fs-spec-value);
    font-weight: 700;
    color: var(--orange);
    margin-bottom: 6px;
  }

  .spec-label { font-size: var(--fs-spec-label); color: var(--grey); line-height: 1.4; }

  /* ===== CHART ===== */
  .chart-wrap-outer { padding: 0 64px 96px; }

  .chart-section-inner {
    background: var(--mid);
    border-radius: 24px;
    padding: 60px;
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 36px;
    flex-wrap: wrap;
    gap: 20px;
  }

  .chart-legend { display: flex; gap: 20px; flex-wrap: wrap; }

  .legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: var(--font-mono);
    font-size: var(--fs-label);
    color: var(--grey);
  }

  .legend-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
  .legend-dash { width: 16px; height: 2px; flex-shrink: 0; border-top: 2px dashed; }

  .chart-bg { background: rgba(0,0,0,0.3); border-radius: 14px; padding: 24px 20px 12px; }

  /* ===== CTA ===== */
  .cta-section {
    padding: 96px 64px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .cta-section::before {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 800px; height: 400px;
    background: radial-gradient(ellipse, rgba(255,107,26,0.07) 0%, transparent 65%);
    pointer-events: none;
  }

  .cta-section .section-sub { margin: 0 auto 40px; }

  .cta-actions { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }

  .cta-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    max-width: 900px;
    margin: 0 auto;
    text-align: left;
  }

  .cta-card {
    padding: 36px 32px;
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: relative;
    transition: border-color .3s, transform .3s;
    cursor: pointer;
  }
  .cta-card:hover { transform: translateY(-2px); }

  .cta-card--info {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
  }
  .cta-card--info:hover { border-color: rgba(26,122,110,0.4); }

  .cta-card--action {
    background: rgba(255,107,26,0.06);
    border: 1px solid rgba(255,107,26,0.2);
  }
  .cta-card--action:hover { border-color: rgba(255,107,26,0.5); }

  .cta-card-eyebrow {
    font-family: var(--font-mono);
    font-size: var(--fs-eyebrow);
    letter-spacing: 2px;
    text-transform: uppercase;
  }
  .cta-card--info .cta-card-eyebrow { color: var(--teal); }
  .cta-card--action .cta-card-eyebrow { color: var(--orange); }

  .cta-card h3 {
    font-size: var(--fs-card-title);
    font-weight: 600;
    color: var(--light);
    line-height: 1.35;
  }

  .cta-card p {
    font-size: var(--fs-card-text);
    color: var(--grey);
    line-height: 1.65;
    flex: 1;
  }

  .cta-card-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: var(--font-mono);
    font-size: var(--fs-card-text);
    font-weight: 600;
    text-decoration: none;
    transition: gap .2s;
  }
  .cta-card-link:hover { gap: 12px; }
  .cta-card--info .cta-card-link { color: var(--teal); }
  .cta-card--action .cta-card-link { color: var(--orange); }

  /* ===== MOBILE ===== */
  @media (max-width: 768px) {
    .hero, .how, .value-section, .audiences-section, .specs-section, .chart-wrap-outer, .cta-section, .trust-bar {
      position: relative;
      z-index: 1;
    }

    .hero {
      grid-template-columns: 1fr;
      padding: 100px 20px 50px;
      min-height: auto;
      gap: 32px;
    }

    .hero-visual { order: -1; max-height: 300px; }
    .hero-visual img { max-height: 280px; width: auto; }

    .hero-h1 { font-size: 32px; }
    .hero-sub { font-size: 16px; }

    .hero-actions { flex-direction: column; gap: 12px; }
    .hero-actions a { text-align: center; width: 100%; }

    .how { padding: 50px 20px; }
    .how-step { padding: 24px 20px; }
    .how-step-num { font-size: 40px; top: 8px; right: 12px; }
    .how-step h3 { font-size: 17px; }

    .how-step-images { flex-direction: column; }
    .how-step-images img { max-width: 100%; }

    .value-section { padding: 50px 20px; }
    .value-grid { grid-template-columns: 1fr; }
    .value-card { padding: 32px 24px; }

    .audiences-section { padding: 50px 20px; }
    .audience-grid { grid-template-columns: 1fr; }
    .audience-card { padding: 32px 24px; }

    .specs-section { padding: 50px 20px; }
    .specs-grid { grid-template-columns: repeat(2, 1fr); }
    .spec-item { padding: 24px 16px; }
    .spec-value { font-size: 20px; }

    .chart-wrap-outer { padding: 0 20px 50px; }
    .chart-section-inner { padding: 30px 16px; }
    .chart-header { flex-direction: column; align-items: flex-start; }

    .cta-section { padding: 50px 20px; }
    .cta-cards { grid-template-columns: 1fr; }
    .cta-card { padding: 28px 24px; }
  }
</style>
<?= renderCustomCSS($lang) ?>
</head>
<body>
<?= renderPreviewBanner() ?>

<!-- NAV -->
<nav>
  <a href="index.php" class="nav-logo">
    <img src="assets/images/logo.png" alt="Estrich Digital Logo"/>
  </a>
  <button class="nav-hamburger" onclick="toggleNav(this)" aria-label="Menü">
    <span></span><span></span><span></span>
  </button>
  <ul class="nav-links">
    <li><a href="#how" onclick="closeNav()"><?= e($ui['nav']['how'] ?? "So funktioniert's") ?></a></li>
    <li><a href="#value" onclick="closeNav()"><?= e($ui['nav']['value'] ?? 'Ihr Nutzen') ?></a></li>
    <li><a href="#technik" onclick="closeNav()"><?= e($ui['nav']['technik'] ?? 'Technik') ?></a></li>
    <li><a href="/news"><?= e($ui['nav']['news'] ?? 'News') ?></a></li>
    <?= renderLangSwitcher() ?>
    <li><a href="pages/kontakt.php" class="nav-cta"><?= e($ui['nav']['kontakt'] ?? 'Kontakt aufnehmen') ?></a></li>
  </ul>
</nav>

<!-- HERO -->
<section class="hero">
  <div>
    <div class="hero-badge">
      <span class="hero-badge-dot"></span>
      Zum Patent angemeldete IoT-Technologie
    </div>
    <h1 class="hero-h1">Der digitale Nachweis<br>für <em>trockenen Estrich</em></h1>
    <p class="hero-sub">Kontinuierliche Feuchtemessung direkt im Estrich – manipulationssicher und transparent dokumentiert. Damit Termine eingehalten und Trocknungserfolge bewiesen werden.</p>
    <div class="hero-actions">
      <a href="#how" class="btn-primary">Wie es funktioniert</a>
      <a href="pages/kontakt.php" class="btn-secondary">Demo anfragen →</a>
    </div>
  </div>

  <div class="hero-visual">
    <div class="float">
      <img src="assets/images/hero-sensor.png" alt="Estrich Digital Sensor – Querschnitt" style="width:100%; max-width:460px; height:auto;">
    </div>
  </div>
</section>

<!-- TRUST BAR -->
<div class="trust-bar">
  <span class="trust-label">Technologie</span>
  <div class="trust-items">
    <span class="trust-item">✓&nbsp; Zum Patent angemeldet</span>
    <span class="trust-item">✓&nbsp; 8 kapazitive Messflächen</span>
    <span class="trust-item">✓&nbsp; LTE-M IoT & NB-IoT</span>
    <span class="trust-item">✓&nbsp; 50×50×120 mm</span>
    <span class="trust-item">✓&nbsp; 24/7 Messung</span>
    <span class="trust-item">✓&nbsp; Entwickelt seit 2019</span>
  </div>
</div>

<!-- HOW IT WORKS -->
<?php $how = loadContent('how', $lang); ?>
<section class="how" id="how">
  <div class="how-header reveal">
    <div>
      <div class="section-eyebrow"><?= e($how['sectionEyebrow'] ?? "So funktioniert's") ?></div>
      <h2 class="section-title"><?= nl2br(e($how['sectionTitle'] ?? '')) ?></h2>
    </div>
    <p class="section-sub" style="max-width:340px"><?= e($how['sectionSub'] ?? '') ?></p>
  </div>
  <?php $isStatic = ($how['displayMode'] ?? 'accordion') === 'static'; ?>
  <div class="how-steps<?= $isStatic ? ' how-steps--static' : '' ?> reveal">
    <?php foreach (($how['steps'] ?? []) as $step): ?>
    <div class="how-step<?= $isStatic ? ' active' : '' ?>"<?= $isStatic ? '' : ' onclick="toggleStep(this)"' ?>>
      <div class="how-step-num"><?= e($step['number'] ?? '') ?></div>
      <div class="how-step-header">
        <div class="how-step-header-left">
          <div class="how-step-tag"><?= e($step['tag'] ?? '') ?></div>
          <h3><?= e($step['title'] ?? '') ?></h3>
          <p><?= e($step['description'] ?? '') ?></p>
        </div>
        <?php if (!$isStatic): ?>
        <div class="how-step-toggle"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></div>
        <?php endif; ?>
      </div>
      <div class="how-step-detail"<?= $isStatic ? ' style="max-height:none;opacity:1"' : '' ?>>
        <div class="how-step-detail-inner">
          <p class="how-step-detail-text"><?= nl2br(e($step['detail'] ?? '')) ?></p>
          <?php if (!empty($step['image1']) || !empty($step['image2'])): ?>
          <div class="how-step-images">
            <?php if (!empty($step['image1'])): ?>
            <img src="<?= e(thumbPath($step['image1'])) ?>" data-full="<?= e($step['image1']) ?>" alt="<?= e($step['image1Alt'] ?? '') ?>" loading="lazy">
            <?php endif; ?>
            <?php if (!empty($step['image2'])): ?>
            <img src="<?= e(thumbPath($step['image2'])) ?>" data-full="<?= e($step['image2']) ?>" alt="<?= e($step['image2Alt'] ?? '') ?>" loading="lazy">
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- VALUE -->
<?php $value = loadContent('value', $lang); ?>
<section class="value-section" id="value">
  <div class="reveal">
    <div class="section-eyebrow"><?= e($value['sectionEyebrow'] ?? 'Ihr Nutzen') ?></div>
    <h2 class="section-title"><?= nl2br(e($value['sectionTitle'] ?? '')) ?></h2>
  </div>
  <div class="value-grid reveal">
    <?php foreach (($value['benefits'] ?? []) as $card): ?>
    <div class="value-card">
      <div class="value-icon">
        <?= $card['iconSvg'] ?? '' ?>
      </div>
      <h3><?= e($card['title'] ?? '') ?></h3>
      <p><?= e($card['description'] ?? '') ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- AUDIENCES -->
<?php $audiences = loadContent('audiences', $lang); ?>
<section class="audiences-section">
  <div class="reveal">
    <div class="section-eyebrow"><?= e($audiences['sectionEyebrow'] ?? 'Zielgruppen') ?></div>
    <h2 class="section-title"><?= nl2br(e($audiences['sectionTitle'] ?? '')) ?></h2>
  </div>
  <div class="audience-grid reveal">
    <?php foreach (($audiences['audiences'] ?? []) as $aud): ?>
    <div class="audience-card <?= e($aud['cssClass'] ?? '') ?>">
      <div class="audience-label"><?= e($aud['label'] ?? '') ?></div>
      <h3><?= e($aud['title'] ?? '') ?></h3>
      <p><?= e($aud['description'] ?? '') ?></p>
      <ul class="benefits">
        <?php foreach (($aud['benefits'] ?? []) as $benefit): ?>
        <li><span class="check">✓</span> <?= e($benefit) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SPECS -->
<?php $specs = loadContent('specs', $lang); ?>
<section class="specs-section" id="technik" style="background:rgba(0,0,0,0.15);">
  <div class="reveal">
    <div class="section-eyebrow"><?= e($specs['sectionEyebrow'] ?? 'Technische Daten') ?></div>
    <h2 class="section-title"><?= nl2br(e($specs['sectionTitle'] ?? '')) ?></h2>
    <p class="section-sub"><?= e($specs['sectionSub'] ?? '') ?></p>
  </div>
  <div class="specs-grid reveal">
    <?php foreach (($specs['specs'] ?? []) as $spec): ?>
    <div class="spec-item"><div class="spec-value"><?= e($spec['value'] ?? '') ?></div><div class="spec-label"><?= e($spec['label'] ?? '') ?></div></div>
    <?php endforeach; ?>
  </div>
</section>

<!-- CHART -->
<?php $chart = loadContent('chart', $lang); ?>
<div class="chart-wrap-outer reveal">
  <div class="chart-section-inner">
    <div class="chart-header">
      <div>
        <div class="section-eyebrow"><?= e($chart['sectionEyebrow'] ?? 'Verlaufsdaten · Beispiel') ?></div>
        <div style="font-size:var(--fs-section-sub);font-weight:700;"><?= e($chart['headerTitle'] ?? 'Feuchte & Temperatur über Zeit') ?></div>
      </div>
      <div class="chart-legend">
        <?php foreach (($chart['legend'] ?? []) as $item): ?>
        <?php if (($item['type'] ?? '') === 'dot'): ?>
        <div class="legend-item"><div class="legend-dot" style="background:<?= e($item['color'] ?? '') ?>"></div><?= e($item['label'] ?? '') ?></div>
        <?php else: ?>
        <div class="legend-item"><div class="legend-dash" style="border-color:<?= e($item['color'] ?? '') ?>"></div><?= e($item['label'] ?? '') ?></div>
        <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="chart-bg">
      <svg viewBox="0 0 1060 160" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;display:block;">
        <line x1="60" y1="10"  x2="1040" y2="10"  stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="60" y1="23"  x2="1040" y2="23"  stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
        <line x1="60" y1="61"  x2="1040" y2="61"  stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="60" y1="87"  x2="1040" y2="87"  stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="60" y1="112" x2="1040" y2="112" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="60" y1="138" x2="1040" y2="138" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
        <line x1="60" y1="112" x2="1040" y2="112" stroke="rgba(255,255,255,0.15)" stroke-width="1" stroke-dasharray="6,4"/>
        <text x="4" y="116" fill="rgba(255,255,255,0.3)" font-family="Space Mono,monospace" font-size="9">2.0</text>
        <text x="4" y="14"  fill="#8BA5AB" font-family="Space Mono,monospace" font-size="9">6.0</text>
        <text x="4" y="27"  fill="#8BA5AB" font-family="Space Mono,monospace" font-size="9">5.5</text>
        <text x="4" y="65"  fill="#8BA5AB" font-family="Space Mono,monospace" font-size="9">4.0</text>
        <text x="4" y="91"  fill="#8BA5AB" font-family="Space Mono,monospace" font-size="9">3.0</text>
        <text x="4" y="142" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="9">1.0</text>
        <polyline points="60,34 120,35 200,39 290,44 380,52 460,61 540,71 610,82 680,95 750,104 820,109 870,112 920,112 1020,112" stroke="#FF6B1A" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        <polyline points="60,30 120,33 200,37 290,43 380,50 460,59 540,70 610,80 680,93 750,103 820,109 880,112 940,112 1020,112" stroke="#1A7A6E" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="8,3"/>
        <polyline points="60,34 120,35 200,39 290,44 370,52 440,62 510,75 570,89 620,102 660,108 690,112 1020,112" stroke="#4A9EDB" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="6,3" opacity="0.85"/>
        <polyline points="60,86 90,82 120,79 150,80 180,77 210,75 240,79 270,77 300,73 330,75 360,71 390,73 420,70 450,71 480,68 510,70 540,66 570,68 600,63 630,66 660,68 690,70 720,66 750,68 780,70 810,66 840,68 870,70 900,71 930,68 960,70 990,71 1020,70" stroke="#FFD166" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
        <defs>
          <linearGradient id="g1" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#FF6B1A" stop-opacity="0.15"/>
            <stop offset="100%" stop-color="#FF6B1A" stop-opacity="0"/>
          </linearGradient>
        </defs>
        <polygon points="60,34 120,35 200,39 290,44 380,52 460,61 540,71 610,82 680,95 750,104 820,109 870,112 920,112 1020,112 1020,150 60,150" fill="url(#g1)"/>
        <line x1="60" y1="20" x2="60" y2="34" stroke="#4A9EDB" stroke-width="1" stroke-dasharray="3,2" opacity="0.6"/>
        <rect x="56" y="4" width="170" height="16" rx="4" fill="rgba(74,158,219,0.16)" stroke="#4A9EDB" stroke-width="1"/>
        <text x="64" y="16" fill="#4A9EDB" font-family="Space Mono,monospace" font-size="8.5">+ Trocknungsbeschleuniger</text>
        <circle cx="690" cy="112" r="4" fill="#4A9EDB"/>
        <line x1="690" y1="20" x2="690" y2="108" stroke="#4A9EDB" stroke-width="1" stroke-dasharray="3,2" opacity="0.5"/>
        <rect x="634" y="4" width="114" height="16" rx="4" fill="rgba(74,158,219,0.16)" stroke="#4A9EDB" stroke-width="1"/>
        <text x="642" y="16" fill="#4A9EDB" font-family="Space Mono,monospace" font-size="8.5">✓ Tag 22  (−7 Tage)</text>
        <circle cx="870" cy="112" r="5" fill="#1A7A6E"/>
        <line x1="870" y1="20" x2="870" y2="107" stroke="#1A7A6E" stroke-width="1" stroke-dasharray="3,2" opacity="0.5"/>
        <rect x="806" y="4" width="132" height="16" rx="4" fill="rgba(26,122,110,0.22)" stroke="#1A7A6E" stroke-width="1"/>
        <text x="814" y="16" fill="#1A7A6E" font-family="Space Mono,monospace" font-size="8.5">✓ Freigabe erreicht</text>
        <text x="56"  y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 0</text>
        <text x="196" y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 5</text>
        <text x="336" y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 10</text>
        <text x="476" y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 15</text>
        <text x="616" y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 20</text>
        <text x="756" y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 25</text>
        <text x="896" y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 30</text>
        <text x="1000" y="156" fill="#8BA5AB" font-family="Space Mono,monospace" font-size="8">Tag 34</text>
      </svg>
    </div>
  </div>
</div>

<!-- CTA -->
<section class="cta-section">
  <div class="section-eyebrow reveal">Wie geht es weiter?</div>
  <h2 class="section-title reveal">Bereit für den nächsten Schritt?</h2>
  <p class="section-sub reveal">Ob Sie sich erst informieren oder direkt starten möchten – wir sind für Sie da.</p>
  <div class="cta-cards reveal">
    <div class="cta-card cta-card--info" onclick="window.location.href='/news'">
      <div class="cta-card-eyebrow">Informieren</div>
      <h3>Erst mal reinlesen?</h3>
      <p>Erfahrungsberichte, Kampagnen und Neuigkeiten rund um die digitale Estrich-Feuchtemessung.</p>
      <a href="/news" class="cta-card-link">News lesen <span>&rarr;</span></a>
    </div>
    <div class="cta-card cta-card--action" onclick="window.location.href='pages/kontakt.php'">
      <div class="cta-card-eyebrow">Starten</div>
      <h3>Direkt loslegen?</h3>
      <p>Sprechen Sie mit uns über Pilotprojekte, Systemintegration oder eine Demo vor Ort.</p>
      <a href="pages/kontakt.php" class="cta-card-link">Kontakt aufnehmen <span>&rarr;</span></a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-left">
    <div class="footer-wordmark">ESTRICH DIGITAL</div>
    <div class="footer-meta"><?= e($ui['footer']['copyright'] ?? 'Estrich Digital GmbH · Erlenbach · © 2025 · Alle Rechte vorbehalten') ?></div>
  </div>
  <div class="footer-right">
    <a href="pages/impressum.php"><?= e($ui['footer']['impressum'] ?? 'Impressum') ?></a>
    <a href="pages/datenschutz.php"><?= e($ui['footer']['datenschutz'] ?? 'Datenschutz') ?></a>
    <a href="pages/kontakt.php"><?= e($ui['footer']['kontakt'] ?? 'Kontakt') ?></a>
  </div>
</footer>

<!-- Lightbox -->
<div class="lightbox-overlay" onclick="closeLightbox()">
  <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
  <img src="" alt="Vergrößerte Ansicht">
</div>

<script src="assets/js/main.js"></script>
<script>
function openLightbox(src) {
  const overlay = document.querySelector('.lightbox-overlay');
  overlay.querySelector('img').src = src;
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  const overlay = document.querySelector('.lightbox-overlay');
  overlay.classList.remove('active');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeLightbox();
});

// Alle Schritte-Bilder klickbar machen – beim Klick Original aus data-full laden,
// Thumbnail bleibt als src für die Ansicht auf der Startseite.
document.querySelectorAll('.how-step-images img').forEach(function(img) {
  img.addEventListener('click', function(e) {
    e.stopPropagation();
    openLightbox(this.dataset.full || this.src);
  });
});
</script>
</body>
</html>
