<?php
/**
 * Öffentliche News-Seite
 */
$jsonFile = __DIR__ . '/../data/articles.json';
$articles = [];
if (file_exists($jsonFile)) {
    $data = json_decode(file_get_contents($jsonFile), true);
    $articles = $data['articles'] ?? [];
}
usort($articles, fn($a, $b) => strcmp($b['date'], $a['date']));

$categoryLabels = [
    'kampagne'          => 'Kampagne',
    'erfahrungsbericht' => 'Erfahrungsbericht',
    'messe'             => 'Messe',
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="News – Estrich Digital. Kampagnen, Erfahrungsberichte und Messe-Ankündigungen rund um die digitale Estrich-Feuchtemessung.">
<title>News – Estrich Digital</title>
<link rel="stylesheet" href="../assets/css/main.css">
<style>
/* ── News-spezifisches Styling ── */
.news-section {
    padding: 0 64px 80px;
}

.news-filters {
    display: flex;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: 2.5rem;
}
.news-filter-btn {
    padding: .5rem 1.2rem;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 100px;
    background: transparent;
    color: var(--grey);
    font-family: 'Space Mono', monospace;
    font-size: .75rem;
    letter-spacing: .5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all .25s;
}
.news-filter-btn:hover,
.news-filter-btn.active {
    background: var(--orange);
    color: #fff;
    border-color: var(--orange);
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.8rem;
}

.news-card {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    overflow: hidden;
    transition: border-color .3s, transform .3s;
}
.news-card:hover {
    border-color: rgba(255,107,26,.3);
    transform: translateY(-2px);
}

.news-card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}

.news-card-body {
    padding: 1.5rem;
}

.news-card-meta {
    display: flex;
    align-items: center;
    gap: .8rem;
    margin-bottom: .8rem;
}

.news-badge {
    display: inline-block;
    padding: .2rem .6rem;
    border-radius: 4px;
    font-family: 'Space Mono', monospace;
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.news-badge-kampagne          { background: rgba(255,107,26,.15); color: var(--orange); }
.news-badge-erfahrungsbericht { background: rgba(26,122,110,.15); color: var(--teal); }
.news-badge-messe             { background: rgba(255,209,102,.15); color: var(--accent); }

.news-card-date {
    font-family: 'Space Mono', monospace;
    font-size: .7rem;
    color: var(--grey);
}

.news-card-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--light);
    margin-bottom: .6rem;
    line-height: 1.35;
}

.news-card-excerpt {
    font-size: .88rem;
    color: var(--grey);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.news-card-toggle {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-family: 'Space Mono', monospace;
    font-size: .75rem;
    color: var(--orange);
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    transition: opacity .2s;
}
.news-card-toggle:hover { opacity: .7; }
.news-card-toggle svg {
    width: 14px;
    height: 14px;
    transition: transform .3s;
}
.news-card.open .news-card-toggle svg {
    transform: rotate(180deg);
}

.news-card-full {
    max-height: 0;
    overflow: hidden;
    transition: max-height .4s ease;
    font-size: .9rem;
    color: var(--light);
    line-height: 1.7;
}
.news-card-full-inner {
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,.08);
    margin-top: 1rem;
}
.news-card-full-inner p   { margin-bottom: .8rem; }
.news-card-full-inner a   { color: var(--orange); }
.news-card-full-inner ul,
.news-card-full-inner ol  { padding-left: 1.2rem; margin-bottom: .8rem; }

.news-empty {
    text-align: center;
    padding: 4rem 1rem;
    color: var(--grey);
}
.news-empty p:first-child {
    font-size: 1.1rem;
    margin-bottom: .5rem;
}

