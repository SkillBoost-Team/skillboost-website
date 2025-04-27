<?php
class ReclamationModel {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Récupérer toutes les réclamations avec filtres
    public function getAllReclamations($filters = []) {
        $query = "SELECT id, user_id, full_name, email, SUBJECT, TYPE, priority, description, STATUS, created_at 
                  FROM reclamations WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $query .= " AND STATUS = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['type'])) {
            $query .= " AND TYPE = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['priority'])) {
            $query .= " AND priority = :priority";
            $params[':priority'] = $filters['priority'];
        }
        if (!empty($filters['date'])) {
            $today = date('Y-m-d');
            if ($filters['date'] === 'today') {
                $query .= " AND DATE(created_at) = :date_today";
                $params[':date_today'] = $today;
            } elseif ($filters['date'] === 'week') {
                $query .= " AND created_at >= DATE_SUB(:date_now, INTERVAL 7 DAY)";
                $params[':date_now'] = $today;
            } elseif ($filters['date'] === 'month') {
                $query .= " AND MONTH(created_at) = MONTH(:date_now) AND YEAR(created_at) = YEAR(:date_now)";
                $params[':date_now'] = $today;
            }
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Créer une nouvelle réclamation
    public function createReclamation($data) {
        $stmt = $this->pdo->prepare("INSERT INTO reclamations (
                                    full_name, email, SUBJECT, TYPE, priority, description, STATUS, created_at
                                    ) VALUES (
                                    :full_name, :email, :subject, :type, :priority, :description, :status, NOW()
                                    )");
        return $stmt->execute($data);
    }

    // Mettre à jour une réclamation existante
    public function updateReclamation($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE reclamations SET 
                                    full_name = :full_name,
                                    email = :email,
                                    SUBJECT = :subject,
                                    TYPE = :type,
                                    priority = :priority,
                                    description = :description,
                                    STATUS = :status
                                    WHERE id = :id");
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    // Supprimer une réclamation
    public function deleteReclamation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reclamations WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Marquer une réclamation comme résolue
    public function resolveReclamation($id) {
        $stmt = $this->pdo->prepare("UPDATE reclamations SET STATUS = 'resolved' WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}