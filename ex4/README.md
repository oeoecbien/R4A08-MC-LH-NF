# Exercice 4 : Docker Compose - Application Node.js et MongoDB

## Objectif

L'objectif de cet exercice est de déployer une application web en utilisant Docker Compose. L'application backend est développée avec Node.js et communique avec une base de données MongoDB. 

## Structure du projet

Le projet contient deux services principaux :
1. **Backend (Node.js)** : Un serveur Node.js qui écoute sur le port 3000.
2. **MongoDB** : Une base de données MongoDB pour stocker les données de l'application.

Le fichier `docker-compose.yml` orchestre les deux services, tandis que le fichier `Dockerfile` définit l'environnement de développement pour le backend.

## Explication du fichier `docker-compose.yml`

Le fichier `docker-compose.yml` est divisé en deux services :
1. **backend** : Ce service construit l'image Docker à partir du `Dockerfile`, expose le port 3000 et dépend du service `mongo`. Il utilise une variable d'environnement pour se connecter à MongoDB.
2. **mongo** : Ce service utilise l'image officielle `mongo` et expose le port 27017 pour la connexion à la base de données. Un volume persistant est utilisé pour stocker les données de MongoDB.

### Contenu du `docker-compose.yml`

```yaml
version: '3.8'

services:
  backend:
    build: .
    ports:
      - "3000:3000"
    depends_on:
      - mongo
    environment:
      MONGO_URL: mongodb://mongo:27017/mydb

  mongo:
    image: mongo
    ports:
      - "27017:27017"
    volumes:
      - mongo-data:/data/db

volumes:
  mongo-data:
