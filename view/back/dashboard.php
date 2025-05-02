<?php
// Include the configuration file to connect to the database
require_once '../../config/config.php';

// Include the DashboardModel to interact with the database
require_once '../../model/DashboardModel.php';

// Create an instance of the DashboardModel
$model = new DashboardModel($pdo);

// Fetch all formations from the database
$formations = $model->getAllFormations();

// Function to format the date
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Function to get the status badge class
function getStatusClass($status) {
    return match ($status) {
        'new' => 'status-new',
        'in-progress' => 'status-in-progress',
        'resolved' => 'status-resolved',
        'rejected' => 'status-rejected',
        default => '',
    };
}

// Function to get the status badge text
function getStatusText($status) {
    return match ($status) {
        'new' => 'Nouveau',
        'in-progress' => 'En cours',
        'resolved' => 'Résolu',
        'rejected' => 'Rejeté',
        default => $status,
    };
}

// Function to get the type text
function getTypeText($type) {
    return match ($type) {
        'technique' => 'Technique',
        'paiement' => 'Paiement',
        'service' => 'Service client',
        'autre' => 'Autre',
        default => $type,
    };
}

// Function to get the priority text
function getPriorityText($priority) {
    return match ($priority) {
        'high' => 'Haute',
        'medium' => 'Moyenne',
        'low' => 'Basse',
        default => $priority,
    };
}

// Function to format the boolean answer
function formatAnswer($answer) {
    return $answer ? 'Vrai' : 'Faux';
}


