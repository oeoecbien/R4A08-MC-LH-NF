# Exercice 5 - Docker : Application PHP avec Apache et MariaDB

Cet exercice consiste à créer une application PHP simple dans un conteneur Docker, à la packager et à l'orchestrer à l'aide de GitLab CI et Docker Compose.

## Objectifs

1. Packager une application PHP dans une image Docker.
2. Créer un pipeline GitLab CI pour générer et pousser l'image Docker sur le registre GitLab.
3. Orchestrer l'application et la base de données avec Docker Compose.

---

## Partie 1 : Créer l'application PHP et le Dockerfile

### Étape 1 : Créer l'application PHP

Nous avons créé un fichier `index.php` qui utilise la fonction `phpinfo()` pour afficher les informations sur la configuration PHP.

```php
<?php
phpinfo();
?>
```

### Étape 2 : Dockerfile pour PHP

Ensuite, nous avons créé un `Dockerfile` pour générer une image Docker qui contient PHP et Apache. Nous avons utilisé l'image officielle `php:8.1-apache` pour simplifier la configuration.

```Dockerfile
FROM php:8.1-apache
COPY index.php /var/www/html/
```

Le fichier `index.php` est copié dans le répertoire `/var/www/html/` du conteneur Apache.

### Étape 3 : Tester en local

Nous avons testé la création de l'image et la vérification de son bon fonctionnement en local avec les commandes suivantes :

```bash
docker build -t php-apache-app .
docker run -p 8081:80 php-apache-app
```

Ensuite, l'application était accessible via `http://localhost:8081`.

---

## Partie 2 : Ajouter la gestion des versions de PHP avec des arguments de build

Nous avons ajouté la possibilité de spécifier la version de PHP en tant qu'argument de build. Voici le contenu du `Dockerfile` modifié :

```Dockerfile
ARG PHP_VERSION="8.1"
FROM php:$PHP_VERSION-apache
COPY index.php /var/www/html/
```

Ensuite, nous avons modifié le fichier `.gitlab-ci.yml` pour permettre la construction de l'image avec différentes versions de PHP (8.1, 8.2, et 8.3).

Voici la configuration du job GitLab CI pour construire l'image avec la version PHP 8.1 :

```yaml
build-image-8.1:
  script:
    - docker build --build-arg PHP_VERSION=8.1 -t $CI_REGISTRY_IMAGE/ex5:8.1 .
    - docker push $CI_REGISTRY_IMAGE/ex5:8.1
```

Nous avons répété cela pour les versions PHP 8.2 et 8.3.

---

## Partie 3 : Optimisation du fichier `.gitlab-ci.yml`

Nous avons optimisé notre fichier `.gitlab-ci.yml` en utilisant des parties réutilisables avec `extends` et des variables d'environnement.

Voici l'exemple d'optimisation :

```yaml
stages:
  - build

.build-image:
  script:
    - docker build --build-arg PHP_VERSION=$PHP_VERSION -t $CI_REGISTRY_IMAGE/ex5:$PHP_VERSION .
    - docker push $CI_REGISTRY_IMAGE/ex5:$PHP_VERSION

build-image-8.1:
  extends:
    - .build-image
  variables:
    PHP_VERSION: '8.1'

build-image-8.2:
  extends:
    - .build-image
  variables:
    PHP_VERSION: '8.2'

build-image-8.3:
  extends:
    - .build-image
  variables:
    PHP_VERSION: '8.3'
```

Cela permet de réduire la duplication de code dans le fichier CI.

---

## Partie 4 : Orchestration avec Docker Compose

Nous avons utilisé Docker Compose pour orchestrer l'application PHP et une base de données MariaDB dans des conteneurs séparés.

### Fichier `docker-compose.yml`

Le fichier `docker-compose.yml` a été configuré pour inclure trois services :

- **`web`** : Conteneur pour l'application PHP avec Apache.
- **`mariadb`** : Conteneur pour la base de données MariaDB.
- **`phpmyadmin`** : Interface web pour gérer la base de données via phpMyAdmin.

Voici le contenu complet du fichier `docker-compose.yml` :

```yaml
version: '3'
services:
  web:
    image: php:8.1-apache
    ports:
      - "8081:80"
    volumes:
      - .:/var/www/html
    networks:
      - app-network

  mariadb:
    image: mariadb:10.5
    environment:
      MYSQL_ROOT_PASSWORD: root
      MARIADB_DATABASE: mydb
    volumes:
      - ./database:/var/lib/mysql
    networks:
      - app-network

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    environment:
      PMA_HOST: mariadb
      PMA_PORT: 3306
    ports:
      - "8082:80"
    networks:
      - app-network

networks:
  app-network:
    driver: bridge

volumes:
  database:
    driver: local
```

### Lancer Docker Compose

Pour lancer l'ensemble des services en mode détaché, nous avons utilisé la commande suivante :

```bash
docker compose up -d
```

L'application PHP est accessible sur `http://localhost:8081` et phpMyAdmin sur `http://localhost:8082`.

---

## Conclusion

Dans cet exercice, nous avons :

1. **Créé une application PHP simple** et l'avons packagée dans une image Docker.
2. **Utilisé GitLab CI** pour automatiser la construction et le déploiement de l'image Docker.
3. **Orchestré l'application et la base de données** avec Docker Compose, facilitant ainsi la gestion de l'infrastructure multi-conteneurs.

### Points clés :

- Utilisation des **arguments de build** pour personnaliser la version de PHP.
- Optimisation du **pipeline CI** avec des jobs réutilisables.
- **Orchestration de conteneurs** avec Docker Compose pour gérer plusieurs services.

---

## Instructions supplémentaires

1. **Variables d'environnement** : Assurez-vous de configurer correctement les variables d'environnement dans le fichier `.gitlab-ci.yml` et dans `docker-compose.yml` (comme `MYSQL_ROOT_PASSWORD`, `PMA_HOST`, etc.).
2. **Docker et Docker Compose** : Assurez-vous que Docker et Docker Compose sont correctement installés et fonctionnels sur votre machine.
3. **Accès à l'application** : Une fois les services lancés, l'application PHP est accessible à l'adresse `http://localhost:8081`, et phpMyAdmin est accessible à l'adresse `http://localhost:8082`.
4. **Dépôt GitLab** : N'oubliez pas de pousser votre code dans le dépôt GitLab avec le message de commit approprié : « Exercice 5 ».