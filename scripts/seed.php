<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$days = (int) config('seed_days', 30);

$phrases = [
    'best running shoes',
    'best video game',
    'dutch oven for bread baking',
    'mechanical keyboard for programmers',
    'robot vacuum for pet hair',
    'dash cam with night vision',
    'ergonomic office chair',
    'cold brew coffee maker',
    'trail running backpack',
    'standing desk converter',
];

db()->exec('DELETE FROM keywords');

$today = new DateTimeImmutable('today');
$startDate = $today->modify('-' . ($days - 1) . ' days');

$totalPositions = 0;

foreach ($phrases as $phrase) {
    $keywordId = Keyword::create($phrase);

    $position = random_int(1, 100);
    $rows = [];

    for ($i = 0; $i < $days; $i++) {
        $delta = random_int(-3, 3);
        if ($i > 0 && random_int(1, 3) === 1) {
            $delta = random_int(-8, 8);
        }
        $position = max(1, min(100, $position + $delta));

        $rows[] = [
            'date' => $startDate->modify("+$i days")->format('Y-m-d'),
            'position' => $position,
        ];
    }

    $totalPositions += Position::createMany($keywordId, $rows);
}

printf("Seeded %d keywords, %d positions\n", count($phrases), $totalPositions);
