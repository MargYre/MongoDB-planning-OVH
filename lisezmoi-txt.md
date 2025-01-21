PLANNING DES CORVÉES D'ÉPLUCHAGE
==============================

URL du projet : https://www.projet-web-training.ovh/licence22/planning-devoir/planning/index.php

1. FONCTIONNALITÉS DÉVELOPPÉES (DEMANDÉES DANS LE SUJET)
------------------------------------------------------
* Affichage du planning annuel par semaine [FINALISÉ]
* Sélection de l'année via un menu déroulant [FINALISÉ]
* Affichage des utilisateurs assignés à chaque semaine [FINALISÉ]
* Modification de l'utilisateur assigné via menu déroulant [FIABLE avec quelques erreurs en console]
* Gestion de la connexion utilisateur [FINALISÉ]
* Stockage des données dans MongoDB [FINALISÉ]
* Interface responsive [A AMÉLIORER]
* Vérification des autorisations pour les modifications [FINALISÉ]
* Affichage des statistiques par utilisateur [FINALISÉ]

3. FONCTIONNALITÉS PERSONNELLES AJOUTÉES
-------------------------------------
* Gestion des erreurs détaillée avec messages explicites
* Confirmation avant modification d'une assignation
* Système de logs pour le débogage
* Validation des données côté serveur et client
* Gestion du timeout des requêtes
* Conservation de la valeur précédente en cas d'erreur lors de la modification

4. ÉTAT DES FONCTIONNALITÉS
-------------------------
Authentification :
- Connexion [FINALISÉ]
- Déconnexion [FINALISÉ]
- Vérification des droits [FINALISÉ]

Gestion du planning :
- Affichage [FINALISÉ]
- Navigation par année [FINALISÉ]
- Modification des assignations [FIABLE avec quelques erreurs en console]
- Validation des données [FINALISÉ]

Base de données :
- Connection MongoDB [FINALISÉ]
- Requêtes CRUD [FINALISÉ]
- Gestion des erreurs [FINALISÉ]

Interface utilisateur :
- Menus déroulants [FINALISÉ]
- Messages de confirmation [FINALISÉ]
- Messages d'erreur [FINALISÉ]
- Statistiques [FINALISÉ]

Notes sur les bugs connus :
- Quelques erreurs non bloquantes apparaissent dans la console lors de la modification d'une assignation
- Les messages d'erreur pourraient être plus explicites dans certains cas
- La gestion des sessions pourrait être améliorée
- Le responsive design pourrait être amélioréb