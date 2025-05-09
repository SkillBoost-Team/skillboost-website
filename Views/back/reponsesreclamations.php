<?php
session_start();

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

// Liste des administrateurs
$admins = [
    1 => ['name' => 'Youssef Sghair', 'email' => 'youssef@skillboost.com'],
    2 => ['name' => 'Ahmed Houimel', 'email' => 'ahmed@skillboost.com'],
    3 => ['name' => 'Slaheddine Ayedi', 'email' => 'slaheddine@skillboost.com'],
    4 => ['name' => 'Ons Maarfi', 'email' => 'ons@skillboost.com'],
    5 => ['name' => 'Neyrouz Echeikh', 'email' => 'neyrouz@skillboost.com'],
    6 => ['name' => 'Oumaima Barhoumi', 'email' => 'oumaima@skillboost.com']
];

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

// Traitement des actions (ajout, modification, suppression de réponse)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'une nouvelle réponse
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_response':
                $response_text = trim($_POST['response_text']);
                $admin_id = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : 0;
                
                if (!empty($response_text) && $admin_id > 0) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO reponses_reclamations (reclamation_id, admin_id, reponse, date_reponse) VALUES (:reclamation_id, :admin_id, :reponse, NOW())");
                        $stmt->execute([
                            ':reclamation_id' => $reclamation_id,
                            ':admin_id' => $admin_id,
                            ':reponse' => $response_text
                        ]);
                        
                        // Mise à jour du statut de la réclamation si c'est la première réponse
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reponses_reclamations WHERE reclamation_id = :reclamation_id");
                        $stmt->execute([':reclamation_id' => $reclamation_id]);
                        $count = $stmt->fetchColumn();
                        
                        if ($count == 1) {
                            $stmt = $pdo->prepare("UPDATE reclamations SET STATUS = 'in-progress' WHERE id = :id");
                            $stmt->execute([':id' => $reclamation_id]);
                        }
                        
                        $_SESSION['success_message'] = "Réponse ajoutée avec succès.";
                        header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $reclamation_id);
                        exit();
                    } catch (PDOException $e) {
                        $_SESSION['error_message'] = "Erreur lors de l'ajout de la réponse : " . $e->getMessage();
                    }
                } else {
                    $_SESSION['error_message'] = "Veuillez remplir tous les champs correctement.";
                }
                break;
                
            case 'update_response':
                $response_id = isset($_POST['response_id']) ? intval($_POST['response_id']) : 0;
                $response_text = trim($_POST['response_text']);
                $admin_id = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : 0;
                
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
        }
    }
}

// Traitement de la suppression d'une réponse (via GET pour simplifier)
if (isset($_GET['delete_response'])) {
    $response_id = intval($_GET['delete_response']);
    
    if ($response_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM reponses_reclamations WHERE id = :id AND reclamation_id = :reclamation_id");
            $stmt->execute([
                ':id' => $response_id,
                ':reclamation_id' => $reclamation_id
            ]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['success_message'] = "Réponse supprimée avec succès.";
                
                // Vérifier s'il reste des réponses et mettre à jour le statut si nécessaire
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM reponses_reclamations WHERE reclamation_id = :reclamation_id");
                $stmt->execute([':reclamation_id' => $reclamation_id]);
                $count = $stmt->fetchColumn();
                
                if ($count == 0) {
                    $stmt = $pdo->prepare("UPDATE reclamations SET STATUS = 'new' WHERE id = :id");
                    $stmt->execute([':id' => $reclamation_id]);
                }
            } else {
                $_SESSION['error_message'] = "Aucune réponse trouvée à supprimer.";
            }
            
            header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $reclamation_id);
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erreur lors de la suppression de la réponse : " . $e->getMessage();
        }
    }
}