@media (max-width: 768px) {
    .news-section { padding: 0 20px 50px; }
    .news-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<nav>
  <a href="../index.php" class="nav-logo">
    <img src="../assets/images/logo.png" alt="Estrich Digital Logo"/>
  </a>
  <ul class="nav-links">
    <li><a href="../index.php#how">So funktioniert's</a></li>
    <li><a href="../index.php#value">Ihr Nutzen</a></li>
    <li><a href="../index.php#technik">Technik</a></li>
    <li><a href="news.php" class="active">News</a></li>
    <li><a href="kontakt.html" class="nav-cta">Kontakt aufnehmen</a></li>
  </ul>
</nav>

<div class="page-hero">
  <div class="page-hero-content">
    <div class="section-eyebrow">News</div>
    <h1 class="section-title" style="font-size: clamp(32px,4vw,52px);">Aktuelles von Estrich Digital</h1>
    <p class="section-sub">Kampagnen, Erfahrungsberichte und Messe-Ankündigungen rund um die digitale Estrich-Feuchtemessung.</p>
  </div>
</div>

<div class="news-section">

  <?php if (!empty($articles)): ?>
    <!-- Kategorie-Filter -->
    <div class="news-filters">
        <button class="news-filter-btn active" data-category="">Alle</button>
        <button class="news-filter-btn" data-category="kampagne">Kampagne</button>
        <button class="news-filter-btn" data-category="erfahrungsbericht">Erfahrungsbericht</button>
        <button class="news-filter-btn" data-category="messe">Messe</button>
    </div>

    <div class="news-grid">
      <?php foreach ($articles as $a):
          $excerpt = mb_strimwidth(strip_tags($a['content']), 0, 180, '…');
          $dateFormatted = date('d.m.Y', strtotime($a['date']));
          $cat = $a['category'];
      ?>
        <div class="news-card" data-category="<?= htmlspecialchars($cat) ?>">
          <?php if (!empty($a['image'])): ?>
            <img class="news-card-img" src="../uploads/<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['title']) ?>">
          <?php endif; ?>
          <div class="news-card-body">
            <div class="news-card-meta">
              <span class="news-badge news-badge-<?= $cat ?>"><?= $categoryLabels[$cat] ?? $cat ?></span>
              <span class="news-card-date"><?= $dateFormatted ?></span>
            </div>
            <h3 class="news-card-title"><?= htmlspecialchars($a['title']) ?></h3>
            <p class="news-card-excerpt"><?= htmlspecialchars($excerpt) ?></p>
            <button class="news-card-toggle" onclick="toggleNews(this)">
              Weiterlesen
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="news-card-full">
              <div class="news-card-full-inner"><?= $a['content'] ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="news-empty">
      <p>Noch keine News vorhanden.</p>
      <p>Bald finden Sie hier Kampagnen, Erfahrungsberichte und Messe-Ankündigungen.</p>
    </div>
  <?php endif; ?>

</div>

<footer>
  <div class="footer-left">
    <div class="footer-wordmark">ESTRICH DIGITAL</div>
    <div class="footer-meta">Estrich Digital · Bad Laasphe · 2026 · Alle Rechte vorbehalten</div>
  </div>
  <div class="footer-right">
    <a href="impressum.html">Impressum</a>
    <a href="datenschutz.html">Datenschutz</a>
    <a href="kontakt.html">Kontakt</a>
  </div>
</footer>

<script src="../assets/js/main.js"></script>
<script>
/* Kategorie-Filter */
document.querySelectorAll('.news-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const cat = btn.dataset.category;
        document.querySelectorAll('.news-card').forEach(card => {
            card.style.display = (!cat || card.dataset.category === cat) ? '' : 'none';
        });
        document.querySelectorAll('.news-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

/* Akkordeon für Artikel */
function toggleNews(el) {
    const card = el.closest('.news-card');
    const full = card.querySelector('.news-card-full');
    const isOpen = card.classList.contains('open');

    if (isOpen) {
        full.style.maxHeight = '0';
        card.classList.remove('open');
        el.firstChild.textContent = 'Weiterlesen';
    } else {
        full.style.maxHeight = full.scrollHeight + 'px';
        card.classList.add('open');
        el.firstChild.textContent = 'Weniger';
    }
}
</script>
</body>
</html>
