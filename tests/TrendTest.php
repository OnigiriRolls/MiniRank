<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TrendTest extends TestCase
{
    private int $projectId;

    protected function setUp(): void
    {
        resetDatabase();
        class_exists(Position::class);
        $this->projectId = Project::create('Trend test project');
    }

    public function testEmptyHistoryReturnsNull(): void
    {
        $keywordId = Keyword::create($this->projectId, 'empty history');

        $this->assertNull(Position::trend($keywordId));
    }

    public function testHistoryTooShortReturnsNull(): void
    {
        $keywordId = Keyword::create($this->projectId, 'short history');

        $today = new DateTimeImmutable('today');
        for ($i = 0; $i < 7; $i++) {
            $date = $today->modify("-{$i} days")->format('Y-m-d');
            Position::create($keywordId, $date, 10);
        }

        $this->assertNull(Position::trend($keywordId));
    }

    public function testImprovedWhenCurrentBelowSevenDaysAgo(): void
    {
        $this->assertSame('improved', trendFromValues(9, 15));
    }

    public function testDeclinedWhenCurrentAboveSevenDaysAgo(): void
    {
        $this->assertSame('declined', trendFromValues(22, 15));
    }

    public function testStableWhenCurrentEqualsSevenDaysAgo(): void
    {
        $this->assertSame('stable', trendFromValues(15, 15));
    }

    public function testTrendWiredThroughPosition(): void
    {
        $keywordId = Keyword::create($this->projectId, 'wiring check');

        $today = new DateTimeImmutable('today');
        $rows = [
            ['date' => $today->format('Y-m-d'), 'position' => 9],
            ['date' => $today->modify('-1 days')->format('Y-m-d'), 'position' => 9],
            ['date' => $today->modify('-2 days')->format('Y-m-d'), 'position' => 11],
            ['date' => $today->modify('-3 days')->format('Y-m-d'), 'position' => 10],
            ['date' => $today->modify('-4 days')->format('Y-m-d'), 'position' => 12],
            ['date' => $today->modify('-5 days')->format('Y-m-d'), 'position' => 15],
            ['date' => $today->modify('-6 days')->format('Y-m-d'), 'position' => 15],
            ['date' => $today->modify('-7 days')->format('Y-m-d'), 'position' => 15],
        ];

        $this->assertSame(8, Position::createMany($keywordId, $rows));
        $this->assertSame('improved', Position::trend($keywordId));
    }
}