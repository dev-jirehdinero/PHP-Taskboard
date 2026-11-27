<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_guest();

$error = '';
$email = '';
$loggedOut = isset($_GET['logged_out']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = strtolower(input_str($_POST, 'email'));
    $password = input_str($_POST, 'password', false);
    $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    if (too_many_attempts($email, $ip)) {
        $error = 'Too many attempts. Please wait 15 minutes and try again.';
    } else {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {

            // Upgrade the stored hash if PHP's default algorithm/cost has improved
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $upd = db()->prepare('UPDATE users SET password_hash = :h WHERE id = :id');
                $upd->execute([':h' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
            }

            clear_attempts($email);                // success: reset the counter
            log_login($email, $ip, true);  

            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];

            flash('success', 'Welcome back, ' . $user['name'] . '!');
            redirect('/index.php');
        }

        record_failed_attempt($email, $ip);        // only reached when the login failed
        log_login($email, $ip, false);  
        $error = 'Invalid email or password.';
    }
}

$title = 'Log in';
require __DIR__ . '/../src/views/header.php';
?>
<h1>Log in</h1>

<?php if ($loggedOut): ?>
    <div class="alert success">You have been logged out.</div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" class="card">
    <?= csrf_field() ?>

    <label>Email
        <input type="email" name="email" value="<?= e($email) ?>" required autofocus>
    </label>

    <label>Password
        <input type="password" name="password" autocomplete="current-password" required>
    </label>

    <button type="submit">Log in</button>
</form>

<p>No account yet? <a href="/register.php">Register</a></p>
<?php require __DIR__ . '/../src/views/footer.php'; ?>