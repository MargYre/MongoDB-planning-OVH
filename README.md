# MongoDB-planning-OVH
# Planning Corvées Patates - Projet MongoDB

## Prérequis
- PHP >= 7.4
- MongoDB
- Composer
- Extension PHP MongoDB (`php-mongodb`)

## Installation locale
1. Cloner le projet
2. Installer les dépendances :
   ```bash
   composer install
   ```
3. Configurer le fichier `.env` avec les informations de connexion à la base de données
```bash
   MONGODB_URI=mongodb+srv://username:password@cluster.mongodb.net/?retryWrites=true&w=majority
   ```
   Modifier le fichier .env avec vos identifiants MongoDBModifier le fichier .env avec vos identifiants MongoDB
4. Initialiser la base de données
```bash
   php scripts/init_db.php
```

## Vérifier l'installation de l'extension MongoDB :
```
php -m | grep mongodb
```

## Déploiement sur OVH

Configuration FileZilla :

-Hôte : ftp.cluster026.hosting.ovh.net
-Identifiant : projetwewv-licenceXX (XX = numéro fourni sur Moodle)
-Mot de passe : (fourni par le professeur)
-Port : 21

## Url du projet
```
https://www.projet-web-training.ovh/licence22/planning-devoir/planning/
```

