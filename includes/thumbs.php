<?php
/**
 * Thumbnail-Generator für /assets/images/
 * Nutzt die PHP-GD-Extension (im Dockerfile aktiviert).
 *
 * Skaliert Bilder auf max. MAX_DIM Pixel (längste Kante) und speichert sie
 * als JPEG mit QUALITY-Prozent in /assets/images/thumbs/<name>-thumb.jpg.
 *
 * Regeneriert nur, wenn der Thumbnail fehlt oder älter ist als das Original.
 */

define('THUMB_MAX_DIM', 800);
define('THUMB_QUALITY', 82);
define('IMAGES_DIR', __DIR__ . '/../assets/images');
define('THUMBS_DIR', __DIR__ . '/../assets/images/thumbs');

/**
 * Alle passenden Bilder in IMAGES_DIR durchgehen und Thumbs erzeugen.
 *
 * @return array {
 *   created: string[]  – neu erzeugte Thumbs
 *   skipped: string[]  – aktuell, nichts zu tun
 *   errors:  string[]  – Fehlermeldungen
 *   stats: array       – Zusammenfassung
 * }
 */
function regenerateThumbnails(): array {
    $result = ['created' => [], 'skipped' => [], 'errors' => [], 'stats' => []];

    if (!is_dir(IMAGES_DIR)) {
        $result['errors'][] = 'Bildordner fehlt: ' . IMAGES_DIR;
        return $result;
    }
    if (!is_dir(THUMBS_DIR) && !mkdir(THUMBS_DIR, 0775, true) && !is_dir(THUMBS_DIR)) {
        $result['errors'][] = 'Konnte Thumbs-Ordner nicht anlegen: ' . THUMBS_DIR;
        return $result;
    }

    $bytesBefore = 0;
    $bytesAfter  = 0;

    // Nur Bilder direkt in assets/images/, nicht in thumbs/ oder Unterordnern.
    $files = glob(IMAGES_DIR . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];

    foreach ($files as $src) {
        $name  = pathinfo($src, PATHINFO_FILENAME);
        $thumb = THUMBS_DIR . '/' . $name . '-thumb.jpg';
        $bytesBefore += filesize($src);

        // Up-to-date? Dann überspringen.
        if (file_exists($thumb) && filemtime($thumb) >= filemtime($src)) {
            $bytesAfter += filesize($thumb);
            $result['skipped'][] = basename($src);
            continue;
        }

        try {
            createThumb($src, $thumb, THUMB_MAX_DIM, THUMB_QUALITY);
            $bytesAfter += filesize($thumb);
            $result['created'][] = basename($src);
        } catch (Throwable $e) {
            $result['errors'][] = basename($src) . ': ' . $e->getMessage();
        }
    }

    $result['stats'] = [
        'total'        => count($files),
        'created'      => count($result['created']),
        'skipped'      => count($result['skipped']),
        'errors'       => count($result['errors']),
        'bytes_before' => $bytesBefore,
        'bytes_after'  => $bytesAfter,
        'saved_pct'    => $bytesBefore > 0
            ? round((1 - $bytesAfter / $bytesBefore) * 100, 1)
            : 0,
    ];

    return $result;
}

/**
 * Einen einzelnen Thumbnail erzeugen (skalieren, als JPEG speichern).
 * Wirft Exception bei unbekanntem Format oder GD-Fehlern.
 */
function createThumb(string $src, string $dst, int $maxDim, int $quality): void {
    if (!function_exists('imagecreatefromjpeg')) {
        throw new RuntimeException('PHP-GD-Extension nicht verfügbar');
    }
    $info = @getimagesize($src);
    if (!$info) {
        throw new RuntimeException('Kann Bild nicht lesen');
    }
    [$w, $h] = $info;
    if ($w <= 0 || $h <= 0) {
        throw new RuntimeException('Ungültige Bildabmessungen');
    }

    // Bild laden (je nach Typ)
    $img = null;
    switch ($info[2]) {
        case IMAGETYPE_JPEG: $img = @imagecreatefromjpeg($src); break;
        case IMAGETYPE_PNG:  $img = @imagecreatefrompng($src);  break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $img = @imagecreatefromwebp($src);
            }
            break;
        default:
            throw new RuntimeException('Format nicht unterstützt (Typ ' . $info[2] . ')');
    }
    if (!$img) {
        throw new RuntimeException('Konnte Bild nicht dekodieren');
    }

    // Zielgröße (nur verkleinern, nie vergrößern)
    $ratio = min($maxDim / $w, $maxDim / $h, 1);
    $newW  = max(1, (int) round($w * $ratio));
    $newH  = max(1, (int) round($h * $ratio));

    $thumb = imagecreatetruecolor($newW, $newH);

    // PNG mit Alpha: vorher auf Weiß flachlegen, damit JPEG sauber wird.
    if ($info[2] === IMAGETYPE_PNG) {
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb, 0, 0, $newW, $newH, $white);
    }

    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);

    if (!imagejpeg($thumb, $dst, $quality)) {
        imagedestroy($img);
        imagedestroy($thumb);
        throw new RuntimeException('Schreiben fehlgeschlagen');
    }

    imagedestroy($img);
    imagedestroy($thumb);
}

/**
 * Einen Thumbnail gezielt löschen (z.B. wenn Original entfernt wurde).
 */
function deleteThumb(string $originalBasename): bool {
    $name = pathinfo($originalBasename, PATHINFO_FILENAME);
    $path = THUMBS_DIR . '/' . $name . '-thumb.jpg';
    return file_exists($path) ? @unlink($path) : true;
}

/**
 * Übersicht der Bilder mit Thumb-Status (für die Admin-UI).
 */
function listImagesWithThumbs(): array {
    if (!is_dir(IMAGES_DIR)) return [];
    $files = glob(IMAGES_DIR . '/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    $out = [];
    foreach ($files as $src) {
        $base  = basename($src);
        $name  = pathinfo($src, PATHINFO_FILENAME);
        $thumb = THUMBS_DIR . '/' . $name . '-thumb.jpg';
        $has   = file_exists($thumb);
        $stale = $has && filemtime($thumb) < filemtime($src);

        $out[] = [
            'name'         => $base,
            'orig_size'    => filesize($src),
            'thumb_exists' => $has,
            'thumb_stale'  => $stale,
            'thumb_size'   => $has ? filesize($thumb) : 0,
        ];
    }
    return $out;
}
