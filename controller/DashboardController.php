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

    private function generateQuestions($title, $description, $niveau) {
        $api_key = "2fc9acc0f00c410590cda5da4c9cfe7d"; // Replace with your actual AIML API key
    $url = "https://api.aimlapi.com/v1/chat/completions";

    $system_prompt = "You are an expert in generating true/false questions for educational purposes.";
    $user_prompt = "
    Create exactly three true/false $niveau technical questions based on the following course:
    Course Title: $title
    Course Description: $description
    Output format (strictly follow this format):
    1. [Question] - [True/False]
    2. [Question] - [True/False]
    3. [Question] - [True/False]
    ";

    $data = [
        "model" => "gpt-4o", // Example of a more capable model
        "messages" => [
            ["role" => "system", "content" => $system_prompt],
            ["role" => "user", "content" => $user_prompt],
        ],
        "temperature" => 0.7, // Control randomness (lower = more deterministic)
        "max_tokens" => 10000, // Limit the response length
    ];

    // Step 3: Send the API request using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Step 4: Handle the API response
    if ($http_code !== 200 && $http_code !== 201) {
        echo "Error calling AIML API: HTTP $http_code\n";
        echo "Response: $response\n";
        return [];
    }

    $responseData = json_decode($response, true);
    if (!isset($responseData['choices'][0]['message']['content'])) {
        echo "Error parsing AIML response.";
        echo "Raw Response: " . print_r($responseData, true); // Log the full response
        return [];
    }

    $generatedText = $responseData['choices'][0]['message']['content'];

    // Step 5: Parse the generated text into questions
    $questions = [];
    $lines = explode("\n", trim($generatedText));


    foreach ($lines as $line) {
        // Use a more flexible regular expression
        if (preg_match('/^\d+\.\s*(.*?)\s*-\s*(Vrai|Faux|True|False)$/i', trim($line), $matches)) {
            $questions[] = [
                'question' => trim($matches[1]),
                'answer' => strtolower(trim($matches[2])) === 'true' || strtolower(trim($matches[2])) === 'vrai',
            ];
        }
    }

    return $questions;

    }

    /**
     * Automatically generate and save quiz questions for a formation.
     */
    public function generateQuiz($formationId) {
        // Fetch the formation details
        $formation = $this->model->getFormationById($formationId);
        if (!$formation) {
            echo "Formation not found.";
            return;
        }

        // Generate questions using Hugging Face API
        $questions = $this->generateQuestions($formation['titre'], $formation['description'], $formation['niveau']);
        if (empty($questions)) {
            echo "No questions were generated.";
            return;
        }

        // Save the quiz to the database
        $quizData = [
            'id_formation' => $formationId,
            'question1' => $questions[0]['question'] ?? null,
            'answer1' => $questions[0]['answer'] ?? false,
            'question2' => $questions[1]['question'] ?? null,
            'answer2' => $questions[1]['answer'] ?? false,
            'question3' => $questions[2]['question'] ?? null,
            'answer3' => $questions[2]['answer'] ?? false,
        ];

        // Check if a quiz already exists for this formation
        $existingQuiz = $this->model->getQuizByFormationId($formationId);
        if ($existingQuiz) {
            // Update the existing quiz
            $this->model->updateQuiz($existingQuiz['id'], $quizData['question1'], $quizData['answer1'], $quizData['question2'], $quizData['answer2'], $quizData['question3'], $quizData['answer3']);
        } else {
            // Add a new quiz
            $this->model->addQuiz($quizData['id_formation'], $quizData['question1'], $quizData['answer1'], $quizData['question2'], $quizData['answer2'], $quizData['question3'], $quizData['answer3']);
        }

        header("location: ../view/back/dashboard.php");
    }

    /**
     * Export all formations to a PDF file
     */
    public function exportPDF() {
        require_once __DIR__ . '/../fpdf/fpdf.php';
        $formations = $this->model->getAllFormations();
        
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetMargins(10, 15, 10);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 15, iconv('UTF-8', 'windows-1252', 'Liste des Formations'), 0, 1, 'C');
        $pdf->Ln(2);
        
        // Table header styling
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(52, 152, 219); // Blue
        $pdf->SetTextColor(255);
        $header = ['Titre', 'Description', 'Niveau', 'Durée', 'Date Création', 'Certificat'];
        $widths = [38, 60, 22, 15, 30, 20];
        foreach ($header as $i => $col) {
            $pdf->Cell($widths[$i], 10, iconv('UTF-8', 'windows-1252', $col), 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Table body
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0);
        $fill = false;
        foreach ($formations as $formation) {
            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255); // Light gray/white
            $pdf->Cell($widths[0], 8, iconv('UTF-8', 'windows-1252', $formation['titre']), 1, 0, 'L', true);
            // Description with MultiCell
            $x = $pdf->GetX(); $y = $pdf->GetY();
            $pdf->MultiCell($widths[1], 8, iconv('UTF-8', 'windows-1252', $formation['description']), 1, 'L', true);
            $pdf->SetXY($x + $widths[1], $y);
            $pdf->Cell($widths[2], 8, iconv('UTF-8', 'windows-1252', $formation['niveau']), 1, 0, 'C', true);
            $pdf->Cell($widths[3], 8, $formation['duree'], 1, 0, 'C', true);
            $pdf->Cell($widths[4], 8, date('d/m/Y', strtotime($formation['date_creation'])), 1, 0, 'C', true);
            $pdf->Cell($widths[5], 8, iconv('UTF-8', 'windows-1252', $formation['certificat']), 1, 0, 'C', true);
            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->Output('D', 'formations.pdf');
        exit();
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
    case 'generate':
        if ($formationId) {
            $controller = new DashboardController();
            $controller->generateQuiz($formationId);
        } else {
            header('Location: /index.php?action=index');
            exit();
        }
        break;
    case 'exportPDF':
        $controller = new DashboardController();
        $controller->exportPDF();
        break;
    default:
        header('Location: dashboard.php');
        exit();
}

