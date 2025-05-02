<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controlers/investissementControllers';

header('Content-Type: application/json');

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id_investissement']) || !isset($data['nouveau_statut'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

// Vérifier que le statut est valide
if (!in_array($data['nouveau_statut'], ['Accepté', 'Refusé'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Statut invalide']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $controller = InvestissementController::getInstance($db);
    
    // Mettre à jour le statut
    $result = $controller->updateStatutInvestissement(
        $data['id_investissement'],
        $data['nouveau_statut'],
        $_SESSION['user_id']
    );
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => $data['nouveau_statut'] === 'Accepté' ? 
                        'Investissement accepté avec succès' : 
                        'Investissement refusé et supprimé'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 