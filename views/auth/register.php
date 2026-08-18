<?php
?>
<h2>Register</h2>

<?php if ($error !== null): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="index.php" class="keyword-form">
    <input type="hidden" name="action" value="register_store">
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
        minlength="8"
        autocomplete="new-password">
    <label for="password-confirmation">Confirm password</label>
    <input
        id="password-confirmation"
        type="password"
        name="password_confirmation"
        required
        minlength="8"
        autocomplete="new-password">
    <div class="form-actions">
        <button type="submit">Create account</button>
    </div>
</form>