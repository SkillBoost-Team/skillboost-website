<?php
// 1) Ne jamais afficher de HTML d'erreur devant le JSON
ini_set('display_errors', 0);
ini_set('log_errors',     1);
ini_set('error_log',      __DIR__ . '/../../../../logs/php-error.log');

header('Content-Type: application/json');
session_start();

// Debug logging
error_log("Session data: " . print_r($_SESSION, true));

// 2) CHEMINS RELATIFS CORRIGÉS
require_once __DIR__ . '/../../../config/config.php';           // 3 niveaux up from Views/front/investissement
require_once __DIR__ . '/../../../controllers/investissementControllers.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'createur') {
    error_log("Auth failed - user_id: " . ($_SESSION['user_id'] ?? 'not set') . ", role: " . ($_SESSION['role'] ?? 'not set'));
    http_response_code(401);
    exit(json_encode(['success' => false, 'message' => 'Accès non autorisé']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

$raw_input = file_get_contents('php://input');
error_log("Raw input: " . $raw_input);

$data = json_decode($raw_input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Données JSON invalides']));
}

if (empty($data['id_investissement']) || empty($data['nouveau_statut'])) {
    error_log("Missing data - id_investissement: " . ($data['id_investissement'] ?? 'not set') . ", nouveau_statut: " . ($data['nouveau_statut'] ?? 'not set'));
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Données manquantes']));
}

if (!in_array($data['nouveau_statut'], ['Accepté', 'Refusé'])) {
    error_log("Invalid status: " . $data['nouveau_statut']);
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Statut invalide']));
}

try {
    error_log("Attempting to get database connection");
    $db = Database::getInstance()->getConnection();
    error_log("Database connection successful");
    
    error_log("Attempting to get controller instance");
    $controller = InvestissementController::getInstance($db);
    error_log("Controller instance created successfully");

    error_log("Calling updateStatutInvestissement with: " . print_r([
        'id_investissement' => $data['id_investissement'],
        'nouveau_statut' => $data['nouveau_statut'],
        'user_id' => $_SESSION['user_id']
    ], true));

    $ok = $controller->updateStatutInvestissement(
        (int)$data['id_investissement'],
        $data['nouveau_statut'],
        $_SESSION['user_id']
    );

    if ($ok) {
        error_log("Update successful");
        echo json_encode([
            'success' => true,
            'message' => $data['nouveau_statut'] === 'Accepté'
                ? 'Investissement accepté avec succès'
                : 'Investissement refusé et supprimé'
        ]);
    } else {
        error_log("Update failed - controller returned false");
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour']);
    }
} catch (Exception $e) {
    error_log("Exception in update_statut.php: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
