<?php
session_start();

// Example: After verifying the user's credentials (e.g., from a database)
$user_id = 123; // Replace with the actual user ID from your database
$_SESSION['user_id'] = $user_id; // Store the user ID in the session
?>

<?php 
require_once '../../config.php';
 $pdo = config::getConnexion();

    // Requête SQL pour récupérer les événements
    $query = "SELECT idevenement, titre FROM evenement";
    $stmt = $pdo->query($query);

    // Stocker les résultats dans un tableau
    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = [
            'idevenement' => $row['idevenement'],
            'titre' => $row['titre']
        ];
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout d'une réservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css ">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
            background-color: #f5f5f9;
        }
        /* Topbar styles */
        .topbar {
            background-color: #1a2a3a;
            color: white;
            padding: 10px 0;
            font-size: 14px;
        }
        .topbar-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        .topbar-info {
            display: flex;
            gap: 20px;
        }
        .topbar-info i {
            margin-right: 5px;
            color: #4CAF50;
        }
        .admin-space {
            display: flex;
            align-items: center;
        }
        .admin-space i {
            margin-right: 5px;
        }
        /* Main content styles */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        /* Form styles */
        form { 
            display: flex; 
            flex-direction: column; 
            gap: 10px; 
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        input, textarea, select { 
            padding: 8px; 
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] { 
            background-color: #4CAF50; 
            color: white; 
            border: none; 
            cursor: pointer;
            padding: 10px;
            font-weight: bold;
        }
        .message { 
            margin: 20px; 
            font-weight: bold; 
            padding: 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .message i {
            margin-right: 10px;
        }
        .message.success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .message.error {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .message.warning {
            background-color: #fff8e1;
            color: #ed6c02;
        }
        .error-border { 
            border: 2px solid red; 
        }
        .error-message { 
            color: red; 
            font-size: 12px; 
            margin-top: 5px; 
        }
        /* Footer styles */
        .footer {
            background-color: #1a2a3a;
            color: white;
            padding: 40px 0 20px;
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .footer-contact {
            flex: 1;
            min-width: 300px;
        }
        .footer-contact h3 {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .footer-contact p {
            margin: 5px 0;
            display: flex;
            align-items: center;
        }
        .footer-contact i {
            margin-right: 10px;
            color: #4CAF50;
        }
        .copyright {
            background-color: #061429;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 14px;
        }
        /* Consult button styles */
        .consult-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .consult-button:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
<!-- Topbar -->
<div class="topbar">
    <div class="topbar-container">
        <div class="topbar-info">
            <span><i class="fas fa-map-marker-alt"></i> Bloc E, Esprit, Cite La Gazelle</span>
            <span><i class="fas fa-phone-alt"></i> +216 90 044 054</span>
            <span><i class="fas fa-envelope"></i> SkillBoost@gmail.com</span>
        </div>
        <div class="admin-space">
            <i class="fas fa-user-shield"></i>
            <span>Espace Administrateur</span>
        </div>
    </div>
</div>
<!-- Main Content -->
<div class="main-container">
    <h1><i class="fas fa-ticket-alt"></i> Gestion des Réservations</h1>
    <h2>Ajouter une réservation</h2>
    <form name="postForm" method="POST" onsubmit="return validateForm()" action="../../controller/reservationcontroller.php">
        <label>ID de l'événement :</label>
        <select name="idevenement" id="idevenement">
            <option value="">-- Sélectionnez un événement --</option>
            <?php foreach ($events as $event) {
                echo '<option value="' . htmlspecialchars($event['idevenement']) . '">';
                echo htmlspecialchars($event['titre']);
                echo '</option>'; 
            }?>
        </select>
        <div id="idevenement-error" class="error-message"></div>
        <input type="hidden" name="id_utilisateur" id="id_utilisateur" value="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_id']) : ''; ?>">
        <div id="id_utilisateur-error" class="error-message"></div>
        <label>Date d'inscription :</label>
        <input type="date" name="date_inscription" id="date_inscription">
        <div id="date_inscription-error" class="error-message"></div>
        <label>Nombre de places :</label>
        <input type="number" name="nombre_places" id="nombre_places">
        <div id="nombre_places-error" class="error-message"></div>
        <label>Statut d'inscription :</label>
        <select name="statut_inscription" id="statut_inscription">
            <option value="">-- Sélectionnez --</option>
            <option value="en attente">En attente</option>
            <option value="payée">Payée</option>
            <option value="annulée">Annulée</option>
        </select>
        <div id="statut_inscription-error" class="error-message"></div>
        <label>Méthode de paiement :</label>
        <select name="methode_paiement" id="methode_paiement">
            <option value="">-- Sélectionnez --</option>
            <option value="carte bancaire">Carte bancaire</option>
            <option value="virement bancaire">Virement bancaire</option>
            <option value="paypal">PayPal</option>
        </select>
        <div id="methode_paiement-error" class="error-message"></div>
        <label>Montant payé :</label>
        <input type="number" step="0.01" name="montant_paye" id="montant_paye">
        <div id="montant_paye-error" class="error-message"></div>
        <label>ID de la réservation :</label>
        <input type="number" name="id_reservation" id="id_reservation">
        <div id="id_reservation-error" class="error-message"></div>
        <input type="submit" value="Ajouter la réservation">
    </form>
    <!-- Bouton Consulter -->
    <a href="affichage_reservation.php" class="consult-button">
        <i class="fas fa-eye"></i> Consulter les réservations
    </a>
    <!-- Bouton Modifier -->
    <a href="update_reservation.php" class="consult-button" style="background-color: #1976d2;">
        <i class="fas fa-edit"></i> Modifier une réservation
    </a>
    <!-- Bouton Supprimer -->
    <a href="delate_reservation.php" class="consult-button" style="background-color: #d32f2f;">
        <i class="fas fa-trash-alt"></i> Supprimer une réservation
    </a>
</div>
<!-- Footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-contact">
            <h3>Contact</h3>
            <p><i class="fas fa-map-marker-alt"></i> 123 Rue Tunis, Tunisie, TN</p>
            <p><i class="fas fa-envelope"></i> SkillBoost@gmail.com</p>
            <p><i class="fas fa-phone-alt"></i> +216 90 044 054</p>
        </div>
    </div>
</footer>
<div class="copyright">
    <p>&copy; SkillBoost. All Rights Reserved.</p>
</div>
<script>
    function validateForm() {
        let idevenement = document.getElementById("idevenement");
        let id_utilisateur = document.getElementById("id_utilisateur");
        let date_inscription = document.getElementById("date_inscription");
        let nombre_places = document.getElementById("nombre_places");
        let statut_inscription = document.getElementById("statut_inscription");
        let methode_paiement = document.getElementById("methode_paiement");
        let montant_paye = document.getElementById("montant_paye");
        let id_reservation = document.getElementById("id_reservation");
        let valid = true;
        // Reset des messages d'erreur
        document.getElementById("idevenement-error").innerText = "";
        document.getElementById("id_utilisateur-error").innerText = "";
        document.getElementById("date_inscription-error").innerText = "";
        document.getElementById("nombre_places-error").innerText = "";
        document.getElementById("statut_inscription-error").innerText = "";
        document.getElementById("methode_paiement-error").innerText = "";
        document.getElementById("montant_paye-error").innerText = "";
        document.getElementById("id_reservation-error").innerText = "";
        // Vérif ID de l'événement : entier positif
        if (idevenement.value.trim() === "" || idevenement.value <= 0) {
            document.getElementById("idevenement-error").innerText = "L'ID de l'événement doit être un entier positif.";
            idevenement.classList.add("error-border");
            valid = false;
        } else {
            idevenement.classList.remove("error-border");
        }
        // Vérif ID de l'utilisateur : entier positif
        if (id_utilisateur.value.trim() === "" || id_utilisateur.value <= 0) {
            document.getElementById("id_utilisateur-error").innerText = "L'ID de l'utilisateur doit être un entier positif.";
            id_utilisateur.classList.add("error-border");
            valid = false;
        } else {
            id_utilisateur.classList.remove("error-border");
        }
        // Vérif Date d'inscription : non vide
        if (date_inscription.value.trim() === "") {
            document.getElementById("date_inscription-error").innerText = "La date d'inscription est obligatoire.";
            date_inscription.classList.add("error-border");
            valid = false;
        } else {
            date_inscription.classList.remove("error-border");
        }
        // Vérif Nombre de places : entier positif
        if (nombre_places.value.trim() === "" || nombre_places.value <= 0) {
            document.getElementById("nombre_places-error").innerText = "Le nombre de places doit être un entier positif.";
            nombre_places.classList.add("error-border");
            valid = false;
        } else {
            nombre_places.classList.remove("error-border");
        }
        // Vérif Statut d'inscription : non vide
        if (statut_inscription.value.trim() === "") {
            document.getElementById("statut_inscription-error").innerText = "Le statut d'inscription est obligatoire.";
            statut_inscription.classList.add("error-border");
            valid = false;
        } else {
            statut_inscription.classList.remove("error-border");
        }
        // Vérif Méthode de paiement : non vide
        if (methode_paiement.value.trim() === "") {
            document.getElementById("methode_paiement-error").innerText = "La méthode de paiement est obligatoire.";
            methode_paiement.classList.add("error-border");
            valid = false;
        } else {
            methode_paiement.classList.remove("error-border");
        }
        // Vérif Montant payé : non négatif
        if (montant_paye.value.trim() === "" || montant_paye.value < 0) {
            document.getElementById("montant_paye-error").innerText = "Le montant payé ne peut pas être négatif.";
            montant_paye.classList.add("error-border");
            valid = false;
        } else {
            montant_paye.classList.remove("error-border");
        }
        // Vérif ID de la réservation : entier positif
        if (id_reservation.value.trim() === "" || id_reservation.value <= 0) {
            document.getElementById("id_reservation-error").innerText = "L'ID de la réservation doit être un entier positif.";
            id_reservation.classList.add("error-border");
            valid = false;
        } else {
            id_reservation.classList.remove("error-border");
        }
        return valid;
    }
</script>
</body>
</html>