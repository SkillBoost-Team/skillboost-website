<?php
require_once '../config/config.php';
require_once '../model/DashboardModel.php';

class DashboardController {
    private $model;

    public function __construct() {
        global $pdo;
        $this->model = new DashboardModel($pdo);
    }

    // Display the dashboard
    public function index() {
        $filters = [
            'titre' => $_GET['titre'] ?? null,
            'niveau' => $_GET['niveau'] ?? null,
            'date_creation' => $_GET['date_creation'] ?? null
        ];

        // Fetch data based on filters
        if (!empty($filters['titre']) || !empty($filters['niveau']) || !empty($filters['date_creation'])) {
            $formations = $this->model->getFilteredFormations($filters);
        } else {
            $formations = $this->model->getAllFormations();
        }

        // Pass data to the view
        require_once '../view/back/dashboard.php';
    }

    // Fetch filtered formations based on search criteria
    public function getFilteredFormations($filters) {
        try {
            $query = "SELECT * FROM formation WHERE 1=1";
            $params = [];

            if (!empty($filters['titre'])) {
                $query .= " AND titre LIKE :titre";
                $params[':titre'] = '%' . $filters['titre'] . '%';
            }

            if (!empty($filters['niveau'])) {
                $query .= " AND niveau = :niveau";
                $params[':niveau'] = $filters['niveau'];
            }

            if (!empty($filters['date_creation'])) {
                $query .= " AND date_creation >= :date_creation";
                $params[':date_creation'] = $filters['date_creation'];
            }

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error fetching filtered formations: ' . $e->getMessage());
        }
    }

    // Display the edit formation form
    public function editFormation($id) {
        $formation = $this->model->getFormationById($id);

        if (!$formation) {
            header('Location: dashboard.php');
            exit();
        }

        // Fetch the quiz details for the given formation ID
        $quiz = $this->model->getQuizByFormationId($id);

        // Pass data to the view
        require_once '../view/back/edit-formation.php';
    }

    // Handle the form submission to update a formation and its quiz
    public function updateFormation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $titre = $_POST['titre'];
            $description = $_POST['description'];
            $duree = $_POST['duree'];
            $niveau = $_POST['niveau'];
            $certificat = isset($_POST['certificat']) ? $_POST['certificat'] : 'Non';

            // Update the formation in the database
            $this->model->updateFormation($id, $titre, $description, $duree, $niveau, $certificat);

            // Update the quiz in the database
            $question1 = $_POST['question1'];
            $answer1 = isset($_POST['answer1']);
            $question2 = $_POST['question2'];
            $answer2 = isset($_POST['answer2']);
            $question3 = $_POST['question3'];
            $answer3 = isset($_POST['answer3']);

            $quiz = $this->model->getQuizByFormationId($id);

            if ($quiz) {
                // Update the existing quiz
                $this->model->updateQuiz($quiz['id'], $question1, $answer1, $question2, $answer2, $question3, $answer3);
            } else {
                // Add a new quiz
                $this->model->addQuiz($id, $question1, $answer1, $question2, $answer2, $question3, $answer3);
            }

            // Redirect to the dashboard
            header('Location: dashboard.php');
            exit();
        }
    }
}

// Determine the action based on the request
$action = $_GET['action'] ?? 'index';
$formationId = $_GET['id'] ?? null;

switch ($action) {
    case 'index':
        $controller = new DashboardController();
        $controller->index();
        break;
    case 'edit-formation':
        if ($formationId) {
            $controller = new DashboardController();
            $controller->editFormation($formationId);
        } else {
            header('Location: dashboard.php');
            exit();
        }
        break;
    case 'update-formation':
        $controller = new DashboardController();
        $controller->updateFormation();
        break;
    default:
        header('Location: dashboard.php');
        exit();
}