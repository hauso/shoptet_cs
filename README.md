# PHP Logistics

Symfony-like PHP 8.4 aplikace pro import výdejních míst.

## Minimální požadavky

- Docker
- Docker Compose
- PHP 8.4 iba pri spúšťaní mimo Docker
- make (pro snažší instalaci)

Odporúčaný runtime projektu je Docker.

## Rychlá instalace

```bash
make install
```

`make install` vytvoří `.env` z `.env.example`, pokud ještě neexistuje, spustí kontejnery, nainstaluje Composer dependencies a spustí databázovou migraci.

## Manualní instalace

```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php composer migrate
cp .env.example .env
```

## Composer Docker wrappery:

```bash
composer docker:migrate
composer docker:import:balikovna
composer docker:test
composer docker:phpstan
composer docker:cs-check
composer docker:cs-fix
```

## Základní příkazy v PHP kontejneru

```bash
composer install
composer migrate
composer import:balikovna
composer test
composer phpstan
composer cs-check
composer cs-fix
```

## Adminer

Adminer je dostupný pouze jako lokální/dev pomocný kontejner.

- URL: `http://localhost:8081`
- Server: `database`
- User: `app`
- Password: `app`
- Database: `app`
