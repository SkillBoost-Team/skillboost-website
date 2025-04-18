<?php
require_once __DIR__ . '/../models/Reclamation.php';

class ReclamationController {
    public function showForm() {
        require __DIR__ . '/../views/reclamations.php';
    }

    public function handleSubmission() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Reclamation::create($_POST)) {
                header('Location: /reclamations?success=1');
            } else {
                die("Erreur lors de l'enregistrement");
            }
        }
    }
}
?>