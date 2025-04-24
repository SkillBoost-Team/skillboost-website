<?php

require_once '../../controller/eventController.php'; // <-- le contrôleur d'événements

$eventController = new EvenementController();
$list = $eventController->listEvenements();

// Définir les variables de comptage
$nouvelles = 0;
$encours = 0;
$resolues = 0;
$urgentes = 0;

// Vérifier si $list contient des événements
if (isset($list) && is_array($list)) {
    $evenements = $list;  // Assigner la liste d'événements si elle est valide

    foreach ($evenements as $e) {
        if ($e['statut'] == 'Nouveau') $nouvelles++;
        elseif ($e['statut'] == 'En cours') $encours++;
        elseif ($e['statut'] == 'Résolu') $resolues++;
        if ($e['type_evenement'] == 'Urgent') $urgentes++;
    }
} else {
    $evenements = [];  // Si $list est vide ou invalide, on initialise $evenements comme un tableau vide
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>SkillBoost - Admin Événements</title>
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
        .badge-urgent { background-color: #dc3545; }
        .badge-resolu { background-color: #198754; }
        .badge-encours { background-color: #0dcaf0; }
        .badge-nouveau { background-color: #ffc107; }
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
                    <a href="admin-events.php" class="nav-item nav-link active">Événements</a>
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

        <?php
        require_once '../../controller/eventController.php';

        $eventController = new EvenementController();
        $list = $eventController->listEvenements();

        // Définir les variables de comptage
        $nouvelles = 0;
        $encours = 0;
        $resolues = 0;
        $urgentes = 0;

        // Vérifier si $list contient des événements
        if (isset($list) && is_array($list)) {
            $evenements = $list;  // Assigner la liste d'événements si elle est valide

            foreach ($evenements as $e) {
                if ($e['statut'] == 'Nouveau') $nouvelles++;
                elseif ($e['statut'] == 'En cours') $encours++;
                elseif ($e['statut'] == 'Résolu') $resolues++;
                if ($e['type_evenement'] == 'Urgent') $urgentes++;
            }
        } else {
            $evenements = [];  // Si $list est vide ou invalide, on initialise $evenements comme un tableau vide
        }
        ?>

        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Gestion des Événements</h2>
                            <div>
                                <a href="ajouter_evenement.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Nouvel Événement
                                </a>
                            </div>
                        </div>

                        <!-- Cartes Statistiques -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card bg-warning">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $nouvelles ?></div>
                                            <div class="title">Nouveaux</div>
                                        </div>
                                        <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $encours ?></div>
                                            <div class="title">En cours</div>
                                        </div>
                                        <i class="fas fa-spinner fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-success">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $resolues ?></div>
                                            <div class="title">Résolus</div>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-danger">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $urgentes ?></div>
                                            <div class="title">Urgents</div>
                                        </div>
                                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtres -->
                        <div class="filter-section mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Statut</label>
                                    <select class="form-select">
                                        <option>Tous les statuts</option>
                                        <option>Nouveau</option>
                                        <option>En cours</option>
                                        <option>Résolu</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Type</label>
                                    <select class="form-select">
                                        <option>Tous les types</option>
                                        <option>Formation</option>
                                        <option>Réunion</option>
                                        <option>Urgent</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date</label>
                                    <select class="form-select">
                                        <option>Toutes les dates</option>
                                        <option>Aujourd'hui</option>
                                        <option>Cette semaine</option>
                                        <option>Ce mois</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des événements -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Titre</th>
                                                <th>Type</th>
                                                <th>Lieu / Lien</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($evenements as $e): ?>
                                                <tr>
                                                    <td><?= isset($e['idevenement']) ? htmlspecialchars($e['idevenement']) : 'ID non défini' ?></td>
                                                    <td><?= isset($e['titre']) ? htmlspecialchars($e['titre']) : 'Titre non défini' ?></td>
                                                    <td><?= isset($e['type_evenement']) ? htmlspecialchars($e['type_evenement']) : 'Type non défini' ?></td>
                                                    <td><?= isset($e['lieu_ou_lien']) ? htmlspecialchars($e['lieu_ou_lien']) : 'Lieu / Lien non défini' ?></td>
                                                    <td><?= isset($e['date_evenement']) ? htmlspecialchars($e['date_evenement']) : 'Date non définie' ?></td>
                                                    <td>
                                                        <?php
                                                            $statut = isset($e['statut']) ? $e['statut'] : 'Statut non défini';
                                                            $badge = match($statut) {
                                                                'Nouveau' => 'badge-nouveau',
                                                                'En cours' => 'badge-encours',
                                                                'Résolu' => 'badge-resolu',
                                                                default => 'badge-secondary'
                                                            };
                                                        ?>
                                                        <span class="badge <?= $badge ?>"><?= $statut ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="voir_evenement.php?id=<?= isset($e['idevenement']) ? $e['idevenement'] : '' ?>" class="btn btn-sm btn-info action-btn">👁️</a>
                                                        <a href="update.php?id=<?= isset($e['idevenement']) ? $e['idevenement'] : '' ?>" class="btn btn-sm btn-warning action-btn">✏️</a>
                                                        <a href="delete.php?id=<?= isset($e['idevenement']) ? $e['idevenement'] : '' ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Supprimer cet événement ?')">🗑️</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
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
        // Initialisation du spinner
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.getElementById('spinner').classList.remove('show');
            }, 500);
        });
    </script>
</body>
</html>
