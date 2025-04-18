<?php
require_once(__DIR__.'/../config/config.php');

class ModifierFormationModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function updateFormation($id, $titre, $description, $duree, $niveau, $certificat) {
        try {
            $sql = "UPDATE formation SET 
                    titre = :titre,
                    description = :description,
                    duree = :duree,
                    niveau = :niveau,
                    certificat = :certificat
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':duree', $duree, PDO::PARAM_INT);
            $stmt->bindParam(':niveau', $niveau, PDO::PARAM_STR);
            $stmt->bindParam(':certificat', $certificat, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating formation: " . $e->getMessage());
            return false;
        }
    }
}
?>