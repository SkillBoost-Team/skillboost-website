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

// Traitement des actions CRUD pour les investissements
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                // Mise à jour d'un investissement
                $stmt = $pdo->prepare("UPDATE investissements SET 
                                      montant = :montant,
                                      pourcentage = :pourcentage,
                                      statut = :statut,
                                      date_creation = :date_creation
                                      WHERE id = :id");
                $stmt->execute([
                    ':id' => $_POST['id'],
                    ':montant' => $_POST['montant'],
                    ':pourcentage' => $_POST['pourcentage'],
                    ':statut' => $_POST['statut'],
                    ':date_creation' => $_POST['date_creation']
                ]);
                break;
            case 'delete':
                // Suppression d'un investissement
                $stmt = $pdo->prepare("DELETE FROM investissements WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                break;
            case 'updateRevenu':
                // Mise à jour d'un revenu
                $stmt = $pdo->prepare("UPDATE revenus_projets SET 
                                      montant = :montant,
                                      date_revenu = :date_revenu
                                      WHERE id = :id");
                $stmt->execute([
                    ':id' => $_POST['id'],
                    ':montant' => $_POST['montant'],
                    ':date_revenu' => $_POST['date_revenu']
                ]);
                break;
            case 'deleteRevenu':
                // Suppression d'un revenu
                $stmt = $pdo->prepare("DELETE FROM revenus_projets WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                break;
            case 'addRevenu':
                // Ajout d'un revenu
                $stmt = $pdo->prepare("INSERT INTO revenus_projets (
                                      id_projet, montant, date_revenu, description
                                      ) VALUES (
                                      :id_projet, :montant, :date_revenu, :description
                                      )");
                $stmt->execute([
                    ':id_projet' => $_POST['id_projet'],
                    ':montant' => $_POST['montant'],
                    ':date_revenu' => $_POST['date_revenu'],
                    ':description' => $_POST['description']
                ]);
                break;
        }
        // Redirection pour éviter la resoumission du formulaire
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Récupération des statistiques des investissements
$stats = [
    'total_investissements' => 0,
    'investissements_actifs' => 0,
    'investissements_en_attente' => 0,
    'investissements_annules' => 0
];

// Requête pour les statistiques
$statsQuery = "SELECT 
    SUM(montant) as total_investissements,
    SUM(CASE WHEN statut = 'Accepté' THEN 1 ELSE 0 END) as investissements_actifs,
    SUM(CASE WHEN statut = 'Proposé' THEN 1 ELSE 0 END) as investissements_en_attente,
    SUM(CASE WHEN statut = 'Refusé' THEN 1 ELSE 0 END) as investissements_annules
    FROM investissements";
$statsStmt = $pdo->prepare($statsQuery);
$statsStmt->execute();
$statsResult = $statsStmt->fetch(PDO::FETCH_ASSOC);
if ($statsResult) {
    $stats['total_investissements'] = $statsResult['total_investissements'] ?? 0;
    $stats['investissements_actifs'] = $statsResult['investissements_actifs'] ?? 0;
    $stats['investissements_en_attente'] = $statsResult['investissements_en_attente'] ?? 0;
    $stats['investissements_annules'] = $statsResult['investissements_annules'] ?? 0;
}

// Récupération des investissements
$investissementsQuery = "SELECT i.*, p.titre as titre_projet, u.prenom as prenom_investisseur, u.nom as nom_investisseur 
                        FROM investissements i
                        JOIN projets p ON i.id_projet = p.id
                        JOIN utilisateurs u ON i.id_investisseur = u.id
                        ORDER BY i.date_creation DESC";
$investissementsStmt = $pdo->prepare($investissementsQuery);
$investissementsStmt->execute();
$investissements = $investissementsStmt->fetchAll(PDO::FETCH_ASSOC);

// Ligne ~109 - changez de :
$query = "SELECT i.*, p.titre as titre_projet, u.prenom as prenom_investisseur, u.nom as nom_investisseur 
          FROM investissements i
          JOIN projets p ON i.id_projet = p.id
          JOIN utilisateurs u ON i.id_investisseur = u.id";

// À :
$query = "SELECT i.*, p.titre as titre_projet, u.firstname as prenom_investisseur, u.lastname as nom_investisseur 
          FROM investissements i
          JOIN projets p ON i.id_projet = p.id
          JOIN users u ON i.id_investisseur = u.id";

// Fonctions utilitaires
function getStatusClass($status) {
    $classes = [
        'Accepté' => 'bg-success',
        'Proposé' => 'bg-warning',
        'Refusé' => 'bg-danger'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

function formatDate($dateString) {
    return date('d/m/Y H:i', strtotime($dateString));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Admin Investissements</title>
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
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
        .table-responsive {
            overflow-x: auto;
        }
        .table th {
            white-space: nowrap;
        }
        .badge {
            font-size: 0.85rem;
            font-weight: 600;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(0, 0, 0, 0.03);
        }
        .custom-shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .btn-action {
            margin: 0 2px;
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
</head>
<body>
    <!-- Spinner -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner"></div>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="img/admin-avatar.jpg" alt="Admin Photo">
            <h4>Admin Name</h4>
            <p>Administrateur</p>
        </div>
        <div class="sidebar-menu">
            <a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="login.php"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="admin-projects.html"><i class="fas fa-project-diagram"></i> Projets</a>
            <a href="admin-formations.html"><i class="fas fa-graduation-cap"></i> Formations</a>
            <a href="admin-events.html"><i class="fas fa-calendar-alt"></i> Événements</a>
            <a href="admin-investments.php" class="active"><i class="fas fa-chart-line"></i> Investissements</a>
            <a href="admin-reclamations.php"><i class="fas fa-exclamation-circle"></i> Réclamations</a>
            <a href="admin-settings.html"><i class="fas fa-cog"></i> Paramètres</a>
            <a href="logout.html"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="container-fluid bg-dark px-5 d-none d-lg-block">
            <div class="row gx-0">
                <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center" style="height: 45px;">
                        <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Adresse</small>
                        <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>Téléphone</small>
                        <small class="text-light"><i class="fa fa-envelope-open me-2"></i>Email</small>
                    </div>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <div class="d-inline-flex align-items-center" style="height: 45px;">
                        <small class="text-light"><i class="fa fa-user-shield me-2"></i>Espace Administrateur</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Content -->
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-chart-line me-2"></i>Gestion des Investissements</h2>
                <a href="http://localhost/skillboost/Views/front/" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-2"></i>Voir le site
                </a>
            </div>

            <!-- Cards Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card bg-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="count"><?= number_format($stats['total_investissements'], 2) ?> €</div>
                                <div class="title">Total Investissements</div>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="count"><?= $stats['investissements_actifs'] ?></div>
                                <div class="title">Investissements Actifs</div>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="count"><?= $stats['investissements_en_attente'] ?></div>
                                <div class="title">En Attente</div>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-danger">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="count"><?= $stats['investissements_annules'] ?></div>
                                <div class="title">Investissements Annulés</div>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table des Investissements -->
            <div class="card custom-shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Projet</th>
                                    <th>Investisseur</th>
                                    <th>Montant</th>
                                    <th>Pourcentage</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($investissements as $investissement): ?>
                                <tr>
                                    <td><?= $investissement['id'] ?></td>
                                    <td><?= htmlspecialchars($investissement['titre_projet']) ?></td>
                                    <td><?= htmlspecialchars($investissement['prenom_investisseur'] . ' ' . $investissement['nom_investisseur']) ?></td>
                                    <td><?= number_format($investissement['montant'], 2) ?> €</td>
                                    <td><?= $investissement['pourcentage'] ?>%</td>
                                    <td><?= formatDate($investissement['date_creation']) ?></td>
                                    <td>
                                        <span class="badge <?= getStatusClass($investissement['statut']) ?>">
                                            <?= $investissement['statut'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-action edit-investment" 
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="<?= $investissement['id'] ?>"
                                                data-montant="<?= $investissement['montant'] ?>"
                                                data-pourcentage="<?= $investissement['pourcentage'] ?>"
                                                data-statut="<?= $investissement['statut'] ?>"
                                                data-date="<?= $investissement['date_creation'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="post" action="" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $investissement['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet investissement ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Revenus des Projets -->
            <h3 class="mb-3"><i class="fas fa-money-bill-trend-up me-2"></i>Revenus des Projets</h3>
            
            <?php if (empty($revenus_par_projet)): ?>
                <div class="alert alert-info">
                    Aucun revenu enregistré pour le moment.
                </div>
            <?php else: ?>
                <div class="accordion" id="accordionProjets">
                    <?php foreach ($revenus_par_projet as $projet_id => $projet): ?>
                        <?php 
                            $total_revenus = array_sum(array_column($projet['revenus'], 'montant'));
                            $accordionId = "collapse" . $projet_id;
                        ?>
                        <div class="card mb-3">
                            <div class="card-header" id="heading<?= $projet_id ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-decoration-none" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#<?= $accordionId ?>" 
                                                aria-expanded="true" aria-controls="<?= $accordionId ?>">
                                            <i class="fas fa-chevron-down me-2"></i>
                                            <?= htmlspecialchars($projet['titre']) ?>
                                        </button>
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-3">
                                            Total: <?= number_format($total_revenus, 2) ?> €
                                        </span>
                                        <button class="btn btn-primary btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#addRevenueModal"
                                                data-projet-id="<?= $projet_id ?>"
                                                data-projet-titre="<?= htmlspecialchars($projet['titre']) ?>">
                                            <i class="fas fa-plus me-1"></i> Ajouter un revenu
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="<?= $accordionId ?>" class="collapse show" aria-labelledby="heading<?= $projet_id ?>" data-bs-parent="#accordionProjets">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Date du Revenu</th>
                                                    <th>Montant</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($projet['revenus'] as $revenu): ?>
                                                <tr>
                                                    <td><?= $revenu['id'] ?></td>
                                                    <td><?= formatDate($revenu['date_revenu']) ?></td>
                                                    <td><?= number_format($revenu['montant'], 2) ?> €</td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm btn-action edit-revenue" 
                                                                data-bs-toggle="modal" data-bs-target="#editRevenueModal"
                                                                data-id="<?= $revenu['id'] ?>"
                                                                data-montant="<?= $revenu['montant'] ?>"
                                                                data-date="<?= $revenu['date_revenu'] ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="post" action="" style="display:inline;">
                                                            <input type="hidden" name="action" value="deleteRevenu">
                                                            <input type="hidden" name="id" value="<?= $revenu['id'] ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce revenu ?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal d'ajout de revenu -->
    <div class="modal fade" id="addRevenueModal" tabindex="-1" aria-labelledby="addRevenueModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="">
                    <input type="hidden" name="action" value="addRevenu">
                    <input type="hidden" name="id_projet" id="addRevenueProjetId">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addRevenueModalLabel">Ajouter un revenu</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Projet</label>
                            <input type="text" class="form-control" id="addRevenueProjetTitre" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="addRevenueMontant" class="form-label">Montant (€)</label>
                            <input type="number" class="form-control" id="addRevenueMontant" name="montant" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="addRevenueDate" class="form-label">Date du revenu</label>
                            <input type="date" class="form-control" id="addRevenueDate" name="date_revenu" required>
                        </div>
                        <div class="mb-3">
                            <label for="addRevenueDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="addRevenueDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de modification d'investissement -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">Modifier l'investissement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editMontant" class="form-label">Montant (€)</label>
                            <input type="number" class="form-control" id="editMontant" name="montant" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPourcentage" class="form-label">Pourcentage</label>
                            <input type="number" class="form-control" id="editPourcentage" name="pourcentage" min="0" max="100" step="0.1" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatut" class="form-label">Statut</label>
                            <select class="form-select" id="editStatut" name="statut" required>
                                <option value="Proposé">Proposé</option>
                                <option value="Accepté">Accepté</option>
                                <option value="Refusé">Refusé</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editDate" class="form-label">Date d'investissement</label>
                            <input type="datetime-local" class="form-control" id="editDate" name="date_creation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de modification de revenu -->
    <div class="modal fade" id="editRevenueModal" tabindex="-1" aria-labelledby="editRevenueModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="">
                    <input type="hidden" name="action" value="updateRevenu">
                    <input type="hidden" name="id" id="editRevenueId">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editRevenueModalLabel">Modifier le revenu</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editRevenueMontant" class="form-label">Montant (€)</label>
                            <input type="number" class="form-control" id="editRevenueMontant" name="montant" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRevenueDate" class="form-label">Date du revenu</label>
                            <input type="date" class="form-control" id="editRevenueDate" name="date_revenu" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Cacher le spinner
        document.getElementById('spinner').classList.remove('show');

        // Gestion du modal d'ajout de revenu
        document.getElementById('addRevenueModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('addRevenueProjetId').value = button.getAttribute('data-projet-id');
            document.getElementById('addRevenueProjetTitre').value = button.getAttribute('data-projet-titre');
            document.getElementById('addRevenueDate').value = new Date().toISOString().split('T')[0];
        });

        // Gestion du modal de modification d'investissement
        document.getElementById('editModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('editId').value = button.getAttribute('data-id');
            document.getElementById('editMontant').value = button.getAttribute('data-montant');
            document.getElementById('editPourcentage').value = button.getAttribute('data-pourcentage');
            document.getElementById('editStatut').value = button.getAttribute('data-statut');
            
            // Formater la date pour l'input datetime-local
            const date = new Date(button.getAttribute('data-date'));
            const formattedDate = date.toISOString().slice(0, 16);
            document.getElementById('editDate').value = formattedDate;
        });

        // Gestion du modal de modification de revenu
        document.getElementById('editRevenueModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('editRevenueId').value = button.getAttribute('data-id');
            document.getElementById('editRevenueMontant').value = button.getAttribute('data-montant');
            
            // Formater la date pour l'input date
            const date = new Date(button.getAttribute('data-date'));
            const formattedDate = date.toISOString().split('T')[0];
            document.getElementById('editRevenueDate').value = formattedDate;
        });
    </script>
</body>
</html>