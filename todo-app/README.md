# Todo List - Application Docker

Cette application de todo list utilise Docker et GitHub Actions.

## Prérequis

- Docker
- Docker Compose

## Installation

1. Démarrez les conteneurs Docker :
   ```
   docker compose up -d
   ```

2. Accédez à l'application dans votre navigateur :
   ```
   http://localhost:8080
   ```

## Structure du projet
   ```
   todo-app/
   ├── docker/
   │   ├── nginx/
   │   │   ├── default.conf
   │   ├── php/
   │   │   ├── Dockerfile
   ├── src/
   │   ├── index.php
   │   ├── api/
   │   │   ├── tasks.php
   │   ├── css/
   │   │   ├── style.css
   │   ├── js/
   │   │   ├── app.js
   ├── .env
   ├── docker-compose.yml
   ├── README.md
   ```

## Fonctionnalités

- Ajouter une tâche
- Marquer une tâche comme terminée
- Supprimer une tâche
- Mise en cache avec Redis pour améliorer les performances

## Structure des conteneurs

- **php** : Exécute le code PHP, basé sur PHP 8.1-FPM
- **nginx** : Sert l'application web, écoute sur le port 8080
- **postgres** : Stocke les données, accessible sur le port 5432
- **redis** : Gère le cache, accessible sur le port 6379

## Développement

Les fichiers sources sont montés en volume dans les conteneurs, ce qui permet de voir les modifications en temps réel sans avoir à reconstruire les images.
