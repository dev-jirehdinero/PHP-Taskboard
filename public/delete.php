<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abort(405, 'Method not allowed.');      // deleting via a GET link is never acceptable
}
verify_csrf();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false) {
    abort(404, 'Task not found.');
}

// $stmt = db()->prepare('DELETE FROM tasks WHERE id = :id AND user_id = :uid');
$stmt = db()->prepare(
    'UPDATE tasks SET deleted_at = CURRENT_TIMESTAMP
      WHERE id = :id AND user_id = :uid AND deleted_at IS NULL'
);

$stmt->execute([':id' => $id, ':uid' => current_user_id()]);

if ($stmt->rowCount() === 0) {
    abort(404, 'Task not found.');          // nothing deleted: wrong id or not yours
}

flash('success', 'Task deleted.');
redirect('/index.php');