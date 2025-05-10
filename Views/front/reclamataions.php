<?php
class ReclamationModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer une réclamation par son ID
    public function getReclamationById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reclamations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les réponses pour une réclamation
    public function getResponsesForReclamation($reclamation_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reponses_reclamations WHERE reclamation_id = :reclamation_id ORDER BY date_reponse ASC");
        $stmt->execute([':reclamation_id' => $reclamation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une réponse à une réclamation
    public function addResponse($reclamation_id, $admin_id, $response_text) {
        $stmt = $this->pdo->prepare("INSERT INTO reponses_reclamations (reclamation_id, admin_id, reponse, date_reponse) VALUES (:reclamation_id, :admin_id, :reponse, NOW())");
        return $stmt->execute([
            ':reclamation_id' => $reclamation_id,
            ':admin_id' => $admin_id,
            ':reponse' => $response_text
        ]);
    }

    // Mettre à jour une réponse
    public function updateResponse($response_id, $reclamation_id, $response_text) {
        $stmt = $this->pdo->prepare("UPDATE reponses_reclamations SET reponse = :reponse WHERE id = :id AND reclamation_id = :reclamation_id");
        return $stmt->execute([
            ':reponse' => $response_text,
            ':id' => $response_id,
            ':reclamation_id' => $reclamation_id
        ]);
    }

    // Supprimer une réponse
    public function deleteResponse($response_id, $reclamation_id) {
        $stmt = $this->pdo->prepare("DELETE FROM reponses_reclamations WHERE id = :id AND reclamation_id = :reclamation_id");
        return $stmt->execute([
            ':id' => $response_id,
            ':reclamation_id' => $reclamation_id
        ]);
    }

    // Mettre à jour le statut d'une réclamation
    public function updateStatus($reclamation_id, $status) {
        $stmt = $this->pdo->prepare("UPDATE reclamations SET STATUS = :status WHERE id = :id");
        return $stmt->execute([
            ':status' => $status,
            ':id' => $reclamation_id
        ]);
    }

    // Compter les réponses pour une réclamation
    public function countResponses($reclamation_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reponses_reclamations WHERE reclamation_id = :reclamation_id");
        $stmt->execute([':reclamation_id' => $reclamation_id]);
        return $stmt->fetchColumn();
    }

    // Fonctions utilitaires
    public static function getStatusClass($status) {
        $classes = [
            'new' => 'status-new',
            'in-progress' => 'status-in-progress',
            'resolved' => 'status-resolved',
            'rejected' => 'status-rejected'
        ];
        return $classes[$status] ?? '';
    }

    public static function getStatusText($status) {
        $texts = [
            'new' => 'Nouveau',
            'in-progress' => 'En cours',
            'resolved' => 'Résolu',
            'rejected' => 'Rejeté'
        ];
        return $texts[$status] ?? $status;
    }

    public static function getTypeText($type) {
        $texts = [
            'technique' => 'Technique',
            'paiement' => 'Paiement',
            'service' => 'Service client',
            'autre' => 'Autre'
        ];
        return $texts[$type] ?? $type;
    }

    public static function getPriorityText($priority) {
        $texts = [
            'high' => 'Haute',
            'medium' => 'Moyenne',
            'low' => 'Basse'
        ];
        return $texts[$priority] ?? $priority;
    }

    public static function formatDate($dateString) {
        return date('d/m/Y H:i', strtotime($dateString));
    }
}
?>