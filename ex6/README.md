# Exercice 6 - Docker Compose

## Objectifs

Dans cet exercice, nous avons configuré une application multi-containers avec Docker Compose. L'objectif était de :

1. Créer un fichier `docker-compose.yml` permettant d'orchestrer plusieurs services Docker.
2. Utiliser différentes images Docker pour faire fonctionner les services nécessaires à l'application : PHP, Nginx (ou Apache), MariaDB, PostgreSQL et Redis.
3. Installer les dépendances PHP avec Composer dans le conteneur PHP.
4. Assurer la persistance des données pour les bases de données (MariaDB, PostgreSQL) en utilisant des volumes Docker.

## Structure du projet

Le projet contient les éléments suivants :

- **Fichier `docker-compose.yml`** : Ce fichier configure les services et permet de les exécuter et de les orchestrer.
- **Code de l'application PHP** : Le dossier `app` contient le code source de l'application PHP, qui est monté dans le conteneur PHP et le serveur Nginx.
- **Fichier `nginx.conf`** : Configuration pour le serveur Nginx afin de servir l'application PHP correctement.
- **Fichiers de données de bases de données** : Utilisation de volumes pour persister les données des bases de données MariaDB et PostgreSQL.

## Prérequis

Avant de commencer, assurez-vous d'avoir les outils suivants installés sur votre machine :

- [Docker](https://www.docker.com/products/docker-desktop) (avec Docker Compose inclus)
- [Composer](https://getcomposer.org/) (pour la gestion des dépendances PHP)

## Configuration

1. **PHP avec Composer** : Le service PHP utilise l'image officielle `php:8.1-fpm`. Dans le conteneur, Composer est utilisé pour installer les dépendances PHP nécessaires. La commande suivante est exécutée à chaque démarrage du conteneur PHP :

   ```bash
   composer install && php-fpm
   ```

2. **Nginx** : Nginx sert de serveur web pour l'application PHP. Il utilise le fichier de configuration `nginx.conf` pour configurer le serveur et pointe vers le dossier `app` contenant le code PHP.

3. **Bases de données** : 
   - MariaDB et PostgreSQL sont utilisés pour gérer les données de l'application. Les mots de passe et les noms de base de données sont définis via des variables d'environnement dans le fichier `docker-compose.yml`.
   - Les données sont persistées grâce aux volumes `mariadb-data` et `postgres-data`.

4. **Redis** : Redis est utilisé comme service de cache en mémoire pour améliorer la performance de l'application.

## Lancer l'application

### 1. Cloner ce dépôt

Clonez ce dépôt sur votre machine locale si ce n'est pas déjà fait.

```bash
git clone https://votre-depot-url.git
cd ex6
```

### 2. Lancer les services Docker

Une fois le fichier `docker-compose.yml` en place, vous pouvez démarrer tous les services en utilisant Docker Compose. Exécutez la commande suivante dans le répertoire du projet :

```bash
docker-compose up -d
```

Cela va télécharger les images Docker nécessaires, créer les conteneurs et démarrer les services en arrière-plan.

### 3. Accéder à l'application

L'application PHP sera accessible via le serveur Nginx sur le port 8080 :

```bash
http://localhost:8080
```

### 4. Vérifier les logs des services

Si vous souhaitez vérifier les logs d'un service spécifique, utilisez la commande suivante :

```bash
docker-compose logs <nom_du_service>
```

Exemple pour voir les logs de Nginx :

```bash
docker-compose logs nginx
```

### 5. Arrêter les services

Pour arrêter tous les services, utilisez :

```bash
docker-compose down
```

Cela arrêtera tous les conteneurs et supprimera les réseaux. Les volumes de données resteront persistant.

## Structure du fichier `docker-compose.yml`

Voici un aperçu de la configuration dans le fichier `docker-compose.yml` :

```yaml
version: '3'
services:
  web:
    image: php:8.1-fpm
    container_name: php-container
    volumes:
      - ./app:/var/www/html
    networks:
      - app-network
    depends_on:
      - mariadb
      - redis
    working_dir: /var/www/html
    command: bash -c "composer install && php-fpm"
  
  nginx:
    image: nginx:latest
    container_name: nginx-container
    volumes:
      - ./app:/var/www/html
      - ./nginx.conf:/etc/nginx/nginx.conf
    ports:
      - "8080:80"
    depends_on:
      - web
    networks:
      - app-network

  mariadb:
    image: mariadb:10.5
    container_name: mariadb-container
    environment:
      MYSQL_ROOT_PASSWORD: root
      MARIADB_DATABASE: app_db
    volumes:
      - mariadb-data:/var/lib/mysql
    networks:
      - app-network

  postgres:
    image: postgres:13
    container_name: postgres-container
    environment:
      POSTGRES_PASSWORD: root
      POSTGRES_DB: app_db
    volumes:
      - postgres-data:/var/lib/postgresql/data
    networks:
      - app-network

  redis:
    image: redis:latest
    container_name: redis-container
    networks:
      - app-network

networks:
  app-network:
    driver: bridge

volumes:
  mariadb-data:
    driver: local
  postgres-data:
    driver: local
```

## Conclusion

Cet exercice vous a permis de configurer une application multi-services avec Docker Compose. Vous avez orchestré plusieurs conteneurs avec des bases de données, un serveur PHP, un serveur web Nginx et Redis. Vous avez également appris à persister des données avec Docker et à gérer les dépendances PHP avec Composer.