<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
session_start();
require_once '../Config/config1.php';
require_once '../model/view-formationFrontModel.php';

class ViewFormationFrontController {
    public $model;

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
            $correct = 0;
            $correct += (isset($_POST['answer1']) && $_POST['answer1'] == $quiz['answer1']) ? 1 : 0;
            $correct += (isset($_POST['answer2']) && $_POST['answer2'] == $quiz['answer2']) ? 1 : 0;
            $correct += (isset($_POST['answer3']) && $_POST['answer3'] == $quiz['answer3']) ? 1 : 0;

            $score = $correct;
            $passed = ($score >= 2) ? 1 : 0;

            // Save participation
            $this->model->saveParticipation($_SESSION['userId'], $id, $quiz['id'], $score, $passed);

            // Redirect back to the same page
            header("Location: ../view/front/view-formation.php?id=$id");
            exit();
        } else {
            require_once '../view/front/view-formation.php';
        }
    }
    public function downloadCertificate($quizName) {
        // Retrieve userId and userName from the session
        if (!isset($_SESSION['userId']) || !isset($_SESSION['userName'])) {
            die("User not logged in.");
        }
        $userId = $_SESSION['userId'];
        $userName = $_SESSION['userName'];
    
        // Generate the certificate
        $filePath = $this->model->generateCertificatePath($userId);
        CertificateGenerator::generateCertificate($userId, $userName, $quizName);
    
        // Redirect to the certificate file for download
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=certificate_user_$userId.pdf");
        readfile($filePath);
        exit;
    }
}

// Instantiate and run the controller
$controller = new ViewFormationFrontController();
$controller->index();