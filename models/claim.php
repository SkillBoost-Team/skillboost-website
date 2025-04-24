<?php
require_once __DIR__.'/../config/database.php';

class Claim {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($userId, $data) {
        $stmt = $this->db->prepare("
            INSERT INTO claims 
            (user_id, description, is_constructive, status) 
            VALUES (?, ?, ?, 'pending')
        ");
        $stmt->execute([$userId, $data['description'], $data['is_constructive']]);
        return $this->db->lastInsertId();
    }

    public function addMedia($claimId, $filePath, $type) {
        $stmt = $this->db->prepare("
            INSERT INTO claim_attachments 
            (claim_id, file_path, file_type) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$claimId, $filePath, $type]);
    }
}