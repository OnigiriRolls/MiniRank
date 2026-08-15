<?php

declare(strict_types=1);

function createSchema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS keywords (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            phrase     TEXT NOT NULL UNIQUE,
            created_at TEXT NOT NULL DEFAULT (datetime(\'now\'))
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS positions (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            keyword_id INTEGER NOT NULL,
            date       TEXT NOT NULL,
            position   INTEGER NOT NULL,
            UNIQUE(keyword_id, date),
            FOREIGN KEY(keyword_id) REFERENCES keywords(id) ON DELETE CASCADE
        )'
    );
}
