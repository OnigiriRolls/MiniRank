# MiniRank

A simulated keyword position tracker (no real search engines involved).

## Requirements

- PHP 8+ with the `pdo_sqlite` extension (verify with `php -m`)
- Docker Desktop with the Docker Compose plugin (only for the Docker quick start)

## Quick start (without Docker)

```
php -S localhost:8000 -t public
```

Open <http://localhost:8000>

## Quick start with Docker

(See Seeding section before running)

```
docker compose up --build
```

Open <http://localhost:8000>

The SQLite database lives in `db/minirank.sqlite` on your machine (bind-mounted
into the container), so your data persists across restarts.

## Database

SQLite - The schema is auto-created on the first run.

## Seeding script

It requires a registered user.
In config.php, you need to set 'seed_username' to a registered one and
'seed_days' to the number of days of history to generate.

If you run the app through Docker, do this configuration **before** building the
image (`docker compose up --build`), so it is baked in. Use the name you set, in config, when registering a user.

Without Docker, from the root folder, run:
```
php scripts/seed.php
```

With Docker, run:
```
docker compose exec app php scripts/seed.php
```

## Tests
 
Without Docker, from the root folder, run:
```
composer test
```
or test just one file using
```
vendor\bin\phpunit tests\SeedingTest.php
vendor\bin\phpunit tests\TrendTest.php
```

With Docker, run:
```
docker compose exec app composer test
```