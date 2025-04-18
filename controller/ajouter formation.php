<?php
session_start();
require_once('../model/ajouter formation.php');

// Initialize the model
$formationModel = new FormationModel();

// Initialize form data in session if not set
if (!isset($_SESSION['form_data'])) {
    $_SESSION['form_data'] = [
        'titre' => '',
        'description' => '',
        'duree' => '',
        'niveau' => '',
        'certificat' => 'Oui'
    ];
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $duree = $_POST['duree'] ?? 0;
    $niveau = $_POST['niveau'] ?? '';
    $certificat = $_POST['certificat'] ?? 'Oui';

    // Store submitted data in session
    $_SESSION['form_data'] = [
        'titre' => $titre,
        'description' => $description,
        'duree' => $duree,
        'niveau' => $niveau,
        'certificat' => $certificat
    ];

    // Basic validation
    $valid = true;
    if (empty($titre)) {
        $_SESSION['message'] = "Le titre est obligatoire";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    } 
    elseif (empty($description)) {
        $_SESSION['message'] = "La description est obligatoire";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    } 
    elseif (empty($niveau)) {
        $_SESSION['message'] = "Veuillez sélectionner un niveau";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    } 
    elseif ($duree <= 0) {
        $_SESSION['message'] = "La durée doit être supérieure à 0";
        $_SESSION['message_type'] = "danger";
        $valid = false;
    }

    if ($valid) {
        // Add formation to database
        if ($formationModel->addFormation($titre, $description, $duree, $niveau, $certificat)) {
            $_SESSION['message'] = "Formation ajoutée avec succès!";
            $_SESSION['message_type'] = "success";
            
            // Clear form fields after successful submission
            $_SESSION['form_data'] = [
                'titre' => '',
                'description' => '',
                'duree' => '',
                'niveau' => '',
                'certificat' => 'Oui'
            ];
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout de la formation";
            $_SESSION['message_type'] = "danger";
        }
    }
}

// Always return to the form page
header("Location: ../view/back/ajouter formation.php");
exit();
?>