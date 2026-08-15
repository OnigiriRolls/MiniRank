<?php

declare(strict_types=1);

class RefreshController
{
    public function refresh(): void
    {
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');

        $positions = [];
        foreach (Keyword::all() as $keyword) {
            $id = (int) $keyword['id'];
            $existing = Position::onDate($id, $today);

            if ($existing === null) {
                $position = simulatePosition(Position::latest($id));
                Position::create($id, $today, $position);
            } else {
                $position = $existing;
            }

            $positions[$id] = $position;
        }

        header('Content-Type: application/json');
        echo json_encode(['date' => $today, 'positions' => $positions]);
        exit;
    }
}
