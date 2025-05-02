<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

try {
    require_once __DIR__ . '/config/db.php';
    require_once __DIR__ . '/controlers/investissementControllers';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de chargement des fichiers: ' . $e->getMessage(),
        'file' => __FILE__,
        'dir' => __DIR__
    ]);
    exit;
}

header('Content-Type: application/json');

// Log des données reçues
error_log('POST data: ' . print_r($_POST, true));
error_log('Session data: ' . print_r($_SESSION, true));

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

if (!isset($_POST['id_investissement']) || !isset($_POST['nouveau_montant'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Données manquantes',
        'received' => $_POST
    ]);
    exit;
}

try {
    $id_investissement = intval($_POST['id_investissement']);
    $nouveau_montant = floatval($_POST['nouveau_montant']);
    $id_investisseur = $_SESSION['user_id'];

    $db = Database::getInstance()->getConnection();
    if (!$db) {
        throw new Exception("Erreur de connexion à la base de données");
    }

    $controller = InvestissementController::getInstance($db);

    // Vérifier que l'investissement appartient bien à l'utilisateur connecté
    $query = "SELECT i.*, p.montant as montant_max, p.montant_actuel 
              FROM investissement i 
              JOIN projet p ON i.id_projet = p.id 
              WHERE i.id = :id AND i.id_investisseur = :user_id 
              AND i.statut = 'Proposé'";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_investissement, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $investissement = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$investissement) {
        echo json_encode(['success' => false, 'message' => 'Investissement non trouvé ou non modifiable']);
        exit();
    }

    // Calculer le montant disponible
    $montant_restant = $investissement['montant_max'] - $investissement['montant_actuel'] + $investissement['montant'];
    
    if ($nouveau_montant > $montant_restant) {
        echo json_encode([
            'success' => false, 
            'message' => 'Le montant demandé dépasse le montant disponible (' . number_format($montant_restant, 2) . ' DT)'
        ]);
        exit();
    }

    // Mettre à jour le montant
    $query = "UPDATE investissement 
              SET montant = :nouveau_montant 
              WHERE id = :id AND id_investisseur = :user_id AND statut = 'Proposé'";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nouveau_montant', $nouveau_montant, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id_investissement, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Montant mis à jour avec succès',
            'nouveau_montant' => number_format($nouveau_montant, 2)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }

} catch (Exception $e) {
    error_log('Error in update_montant.php: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} 