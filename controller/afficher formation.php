<?php
require_once(__DIR__.'/../model/FormationModel.php');

class FormationController {
    private $formationModel;

    public function __construct() {
        $this->formationModel = new FormationModel();
    }

    public function showFormations() {
        // Fetch formations from the model
        $formations = $this->formationModel->getFormations();
        
        // Check if we got valid data
        if ($formations === false) {
            die("Error fetching formations from database");
        }
        
        // Extract variables for the view
        extract(['formations' => $formations]);
        
        // Load the view and stop further execution
        include __DIR__.'/../view/back/afficher formation.php';
        exit();  // This ensures no further code executes after loading the view
    }

    public function deleteFormation($id) {
        if ($this->formationModel->deleteFormationById($id)) {
            $_SESSION['message'] = "Formation supprimée avec succès";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression";
            $_SESSION['message_type'] = "danger";
        }
        
        // Get updated formations list
        $formations = $this->formationModel->getFormations();
        if ($formations === false) {
            die("Error fetching formations");
        }
        
        // Show the view directly
        extract(['formations' => $formations]);
        include __DIR__.'/../view/back/afficher formation.php';
        exit();
    }
}

session_start();

// Check if delete action has been triggered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $controller = new FormationController();
    $controller->deleteFormation($_POST['id']);
} else {
    // Show the list of formations
    $controller = new FormationController();
    $controller->showFormations();
}
?>