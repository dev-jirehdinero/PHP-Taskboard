<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abort(405, 'Method not allowed.');
}
verify_csrf();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false) {
    abort(404, 'Task not found.');
}

$stmt = db()->prepare(
    'UPDATE tasks SET deleted_at = NULL
      WHERE id = :id AND user_id = :uid AND deleted_at IS NOT NULL'
);
$stmt->execute([':id' => $id, ':uid' => current_user_id()]);

if ($stmt->rowCount() === 0) {
    abort(404, 'Task not found.');
}

flash('success', 'Task restored.');
redirect('/trash.php');