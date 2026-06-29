.PHONY: install up down shell wait-db migrate import test phpstan cs-check cs-fix

install:
	@if [ ! -f .env ]; then cp .env.example .env; fi
	docker compose up -d --build
	docker compose exec php composer install
	$(MAKE) wait-db
	docker compose exec php composer migrate
	@echo ""
	@echo "Next steps:"
	@echo "  make import      # import Balíkovna pickup points"
	@echo "  make test        # run PHPUnit"
	@echo "  Adminer: http://localhost:8081"

up:
	docker compose up -d --build

down:
	docker compose down

shell:
	docker compose exec php sh

wait-db:
	@echo "Waiting for database..."
	@until docker compose exec database mariadb-admin ping -h localhost -uapp -papp --silent; do sleep 2; done

migrate:
	docker compose exec php composer migrate

import:
	docker compose exec php composer import:balikovna

test:
	docker compose exec php composer test

phpstan:
	docker compose exec php composer phpstan

cs-check:
	docker compose exec php composer cs-check

cs-fix:
	docker compose exec php composer cs-fix
