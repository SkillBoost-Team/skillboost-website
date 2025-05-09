<?php
class Investissement {
    private $conn;
    private $table = "investissement";

    public $id;
    public $id_projet;
    public $id_investisseur;
    public $montant;
    public $statut;
    public $date_investissement;
    public $pourcentage;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getters et Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getIdProjet() { return $this->id_projet; }
    public function setIdProjet($id_projet) { $this->id_projet = $id_projet; }

    public function getIdInvestisseur() { return $this->id_investisseur; }
    public function setIdInvestisseur($id_investisseur) { $this->id_investisseur = $id_investisseur; }

    public function getMontant() { return $this->montant; }
    public function setMontant($montant) { $this->montant = $montant; }

    public function getStatut() { return $this->statut; }
    public function setStatut($statut) { $this->statut = $statut; }

    public function getDateInvestissement() { return $this->date_investissement; }

    public function getPourcentage() { return $this->pourcentage; }
    public function setPourcentage($pourcentage) { $this->pourcentage = $pourcentage; }

    // CRUD de base
    public function getAll() {
        try {
            $query = "SELECT i.*, p.titre as projet_titre, u.full_name as nom_investisseur 
                     FROM " . $this->table . " i
                     LEFT JOIN projet p ON i.id_projet = p.id
                     LEFT JOIN users u ON i.id_investisseur = u.id
                     ORDER BY i.date_investissement DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des investissements : " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des investissements");
        }
    }

    public function create() {
        try {
            // Vérifier si le projet existe et récupérer son montant
            $query_projet = "SELECT montant FROM projet WHERE id = :id_projet";
            $stmt_projet = $this->conn->prepare($query_projet);
            $stmt_projet->bindParam(":id_projet", $this->id_projet);
            $stmt_projet->execute();
            $projet = $stmt_projet->fetch(PDO::FETCH_ASSOC);

            if (!$projet) {
                throw new Exception("Le projet n'existe pas");
            }

            // Vérifier si l'investisseur n'est pas le créateur du projet
            $query_creator = "SELECT id_createur FROM projet WHERE id = :id_projet";
            $stmt_creator = $this->conn->prepare($query_creator);
            $stmt_creator->bindParam(":id_projet", $this->id_projet);
            $stmt_creator->execute();
            $creator = $stmt_creator->fetch(PDO::FETCH_ASSOC);

            if ($creator && $creator['id_createur'] == $this->id_investisseur) {
                throw new Exception("Vous ne pouvez pas investir dans votre propre projet");
            }

            // Calculer le montant total déjà investi
            $query_montant = "SELECT COALESCE(SUM(montant), 0) as montant_total 
                            FROM " . $this->table . " 
                            WHERE id_projet = :id_projet AND statut = 'Accepté'";
            $stmt_montant = $this->conn->prepare($query_montant);
            $stmt_montant->bindParam(":id_projet", $this->id_projet);
            $stmt_montant->execute();
            $montant_total = $stmt_montant->fetch(PDO::FETCH_ASSOC)['montant_total'];

            // Vérifier si le montant restant est suffisant
            $montant_restant = $projet['montant'] - $montant_total;
            if ($this->montant > $montant_restant) {
                throw new Exception("Le montant d'investissement dépasse le montant restant à collecter");
            }

            // Vérifier le pourcentage
            if ($this->pourcentage < 1 || $this->pourcentage > 100) {
                throw new Exception("Le pourcentage doit être compris entre 1 et 100");
            }

            // Insérer l'investissement
            $query = "INSERT INTO " . $this->table . " 
                     (id_projet, id_investisseur, montant, pourcentage, statut, date_investissement) 
                     VALUES (:id_projet, :id_investisseur, :montant, :pourcentage, 'Proposé', NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":id_projet", $this->id_projet);
            $stmt->bindParam(":id_investisseur", $this->id_investisseur);
            $stmt->bindParam(":montant", $this->montant);
            $stmt->bindParam(":pourcentage", $this->pourcentage, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la création de l'investissement");
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'investissement : " . $e->getMessage());
            throw new Exception("Erreur lors de la création de l'investissement");
        }
    }

    public function updateStatut($id, $nouveau_statut) {
        try {
            // Vérifier que le statut est valide
            $statuts_valides = ['Proposé', 'Accepté', 'Refusé'];
            if (!in_array($nouveau_statut, $statuts_valides)) {
                throw new Exception("Statut invalide");
            }

            $query = "UPDATE " . $this->table . " 
                     SET statut = :statut 
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":statut", $nouveau_statut);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut : " . $e->getMessage());
            throw new Exception("Erreur lors de la mise à jour du statut");
        }
    }

    public function getInvestissementById($id) {
        try {
            $query = "SELECT i.*, p.titre as projet_titre, u.full_name as nom_investisseur 
                     FROM " . $this->table . " i
                     LEFT JOIN projet p ON i.id_projet = p.id
                     LEFT JOIN users u ON i.id_investisseur = u.id
                     WHERE i.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'investissement : " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération de l'investissement");
        }
    }

    public function getInvestissementsByProjet($id_projet) {
        try {
            $query = "SELECT i.*, u.full_name as nom_investisseur 
                     FROM " . $this->table . " i
                     LEFT JOIN users u ON i.id_investisseur = u.id
                     WHERE i.id_projet = :id_projet
                     ORDER BY i.date_investissement DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_projet", $id_projet);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des investissements du projet : " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des investissements du projet");
        }
    }

    public function getInvestissementsByInvestisseur($id_investisseur) {
        try {
            $query = "SELECT i.*, p.titre as projet_titre 
                     FROM " . $this->table . " i
                     LEFT JOIN projet p ON i.id_projet = p.id
                     WHERE i.id_investisseur = :id_investisseur
                     ORDER BY i.date_investissement DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_investisseur", $id_investisseur);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des investissements de l'investisseur : " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des investissements de l'investisseur");
        }
    }
}
