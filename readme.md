# FitSync

FitSync is a microservice-based backend system designed to demonstrate strong backend engineering skills with **PHP 8.4**, Go, PostgreSQL, Redis, RabbitMQ, and Docker.

The system simulates data ingestion and processing for fitness trackers and smart scales — similar to real-world HealthTech backend pipelines.

This project is designed to showcase experience required for modern backend roles (PHP 8.x, Go, microservices, queues, Docker, CI/CD, clean architecture).

---

## Tech Stack

### Backend Services
- **Gateway API** — PHP 8.4 (Symfony, served via PHP built-in server)
- **Devices Service** — PHP 8.4 (raw PHP or lightweight framework)
- **Notifier Service** — PHP 8.4 (RabbitMQ worker)
- **Analytics Service** — Go (data aggregation & async processing)

### Infrastructure
- **PostgreSQL 15**
- **Redis 7**
- **RabbitMQ 3**
- **MinIO** — S3-compatible storage
- **Docker & docker-compose**
- **Makefile automation**

---

## Quick Start

### 1. Clone the repository

```bash
git clone https://github.com/comradeos/fitsync.git
cd fitsync
```

### 2. Start all services

```bash
make up
```

### 3. View logs

```bash
make logs
```

### 4. Connect to containers

```bash
make sh-gateway
make sh-devices
make sh-notifier
make sh-analytics
```

---

## Developer Commands

| Command | Description |
|--------|-------------|
| make up | Build & start all containers |
| make down | Stop and remove containers |
| make restart | Restart environment |
| make logs | Follow logs |
| make sh-gateway | Shell into Gateway |
| make sh-devices | Shell into Devices |
| make sh-notifier | Shell into Notifier |
| make sh-analytics | Shell into Analytics |
| make composer-gateway | Install Gateway PHP deps |
| make composer-devices | Install Devices PHP deps |
| make composer-notifier | Install Notifier PHP deps |
| make go-build | Build Go analytics |
| make go-test | Run Go tests |
| make init | Full setup for fresh clone |
| make fresh | Full reset |

---

## Ports

| Service | Port |
|--------|------|
| Gateway API | 8080 |
| PostgreSQL | 5432 |
| Redis | 6379 |
| RabbitMQ | 5672 |
| RabbitMQ UI | 15672 |
| MinIO API | 9000 |
| MinIO Console | 9001 |

---

## Structure

```
fitsync/
├── docker-compose.yml
├── Makefile
├── README.md
├── services/
│   ├── gateway/
│   ├── devices/
│   ├── notifier/
│   └── analytics/
```

---

# ⚙️ Deployment Guide (Fresh Clone)

### 1. Clone

```bash
git clone https://github.com/comradeos/fitsync.git
cd fitsync
```

### 2. Check Docker

```bash
docker -v
docker compose version
```

### 3. Start stack

```bash
make up
```

### 4. Install dependencies

```bash
make composer-gateway
make composer-devices
make composer-notifier
```

### 5. Run migrations (optional)

```bash
docker compose exec gateway php bin/console doctrine:migrations:migrate
```

### 6. Run tests

PHP:

```bash
docker compose exec gateway vendor/bin/phpunit
```

Go:

```bash
docker compose exec analytics go test ./...
```
