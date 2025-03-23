# Définir une variable PHP_VERSION avec une valeur par défaut
ARG PHP_VERSION="7.4"

# Utiliser cette version de PHP pour créer l'image
FROM php:$PHP_VERSION-apache

# Copier le fichier PHP
COPY index.php /var/www/html/

# Exposer le port 80
EXPOSE 80