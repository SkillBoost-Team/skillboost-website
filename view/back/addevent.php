<?php
require_once '../../controller/eventcontroller.php';
require_once '../../model/event.php';

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $description = $_POST['description'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $lieu = $_POST['lieu'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $places_max = $_POST['places_max'] ?? 0;
    $statut = $_POST['statut'] ?? 'à venir';
    $date_creation = $_POST['date_creation'] ?? '';

    $places_restantes = $places_max;

    if (!empty($titre) && !empty($description) && !empty($date_debut) && !empty($date_fin) && !empty($lieu) && !empty($date_creation)) {
        $event = new evenement($titre, $description, $date_debut, $date_fin, $lieu, $prix, $places_max, $places_restantes, $statut, $date_creation);
        $controller = new EvenementController();
        $result = $controller->addEvenement($event);
        $successMessage = $result ? "Événement ajouté avec succès !" : "Erreur lors de l'ajout de l'événement.";
    } else {
        $errorMessage = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter un Événement</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f5f9;
      padding: 40px;
      display: flex;
    }

    .sidebar {
      width: 220px;
      background-color: #fff;
      height: 100vh;
      padding: 20px 15px;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
    }

    .sidebar h2 {
      color: #6f42c1;
      margin-bottom: 30px;
    }

    .sidebar a {
      display: block;
      color: #555;
      text-decoration: none;
      margin: 15px 0;
    }

    .form-container {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    h2 {
      color: #6f42c1;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    input, textarea, select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    button {
      background-color: #6f42c1;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
    }

    .message {
      margin: 15px 0;
      padding: 10px;
      border-radius: 5px;
    }

    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }

    .error-msg {
      color: red;
      font-size: 13px;
      margin-top: 4px;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>ECORIDE</h2>
    <a href="#">Dashboard</a>
    <a href="liste.php">Liste Événement</a>
    <a href="#">Paramètres</a>
  </div>

  <div class="form-container">
    <h2>Ajouter un événement</h2>

    <?php if ($successMessage): ?>
      <div class="message success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form method="POST" action="" id="eventForm" novalidate>
      <div class="form-group">
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre">
        <div class="error-msg" id="error-titre"></div>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description"></textarea>
        <div class="error-msg" id="error-description"></div>
      </div>

      <div class="form-group">
        <label for="date_debut">Date de début</label>
        <input type="date" name="date_debut" id="date_debut">
        <div class="error-msg" id="error-date_debut"></div>
      </div>

      <div class="form-group">
        <label for="date_fin">Date de fin</label>
        <input type="date" name="date_fin" id="date_fin">
        <div class="error-msg" id="error-date_fin"></div>
      </div>

      <div class="form-group">
        <label for="lieu">Lieu</label>
        <input type="text" name="lieu" id="lieu">
        <div class="error-msg" id="error-lieu"></div>
      </div>

      <div class="form-group">
        <label for="prix">Prix</label>
        <input type="number" name="prix" id="prix" min="0" step="0.01">
        <div class="error-msg" id="error-prix"></div>
      </div>

      <div class="form-group">
        <label for="places_max">Places Max</label>
        <input type="number" name="places_max" id="places_max" min="1">
        <div class="error-msg" id="error-places"></div>
      </div>

      <div class="form-group">
        <label for="statut">Statut</label>
        <select name="statut" id="statut">
          <option value="actif">Actif</option>
          <option value="en cours">En cours</option>
          <option value="annulé">Annulé</option>
        </select>
      </div>

      <div class="form-group">
        <label for="date_creation">Date de création</label>
        <input type="date" name="date_creation" id="date_creation">
        <div class="error-msg" id="error-date_creation"></div>
      </div>

      <button type="submit">Ajouter</button>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('eventForm');

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        let valid = true;

        const getField = id => document.getElementById(id);
        const showError = (id, message) => {
          document.getElementById(`error-${id}`).textContent = message;
        };
        const clearError = (id) => {
          document.getElementById(`error-${id}`).textContent = '';
        };

        // Titre
        if (!getField('titre').value.trim()) {
          showError('titre', 'Le titre est obligatoire.');
          valid = false;
        } else clearError('titre');

        // Description
        if (!getField('description').value.trim()) {
          showError('description', 'La description est obligatoire.');
          valid = false;
        } else clearError('description');

        // Date début / fin
        const debut = new Date(getField('date_debut').value);
        const fin = new Date(getField('date_fin').value);
        const today = new Date();
        if (!getField('date_debut').value) {
          showError('date_debut', 'La date de début est requise.');
          valid = false;
        } else if (debut < today) {
          showError('date_debut', 'La date doit être future.');
          valid = false;
        } else clearError('date_debut');

        if (!getField('date_fin').value) {
          showError('date_fin', 'La date de fin est requise.');
          valid = false;
        } else if (fin < debut) {
          showError('date_fin', 'La date de fin doit être après la date de début.');
          valid = false;
        } else clearError('date_fin');

        // Lieu
        if (!getField('lieu').value.trim()) {
          showError('lieu', 'Le lieu est obligatoire.');
          valid = false;
        } else clearError('lieu');

        // Prix
        if (getField('prix').value < 0) {
          showError('prix', 'Le prix ne peut pas être négatif.');
          valid = false;
        } else clearError('prix');

        // Places
        if (!getField('places_max').value || getField('places_max').value < 1) {
          showError('places', 'Le nombre de places doit être supérieur à zéro.');
          valid = false;
        } else clearError('places');

        // Date création
        if (!getField('date_creation').value) {
          showError('date_creation', 'La date de création est requise.');
          valid = false;
        } else clearError('date_creation');

        if (valid) {
          form.submit();
        }
      });
    });
  </script>
</body>
</html>
