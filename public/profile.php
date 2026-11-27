<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_login();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $current = input_str($_POST, 'current_password', false);
    $new     = input_str($_POST, 'new_password', false);
    $confirm = input_str($_POST, 'confirm_password', false);

    $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = :id');
    $stmt->execute([':id' => current_user_id()]);
    $hash = (string) $stmt->fetchColumn();

    if (!password_verify($current, $hash)) {
        $errors['current_password'] = 'Current password is incorrect.';
    }
    if (strlen($new) < 8) {
        $errors['new_password'] = 'New password must be at least 8 characters.';
    }
    if ($new !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $upd = db()->prepare('UPDATE users SET password_hash = :h WHERE id = :id');
        $upd->execute([':h' => password_hash($new, PASSWORD_DEFAULT), ':id' => current_user_id()]);

        session_regenerate_id(true);       // privilege change → new session ID
        flash('success', 'Password changed.');
        redirect('/index.php');
    }
}

$title = 'Change password';
require __DIR__ . '/../src/views/header.php';
?>
<h1>Change password</h1>
<form method="post" class="card" novalidate>
    <?= csrf_field() ?>
    <label>Current password
        <input type="password" name="current_password" autocomplete="current-password" required>
        <?= field_error($errors, 'current_password') ?>
    </label>
    <label>New password
        <input type="password" name="new_password" autocomplete="new-password" required>
        <?= field_error($errors, 'new_password') ?>
    </label>
    <label>Confirm new password
        <input type="password" name="confirm_password" autocomplete="new-password" required>
        <?= field_error($errors, 'confirm_password') ?>
    </label>
    <button type="submit">Update password</button>
</form>
<?php require __DIR__ . '/../src/views/footer.php'; ?>