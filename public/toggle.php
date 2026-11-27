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
    "UPDATE tasks
        SET status = CASE status WHEN 'done' THEN 'todo' ELSE 'done' END,
            updated_at = CURRENT_TIMESTAMP
      WHERE id = :id AND user_id = :uid"
);
$stmt->execute([':id' => $id, ':uid' => current_user_id()]);

redirect('/index.php');