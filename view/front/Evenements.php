<?php
require_once '../../controller/eventcontroller.php';
require_once '../../model/event.php';
$controller = new EvenementController();
$message = '';
$evenements = $controller->listEvenements();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification des champs
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $date_evenement = $_POST['date_evenement'];
    $type_evenement = $_POST['type_evenement'];
    $lieu_ou_lien = trim($_POST['lieu_ou_lien']);
    $statut = $_POST['statut'];
    if (
        !empty($titre) && strlen($titre) >= 4 && strlen($titre) <= 10 &&
        !empty($description) && strlen($description) >= 7 &&
        !empty($date_evenement) &&
        !empty($type_evenement) &&
        !empty($lieu_ou_lien) &&
        !empty($statut)
    ) {
        $evenement = new Evenement(
            $titre, $description, $date_evenement, $type_evenement,
            $lieu_ou_lien, $statut
        );
        if ($controller->addEvenement($evenement)) {
            $message = "✅ Événement ajouté avec succès !";
            $evenements = $controller->listEvenements(); // Rafraîchir la liste
        } else {
            $message = "❌ Erreur lors de l'ajout de l'événement.";
        }
    } else {
        $message = "❗ Tous les champs sont obligatoires. Titre: 4-10 lettres. Description: min 7 lettres.";
    }
}
// Check if the request is for fetching events in JSON format
if (isset($_GET['action']) && $_GET['action'] === 'fetchEvents') {
    header('Content-Type: application/json');
    $eventsArray = [];
    foreach ($evenements as $event) {
        $eventsArray[] = [
            'title' => htmlspecialchars($event['titre']),
            'start' => htmlspecialchars($event['date_debut'] ?? $event['date_evenement']),
            'end' => htmlspecialchars($event['date_fin'] ?? null), // Assuming you have an end date
            'description' => htmlspecialchars($event['description']),
            'type' => htmlspecialchars($event['type_evenement'] ?? $event['type'] ?? 'N/A'),
            'lieu' => htmlspecialchars($event['lieu'] ?? $event['lieu_ou_lien']),
            'statut' => htmlspecialchars($event['statut'])
        ];
    }
    echo json_encode($eventsArray);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout d'un événement</title>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
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
        }
        .error-border { 
            border: 2px solid red; 
        }
        .error-message { 
            color: red; 
            font-size: 12px; 
            margin-top: 5px; 
        }
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
        .voice-button {
            background-color: #2196F3;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            font-weight: bold;
            margin-top: 10px;
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
<div id='calendar' style="width: 50%;" alignt="centre" >
</div>
<!-- Main Content -->
<div class="main-container">
    <h1><i class="fas fa-calendar-alt"></i> Gestion des Événements</h1>
    <h2>Ajouter un événement</h2>
    <?php if (!empty($message)): ?>
        <div class="message" style="background-color: <?= strpos($message, '❌') !== false ? '#ffebee' : (strpos($message, '❗') !== false ? '#fff8e1' : '#e8f5e9'); ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form name="postForm" method="POST" onsubmit="return validateForm()">
        <label>Titre :</label>
        <input type="text" name="titre" id="titre" required>
        <div id="titre-error" class="error-message"></div>
        <label>Description :</label>
        <textarea name="description" id="description" rows="4" required></textarea>
        <div id="description-error" class="error-message"></div>
        <button type="button" class="voice-button" onclick="startVoiceRecognition()">Parlez pour remplir la description</button>
        <label>Date de l'événement :</label>
        <input type="date" name="date_evenement" required>
        <label>Type d'événement :</label>
        <select name="type_evenement" required>
            <option value="">-- Sélectionnez --</option>
            <option value="présentiel">Présentiel</option>
            <option value="en ligne">En ligne</option>
            <option value="hybride">Hybride</option>
        </select>
        <label>Lieu ou lien :</label>
        <input type="text" name="lieu_ou_lien" required>
        <label>Statut :</label>
        <select name="statut" required>
            <option value="">-- Sélectionnez --</option>
            <option value="à venir">À venir</option>
            <option value="en cours">En cours</option>
            <option value="terminé">Terminé</option>
        </select>
        <input type="submit" value="Ajouter l'événement">
    </form>
    <!-- Liste des événements -->
    <h2>Liste des événements</h2>
    <?php if (!empty($evenements)): ?>
        <table>
            <thead>
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Date</th>
                <th>Type</th>
                <th>Lieu/Lien</th>
                <th>Statut</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($evenements as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['titre']) ?></td>
                    <td><?= htmlspecialchars($event['description']) ?></td>
                    <td><?= htmlspecialchars($event['date_debut'] ?? $event['date_evenement']) ?></td>
                    <td><?= htmlspecialchars($event['type_evenement'] ?? $event['type'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($event['lieu'] ?? $event['lieu_ou_lien']) ?></td>
                    <td><?= htmlspecialchars($event['statut']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun événement pour le moment.</p>
    <?php endif; ?>
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
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: {
            url: 'Evenements.php?action=fetchEvents', // URL to fetch events
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },
        eventClick: function(info) {
            alert('Event: ' + info.event.title);
            // You can also open a modal or redirect to a detailed view
        }
    });
    calendar.render();
});

function validateForm() {
    let titre = document.getElementById("titre");
    let description = document.getElementById("description");
    let valid = true;
    // Reset des messages d'erreur
    document.getElementById("titre-error").innerText = "";
    document.getElementById("description-error").innerText = "";
    // Vérif titre : 4 à 10 lettres
    if (titre.value.trim().length < 4 || titre.value.trim().length > 10) {
        document.getElementById("titre-error").innerText = "Le titre doit contenir entre 4 et 10 caractères.";
        titre.classList.add("error-border");
        valid = false;
    } else {
        titre.classList.remove("error-border");
    }
    // Vérif description : min 7 lettres
    if (description.value.trim().length < 7) {
        document.getElementById("description-error").innerText = "La description doit contenir au moins 7 caractères.";
        description.classList.add("error-border");
        valid = false;
    } else {
        description.classList.remove("error-border");
    }
    return valid;
}

function startVoiceRecognition() {
    if ('webkitSpeechRecognition' in window) {
        var recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'fr-FR';

        recognition.start();

        recognition.onresult = function(event) {
            var transcript = event.results[0][0].transcript;
            document.getElementById('description').value = transcript;
        };

        recognition.onerror = function(event) {
            console.error('Error occurred in recognition: ' + event.error);
        };
    } else {
        alert('Reconnaissance vocale non supportée par ce navigateur.');
    }
}
</script>
</body>
</html>