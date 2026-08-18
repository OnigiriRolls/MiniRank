<?php
?>
<h2><?= e($keyword['phrase']) ?></h2>

<p><a href="index.php?action=index&amp;project_id=<?= (int) $projectId ?>">Back to keyword list</a> <a class="button-link" href="index.php?action=export&amp;project_id=<?= (int) $projectId ?>&amp;id=<?= (int) $keyword['id'] ?>">Export CSV</a></p>

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
                    <td data-label="Date"><?= e($row['date']) ?></td>
                    <td data-label="Position"><?= (int) $row['position'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    $labels = [];
    $values = [];
    foreach (array_reverse($history) as $row) {
        $labels[] = $row['date'];
        $values[] = (int) $row['position'];
    }
    $chartData = json_encode(
        ['labels' => $labels, 'values' => $values],
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    ?>
    <div class="chart-wrapper">
        <canvas id="position-chart" aria-label="Position history chart" role="img"></canvas>
    </div>
    <script type="application/json" id="position-chart-data">
        <?= $chartData ?>
    </script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>