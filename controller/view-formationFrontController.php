<?php
require_once '../config/config.php';
require_once '../model/view-formationFrontModel.php';

class ViewFormationFrontController {
    private $model;

    public function __construct() {
        global $pdo;
        $this->model = new ViewFormationFrontModel($pdo);
    }

    // Display the formation details and handle quiz submission
    public function index() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: formations.php');
            exit();
        }

        // Fetch the formation details
        $formation = $this->model->getFormationById($id);

        if (!$formation) {
            header('Location: formations.php');
            exit();
        }

        // Fetch the quiz details for the given formation ID
        $quiz = $this->model->getQuizByFormationId($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $answer1 = isset($_POST['answer1']) ? (int)$_POST['answer1'] : 0;
            $answer2 = isset($_POST['answer2']) ? (int)$_POST['answer2'] : 0;
            $answer3 = isset($_POST['answer3']) ? (int)$_POST['answer3'] : 0;

            // Store the answers or process them as needed
            // For now, we'll just display them
            $userAnswers = [
                'answer1' => $answer1,
                'answer2' => $answer2,
                'answer3' => $answer3
            ];

            // Calculate score
            $score = 0;
            if ($quiz) {
                $score = ($quiz['answer1'] == $answer1) + ($quiz['answer2'] == $answer2) + ($quiz['answer3'] == $answer3);
            }

            // Pass data to the view
            require_once '../view/back/view-formation.php';
        } else {
            // Pass data to the view
            require_once '../view/back/view-formation.php';
        }
    }
}

// Instantiate and run the controller
$controller = new ViewFormationFrontController();
$controller->index();