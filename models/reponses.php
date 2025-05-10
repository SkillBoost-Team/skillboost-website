<?php
class Reponse {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Création avec historique
    public function create($reclamationId, $data) {
        $this->db->beginTransaction();
        
        try {
            // Insertion de la réponse
            $stmt = $this->db->prepare("
                INSERT INTO reponses_reclamations 
                (reclamation_id, admin_id, reponse, date_reponse) 
                VALUES (:reclamation_id, :admin_id, :reponse, NOW())
            ");
            $stmt->execute([
                ':reclamation_id' => $reclamationId,
                ':admin_id' => $_SESSION['admin_id'],
                ':reponse' => $data['response_text']
            ]);
            
            // Mise à jour du statut de la réclamation
            if (!empty($data['change_status'])) {
                $updateStmt = $this->db->prepare("
                    UPDATE reclamations 
                    SET status = :status 
                    WHERE id = :id
                ");
                $updateStmt->execute([
                    ':status' => $data['change_status'],
                    ':id' => $reclamationId
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Récupération avec jointure admin
    public function getFullResponses($reclamationId) {
        $stmt = $this->db->prepare("
            SELECT r.*, a.username as admin_name, a.role as admin_role
            FROM reponses_reclamations r
            JOIN admins a ON r.admin_id = a.id
            WHERE r.reclamation_id = :reclamation_id
            ORDER BY r.date_reponse ASC
        ");
        $stmt->execute([':reclamation_id' => $reclamationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}