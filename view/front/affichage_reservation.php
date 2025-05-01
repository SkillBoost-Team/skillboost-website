<?php
require_once '../../controller/reservationcontroller.php';
require_once '../../model/reservation.php';

$controller = new ReservationController();
$reservations = $controller->listReservations();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Réservations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        /* Table styles */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 30px 0;
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 12px; 
            text-align: center; 
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
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
    <h1><i class="fas fa-ticket-alt"></i> Liste des Réservations</h1>
    <?php if (!empty($reservations)): ?>
        <table>
            <thead>
            <tr>
                <th>ID Événement</th>
                <th>ID Utilisateur</th>
                <th>Date Inscription</th>
                <th>Nombre Places</th>
                <th>Statut Inscription</th>
                <th>Méthode Paiement</th>
                <th>Montant Payé</th>
                <th>ID Réservation</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= htmlspecialchars($reservation['idevenement']) ?></td>
                    <td><?= htmlspecialchars($reservation['id_utilisateur']) ?></td>
                    <td><?= htmlspecialchars($reservation['date_inscription']) ?></td>
                    <td><?= htmlspecialchars($reservation['nombre_places']) ?></td>
                    <td><?= htmlspecialchars($reservation['statut_inscription']) ?></td>
                    <td><?= htmlspecialchars($reservation['methode_paiement']) ?></td>
                    <td><?= htmlspecialchars($reservation['montant_paye']) ?></td>
                    <td><?= htmlspecialchars($reservation['id_reservation']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune réservation pour le moment.</p>
    <?php endif; ?>
    <a href="reservation.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à l'ajout de réservations</a>
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