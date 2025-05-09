<?php
require_once '../../controller/reservationcontroller.php';
require_once '../../model/reservation.php';

$controller = new ReservationController();
$reservations = $controller->getAllReservations(); // Assuming you have this method in your controller
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des réservations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css ">
    <style>
        /* Existing styles here */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-button {
            padding: 5px 10px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .edit-button {
            background-color: #2196F3;
        }
        .edit-button:hover {
            background-color: #0b7dda;
        }
        .delete-button {
            background-color: #f44336;
        }
        .delete-button:hover {
            background-color: #d32f2f;
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
        <h2>Liste des réservations</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Réservation</th>
                    <th>ID Événement</th>
                    <th>ID Utilisateur</th>
                    <th>Date Inscription</th>
                    <th>Nombre Places</th>
                    <th>Statut Inscription</th>
                    <th>Méthode Paiement</th>
                    <th>Montant Payé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation->getIdReservation()); ?></td>
                        <td><?php echo htmlspecialchars($reservation->getIdEvenement()); ?></td>
                        <td><?php echo htmlspecialchars($reservation->getIdUtilisateur()); ?></td>
                        <td><?php echo htmlspecialchars($reservation->getDateInscription()); ?></td>
                        <td><?php echo htmlspecialchars($reservation->getNombrePlaces()); ?></td>
                        <td><?php echo htmlspecialchars($reservation->getStatutInscription()); ?></td>
                        <td><?php echo htmlspecialchars($reservation->getMethodePaiement()); ?></td>
                        <td><?php echo htmlspecialchars($reservation->getMontantPaye()); ?></td>
                        <!-- Bouton Modifier -->
    <a href="update_reservation.php" class="consult-button" style="background-color: #1976d2;">
        <i class="fas fa-edit"></i> Modifier une réservation
    </a>
    <!-- Bouton Supprimer -->
    <a href="delate_reservation.php" class="consult-button" style="background-color: #d32f2f;">
        <i class="fas fa-trash-alt"></i> Supprimer une réservation
    </a>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="ajout_reservation.php" class="consult-button">
            <i class="fas fa-plus"></i> Ajouter une réservation
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
</body>
</html>