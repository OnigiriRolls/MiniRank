<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SanityTest extends TestCase
{
    protected function setUp(): void
    {
        resetDatabase();
    }

    public function testDbIsPdo(): void
    {
        $this->assertInstanceOf(PDO::class, db());
    }

    public function testSchemaExists(): void
    {
        $tables = db()->query(
            "SELECT name FROM sqlite_master WHERE type = 'table'"
        )->fetchAll(PDO::FETCH_COLUMN);

        $this->assertContains('keywords', $tables);
        $this->assertContains('positions', $tables);
    }

    public function testModelRoundTrip(): void
    {
        $keywordId = Keyword::create('sanity check');

        $this->assertSame(1, count(Keyword::all()));
        $this->assertSame('sanity check', Keyword::find($keywordId)['phrase']);

        $this->assertTrue(Position::create($keywordId, '2026-08-17', 12));
        $this->assertSame(12, Position::latest($keywordId));
    }

    public function testResetDatabaseEmptiesTables(): void
    {
        Keyword::create('to be wiped');
        resetDatabase();

        $this->assertSame(0, count(Keyword::all()));
    }
}
