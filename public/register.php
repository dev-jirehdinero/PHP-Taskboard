<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_guest();

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name     = input_str($_POST, 'name');
    $email    = strtolower(input_str($_POST, 'email'));
    $password = input_str($_POST, 'password', false);   // do NOT trim passwords
    $confirm  = input_str($_POST, 'password_confirm', false);

    if ($name === '' || mb_strlen($name) > 50) {
        $errors['name'] = 'Name is required (max 50 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors['password_confirm'] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'That email is already registered.';
        }
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :hash)'
        );
        $stmt->execute([
            ':name'  => $name,
            ':email' => $email,
            ':hash'  => password_hash($password, PASSWORD_DEFAULT),
        ]);

        flash('success', 'Account created. You can log in now.');
        redirect('/login.php');          // PRG: never render after a successful POST
    }
}

$title = 'Register';
require __DIR__ . '/../src/views/header.php';
?>
<h1>Create an account</h1>

<form method="post" class="card" novalidate>
    <?= csrf_field() ?>

    <label>Name
        <input type="text" name="name" value="<?= e($name) ?>" maxlength="50" required>
        <?= field_error($errors, 'name') ?>
    </label>

    <label>Email
        <input type="email" name="email" value="<?= e($email) ?>" required>
        <?= field_error($errors, 'email') ?>
    </label>

    <label>Password (min 8 characters)
        <input type="password" name="password" autocomplete="new-password" required>
        <?= field_error($errors, 'password') ?>
    </label>

    <label>Confirm password
        <input type="password" name="password_confirm" autocomplete="new-password" required>
        <?= field_error($errors, 'password_confirm') ?>
    </label>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="/login.php">Log in</a></p>
<?php require __DIR__ . '/../src/views/footer.php'; ?>