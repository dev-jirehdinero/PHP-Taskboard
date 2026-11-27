<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_login();

$stmt = db()->prepare(
    'SELECT * FROM tasks WHERE user_id = :uid AND deleted_at IS NOT NULL ORDER BY deleted_at DESC'
);
$stmt->execute([':uid' => current_user_id()]);
$tasks = $stmt->fetchAll();

$title = 'Trash';
require __DIR__ . '/../src/views/header.php';
?>
<div class="toolbar">
    <h1>Trash (<?= count($tasks) ?>)</h1>
    <a href="/index.php">← Back to tasks</a>
</div>

<?php if (!$tasks): ?>
    <p class="empty">Trash is empty.</p>
<?php else: ?>
    <table>
        <thead>
            <tr><th>Task</th><th>Deleted</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><strong><?= e($task['title']) ?></strong></td>
                <td><?= e($task['deleted_at']) ?></td>
                <td>
                    <form method="post" action="/restore.php" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                        <button type="submit" class="link">Restore</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../src/views/footer.php'; ?>