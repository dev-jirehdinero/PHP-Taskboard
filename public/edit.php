<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_login();
$userId = (int) current_user_id();

// 1. Validate the id from the URL
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false) {
    abort(404, 'Task not found.');
}

// 2. Load the task ONLY if it belongs to this user
$task = find_task($id, $userId);
if ($task === null) {
    abort(404, 'Task not found.');      // 404, not 403: don't reveal that the id exists
}

$errors = [];
$data = [
    'title'       => $task['title'],
    'description' => $task['description'],
    'status'      => $task['status'],
];

// 3. Handle the submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $data = [
        'title'       => input_str($_POST, 'title'),
        'description' => input_str($_POST, 'description'),
        'status'      => input_str($_POST, 'status'),
    ];
    $errors = validate_task($data);

    if (!$errors) {
        $stmt = db()->prepare(
            'UPDATE tasks
                SET title = :title, description = :description, status = :status,
                    updated_at = CURRENT_TIMESTAMP
              WHERE id = :id AND user_id = :uid'      // ownership enforced AGAIN in the query
        );
        $stmt->execute([
            ':title'       => $data['title'],
            ':description' => $data['description'],
            ':status'      => $data['status'],
            ':id'          => $id,
            ':uid'         => $userId,
        ]);

        flash('success', 'Task updated.');
        redirect('/index.php');
    }
}

$title = 'Edit task';
$submitLabel = 'Save changes';
require __DIR__ . '/../src/views/header.php';
?>
<h1>Edit task</h1>
<?php require __DIR__ . '/../src/views/task_form.php'; ?>
<?php require __DIR__ . '/../src/views/footer.php'; ?>