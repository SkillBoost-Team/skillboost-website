<?php
session_start();
require_once(__DIR__.'/../model/FormationModel.php');
require_once(__DIR__.'/../model/ModifierFormationModel.php');

$formationModel = new FormationModel();
$modifierModel = new ModifierFormationModel();

// Check if ID is provided for editing
if (isset($_GET['id'])) {
    $formationId = (int)$_GET['id'];
    $formation = $formationModel->getFormationById($formationId);
    
    if ($formation) {
        $_SESSION['form_data'] = [
            'id' => $formation['id'],
            'titre' => $formation['titre'],
            'description' => $formation['description'],
            'duree' => $formation['duree'],
            'niveau' => $formation['niveau'],
            'certificat' => $formation['certificat']
        ];
        
        header("Location: ../view/back/modifier formation.php");
        exit();
    } else {
        $_SESSION['message'] = "Formation non trouvée";
        $_SESSION['message_type'] = "danger";
        header("Location: ../controller/afficher formation.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $duree = (int)$_POST['duree'] ?? 0;
    $niveau = $_POST['niveau'] ?? '';
    $certificat = $_POST['certificat'] ?? 'Non';

    // Validation
    $valid = true;
    if (empty($titre)) {
        $_SESSION['message'] = "Le titre est obligatoire";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    } elseif (empty($description)) {
        $_SESSION['message'] = "La description est obligatoire";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    } elseif (empty($niveau)) {
        $_SESSION['message'] = "Veuillez sélectionner un niveau";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    } elseif ($duree <= 0) {
        $_SESSION['message'] = "La durée doit être supérieure à 0";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    }

    if ($valid) {
        if ($modifierModel->updateFormation($id, $titre, $description, $duree, $niveau, $certificat)) {
            $_SESSION['message'] = "Formation mise à jour avec succès!";
            $_SESSION['message_type'] = "success";
            unset($_SESSION['form_data']);
        } else {
            $_SESSION['message'] = "Erreur lors de la mise à jour";
            $_SESSION['message_type'] = "danger";
        }
        header("Location: ../view/back/afficher formation.php");
        exit();
    } else {
        // Keep form data for correction
        $_SESSION['form_data'] = $_POST;
        header("Location: ../view/back/modifier formation.php");
        exit();
    }
}
?>