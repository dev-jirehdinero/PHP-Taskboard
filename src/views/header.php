<?php
// Expects $title to be set by the page that includes it
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'TaskBoard') ?> · TaskBoard</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="/index.php">TaskBoard</a>
    <nav>
        <?php if ($user): ?>
            <span>Hi, <?= e($user['name']) ?></span>
            <a href="/profile.php">Change password</a>
            <a href="/trash.php">Trash</a>
        <?php if (($user['role'] ?? 'user') === 'admin'): ?>
            <a href="/admin_users.php">Users</a>
        <?php endif; ?>
        <form method="post" action="/logout.php" class="inline">
            <?= csrf_field() ?>
            <button type="submit" class="link">Log out</button>
        </form>
        <?php else: ?>
            <a href="/login.php">Log in</a>
            <a href="/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
<?php foreach (get_flashes() as $flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
<?php endforeach; ?>