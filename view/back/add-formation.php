<?php
// Include the configuration file to connect to the database
require_once '../../config/config.php';

// Include the DashboardModel to interact with the database
require_once '../../model/DashboardModel.php';

// Create an instance of the DashboardModel
$model = new DashboardModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $quizErrors = [];

    // Validate titre
    if (empty($_POST['titre'])) {
        $errors[] = "Le titre est requis.";
    } else {
        $titre = htmlspecialchars(trim($_POST['titre']));
        if (strlen($titre) < 3 || strlen($titre) > 255) {
            $errors[] = "Le titre doit contenir entre 3 et 255 caractères.";
        }
        if (!preg_match('/^[a-zA-Z\s]+$/', $titre)) {
            $errors[] = "Le titre ne doit contenir que des lettres et des espaces.";
        }
    }

    // Validate description
    if (empty($_POST['description'])) {
        $errors[] = "La description est requise.";
    } else {
        $description = htmlspecialchars(trim($_POST['description']));
        if (strlen($description) < 10 || strlen($description) > 1000) {
            $errors[] = "La description doit contenir entre 10 et 1000 caractères.";
        }
    }

    // Validate duree
    if (empty($_POST['duree'])) {
        $errors[] = "La durée est requise.";
    } else {
        $duree = htmlspecialchars(trim($_POST['duree']));
        if (!is_numeric($duree) || $duree <= 0) {
            $errors[] = "La durée doit être un nombre positif.";
        }
    }

    // Validate niveau
    if (empty($_POST['niveau'])) {
        $errors[] = "Le niveau est requis.";
    } else {
        $niveau = htmlspecialchars(trim($_POST['niveau']));
        if (!in_array($niveau, ['Débutant', 'Intermédiaire', 'Avancé'])) {
            $errors[] = "Le niveau doit être Débutant, Intermédiaire ou Avancé.";
        }
    }

    // Validate certificat
    $certificat = isset($_POST['certificat']) ? 'Oui' : 'Non';

    // If no errors, proceed to add the formation to the database
    if (empty($errors)) {
        $formationId = $model->addFormation($titre, $description, $duree, $niveau, $certificat);
        if ($formationId) {
            // Validate quiz questions
            // Validate question1 and answer1
            if (empty($_POST['question1'])) {
                $quizErrors[] = "La question 1 est requise.";
            } else {
                $question1 = htmlspecialchars(trim($_POST['question1']));
                if (strlen($question1) < 5 || strlen($question1) > 255) {
                    $quizErrors[] = "La question 1 doit contenir entre 5 et 255 caractères.";
                }
                if (!preg_match('/^[a-zA-Z\s]+$/', $question1)) {
                    $quizErrors[] = "La question 1 ne doit contenir que des lettres et des espaces.";
                }
            }

            $answer1 = isset($_POST['answer1']) ? true : false;

            // Validate question2 and answer2
            if (empty($_POST['question2'])) {
                $quizErrors[] = "La question 2 est requise.";
            } else {
                $question2 = htmlspecialchars(trim($_POST['question2']));
                if (strlen($question2) < 5 || strlen($question2) > 255) {
                    $quizErrors[] = "La question 2 doit contenir entre 5 et 255 caractères.";
                }
                if (!preg_match('/^[a-zA-Z\s]+$/', $question2)) {
                    $quizErrors[] = "La question 2 ne doit contenir que des lettres et des espaces.";
                }
            }

            $answer2 = isset($_POST['answer2']) ? true : false;

            // Validate question3 and answer3
            if (empty($_POST['question3'])) {
                $quizErrors[] = "La question 3 est requise.";
            } else {
                $question3 = htmlspecialchars(trim($_POST['question3']));
                if (strlen($question3) < 5 || strlen($question3) > 255) {
                    $quizErrors[] = "La question 3 doit contenir entre 5 et 255 caractères.";
                }
                if (!preg_match('/^[a-zA-Z\s]+$/', $question3)) {
                    $quizErrors[] = "La question 3 ne doit contenir que des lettres et des espaces.";
                }
            }

            $answer3 = isset($_POST['answer3']) ? true : false;

            // If no quiz errors, proceed to add the quiz questions to the database
            if (empty($quizErrors)) {
                $result = $model->addQuiz($formationId, $question1, $answer1, $question2, $answer2, $question3, $answer3);
                if ($result) {
                     //echo "<p>Quiz ajouté avec succès !</p>";
                    // Optionally, redirect to another page or reset the form
                } else {
                     //echo "<p>Une erreur s'est produite lors de l'ajout du quiz.</p>";
                }
            } else {
                // Display quiz errors
                foreach ($quizErrors as $error) {
                     //echo "<p style='color: red;'>$error</p>";
                }
            }
        } else {
             //echo "<p>Une erreur s'est produite lors de l'ajout de la formation.</p>";
        }
    } else {
        // Display formation errors
        foreach ($errors as $error) {
             //echo "<p style='color: red;'>$error</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Ajouter une Formation</title>
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
        .dashboard-container {
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
        <!-- Add Formation Content -->
        <div class="dashboard-container">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0"><i class="fas fa-book me-2"></i>Ajouter une Formation</h2>
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
                            <?php if (!empty($errors)): ?>
                                <div>
                                    <?php foreach ($errors as $error): ?>
                                        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Titre *</label>
                                        <input type="text" class="form-control bg-white border-0" name="titre" placeholder="Titre de la formation" style="height: 55px;"  >
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Niveau *</label>
                                        <select class="form-select bg-white border-0" name="niveau" style="height: 55px;"  >
                                            <option value="Débutant">Débutant</option>
                                            <option value="Intermédiaire">Intermédiaire</option>
                                            <option value="Avancé">Avancé</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Description *</label>
                                        <textarea class="form-control bg-white border-0" name="description" rows="5" placeholder="Description de la formation"  ></textarea>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Durée (heures) *</label>
                                        <input type="number" class="form-control bg-white border-0" name="duree" placeholder="Durée en heures" style="height: 55px;"  >
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label">Certificat</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="certificat" id="certificatOui" value="Oui"  >
                                            <label class="form-check-label" for="certificatOui">
                                                Oui
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="certificat" id="certificatNon" value="Non"  >
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
                                            <input type="text" class="form-control bg-white border-0" name="question1" placeholder="Question 1" style="height: 55px;"  >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Réponse 1</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="answer1" id="answer1">
                                                <label class="form-check-label" for="answer1">
                                                    Vrai
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Question 2 *</label>
                                            <input type="text" class="form-control bg-white border-0" name="question2" placeholder="Question 2" style="height: 55px;"  >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Réponse 2</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="answer2" id="answer2">
                                                <label class="form-check-label" for="answer2">
                                                    Vrai
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Question 3 *</label>
                                            <input type="text" class="form-control bg-white border-0" name="question3" placeholder="Question 3" style="height: 55px;"  >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Réponse 3</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="answer3" id="answer3">
                                                <label class="form-check-label" for="answer3">
                                                    Vrai
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" type="submit">Ajouter la Formation</button>
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