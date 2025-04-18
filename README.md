SkillBoost-Team

Description

Le projet SkillBoost est une plateforme web modulaire conçue 
pour gérer des compétences, soumettre des réclamations, et interagir 
avec d'autres utilisateurs. Ce dépôt contient le code source principal du site.

Fonctionnalités

Gestion des utilisateurs (inscription, connexion, profil).
Module de réclamation pour suivre les demandes.
Interface responsive et intuitive.
Base de données MySQL sécurisée.
Installation

Clone le dépôt :
git clone https://github.com/SkillBoost-Team/skillboost-website.git
Configure la base de données :
Crée une base skillboost dans phpMyAdmin.
Importe le fichier database/skillboost.sql.
Modifie les paramètres de connexion dans includes/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'skillboost');
Lance XAMPP et accède au site via http://localhost/skillboost.
Structure
├── assets/       # CSS, JS, images
├── includes/     # Fichiers PHP partagés
├── modules/      # Modules spécifiques (ex: réclamation)
└── index.php     # Page d'accueil
Contribution
Contributions bienvenues ! Fork le dépôt, crée une branche, et ouvre une Pull Request.

Contact
Pour toute question : SkillBoost-Team .

Licence
MIT License. Voir LICENSE pour plus de détails.
