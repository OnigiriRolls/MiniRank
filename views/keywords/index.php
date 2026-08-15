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
    <input
        type="text"
        id="keyword-search"
        placeholder="Search keywords"
        class="keyword-search"
    >
    <p id="no-results" class="empty" hidden>No keywords match your search.</p>
    <table class="keyword-table">
        <thead>
            <tr>
                <th>Keyword</th>
                <th>Current Position</th>
                <th>7-day Trend</th>
                <th class="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($keywords as $kw): ?>
                <tr data-keyword-id="<?= (int) $kw['id'] ?>">
                    <td><a href="index.php?action=show&id=<?= (int) $kw['id'] ?>"><?= e($kw['phrase']) ?></a></td>
                    <td><?= $kw['current_position'] !== null ? (int) $kw['current_position'] : '&ndash;' ?></td>
                    <td><?= $kw['trend'] === null ? '&ndash;' : e($kw['trend']) ?></td>
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
