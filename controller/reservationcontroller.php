<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/reservation.php';
//require_once __DIR__ . '/../../vendor/autoload.php'; // Inclure FPDF si vous utilisez Composer

//use FPDF;

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

    /**
     * Génère une facture PDF pour une réservation donnée
     *
     * @param int $id ID de la réservation
     * @return string Chemin vers le fichier PDF généré
     */
    public function generateInvoice($id)
    {
        // Récupérer les détails de la réservation
        $reservation = $this->getReservationById($id);

        if (!$reservation) {
            throw new Exception("Réservation non trouvée.");
        }

        // Créer un nouveau PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Ajouter le titre
        $pdf->Cell(0, 10, 'Facture de Paiement', 0, 1, 'C');
        $pdf->Ln(10);

        // Ajouter les détails de la réservation
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'ID Réservation: ' . $reservation['id_reservation'], 0, 1);
        $pdf->Cell(0, 10, 'ID Événement: ' . $reservation['idevenement'], 0, 1);
        $pdf->Cell(0, 10, 'Date Inscription: ' . $reservation['date_inscription'], 0, 1);
        $pdf->Cell(0, 10, 'Nombre de Places: ' . $reservation['nombre_places'], 0, 1);
        $pdf->Cell(0, 10, 'Montant Payé: ' . $reservation['montant_paye'] . ' EUR', 0, 1);
        $pdf->Ln(10);

        // Message de confirmation
        $pdf->Cell(0, 10, 'Paiement confirmé avec succès!', 0, 1, 'C');

        // Sauvegarder le PDF dans un fichier
        $filename = "../../invoices/invoice_{$reservation['id_reservation']}.pdf";
        $pdf->Output('F', $filename); // 'F' pour sauvegarder le fichier localement

        return $filename;
    }

    /**
     * Met à jour le statut de paiement d'une réservation
     *
     * @param int $id ID de la réservation
     * @param string $status Nouveau statut ("payée", "en attente", etc.)
     * @return bool True si la mise à jour a réussi, False sinon
     */
    public function updatePaymentStatus($id, $status)
    {
        $db = config::getConnexion();
        $sql = "UPDATE reservation SET statut_inscription = :status WHERE id_reservation = :id";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'status' => $status,
                'id' => $id,
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
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