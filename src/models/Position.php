<?php

declare(strict_types=1);

class Position
{
    public static function latest(int $keywordId): ?int
    {
        $stmt = db()->prepare(
            'SELECT position FROM positions WHERE keyword_id = :keyword_id ORDER BY date DESC, id DESC LIMIT 1'
        );
        $stmt->execute([':keyword_id' => $keywordId]);
        $row = $stmt->fetch();
        return $row === false ? null : (int) $row['position'];
    }

    public static function onDate(int $keywordId, string $date): ?int
    {
        $stmt = db()->prepare(
            'SELECT position FROM positions WHERE keyword_id = :keyword_id AND date = :date LIMIT 1'
        );
        $stmt->execute([':keyword_id' => $keywordId, ':date' => $date]);
        $row = $stmt->fetch();
        return $row === false ? null : (int) $row['position'];
    }

    public static function create(int $keywordId, string $date, int $position): bool
    {
        $stmt = db()->prepare(
            'INSERT OR IGNORE INTO positions (keyword_id, date, position) VALUES (:keyword_id, :date, :position)'
        );
        $stmt->execute([
            ':keyword_id' => $keywordId,
            ':date' => $date,
            ':position' => $position,
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function createMany(int $keywordId, array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        $pdo = db();
        $stmt = $pdo->prepare(
            'INSERT OR IGNORE INTO positions (keyword_id, date, position) VALUES (:keyword_id, :date, :position)'
        );

        $count = 0;
        $pdo->beginTransaction();
        try {
            foreach ($rows as $row) {
                $stmt->execute([
                    ':keyword_id' => $keywordId,
                    ':date' => $row['date'],
                    ':position' => $row['position'],
                ]);
                $count += $stmt->rowCount();
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        return $count;
    }
}
