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
        $query = "SELECT * FROM " . $this->table . " ORDER BY date_investissement DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (id_projet, id_investisseur, montant, pourcentage, statut) 
                  VALUES (:id_projet, :id_investisseur, :montant, :pourcentage, :statut)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_projet", $this->id_projet);
        $stmt->bindParam(":id_investisseur", $this->id_investisseur);
        $stmt->bindParam(":montant", $this->montant);
        $stmt->bindParam(":pourcentage", $this->pourcentage, PDO::PARAM_INT);
        $stmt->bindParam(":statut", $this->statut);

        return $stmt->execute();
    }
}
