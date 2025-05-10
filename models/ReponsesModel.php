<?php
class Reclamation {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Création avec validation
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO reclamations 
            (user_id, full_name, email, subject, type, priority, description, status, created_at) 
            VALUES (:user_id, :full_name, :email, :subject, :type, :priority, :description, 'new', NOW())
        ");
        return $stmt->execute([
            ':user_id' => $_SESSION['user_id'] ?? null,
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':subject' => $data['subject'],
            ':type' => $data['type'],
            ':priority' => $data['priority'],
            ':description' => $data['description']
        ]);
    }

    // Récupération avec jointure utilisateur
    public function findWithUser($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, u.profile_image 
            FROM reclamations r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Liste paginée avec filtres
    public function paginate($page = 1, $perPage = 10, $filters = []) {
        $where = [];
        $params = [];
        
        // Construction des filtres
        if (!empty($filters['status'])) {
            $where[] = "r.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['type'])) {
            $where[] = "r.type = :type";
            $params[':type'] = $filters['type'];
        }
        
        $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";
        
        // Requête principale avec jointure
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, COUNT(res.id) as responses_count
            FROM reclamations r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN reponses_reclamations res ON r.id = res.reclamation_id
            $whereClause
            GROUP BY r.id
            ORDER BY r.created_at DESC
            LIMIT :offset, :limit
        ");
        
        $params[':offset'] = ($page - 1) * $perPage;
        $params[':limit'] = $perPage;
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}