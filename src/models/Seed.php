<?php

declare(strict_types=1);

class Seed
{
    public static function run(int $days, array $phrases): int
    {
        db()->exec('DELETE FROM keywords');

        $today = new DateTimeImmutable('today');
        $startDate = $today->modify('-' . ($days - 1) . ' days');

        $totalPositions = 0;

        foreach ($phrases as $phrase) {
            $keywordId = Keyword::create($phrase);

            $position = simulatePosition(null);
            $rows = [];

            for ($i = 0; $i < $days; $i++) {
                $position = simulatePosition($position);

                $rows[] = [
                    'date' => $startDate->modify("+$i days")->format('Y-m-d'),
                    'position' => $position,
                ];
            }

            $totalPositions += Position::createMany($keywordId, $rows);
        }

        return $totalPositions;
    }
}