<?php

declare(strict_types=1);

class Keyword
{
    public static function all(): array
    {
        return db()
            ->query('SELECT id, phrase, created_at FROM keywords ORDER BY phrase COLLATE NOCASE ASC')
            ->fetchAll();
    }

    public static function withStats(): array
    {
        $keywords = self::all();

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

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT id, phrase, created_at FROM keywords WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(string $phrase): int
    {
        $stmt = db()->prepare('INSERT INTO keywords (phrase) VALUES (:phrase)');
        $stmt->execute([':phrase' => $phrase]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, string $phrase): bool
    {
        $stmt = db()->prepare('UPDATE keywords SET phrase = :phrase WHERE id = :id');
        $stmt->execute([':id' => $id, ':phrase' => $phrase]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id): bool
    {
        $stmt = db()->prepare('DELETE FROM keywords WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
