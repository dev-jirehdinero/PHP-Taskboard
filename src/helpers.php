<?php
declare(strict_types=1);

/* ---------- Output & input ---------- */

// Escape text before printing into HTML (prevents XSS). Use on EVERY dynamic value.
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Read a string from $_GET / $_POST safely (arrays and missing keys become '')
function input_str(array $source, string $key, bool $trim = true): string
{
    $value = $source[$key] ?? '';
    if (!is_string($value)) {
        return '';
    }
    return $trim ? trim($value) : $value;
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function abort(int $status, string $message): never
{
    http_response_code($status);
    echo '<!doctype html><meta charset="utf-8"><title>' . $status . '</title>'
       . '<p style="font-family:sans-serif;padding:2rem">' . e($message)
       . ' <a href="/index.php">Back home</a></p>';
    exit;
}

function field_error(array $errors, string $key): string
{
    return isset($errors[$key]) ? '<small class="error">' . e($errors[$key]) . '</small>' : '';
}

/* ---------- CSRF protection ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        abort(419, 'Your form expired or was invalid. Please go back and try again.');
    }
}

/* ---------- Flash messages (shown once, then gone) ---------- */

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $message];
}

function get_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/* ---------- Authentication helpers ---------- */

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function is_logged_in(): bool
{
    return current_user_id() !== null;
}

// Loads the logged-in user's row once per request (never selects the password hash)
function current_user(): ?array
{
    static $cache = [];

    $id = current_user_id();
    if ($id === null) {
        return null;
    }
    if (!array_key_exists($id, $cache)) {
        $stmt = db()->prepare('SELECT id, name, email, role FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $cache[$id] = $stmt->fetch() ?: null;
    }
    return $cache[$id];
}

// Put at the top of every page that needs a logged-in user
function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please log in to continue.');
        redirect('/login.php');
    }
    if (current_user() === null) {   // session points to a user that no longer exists
        logout();
        redirect('/login.php');
    }
}

// Put at the top of login/register so logged-in users skip them
function require_guest(): void
{
    if (is_logged_in()) {
        redirect('/index.php');
    }
}

function logout(): void
{
    $_SESSION = [];                                   // 1. clear the data

    if (ini_get('session.use_cookies')) {             // 2. expire the browser cookie
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'] ?: 'Lax',
        ]);
    }

    session_destroy();                                // 3. delete the server-side session
}

// login attempts
function too_many_attempts(string $email, string $ip): bool
{
    $stmt = db()->prepare(
        'SELECT COUNT(*) FROM login_attempts
          WHERE (email = :email OR ip = :ip) AND attempted_at > :since'
    );
    $stmt->execute([':email' => $email, ':ip' => $ip, ':since' => time() - 900]);
    return (int) $stmt->fetchColumn() >= 5;
}

function record_failed_attempt(string $email, string $ip): void
{
    $stmt = db()->prepare('INSERT INTO login_attempts (email, ip, attempted_at) VALUES (:e, :ip, :t)');
    $stmt->execute([':e' => $email, ':ip' => $ip, ':t' => time()]);
}

function clear_attempts(string $email): void
{
    db()->prepare('DELETE FROM login_attempts WHERE email = :e')->execute([':e' => $email]);
}

// Record the log-in attempts
function log_login(string $email, string $ip, bool $success): void
{
    $stmt = db()->prepare(
        'INSERT INTO login_log (email, ip, success) VALUES (:e, :ip, :s)'
    );
    $stmt->execute([':e' => $email, ':ip' => $ip, ':s' => (int) $success]);
}

// sort task
function sort_link(string $key, string $label, string $sortKey, string $direction, string $q, string $status): string
{
    $nextDir = ($sortKey === $key && $direction === 'ASC') ? 'desc' : 'asc';
    $arrow   = $sortKey === $key ? ($direction === 'ASC' ? ' ▲' : ' ▼') : '';
    $query   = http_build_query(array_filter([
        'q' => $q, 'status' => $status, 'sort' => $key, 'dir' => $nextDir,
    ]));
    return '<a href="/index.php?' . e($query) . '">' . e($label) . $arrow . '</a>';
}

//users
function require_role(string $role): void
{
    require_login();
    $user = current_user();
    if (($user['role'] ?? 'user') !== $role) {
        abort(403, 'You do not have permission to view this page.');
    }
}