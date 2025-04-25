<?php
require_once '../config/config.php';
require_once '../model/add-formationFrontModel.php';

class AddFormationFrontController {
    private $model;

    public function __construct() {
        global $pdo;
        $this->model = new AddFormationFrontModel($pdo);
    }

    // Handle the form submission to add a new formation and its quiz
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $duree = $_POST['duree'];
            $niveau = $_POST['niveau'];
            $certificat = isset($_POST['certificat']) ? $_POST['certificat'] : 'Non';

            // Add the new formation to the database
            $formationId = $this->model->addFormation($titre, $description, $duree, $niveau, $certificat);

            // Add the quiz questions to the database
            $question1 = $_POST['question1'];
            $answer1 = isset($_POST['answer1']);
            $question2 = $_POST['question2'];
            $answer2 = isset($_POST['answer2']);
            $question3 = $_POST['question3'];
            $answer3 = isset($_POST['answer3']);

            $this->model->addQuiz($formationId, $question1, $answer1, $question2, $answer2, $question3, $answer3);

            // Redirect to the formations page
            header('Location: formations.php');
            exit();
        }

        // Render the add-formation form
        require_once '../view/front/add-formation.php';
    }
}

// Instantiate and run the controller
$controller = new AddFormationFrontController();
$controller->index();