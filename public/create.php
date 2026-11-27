<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_login();

$errors = [];
$data   = ['title' => '', 'description' => '', 'status' => 'todo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data = [
        'title'       => input_str($_POST, 'title'),
        'description' => input_str($_POST, 'description'),
        'status'      => input_str($_POST, 'status') ?: 'todo',
    ];
    $errors = validate_task($data);

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO tasks (user_id, title, description, status)
             VALUES (:uid, :title, :description, :status)'
        );
        $stmt->execute([
            ':uid'         => current_user_id(),    // owner comes from the SESSION, never from the form
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':status'      => $data['status'],
        ]);

        flash('success', 'Task created.');
        redirect('/index.php');
    }
}

$title = 'New task';
$submitLabel = 'Create task';
require __DIR__ . '/../src/views/header.php';
?>
<h1>New task</h1>
<?php require __DIR__ . '/../src/views/task_form.php'; ?>
<?php require __DIR__ . '/../src/views/footer.php'; ?>