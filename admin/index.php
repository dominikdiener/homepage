<?php
/**
 * Admin – Login
 */
require_once __DIR__ . '/auth.php';

// Bereits eingeloggt → weiter zum Dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isLockedOut()) {
        $error = 'Zu viele Fehlversuche. Bitte warte 30 Sekunden.';
    } elseif (!verifyCsrf($_POST['csrf'] ?? '')) {
        $error = 'Ungültige Anfrage. Bitte versuche es erneut.';
    } else {
        $password = $_POST['password'] ?? '';
        $hash = getPasswordHash();

        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_attempts'] = 0;
            header('Location: dashboard.php');
            exit;
        } else {
            registerFailedAttempt();
            $error = 'Falsches Passwort.';
        }
    }
}

$csrf = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Estrich Digital</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-box">
        <h1>🔒 Admin Login</h1>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">
            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" required autofocus>
            <button type="submit" class="btn btn-primary" style="width:100%">Anmelden</button>
        </form>
    </div>
</body>
</html>
