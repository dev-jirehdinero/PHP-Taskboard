<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_role('admin');

$users = db()->query(
    'SELECT id, name, email, role, created_at FROM users ORDER BY id'
)->fetchAll();

$title = 'All users';
require __DIR__ . '/../src/views/header.php';
?>
<h1>All users (<?= count($users) ?>)</h1>

<table>
    <thead>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Registered</th></tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= (int) $u['id'] ?></td>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['role']) ?></td>
            <td><?= e($u['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../src/views/footer.php'; ?>