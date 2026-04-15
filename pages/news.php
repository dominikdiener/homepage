<?php
/**
 * Öffentliche News-Seite – liest aus data/news.csv
 * CSV-Format: nummer|datum|ersteller|kategorie|ueberschrift|unterueberschrift|langtext
 */
$csvFile = __DIR__ . '/../data/news.csv';
$articles = [];
if (file_exists($csvFile)) {
    $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    array_shift($lines); // Header überspringen
    foreach ($lines as $line) {
        $cols = explode('|', $line);
        if (count($cols) < 7) continue;
        // Dateien aus dem nummerierten Ordner laden
        $nr = trim($cols[0]);
        $files = [];
        $filesJson = __DIR__ . "/../data/news/$nr/files.json";
        if (file_exists($filesJson)) {
            $files = json_decode(file_get_contents($filesJson), true) ?: [];
        }
        $articles[] = [
            'nummer'    => $nr,
            'date'      => trim($cols[1]),
            'author'    => trim($cols[2]),
            'category'  => strtolower(trim($cols[3])),
            'title'     => trim($cols[4]),
            'subtitle'  => trim($cols[5]),
            'content'   => trim($cols[6]),
            'files'     => $files,
        ];
    }
}
// Nach Datum sortieren (neueste zuerst)
usort($articles, function($a, $b) {
    $da = strtotime(str_replace('.', '-', $a['date'])) ?: 0;
    $db = strtotime(str_replace('.', '-', $b['date'])) ?: 0;
    return $db - $da;
});

$categoryLabels = [
    'news'              => 'News',
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
    display: flex;
    flex-direction: column;
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
    <li><a href="kontakt.php" class="nav-cta">Kontakt aufnehmen</a></li>
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
        <?php
        $usedCats = array_unique(array_column($articles, 'category'));
        foreach ($usedCats as $c):
        ?>
        <button class="news-filter-btn" data-category="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($categoryLabels[$c] ?? ucfirst($c)) ?></button>
        <?php endforeach; ?>
    </div>

    <div class="news-grid">
      <?php foreach ($articles as $a):
          $ts = strtotime(str_replace('.', '-', $a['date']));
          $dateFormatted = $ts ? date('d.m.Y', $ts) : htmlspecialchars($a['date']);
          $cat = $a['category'];
          $nr = $a['nummer'];
      ?>
        <div class="news-card" id="<?= $nr ?>" data-category="<?= htmlspecialchars($cat) ?>">
          <div class="news-card-body">
            <div class="news-card-meta">
              <span class="news-badge news-badge-<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($categoryLabels[$cat] ?? $cat) ?></span>
              <span class="news-card-date"><?= $dateFormatted ?></span>
              <span class="news-card-author">von <?= htmlspecialchars($a['author']) ?></span>
              <span class="news-card-nr">#<?= htmlspecialchars($nr) ?></span>
            </div>
            <h3 class="news-card-title"><?= htmlspecialchars($a['title']) ?></h3>
            <?php if (!empty($a['subtitle'])): ?>
              <p class="news-card-subtitle"><?= htmlspecialchars($a['subtitle']) ?></p>
            <?php endif; ?>
            <p class="news-card-excerpt"><?= htmlspecialchars($a['content']) ?></p>
            <?php if (!empty($a['files'])): ?>
              <div class="news-card-files">
                <?php foreach ($a['files'] as $file): ?>
                  <a href="../data/news/<?= $nr ?>/<?= htmlspecialchars($file) ?>" target="_blank" class="news-file-link">
                    📄 <?= htmlspecialchars($file) ?>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
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
    <a href="impressum.php">Impressum</a>
    <a href="datenschutz.php">Datenschutz</a>
    <a href="kontakt.php">Kontakt</a>
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
