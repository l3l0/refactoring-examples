FROM php:8.2-apache-bullseye as refactoring-examples-base

RUN apt-get -y update && apt-get -y upgrade && apt-get -y install git bash libpq-dev libzip-dev unzip
RUN docker-php-ext-install pdo pdo_pgsql
RUN groupadd --gid 1000 refactoring-examples \
  && useradd --uid 1000 --gid refactoring-examples --shell /bin/bash --create-home refactoring-examples

RUN docker-php-ext-install opcache zip

COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer

WORKDIR /home/refactoring-examples/project
