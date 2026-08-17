<?php

declare(strict_types=1);

function setActiveProject(?int $id): void
{
    if ($id === null) {
        unset($_SESSION['active_project_id']);
        return;
    }
    $_SESSION['active_project_id'] = $id;
}

function activeProjectId(): ?int
{
    return isset($_SESSION['active_project_id']) ? (int) $_SESSION['active_project_id'] : null;
}

function simulatePosition(?int $previous): int
{
    if ($previous === null) {
        return random_int(1, 100);
    }

    $delta = random_int(1, 3) === 1 ? random_int(-8, 8) : random_int(-3, 3);

    return max(1, min(100, $previous + $delta));
}
