<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

db()->exec('DELETE FROM positions
WHERE id IN (30, 60, 90, 120, 150, 180, 210, 240, 270 , 300);');