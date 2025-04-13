<?php
require_once __DIR__ . '/../config/database.php';

class ReclamationModel {
    private $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM reclamations");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insert($data) {
        $stmt = $this->pdo->prepare("INSERT INTO reclamations (nom, email, message) VALUES (?, ?, ?)");
        return $stmt->execute([$data['nom'], $data['email'], $data['message']]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reclamations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($data) {
        $stmt = $this->pdo->prepare("UPDATE reclamations SET nom=?, email=?, message=? WHERE id=?");
        return $stmt->execute([$data['nom'], $data['email'], $data['message'], $data['id']]);
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reclamations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>
