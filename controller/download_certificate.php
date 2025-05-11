<?php
session_start();
require_once '../config/config.php';
require_once '../model/view-formationFrontModel.php';
require_once 'CertificateGenerator.php';

// Retrieve quizId from POST data
$quizId = $_POST['quizId'] ?? null;
if (!$quizId) {
    die("Quiz ID is missing.");
}

// Instantiate the model
$model = new ViewFormationFrontModel($pdo);

// Fetch the quiz details to get the formation_id
$quiz = $model->getQuizById($quizId);
if (!$quiz) {
    die("Quiz not found.");
}

// Fetch the formation details to get the formation title
$formation = $model->getFormationById($quiz['id_formation']);
if (!$formation) {
    die("Formation not found.");
}

// Get the formation title for the certificate
$quizName = $formation['titre'];

// Retrieve userId and userName from the session
if (!isset($_SESSION['userId']) || !isset($_SESSION['userName'])) {
    die("User not logged in.");
}
$userId = $_SESSION['userId'];
$userName = $_SESSION['userName'];

// Generate the certificate
$filePath = $model->generateCertificatePath($userId);
CertificateGenerator::generateCertificate($userId, $userName, $quizName);

// Serve the certificate for download
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=certificate_user_$userId.pdf");
readfile($filePath);

// Redirect back to the view-formation.php page after download
header("Refresh: 0; url=../view/front/view-formation.php?id=" . $quiz['id_formation']);
exit;