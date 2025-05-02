<?php
// Include the configuration file to connect to the database
require_once '../../config/config.php';

// Include the view-formationFrontModel to interact with the database
require_once '../../model/view-formationFrontModel.php';

// Create an instance of the view-formationFrontModel
$model = new ViewFormationFrontModel($pdo);

// Get the formation ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: formations.php');
    exit();
}

// Fetch the formation details
$formation = $model->getFormationById($id);

if (!$formation) {
    header('Location: formations.php');
    exit();
}

// Fetch the quiz details for the given formation ID
$quiz = $model->getQuizByFormationId($id);

// Initialize questions and answers if no quiz exists
$question1 = $quiz['question1'] ?? '';
$question2 = $quiz['question2'] ?? '';
$question3 = $quiz['question3'] ?? '';

// Function to format the date
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Handle quiz submission
$userAnswers = [];
$score = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswers = [
        'answer1' => isset($_POST['answer1']) ? (bool)$_POST['answer1'] : false,
        'answer2' => isset($_POST['answer2']) ? (bool)$_POST['answer2'] : false,
        'answer3' => isset($_POST['answer3']) ? (bool)$_POST['answer3'] : false
    ];

    if ($quiz) {
        $score = ($quiz['answer1'] == $userAnswers['answer1']) + 
                 ($quiz['answer2'] == $userAnswers['answer2']) + 
                 ($quiz['answer3'] == $userAnswers['answer3']);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Détails de la Formation</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Styles personnalisés */
        .details-container {
            padding: 2rem 0;
            min-height: calc(100vh - 300px);
        }
        .details-card {
            border-radius: 10px;
            padding: 1.5rem;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .details-card h3 {
            margin-bottom: 1rem;
        }
        .details-card p {
            margin-bottom: 0.5rem;
        }
        .action-btn { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .admin-notes {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }
        .quiz-section {
            margin-top: 2rem;
        }
        .quiz-form {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .quiz-form h3 {
            margin-bottom: 1rem;
        }
        .quiz-form .form-check {
            margin-bottom: 0.5rem;
        }


                /* Sidebar Styling */
.sidebar {
    width: 280px; /* Full width of the sidebar */
    min-height: 100vh;
    background: #343a40;
    color: white;
    transition: all 0.3s;
    position: fixed;
    z-index: 1000;
    left: -260px; /* Hide the sidebar by moving it off-screen */
    top: 0;
}

.sidebar-header {
    padding: 20px;
    background: #212529;
    text-align: center;
}

.sidebar-header img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    border: 3px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h4 {
    color: #fff;
    margin-bottom: 0;
}

.sidebar-header p {
    color: #adb5bd;
    font-size: 0.8rem;
    margin-bottom: 0;
}

.sidebar-menu {
    padding: 20px 0;
}

.sidebar-menu a {
    display: block;
    padding: 12px 20px;
    color: #adb5bd;
    text-decoration: none;
    transition: all 0.3s;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.sidebar-menu a i {
    margin-right: 10px;
}

/* Trigger Zone for Hover */
.trigger-zone {
    position: fixed;
    top: 0;
    left: 0;
    width: 20px; /* Width of the hover-sensitive area */
    height: 100vh; /* Full height of the viewport */
    z-index: 1001;
    cursor: pointer; /* Optional: Change cursor to indicate interactivity */
}

/* Show Sidebar on Hover */
.trigger-zone:hover + .sidebar,
.sidebar:hover {
    left: 0; /* Bring the sidebar back into view */
}

    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white navbar-sticky py-3 py-lg-0 px-4 px-lg-5">
        <a href="index.html" class="navbar-brand p-0">
            <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>SkillBoost</h1>
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-0">
                <a href="index.html" class="nav-item nav-link">Accueil</a>
                <a href="about.html" class="nav-item nav-link">À propos</a>
                <a href="services.html" class="nav-item nav-link">Services</a>
                <a href="projects.html" class="nav-item nav-link">Projets</a>
                <a href="formations.php" class="nav-item nav-link active">Formations</a>
                <a href="events.html" class="nav-item nav-link">Événements</a>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white mb-4 animated slideInDown">Détails de la Formation</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="formations.php">Formations</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Détails</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->
    <!-- View Formation Content -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- Formation Details Card -->
                    <div class="card shadow-sm details-card">
                        <div class="card-body">
                            <h3> <?= htmlspecialchars($formation['titre']) ?></h3>
                            <p><strong>Description:</strong> <?= htmlspecialchars($formation['description']) ?></p>
                            <p><strong>Niveau:</strong> <?= htmlspecialchars($formation['niveau']) ?></p>
                            <p><strong>Durée (heures):</strong> <?= htmlspecialchars($formation['duree']) ?></p>
                            <p><strong>Date de création:</strong> <?= htmlspecialchars(formatDate($formation['date_creation'])) ?></p>
                            <p><strong>Certificat:</strong> <?= htmlspecialchars($formation['certificat']) ?></p>
                            <div class="mt-4">
                                <a href="formations.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Retour
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Quiz Section -->
                    <?php if ($quiz): ?>
                        <div class="card shadow-sm details-card quiz-section">
                            <div class="card-body">
                                <h3>Quiz</h3>
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label class="form-label"><?= htmlspecialchars($question1) ?></label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer1" id="answer1_true" value="1" required>
                                            <label class="form-check-label" for="answer1_true">
                                                Vrai
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer1" id="answer1_false" value="0" required>
                                            <label class="form-check-label" for="answer1_false">
                                                Faux
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?= htmlspecialchars($question2) ?></label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer2" id="answer2_true" value="1" required>
                                            <label class="form-check-label" for="answer2_true">
                                                Vrai
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer2" id="answer2_false" value="0" required>
                                            <label class="form-check-label" for="answer2_false">
                                                Faux
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?= htmlspecialchars($question3) ?></label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer3" id="answer3_true" value="1" required>
                                            <label class="form-check-label" for="answer3_true">
                                                Vrai
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer3" id="answer3_false" value="0" required>
                                            <label class="form-check-label" for="answer3_false">
                                                Faux
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" type="submit">Soumettre le Quiz</button>
                                    </div>
                                </form>
                                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                                    <div class="admin-notes mt-4">
                                        <h4>Résultats du Quiz</h4>
                                        <p><strong>Question 1:</strong> <?= htmlspecialchars($question1) ?> - Réponse: <?= $quiz['answer1'] ? 'Vrai' : 'Faux' ?></p>
                                        <p><strong>Question 2:</strong> <?= htmlspecialchars($question2) ?> - Réponse: <?= $quiz['answer2'] ? 'Vrai' : 'Faux' ?></p>
                                        <p><strong>Question 3:</strong> <?= htmlspecialchars($question3) ?> - Réponse: <?= $quiz['answer3'] ? 'Vrai' : 'Faux' ?></p>
                                        <p><strong>Score:</strong> <?= htmlspecialchars($score) ?>/3</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4">
                    <!-- Sidebar Widgets -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3">
                            <h5 class="m-0">Informations supplémentaires</h5>
                        </div>
                        <div class="card-body">
                            <p>Assurez-vous de bien répondre aux questions du quiz.</p>
                            <p>Les résultats du quiz seront affichés après soumission.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light mt-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-8 col-md-6">
                    <div class="row gx-5">
                        <div class="col-lg-4 col-md-12 pt-5 mb-5">
                            <div class="section-title section-title-sm position-relative pb-3 mb-4">
                                <h3 class="text-light mb-0">Contact</h3>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                <p class="mb-0">123 Rue Tunis,Tunisie, TN</p>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-envelope-open text-primary me-2"></i>
                                <p class="mb-0">SkillBoost@gmail.com</p>
                            </div>
                            <div class="d-flex mb-2">
                                <i class="bi bi-telephone text-primary me-2"></i>
                                <p class="mb-0">+216 90 044 054</p>
                            </div>
                            <div class="d-flex mt-4">
                                <a class="btn btn-primary btn-square me-2" href="#"><i class="fab fa-twitter fw-normal"></i></a>
                                <a class="btn btn-primary btn-square me-2" href="#"><i class="fab fa-facebook-f fw-normal"></i></a>
                                <a class="btn btn-primary btn-square me-2" href="#"><i class="fab fa-linkedin-in fw-normal"></i></a>
                                <a class="btn btn-primary btn-square" href="#"><i class="fab fa-instagram fw-normal"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid text-white" style="background: #061429;">
        <div class="container text-center">
            <div class="row justify-content-end">
                <div class="col-lg-8 col-md-6">
                    <div class="d-flex align-items-center justify-content-center" style="height: 75px;">
                        <p class="mb-0">&copy; <a class="text-white border-bottom" href="#">SkillBoost</a>. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->
    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script>
        // Simuler un délai de chargement pour le spinner
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.getElementById('spinner').classList.remove('show');
            }, 500);
        });
    </script>
</body>
</html>