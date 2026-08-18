<?php

declare(strict_types=1);

class RefreshController
{
    public function refresh(int $projectId): void
    {
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');

        $positions = [];
        $trends = [];
        foreach (Keyword::allForProject($projectId) as $keyword) {
            $id = (int) $keyword['id'];
            $existing = Position::onDate($id, $today);

            if ($existing === null) {
                $position = simulatePosition(Position::latest($id));
                Position::create($id, $today, $position);
            } else {
                $position = $existing;
            }

            $positions[$id] = $position;
            $trends[$id] = Position::trend($id);
        }

        header('Content-Type: application/json');
        echo json_encode(['date' => $today, 'positions' => $positions, 'trends' => $trends]);
        exit;
    }
}