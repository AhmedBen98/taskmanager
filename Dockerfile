FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Désactive les scripts Symfony qui posent problème sur Renders
RUN composer config --no-plugins allow-plugins.symfony/flex true
RUN composer config --no-plugins allow-plugins.symfony/runtime true
RUN composer config --no-plugins allow-plugins.symfony/scripts false
RUN composer install --no-dev --optimize-autoloader

CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]