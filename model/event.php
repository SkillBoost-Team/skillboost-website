<?php
class Evenement {
    public $idevenement;
    public $titre;
    public $description;
    public $date_evenement;
    public $type_evenement;
    public $lieu_ou_lien;
    public $statut;

    public function __construct($titre, $description, $date_evenement, $type_evenement, $lieu_ou_lien, $statut) {
        $this->titre = $titre;
        $this->description = $description;
        $this->date_evenement = $date_evenement;
        $this->type_evenement = $type_evenement;
        $this->lieu_ou_lien = $lieu_ou_lien;
        $this->statut = $statut;
    }

    // Getters
    public function getId() {
        return $this->idevenement;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getDateEvenement() {
        return $this->date_evenement;
    }

    public function getTypeEvenement() {
        return $this->type_evenement;
    }

    public function getLieuOuLien() {
        return $this->lieu_ou_lien;
    }

    public function getStatut() {
        return $this->statut;
    }

    // Setters
    public function setId($id) {
        $this->idevenement = $id;
    }

    public function setTitre($titre) {
        $this->titre = $titre;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setDateEvenement($date_evenement) {
        $this->date_evenement = $date_evenement;
    }

    public function setTypeEvenement($type_evenement) {
        $this->type_evenement = $type_evenement;
    }

    public function setLieuOuLien($lieu_ou_lien) {
        $this->lieu_ou_lien = $lieu_ou_lien;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }
}
?>
