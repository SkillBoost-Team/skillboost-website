<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'createur') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['montant'])) {
        throw new Exception('Données manquantes');
    }

    $db = Database::getInstance()->getConnection();

    // Vérifier que le revenu appartient à un projet du créateur
    $sql = "
        SELECT rp.id 
        FROM revenu_projet rp
        JOIN projet p ON rp.id_projet = p.id
        WHERE rp.id = :id
        AND p.id_createur = :id_createur
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id' => $data['id'],
        ':id_createur' => $_SESSION['user_id']
    ]);

    if (!$stmt->fetch()) {
        throw new Exception('Revenu non trouvé ou non autorisé');
    }

    // Mettre à jour le montant
    $sql = "
        UPDATE revenu_projet
        SET montant = :montant
        WHERE id = :id
    ";
    $stmt = $db->prepare($sql);
    $success = $stmt->execute([
        ':montant' => $data['montant'],
        ':id' => $data['id']
    ]);

    if (!$success) {
        throw new Exception('Erreur lors de la mise à jour');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 