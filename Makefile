.DEFAULT_GOAL := help

.PHONY: help \
        up down stop start restart logs \
        sh-gateway sh-devices sh-notifier sh-analytics \
        composer-gateway composer-devices composer-notifier composer-all \
        analytics-build analytics-test \
        init fresh \
        backup-postgres restore-postgres \
        backup-minio restore-minio

help:
	@echo ""
	@echo "Available Make commands:"
	@echo ""
	@echo "  up                 Build and start all Docker services"
	@echo "  down               Stop and remove all Docker services including volumes"
	@echo "  stop               Stop all running containers without removing them"
	@echo "  start              Start stopped containers"
	@echo "  restart            Restart Docker services by rebuilding them"
	@echo "  logs               Show logs of all services"
	@echo ""
	@echo "  sh-gateway         Open shell inside the gateway container"
	@echo "  sh-devices         Open shell inside the devices container"
	@echo "  sh-notifier        Open shell inside the notifier container"
	@echo "  sh-analytics       Open shell inside the analytics container"
	@echo ""
	@echo "  composer-gateway   Run composer install inside gateway"
	@echo "  composer-devices   Run composer install inside devices"
	@echo "  composer-notifier  Run composer install inside notifier"
	@echo "  composer-all       Run composer install for all PHP services"
	@echo ""
	@echo "  analytics-build    Build the analytics Go binary"
	@echo "  analytics-test     Run Go tests inside analytics"
	@echo ""
	@echo "  init               Initialize project: build, install dependencies, build Go"
	@echo "  fresh              Full reset: remove volumes and reinitialize"
	@echo ""
	@echo "  backup-postgres    Create a backup of the PostgreSQL volume"
	@echo "  restore-postgres   Restore the PostgreSQL volume from backup"
	@echo "  backup-minio       Create a backup of the MinIO volume"
	@echo "  restore-minio      Restore the MinIO volume from backup"
	@echo ""

up:
	docker compose up -d --build

down:
	docker compose down -v

stop:
	docker compose stop

start:
	docker compose start

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

composer-all: composer-gateway composer-devices composer-notifier

analytics-build:
	docker compose exec analytics go build -o analytics

analytics-test:
	docker compose exec analytics go test ./...

init:
	make up
	sleep 3
	make composer-all
	make analytics-build

fresh:
	docker compose down -v
	make init

backup-postgres:
	docker compose stop postgres
	mkdir -p backup
	docker run --rm -v fitsync_pgdata:/data -v $(PWD)/backup:/backup busybox \
		sh -c "tar czf - /data | split -b 1024m - /backup/pgdata.tar.gz.part-"
	docker compose start postgres

restore-postgres:
	docker compose stop postgres
	docker run --rm -v fitsync_pgdata:/data busybox sh -c "rm -rf /data/*"
	cat backup/pgdata.tar.gz.part-* \
		| docker run --rm -i -v fitsync_pgdata:/data busybox tar xzf - -C /
	docker compose up -d postgres

backup-minio:
	docker run --rm -v minio:/data -v $(PWD)/backup:/backup busybox tar czf /backup/minio.tar.gz /data

restore-minio:
	docker compose stop minio
	docker run --rm -v minio:/data -v $(PWD)/backup:/backup busybox sh -c "rm -rf /data/* && tar xzf /backup/minio.tar.gz -C /"
	docker compose start minio

migrations-diff:
	docker compose exec gateway php bin/console doctrine:migrations:diff

migrations-migrate:
	docker compose exec gateway php bin/console doctrine:migrations:migrate --no-interaction

migrations-status:
	docker compose exec gateway php bin/console doctrine:migrations:status
