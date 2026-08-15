<?php
?>
<h2><?= e($keyword['phrase']) ?></h2>

<p><a href="index.php">Back to keyword list</a></p>

<?php if (empty($history)): ?>
    <p class="empty">No position history for this keyword yet.</p>
<?php else: ?>
    <table class="keyword-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $row): ?>
                <tr>
                    <td><?= $row['date'] ?></td>
                    <td><?= (int) $row['position'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>