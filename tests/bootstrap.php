<?php

declare(strict_types=1);

putenv('MINIRANK_DB_PATH=:memory:');

require dirname(__DIR__) . '/src/bootstrap.php';

function resetDatabase(): void
{
    db()->exec('DELETE FROM positions');
    db()->exec('DELETE FROM keywords');
}
