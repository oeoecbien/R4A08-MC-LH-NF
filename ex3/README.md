Méthode 1 : Utilisation d’un conteneur Node.js en mode interactif (sous powershell)
Lancer le conteneur :
docker run -v %cd%:/app -it node:19 bash

Une fois dans le conteneur :
cd /app
npm install
npm run sass-compile

Sortir du conteneur :
exit

Méthode 2 : Création d’une image Docker avec Node.js
Créer le fichier Dockerfile :
touch Dockerfile

Contenu du Dockerfile :
dockerfile

FROM node:19
WORKDIR /app
COPY . .
RUN npm install && npm run sass-compile
RUN rm -rf node_modules

Construire l’image :
docker build -t mon_image_sass .

Exécuter l’image :
docker run mon_image_sass