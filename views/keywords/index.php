<?php
?>
<h2>Tracked keywords</h2>

<button type="button" id="refresh-positions" class="refresh-button">Refresh positions</button>
<p id="refresh-status" class="refresh-status" hidden></p>

<form method="post" action="index.php" class="add-form">
    <input type="hidden" name="action" value="store">
    <input
        type="text"
        name="phrase"
        placeholder="Add a keyword to track"
        value="<?= e($addPhrase ?? '') ?>"
        required
        maxlength="255"
    >
    <button type="submit">Add</button>
</form>

<?php if (empty($keywords)): ?>
    <p class="empty">No keywords yet. Add your first one above.</p>
<?php else: ?>
    <table class="keyword-table">
        <thead>
            <tr>
                <th>Keyword</th>
                <th class="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($keywords as $kw): ?>
                <tr>
                    <td><?= e($kw['phrase']) ?></td>
                    <td class="actions">
                        <a href="index.php?action=edit&id=<?= (int) $kw['id'] ?>">Edit</a>
                        <form method="post" action="index.php" class="inline" onsubmit="return confirm('Delete this keyword?');">
                            <input type="hidden" name="action" value="destroy">
                            <input type="hidden" name="id" value="<?= (int) $kw['id'] ?>">
                            <button type="submit" class="link-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
