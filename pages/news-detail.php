<?php
/**
 * News-Detail-Seite – einzelner Artikel unter /news/<slug>
 * Wird per Rewrite von /news/<slug> → pages/news-detail.php?slug=<slug> aufgerufen.
 *
 * Vorteile:
 * - Saubere, SEO-freundliche URL
 * - Eigene <title>- und meta-description-Tags pro Artikel
 * - Open-Graph-Tags für Social-Media-Vorschau
 */
require_once __DIR__ . '/../includes/content.php';
require_once __DIR__ . '/../includes/news.php';
require_once __DIR__ . '/../includes/lib/Parsedown.php';

$lang    = getCurrentLang();
$slug    = trim($_GET['slug'] ?? '');
$article = $slug ? findArticleBySlug($slug) : null;

// Artikel nicht gefunden → 404
if (!$article) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="<?= htmlspecialchars($lang) ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Artikel nicht gefunden – Estrich Digital</title>
        <link rel="stylesheet" href="/assets/css/main.css">
    </head>
    <body>
        <nav>
            <a href="/index.php" class="nav-logo">
                <img src="/assets/images/logo.png" alt="Estrich Digital Logo"/>
            </a>
            <ul class="nav-links">
                <li><a href="/news">News</a></li>
                <li><a href="/pages/kontakt.php" class="nav-cta">Kontakt aufnehmen</a></li>
            </ul>
        </nav>
        <div class="page-hero">
            <div class="page-hero-content">
                <div class="section-eyebrow">404</div>
                <h1 class="section-title">Artikel nicht gefunden</h1>
                <p class="section-sub">Dieser Artikel existiert nicht (mehr). <a href="/news" style="color:var(--orange);">Zurück zur News-Übersicht</a></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Wenn der aufgerufene Slug vom aktuellen (aus Titel/Datum berechneten) abweicht
// → 301-Weiterleitung auf die kanonische URL (Titel wurde z.B. umbenannt)
$canonicalSlug = $article['slug'];
if ($slug !== $canonicalSlug) {
    header('Location: /news/' . $canonicalSlug, true, 301);
    exit;
}

$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$contentHtml = $article['content'] !== '' ? $parsedown->text($article['content']) : '';

// Anzeige-Daten
$ts            = strtotime(str_replace('.', '-', $article['date']));
$dateFormatted = $ts ? date('d.m.Y', $ts) : htmlspecialchars($article['date']);
$isoDate       = $ts ? date('Y-m-d', $ts) : '';

$categoryLabels = [
    'news'              => 'News',
    'kampagne'          => 'Kampagne',
    'erfahrungsbericht' => 'Erfahrungsbericht',
    'messe'             => 'Messe',
];
$catLabel = $categoryLabels[$article['category']] ?? ucfirst($article['category']);

// Meta-Description: Plain-Text aus dem Langtext, gekürzt
$metaDesc = trim($article['subtitle']);
if ($metaDesc === '' && $article['content'] !== '') {
    $plain = strip_tags($contentHtml);
    $plain = preg_replace('/\s+/', ' ', $plain);
    $metaDesc = mb_substr(trim($plain), 0, 160);
}
if ($metaDesc === '') {
    $metaDesc = 'News-Beitrag von Estrich Digital.';
}

// Kanonische URL (Host aus Server, Pfad fix)
$scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host        = $_SERVER['HTTP_HOST'] ?? 'estrich-digital.de';
$canonical   = $scheme . '://' . $host . '/news/' . $canonicalSlug;

// Vorschaubild für OpenGraph
$ogImage = '';
if ($article['previewDesktop']) {
    $ogImage = $scheme . '://' . $host . '/data/news/' . $article['nummer'] . '/' . rawurlencode($article['previewDesktop']);
}

$hasPreviewDesktop = !empty($article['previewDesktop']);
$hasPreviewMobile  = !empty($article['previewMobile']);
$baseUrl           = '/data/news/' . $article['nummer'] . '/';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($article['title']) ?> – Estrich Digital</title>
<meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

<!-- Open Graph / Social Media -->
<meta property="og:type" content="article">
<meta property="og:title" content="<?= htmlspecialchars($article['title']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
<meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
<meta property="og:site_name" content="Estrich Digital">
<?php if ($ogImage): ?>
<meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
<?php endif; ?>
<?php if ($isoDate): ?>
<meta property="article:published_time" content="<?= htmlspecialchars($isoDate) ?>">
<?php endif; ?>
<?php if ($article['author']): ?>
<meta name="author" content="<?= htmlspecialchars($article['author']) ?>">
<meta property="article:author" content="<?= htmlspecialchars($article['author']) ?>">
<?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="<?= $ogImage ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title" content="<?= htmlspecialchars($article['title']) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($metaDesc) ?>">
<?php if ($ogImage): ?>
<meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
<?php endif; ?>

