FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git zip unzip curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY . /app
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install

CMD ["php", "index.php"]
