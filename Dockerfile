FROM php:8.2-cli

# Installer les dépendances nécessaires et extensions PHP pour PostgreSQL
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer (gestionnaire PHP)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier les fichiers du projet
COPY . .

# Autoriser l'exécution des plugins Composer en root (Symfony Flex, auto-scripts)
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances sans dev et optimiser l'autoloader
RUN composer install --no-dev --optimize-autoloader

# Exposer le port 10000 (ou autre port que tu utilises)
EXPOSE 10000

# Lancer le serveur PHP built-in (change le port si besoin)
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
