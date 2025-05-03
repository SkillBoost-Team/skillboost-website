<?php
session_start();

// Vérifier si l'utilisateur est connecté (vérification plus générale)
if (!isset($_SESSION['role']) || empty($_SESSION['role'])) {
    // Si l'utilisateur n'est pas connecté, rediriger vers la page d'accueil
    header("Location: ../index.php");
    exit();
}

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous voulez détruire complètement la session, effacez également le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header("Location: ../index.php");
exit();
?> 