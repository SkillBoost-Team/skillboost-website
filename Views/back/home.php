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

// Récupération des statistiques globales
$stats = [
    'users' => [],
    'formations' => [],
    'projects' => [],
    'investments' => [],
    'events' => [],
    'reclamations' => []
];


// Statistiques Réclamations (gardé de votre code original)
$statsQuery = "SELECT 
    SUM(CASE WHEN STATUS = 'new' THEN 1 ELSE 0 END) as new,
    SUM(CASE WHEN STATUS = 'in-progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN STATUS = 'resolved' THEN 1 ELSE 0 END) as resolved,
    SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as urgent,
    COUNT(*) as total
    FROM reclamations";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute();
$statsResult = $statsStmt->fetch(PDO::FETCH_ASSOC);
if ($statsResult) {
    $stats['reclamations'] = [
        'new' => $statsResult['new'],
        'in-progress' => $statsResult['in_progress'],
        'resolved' => $statsResult['resolved'],
        'urgent' => $statsResult['urgent'],
        'total' => $statsResult['total']
    ];
}


// Fonction pour formater la date
function formatDate($dateString) {
    return date('d/m/Y H:i', strtotime($dateString));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Tableau de Bord Admin</title>
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
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            min-height: 100vh;
            background: #343a40;
            color: white;
            transition: all 0.3s;
            position: fixed;
            z-index: 1000;
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
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar-menu a i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 20px;
        }
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
        .stat-card .count {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-card .title {
            font-size: 1rem;
            opacity: 0.9;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #343a40;
            font-weight: 600;
        }
        .activity-item {
            border-left: 3px solid #0d6efd;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
            background-color: #f8f9fa;
            border-radius: 0 4px 4px 0;
        }
        .activity-item .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .activity-item .activity-text {
            margin-bottom: 0;
        }
        .module-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: rgba(255,255,255,0.8);
        }
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            .main-content.active {
                margin-left: 280px;
            }
        }
    </style>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <img src="img/admin-avatar.jpg" alt="Admin Photo">
            <h4>Maarfi Ons</h4>
            <p>Administrateur Principal</p>
        </div>
        <div class="sidebar-menu">
            <a href="admin-dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="admin-users.html"><i class="fas fa-users"></i> Utilisateurs (<?= $stats['users']['total'] ?>)</a>
            <a href="admin-projects.html"><i class="fas fa-project-diagram"></i> Projets (<?= $stats['projects']['total'] ?>)</a>
            <a href="admin-formations.html"><i class="fas fa-graduation-cap"></i> Formations (<?= $stats['formations']['total'] ?>)</a>
            <a href="admin-events.html"><i class="fas fa-calendar-alt"></i> Événements (<?= $stats['events']['total'] ?>)</a>
            <a href="admin-investments.html"><i class="fas fa-chart-line"></i> Investissements (<?= $stats['investments']['total'] ?>)</a>
            <a href="admin-reclamations.php"><i class="fas fa-exclamation-circle"></i> Réclamations (<?= $stats['reclamations']['total'] ?>)</a>
            <a href="admin-settings.html"><i class="fas fa-cog"></i> Paramètres</a>
            <a href="logout.html"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
    <!-- Sidebar End -->
    <!-- Main Content Start -->
    <div class="main-content">
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
        <!-- Dashboard Content -->
        <div class="dashboard-container">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord Administrateur</h2>
                        
                        <!-- Cartes Statistiques Globales -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card bg-primary">
                                    <div class="module-icon text-center">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="count"><?= $stats['users']['total'] ?></div>
                                        <div class="title">Utilisateurs</div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small>Admins: <?= $stats['users']['admins'] ?></small>
                                        <small>Formateurs: <?= $stats['users']['formateurs'] ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-success">
                                    <div class="module-icon text-center">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="count"><?= $stats['formations']['total'] ?></div>
                                        <div class="title">Formations</div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small>Actives: <?= $stats['formations']['active'] ?></small>
                                        <small>À venir: <?= $stats['formations']['upcoming'] ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-info">
                                    <div class="module-icon text-center">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="count"><?= $stats['projects']['total'] ?></div>
                                        <div class="title">Projets</div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small>En cours: <?= $stats['projects']['in_progress'] ?></small>
                                        <small>Terminés: <?= $stats['projects']['completed'] ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-warning">
                                    <div class="module-icon text-center">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="count"><?= number_format($stats['investments']['total_amount'], 0, ',', ' ') ?> DT</div>
                                        <div class="title">Investissements</div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small>Total: <?= $stats['investments']['total'] ?></small>
                                        <small>En attente: <?= $stats['investments']['pending'] ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Graphiques Principaux -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="chart-container">
                                    <div class="chart-title">Activité Récente</div>
                                    <canvas id="activityChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="chart-container">
                                    <div class="chart-title">Statut des Réclamations</div>
                                    <canvas id="reclamationsChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dernières Activités et Statistiques Rapides -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Dernières Activités</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($latestActivities as $activity): ?>
                                        <div class="activity-item">
                                            <div class="activity-time">
                                                <?= formatDate($activity['created_at']) ?>
                                                <span class="badge bg-secondary float-end"><?= ucfirst($activity['type']) ?></span>
                                            </div>
                                            <p class="activity-text"><?= htmlspecialchars($activity['description']) ?></p>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistiques Rapides</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <div class="p-3 border rounded text-center">
                                                    <div class="text-primary">
                                                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                                        <h5><?= $stats['events']['total'] ?></h5>
                                                        <small>Événements</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="p-3 border rounded text-center">
                                                    <div class="text-danger">
                                                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                                        <h5><?= $stats['reclamations']['urgent'] ?></h5>
                                                        <small>Réclamations urgentes</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="p-3 border rounded text-center">
                                                    <div class="text-info">
                                                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                                        <h5><?= $stats['users']['clients'] ?></h5>
                                                        <small>Clients</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="p-3 border rounded text-center">
                                                    <div class="text-success">
                                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                                        <h5><?= $stats['reclamations']['resolved'] ?></h5>
                                                        <small>Réclamations résolues</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content End -->
     
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
        // Cacher le spinner
        document.getElementById('spinner').classList.remove('show');
        
        // Graphique d'activité
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [
                    {
                        label: 'Utilisateurs',
                        data: [12, 19, 15, 27, 34, 45, 52, 60, 48, 55, 62, 70],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Formations',
                        data: [5, 8, 7, 12, 15, 18, 20, 22, 19, 25, 28, 30],
                        borderColor: '#20c997',
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Réclamations',
                        data: [3, 5, 8, 6, 10, 12, 15, 14, 12, 16, 18, 20],
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253, 126, 20, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Graphique des réclamations
        const reclamationsCtx = document.getElementById('reclamationsChart').getContext('2d');
        const reclamationsChart = new Chart(reclamationsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Nouvelles', 'En cours', 'Résolues', 'Urgentes'],
                datasets: [{
                    data: [
                        <?= $stats['reclamations']['new'] ?>, 
                        <?= $stats['reclamations']['in-progress'] ?>, 
                        <?= $stats['reclamations']['resolved'] ?>,
                        <?= $stats['reclamations']['urgent'] ?>
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#0dcaf0',
                        '#198754',
                        '#dc3545'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>