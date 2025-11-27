# FitSync

FitSync is a microservice-based backend system designed to demonstrate strong backend engineering skills with PHP 8.4, Go, PostgreSQL, Redis, RabbitMQ, and Docker.
The system simulates data ingestion and processing for fitness trackers and smart scales — similar to real-world HealthTech backend services.

This project is tailored to showcase experience relevant for senior backend roles (PHP / Go / microservices / queues / cloud patterns).

---

## Tech Stack

### Backend Services
- **Gateway API** — PHP 8.3 (Symfony)
- **Devices Service** — PHP 8.3 (raw PHP or lightweight framework)
- **Notifier Service** — PHP 8.3 (RabbitMQ worker)
- **Analytics Service** — Go (data aggregation and processing)

### Infrastructure
- **PostgreSQL 15** — main database
- **Redis 7** — caching and auxiliary storage
- **RabbitMQ 3** — event-driven communication
- **MinIO** — S3-compatible storage for file operations
- **Docker + docker-compose** — full system orchestration
- **Makefile** — automation for developer commands

---

## Quick Start

### 1. Clone the repository
```
git clone https://github.com/comradeos/fitsync.git
cd fitsync
```

### 2. Start the entire environment
```
make up
```

### 3. View logs
```
make logs
```

### 4. Connect to service containers
```
make sh-gateway
make sh-devices
make sh-notifier
make sh-analytics
```

---

## Developer Commands

| Command | Description |
|--------|-------------|
| make up | Build and start all containers |
| make down | Stop and remove containers |
| make restart | Restart the whole environment |
| make logs | View logs |
| make sh-gateway | Shell into Gateway service |
| make sh-devices | Shell into Devices service |
| make sh-notifier | Shell into Notifier service |
| make sh-analytics | Shell into Analytics service |

---

## Service Ports

| Service | Port |
|--------|------|
| PostgreSQL | 5432 |
| Redis | 6379 |
| RabbitMQ | 5672 |
| RabbitMQ UI | 15672 |
| MinIO API | 9000 |
| MinIO Console | 9001 |

---

## Directory Structure

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

# Deployment Guide (Fresh Clone Setup)

### 1. Clone the repository
```
git clone https://github.com/<yourname>/fitsync.git
cd fitsync
```

### 2. Ensure Docker is installed
```
docker -v
docker compose version
```

### 3. Start all services
```
make up
```

### 4. Check logs
```
make logs
```

### 5. Install dependencies inside containers
```
make sh-gateway && composer install
make sh-devices && composer install
make sh-notifier && composer install
```

### 6. Run migrations (optional)
```
php bin/console doctrine:migrations:migrate
```

### 7. Run tests
PHP:
```
docker compose exec gateway vendor/bin/phpunit
```

Go:
```
docker compose exec analytics go test ./...
```

