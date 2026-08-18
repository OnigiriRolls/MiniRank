<?php
?>
<h2>Projects</h2>

<p><a class="button-link" href="index.php?action=project_create">Add project</a></p>

<?php if (empty($projects)): ?>
    <p class="empty">No projects yet. Add your first one above.</p>
<?php else: ?>
    <table class="keyword-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>URL</th>
                <th>Keywords</th>
                <th class="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td data-label="Name">
                        <a href="index.php?action=index&amp;project_id=<?= (int) $project['id'] ?>"><?= e($project['name']) ?></a>
                    </td>
                    <td data-label="URL"><?= $project['url'] !== null ? e($project['url']) : '&ndash;' ?></td>
                    <td data-label="Keywords"><?= (int) $project['keyword_count'] ?></td>
                    <td class="actions" data-label="Actions">
                        <a href="index.php?action=index&amp;project_id=<?= (int) $project['id'] ?>">View</a>
                        <a href="index.php?action=project_edit&amp;id=<?= (int) $project['id'] ?>">Edit</a>
                        <form method="post" action="index.php" class="inline" onsubmit="return confirm('Delete this project and all of its keywords?');">
                            <input type="hidden" name="action" value="project_destroy">
                            <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
                            <?= csrfField() ?>
                            <button type="submit" class="link-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>