<?php
// Shared by create.php and edit.php. Expects: $data, $errors, $submitLabel
?>
<form method="post" class="card" novalidate>
    <?= csrf_field() ?>

    <label>Title
        <input type="text" name="title" value="<?= e($data['title']) ?>" maxlength="120" required>
        <?= field_error($errors, 'title') ?>
    </label>

    <label>Description
        <textarea name="description" rows="5"><?= e($data['description']) ?></textarea>
        <?= field_error($errors, 'description') ?>
    </label>

    <label>Status
        <select name="status">
            <?php foreach (STATUSES as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $data['status'] === $value ? 'selected' : '' ?>>
                    <?= e($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= field_error($errors, 'status') ?>
    </label>

    <div class="actions">
        <button type="submit"><?= e($submitLabel) ?></button>
        <a href="/index.php">Cancel</a>
    </div>
</form>