<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id_projet'])) {
    echo json_encode(['success' => false, 'message' => 'Projet non spécifié.']);
    exit;
}

$id_projet = (int)$data['id_projet'];

if (isset($_SESSION['favoris']) && ($key = array_search($id_projet, $_SESSION['favoris'])) !== false) {
    unset($_SESSION['favoris'][$key]);
    $_SESSION['favoris'] = array_values($_SESSION['favoris']);
    echo json_encode(['success' => true, 'message' => 'Projet retiré des favoris.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Projet non trouvé dans les favoris.']);
}