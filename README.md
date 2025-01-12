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