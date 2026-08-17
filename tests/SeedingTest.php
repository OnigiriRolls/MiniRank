<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SeedingTest extends TestCase
{
    protected function setUp(): void
    {
        resetDatabase();
    }

    public function testNullPreviousAlwaysInRange(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $position = simulatePosition(null);
            $this->assertGreaterThanOrEqual(1, $position);
            $this->assertLessThanOrEqual(100, $position);
        }
    }

    public function testFloorClampNeverBelowOne(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $this->assertGreaterThanOrEqual(1, simulatePosition(1));
        }
    }

    public function testCeilingClampNeverAboveOneHundred(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $this->assertLessThanOrEqual(100, simulatePosition(100));
        }
    }

    public function testMidRangeAlwaysInRange(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $position = simulatePosition(50);
            $this->assertGreaterThanOrEqual(1, $position);
            $this->assertLessThanOrEqual(100, $position);
        }
    }

    public function testSeedCreatesConsecutiveDailyHistory(): void
    {
        $days = 30;
        $total = Seed::run($days, ['end to end']);

        $keywordId = (int) Keyword::all()[0]['id'];
        $history = Position::history($keywordId);

        $this->assertSame($days, $total);
        $this->assertCount($days, $history);

        $today = new DateTimeImmutable('today');
        $expectedDates = [];
        for ($i = 0; $i < $days; $i++) {
            $expectedDates[] = $today->modify("-{$i} days")->format('Y-m-d');
        }

        $actualDates = array_map(static fn (array $row): string => $row['date'], $history);
        $this->assertSame($expectedDates, $actualDates);

        foreach ($history as $row) {
            $this->assertGreaterThanOrEqual(1, (int) $row['position']);
            $this->assertLessThanOrEqual(100, (int) $row['position']);
        }
    }
}