SHELL := /bin/bash
COMPOSE_COMMAND ?= docker compose
EXEC_COMMAND ?= ${COMPOSE_COMMAND} exec application

init: create_volumes up composer_install create_database migrate_database
create_volumes:
	docker volume create --name=refactoring-examples-postgresql || true
rebuild:
	${COMPOSE_COMMAND} build --force-rm --no-cache
build:
	${COMPOSE_COMMAND} build
up:
	${COMPOSE_COMMAND} up -d
down:
	${COMPOSE_COMMAND} down
composer_install:
	${EXEC_COMMAND} composer install
bash:
	${EXEC_COMMAND} bash
create_database:
	${EXEC_COMMAND} bin/console do:database:create --if-not-exists
migrate_database:
	${EXEC_COMMAND} bin/console do:migration:migrate --no-interaction
test: prepare_test phpunit behat
prepare_test:
	${EXEC_COMMAND} bin/console do:database:create --if-not-exists --env=test
	${EXEC_COMMAND} bin/console do:migration:migrate --no-interaction --env=test
behat:
	${EXEC_COMMAND} vendor/bin/behat --suite=default
	${EXEC_COMMAND} vendor/bin/behat --suite=api
phpunit:
	${EXEC_COMMAND} bin/phpunit
