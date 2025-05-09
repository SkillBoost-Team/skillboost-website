<?php
// traitement_reservation.php

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $type_evenement = htmlspecialchars($_POST['type_evenement']);
    $date_inscription = htmlspecialchars($_POST['date_inscription']);
    $nombre_places = intval($_POST['nombre_places']);
    $statut_inscription = htmlspecialchars($_POST['statut_inscription']);
    $methode_paiement = htmlspecialchars($_POST['methode_paiement']);
    $montant_paye = floatval($_POST['montant_paye']);
    $id_reservation = intval($_POST['id_reservation']);

    // Connexion à la base de données (exemple MySQLi)
    /*$servername = "localhost";
    $username = "root"; // Remplacez par votre nom d'utilisateur
    $password = ""; // Remplacez par votre mot de passe
    $dbname = "skillboost"; // Remplacez par le nom de votre base de données

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }
*/
    // Préparer et exécuter la requête d'insertion
    $sql = "INSERT INTO reservations (type_evenement, date_inscription, nombre_places, statut_inscription, methode_paiement, montant_paye, id_reservation)
            VALUES ('$type_evenement', '$date_inscription', '$nombre_places', '$statut_inscription', '$methode_paiement', '$montant_paye', '$id_reservation')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Réservation ajoutée avec succès.');</script>";
        echo "<script>window.location.href = 'confirmation.html';</script>";
    } else {
        echo "<script>alert('Erreur lors de l\'ajout de la réservation : " . $conn->error . "');</script>";
    }

    // Fermer la connexion
    $conn->close();
}
?>