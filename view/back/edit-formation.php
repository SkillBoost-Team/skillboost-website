<?php
// Include the configuration file to connect to the database
require_once '../../config/config.php';

// Include the DashboardModel to interact with the database
require_once '../../model/DashboardModel.php';

// Create an instance of the DashboardModel
$model = new DashboardModel($pdo);

// Get the formation ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: dashboard.php');
    exit();
}

// Fetch the formation details
$formation = $model->getFormationById($id);

if (!$formation) {
    header('Location: dashboard.php');
    exit();
}

// Fetch the quiz details for the given formation ID
$quiz = $model->getQuizByFormationId($id);

// Initialize questions and answers if no quiz exists
$question1 = $quiz['question1'] ?? '';
$answer1 = $quiz['answer1'] ?? false;
$question2 = $quiz['question2'] ?? '';
$answer2 = $quiz['answer2'] ?? false;
$question3 = $quiz['question3'] ?? '';
$answer3 = $quiz['answer3'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $duree = $_POST['duree'];
    $niveau = $_POST['niveau'];
    $certificat = isset($_POST['certificat']) ? $_POST['certificat'] : 'Non';

    // Update the formation in the database
    $model->updateFormation($id, $titre, $description, $duree, $niveau, $certificat);

    // Update the quiz in the database
    $question1 = $_POST['question1'];
    $answer1 = isset($_POST['answer1']);
    $question2 = $_POST['question2'];
    $answer2 = isset($_POST['answer2']);
    $question3 = $_POST['question3'];
    $answer3 = isset($_POST['answer3']);

    if ($quiz) {
        // Update the existing quiz
        $model->updateQuiz($quiz['id'], $question1, $answer1, $question2, $answer2, $question3, $answer3);
    } else {
        // Add a new quiz
        $model->addQuiz($id, $question1, $answer1, $question2, $answer2, $question3, $answer3);
    }

    // Redirect to the dashboard
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Modifier une Formation</title>
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
        .quiz-section {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    <!-- Spinner End -->
    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Bloc E, Esprit , Cite La Gazelle</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+216 90 044 054</small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>SkillBoost@gmail.com</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="text-light"><i class="fa fa-user-shield me-2"></i>Espace Administrateur</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->
    <!-- Navbar & Carousel Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="index.html" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>SkillBoost</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="admin-dashboard.html" class="nav-item nav-link">Tableau de bord</a>
                    <a href="admin-users.html" class="nav-item nav-link">Utilisateurs</a>
                    <a href="admin-projects.html" class="nav-item nav-link">Projets</a>
                    <a href="admin-formations.html" class="nav-item nav-link active">Formations</a>
                    <a href="admin-events.html" class="nav-item nav-link">Événements</a>
                    <a href="admin-investments.html" class="nav-item nav-link">Investissements</a>
                    <a href="admin-reclamations.html" class="nav-item nav-link">Réclamations</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-user-circle me-1"></i> Admin
                        </a>
                        <div class="dropdown-menu m-0">
                            <a href="admin-profile.html" class="dropdown-item">Profil</a>
                            <a href="admin-settings.html" class="dropdown-item">Paramètres</a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.html" class="dropdown-item">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Edit Formation Content -->
        <div class="details-container">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0"><i class="fas fa-book me-2"></i>Modifier une Formation</h2>
                            <div>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Retour
                                </a>
                            </div>
                        </div>
                        <!-- Formation Form -->
                        <div class="bg-light rounded p-5">
                            <div class="section-title section-title-sm position-relative pb-3 mb-4">
                                <h3 class="mb-0">Formation</h3>
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($formation['id']) ?>">
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Titre *</label>
                                        <input type="text" class="form-control bg-white border-0" name="titre" value="<?= htmlspecialchars($formation['titre']) ?>" style="height: 55px;" required>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Niveau *</label>
                                        <select class="form-select bg-white border-0" name="niveau" style="height: 55px;" required>
                                            <option value="Débutant" <?= ($formation['niveau'] === 'Débutant') ? 'selected' : '' ?>>Débutant</option>
                                            <option value="Intermédiaire" <?= ($formation['niveau'] === 'Intermédiaire') ? 'selected' : '' ?>>Intermédiaire</option>
                                            <option value="Avancé" <?= ($formation['niveau'] === 'Avancé') ? 'selected' : '' ?>>Avancé</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Description *</label>
                                        <textarea class="form-control bg-white border-0" name="description" rows="5" style="height: auto;" required><?= htmlspecialchars($formation['description']) ?></textarea>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Durée (heures) *</label>
                                        <input type="number" class="form-control bg-white border-0" name="duree" value="<?= htmlspecialchars($formation['duree']) ?>" style="height: 55px;" required>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Certificat</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="certificat" id="certificatOui" value="Oui" <?= ($formation['certificat'] === 'Oui') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="certificatOui">
                                                Oui
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="certificat" id="certificatNon" value="Non" <?= ($formation['certificat'] === 'Non') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="certificatNon">
                                                Non
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Quiz Section -->
                                    <div class="col-12 quiz-section">
                                        <div class="section-title section-title-sm position-relative pb-3 mb-4">
                                            <h3 class="mb-0">Quiz</h3>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Question 1 *</label>
                                            <input type="text" class="form-control bg-white border-0" name="question1" value="<?= htmlspecialchars($question1) ?>" style="height: 55px;" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Réponse 1</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="answer1" id="answer1" <?= ($answer1) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="answer1">
                                                    Vrai
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Question 2 *</label>
                                            <input type="text" class="form-control bg-white border-0" name="question2" value="<?= htmlspecialchars($question2) ?>" style="height: 55px;" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Réponse 2</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="answer2" id="answer2" <?= ($answer2) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="answer2">
                                                    Vrai
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Question 3 *</label>
                                            <input type="text" class="form-control bg-white border-0" name="question3" value="<?= htmlspecialchars($question3) ?>" style="height: 55px;" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Réponse 3</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="answer3" id="answer3" <?= ($answer3) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="answer3">
                                                    Vrai
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" type="submit">Enregistrer</button>
                                    </div>
                                </div>
                            </form>
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