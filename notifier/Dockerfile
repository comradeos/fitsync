FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git zip unzip curl \
    && docker-php-ext-install pcntl

COPY . /app
WORKDIR /app

CMD ["php", "worker.php"]
