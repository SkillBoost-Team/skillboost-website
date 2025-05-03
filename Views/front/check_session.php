<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier l'activité de la session
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) { // 30 minutes
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Mettre à jour le timestamp de la dernière activité
$_SESSION['last_activity'] = time();
?> 