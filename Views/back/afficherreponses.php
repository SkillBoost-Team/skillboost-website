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

// Récupération de l'ID de la réclamation depuis l'URL
$reclamation_id = isset($_GET['reclamation_id']) ? intval($_GET['reclamation_id']) : 0;

if ($reclamation_id <= 0) {
    die("ID de réclamation invalide.");
}

// Récupération des détails de la réclamation
$stmt = $pdo->prepare("SELECT * FROM reclamations WHERE id = :id");
$stmt->execute([':id' => $reclamation_id]);
$reclamation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reclamation) {
    die("Réclamation non trouvée.");
}

// Traitement de la soumission du formulaire de réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_response') {
    $response_text = $_POST['response_text'];
    if (!empty($response_text)) {
        $stmt = $pdo->prepare("INSERT INTO reponses_reclamations (reclamation_id, admin_id, reponse, date_reponse) VALUES (:reclamation_id, :admin_id, :reponse, NOW())");
        $stmt->execute([
            ':reclamation_id' => $reclamation_id,
            ':admin_id' => 1, // Remplacer par l'ID de l'administrateur actuel
            ':reponse' => $response_text
        ]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $reclamation_id);
        exit();
    }
}

// Récupération des réponses pour la réclamation
$stmt = $pdo->prepare("SELECT * FROM reponses_reclamations WHERE reclamation_id = :reclamation_id ORDER BY date_reponse ASC");
$stmt->execute([':reclamation_id' => $reclamation_id]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonctions utilitaires
function getStatusClass($status) {
    $classes = [
        'new' => 'status-new',
        'in-progress' => 'status-in-progress',
        'resolved' => 'status-resolved',
        'rejected' => 'status-rejected'
    ];
    return $classes[$status] ?? '';
}

function getStatusText($status) {
    $texts = [
        'new' => 'Nouveau',
        'in-progress' => 'En cours',
        'resolved' => 'Résolu',
        'rejected' => 'Rejeté'
    ];
    return $texts[$status] ?? $status;
}

function getTypeText($type) {
    $texts = [
        'technique' => 'Technique',
        'paiement' => 'Paiement',
        'service' => 'Service client',
        'autre' => 'Autre'
    ];
    return $texts[$type] ?? $type;
}

function getPriorityText($priority) {
    $texts = [
        'high' => 'Haute',
        'medium' => 'Moyenne',
        'low' => 'Basse'
    ];
    return $texts[$priority] ?? $priority;
}

function formatDate($dateString) {
    return date('d/m/Y', strtotime($dateString));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Réponses à la Réclamation</title>
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
        .response-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background-color: #f9f9f9;
        }
        .response-author {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .response-date {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .response-text {
            margin-top: 0.5rem;
        }
        .response-form {
            margin-top: 2rem;
        }
        .status-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-new { background-color: #ffc107; color: #212529; }
        .status-in-progress { background-color: #17a2b8; color: white; }
        .status-resolved { background-color: #28a745; color: white; }
        .status-rejected { background-color: #dc3545; color: white; }
        .action-btn { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .priority-high { color: #dc3545; font-weight: bold; }
        .priority-medium { color: #fd7e14; font-weight: bold; }
        .priority-low { color: #28a745; font-weight: bold; }
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
                    <a href="admin-formations.html" class="nav-item nav-link">Formations</a>
                    <a href="admin-events.html" class="nav-item nav-link">Événements</a>
                    <a href="admin-investments.html" class="nav-item nav-link">Investissements</a>
                    <a href="admin-reclamations.php" class="nav-item nav-link">Réclamations</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-user-circle me-2"></i> Admin
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
        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Réponses à la Réclamation #<?= htmlspecialchars($reclamation['id']) ?></h2>
                            <div>
                                <a href="admin-reclamations.php" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i> Retour aux Réclamations
                                </a>
                            </div>
                        </div>
                        <!-- Détails de la Réclamation -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h5>Détails de la Réclamation</h5>
                                <hr>
                                <p><strong>Nom Complet:</strong> <?= htmlspecialchars($reclamation['full_name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($reclamation['email']) ?></p>
                                <p><strong>Sujet:</strong> <?= htmlspecialchars($reclamation['SUBJECT']) ?></p>
                                <p><strong>Type:</strong> <?= getTypeText($reclamation['TYPE']) ?></p>
                                <p><strong>Priorité:</strong> <span class="priority-<?= $reclamation['priority'] ?>"><?= getPriorityText($reclamation['priority']) ?></span></p>
                                <p><strong>Date de Création:</strong> <?= formatDate($reclamation['created_at']) ?></p>
                                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($reclamation['description'])) ?></p>
                                <p><strong>Statut:</strong> <span class="status-badge <?= getStatusClass($reclamation['STATUS']) ?>"><?= getStatusText($reclamation['STATUS']) ?></span></p>
                            </div>
                        </div>
                        <!-- Réponses -->
                        <div class="response-form">
                            <h5>Ajouter une Réponse</h5>
                            <hr>
                            <form method="post" action="">
                                <input type="hidden" name="action" value="add_response">
                                <textarea class="form-control mb-3" name="response_text" rows="4" placeholder="Entrez votre réponse ici..." required></textarea>
                                <button type="submit" class="btn btn-primary">Envoyer la Réponse</button>
                            </form>
                        </div>
                        <div class="mt-4">
                            <?php if (count($responses) > 0): ?>
                                <?php foreach ($responses as $response): ?>
                                    <div class="response-card">
                                        <div class="response-author">Admin #<?= htmlspecialchars($response['admin_id']) ?></div>
                                        <div class="response-date"><?= formatDate($response['date_reponse']) ?></div>
                                        <div class="response-text"><?= nl2br(htmlspecialchars($response['reponse'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucune réponse n'a encore été ajoutée pour cette réclamation.</p>
                            <?php endif; ?>
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
        // Gestion du spinner
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('spinner').classList.remove('show');
        });
    </script>
</body>
</html>