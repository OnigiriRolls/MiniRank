<?php

declare(strict_types=1);

function trendFromValues(?int $current, ?int $sevenDaysAgo): ?string
{
    if ($current === null || $sevenDaysAgo === null) {
        return null;
    }

    if ($current < $sevenDaysAgo) {
        return 'improved';
    }
    if ($current > $sevenDaysAgo) {
        return 'declined';
    }
    return 'stable';
}

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

    public static function trend(int $keywordId): ?string
    {
        $rows = self::recent($keywordId, 8);
        if (empty($rows)) {
            return null;
        }

        $current = (int) $rows[0]['position'];
        $targetDate = (new DateTimeImmutable('today'))->modify('-7 days')->format('Y-m-d');

        $sevenDaysAgo = null;
        foreach ($rows as $row) {
            if (strcmp($row['date'], $targetDate) <= 0) {
                $sevenDaysAgo = (int) $row['position'];
                break;
            }
        }

        return trendFromValues($current, $sevenDaysAgo);
    }

    public static function recent(int $keywordId, int $limit): array
    {
        $stmt = db()->prepare(
            'SELECT date, position FROM positions WHERE keyword_id = :keyword_id ORDER BY date DESC, id DESC LIMIT :limit'
        );
        $stmt->bindValue(':keyword_id', $keywordId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function history(int $keywordId): array
    {
        return self::recent($keywordId, -1);
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
