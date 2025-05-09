<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/reservation.php';

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

$controller = new ReservationController();
$message = '';
$reservations = $controller->listReservations();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification des champs
    $idevenement = intval(trim($_POST['idevenement']));
    $id_utilisateur = intval(trim($_POST['id_utilisateur']));
    $date_inscription = $_POST['date_inscription'];
    $nombre_places = intval(trim($_POST['nombre_places']));
    $statut_inscription = trim($_POST['statut_inscription']);
    $methode_paiement = trim($_POST['methode_paiement']);
    $montant_paye = floatval(trim($_POST['montant_paye']));
    $id_reservation = intval(trim($_POST['id_reservation']));

    if (
        $idevenement > 0 &&
        $id_utilisateur > 0 &&
        !empty($date_inscription) &&
        $nombre_places > 0 &&
        !empty($statut_inscription) &&
        !empty($methode_paiement) &&
        $montant_paye >= 0 &&
        $id_reservation > 0
    ) {
        $reservation = new Reservation(
            $idevenement, $id_utilisateur, $date_inscription, $nombre_places,
            $statut_inscription, $methode_paiement, $montant_paye, $id_reservation
        );
        if ($controller->addReservation($reservation)) {
            $message = "✅ Réservation ajoutée avec succès !";
            $reservations = $controller->listReservations(); // Rafraîchir la liste
        } else {
            $message = "❌ Erreur lors de l'ajout de la réservation.";
        }
    } else {
        $message = "❗ Tous les champs sont obligatoires et doivent être correctement remplis.";
    }
    header("location: ../view/front/reservation.php");
}