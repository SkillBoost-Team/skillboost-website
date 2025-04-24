<?php

class Reservation
{
    public $id_reservation;
    public $idevenement;
    public $id_utilisateur;
    public $date_inscription;
    public $nombre_places;
    public $statut_inscription;
    public $methode_paiement;
    public $montant_paye;

    public function __construct(
        $idevenement,
        $id_utilisateur,
        $date_inscription,
        $nombre_places,
        $statut_inscription,
        $methode_paiement,
        $montant_paye,
        $id_reservation
    ) {
        $this->idevenement = $idevenement;
        $this->id_utilisateur = $id_utilisateur;
        $this->date_inscription = $date_inscription;
        $this->nombre_places = $nombre_places;
        $this->statut_inscription = $statut_inscription;
        $this->methode_paiement = $methode_paiement;
        $this->montant_paye = $montant_paye;
        $this->id_reservation = $id_reservation;
    }

    // Getters
    public function getIdReservation()
    {
        return $this->id_reservation;
    }

    public function getIdevenement()
    {
        return $this->idevenement;
    }

    public function getIdUtilisateur()
    {
        return $this->id_utilisateur;
    }

    public function getDateInscription()
    {
        return $this->date_inscription;
    }

    public function getNombrePlaces()
    {
        return $this->nombre_places;
    }

    public function getStatutInscription()
    {
        return $this->statut_inscription;
    }

    public function getMethodePaiement()
    {
        return $this->methode_paiement;
    }

    public function getMontantPaye()
    {
        return $this->montant_paye;
    }

    // Setters
    public function setIdReservation($id_reservation)
    {
        $this->id_reservation = $id_reservation;
    }

    public function setIdevenement($idevenement)
    {
        $this->idevenement = $idevenement;
    }

    public function setIdUtilisateur($id_utilisateur)
    {
        $this->id_utilisateur = $id_utilisateur;
    }

    public function setDateInscription($date_inscription)
    {
        $this->date_inscription = $date_inscription;
    }

    public function setNombrePlaces($nombre_places)
    {
        $this->nombre_places = $nombre_places;
    }

    public function setStatutInscription($statut_inscription)
    {
        $this->statut_inscription = $statut_inscription;
    }

    public function setMethodePaiement($methode_paiement)
    {
        $this->methode_paiement = $methode_paiement;
    }

    public function setMontantPaye($montant_paye)
    {
        $this->montant_paye = $montant_paye;
    }
}