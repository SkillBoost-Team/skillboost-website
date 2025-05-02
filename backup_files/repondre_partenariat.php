<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controlers/partenariatControllers';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier les données POST
if (!isset($_POST['id_partenariat']) || !isset($_POST['action'])) {
    $_SESSION['error'] = "Données manquantes";
    header('Location: Views/front/partenariat.php');
    exit();
}

try {
    $db = Database::getInstance()->getConnection();
    $controller = PartenariatController::getInstance($db);
    
    $pourcentage = null;
    if ($_POST['action'] === 'accepter') {
        if (!isset($_POST['pourcentage']) || !is_numeric($_POST['pourcentage']) || 
            $_POST['pourcentage'] <= 0 || $_POST['pourcentage'] > 100) {
            throw new Exception("Le pourcentage doit être compris entre 1 et 100");
        }
        $pourcentage = floatval($_POST['pourcentage']);
    }

    $result = $controller->repondrePartenariat(
        $_POST['id_partenariat'],
        $_POST['action'],
        $pourcentage
    );

    if ($result) {
        $_SESSION['success'] = "Le partenariat a été " . 
            ($_POST['action'] === 'accepter' ? "accepté" : "refusé") . " avec succès";
    } else {
        $_SESSION['error'] = "Une erreur est survenue lors du traitement de la réponse";
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: Views/front/partenariat.php'); 