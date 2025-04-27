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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #4e73df;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--dark-color);
            color: white;
            transition: all 0.3s;
            position: fixed;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            color: white;
            margin-top: 10px;
        }
        
        .sidebar-header p {
            color: #adb5bd;
            font-size: 0.9rem;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }
        
        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 20px;
            min-height: 100vh;
        }
        
        .topbar {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        /* Response Styles */
        .response-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .response-author {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .response-date {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-new { background-color: #ffc107; color: #212529; }
        .status-in-progress { background-color: #17a2b8; color: white; }
        .status-resolved { background-color: #28a745; color: white; }
        .status-rejected { background-color: #dc3545; color: white; }
        
        .priority-high { color: #dc3545; font-weight: bold; }
        .priority-medium { color: #fd7e14; font-weight: bold; }
        .priority-low { color: #28a745; font-weight: bold; }
        
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-back:hover {
            background-color: #5a6268;
            color: white;
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
            <h4><i class="fas fa-user-tie me-2"></i>SkillBoost</h4>
            <p> Maarfi Ons <br> Admin </p>
        </div>
        
        <div class="sidebar-menu">
            <h6 class="px-3 mb-3 text-muted">Tableau de bord</h6>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="admin-dashboard.html">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-users.html">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-projects.html">
                        <i class="fas fa-project-diagram"></i>
                        <span>Projets</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-formations.html">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Formations</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-events.html">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="admin-investments.html">
                        <i class="fas fa-chart-line"></i>
                        <span>Investissements</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link active" href="admin-reclamations.php">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Réclamations</span>
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <a class="nav-link" href="admin-settings.html">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="logout.html">
                        <i class="fas fa-sign-out-alt"></i>
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
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-exclamation-circle me-2"></i>Réponses à la Réclamation #<?= htmlspecialchars($reclamation['id']) ?></h4>
                <span><i class="fas fa-user-shield me-2"></i>Espace Administrateur</span>
            </div>
        </div>

        <!-- Content -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Détails de la Réclamation -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Détails de la Réclamation</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nom Complet:</strong> <?= htmlspecialchars($reclamation['full_name']) ?></p>
                                    <p><strong>Email:</strong> <?= htmlspecialchars($reclamation['email']) ?></p>
                                    <p><strong>Sujet:</strong> <?= htmlspecialchars($reclamation['SUBJECT']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Type:</strong> <?= getTypeText($reclamation['TYPE']) ?></p>
                                    <p><strong>Priorité:</strong> <span class="priority-<?= $reclamation['priority'] ?>"><?= getPriorityText($reclamation['priority']) ?></span></p>
                                    <p><strong>Date de Création:</strong> <?= formatDate($reclamation['created_at']) ?></p>
                                    <p><strong>Statut:</strong> <span class="status-badge <?= getStatusClass($reclamation['STATUS']) ?>"><?= getStatusText($reclamation['STATUS']) ?></span></p>
                                </div>
                            </div>
                            <hr>
                            <p><strong>Description:</strong></p>
                            <div class="p-3 bg-light rounded">
                                <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de réponse -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Ajouter une Réponse</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <input type="hidden" name="action" value="add_response">
                                <div class="mb-3">
                                    <textarea class="form-control" name="response_text" rows="4" placeholder="Entrez votre réponse ici..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer la Réponse
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Liste des réponses -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Réponses</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($responses) > 0): ?>
                                <?php foreach ($responses as $response): ?>
                                    <div class="response-card">
                                        <div class="response-author">
                                            <i class="fas fa-user-shield me-2"></i>Admin #<?= htmlspecialchars($response['admin_id']) ?>
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
                                    <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune réponse n'a encore été ajoutée pour cette réclamation.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Bouton Retour -->
                    <div class="text-end mt-3">
                        <a href="reclamations.php" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Retour aux Réclamations
                        </a>
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
        });
    </script>
</body>
</html>