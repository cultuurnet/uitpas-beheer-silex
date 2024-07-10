.PHONY: up down build install install-npm bash test init

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

bash:
	docker exec -it php.balie bash

test:
	docker exec -it php.balie composer test

init: install install-npm