SHELL := /bin/bash
EXEC_COMMAND ?= docker-compose -f ../docker-compose.yml exec application

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
test: prepare_test phpunit behat
prepare_test:
	${EXEC_COMMAND} bin/console do:database:create --if-not-exists --env=test
	${EXEC_COMMAND} bin/console do:migration:migrate --no-interaction --env=test
behat:
	${EXEC_COMMAND} vendor/bin/behat --suite=default
	${EXEC_COMMAND} vendor/bin/behat --suite=api
phpunit:
	${EXEC_COMMAND} bin/phpunit
