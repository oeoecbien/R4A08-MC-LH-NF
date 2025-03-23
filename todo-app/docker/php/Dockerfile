FROM php:8.1-fpm

# Installation des dépendances et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installation de l'extension Redis
RUN pecl install redis && docker-php-ext-enable redis

# Nettoyage
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html