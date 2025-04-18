<?php
require_once __DIR__ . '/../config/database.php';

class Reclamation {
    public static function create($data) {
        $pdo = Database::getInstance();
        
        $sql = "INSERT INTO reclamations 
                (user_id, full_name, email, SUBJECT, TYPE, priority, description, STATUS, created_at) 
                VALUES (:user_id, :full_name, :email, :subject, :type, :priority, :description, 'in progress', NOW())";
        
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute([
            ':user_id' => $data['user_id'] ?? null,
            ':full_name' => htmlspecialchars($data['full_name']),
            ':email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
            ':subject' => htmlspecialchars($data['subject']),
            ':type' => $data['type'],
            ':priority' => $data['priority'],
            ':description' => htmlspecialchars($data['description'])
        ]);
    }
}
?>