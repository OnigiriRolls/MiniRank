<?php

declare(strict_types=1);

class User
{
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT id, username, password_hash, created_at FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = db()->prepare('SELECT id, username, password_hash, created_at FROM users WHERE username = :username COLLATE NOCASE');
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(string $username, string $passwordHash): int
    {
        $stmt = db()->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)');
        $stmt->execute([':username' => $username, ':password_hash' => $passwordHash]);
        return (int) db()->lastInsertId();
    }
}