<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(config('site_name', 'MiniRank')) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="container">
            <h1><a href="index.php?action=project_index"><?= e(config('site_name', 'MiniRank')) ?></a></h1>
            <?php $activeId = activeProjectId(); ?>
            <nav class="project-nav" aria-label="Projects">
                <a class="button-link" href="index.php?action=project_index">Projects</a>
                <ul class="project-switcher">
                    <?php foreach (Project::all() as $project): ?>
                        <?php $isActive = $activeId !== null && (int) $project['id'] === $activeId; ?>
                        <li>
                            <a
                                class="project-switch-link<?= $isActive ? ' is-active' : '' ?>"
                                href="index.php?action=project_switch&amp;id=<?= (int) $project['id'] ?>"
                                <?= $isActive ? 'aria-current="page"' : '' ?>>
                                <?= e($project['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">