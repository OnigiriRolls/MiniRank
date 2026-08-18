<?php

declare(strict_types=1);

class AuthController
{
    public function register(): void
    {
        $this->renderRegister('', null);
    }

    public function login(): void
    {
        $this->renderLogin('', null);
    }

    public function authenticate(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $user = User::findByUsername($username);
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $this->renderLogin($username, 'Invalid username or password.');
            return;
        }

        loginUser((int) $user['id']);
        redirect('index.php');
    }

    public function logout(): void
    {
        logoutUser();
        redirect('index.php?action=login');
    }

    public function store(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirmation'] ?? '');

        $error = $this->validateRegistration($username, $password, $passwordConfirm);
        if ($error !== null) {
            $this->renderRegister($username, $error);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = User::create($username, $hash);
        loginUser($userId);
        redirect('index.php');
    }

    private function validateRegistration(string $username, string $password, string $passwordConfirm): ?string
    {
        $username = trim($username);

        if ($username === '') {
            return 'The username must not be empty.';
        }
        if (mb_strlen($username) > 255) {
            return 'The username must be at most 255 characters.';
        }
        if (preg_match('/[\x00-\x1F\x7F]/', $username)) {
            return 'The username must not contain control characters.';
        }
        if (User::findByUsername($username) !== null) {
            return 'This username already exists.';
        }
        if (mb_strlen($password) < 8) {
            return 'The password must be at least 8 characters.';
        }
        if ($password !== $passwordConfirm) {
            return 'The password confirmation does not match.';
        }
        return null;
    }

    private function renderRegister(string $username, ?string $error): void
    {
        $this->render('auth/register', [
            'username' => $username,
            'error' => $error,
        ], $error !== null ? 400 : 200);
    }

    private function renderLogin(string $username, ?string $error): void
    {
        $this->render('auth/login', [
            'username' => $username,
            'error' => $error,
        ], $error !== null ? 400 : 200);
    }

    private function render(string $view, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../views/header.php';
        require __DIR__ . '/../../views/' . $view . '.php';
        require __DIR__ . '/../../views/footer.php';
    }
}