<?php
require_once '../../controller/reservationcontroller.php';
require_once '../../model/reservation.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("HTTP/1.0 400 Bad Request");
    exit("ID de réservation invalide");
}

$controller = new ReservationController();
$reservation = $controller->getReservationById($_GET['id']);

if (!$reservation) {
    header("HTTP/1.0 404 Not Found");
    exit("Réservation introuvable.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $idevenement = intval(trim($_POST['idevenement']));
    $id_utilisateur = intval(trim($_POST['id_utilisateur']));
    $date_inscription = $_POST['date_inscription'];
    $nombre_places = intval(trim($_POST['nombre_places']));
    $statut_inscription = trim($_POST['statut_inscription']);
    $methode_paiement = trim($_POST['methode_paiement']);
    $montant_paye = floatval(trim($_POST['montant_paye']));
    $id_reservation = intval(trim($_POST['id_reservation']));

    // Préparation des données pour la mise à jour
    $updatedReservation = [
        'idevenement' => $idevenement,
        'id_utilisateur' => $id_utilisateur,
        'date_inscription' => $date_inscription,
        'nombre_places' => $nombre_places,
        'statut_inscription' => $statut_inscription,
        'methode_paiement' => $methode_paiement,
        'montant_paye' => $montant_paye,
        'id_reservation' => $id_reservation
    ];

    // Mise à jour dans la base de données
    if ($controller->updateReservation($_GET['id'], $updatedReservation)) {
        $message = "✅ Réservation mise à jour avec succès !";
        // Recharger les données mises à jour
        $reservation = $controller->getReservationById($_GET['id']);
    } else {
        $message = "❌ Erreur lors de la mise à jour de la réservation.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Réservation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif; 
        }
        body { 
            display: flex; 
            flex-direction: column;
            background-color: #f5f5f9; 
            min-height: 100vh;
        }
        .main-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }
        .quick-links {
            background-color: #1a2a3a;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .quick-links h3 {
            color: white;
            margin-bottom: 1rem;
            text-align: center;
        }
        .quick-links ul {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            list-style: none;
        }
        .quick-links ul li a {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background-color: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .quick-links ul li a:hover {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #6f42c1;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 1rem;
            transition: border 0.3s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #6f42c1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(111, 66, 193, 0.2);
        }
        button {
            background-color: #6f42c1;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        button:hover {
            background-color: #5a32a8;
        }
        .success-message {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 6px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            font-weight: 600;
            text-align: center;
        }
        .error-message {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 6px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            font-weight: 600;
            text-align: center;
        }
        .error-border { 
            border: 1px solid #dc3545 !important; 
        }
        .field-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        /* Footer styles */
        .footer-main {
            background-color: #1a2a3a;
            color: white;
            padding: 50px 0 30px;
            width: 100%;
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .footer-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .footer-col {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
            padding: 0 15px;
        }
        .footer-col h3 {
            color: white;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .footer-col p {
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
        }
        .footer-contact i {
            margin-right: 10px;
            color: #4CAF50;
        }
        .footer-contact div {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .footer-social {
            display: flex;
            margin-top: 20px;
        }
        .btn-square {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 2px;
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            margin-right: 10px;
            transition: all 0.3s;
        }
        .btn-square:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .footer-bottom {
            background-color: #061429;
            color: white;
            padding: 15px 0;
            text-align: center;
            width: 100%;
        }
        @media (max-width: 768px) {
            .quick-links ul {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
<div class="main-container">
    <!-- Quick Links Section -->
    <div class="quick-links">
        <h3>SkillBoost - Plateforme d'apprentissage et de développement des compétences</h3>
        <ul>
            <li><a href="admin-dashboard.html"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="admin-users.html"><i class="fas fa-book"></i> Utilisateurs</a></li>
            <li><a href="admin-projects.html"><i class="fas fa-calendar-alt"></i> Projets</a></li>
            <li><a href="admin-formations.html"><i class="fas fa-chart-line"></i> Formations</a></li>
            <li><a href="admin-events.php"><i class="fas fa-calendar-alt"></i> Événements</a></li>
            <li><a href="admin-investments.html"><i class="fas fa-chart-line"></i> Investissements</a></li>
            <li><a href="admin-reclamations.html"><i class="fas fa-exclamation-circle"></i> Réclamations</a></li>
        </ul>
    </div>
    <!-- Form Container -->
    <div class="form-container">
        <h2><i class="fas fa-ticket-alt"></i> Modifier une Réservation</h2>
        <?php if (!empty($message)): ?>
            <div class="success-message" style="background-color: <?= strpos($message, '❌') !== false ? '#f8d7da' : '#d4edda'; ?>; color: <?= strpos($message, '❌') !== false ? '#721c24' : '#155724'; ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form method="POST" id="reservationForm">
            <div class="form-group">
                <label for="idevenement">ID de l'événement :</label>
                <input type="number" id="idevenement" name="idevenement" 
                       value="<?= htmlspecialchars($reservation['idevenement']) ?>">
                <div class="field-error" id="idevenement-error"></div>
            </div>
            <div class="form-group">
                <label for="id_utilisateur">ID de l'utilisateur :</label>
                <input type="number" id="id_utilisateur" name="id_utilisateur" 
                       value="<?= htmlspecialchars($reservation['id_utilisateur']) ?>">
                <div class="field-error" id="id_utilisateur-error"></div>
            </div>
            <div class="form-group">
                <label for="date_inscription">Date d'inscription :</label>
                <input type="date" id="date_inscription" name="date_inscription" 
                       value="<?= htmlspecialchars($reservation['date_inscription']) ?>">
                <div class="field-error" id="date_inscription-error"></div>
            </div>
            <div class="form-group">
                <label for="nombre_places">Nombre de places :</label>
                <input type="number" id="nombre_places" name="nombre_places" 
                       value="<?= htmlspecialchars($reservation['nombre_places']) ?>">
                <div class="field-error" id="nombre_places-error"></div>
            </div>
            <div class="form-group">
                <label for="statut_inscription">Statut d'inscription :</label>
                <select id="statut_inscription" name="statut_inscription">
                    <option value="">-- Sélectionnez --</option>
                    <option value="en attente" <?= $reservation['statut_inscription'] == 'en attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="payée" <?= $reservation['statut_inscription'] == 'payée' ? 'selected' : '' ?>>Payée</option>
                    <option value="annulée" <?= $reservation['statut_inscription'] == 'annulée' ? 'selected' : '' ?>>Annulée</option>
                </select>
                <div class="field-error" id="statut_inscription-error"></div>
            </div>
            <div class="form-group">
                <label for="methode_paiement">Méthode de paiement :</label>
                <select id="methode_paiement" name="methode_paiement">
                    <option value="">-- Sélectionnez --</option>
                    <option value="carte bancaire" <?= $reservation['methode_paiement'] == 'carte bancaire' ? 'selected' : '' ?>>Carte bancaire</option>
                    <option value="virement bancaire" <?= $reservation['methode_paiement'] == 'virement bancaire' ? 'selected' : '' ?>>Virement bancaire</option>
                    <option value="paypal" <?= $reservation['methode_paiement'] == 'paypal' ? 'selected' : '' ?>>PayPal</option>
                </select>
                <div class="field-error" id="methode_paiement-error"></div>
            </div>
            <div class="form-group">
                <label for="montant_paye">Montant payé :</label>
                <input type="number" step="0.01" id="montant_paye" name="montant_paye" 
                       value="<?= htmlspecialchars($reservation['montant_paye']) ?>">
                <div class="field-error" id="montant_paye-error"></div>
            </div>
            <div class="form-group">
                <label for="id_reservation">ID de la réservation :</label>
                <input type="number" id="id_reservation" name="id_reservation" 
                       value="<?= htmlspecialchars($reservation['id_reservation']) ?>">
                <div class="field-error" id="id_reservation-error"></div>
            </div>
            <button type="submit">
                <i class="fas fa-save"></i> Mettre à jour
            </button>
        </form>
    </div>
</div>
<!-- Footer Start -->
<footer class="footer-main">
    <div class="footer-container">
        <div class="footer-row">
            <div class="footer-col">
                <h3>SkillBoost</h3>
                <p>Plateforme d'apprentissage et de développement des compétences pour les étudiants et professionnels.</p>
            </div>
            <div class="footer-col">
                <h3>Contact</h3>
                <div class="footer-contact">
                    <div>
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Bloc E, Esprit, Cite La Gazelle<br>123 Rue Tunis, Tunisie, TN</p>
                    </div>
                    <div>
                        <i class="fas fa-envelope"></i>
                        <p>SkillBoost@gmail.com</p>
                    </div>
                    <div>
                        <i class="fas fa-phone-alt"></i>
                        <p>+216 90 044 054</p>
                    </div>
                </div>
                <div class="footer-social">
                    <a href="#" class="btn-square"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn-square"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn-square"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="btn-square"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="footer-bottom">
    <div class="footer-container">
        <p>&copy; SkillBoost. All Rights Reserved.</p>
    </div>
</div>
<!-- Footer End -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reservationForm');
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
        // Validation en temps réel
        document.getElementById('idevenement').addEventListener('input', validateIdEvenement);
        document.getElementById('id_utilisateur').addEventListener('input', validateIdUtilisateur);
        document.getElementById('date_inscription').addEventListener('change', validateDateInscription);
        document.getElementById('nombre_places').addEventListener('input', validateNombrePlaces);
        document.getElementById('statut_inscription').addEventListener('change', validateStatutInscription);
        document.getElementById('methode_paiement').addEventListener('change', validateMethodePaiement);
        document.getElementById('montant_paye').addEventListener('input', validateMontantPaye);
        document.getElementById('id_reservation').addEventListener('input', validateIdReservation);
    });

    function validateForm() {
        let isValid = true;
        // Réinitialiser les erreurs
        document.querySelectorAll('.field-error').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.error-border').forEach(el => {
            el.classList.remove('error-border');
        });
        // Valider chaque champ
        isValid = validateIdEvenement() && isValid;
        isValid = validateIdUtilisateur() && isValid;
        isValid = validateDateInscription() && isValid;
        isValid = validateNombrePlaces() && isValid;
        isValid = validateStatutInscription() && isValid;
        isValid = validateMethodePaiement() && isValid;
        isValid = validateMontantPaye() && isValid;
        isValid = validateIdReservation() && isValid;
        return isValid;
    }

    function validateIdEvenement() {
        const idevenement = document.getElementById('idevenement');
        const error = document.getElementById('idevenement-error');
        if (idevenement.value.trim() === "" || idevenement.value <= 0) {
            showError(idevenement, error, "L'ID de l'événement doit être un entier positif.");
            return false;
        }
        return true;
    }

    function validateIdUtilisateur() {
        const id_utilisateur = document.getElementById('id_utilisateur');
        const error = document.getElementById('id_utilisateur-error');
        if (id_utilisateur.value.trim() === "" || id_utilisateur.value <= 0) {
            showError(id_utilisateur, error, "L'ID de l'utilisateur doit être un entier positif.");
            return false;
        }
        return true;
    }

    function validateDateInscription() {
        const date_inscription = document.getElementById('date_inscription');
        const error = document.getElementById('date_inscription-error');
        if (date_inscription.value.trim() === "") {
            showError(date_inscription, error, "La date d'inscription est obligatoire.");
            return false;
        }
        return true;
    }

    function validateNombrePlaces() {
        const nombre_places = document.getElementById('nombre_places');
        const error = document.getElementById('nombre_places-error');
        if (nombre_places.value.trim() === "" || nombre_places.value <= 0) {
            showError(nombre_places, error, "Le nombre de places doit être un entier positif.");
            return false;
        }
        return true;
    }

    function validateStatutInscription() {
        const statut_inscription = document.getElementById('statut_inscription');
        const error = document.getElementById('statut_inscription-error');
        if (statut_inscription.value.trim() === "") {
            showError(statut_inscription, error, "Le statut d'inscription est obligatoire.");
            return false;
        }
        return true;
    }

    function validateMethodePaiement() {
        const methode_paiement = document.getElementById('methode_paiement');
        const error = document.getElementById('methode_paiement-error');
        if (methode_paiement.value.trim() === "") {
            showError(methode_paiement, error, "La méthode de paiement est obligatoire.");
            return false;
        }
        return true;
    }

    function validateMontantPaye() {
        const montant_paye = document.getElementById('montant_paye');
        const error = document.getElementById('montant_paye-error');
        if (montant_paye.value.trim() === "" || montant_paye.value < 0) {
            showError(montant_paye, error, "Le montant payé ne peut pas être négatif.");
            return false;
        }
        return true;
    }

    function validateIdReservation() {
        const id_reservation = document.getElementById('id_reservation');
        const error = document.getElementById('id_reservation-error');
        if (id_reservation.value.trim() === "" || id_reservation.value <= 0) {
            showError(id_reservation, error, "L'ID de la réservation doit être un entier positif.");
            return false;
        }
        return true;
    }

    function showError(field, errorElement, message) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        field.classList.add('error-border');
        field.focus();
    }
</script>
</body>
</html>