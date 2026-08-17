<?php

declare(strict_types=1);

class Seed
{
    public static function run(int $days, array $projects, int $userId): array
    {
        db()->exec('DELETE FROM projects');

        $today = new DateTimeImmutable('today');
        $startDate = $today->modify('-' . ($days - 1) . ' days');

        $summary = [];

        foreach ($projects as $project) {
            $name = (string) ($project['name'] ?? 'Unnamed project');
            $url = !empty($project['url']) ? (string) $project['url'] : null;
            $phrases = (array) ($project['phrases'] ?? []);

            $projectId = Project::create($userId, $name, $url);

            $keywordCount = 0;
            $positionCount = 0;

            foreach ($phrases as $phrase) {
                $keywordId = Keyword::create($projectId, (string) $phrase);
                $keywordCount++;

                $position = simulatePosition(null);
                $rows = [];

                for ($i = 0; $i < $days; $i++) {
                    $position = simulatePosition($position);

                    $rows[] = [
                        'date' => $startDate->modify("+$i days")->format('Y-m-d'),
                        'position' => $position,
                    ];
                }

                $positionCount += Position::createMany($keywordId, $rows);
            }

            $summary[] = [
                'name' => $name,
                'keywords' => $keywordCount,
                'positions' => $positionCount,
            ];
        }

        return $summary;
    }
}
