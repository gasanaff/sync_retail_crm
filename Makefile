ifndef ENV
    ENV = dev
endif
ifeq ($(ENV), dev)
	COMPOSER = composer install
else ifeq ($(ENV), test)
	COMPOSER = composer install
else
	COMPOSER = /usr/local/bin/composer install --no-interaction --optimize-autoloader --no-dev --prefer-dist
endif

up:
	@echo "==> Up $(ENV)"
	docker-compose up -d --build --force-recreate
down:
	@echo "==> Down $(ENV)"
	docker-compose down
before_up:
	@echo "==> Before Up $(ENV)"
	cp .env.$(ENV).dist .env
	cp docker-compose.$(ENV).override.yaml docker-compose.override.yaml
after_up:
	@echo "==> After Up $(ENV)"
	docker-compose exec -T php $(COMPOSER)
	docker-compose exec -T php bin/console doctrine:database:create --if-not-exists
	docker-compose exec -T php ./bin/console do:mi:mi --no-interaction
fixer:
	@echo "==> Fixer"
	docker-compose run --rm --no-deps php vendor/bin/php-cs-fixer fix

test:
	@echo "==> Test"
	docker-compose exec -T php vendor/bin/php-cs-fixer fix --dry-run --config=.php_cs.dist --using-cache=no -v
	docker-compose exec -T php bin/console doctrine:database:create --if-not-exists --env=test
	docker-compose exec -T php bin/console doctrine:migrations:migrate first --env=test --no-interaction
	docker-compose exec -T php bin/console doctrine:migrations:migrate --env=test --no-interaction
	docker-compose exec -T php bin/console doctrine:schema:validate
	docker-compose exec -T php vendor/bin/codecept run