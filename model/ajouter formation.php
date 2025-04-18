<?php
require_once(__DIR__.'/../config/config.php');

class FormationModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function addFormation($titre, $description, $duree, $niveau, $certificat) {
        try {
            $sql = "INSERT INTO formation (titre, description, duree, niveau, certificat, date_creation) 
                    VALUES (:titre, :description, :duree, :niveau, :certificat, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':duree', $duree, PDO::PARAM_INT);
            $stmt->bindParam(':niveau', $niveau, PDO::PARAM_STR);
            $stmt->bindParam(':certificat', $certificat, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding formation: " . $e->getMessage());
            return false;
        }
    }
}
?>