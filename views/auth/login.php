<?php
?>
<h2>Log in</h2>

<?php if ($error !== null): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="index.php" class="keyword-form">
    <input type="hidden" name="action" value="login_store">
    <?= csrfField() ?>
    <label for="username">Username</label>
    <input
        id="username"
        type="text"
        name="username"
        value="<?= e($username) ?>"
        required
        maxlength="255"
        autocomplete="username"
        placeholder="e.g. alice">
    <label for="password">Password</label>
    <input
        id="password"
        type="password"
        name="password"
        required
        autocomplete="current-password">
    <div class="form-actions">
        <button type="submit">Log in</button>
    </div>
</form>