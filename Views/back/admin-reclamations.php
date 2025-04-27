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
                // Insertion d'une nouvelle réclamation
                $stmt = $pdo->prepare("INSERT INTO reclamations (
                                      full_name, email, SUBJECT, TYPE, priority, description, STATUS, created_at
                                      ) VALUES (
                                      :full_name, :email, :subject, :type, :priority, :description, :status, NOW()
                                      )");
                $stmt->execute([
                    ':full_name' => $_POST['full_name'],
                    ':email' => $_POST['email'],
                    ':subject' => $_POST['subject'],
                    ':type' => $_POST['type'],
                    ':priority' => $_POST['priority'],
                    ':description' => $_POST['description'],
                    ':status' => $_POST['status']
                ]);
                break;

            case 'update':
                // Mise à jour d'une réclamation existante
                $stmt = $pdo->prepare("UPDATE reclamations SET 
                                      full_name = :full_name,
                                      email = :email,
                                      SUBJECT = :subject,
                                      TYPE = :type,
                                      priority = :priority,
                                      description = :description,
                                      STATUS = :status
                                      WHERE id = :id");
                $stmt->execute([
                    ':id' => $_POST['id'],
                    ':full_name' => $_POST['full_name'],
                    ':email' => $_POST['email'],
                    ':subject' => $_POST['subject'],
                    ':type' => $_POST['type'],
                    ':priority' => $_POST['priority'],
                    ':description' => $_POST['description'],
                    ':status' => $_POST['status']
                ]);
                break;

            case 'delete':
                // Suppression d'une réclamation
                $stmt = $pdo->prepare("DELETE FROM reclamations WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                break;

            case 'resolve':
                // Marquer comme résolu
                $stmt = $pdo->prepare("UPDATE reclamations SET STATUS = 'resolved' WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                break;
        }
        // Redirection pour éviter la resoumission du formulaire
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Récupération des réclamations avec filtres
$statusFilter = $_GET['status'] ?? '';
$typeFilter = $_GET['type'] ?? '';
$priorityFilter = $_GET['priority'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Construction de la requête de base
$query = "SELECT id, user_id, full_name, email, SUBJECT, TYPE, priority, description, STATUS, created_at 
          FROM reclamations WHERE 1=1";
$params = [];

// Filtre par statut
if (!empty($statusFilter)) {
    $query .= " AND STATUS = :status";
    $params[':status'] = $statusFilter;
}

// Filtre par type
if (!empty($typeFilter)) {
    $query .= " AND TYPE = :type";
    $params[':type'] = $typeFilter;
}

// Filtre par priorité
if (!empty($priorityFilter)) {
    $query .= " AND priority = :priority";
    $params[':priority'] = $priorityFilter;
}

// Filtre par date 
if (!empty($dateFilter)) {
    $today = date('Y-m-d');
    if ($dateFilter === 'today') {
        $query .= " AND DATE(created_at) = :date_today";
        $params[':date_today'] = $today;
    } elseif ($dateFilter === 'week') {
        $query .= " AND created_at >= DATE_SUB(:date_now, INTERVAL 7 DAY)";
        $params[':date_now'] = $today;
    } elseif ($dateFilter === 'month') {
        $query .= " AND MONTH(created_at) = MONTH(:date_now) AND YEAR(created_at) = YEAR(:date_now)";
        $params[':date_now'] = $today;
    }
}

// Tri par défaut
$query .= " ORDER BY created_at DESC";

// Debug: Afficher la requête et les paramètres (à supprimer en production)
// echo "Requête: $query<br>";
// print_r($params);

try {
    $stmt = $pdo->prepare($query);
    // Liaison des paramètres
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données: " . $e->getMessage());
}

// Statistiques
$stats = [
    'new' => 0,
    'in-progress' => 0,
    'resolved' => 0,
    'urgent' => 0
];
foreach ($reclamations as $rec) {
    if ($rec['STATUS'] === 'new') $stats['new']++;
    if ($rec['STATUS'] === 'in-progress') $stats['in-progress']++;
    if ($rec['STATUS'] === 'resolved') $stats['resolved']++;
    if ($rec['priority'] === 'high') $stats['urgent']++;
}

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
    <title>SkillBoost - Admin Réclamations</title>
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
                    <a href="admin-reclamations.php" class="nav-item nav-link active">Réclamations</a>
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
                            <h2 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Gestion des Réclamations</h2>
                            <div>
                                <button id="exportBtn" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-file-export me-1"></i> Exporter
                                </button>
                            </div>
                        </div>
                        <!-- Filtres Admin -->
                        <div class="filter-section mb-4">
                            <form method="get" action="">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Statut</label>
                                        <select class="form-select" name="status">
                                            <option value="">Tous</option>
                                            <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>Nouveau</option>
                                            <option value="in-progress" <?= $statusFilter === 'in-progress' ? 'selected' : '' ?>>En cours</option>
                                            <option value="resolved" <?= $statusFilter === 'resolved' ? 'selected' : '' ?>>Résolu</option>
                                            <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejeté</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Type</label>
                                        <select class="form-select" name="type">
                                            <option value="">Tous</option>
                                            <option value="technique" <?= $typeFilter === 'technique' ? 'selected' : '' ?>>Technique</option>
                                            <option value="paiement" <?= $typeFilter === 'paiement' ? 'selected' : '' ?>>Paiement</option>
                                            <option value="service" <?= $typeFilter === 'service' ? 'selected' : '' ?>>Service client</option>
                                            <option value="autre" <?= $typeFilter === 'autre' ? 'selected' : '' ?>>Autre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Priorité</label>
                                        <select class="form-select" name="priority">
                                            <option value="">Toutes</option>
                                            <option value="high" <?= $priorityFilter === 'high' ? 'selected' : '' ?>>Haute</option>
                                            <option value="medium" <?= $priorityFilter === 'medium' ? 'selected' : '' ?>>Moyenne</option>
                                            <option value="low" <?= $priorityFilter === 'low' ? 'selected' : '' ?>>Basse</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date</label>
                                        <select class="form-select" name="date">
                                            <option value="">Toutes</option>
                                            <option value="today" <?= $dateFilter === 'today' ? 'selected' : '' ?>>Aujourd'hui</option>
                                            <option value="week" <?= $dateFilter === 'week' ? 'selected' : '' ?>>Cette semaine</option>
                                            <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>Ce mois</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12 text-end">
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
                                            <div class="count"><?= $stats['new'] ?></div>
                                            <div class="title">Nouvelles</div>
                                        </div>
                                        <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $stats['in-progress'] ?></div>
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
                                            <div class="count"><?= $stats['resolved'] ?></div>
                                            <div class="title">Résolues</div>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-danger">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $stats['urgent'] ?></div>
                                            <div class="title">Urgentes</div>
                                        </div>
                                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tableau des Réclamations -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Utilisateur</th>
                                                <th>Email</th>
                                                <th>Sujet</th>
                                                <th>Type</th>
                                                <th>Priorité</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reclamations as $rec): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($rec['id']) ?></td>
                                                <td><?= htmlspecialchars($rec['full_name']) ?></td>
                                                <td><?= htmlspecialchars($rec['email']) ?></td>
                                                <td><?= htmlspecialchars($rec['SUBJECT']) ?></td>
                                                <td><?= getTypeText($rec['TYPE']) ?></td>
                                                <td class="priority-<?= $rec['priority'] ?>"><?= getPriorityText($rec['priority']) ?></td>
                                                <td><?= formatDate($rec['created_at']) ?></td>
                                                <td><span class="status-badge <?= getStatusClass($rec['STATUS']) ?>"><?= getStatusText($rec['STATUS']) ?></span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary action-btn view-btn" 
                                                            data-bs-toggle="modal" data-bs-target="#crudModal"
                                                            data-id="<?= $rec['id'] ?>"
                                                            data-fullname="<?= htmlspecialchars($rec['full_name']) ?>"
                                                            data-email="<?= htmlspecialchars($rec['email']) ?>"
                                                            data-subject="<?= htmlspecialchars($rec['SUBJECT']) ?>"
                                                            data-type="<?= $rec['TYPE'] ?>"
                                                            data-priority="<?= $rec['priority'] ?>"
                                                            data-status="<?= $rec['STATUS'] ?>"
                                                            data-description="<?= htmlspecialchars($rec['description']) ?>"
                                                            data-date="<?= $rec['created_at'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info action-btn edit-btn" 
                                                            data-bs-toggle="modal" data-bs-target="#crudModal"
                                                            data-id="<?= $rec['id'] ?>"
                                                            data-fullname="<?= htmlspecialchars($rec['full_name']) ?>"
                                                            data-email="<?= htmlspecialchars($rec['email']) ?>"
                                                            data-subject="<?= htmlspecialchars($rec['SUBJECT']) ?>"
                                                            data-type="<?= $rec['TYPE'] ?>"
                                                            data-priority="<?= $rec['priority'] ?>"
                                                            data-status="<?= $rec['STATUS'] ?>"
                                                            data-description="<?= htmlspecialchars($rec['description']) ?>"
                                                            data-date="<?= $rec['created_at'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="post" action="" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Supprimer cette réclamation ?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <a href="reponsesreclamations.php?reclamation_id=<?= $rec['id'] ?>" class="btn btn-sm btn-warning">
    <i class="fas fa-reply"></i> Répondre
</a>
                                                </td>
                                            </tr>
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
        <!-- Modal CRUD -->
        <div class="modal fade" id="crudModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="post" action="">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="editId" value="">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="modalTitle">Nouvelle Réclamation</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">ID Réclamation</label>
                                    <input type="text" class="form-control" id="displayId" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de création</label>
                                    <input type="text" class="form-control" id="displayDate" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom Complet *</label>
                                    <input type="text" class="form-control" name="full_name" id="editFullName" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" id="editEmail" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Sujet *</label>
                                    <input type="text" class="form-control" name="subject" id="editSubject" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type *</label>
                                    <select class="form-select" name="type" id="editType" required>
                                        <option value="technique">Technique</option>
                                        <option value="paiement">Paiement</option>
                                        <option value="service">Service client</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea class="form-control" name="description" id="editDescription" rows="4" required></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Priorité *</label>
                                    <select class="form-select" name="priority" id="editPriority" required>
                                        <option value="high">Haute</option>
                                        <option value="medium">Moyenne</option>
                                        <option value="low">Basse</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Statut *</label>
                                    <select class="form-select" name="status" id="editStatus" required>
                                        <option value="new">Nouveau</option>
                                        <option value="in-progress">En cours</option>
                                        <option value="resolved">Résolu</option>
                                        <option value="rejected">Rejeté</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes de l'admin</label>
                                <textarea class="form-control" id="editAdminNotes" rows="2" placeholder="Ajoutez des notes internes..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="button" class="btn btn-danger" id="deleteBtn" style="display:none;">
                                Supprimer
                            </button>
                            <button type="submit" class="btn btn-success">Enregistrer</button>
                            <button type="button" class="btn btn-primary" id="resolveBtn" style="display:none;">
                                <i class="fas fa-check-circle me-1"></i> Marquer comme résolu
                            </button>
                            <a id="modalResponseLink" href="#" class="btn btn-info" style="display:none;">
                                <i class="fas fa-comments me-1"></i> Voir les réponses
                            </a>
                        </div>
                    </form>
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
                        document.getElementById('modalTitle').textContent = isView ? 'Détails de la Réclamation' : 'Modifier la Réclamation';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('editId').value = button.dataset.id;
                        document.getElementById('displayId').value = button.dataset.id;
                        document.getElementById('displayDate').value = formatDate(button.dataset.date);
                        document.getElementById('editFullName').value = button.dataset.fullname;
                        document.getElementById('editEmail').value = button.dataset.email;
                        document.getElementById('editSubject').value = button.dataset.subject;
                        document.getElementById('editType').value = button.dataset.type;
                        document.getElementById('editPriority').value = button.dataset.priority;
                        document.getElementById('editStatus').value = button.dataset.status;
                        document.getElementById('editDescription').value = button.dataset.description;
                        // Afficher/masquer les éléments selon le mode
                        document.getElementById('deleteBtn').style.display = isView ? 'none' : 'inline-block';
                        document.getElementById('resolveBtn').style.display = isView && button.dataset.status !== 'resolved' ? 'inline-block' : 'none';
                        document.getElementById('modalResponseLink').style.display = isView ? 'inline-block' : 'none';
                        document.getElementById('modalResponseLink').href = 'reponsesreclamations.html?reclamation_id=' + button.dataset.id;
                        // Activer/désactiver les champs
                        const inputs = crudModal.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.disabled = isView && input.id !== 'editAdminNotes';
                        });
                    } else {
                        // Mode création
                        document.getElementById('modalTitle').textContent = 'Nouvelle Réclamation';
                        document.getElementById('formAction').value = 'create';
                        document.getElementById('editId').value = '';
                        document.getElementById('displayId').value = 'REC-' + Math.random().toString(36).substr(2, 8).toUpperCase();
                        document.getElementById('displayDate').value = formatDate(new Date());
                        document.getElementById('deleteBtn').style.display = 'none';
                        document.getElementById('resolveBtn').style.display = 'none';
                        document.getElementById('modalResponseLink').style.display = 'none';
                        // Réactiver tous les champs
                        const inputs = crudModal.querySelectorAll('input, select, textarea');
                        inputs.forEach(input => {
                            input.disabled = false;
                        });
                    }
                });
            }
            // Bouton Résoudre
            document.getElementById('resolveBtn').addEventListener('click', function() {
                if (confirm('Marquer cette réclamation comme résolue ?')) {
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.action = '';
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'resolve';
                    form.appendChild(actionInput);
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'id';
                    idInput.value = document.getElementById('editId').value;
                    form.appendChild(idInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
          
            // Bouton Exporter
            document.getElementById('exportBtn').addEventListener('click', function() {
                let csvContent = "ID,Nom,Email,Sujet,Type,Priorité,Statut,Date\n";
                
                <?php foreach ($reclamations as $rec): ?>
                csvContent += `"<?= $rec['id'] ?>","<?= htmlspecialchars($rec['full_name']) ?>","<?= htmlspecialchars($rec['email']) ?>","<?= htmlspecialchars($rec['SUBJECT']) ?>",` +
                             `"<?= getTypeText($rec['TYPE']) ?>","<?= getPriorityText($rec['priority']) ?>",` +
                             `"<?= getStatusText($rec['STATUS']) ?>","<?= formatDate($rec['created_at']) ?>"\n`;
                <?php endforeach; ?>
                
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', `reclamations_${formatDate(new Date())}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                alert('Export des réclamations effectué avec succès!');
            });
        });
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    </script>
</body>
</html>