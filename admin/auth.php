<?php
/**
 * Authentifizierung & Hilfsfunktionen
 */

require_once __DIR__ . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

/* ── Auth ── */

function isLoggedIn(): bool {
    return !empty($_SESSION['admin_logged_in']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function isLockedOut(): bool {
    return !empty($_SESSION['lockout_until']) && time() < $_SESSION['lockout_until'];
}

function registerFailedAttempt(): void {
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['lockout_until'] = time() + 30;
        $_SESSION['login_attempts'] = 0;
    }
}

function flashMessage(string $msg, string $type = 'success'): void {
    $_SESSION['flash'] = ['message' => $msg, 'type' => $type];
}

function getFlash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
