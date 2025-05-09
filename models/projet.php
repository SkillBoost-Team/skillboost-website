<?php
class Projet {
    private $conn;
    private $table = "projet";

    public $id;
    public $titre;
    public $description;
    public $montant_cible;
    public $montant_actuel;
    public $statut;
    public $id_createur;
    public $date_creation;
    public $categorie;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        try {
            $query = "INSERT INTO " . $this->table . " 
                    (titre, description, montant, id_createur, categorie) 
                    VALUES (:titre, :description, :montant, :id_createur, :categorie)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":titre", $this->titre);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":montant", $this->montant_cible);
            $stmt->bindParam(":id_createur", $this->id_createur);
            $stmt->bindParam(":categorie", $this->categorie);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création du projet: " . $e->getMessage());
        }
    }

    public function getProjetsInvestissables() {
        try {
            $query = "SELECT 
                        p.*,
                        COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_actuel,
                        p.montant - COALESCE(SUM(CASE WHEN i.statut = 'Accepté' THEN i.montant ELSE 0 END), 0) as montant_restant
                     FROM projet p
                     LEFT JOIN investissement i ON p.id = i.id_projet
                     GROUP BY 
                        p.id, 
                        p.titre, 
                        p.description, 
                        p.date_creation, 
                        p.statut, 
                        p.id_createur, 
                        p.montant";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des projets investissables: " . $e->getMessage());
        }
    }

    public function getProjetsByCreateur($id_createur) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id_createur = :id_createur";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_createur", $id_createur);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des projets du créateur: " . $e->getMessage());
        }
    }
} 