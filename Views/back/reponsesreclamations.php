<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'skillboost';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer l'ID de la réclamation depuis l'URL
if (!isset($_GET['reclamation_id']) || !is_numeric($_GET['reclamation_id'])) {
    die("ID de réclamation invalide.");
}
$reclamationId = intval($_GET['reclamation_id']);

// Récupérer les détails de la réclamation
$stmt = $pdo->prepare("SELECT * FROM reclamations WHERE id = :id");
$stmt->execute([':id' => $reclamationId]);
$reclamation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reclamation) {
    die("Réclamation introuvable.");
}

// Ajouter une nouvelle réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['response'])) {
        $adminName = htmlspecialchars($_POST['admin_name']);
        $response = htmlspecialchars($_POST['response']);

        // Insérer la réponse dans la base de données
        $stmt = $pdo->prepare("INSERT INTO reponses (reclamation_id, admin_name, response) VALUES (:reclamation_id, :admin_name, :response)");
        $stmt->execute([
            ':reclamation_id' => $reclamationId,
            ':admin_name' => $adminName,
            ':response' => $response
        ]);

        // Redirection pour éviter la resoumission du formulaire
        header("Location: reponsesreclamations.php?reclamation_id=" . $reclamationId);
        exit();
    }
}

// Récupérer toutes les réponses pour cette réclamation
$stmt = $pdo->prepare("SELECT * FROM reponses WHERE reclamation_id = :reclamation_id ORDER BY created_at DESC");
$stmt->execute([':reclamation_id' => $reclamationId]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatDate($dateString) {
    return date('d/m/Y H:i', strtotime($dateString));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Réponses - Réclamation #<?= $reclamationId ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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
        .response-card {
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f8f9fa;
        }
        .response-header {
            font-size: 0.875rem;
            color: #6c757d;
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

    <!-- Navbar Start -->
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
                    <a href="admin-formations.html" class="nav-item nav-link">Formations</a>
                    <a href="admin-events.html" class="nav-item nav-link">Événements</a>
                    <a href="admin-investments.html" class="nav-item nav-link">Investissements</a>
                    <a href="admin-reclamations.php" class="nav-item nav-link active">Réclamations</a>
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
    </div>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Réponses aux Réclamations</h1>
                    <p class="fs-4 text-white mb-4 animated slideInDown">Gérez les réponses pour la réclamation #<?= $reclamationId ?>.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contenu principal -->
    <div class="container mt-5">
        <h2 class="mb-4">Réponses pour la réclamation #<?= $reclamationId ?></h2>

        <!-- Détails de la réclamation -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Détails de la réclamation</h5>
                <p><strong>Nom :</strong> <?= htmlspecialchars($reclamation['full_name']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($reclamation['email']) ?></p>
                <p><strong>Sujet :</strong> <?= htmlspecialchars($reclamation['SUBJECT']) ?></p>
                <p><strong>Description :</strong> <?= htmlspecialchars($reclamation['description']) ?></p>
                <p><strong>Statut :</strong> <?= htmlspecialchars($reclamation['STATUS']) ?></p>
            </div>
        </div>

        <!-- Formulaire pour ajouter une réponse -->
        <form method="post" action="" class="mb-4">
            <h5>Ajouter une réponse</h5>
            <div class="mb-3">
                <label for="admin_name" class="form-label">Nom de l'admin *</label>
                <input type="text" class="form-control" id="admin_name" name="admin_name" required>
            </div>
            <div class="mb-3">
                <label for="response" class="form-label">Réponse *</label>
                <textarea class="form-control" id="response" name="response" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>

        <!-- Liste des réponses -->
        <?php if (empty($responses)): ?>
            <p>Aucune réponse pour cette réclamation.</p>
        <?php else: ?>
            <h5>Liste des réponses</h5>
            <?php foreach ($responses as $response): ?>
                <div class="response-card">
                    <div class="response-header">
                        <span><?= htmlspecialchars($response['admin_name']) ?></span>
                        <span class="float-end"><?= formatDate($response['created_at']) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($response['response'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Bouton Retour -->
        <a href="reclamations.php" class="btn btn-secondary mt-3">Retour</a>
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
                                <p class="mb-0">123 Rue Tunis, Tunisie, TN</p>
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
        // Cacher le spinner après chargement
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('spinner').classList.remove('show');
        });
    </script>
</body>
</html>