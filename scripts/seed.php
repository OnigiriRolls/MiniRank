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

$totalPositions = Seed::run($days, $phrases);

printf("Seeded %d keywords, %d positions\n", count($phrases), $totalPositions);