<!-- Strukturierte Daten für Google (Schema.org Article) -->
<script type="application/ld+json">
<?= json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'NewsArticle',
    'headline'        => $article['title'],
    'description'     => $metaDesc,
    'datePublished'   => $isoDate,
    'author'          => $article['author'] ? ['@type' => 'Person', 'name' => $article['author']] : null,
    'publisher'       => [
        '@type' => 'Organization',
        'name'  => 'Estrich Digital',
        'logo'  => ['@type' => 'ImageObject', 'url' => $scheme . '://' . $host . '/assets/images/logo.png'],
    ],
    'mainEntityOfPage' => $canonical,
    'image'            => $ogImage ?: null,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="stylesheet" href="/assets/css/main.css">

<style>
  .news-detail {
    max-width: 780px;
    margin: 0 auto;
    padding: 40px 24px 80px;
  }

  .news-detail-back {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    color: var(--grey);
    text-decoration: none;
    font-family: var(--font-mono);
    font-size: .8rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 2rem;
    transition: color .2s;
  }
  .news-detail-back:hover { color: var(--orange); }

  .news-detail-meta {
    display: flex;
    flex-wrap: wrap;
    gap: .8rem;
    align-items: center;
    margin-bottom: 1.2rem;
    font-family: var(--font-mono);
    font-size: .78rem;
    color: var(--grey);
    letter-spacing: .5px;
    text-transform: uppercase;
  }

  .news-detail-badge {
    display: inline-block;
    padding: .25rem .7rem;
    border-radius: 100px;
    background: var(--orange-dim);
    color: var(--orange);
    font-weight: 700;
    font-size: .7rem;
    letter-spacing: 1.5px;
  }

  .news-detail-title {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 700;
    line-height: 1.2;
    color: var(--light);
    margin-bottom: .8rem;
  }

  .news-detail-subtitle {
    font-size: 1.15rem;
    color: var(--grey);
    line-height: 1.55;
    margin-bottom: 2rem;
    font-weight: 300;
  }

  .news-detail-preview {
    width: 100%;
    border-radius: 14px;
    margin: 2rem 0;
    border: 1px solid rgba(255,255,255,.08);
  }
  .news-detail-preview.desktop { display: block; }
  .news-detail-preview.mobile  { display: none; }
  @media (max-width: 768px) {
    .news-detail-preview.desktop { display: none; }
    .news-detail-preview.mobile  { display: block; }
  }

  .news-detail-content {
    color: var(--light);
    font-size: 1.05rem;
    line-height: 1.75;
  }
  .news-detail-content > *:first-child { margin-top: 0; }
  .news-detail-content p { margin: 0 0 1.1rem; }
  .news-detail-content h1,
  .news-detail-content h2,
  .news-detail-content h3 {
    color: var(--light);
    margin: 2rem 0 .8rem;
    font-weight: 700;
    line-height: 1.3;
  }
  .news-detail-content h1 { font-size: 1.8rem; }
  .news-detail-content h2 { font-size: 1.45rem; }
  .news-detail-content h3 { font-size: 1.2rem; }
  .news-detail-content strong { color: var(--light); font-weight: 700; }
  .news-detail-content em { font-style: italic; }
  .news-detail-content ul,
  .news-detail-content ol {
    margin: .8rem 0 1.2rem;
    padding-left: 1.5rem;
  }
  .news-detail-content li { margin-bottom: .4rem; color: var(--light); }
  .news-detail-content a {
    color: var(--orange);
    text-decoration: none;
    border-bottom: 1px solid rgba(255,107,26,.4);
    transition: border-color .2s;
  }
  .news-detail-content a:hover { border-color: var(--orange); }
  .news-detail-content blockquote {
    margin: 1.2rem 0;
    padding: .5rem 0 .5rem 1.3rem;
    border-left: 3px solid var(--orange);
    color: var(--grey);
    font-style: italic;
  }
  .news-detail-content hr {
    border: none;
    border-top: 1px solid rgba(255,255,255,.12);
    margin: 2rem 0;
  }
  .news-detail-content code {
    background: rgba(255,255,255,.06);
    padding: 2px 8px;
    border-radius: 4px;
    font-family: var(--font-mono);
    font-size: .9em;
  }
  .news-detail-content pre {
    background: rgba(0,0,0,.3);
    padding: 1rem;
    border-radius: 10px;
    overflow-x: auto;
    margin: 1.2rem 0;
  }

  .news-detail-pdfs {
    margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,.08);
  }
  .news-detail-pdfs h3 {
    font-family: var(--font-mono);
    font-size: .8rem;
    letter-spacing: 1.5px;
    color: var(--grey);
    text-transform: uppercase;
    margin-bottom: 1rem;
  }
  .news-detail-pdf-link {
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    padding: .7rem 1rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 8px;
    color: var(--light);
    text-decoration: none;
    font-size: .9rem;
    margin: 0 .5rem .5rem 0;
    transition: border-color .2s, background .2s;
  }
  .news-detail-pdf-link:hover {
    border-color: var(--orange);
    background: rgba(255,107,26,.05);
  }
  .news-detail-pdf-link svg {
    width: 18px; height: 18px;
    color: var(--orange);
    flex-shrink: 0;
  }
  .news-detail-pdf-embed {
    width: 100%;
    height: 720px;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 12px;
    margin-top: 1rem;
    background: #fff;
  }
  @media (max-width: 768px) {
    .news-detail-pdf-embed { display: none; }
  }

  .news-detail-share {
    margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,.08);
    font-size: .9rem;
    color: var(--grey);
  }
