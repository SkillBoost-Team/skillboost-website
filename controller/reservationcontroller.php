<?php
require_once __DIR__ . '/../config.php';

class ReservationController
{
    /**
     * Récupère la liste de toutes les réservations
     *
     * @return array|false Liste des réservations ou false en cas d'erreur
     */
    public function listReservations()
    {
        $sql = "SELECT * FROM reservation";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    /**
     * Ajoute une nouvelle réservation
     *
     * @param object $reservation Objet contenant les données de la réservation
     * @return bool True si l'ajout a réussi, False sinon
     */
    public function addReservation($reservation)
    {
        $sql = "INSERT INTO reservation (
                    idevenement, id_utilisateur, date_inscription, nombre_places, statut_inscription, 
                    methode_paiement, montant_paye, id_reservation
                ) VALUES (
                    :idevenement, :id_utilisateur, :date_inscription, :nombre_places, :statut_inscription, 
                    :methode_paiement, :montant_paye, :id_reservation
                )";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'idevenement' => $reservation->getIdevenement(),
                'id_utilisateur' => $reservation->getIdUtilisateur(),
                'date_inscription' => $reservation->getDateInscription(),
                'nombre_places' => $reservation->getNombrePlaces(),
                'statut_inscription' => $reservation->getStatutInscription(),
                'methode_paiement' => $reservation->getMethodePaiement(),
                'montant_paye' => $reservation->getMontantPaye(),
                'id_reservation' => $reservation->getIdReservation(),
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime une réservation par son ID
     *
     * @param int $id ID de la réservation à supprimer
     * @return void
     */
    public function deleteReservation($id)
    {
        $sql = "DELETE FROM reservation WHERE id_reservation = :id_reservation";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_reservation', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour une réservation existante
     *
     * @param int $id ID de la réservation à mettre à jour
     * @param array $data Tableau contenant les nouvelles données de la réservation
     * @return bool True si la mise à jour a réussi, False sinon
     */
    public function updateReservation($id, $data)
    {
        $db = config::getConnexion();
        $sql = "UPDATE reservation 
                SET idevenement = :idevenement, id_utilisateur = :id_utilisateur, 
                    date_inscription = :date_inscription, nombre_places = :nombre_places, 
                    statut_inscription = :statut_inscription, methode_paiement = :methode_paiement, 
                    montant_paye = :montant_paye 
                WHERE id_reservation = :id_reservation";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'idevenement' => $data['idevenement'],
                'id_utilisateur' => $data['id_utilisateur'],
                'date_inscription' => $data['date_inscription'],
                'nombre_places' => $data['nombre_places'],
                'statut_inscription' => $data['statut_inscription'],
                'methode_paiement' => $data['methode_paiement'],
                'montant_paye' => $data['montant_paye'],
                'id_reservation' => $id,
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Récupère une réservation par son ID
     *
     * @param int $id ID de la réservation
     * @return array|false Détails de la réservation ou false en cas d'erreur
     */
    public function getReservationById($id)
    {
        $db = config::getConnexion();
        $sql = "SELECT * FROM reservation WHERE id_reservation = :id_reservation";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_reservation', $id, PDO::PARAM_INT);
            $stmt->execute();

            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
            return $reservation ? $reservation : false;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }
}