<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config.php';

function config(string $key, mixed $default = null): mixed
{
    global $config;
    return $config[$key] ?? $default;
}

spl_autoload_register(function (string $class): void {
    $dirs = [
        __DIR__ . '/models/',
        __DIR__ . '/controllers/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

$dbDir = dirname($config['db_path']);
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

$pdo = new PDO('sqlite:' . $config['db_path']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->exec('PRAGMA foreign_keys = ON');

function db(): PDO
{
    global $pdo;
    return $pdo;
}

require __DIR__ . '/database.php';
createSchema($pdo);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
