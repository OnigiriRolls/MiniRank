<?php
?>
<h2>Tracked keywords</h2>

<?php if ($project === null): ?>
    <p class="empty">No project selected. <a href="index.php?action=project_index">Create a project first.</a></p>
<?php else: ?>
<button type="button" id="refresh-positions" class="refresh-button">Refresh positions</button>
<p id="refresh-status" class="refresh-status" hidden></p>

<div class="filter-form" role="search" aria-label="Filter keywords">
    <label class="filter-field">
        <span>Position from</span>
        <input type="number" id="position-min" min="1" max="100" placeholder="1">
    </label>
    <label class="filter-field">
        <span>Position to</span>
        <input type="number" id="position-max" min="1" max="100" placeholder="100">
    </label>
    <label class="filter-field">
        <span>Movement</span>
        <select id="movement-filter">
            <option value="">Any</option>
            <option value="improved">Improved</option>
            <option value="declined">Declined</option>
            <option value="stable">Stable</option>
        </select>
    </label>
    <button type="button" id="clear-filters" class="button-link">Clear filters</button>
</div>

<form method="post" action="index.php" class="add-form">
    <input type="hidden" name="action" value="store">
    <input
        type="text"
        name="phrase"
        placeholder="Add a keyword to track"
        value="<?= e($addPhrase ?? '') ?>"
        required
        maxlength="255">
    <button type="submit">Add</button>
</form>

<?php if (($addError ?? null) !== null): ?>
    <p id="add-error" class="error"><?= e($addError) ?></p>
<?php endif; ?>

<?php if (empty($keywords)): ?>
    <p class="empty">No keywords yet. Add your first one above.</p>
<?php else: ?>
    <input
        type="text"
        id="keyword-search"
        placeholder="Search keywords"
        class="keyword-search">
    <p id="no-results" class="empty" hidden>No keywords match your filters.</p>
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
                <tr
                    data-keyword-id="<?= (int) $kw['id'] ?>"
                    data-position="<?= $kw['current_position'] !== null ? (int) $kw['current_position'] : 0 ?>"
                    data-trend="<?= e($kw['trend'] ?? '') ?>">
                    <td data-label="Keyword"><a href="index.php?action=show&id=<?= (int) $kw['id'] ?>"><?= e($kw['phrase']) ?></a></td>
                    <td data-label="Current Position"><?= $kw['current_position'] !== null ? (int) $kw['current_position'] : '&ndash;' ?></td>
                    <td data-label="7-day Trend"><?= $kw['trend'] === null ? '&ndash;' : e($kw['trend']) ?></td>
                    <td class="actions" data-label="Actions">
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
<?php endif; ?>