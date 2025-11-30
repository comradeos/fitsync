FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git zip unzip curl \
    && docker-php-ext-install pcntl

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

WORKDIR /var/www

COPY . .

CMD ["php", "worker.php"]
