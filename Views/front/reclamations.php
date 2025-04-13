<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create_reclamation':
        $stmt = $conn->prepare("INSERT INTO reclamations (id_utilisateur, sujet, type_reclamation, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $_SESSION['user_id'], $_POST['sujet'], $_POST['type_reclamation'], $_POST['description']);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Réclamation créée avec succès']);
        break;

    case 'update_status':
        $stmt = $conn->prepare("UPDATE reclamations SET statut = ? WHERE id = ?");
        $stmt->bind_param("si", $_POST['statut'], $_POST['id']);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
        break;

    case 'add_response':
        $stmt = $conn->prepare("INSERT INTO reponsesreclamations (id_reclamation, reponse) VALUES (?, ?)");
        $stmt->bind_param("is", $_POST['id_reclamation'], $_POST['reponse']);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Réponse ajoutée']);
        break;

    case 'get_reclamations':
        $isAdmin = $_SESSION['role'] == 'admin';
        $query = $isAdmin
            ? "SELECT r.*, u.nom_complet FROM reclamations r JOIN utilisateurs u ON r.id_utilisateur = u.id ORDER BY r.date_reclamation DESC"
            : "SELECT * FROM reclamations WHERE id_utilisateur = {$_SESSION['user_id']} ORDER BY date_reclamation DESC";
        $result = $conn->query($query);
        $reclamations = [];
        while ($row = $result->fetch_assoc()) {
            $reclamations[] = $row;
        }
        echo json_encode($reclamations);
        break;

    case 'get_reclamation_details':
        $id = $_GET['id'];
        $query = "SELECT * FROM reclamations WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reclamation = $result->fetch_assoc();
        echo json_encode($reclamation);
        break;

    case 'get_responses':
        $id_reclamation = $_GET['id_reclamation'];
        $query = "SELECT * FROM reponsesreclamations WHERE id_reclamation = ? ORDER BY date_reponse DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_reclamation);
        $stmt->execute();
        $result = $stmt->get_result();
        $responses = [];
        while ($row = $result->fetch_assoc()) {
            $responses[] = $row;
        }
        echo json_encode($responses);
        break;

    default:
        echo json_encode(['error' => 'Action invalide']);
        break;
}
?>
