SHELL := /bin/bash
COMPOSE_COMMAND ?= docker compose
EXEC_COMMAND ?= ${COMPOSE_COMMAND} exec application

init: create_volumes up composer_install
create_volumes:
	docker volume create --name=refactoring-examples-postgresql || true
rebuild:
	${COMPOSE_COMMAND} build --force-rm --no-cache
build:
	${COMPOSE_COMMAND} build
up:
	${COMPOSE_COMMAND} up -d
down:
	${COMPOSE_COMMAND} up -d
composer_install:
	${EXEC_COMMAND} composer install
bash:
	${EXEC_COMMAND} bash
