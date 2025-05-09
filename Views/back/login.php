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

// Traitement des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Insertion d'un nouvel utilisateur
                $stmt = $pdo->prepare("INSERT INTO users (
                                      username, full_name, nom, role, email, password_hash, created_at
                                      ) VALUES (
                                      :username, :full_name, :nom, :role, :email, :password_hash, NOW()
                                      )");
                $stmt->execute([
                    ':username' => $_POST['username'],
                    ':full_name' => $_POST['full_name'],
                    ':nom' => $_POST['nom'],
                    ':role' => $_POST['role'],
                    ':email' => $_POST['email'],
                    ':password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT)
                ]);
                break;
            case 'update':
                // Mise à jour d'un utilisateur existant
                $updateFields = [
                    'username' => $_POST['username'],
                    'full_name' => $_POST['full_name'],
                    'nom' => $_POST['nom'],
                    'role' => $_POST['role'],
                    'email' => $_POST['email'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Si un nouveau mot de passe est fourni
                if (!empty($_POST['password'])) {
                    $updateFields['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                
                $sql = "UPDATE users SET ";
                $params = [];
                foreach ($updateFields as $field => $value) {
                    $sql .= "$field = :$field, ";
                    $params[":$field"] = $value;
                }
                $sql = rtrim($sql, ', ');
                $sql .= " WHERE id = :id";
                $params[':id'] = $_POST['id'];
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                break;
            case 'delete':
                // Suppression d'un utilisateur
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                break;
            case 'reset_password':
                // Réinitialisation du mot de passe
                $stmt = $pdo->prepare("UPDATE users SET 
                                      password_hash = :password_hash,
                                      reset_token = NULL,
                                      reset_token_expiry = NULL,
                                      updated_at = NOW()
                                      WHERE id = :id");
                $stmt->execute([
                    ':id' => $_POST['id'],
                    ':password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT)
                ]);
                break;
        }
        // Redirection pour éviter la resoumission du formulaire
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Récupération des utilisateurs avec filtres
$roleFilter = $_GET['role'] ?? '';
$searchTerm = $_GET['search'] ?? '';

// Construction de la requête de base
$query = "SELECT id, username, full_name, nom, role, email, created_at, updated_at 
          FROM users WHERE 1=1";
$params = [];

// Filtre par rôle
if (!empty($roleFilter)) {
    $query .= " AND role = :role";
    $params[':role'] = $roleFilter;
}

// Filtre par recherche
if (!empty($searchTerm)) {
    $query .= " AND (username LIKE :search OR full_name LIKE :search OR nom LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$searchTerm%";
}

// Pagination
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Requête pour le nombre total d'éléments
$countQuery = "SELECT COUNT(*) as total FROM users WHERE 1=1" . 
              (!empty($roleFilter) ? " AND role = :role" : "") .
              (!empty($searchTerm) ? " AND (username LIKE :search OR full_name LIKE :search OR nom LIKE :search OR email LIKE :search)" : "");
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    if (strpos($countQuery, $key) !== false) {
        $countStmt->bindValue($key, $value);
    }
}
$countStmt->execute();
$totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Tri par défaut et ajout de la pagination
$query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
try {
    $stmt = $pdo->prepare($query);
    // Liaison des paramètres
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données: " . $e->getMessage());
}

// Statistiques
$stats = [
    'total' => $totalItems,
    'admin' => 0,
    'formateur' => 0,
    'etudiant' => 0,
    'inactif' => 0
];

// Requête séparée pour les statistiques
$statsQuery = "SELECT 
    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin,
    SUM(CASE WHEN role = 'formateur' THEN 1 ELSE 0 END) as formateur,
    SUM(CASE WHEN role = 'etudiant' THEN 1 ELSE 0 END) as etudiant,
    SUM(CASE WHEN updated_at < DATE_SUB(NOW(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) as inactif
    FROM users";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute();
$statsResult = $statsStmt->fetch(PDO::FETCH_ASSOC);
if ($statsResult) {
    $stats['admin'] = $statsResult['admin'];
    $stats['formateur'] = $statsResult['formateur'];
    $stats['etudiant'] = $statsResult['etudiant'];
    $stats['inactif'] = $statsResult['inactif'];
}

// Fonctions utilitaires
function getRoleText($role) {
    $texts = [
        'admin' => 'Administrateur',
        'formateur' => 'Formateur',
        'etudiant' => 'Étudiant',
        'autre' => 'Autre'
    ];
    return $texts[$role] ?? $role;
}

function getRoleClass($role) {
    $classes = [
        'admin' => 'role-admin',
        'formateur' => 'role-formateur',
        'etudiant' => 'role-etudiant'
    ];
    return $classes[$role] ?? '';
}

function formatDate($dateString) {
    return date('d/m/Y H:i', strtotime($dateString));
}

function getActivityClass($updatedAt) {
    $threeMonthsAgo = date('Y-m-d H:i:s', strtotime('-3 months'));
    return ($updatedAt < $threeMonthsAgo) ? 'inactive' : 'active';
}

// Chemin pour l'export Excel
$exportDir = 'exports/';
if (!file_exists($exportDir)) {
    mkdir($exportDir, 0777, true);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Gestion des Utilisateurs</title>
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
        .role-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .role-admin { background-color: #dc3545; color: white; }
        .role-formateur { background-color: #17a2b8; color: white; }
        .role-etudiant { background-color: #28a745; color: white; }
        .activity-badge {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .active { background-color: #28a745; }
        .inactive { background-color: #6c757d; }
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
        .pagination {
            justify-content: center;
            margin-top: 20px;
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
        .password-field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
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
            <img src="img/admin-avatar2.jpg" alt="Admin Photo">
            <h4>Sghair Youssef </h4>
            <p>Administrateur</p>
        </div>
        <div class="sidebar-menu">
            <a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="login.php" class="active"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="admin-projects.html"><i class="fas fa-project-diagram"></i> Projets</a>
            <a href="admin-formations.html"><i class="fas fa-graduation-cap"></i> Formations</a>
            <a href="admin-events.html"><i class="fas fa-calendar-alt"></i> Événements</a>
            <a href="admin-investments.html"><i class="fas fa-chart-line"></i> Investissements</a>
            <a href="reclamations.php"><i class="fas fa-exclamation-circle"></i> Réclamations</a>
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0"><i class="fas fa-users me-2"></i>Gestion des Utilisateurs</h2>
                            <div>
                                <button id="exportExcelBtn" class="btn btn-outline-success me-2">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                                <button id="exportPdfBtn" class="btn btn-outline-danger me-2">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </button>
                                <button id="exportCsvBtn" class="btn btn-outline-secondary">
                                    <i class="fas fa-file-csv me-1"></i> CSV
                                </button>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crudModal">
                                    <i class="fas fa-plus me-1"></i> Nouvel utilisateur
                                </button>
                            </div>
                        </div>
                        <!-- Filtres -->
                        <div class="filter-section mb-4">
                            <form method="get" action="">
                                <input type="hidden" name="page" value="1">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Rôle</label>
                                        <select class="form-select" name="role">
                                            <option value="">Tous</option>
                                            <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                                            <option value="formateur" <?= $roleFilter === 'formateur' ? 'selected' : '' ?>>Formateur</option>
                                            <option value="etudiant" <?= $roleFilter === 'etudiant' ? 'selected' : '' ?>>Étudiant</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Recherche</label>
                                        <input type="text" class="form-control" name="search" placeholder="Nom, prénom, email..." value="<?= htmlspecialchars($searchTerm) ?>">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary">Réinitialiser</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Cartes Statistiques -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card bg-primary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $stats['total'] ?></div>
                                            <div class="title">Total Utilisateurs</div>
                                        </div>
                                        <i class="fas fa-users fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $stats['admin'] ?></div>
                                            <div class="title">Administrateurs</div>
                                        </div>
                                        <i class="fas fa-user-shield fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-success">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $stats['formateur'] ?></div>
                                            <div class="title">Formateurs</div>
                                        </div>
                                        <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-danger">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $stats['inactif'] ?></div>
                                            <div class="title">Inactifs</div>
                                        </div>
                                        <i class="fas fa-user-clock fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Graphique des statistiques -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <div class="chart-title">Répartition par Rôle</div>
                                    <canvas id="roleChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <div class="chart-title">Activité des Utilisateurs</div>
                                    <canvas id="activityChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Tableau des Utilisateurs -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom d'utilisateur</th>
                                                <th>Nom Complet</th>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Rôle</th>
                                                <th>Créé le</th>
                                                <th>Mis à jour</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['id']) ?></td>
                                                <td><?= htmlspecialchars($user['username']) ?></td>
                                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><span class="role-badge <?= getRoleClass($user['role']) ?>"><?= getRoleText($user['role']) ?></span></td>
                                                <td><?= formatDate($user['created_at']) ?></td>
                                                <td>
                                                    <span class="activity-badge <?= getActivityClass($user['updated_at']) ?>"></span>
                                                    <?= formatDate($user['updated_at']) ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary action-btn view-btn" 
                                                            data-bs-toggle="modal" data-bs-target="#crudModal"
                                                            data-id="<?= $user['id'] ?>"
                                                            data-username="<?= htmlspecialchars($user['username']) ?>"
                                                            data-full_name="<?= htmlspecialchars($user['full_name']) ?>"
                                                            data-nom="<?= htmlspecialchars($user['nom']) ?>"
                                                            data-role="<?= $user['role'] ?>"
                                                            data-email="<?= htmlspecialchars($user['email']) ?>"
                                                            data-created_at="<?= $user['created_at'] ?>"
                                                            data-updated_at="<?= $user['updated_at'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info action-btn edit-btn" 
                                                            data-bs-toggle="modal" data-bs-target="#crudModal"
                                                            data-id="<?= $user['id'] ?>"
                                                            data-username="<?= htmlspecialchars($user['username']) ?>"
                                                            data-full_name="<?= htmlspecialchars($user['full_name']) ?>"
                                                            data-nom="<?= htmlspecialchars($user['nom']) ?>"
                                                            data-role="<?= $user['role'] ?>"
                                                            data-email="<?= htmlspecialchars($user['email']) ?>"
                                                            data-created_at="<?= $user['created_at'] ?>"
                                                            data-updated_at="<?= $user['updated_at'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="post" action="" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Supprimer cet utilisateur ?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-sm btn-warning action-btn reset-btn" 
                                                            data-bs-toggle="modal" data-bs-target="#resetPasswordModal"
                                                            data-id="<?= $user['id'] ?>"
                                                            data-username="<?= htmlspecialchars($user['username']) ?>">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?php if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <?php if ($currentPage < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal CRUD -->
        <div class="modal fade" id="crudModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="post" action="">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="editId" value="">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="modalTitle">Nouvel Utilisateur</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ID Utilisateur</label>
                                    <input type="text" class="form-control" id="displayId" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de création</label>
                                    <input type="text" class="form-control" id="displayCreatedAt" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom d'utilisateur *</label>
                                    <input type="text" class="form-control" name="username" id="editUsername" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Rôle *</label>
                                    <select class="form-select" name="role" id="editRole" required>
                                        <option value="admin">Administrateur</option>
                                        <option value="formateur">Formateur</option>
                                        <option value="etudiant">Étudiant</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom Complet *</label>
                                    <input type="text" class="form-control" name="full_name" id="editFullName" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" class="form-control" name="nom" id="editNom" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" id="editEmail" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mot de passe <?= isset($_POST['action']) && $_POST['action'] === 'update' ? '(laisser vide pour ne pas changer)' : '*' ?></label>
                                    <div class="password-field">
                                        <input type="password" class="form-control" name="password" id="editPassword" <?= isset($_POST['action']) && $_POST['action'] === 'update' ? '' : 'required' ?>>
                                        <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('editPassword')"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dernière mise à jour</label>
                                <input type="text" class="form-control" id="displayUpdatedAt" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="button" class="btn btn-danger" id="deleteBtn" style="display:none;">
                                Supprimer
                            </button>
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Modal Reset Password -->
        <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="id" id="resetUserId" value="">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">Réinitialiser le mot de passe</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Vous êtes sur le point de réinitialiser le mot de passe de <strong id="resetUsername"></strong>.</p>
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe *</label>
                                <div class="password-field">
                                    <input type="password" class="form-control" name="password" id="resetPassword" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('resetPassword')"></i>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmer le mot de passe *</label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="resetPasswordConfirm" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('resetPasswordConfirm')"></i>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Confirmer</button>
                        </div>
                    </form>
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
        // Gestion du modal
        document.addEventListener('DOMContentLoaded', function() {
            // Cacher le spinner
            document.getElementById('spinner').classList.remove('show');
            
            // Gestion des boutons view/edit
            const crudModal = document.getElementById('crudModal');
            if (crudModal) {
                crudModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const isView = button.classList.contains('view-btn');
                    const isEdit = button.classList.contains('edit-btn');
                    
                    // Configurer le formulaire selon le bouton cliqué
                    if (isView || isEdit) {
                        document.getElementById('modalTitle').textContent = isView ? 'Détails de l\'Utilisateur' : 'Modifier l\'Utilisateur';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('editId').value = button.dataset.id;
                        document.getElementById('displayId').value = button.dataset.id;
                        document.getElementById('editUsername').value = button.dataset.username;
                        document.getElementById('editFullName').value = button.dataset.full_name;
                        document.getElementById('editNom').value = button.dataset.nom;
                        document.getElementById('editRole').value = button.dataset.role;
                        document.getElementById('editEmail').value = button.dataset.email;
                        document.getElementById('displayCreatedAt').value = formatDate(button.dataset.created_at);
                        document.getElementById('displayUpdatedAt').value = formatDate(button.dataset.updated_at);
                        
                        // Afficher/masquer les éléments selon le mode
                        document.getElementById('deleteBtn').style.display = isView ? 'none' : 'inline-block';
                        document.getElementById('editPassword').required = !isView;
                        
                        // Activer/désactiver les champs
                        const inputs = crudModal.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.disabled = isView;
                        });
                    } else {
                        // Mode création
                        document.getElementById('modalTitle').textContent = 'Nouvel Utilisateur';
                        document.getElementById('formAction').value = 'create';
                        document.getElementById('editId').value = '';
                        document.getElementById('displayId').value = 'USER-' + Math.random().toString(36).substr(2, 8).toUpperCase();
                        document.getElementById('displayCreatedAt').value = formatDate(new Date());
                        document.getElementById('displayUpdatedAt').value = 'N/A';
                        document.getElementById('deleteBtn').style.display = 'none';
                        
                        // Réactiver tous les champs
                        const inputs = crudModal.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.disabled = false;
                            if (input.type !== 'hidden') {
                                input.value = '';
                            }
                        });
                    }
                });
            }
            
            // Gestion du modal de réinitialisation de mot de passe
            const resetModal = document.getElementById('resetPasswordModal');
            if (resetModal) {
                resetModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('resetUserId').value = button.dataset.id;
                    document.getElementById('resetUsername').textContent = button.dataset.username;
                });
            }
            
            // Validation du formulaire de réinitialisation de mot de passe
            const resetForm = document.querySelector('#resetPasswordModal form');
            if (resetForm) {
                resetForm.addEventListener('submit', function(e) {
                    const password = document.getElementById('resetPassword').value;
                    const confirmPassword = document.getElementById('resetPasswordConfirm').value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Les mots de passe ne correspondent pas!');
                        return false;
                    }
                    
                    if (password.length < 8) {
                        e.preventDefault();
                        alert('Le mot de passe doit contenir au moins 8 caractères!');
                        return false;
                    }
                    
                    return true;
                });
            }
            
            // Bouton Exporter CSV
            document.getElementById('exportCsvBtn').addEventListener('click', function() {
                let csvContent = "ID,Nom d'utilisateur,Nom Complet,Nom,Email,Rôle,Créé le,Mis à jour\n";
                <?php foreach ($users as $user): ?>
                csvContent += `"<?= $user['id'] ?>","<?= htmlspecialchars($user['username']) ?>","<?= htmlspecialchars($user['full_name']) ?>",` +
                             `"<?= htmlspecialchars($user['nom']) ?>","<?= htmlspecialchars($user['email']) ?>",` +
                             `"<?= getRoleText($user['role']) ?>","<?= formatDate($user['created_at']) ?>",` +
                             `"<?= formatDate($user['updated_at']) ?>"\n`;
                <?php endforeach; ?>
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', `utilisateurs_${formatDate(new Date())}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                alert('Export des utilisateurs effectué avec succès!');
            });

            // Bouton Exporter Excel
            document.getElementById('exportExcelBtn').addEventListener('click', function() {
                let csvContent = "ID,Nom d'utilisateur,Nom Complet,Nom,Email,Rôle,Créé le,Mis à jour\n";
                <?php foreach ($users as $user): ?>
                csvContent += `"<?= $user['id'] ?>","<?= htmlspecialchars($user['username']) ?>","<?= htmlspecialchars($user['full_name']) ?>",` +
                             `"<?= htmlspecialchars($user['nom']) ?>","<?= htmlspecialchars($user['email']) ?>",` +
                             `"<?= getRoleText($user['role']) ?>","<?= formatDate($user['created_at']) ?>",` +
                             `"<?= formatDate($user['updated_at']) ?>"\n`;
                <?php endforeach; ?>
                const blob = new Blob([csvContent], { type: 'application/vnd.ms-excel;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', `utilisateurs_${formatDate(new Date())}.xls`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                alert('Export des utilisateurs effectué avec succès!');
            });

            // Bouton Exporter PDF
            document.getElementById('exportPdfBtn').addEventListener('click', function() {
                const element = document.querySelector('.dashboard-container');
                html2pdf().from(element).save(`utilisateurs_${formatDate(new Date())}.pdf`);
            });

            // Initialisation des graphiques
            const roleData = {
                labels: ['Administrateurs', 'Formateurs', 'Étudiants'],
                datasets: [{
                    label: 'Nombre d\'utilisateurs',
                    data: [<?= $stats['admin'] ?>, <?= $stats['formateur'] ?>, <?= $stats['etudiant'] ?>],
                    backgroundColor: ['#dc3545', '#17a2b8', '#28a745'],
                    borderColor: ['#dc3545', '#17a2b8', '#28a745'],
                    borderWidth: 1
                }]
            };
            const roleConfig = {
                type: 'pie',
                data: roleData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Répartition par Rôle'
                        }
                    }
                }
            };
            const roleChart = new Chart(
                document.getElementById('roleChart'),
                roleConfig
            );

            const activityData = {
                labels: ['Actifs (mis à jour < 3 mois)', 'Inactifs (mis à jour ≥ 3 mois)'],
                datasets: [{
                    label: 'Nombre d\'utilisateurs',
                    data: [<?= $stats['total'] - $stats['inactif'] ?>, <?= $stats['inactif'] ?>],
                    backgroundColor: ['#28a745', '#6c757d'],
                    borderColor: ['#28a745', '#6c757d'],
                    borderWidth: 1
                }]
            };
            const activityConfig = {
                type: 'bar',
                data: activityData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Activité des Utilisateurs'
                        }
                    }
                }
            };
            const activityChart = new Chart(
                document.getElementById('activityChart'),
                activityConfig
            );
        });

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
        
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
    <!-- HTML2PDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLdZE7pnoj14Zc2D1baTq9lVY+iJgxQ5xy5DkW0yei8pw5uRTSUT1naodr+8x372m95A2SOV0VKUpqVZGJgCiqBX0Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>