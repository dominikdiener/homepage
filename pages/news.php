<?php
/**
 * Öffentliche News-Seite – liest data/news/<nr>/article.json
 * Langtext wird als Markdown gespeichert und serverseitig mit Parsedown gerendert.
 */
require_once __DIR__ . '/../includes/content.php';
require_once __DIR__ . '/../includes/news.php';
require_once __DIR__ . '/../includes/lib/Parsedown.php';
$lang = getCurrentLang();

$parsedown = new Parsedown();
$parsedown->setSafeMode(true); // HTML im Markdown wird escaped → XSS-Schutz

// Zentrale News-Quelle inkl. SEO-Slug + Sortierung (siehe includes/news.php)
$articles = loadPublicNews();

// Langtext serverseitig zu HTML rendern (Parsedown SafeMode)
foreach ($articles as &$a) {
    $a['contentHtml'] = $a['content'] !== '' ? $parsedown->text($a['content']) : '';
}
unset($a);

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
<link rel="stylesheet" href="/assets/css/main.css">
<style>
/* ── News-spezifisches Styling ── */
.news-section {
    padding: 2.5rem 64px 80px;
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
    font-family: var(--font-mono);
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
    width: 100%;
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
    font-family: var(--font-mono);
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.news-badge-kampagne          { background: rgba(255,107,26,.15); color: var(--orange); }
.news-badge-erfahrungsbericht { background: rgba(26,122,110,.15); color: var(--teal); }
.news-badge-messe             { background: rgba(255,209,102,.15); color: var(--accent); }

.news-card-date {
    font-family: var(--font-mono);
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
.news-card-title a {
    color: inherit;
    text-decoration: none;
    transition: color .2s;
}
.news-card-title a:hover { color: var(--orange); }

.news-card-permalink {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    margin-top: 1rem;
    font-family: var(--font-mono);
    font-size: .78rem;
    letter-spacing: .5px;
    text-transform: uppercase;
    color: var(--orange);
    text-decoration: none;
    transition: gap .2s, opacity .2s;
}
.news-card-permalink:hover { gap: .75rem; opacity: .85; }
.news-card-permalink svg { width: 14px; height: 14px; }

.news-card-excerpt {
    font-size: .88rem;
    color: var(--grey);
    line-height: 1.6;
    margin-bottom: 1rem;
}
/* Markdown-Elemente im Excerpt */
.news-card-excerpt > *:first-child { margin-top: 0; }
.news-card-excerpt > *:last-child  { margin-bottom: 0; }
.news-card-excerpt p { margin: 0 0 .7rem; }
.news-card-excerpt h1,
.news-card-excerpt h2,
.news-card-excerpt h3 {
    color: var(--light);
    margin: 1rem 0 .5rem;
    font-weight: 600;
    line-height: 1.3;
}
.news-card-excerpt h1 { font-size: 1.15rem; }
.news-card-excerpt h2 { font-size: 1.05rem; }
.news-card-excerpt h3 { font-size: .95rem; }
.news-card-excerpt strong { color: var(--light); font-weight: 600; }
.news-card-excerpt em { font-style: italic; }
.news-card-excerpt ul,
.news-card-excerpt ol {
    margin: .4rem 0 .8rem;
    padding-left: 1.3rem;
}
.news-card-excerpt li { margin-bottom: .2rem; }
.news-card-excerpt a {
    color: var(--orange);
    text-decoration: none;
    border-bottom: 1px solid rgba(255,107,26,.35);
    transition: border-color .2s;
}
.news-card-excerpt a:hover { border-color: var(--orange); }
.news-card-excerpt blockquote {
    margin: .6rem 0;
    padding: .3rem 0 .3rem 1rem;
    border-left: 3px solid rgba(255,107,26,.4);
    color: var(--grey);
    font-style: italic;
}
.news-card-excerpt hr {
    border: none;
    border-top: 1px solid rgba(255,255,255,.08);
    margin: 1rem 0;
}
.news-card-excerpt code {
    background: rgba(255,255,255,.06);
    padding: 1px 6px;
    border-radius: 4px;
    font-family: var(--font-mono);
    font-size: .85em;
}

.news-card-toggle {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-family: var(--font-mono);
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

.news-pdfs {
    margin-top: 1.2rem;
    display: flex;
    flex-direction: column;
    gap: .6rem;
}
.news-pdf-link {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    font-family: var(--font-mono);
    font-size: .8rem;
    color: var(--orange);
    text-decoration: none;
    padding: .5rem .8rem;
    border: 1px solid rgba(255,107,26,.2);
    border-radius: 8px;
    transition: background .2s;
}
.news-pdf-link:hover { background: rgba(255,107,26,.08); }
.news-pdf-link svg { width: 16px; height: 16px; flex-shrink: 0; }

.news-pdf-embed {
    width: 100%;
    height: 500px;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 8px;
}

.news-preview-img {
    width: 100%;
    border-radius: 8px;
    margin-top: .8rem;
    border: 1px solid rgba(255,255,255,.08);
}
.news-preview-desktop { display: block; }
.news-preview-mobile  { display: none; }

.news-card-subtitle {
    font-size: 1rem;
    font-weight: 500;
    color: var(--orange);
    margin-bottom: .6rem;
}
.news-card-author, .news-card-nr {
    font-family: var(--font-mono);
    font-size: .7rem;
    color: var(--grey);
}

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
    .news-section { padding: 1.5rem 20px 50px; }
    .news-pdf-embed { display: none; }
    .news-preview-desktop { display: none; }
    .news-preview-mobile  { display: block; }
}
</style>
<?= renderCustomCSS($lang) ?>
</head>
<body>
<?= renderPreviewBanner() ?>

<nav>
  <a href="/index.php" class="nav-logo">
    <img src="/assets/images/logo.png" alt="Estrich Digital Logo"/>
  </a>
  <button class="nav-hamburger" onclick="toggleNav(this)" aria-label="Menü">
    <span></span><span></span><span></span>
  </button>
  <ul class="nav-links">
    <li><a href="/index.php#how">So funktioniert's</a></li>
    <li><a href="/index.php#value">Ihr Nutzen</a></li>
    <li><a href="/index.php#technik">Technik</a></li>
    <li><a href="/news" class="active">News</a></li>
    <li><a href="/pages/kontakt.php" class="nav-cta">Kontakt aufnehmen</a></li>
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
            <h3 class="news-card-title">
              <a href="<?= htmlspecialchars(newsUrl($a)) ?>"><?= htmlspecialchars($a['title']) ?></a>
            </h3>
            <?php if (!empty($a['subtitle'])): ?>
              <p class="news-card-subtitle"><?= htmlspecialchars($a['subtitle']) ?></p>
            <?php endif; ?>
            <?php if (!empty($a['contentHtml'])): ?>
              <div class="news-card-excerpt"><?= $a['contentHtml'] /* Parsedown SafeMode aktiv → eingebettetes HTML ist escaped */ ?></div>
            <?php endif; ?>
            <a class="news-card-permalink" href="<?= htmlspecialchars(newsUrl($a)) ?>">
              Zum Beitrag
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </a>
            <?php
              $hasPreviewDesktop = !empty($a['previewDesktop']);
              $hasPreviewMobile  = !empty($a['previewMobile']);
              $baseUrl = '/data/news/' . $nr . '/';
            ?>
            <?php if ($hasPreviewDesktop): ?>
              <img class="news-preview-img news-preview-desktop" src="<?= $baseUrl . rawurlencode($a['previewDesktop']) ?>" alt="<?= htmlspecialchars($a['title']) ?>">
            <?php endif; ?>
            <?php if ($hasPreviewMobile): ?>
              <img class="news-preview-img news-preview-mobile" src="<?= $baseUrl . rawurlencode($a['previewMobile']) ?>" alt="<?= htmlspecialchars($a['title']) ?>">
            <?php endif; ?>
            <?php if (!empty($a['files'])): ?>
              <div class="news-pdfs">
                <?php foreach ($a['files'] as $file):
                    $pdfUrl = $baseUrl . rawurlencode($file);
                ?>
                  <a class="news-pdf-link" href="<?= $pdfUrl ?>" target="_blank">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <?= htmlspecialchars($file) ?>
                  </a>
                  <?php if (!$hasPreviewDesktop): ?>
                    <iframe class="news-pdf-embed" data-src="<?= $pdfUrl ?>"></iframe>
                  <?php endif; ?>
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
    <a href="/pages/impressum.php">Impressum</a>
    <a href="/pages/datenschutz.php">Datenschutz</a>
    <a href="/pages/kontakt.php">Kontakt</a>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
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

/* PDF-Vorschau nur auf Desktop laden */
if (window.innerWidth > 768) {
    document.querySelectorAll('.news-pdf-embed[data-src]').forEach(function(iframe) {
        iframe.src = iframe.dataset.src;
    });
}
</script>
</body>
</html>
