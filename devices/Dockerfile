FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git zip unzip curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql bcmath sockets

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www

CMD ["sh", "-c", "composer install && php index.php"]
