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

function loginUser(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $id;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function logoutUser(): void
{
    unset($_SESSION['user_id'], $_SESSION['active_project_id']);
    session_regenerate_id(true);
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    return User::find((int) $_SESSION['user_id']);
}

function currentUserId(): ?int
{
    return isLoggedIn() ? (int) $_SESSION['user_id'] : null;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    $token = csrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

function verifyCsrf(mixed $token): void
{
    if (!is_string($token) || !hash_equals(csrfToken(), $token)) {
        http_response_code(400);
        echo 'Bad request: invalid CSRF token.';
        exit;
    }
}

function simulatePosition(?int $previous): int
{
    if ($previous === null) {
        return random_int(1, 100);
    }

    $delta = random_int(1, 3) === 1 ? random_int(-8, 8) : random_int(-3, 3);

    return max(1, min(100, $previous + $delta));
}
