.PHONY: up down build install install-npm install-githooks bash init

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose up --build

install:
	docker exec -it php.balie composer install

install-npm:
	docker exec -it php.balie npm install

install-githooks:
	docker exec -it php.balie ./vendor/bin/phpstan githooks

bash:
	docker exec -it php.balie bash

ci:
	docker exec -it php.balie composer phpstan

init: install install-npm install-githooks