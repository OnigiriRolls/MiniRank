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
            <?php if (isLoggedIn()): ?>
                <nav class="project-nav" aria-label="Projects">
                    <a class="button-link" href="index.php?action=project_index">Projects</a>
                    <ul class="project-switcher">
                        <?php foreach (Project::all(currentUserId()) as $project): ?>
                            <?php $isSelected = isset($projectId) && (int) $projectId === (int) $project['id']; ?>
                            <li>
                                <a
                                    class="project-switch-link<?= $isSelected ? ' is-selected' : '' ?>"
                                    href="index.php?action=index&amp;project_id=<?= (int) $project['id'] ?>"
                                    <?= $isSelected ? 'aria-current="page"' : '' ?>>
                                    <?= e($project['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            <?php if (!isLoggedIn()): ?>
                <nav class="auth-nav" aria-label="Account">
                    <a class="button-link" href="index.php?action=login">Log in</a>
                    <a class="button-link" href="index.php?action=register">Register</a>
                </nav>
            <?php else: ?>
                <?php $user = currentUser(); ?>
                <nav class="auth-nav" aria-label="Account">
                    <span class="auth-username"><?= e($user['username']) ?></span>
                    <form method="post" action="index.php" class="inline-form">
                        <input type="hidden" name="action" value="logout">
                        <?= csrfField() ?>
                        <button type="submit" class="button-link">Log out</button>
                    </form>
                </nav>
            <?php endif; ?>
        </div>
    </header>
    <main class="container">