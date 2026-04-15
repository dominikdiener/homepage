<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Nur POST erlaubt']);
    exit;
}

$vorname   = strip_tags(trim($_POST['vorname'] ?? ''));
$nachname  = strip_tags(trim($_POST['nachname'] ?? ''));
$firma     = strip_tags(trim($_POST['firma'] ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$rolle     = strip_tags(trim($_POST['rolle'] ?? ''));
$betreff   = strip_tags(trim($_POST['betreff'] ?? ''));
$nachricht = strip_tags(trim($_POST['nachricht'] ?? ''));

if (!$vorname || !$nachname || !$email || !$nachricht) {
    http_response_code(400);
    echo json_encode(['error' => 'Pflichtfelder fehlen']);
    exit;
}

$rollen = [
    'gu' => 'Generalunternehmer / Bauträger',
    'hersteller' => 'Estrich-Hersteller / Baustoffhandel',
    'estrichleger' => 'Estrichleger / Handwerker',
    'planer' => 'Architekt / Planer',
    'sonstiges' => 'Sonstiges'
];
$rolleText = $rollen[$rolle] ?? $rolle;

$an = 'Dominik.diener@estrich-digital.de';
$mailBetreff = 'Kontaktanfrage: ' . ($betreff ?: 'Estrich Digital Homepage');

$body  = "Neue Kontaktanfrage über die Homepage\n";
$body .= "=====================================\n\n";
$body .= "Name:         $vorname $nachname\n";
if ($firma)     $body .= "Unternehmen:  $firma\n";
$body .= "E-Mail:       $email\n";
if ($rolleText) $body .= "Rolle:        $rolleText\n";
if ($betreff)   $body .= "Betreff:      $betreff\n";
$body .= "\nNachricht:\n$nachricht\n";

$headers  = "From: Dominik.diener@estrich-digital.de\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$ok = mail($an, $mailBetreff, $body, $headers);

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Mail konnte nicht gesendet werden']);
}
