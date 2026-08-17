<?php

declare(strict_types=1);

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = :name");
    $stmt->execute([':name' => $table]);
    return $stmt->fetch() !== false;
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->query("PRAGMA table_info(" . $table . ")");
    foreach ($stmt->fetchAll() as $col) {
        if ($col['name'] === $column) {
            return true;
        }
    }
    return false;
}

function createSchema(PDO $pdo): void
{
    $needsProjectsMigration = tableExists($pdo, 'projects') && !columnExists($pdo, 'projects', 'user_id');
    $needsKeywordsMigration = tableExists($pdo, 'keywords') && !columnExists($pdo, 'keywords', 'project_id');

    if ($needsProjectsMigration || $needsKeywordsMigration) {
        $pdo->exec('DROP TABLE positions');
        $pdo->exec('DROP TABLE keywords');
        $pdo->exec('DROP TABLE projects');
    }

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            username      TEXT NOT NULL UNIQUE COLLATE NOCASE,
            password_hash TEXT NOT NULL,
            created_at    TEXT NOT NULL DEFAULT (datetime(\'now\'))
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS projects (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id    INTEGER NOT NULL,
            name       TEXT NOT NULL,
            url        TEXT,
            created_at TEXT NOT NULL DEFAULT (datetime(\'now\')),
            UNIQUE(user_id, name),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS keywords (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL,
            phrase     TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime(\'now\')),
            UNIQUE(project_id, phrase),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
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
