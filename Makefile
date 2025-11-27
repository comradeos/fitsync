.PHONY: up down restart logs \
        sh-gateway sh-devices sh-notifier sh-analytics \
        composer-gateway composer-devices composer-notifier \
        go-build go-test init fresh

up:
	docker compose up -d --build

down:
	docker compose down

restart:
	docker compose down && docker compose up -d --build

logs:
	docker compose logs -f

sh-gateway:
	docker compose exec gateway bash

sh-devices:
	docker compose exec devices bash

sh-notifier:
	docker compose exec notifier bash

sh-analytics:
	docker compose exec analytics sh

composer-gateway:
	docker compose exec gateway composer install

composer-devices:
	docker compose exec devices composer install || true

composer-notifier:
	docker compose exec notifier composer install || true

go-build:
	docker compose exec analytics go build -o analytics

go-test:
	docker compose exec analytics go test ./...

init:
	make up
	sleep 3
	make composer-gateway
	make composer-devices
	make composer-notifier
	make go-build

fresh:
	docker compose down -v
	make init
