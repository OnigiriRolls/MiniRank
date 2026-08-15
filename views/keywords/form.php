<?php
?>
<h2>Edit keyword</h2>

<?php if ($error !== null): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="index.php" class="keyword-form">
    <input type="hidden" name="action" value="update">
    <?php if ($id !== null): ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
    <?php endif; ?>
    <label for="phrase">Keyword</label>
    <input
        id="phrase"
        type="text"
        name="phrase"
        value="<?= e($phrase) ?>"
        required
        maxlength="255"
        placeholder="e.g. best running shoes"
    >
    <div class="form-actions">
        <button type="submit">Save</button>
        <a href="index.php" class="button-link">Cancel</a>
    </div>
</form>