</style>
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

<article class="news-detail">
  <a href="/news" class="news-detail-back">← Zurück zur Übersicht</a>

  <div class="news-detail-meta">
    <?php if ($article['category']): ?>
      <span class="news-detail-badge"><?= htmlspecialchars($catLabel) ?></span>
    <?php endif; ?>
    <?php if ($isoDate): ?>
      <time datetime="<?= $isoDate ?>"><?= $dateFormatted ?></time>
    <?php endif; ?>
    <?php if ($article['author']): ?>
      <span>von <?= htmlspecialchars($article['author']) ?></span>
    <?php endif; ?>
  </div>

  <h1 class="news-detail-title"><?= htmlspecialchars($article['title']) ?></h1>

  <?php if ($article['subtitle']): ?>
    <p class="news-detail-subtitle"><?= htmlspecialchars($article['subtitle']) ?></p>
  <?php endif; ?>

  <?php if ($hasPreviewDesktop): ?>
    <img class="news-detail-preview desktop" src="<?= $baseUrl . rawurlencode($article['previewDesktop']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
  <?php endif; ?>
  <?php if ($hasPreviewMobile): ?>
    <img class="news-detail-preview mobile" src="<?= $baseUrl . rawurlencode($article['previewMobile']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
  <?php endif; ?>

  <?php if ($contentHtml): ?>
    <div class="news-detail-content"><?= $contentHtml ?></div>
  <?php endif; ?>

  <?php if (!empty($article['files'])): ?>
    <div class="news-detail-pdfs">
      <h3>Dokumente</h3>
      <?php foreach ($article['files'] as $file):
          $pdfUrl = $baseUrl . rawurlencode($file);
      ?>
        <a class="news-detail-pdf-link" href="<?= $pdfUrl ?>" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
          <?= htmlspecialchars($file) ?>
        </a>
      <?php endforeach; ?>
      <?php if (!$hasPreviewDesktop && !empty($article['files'])): ?>
        <iframe class="news-detail-pdf-embed" data-src="<?= $baseUrl . rawurlencode($article['files'][0]) ?>"></iframe>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</article>

<footer>
  <div class="footer-left">
    <div class="footer-wordmark">ESTRICH DIGITAL</div>
    <div class="footer-meta">Estrich Digital · Bad Laasphe · <?= date('Y') ?> · Alle Rechte vorbehalten</div>
  </div>
  <div class="footer-right">
    <a href="/pages/impressum.php">Impressum</a>
    <a href="/pages/datenschutz.php">Datenschutz</a>
    <a href="/pages/kontakt.php">Kontakt</a>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
<script>
  // PDF-Embeds nur auf Desktop nachladen (Performance)
  if (window.innerWidth > 768) {
    document.querySelectorAll('iframe[data-src]').forEach(function (f) {
      f.src = f.dataset.src;
    });
  }
</script>
</body>
</html>
