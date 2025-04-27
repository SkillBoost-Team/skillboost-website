<?php
// Initialize $message and $alert_class with default values
$message = '';
$alert_class = '';

// Connexion à la base de données
try {
    $db = new PDO('mysql:host=localhost;dbname=skillboost', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Récupération des détails de la réclamation et des réponses si un ID est fourni dans l'URL
$reclamation_data = null;
$reponses = [];
if (isset($_GET['id'])) {
    $reclamation_id = intval($_GET['id']);
    if ($reclamation_id <= 0) {
        $message = "ID de réclamation invalide.";
        $alert_class = "danger";
    } else {
        try {
            // Récupérer la réclamation
            $stmt = $db->prepare("SELECT * FROM reclamations WHERE id = :id");
            $stmt->execute(['id' => $reclamation_id]);
            $reclamation = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($reclamation) {
                // Récupérer les réponses associées
                $stmt = $db->prepare("SELECT * FROM reponses_reclamations WHERE reclamation_id = :id ORDER BY date_reponse DESC");
                $stmt->execute(['id' => $reclamation_id]);
                $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $reclamation_data = [
                    'reclamation' => $reclamation,
                    'reponses' => $reponses
                ];
            } else {
                $message = "Aucune réclamation trouvée avec cet ID.";
                $alert_class = "warning";
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de la récupération des données: " . $e->getMessage();
            $alert_class = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rechercher Réclamation - SkillBoost</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        /* Styles personnalisés */
        .reponse-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e1e1e1;
            margin-top: 20px;
        }
        .reponse-container .card {
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }
        .reponse-container .card-header {
            border-radius: 5px 5px 0 0 !important;
            font-size: 0.9rem;
        }
        .reponse-container p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .reponse-container h4 {
            color: #061429;
            border-bottom: 2px solid #061429;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .reponse-container h5 {
            color: #061429;
            margin-top: 25px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Bloc E, Esprit, Cite La Gazelle</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+216 90 044 054</small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>SkillBoost@gmail.com</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>SkillBoost</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link">Accueil</a>
                    <a href="login.php" class="nav-item nav-link">Connexion</a>
                    <a href="#" class="nav-item nav-link">Projets</a>
                    <a href="Formations.php" class="nav-item nav-link">Formations</a>
                    <a href="evenements.php" class="nav-item nav-link">Événements</a>
                    <a href="gestionInvestissement.php" class="nav-item nav-link">Investissements</a>
                    <a href="reclamations.php" class="nav-item nav-link">Réclamations</a>
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
                    <h1 class="display-3 text-white animated slideInDown">Rechercher une Réclamation</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Accueil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Recherche Réclamation</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contenu Principal Start -->
    <div class="container mt-5">
        <h2 class="mb-4">Rechercher une Réclamation</h2>
        <form method="GET" action="recherchereclamation.php" class="mb-4">
            <div class="input-group">
                <input type="number" class="form-control" name="id" placeholder="Entrez votre numéro de réclamation" value="<?= isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '' ?>">
                <button class="btn btn-primary" type="submit">Rechercher</button>
            </div>
        </form>

        <?php if ($message): ?>
            <div class="alert alert-<?= $alert_class ?> alert-dismissible fade show mb-4">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($reclamation_data): ?>
            <div class="reponse-container">
                <h4 class="mb-3">Réclamation #<?= $reclamation_data['reclamation']['id'] ?></h4>
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>Détails de la Réclamation</strong>
                    </div>
                    <div class="card-body">
                        <p><strong>Nom Complet:</strong> <?= htmlspecialchars($reclamation_data['reclamation']['full_name'] ?? 'Non spécifié') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($reclamation_data['reclamation']['email'] ?? 'Non spécifié') ?></p>
                        <p><strong>Sujet:</strong> <?= htmlspecialchars($reclamation_data['reclamation']['subject'] ?? 'Non spécifié') ?></p>
                        <p><strong>Type:</strong> <?= htmlspecialchars($reclamation_data['reclamation']['type'] ?? 'Non spécifié') ?></p>
                        <p><strong>Priorité:</strong> <?= htmlspecialchars($reclamation_data['reclamation']['priority'] ?? 'Non spécifiée') ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($reclamation_data['reclamation']['created_at'] ?? 'Non spécifiée') ?></p>
                        <p><strong>Statut:</strong> <?= htmlspecialchars($reclamation_data['reclamation']['status'] ?? 'Non spécifié') ?></p>
                    </div>
                </div>
                <?php if (!empty($reclamation_data['reponses'])): ?>
                    <h5 class="mb-3">Réponses de l'administration</h5>
                    <?php foreach ($reclamation_data['reponses'] as $reponse): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <strong>Réponse du <?= htmlspecialchars($reponse['date_reponse'] ?? 'Date inconnue') ?></strong>
                            </div>
                            <div class="card-body">
                                <p><?= nl2br(htmlspecialchars($reponse['reponse'] ?? 'Aucune réponse')) ?></p>
                                <?php if (!empty($reponse['admin_id'])): ?>
                                    <p class="text-muted"><small>Réponse de l'administrateur #<?= htmlspecialchars($reponse['admin_id']) ?></small></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        Il n'y a pas encore de réponse pour cette réclamation.
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (isset($_GET['id'])): ?>
            <div class="alert alert-warning">
                Aucune réclamation trouvée avec cet ID.
            </div>
        <?php endif; ?>
    </div>
    <!-- Contenu Principal End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">SkillBoost</h4>
                    <p>Plateforme complète pour l'entrepreneuriat et l'investissement.</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Liens rapides</h4>
                    <a class="btn btn-link" href="index.php">Accueil</a>
                    <a class="btn btn-link" href="Formations.php">Formations</a>
                    <a class="btn btn-link" href="evenements.php">Événements</a>
                    <a class="btn btn-link" href="reclamations.php">Réclamations</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p><i class="fa fa-map-marker-alt me-3"></i>Bloc E, Esprit, Cite La Gazelle</p>
                    <p><i class="fa fa-phone-alt me-3"></i>+216 90 044 054</p>
                    <p><i class="fa fa-envelope me-3"></i>SkillBoost@gmail.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Abonnez-vous à notre newsletter pour les dernières actualités.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Votre email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">S'inscrire</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">SkillBoost</a>, Tous droits réservés.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="#">Accueil</a>
                            <a href="#">Cookies</a>
                            <a href="#">Aide</a>
                            <a href="#">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>