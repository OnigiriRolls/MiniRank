<?php

return [
    'site_name' => 'MiniRank',
    'db_path' => getenv('MINIRANK_DB_PATH') ?: __DIR__ . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'minirank.sqlite',
    'base_url' => '',
    'seed_days' => 30,
    'seed_username' => 'alice1',
];
