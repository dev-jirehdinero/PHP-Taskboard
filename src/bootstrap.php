<?php
declare(strict_types=1);

header('X-Frame-Options: DENY');                 // blocks clickjacking
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

// Development: show every error. In production set display_errors to 0 and log instead.
error_reporting(E_ALL);
ini_set('display_errors', '1');

const SESSION_IDLE_SECONDS = 1800;   // log out after 30 minutes of inactivity

ini_set('session.use_strict_mode', '1');   // reject session IDs the server never issued
ini_set('session.use_only_cookies', '1');  // never accept IDs from the URL

session_set_cookie_params([
    'lifetime' => 0,                          // cookie dies when the browser closes
    'path'     => '/',
    'secure'   => !empty($_SERVER['HTTPS']),  // send only over HTTPS when available
    'httponly' => true,                       // JavaScript cannot read the cookie
    'samesite' => 'Lax',                      // blocks most cross-site request forgery
]);
session_name('taskboard_session');
session_start();

// Idle timeout: if the user has been away too long, wipe the session
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > SESSION_IDLE_SECONDS) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['last_activity'] = time();

require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/tasks.php';