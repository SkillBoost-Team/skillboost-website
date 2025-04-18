<?php
require_once(__DIR__.'/../config/config.php');

class FormationModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getFormations() {
        try {
            $sql = "SELECT * FROM formation";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching formations: " . $e->getMessage());
            return false;
        }
    }

    public function getFormationById($id) {
        try {
            $sql = "SELECT * FROM formation WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting formation: " . $e->getMessage());
            return false;
        }
    }

    public function deleteFormationById($id) {
        try {
            $sql = "DELETE FROM formation WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting formation: " . $e->getMessage());
            return false;
        }
    }
}
?>