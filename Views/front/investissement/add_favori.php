<?php
session_start();
header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté.']);
    exit;
}

// Récupérer l'ID du projet depuis la requête POST JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id_projet'])) {
    echo json_encode(['success' => false, 'message' => 'Projet non spécifié.']);
    exit;
}

$id_projet = (int)$data['id_projet'];

// Initialiser la liste des favoris si besoin
if (!isset($_SESSION['favoris'])) {
    $_SESSION['favoris'] = [];
}

// Ajouter le projet s'il n'est pas déjà dans les favoris
if (!in_array($id_projet, $_SESSION['favoris'])) {
    $_SESSION['favoris'][] = $id_projet;
    echo json_encode(['success' => true, 'message' => 'Projet ajouté aux favoris.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Projet déjà dans les favoris.']);
}