<?php
require_once '../../controller/eventcontroller.php';
require_once '../../model/event.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("HTTP/1.0 400 Bad Request");
    exit("ID d'événement invalide");
}

$controller = new EvenementController();
$event = $controller->getEventById($_GET['id']);

if (!$event) {
    header("HTTP/1.0 404 Not Found");
    exit("Événement introuvable.");
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $date_evenement = $_POST['date_evenement'] ?? '';
    $type_evenement = $_POST['type_evenement'] ?? '';
    $lieu_ou_lien = $_POST['lieu_ou_lien'] ?? '';
    $statut = $_POST['statut'] ?? '';

    // Préparation des données pour la mise à jour
    $updatedEvent = [
        'titre' => $titre,
        'description' => $description,
        'date_evenement' => $date_evenement,
        'type_evenement' => $type_evenement,
        'lieu_ou_lien' => $lieu_ou_lien,
        'statut' => $statut
    ];

    // Mise à jour dans la base de données
    if ($controller->updateEvenement($_GET['id'], $updatedEvent)) {
        $message = "✅ Événement mis à jour avec succès !";
        // Recharger les données mises à jour
        $event = $controller->getEventById($_GET['id']);
    } else {
        $message = "❌ Erreur lors de la mise à jour de l'événement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Événement</title>
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
        
        .error-border { 
            border: 1px solid #dc3545 !important; 
        }
        
        .error-message {
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
            <li><a href="#"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="#"><i class="fas fa-book"></i> Formations</a></li>
            <li><a href="#"><i class="fas fa-calendar-alt"></i> Événements</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Investissements</a></li>
            <li><a href="#"><i class="fas fa-exclamation-circle"></i> Réclamations</a></li>
        </ul>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <h2><i class="fas fa-calendar-edit"></i> Modifier un Événement</h2>
        
        <?php if (!empty($message)): ?>
            <div class="success-message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="eventForm">
            <div class="form-group">
                <label for="titre">Titre :</label>
                <input type="text" id="titre" name="titre" 
                       value="<?= htmlspecialchars($event['titre']) ?>" required>
                <div class="error-message" id="titre-error"></div>
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($event['description']) ?></textarea>
                <div class="error-message" id="description-error"></div>
            </div>

            <div class="form-group">
                <label for="date_evenement">Date de l'Événement :</label>
                <input type="date" id="date_evenement" name="date_evenement" 
                       value="<?= htmlspecialchars(date('Y-m-d', strtotime($event['date_evenement']))) ?>" required>
                <div class="error-message" id="date-error"></div>
            </div>

            <div class="form-group">
                <label for="type_evenement">Type d'Événement :</label>
                <select id="type_evenement" name="type_evenement" required>
                    <option value="présentiel" <?= $event['type_evenement'] == 'présentiel' ? 'selected' : '' ?>>Présentiel</option>
                    <option value="en ligne" <?= $event['type_evenement'] == 'en ligne' ? 'selected' : '' ?>>En ligne</option>
                    <option value="hybride" <?= $event['type_evenement'] == 'hybride' ? 'selected' : '' ?>>Hybride</option>
                </select>
            </div>

            <div class="form-group">
                <label for="lieu_ou_lien">Lieu ou Lien :</label>
                <input type="text" id="lieu_ou_lien" name="lieu_ou_lien" 
                       value="<?= htmlspecialchars($event['lieu_ou_lien']) ?>" required>
                <div class="error-message" id="lieu-error"></div>
            </div>

            <div class="form-group">
                <label for="statut">Statut :</label>
                <select id="statut" name="statut" required>
                    <option value="à venir" <?= $event['statut'] == 'à venir' ? 'selected' : '' ?>>À venir</option>
                    <option value="en cours" <?= $event['statut'] == 'en cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="terminé" <?= $event['statut'] == 'terminé' ? 'selected' : '' ?>>Terminé</option>
                </select>
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
        const form = document.getElementById('eventForm');
        
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
        
        // Validation en temps réel
        document.getElementById('titre').addEventListener('input', validateTitre);
        document.getElementById('description').addEventListener('input', validateDescription);
        document.getElementById('date_evenement').addEventListener('change', validateDate);
        document.getElementById('lieu_ou_lien').addEventListener('input', validateLieu);
    });

    function validateForm() {
        let isValid = true;
        
        // Réinitialiser les erreurs
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.error-border').forEach(el => {
            el.classList.remove('error-border');
        });
        
        // Valider chaque champ
        isValid = validateTitre() && isValid;
        isValid = validateDescription() && isValid;
        isValid = validateDate() && isValid;
        isValid = validateLieu() && isValid;
        
        return isValid;
    }
    
    function validateTitre() {
        const titre = document.getElementById('titre');
        const error = document.getElementById('titre-error');
        
        if (!titre.value.trim()) {
            showError(titre, error, "Le titre est obligatoire.");
            return false;
        }
        
        if (titre.value.trim().length < 4 || titre.value.trim().length > 100) {
            showError(titre, error, "Le titre doit contenir entre 4 et 100 caractères.");
            return false;
        }
        
        return true;
    }
    
    function validateDescription() {
        const description = document.getElementById('description');
        const error = document.getElementById('description-error');
        
        if (!description.value.trim()) {
            showError(description, error, "La description est obligatoire.");
            return false;
        }
        
        if (description.value.trim().length < 10) {
            showError(description, error, "La description doit contenir au moins 10 caractères.");
            return false;
        }
        
        return true;
    }
    
    function validateDate() {
        const dateInput = document.getElementById('date_evenement');
        const error = document.getElementById('date-error');
        
        if (!dateInput.value) {
            showError(dateInput, error, "La date est obligatoire.");
            return false;
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selectedDate = new Date(dateInput.value);
        
        if (selectedDate < today) {
            showError(dateInput, error, "La date doit être dans le futur.");
            return false;
        }
        
        return true;
    }
    
    function validateLieu() {
        const lieu = document.getElementById('lieu_ou_lien');
        const error = document.getElementById('lieu-error');
        
        if (!lieu.value.trim()) {
            showError(lieu, error, "Le lieu ou lien est obligatoire.");
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