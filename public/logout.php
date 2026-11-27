<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

// Logout changes state, so it must be a POST with a CSRF token (not a plain link)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    logout();
    redirect('/login.php?logged_out=1');
}

redirect('/index.php');