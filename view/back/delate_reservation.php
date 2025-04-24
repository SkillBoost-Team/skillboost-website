<?php
include '../../controller/reservationcontroller.php'; // <-- le contrôleur de réservations
$controller = new ReservationController();

// Vérification de l'ID de réservation
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("HTTP/1.0 400 Bad Request");
    exit("ID de réservation invalide");
}

$id_reservation = intval($_GET['id']);

try {
    // Suppression de la réservation
    $controller->deleteReservation($id_reservation);
    header('Location: liste_reservation.php'); // Redirection vers liste_reservation.php
    exit();
} catch (Exception $e) {
    // Gestion des erreurs
    $errorMessage = 'Erreur lors de la suppression de la réservation : ' . $e->getMessage();
    header('Location: liste_reservation.php?error=' . urlencode($errorMessage)); // Redirection vers liste_reservation.php avec message d'erreur
    exit();
}
?>