function getFilters() {

    $filters = [
        'titre' => '',
        'niveau' => '',
        'date_creation' => ''
    ];

    // Validate titre
    if (isset($_GET['titre']) && !empty($_GET['titre'])) {
        $filters['titre'] = htmlspecialchars(trim($_GET['titre']));
        if (!preg_match('/^[a-zA-Z\s]+$/', $filters['titre'])) {
            $filters['titre'] = '';
        }
    }

    // Validate niveau
    if (isset($_GET['niveau']) && !empty($_GET['niveau'])) {
        $filters['niveau'] = htmlspecialchars(trim($_GET['niveau']));
        if (!in_array($filters['niveau'], ['Débutant', 'Intermédiaire', 'Avancé'])) {
            $filters['niveau'] = '';
        }
    }

    // Validate date_creation
    if (isset($_GET['date_creation']) && !empty($_GET['date_creation'])) {
        $filters['date_creation'] = htmlspecialchars(trim($_GET['date_creation']));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_creation'])) {
            $filters['date_creation'] = '';
        }
    }

    return $filters;
}
$filters=getFilters();
// Check if all filters are empty
if (empty($filters['titre']) && empty($filters['niveau']) && empty($filters['date_creation'])) {
    // Retrieve all formations
    $formations = $model->getAllFormations();
} else {
    // Call the filtering function in the controller
    $formations = $model->filterFormations($filters);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Admin Formations</title>
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
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            color: white;
            margin-bottom: 1rem;
            transition: transform 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0,0,0,0.15);
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
        .filter-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table th {
            white-space: nowrap;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }
        .priority-high { color: #dc3545; font-weight: bold; }
        .priority-medium { color: #fd7e14; font-weight: bold; }
        .priority-low { color: #28a745; font-weight: bold; }
        .admin-notes {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }
        .quiz-table {
            margin-top: 2rem;
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
     <!-- Sidebar Start -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>Neyrouz Chekir</h4>
            <p>Administrateur</p>
        </div>
        <div class="sidebar-menu">
            <a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="admin-users.html"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="admin-projects.html"><i class="fas fa-project-diagram"></i> Projets</a>
            <a href="admin-formations.html" class="active"><i class="fas fa-graduation-cap"></i> Formations</a>
            <a href="admin-events.html"><i class="fas fa-calendar-alt"></i> Événements</a>
            <a href="admin-investments.html"><i class="fas fa-chart-line"></i> Investissements</a>
            <a href="admin-reclamations.php" ><i class="fas fa-exclamation-circle"></i> Réclamations</a>
            <a href="admin-settings.html"><i class="fas fa-cog"></i> Paramètres</a>
            <a href="logout.html"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
    <!-- Sidebar End -->
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
     <!-- Trigger Zone -->
    <div class="trigger-zone"></div>

<!-- Sidebar Start -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4>Neyrouz Chekir</h4>
        <p>Administrateur</p>
    </div>
    <div class="sidebar-menu">
        <a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
        <a href="admin-users.html"><i class="fas fa-users"></i> Utilisateurs</a>
        <a href="admin-projects.html"><i class="fas fa-project-diagram"></i> Projets</a>
        <a href="admin-formations.html" class="active"><i class="fas fa-graduation-cap"></i> Formations</a>
        <a href="admin-events.html"><i class="fas fa-calendar-alt"></i> Événements</a>
        <a href="admin-investments.html"><i class="fas fa-chart-line"></i> Investissements</a>
        <a href="admin-reclamations.php" ><i class="fas fa-exclamation-circle"></i> Réclamations</a>
        <a href="admin-settings.html"><i class="fas fa-cog"></i> Paramètres</a>
        <a href="logout.html"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
</div>
<!-- Sidebar End -->
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
        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0"><i class="fas fa-book me-2"></i>Gestion des Formations</h2>
                            <div>
                                <button id="exportBtn" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-file-export me-1"></i> Exporter
                                </button>
                                <a href="add-formation.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Nouvelle Formation
                                </a>
                            </div>
                        </div>
                        <!-- Filtres Admin -->
                        <div class="filter-section mb-4">
                            <form method="GET" action="">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Titre</label>
                                        <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($_GET['titre'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Niveau</label>
                                        <select class="form-select" name="niveau">
                                            <option value="">Tous</option>
                                            <option value="Débutant" <?= ($_GET['niveau'] ?? '') === 'Débutant' ? 'selected' : '' ?>>Débutant</option>
                                            <option value="Intermédiaire" <?= ($_GET['niveau'] ?? '') === 'Intermédiaire' ? 'selected' : '' ?>>Intermédiaire</option>
                                            <option value="Avancé" <?= ($_GET['niveau'] ?? '') === 'Avancé' ? 'selected' : '' ?>>Avancé</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date de création</label>
                                        <input type="date" class="form-control" name="date_creation" value="<?= htmlspecialchars($_GET['date_creation'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Filtrer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Tableau des Formations -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="formationsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Titre</th>
                                                <th>Description</th>
                                                <th>Niveau</th>
                                                <th>Durée (heures)</th>
                                                <th>Date de création</th>
                                                <th>Certificat</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="formationsBody">
                                            <?php foreach ($formations as $formation): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($formation['titre']) ?></td>
                                                    <td><?= htmlspecialchars(substr($formation['description'], 0, 50)) ?>...</td>
                                                    <td><?= htmlspecialchars($formation['niveau']) ?></td>
                                                    <td><?= htmlspecialchars($formation['duree']) ?></td>
                                                    <td><?= htmlspecialchars(formatDate($formation['date_creation'])) ?></td>
                                                    <td><?= htmlspecialchars($formation['certificat']) ?></td>
                                                    <td>
                                                        <a href="view-formation.php?id=<?= htmlspecialchars($formation['id']) ?>" class="btn btn-sm btn-primary action-btn" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit-formation.php?id=<?= htmlspecialchars($formation['id']) ?>" class="btn btn-sm btn-info action-btn" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="delete-formation.php?id=<?= htmlspecialchars($formation['id']) ?>" class="btn btn-sm btn-danger action-btn" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer cette formation ? Cette action est irréversible.')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                        <a href="../../controller/DashboardController.php?action=generate&id=<?= $formation['id'] ?>" class="generate-link" title="Generate Questions">
                                                            <i class="fas fa-question-circle"></i>
                                                        </a>
                                                        <a href="/generate-certificate" class="icon-button" title="Generate Certificate">
                                                            <i class="fas fa-certificate"></i> <!-- Certificate icon -->
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Tableau des Quizzes -->
                        <div class="card shadow-sm quiz-table mt-4">
                            <div class="card-body">
                                <h3 class="mb-4"><i class="fas fa-question-circle me-2"></i>Gestion des Quizzes</h3>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="quizzesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID Formation</th>
                                                <th>Question 1</th>
                                                <th>Réponse 1</th>
                                                <th>Question 2</th>
                                                <th>Réponse 2</th>
                                                <th>Question 3</th>
                                                <th>Réponse 3</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="quizzesBody">
                                            <?php foreach ($formations as $formation): ?>
                                                <?php
                                                $quiz = $model->getQuizByFormationId($formation['id']);
                                                if ($quiz):
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($formation['id']) ?></td>
                                                        <td><?= htmlspecialchars($quiz['question1']) ?></td>
                                                        <td><?= formatAnswer($quiz['answer1']) ?></td>
                                                        <td><?= htmlspecialchars($quiz['question2']) ?></td>
                                                        <td><?= formatAnswer($quiz['answer2']) ?></td>
                                                        <td><?= htmlspecialchars($quiz['question3']) ?></td>
                                                        <td><?= formatAnswer($quiz['answer3']) ?></td>
                                                        <td>
                                                            <a href="edit-formation.php?id=<?= htmlspecialchars($quiz['id_formation']) ?>" class="btn btn-sm btn-info action-btn" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="../../controller/delete-quiz.php?id=<?= htmlspecialchars($quiz['id']) ?>" class="btn btn-sm btn-danger action-btn" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer ce quiz ? Cette action est irréversible.')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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