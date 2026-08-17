<?php
?>
<h2><?= $id === null ? 'Add project' : 'Edit project' ?></h2>

<?php if ($error !== null): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="index.php" class="keyword-form">
    <input type="hidden" name="action" value="<?= $id === null ? 'project_store' : 'project_update' ?>">
    <?= csrfField() ?>
    <?php if ($id !== null): ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
    <?php endif; ?>
    <label for="name">Project name</label>
    <input
        id="name"
        type="text"
        name="name"
        value="<?= e($name) ?>"
        required
        maxlength="255"
        placeholder="e.g. Nice Store">
    <label for="url">Website URL (optional)</label>
    <input
        id="url"
        type="text"
        name="url"
        value="<?= e($url ?? '') ?>"
        maxlength="2048"
        placeholder="e.g. https://www.nicestore.com">
    <div class="form-actions">
        <button type="submit">Save</button>
        <a href="index.php?action=project_index" class="button-link">Cancel</a>
    </div>
</form>