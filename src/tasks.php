<?php
declare(strict_types=1);

const STATUSES = [
    'todo'  => 'To do',
    'doing' => 'In progress',
    'done'  => 'Done',
];

// Returns an array of error messages (empty array = valid)
function validate_task(array $data): array
{
    $errors = [];

    if ($data['title'] === '') {
        $errors['title'] = 'Title is required.';
    } elseif (mb_strlen($data['title']) > 120) {
        $errors['title'] = 'Title must be 120 characters or fewer.';
    }

    if (mb_strlen($data['description']) > 2000) {
        $errors['description'] = 'Description must be 2000 characters or fewer.';
    }

    if (!array_key_exists($data['status'], STATUSES)) {
        $errors['status'] = 'Please choose a valid status.';
    }

    return $errors;
}

// AUTHORIZATION: only returns the task if it belongs to this user
// function find_task(int $id, int $userId): ?array
// {
//     $stmt = db()->prepare('SELECT * FROM tasks WHERE id = :id AND user_id = :uid');
//     $stmt->execute([':id' => $id, ':uid' => $userId]);
//     return $stmt->fetch() ?: null;
// }

function find_task(int $id, int $userId): ?array
{
    $stmt = db()->prepare(
        'SELECT * FROM tasks WHERE id = :id AND user_id = :uid AND deleted_at IS NULL'
    );
    $stmt->execute([':id' => $id, ':uid' => $userId]);
    return $stmt->fetch() ?: null;
}