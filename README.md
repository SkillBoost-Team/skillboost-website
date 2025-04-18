SkillBoost Website
GitHub Logo
SkillBoost-Team

Description du projet
Le projet SkillBoost est une plateforme web conçue pour offrir des fonctionnalités avancées de gestion et d'interaction pour les utilisateurs. Il s'agit d'un site web modulaire qui permet aux utilisateurs de gérer leurs compétences, suivre leurs progrès, et interagir avec d'autres membres.

Ce dépôt contient le code source principal du site web SkillBoost , développé en PHP, HTML, CSS, et JavaScript, avec une base de données MySQL pour stocker les données.

Fonctionnalités principales
Gestion des utilisateurs : Inscription, connexion, et profil utilisateur.
Modules personnalisés :
Module de gestion des réclamations (module-reclamation) : Permet aux utilisateurs de soumettre des réclamations et de suivre leur statut.
Autres modules à venir (gestion des tâches, tableaux de bord, etc.).
Interface utilisateur intuitive : Design moderne et responsive pour une expérience utilisateur optimale.
Base de données sécurisée : Utilisation de MySQL pour stocker les données de manière sécurisée.
Installation et configuration
Prérequis
Avant de commencer, assure-toi d'avoir les outils suivants installés sur ton système :

XAMPP (ou tout autre serveur Apache/MySQL)
Git
Navigateur web moderne (Chrome, Firefox, Edge, etc.)
Étapes d'installation
Clone le dépôt :
bash
Copy
1
git clone https://github.com/SkillBoost-Team/skillboost-website.git
Déplace-toi dans le dossier du projet :
bash
Copy
1
cd skillboost-website
Configure la base de données :
Ouvre phpMyAdmin via XAMPP.
Crée une nouvelle base de données nommée skillboost.
Importe le fichier SQL fourni dans le dépôt (database/skillboost.sql).
Configure les variables d'environnement :
Modifie le fichier config.php dans le dossier includes pour définir les informations de connexion à la base de données :
php
Copy
1
2
3
4
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'skillboost');
Démarre le serveur local :
Lance Apache et MySQL depuis le Panneau de Contrôle XAMPP.
Accède au site en visitant http://localhost/skillboost dans ton navigateur.
Structure du projet
Copy
1
2
3
4
5
6
7
skillboost-website/
├── assets/               # Fichiers CSS, JS, et images
├── includes/             # Fichiers PHP partagés (ex: config.php, fonctions utilitaires)
├── modules/              # Modules spécifiques (ex: module-reclamation)
├── database/             # Scripts SQL pour la base de données
├── index.php             # Page d'accueil
└── README.md             # Documentation du projet
Contribution
Nous encourageons les contributions à ce projet ! Si tu souhaites contribuer :

Fork ce dépôt.
Crée une nouvelle branche pour tes modifications :
bash
Copy
1
git checkout -b feature/nom-de-la-fonctionnalité
Effectue tes modifications et commit-les :
bash
Copy
1
git commit -m "Ajout de la fonctionnalité XYZ"
Pousse tes modifications vers GitHub :
bash
Copy
1
git push origin feature/nom-de-la-fonctionnalité
Ouvre une Pull Request (PR) sur ce dépôt.
Contact
Pour toute question ou suggestion, n'hésite pas à nous contacter :

Email : contact@skillboost.com
GitHub : SkillBoost-Team
Licence
Ce projet est sous licence MIT . Consulte le fichier LICENSE pour plus de détails.
