Refactoring examples
=====================

Project which should show examples of how we can refactor some parts of the application. Main target will be internal and external workshops.

Install dev env
========

Project use Docker in version >= 24.00 and docker-composer version >= 2.17. Please check [documentation](https://docs.docker.com/engine/install/) to learn how to install docker and docker-compose in your system.

After installation you can use `make` command to install project. To install it manully please check `Makefile`

```
docker compose build
docker compose up -d
docker compose exec application composer install # to install composer deps
```

