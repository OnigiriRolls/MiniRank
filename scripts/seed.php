<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$days = (int) config('seed_days', 30);

$projects = [
    [
        'name' => "Runner's Hub",
        'url' => 'https://runners.example.com',
        'phrases' => [
            'best running shoes',
            'trail running backpack',
            'running socks for marathon',
        ],
    ],
    [
        'name' => 'Tech & Gaming',
        'url' => 'https://techgaming.example.com',
        'phrases' => [
            'best video game',
            'mechanical keyboard for programmers',
            'ergonomic office chair',
            'dash cam with night vision',
        ],
    ],
    [
        'name' => 'Kitchen & Home',
        'url' => 'https://kitchen.example.com',
        'phrases' => [
            'dutch oven for bread baking',
            'robot vacuum for pet hair',
            'cold brew coffee maker',
            'standing desk converter',
        ],
    ],
];

$summary = Seed::run($days, $projects);

$totalKeywords = 0;
$totalPositions = 0;
foreach ($summary as $project) {
    $totalKeywords += $project['keywords'];
    $totalPositions += $project['positions'];
}

printf("Seeded %d projects, %d keywords, %d positions\n", count($summary), $totalKeywords, $totalPositions);
foreach ($summary as $project) {
    printf("  %s: %d keywords, %d positions\n", $project['name'], $project['keywords'], $project['positions']);
}
