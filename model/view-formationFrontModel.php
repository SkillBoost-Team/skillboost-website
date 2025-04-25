<?php
class ViewFormationFrontModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch a single formation by ID
    public function getFormationById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM formation WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching formation: ' . $e->getMessage());
        }
    }

    // Fetch a single quiz by formation ID
    public function getQuizByFormationId($formationId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM quiz WHERE id_formation = :id_formation");
            $stmt->bindParam(':id_formation', $formationId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching quiz: ' . $e->getMessage());
        }
    }
}