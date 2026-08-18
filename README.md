# MiniRank

A simulated keyword position tracker (no real search engines involved).

## Requirements

- PHP 8+ with the `pdo_sqlite` extension (verify with `php -m`)

## Quick start

```
php -S localhost:8000 -t public
```

Open <http://localhost:8000>

## Database

SQLite - The schema is auto-created on the first run.

## Seeding script

It requires a registered user.
In config.php, you need to set 'seed_username' to a registered one. 

From the root folder, run:
```
php scripts/seed.php
```

## Tests
 
From the root folder, run:
```
composer test
```
or test just one file using
```
vendor\bin\phpunit tests\SeedingTest.php
vendor\bin\phpunit tests\TrendTest.php
```