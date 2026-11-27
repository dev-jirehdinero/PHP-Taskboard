<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

require_login();                       // guard: redirects guests to login
$userId = (int) current_user_id();

// --- 1. Read and sanitize the query-string inputs ---
$q       = input_str($_GET, 'q');
$status  = input_str($_GET, 'status');
$page    = max(1, (int) input_str($_GET, 'page'));
$perPage = 5;

// sort task
$sortable = [
    'title'   => 'title',
    'status'  => 'status',
    'created' => 'created_at',
];
$sortKey    = array_key_exists(input_str($_GET, 'sort'), $sortable) ? input_str($_GET, 'sort') : 'created';
$sortColumn = $sortable[$sortKey];
$direction  = input_str($_GET, 'dir') === 'asc' ? 'ASC' : 'DESC';

// --- 2. Build the WHERE clause (always limited to the current user!) ---
// $where  = 'WHERE user_id = :uid';
$where = 'WHERE user_id = :uid AND deleted_at IS NULL';
$params = [':uid' => $userId];

if ($q !== '') {
    $where .= ' AND (title LIKE :q1 OR description LIKE :q2)';
    $params[':q1'] = '%' . $q . '%';
    $params[':q2'] = '%' . $q . '%';
}
if (array_key_exists($status, STATUSES)) {
    $where .= ' AND status = :status';
    $params[':status'] = $status;
}

// --- 3. Count for pagination ---
$countStmt = db()->prepare("SELECT COUNT(*) FROM tasks $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

$pages  = max(1, (int) ceil($total / $perPage));
$page   = min($page, $pages);
$offset = ($page - 1) * $perPage;

// --- 4. Fetch the current page ---
// $stmt = db()->prepare(
//     "SELECT * FROM tasks $where ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset"
// );
$stmt = db()->prepare(
    "SELECT * FROM tasks $where ORDER BY $sortColumn $direction, id DESC LIMIT :limit OFFSET :offset"
);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tasks = $stmt->fetchAll();

$title = 'My tasks';
require __DIR__ . '/../src/views/header.php';
?>
<div class="toolbar">
    <h1>My tasks (<?= $total ?>)</h1>
    <a class="button" href="/create.php">+ New task</a>
</div>

<form method="get" class="filters">
    <input type="search" name="q" placeholder="Search tasks…" value="<?= e($q) ?>">
    <select name="status">
        <option value="">All statuses</option>
        <?php foreach (STATUSES as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filter</button>
</form>

<?php if (!$tasks): ?>
    <p class="empty">No tasks found. Create your first one!</p>
<?php else: ?>
    <table>
        <!-- <thead>
            <tr><th>Task</th><th>Status</th><th>Created</th><th></th></tr>
        </thead> -->
        <thead>
            <tr>
                <th><?= sort_link('title',   'Task',    $sortKey, $direction, $q, $status) ?></th>
                <th><?= sort_link('status',  'Status',  $sortKey, $direction, $q, $status) ?></th>
                <th><?= sort_link('created', 'Created', $sortKey, $direction, $q, $status) ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td>
                    <strong><?= e($task['title']) ?></strong><br>
                    <small><?= e($task['description']) ?></small>
                </td>
                <td>
                    <span class="badge <?= e($task['status']) ?>">
                        <?= e(STATUSES[$task['status']] ?? $task['status']) ?>
                    </span>
                </td>
                <td><?= e($task['created_at']) ?></td>
                
                <td class="row-actions">
                    <a href="/edit.php?id=<?= (int) $task['id'] ?>">Edit</a>

                    <form method="post" action="/toggle.php" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                        <button type="submit" class="link">
                            <?= $task['status'] === 'done' ? 'Reopen' : 'Mark done' ?>
                        </button>
                    </form>

                    <form method="post" action="/delete.php" class="inline"
                        onsubmit="return confirm('Move this task to trash?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                        <button type="submit" class="link danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- <//?php if ($pages > 1): ?>
        <nav class="pager">
            <//?php for ($p = 1; $p <= $pages; $p++): ?>
                <a class="<//?= $p === $page ? 'current' : '' ?>"
                   href="/index.php?<//?= e(http_build_query(array_filter(['q' => $q, 'status' => $status, 'page' => $p]))) ?>">
                    <//?= $p ?>
                </a>
            <//?php endfor; ?>
        </nav>
    <//?php endif; ?> -->
    
    <?php if ($pages > 1): ?>
        <nav class="pager">
            <?php for ($p = 1; $p <= $pages; $p++): ?>
                <a class="<?= $p === $page ? 'current' : '' ?>"
                href="/index.php?<?= e(http_build_query(array_filter([
                    'q' => $q, 'status' => $status, 'sort' => $sortKey,
                    'dir' => strtolower($direction), 'page' => $p,
                ]))) ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>



<?php endif; ?>

<?php require __DIR__ . '/../src/views/footer.php'; ?>