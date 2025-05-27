# Dockerfile pour déploiement Symfony sur Render
FROM php:8.2-cli

# Installe les extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
