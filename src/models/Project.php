<?php

declare(strict_types=1);

class Project
{
    public static function all(): array
    {
        return db()
            ->query('SELECT id, name, url, created_at FROM projects ORDER BY name COLLATE NOCASE ASC')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT id, name, url, created_at FROM projects WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(string $name, ?string $url = null): int
    {
        $stmt = db()->prepare('INSERT INTO projects (name, url) VALUES (:name, :url)');
        $stmt->execute([':name' => $name, ':url' => $url]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, string $name, ?string $url = null): bool
    {
        $stmt = db()->prepare('UPDATE projects SET name = :name, url = :url WHERE id = :id');
        $stmt->execute([':id' => $id, ':name' => $name, ':url' => $url]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id): bool
    {
        $stmt = db()->prepare('DELETE FROM projects WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public static function keywordCount(int $projectId): int
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM keywords WHERE project_id = :project_id');
        $stmt->execute([':project_id' => $projectId]);
        return (int) $stmt->fetchColumn();
    }
}