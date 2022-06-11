SHELL := /bin/bash
EXEC_COMMAND ?= docker-compose exec application

init: create_volumes up composer_install create_database migrate_database
create_volumes:
	docker volume create --name=refactoring-examples-postgresql || true
up:
	docker-compose up -d
composer_install:
	${EXEC_COMMAND} composer install
bash:
	${EXEC_COMMAND} bash
create_database:
	${EXEC_COMMAND} bin/console do:database:create --if-not-exists
migrate_database:
	${EXEC_COMMAND} bin/console do:migration:migrate --no-interaction