// Traitement du changement de statut de la réclamation
if (isset($_GET['change_status'])) {
    $new_status = $_GET['change_status'];
    $allowed_statuses = ['new', 'in-progress', 'resolved', 'rejected'];
    
    if (in_array($new_status, $allowed_statuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE reclamations SET STATUS = :status WHERE id = :id");
            $stmt->execute([
                ':status' => $new_status,
                ':id' => $reclamation_id
            ]);
            
            $_SESSION['success_message'] = "Statut de la réclamation mis à jour avec succès.";
            header("Location: " . $_SERVER['PHP_SELF'] . "?reclamation_id=" . $reclamation_id);
            exit();
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour du statut : " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Statut invalide.";
    }
}

// Récupération des réponses pour la réclamation
$stmt = $pdo->prepare("SELECT * FROM reponses_reclamations WHERE reclamation_id = :reclamation_id ORDER BY date_reponse ASC");
$stmt->execute([':reclamation_id' => $reclamation_id]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des statistiques
$stats = [
    'total' => 0,
    'new' => 0,
    'in-progress' => 0,
    'resolved' => 0,
    'rejected' => 0,
    'with_response' => 0,
    'without_response' => 0
];

// Statistiques globales
$stmt = $pdo->query("SELECT STATUS, COUNT(*) as count FROM reclamations GROUP BY STATUS");
$status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($status_counts as $row) {
    $stats[$row['STATUS']] = $row['count'];
    $stats['total'] += $row['count'];
}

// Réclamations avec/sans réponse
$stmt = $pdo->query("SELECT 
    SUM(CASE WHEN EXISTS (SELECT 1 FROM reponses_reclamations WHERE reclamations.id = reponses_reclamations.reclamation_id) THEN 1 ELSE 0 END) as with_response,
    SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM reponses_reclamations WHERE reclamations.id = reponses_reclamations.reclamation_id) THEN 1 ELSE 0 END) as without_response
FROM reclamations");
$response_stats = $stmt->fetch(PDO::FETCH_ASSOC);

$stats['with_response'] = $response_stats['with_response'];
$stats['without_response'] = $response_stats['without_response'];

// Récupération du message de session s'il existe
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

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
    return date('d/m/Y H:i', strtotime($dateString));
}

