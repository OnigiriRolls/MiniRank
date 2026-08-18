<?php

declare(strict_types=1);

class Project
{
    public static function all(int $userId): array
    {
        $stmt = db()->prepare('SELECT id, name, url, created_at FROM projects WHERE user_id = :user_id ORDER BY name COLLATE NOCASE ASC');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id, int $userId): ?array
    {
        $stmt = db()->prepare('SELECT id, name, url, created_at FROM projects WHERE id = :id AND user_id = :user_id');
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(int $userId, string $name, ?string $url = null): int
    {
        $stmt = db()->prepare('INSERT INTO projects (user_id, name, url) VALUES (:user_id, :name, :url)');
        $stmt->execute([':user_id' => $userId, ':name' => $name, ':url' => $url]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, int $userId, string $name, ?string $url = null): bool
    {
        $stmt = db()->prepare('UPDATE projects SET name = :name, url = :url WHERE id = :id AND user_id = :user_id');
        $stmt->execute([':id' => $id, ':user_id' => $userId, ':name' => $name, ':url' => $url]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id, int $userId): bool
    {
        $stmt = db()->prepare('DELETE FROM projects WHERE id = :id AND user_id = :user_id');
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }

    public static function keywordCount(int $projectId): int
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM keywords WHERE project_id = :project_id');
        $stmt->execute([':project_id' => $projectId]);
        return (int) $stmt->fetchColumn();
    }
}
