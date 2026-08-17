<?php

declare(strict_types=1);

class Keyword
{
    public static function allForProject(int $projectId): array
    {
        $stmt = db()->prepare(
            'SELECT id, phrase, created_at FROM keywords WHERE project_id = :project_id ORDER BY phrase COLLATE NOCASE ASC'
        );
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll();
    }

    public static function withStats(int $projectId): array
    {
        $keywords = self::allForProject($projectId);

        foreach ($keywords as &$keyword) {
            $keyword['current_position'] = null;
            $recent = Position::recent((int) $keyword['id'], 1);
            if (!empty($recent)) {
                $keyword['current_position'] = (int) $recent[0]['position'];
            }
            $keyword['trend'] = Position::trend((int) $keyword['id']);
        }
        unset($keyword);

        return $keywords;
    }

    public static function find(int $id, int $projectId): ?array
    {
        $stmt = db()->prepare('SELECT id, phrase, created_at FROM keywords WHERE id = :id AND project_id = :project_id');
        $stmt->execute([':id' => $id, ':project_id' => $projectId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(int $projectId, string $phrase): int
    {
        $stmt = db()->prepare('INSERT INTO keywords (project_id, phrase) VALUES (:project_id, :phrase)');
        $stmt->execute([':project_id' => $projectId, ':phrase' => $phrase]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, int $projectId, string $phrase): bool
    {
        $stmt = db()->prepare('UPDATE keywords SET phrase = :phrase WHERE id = :id AND project_id = :project_id');
        $stmt->execute([':id' => $id, ':project_id' => $projectId, ':phrase' => $phrase]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id, int $projectId): bool
    {
        $stmt = db()->prepare('DELETE FROM keywords WHERE id = :id AND project_id = :project_id');
        $stmt->execute([':id' => $id, ':project_id' => $projectId]);
        return $stmt->rowCount() > 0;
    }
}