function getAdminName($admin_id, $admins) {
    return $admins[$admin_id]['name'] ?? 'Admin #' . $admin_id;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Réponses à la Réclamation</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 pour les messages de confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js pour les statistiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    :root {
        --primary-color: #495057;  /* Gris foncé */
        --secondary-color: #6c757d; /* Gris moyen */
        --success-color: #28a745;   /* Vert */
        --info-color: #17a2b8;      /* Bleu clair */
        --warning-color: #ffc107;   /* Jaune */
        --danger-color: #dc3545;    /* Rouge */
        --dark-color: #212529;      /* Noir */
        --light-color: #f8f9fa;     /* Gris très clair */
        --sidebar-width: 280px;
    }
    
    body {
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: var(--light-color);
        display: flex;
        min-height: 100vh;
    }
    
    /* Sidebar Styles */
    .sidebar {
        width: var(--sidebar-width);
        min-height: 100vh;
        background: linear-gradient(180deg, #495057 10%, #343a40 100%);
        color: white;
        transition: all 0.3s;
        position: fixed;
        z-index: 1000;
        box-shadow: 4px 0 10px rgba(0,0,0,0.1);
    }
    
    .sidebar-header {
        padding: 1.5rem 1rem;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar-header h4 {
        color: white;
        margin-top: 10px;
        font-weight: 800;
        letter-spacing: 1px;
    }
    
    .sidebar-header p {
        color: rgba(255,255,255,0.8);
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    
    .sidebar-menu {
        padding: 1rem 0;
    }
    
    .sidebar-divider {
        border-top: 1px solid rgba(255,255,255,0.1);
        margin: 1rem 0;
    }
    
    .nav-item {
        margin-bottom: 0.2rem;
    }
    
    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 0.75rem 1.5rem;
        border-radius: 0;
        display: flex;
        align-items: center;
        transition: all 0.3s;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .nav-link:hover, .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        text-decoration: none;
    }
    
    .nav-link i {
        margin-right: 0.5rem;
        width: 20px;
        text-align: center;
        font-size: 0.9rem;
    }
    
    /* Main Content Styles */
    .main-content {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        padding: 1.5rem;
        min-height: 100vh;
    }
    
    .topbar {
        background: white;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .topbar h4 {
        margin-bottom: 0;
        font-weight: 700;
        color: var(--dark-color);
    }
    
    .topbar .badge {
        font-weight: 600;
        padding: 0.35rem 0.65rem;
        background-color: var(--dark-color);
    }
    
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        border-radius: 0.35rem 0.35rem 0 0 !important;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    /* Response Styles */
    .response-card {
        background: white;
        border-radius: 0.35rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border-left: 4px solid var(--dark-color);
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .response-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    
    .response-actions {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .response-card:hover .response-actions {
        opacity: 1;
    }
    
    .response-actions .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin-left: 0.25rem;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .response-author {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
    }
    
    .response-date {
        font-size: 0.8rem;
        color: var(--secondary-color);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }
    
    .response-text {
        line-height: 1.6;
    }
    
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: white;
    }
    
    .status-new { background-color: var(--secondary-color); }
    .status-in-progress { background-color: #adb5bd; }
    .status-resolved { background-color: var(--success-color); }
    .status-rejected { background-color: var(--danger-color); }
    
    .priority-high { color: var(--danger-color); font-weight: 700; }
    .priority-medium { color: var(--warning-color); font-weight: 700; }
    .priority-low { color: var(--success-color); font-weight: 700; }
    
    .btn-primary {
        background-color: var(--dark-color);
        border-color: var(--dark-color);
    }
    
    .btn-primary:hover {
        background-color: #343a40;
        border-color: #343a40;
    }
    
    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    
    .status-selector {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }
    
    .status-selector .btn {
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 0.35rem;
    }
    
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card .stat-title {
        font-size: 0.8rem;
        color: var(--secondary-color);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
    }
    
    .stat-new { border-left-color: var(--secondary-color); }
    .stat-in-progress { border-left-color: #adb5bd; }
    .stat-resolved { border-left-color: var(--success-color); }
    .stat-rejected { border-left-color: var(--danger-color); }
    .stat-with-response { border-left-color: var(--dark-color); }
    .stat-without-response { border-left-color: var(--secondary-color); }
    
    .admin-selector {
        border-radius: 0.35rem;
        padding: 0.5rem;
        border: 1px solid #d1d3e2;
        width: 100%;
        font-size: 0.9rem;
    }
    
    .admin-selector:focus {
        border-color: var(--dark-color);
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.25);
    }
    
    .character-counter {
        font-size: 0.75rem;
        color: var(--secondary-color);
        text-align: right;
        margin-top: 0.25rem;
    }
    
    .character-counter.warning {
        color: var(--warning-color);
    }
    
    .character-counter.danger {
        color: var(--danger-color);
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
    }
</style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-fw fa-user-tie"></i>SkillBoost</h4>
            <p>Administration</p>
        </div>
        
        <div class="sidebar-menu">
            <h6 class="px-3 mb-3 text-uppercase text-white-50 small fw-bold">Tableau de bord</h6>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-projects.html">
                        <i class="fas fa-fw fa-project-diagram"></i>
                        <span>Projets</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-formations.html">
                        <i class="fas fa-fw fa-graduation-cap"></i>
                        <span>Formations</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-events.html">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-investments.html">
                        <i class="fas fa-fw fa-chart-line"></i>
                        <span>Investissements</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link active" href="reclamations.php">
                        <i class="fas fa-fw fa-exclamation-circle"></i>
                        <span>Réclamations</span>
                    </a>
                </li>
                
                <div class="sidebar-divider"></div>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-settings.html">
                        <i class="fas fa-fw fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="logout.html">
                        <i class="fas fa-fw fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h4><i class="fas fa-exclamation-circle me-2"></i>Réponses à la Réclamation #<?= htmlspecialchars($reclamation['id']) ?></h4>
            <span class="badge bg-primary"><i class="fas fa-user-shield me-1"></i>Espace Administrateur</span>
        </div>

        <!-- Content -->
        <div class="container-fluid">
            <!-- Messages d'alerte -->
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Détails de la Réclamation -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Détails de la Réclamation</h5>
                            <div>
                                <span class="status-badge <?= getStatusClass($reclamation['STATUS']) ?>">
                                    <?= getStatusText($reclamation['STATUS']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-user me-2"></i>Nom Complet:</strong> <?= htmlspecialchars($reclamation['full_name']) ?></p>
                                    <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong> <?= htmlspecialchars($reclamation['email']) ?></p>
                                    <p><strong><i class="fas fa-tag me-2"></i>Sujet:</strong> <?= htmlspecialchars($reclamation['SUBJECT']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-tags me-2"></i>Type:</strong> <?= getTypeText($reclamation['TYPE']) ?></p>
                                    <p><strong><i class="fas fa-exclamation-triangle me-2"></i>Priorité:</strong> <span class="priority-<?= $reclamation['priority'] ?>"><?= getPriorityText($reclamation['priority']) ?></span></p>
                                    <p><strong><i class="fas fa-calendar-alt me-2"></i>Date de Création:</strong> <?= formatDate($reclamation['created_at']) ?></p>
                                </div>
                            </div>
                            <hr>
                            <p><strong><i class="fas fa-align-left me-2"></i>Description:</strong></p>
                            <div class="p-3 bg-light rounded">
                                <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
                            </div>
                            
                            <!-- Sélecteur de statut -->
                            <div class="mt-3">
                                <p><strong><i class="fas fa-sync-alt me-2"></i>Changer le statut:</strong></p>
                                <div class="status-selector">
                                    <a href="?reclamation_id=<?= $reclamation_id ?>&change_status=new" class="btn <?= $reclamation['STATUS'] === 'new' ? 'btn-warning' : 'btn-outline-warning' ?>"><i class="fas fa-plus-circle me-1"></i>Nouveau</a>
                                    <a href="?reclamation_id=<?= $reclamation_id ?>&change_status=in-progress" class="btn <?= $reclamation['STATUS'] === 'in-progress' ? 'btn-info' : 'btn-outline-info' ?>"><i class="fas fa-spinner me-1"></i>En cours</a>
                                    <a href="?reclamation_id=<?= $reclamation_id ?>&change_status=resolved" class="btn <?= $reclamation['STATUS'] === 'resolved' ? 'btn-success' : 'btn-outline-success' ?>"><i class="fas fa-check-circle me-1"></i>Résolu</a>
                                    <a href="?reclamation_id=<?= $reclamation_id ?>&change_status=rejected" class="btn <?= $reclamation['STATUS'] === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>"><i class="fas fa-times-circle me-1"></i>Rejeté</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de réponse -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-reply me-2"></i><?= isset($_GET['edit_response']) ? 'Modifier une Réponse' : 'Ajouter une Réponse' ?></h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['edit_response'])): 
                                $edit_id = intval($_GET['edit_response']);
                                $edit_response = null;
                                
                                foreach ($responses as $response) {
                                    if ($response['id'] == $edit_id) {
                                        $edit_response = $response;
                                        break;
                                    }
                                }
                                
                                if ($edit_response): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="action" value="update_response">
                                        <input type="hidden" name="response_id" value="<?= $edit_response['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label for="admin_id_edit" class="form-label"><i class="fas fa-user-shield me-1"></i>Administrateur:</label>
                                            <select class="admin-selector" name="admin_id" id="admin_id_edit" required>
                                                <?php foreach ($admins as $id => $admin): ?>
                                                    <option value="<?= $id ?>" <?= $id == $edit_response['admin_id'] ? 'selected' : '' ?>><?= htmlspecialchars($admin['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="response_text_edit" class="form-label"><i class="fas fa-comment-dots me-1"></i>Réponse:</label>
                                            <textarea class="form-control" name="response_text" id="response_text_edit" rows="5" required oninput="updateCounter('edit')"><?= htmlspecialchars($edit_response['reponse']) ?></textarea>
                                            <div id="counter_edit" class="character-counter">0/1000 caractères</div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                        </button>
                                        <a href="?reclamation_id=<?= $reclamation_id ?>" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Annuler
                                        </a>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Réponse à modifier non trouvée.</div>
                                    <a href="?reclamation_id=<?= $reclamation_id ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Retour
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <form method="post" action="">
                                    <input type="hidden" name="action" value="add_response">
                                    
                                    <div class="mb-3">
                                        <label for="admin_id" class="form-label"><i class="fas fa-user-shield me-1"></i>Administrateur:</label>
                                        <select class="admin-selector" name="admin_id" id="admin_id" required>
                                            <option value="">-- Sélectionner un administrateur --</option>
                                            <?php foreach ($admins as $id => $admin): ?>
                                                <option value="<?= $id ?>"><?= htmlspecialchars($admin['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="response_text" class="form-label"><i class="fas fa-comment-dots me-1"></i>Réponse:</label>
                                        <textarea class="form-control" name="response_text" id="response_text" rows="5" placeholder="Entrez votre réponse ici..." required oninput="updateCounter('add')"></textarea>
                                        <div id="counter_add" class="character-counter">0/1000 caractères</div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Envoyer la Réponse
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Liste des réponses -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Réponses (<?= count($responses) ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($responses) > 0): ?>
                                <?php foreach ($responses as $response): ?>
                                    <div class="response-card">
                                        <div class="response-actions">
                                            <a href="?reclamation_id=<?= $reclamation_id ?>&edit_response=<?= $response['id'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="confirmDelete(<?= $response['id'] ?>)" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                        <div class="response-author">
                                            <i class="fas fa-user-shield me-2"></i><?= htmlspecialchars(getAdminName($response['admin_id'], $admins)) ?>
                                        </div>
                                        <div class="response-date">
                                            <i class="fas fa-clock me-2"></i><?= formatDate($response['date_reponse']) ?>
                                        </div>
                                        <div class="response-text mt-2">
                                            <?= nl2br(htmlspecialchars($response['reponse'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-comment-slash fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucune réponse n'a encore été ajoutée</h5>
                                    <p class="text-muted mb-0">Soyez le premier à répondre à cette réclamation</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Bouton Retour -->
                    <div class="text-end mt-3">
                        <a href="admin-reclamations.php" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Retour aux Réclamations
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Statistiques -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistiques des Réclamations</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card stat-with-response p-3 bg-white rounded shadow-sm">
                                        <div class="stat-title">Avec réponse</div>
                                        <div class="stat-value"><?= $stats['with_response'] ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card stat-without-response p-3 bg-white rounded shadow-sm">
                                        <div class="stat-title">Sans réponse</div>
                                        <div class="stat-value"><?= $stats['without_response'] ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card stat-new p-3 bg-white rounded shadow-sm">
                                        <div class="stat-title">Nouveau</div>
                                        <div class="stat-value"><?= $stats['new'] ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card stat-in-progress p-3 bg-white rounded shadow-sm">
                                        <div class="stat-title">En cours</div>
                                        <div class="stat-value"><?= $stats['in-progress'] ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card stat-resolved p-3 bg-white rounded shadow-sm">
                                        <div class="stat-title">Résolu</div>
                                        <div class="stat-value"><?= $stats['resolved'] ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="stat-card stat-rejected p-3 bg-white rounded shadow-sm">
                                        <div class="stat-title">Rejeté</div>
                                        <div class="stat-value"><?= $stats['rejected'] ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <canvas id="statusChart" height="200"></canvas>
                        </div>
                    </div>
                    
                    <!-- Graphique des réponses -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Activité des Réponses</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="responseChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Gestion du menu mobile
        $(document).ready(function() {
            $('.navbar-toggler').click(function() {
                $('.sidebar').toggleClass('active');
                $('.main-content').toggleClass('active');
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            
            // Initialiser les graphiques
            initCharts();
        });
        
        // Confirmation de suppression
        function confirmDelete(responseId) {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous ne pourrez pas annuler cette action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler',
                buttonsStyling: true,
                customClass: {
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-secondary px-4 ms-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '?reclamation_id=<?= $reclamation_id ?>&delete_response=' + responseId;
                }
            });
        }
        
        // Compteur de caractères
        function updateCounter(type) {
            const textarea = document.getElementById('response_text' + (type === 'edit' ? '_edit' : ''));
            const counter = document.getElementById('counter_' + type);
            const length = textarea.value.length;
            const maxLength = 1000;
            
            counter.textContent = length + '/' + maxLength + ' caractères';
            
            if (length > maxLength * 0.9) {
                counter.classList.add('warning');
                counter.classList.remove('danger');
            } else if (length > maxLength) {
                counter.classList.remove('warning');
                counter.classList.add('danger');
            } else {
                counter.classList.remove('warning', 'danger');
            }
        }
        
        // Initialiser les graphiques
        function initCharts() {
            // Graphique des statuts
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Nouveau', 'En cours', 'Résolu', 'Rejeté'],
                    datasets: [{
                        data: [
                            <?= $stats['new'] ?>,
                            <?= $stats['in-progress'] ?>,
                            <?= $stats['resolved'] ?>,
                            <?= $stats['rejected'] ?>
                        ],
                        backgroundColor: [
                            'rgba(246, 194, 62, 0.8)',
                            'rgba(54, 185, 204, 0.8)',
                            'rgba(28, 200, 138, 0.8)',
                            'rgba(231, 74, 59, 0.8)'
                        ],
                        borderColor: [
                            'rgba(246, 194, 62, 1)',
                            'rgba(54, 185, 204, 1)',
                            'rgba(28, 200, 138, 1)',
                            'rgba(231, 74, 59, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw;
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
            
            // Graphique des réponses (exemple avec des données factices)
            const responseCtx = document.getElementById('responseChart').getContext('2d');
            const responseChart = new Chart(responseCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Réponses',
                        data: [12, 19, 15, 20, 17, 25, 22, 18, 14, 20, 16, 22],
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>