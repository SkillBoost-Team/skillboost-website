<?php
require_once __DIR__ . '/../../controllers/investissementsController.php';

// Initialiser le contrôleur
$controller = new InvestissementsController();

// Récupérer les données
$data = $controller->index();

// S'assurer que les variables sont définies
$total_investissements = isset($data['total_investissements']) ? $data['total_investissements'] : 0;
$investissements_actifs = isset($data['investissements_actifs']) ? $data['investissements_actifs'] : 0;
$investissements_en_attente = isset($data['investissements_en_attente']) ? $data['investissements_en_attente'] : 0;
$investissements_annules = isset($data['investissements_annules']) ? $data['investissements_annules'] : 0;
$investissements = isset($data['investissements']) ? $data['investissements'] : [];
$revenus_projets = isset($data['revenus_projets']) ? $data['revenus_projets'] : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Investissements</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
            --footer-height: 60px;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            padding: 20px;
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            flex: 1;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .btn-action {
            margin: 0 2px;
        }

        .section-title {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background-color: #2c3e50;
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: #34495e;
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Topbar Styles */
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: var(--topbar-height);
            background-color: #000000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        .topbar-search {
            flex: 1;
            max-width: 400px;
        }

        .topbar-search input {
            width: 100%;
            padding: 8px 15px;
            border: 1px solid #333;
            border-radius: 20px;
            background-color: #1a1a1a;
            color: white;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .topbar-right a {
            color: white !important;
        }

        /* Footer Styles */
        .footer {
            margin-left: var(--sidebar-width);
            background-color: #000000;
            color: white;
            padding: 20px 0;
        }

        .footer-links {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .footer-section {
            margin: 10px 20px;
        }

        .footer-section h5 {
            color: #fff;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: #fff;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #333;
        }

        .social-links {
            margin-bottom: 15px;
        }

        .social-links a {
            color: #999;
            margin: 0 10px;
            font-size: 18px;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Sidebar Start -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="img/admin-avatar.jpg" alt="Admin Photo">
            <h4>Ahmed Houimel</h4>
            <p>Administrateur</p>
        </div>
        <div class="sidebar-menu">
            <a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="admin-projects.html"><i class="fas fa-project-diagram"></i> Projets</a>
            <a href="admin-formations.html"><i class="fas fa-graduation-cap"></i> Formations</a>
            <a href="admin-events.html"><i class="fas fa-calendar-alt"></i> Événements</a>
            <a href="admin-investments.html" class="active"><i class="fas fa-chart-line"></i> Investissements</a>
            <a href="reclamations.php"><i class="fas fa-exclamation-circle"></i> Réclamations</a>
            <a href="admin-settings.html"><i class="fas fa-cog"></i> Paramètres</a>
            <a href="logout.html"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
    <!-- Sidebar End -->

    <!-- Topbar Start -->
    <div class="topbar">
        <div class="topbar-search">
            <input type="text" placeholder="Rechercher...">
        </div>
        <div class="topbar-right">
            <a href="#" class="text-dark"><i class="fas fa-bell"></i></a>
            <a href="#" class="text-dark"><i class="fas fa-envelope"></i></a>
            <div class="dropdown">
                <a href="#" class="text-dark dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="img/admin-avatar.jpg" alt="Admin" style="width: 32px; height: 32px; border-radius: 50%;">
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-12 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestion des Investissements</h2>
                    <a href="http://localhost/skillboost/Views/front/" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Voir le site
                    </a>
                </div>

                <!-- Cards Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Investissements</h5>
                                <h2 class="card-text"><?php echo number_format($total_investissements, 2); ?> €</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Investissements Actifs</h5>
                                <h2 class="card-text"><?php echo $investissements_actifs; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">En Attente</h5>
                                <h2 class="card-text"><?php echo $investissements_en_attente; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Investissements Annulés</h5>
                                <h2 class="card-text"><?php echo $investissements_annules; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="card">
                    <div class="card-body">
                        <!-- Export PDF Button for Investissements -->
                        <div class="mb-2 d-flex justify-content-end">
                            <button class="btn btn-danger" onclick="exportInvestissementsPDF()">
                                <i class="fas fa-file-pdf"></i> Exporter Investissements en PDF
                            </button>
                        </div>
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
                                        <td><?php echo $investissement['id']; ?></td>
                                        <td><?php echo htmlspecialchars($investissement['titre_projet']); ?></td>
                                        <td><?php echo htmlspecialchars($investissement['nom_investisseur']); ?></td>
                                        <td><?php echo number_format($investissement['montant'], 2); ?> €</td>
                                        <td><?php echo $investissement['pourcentage']; ?>%</td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($investissement['date_creation'])); ?></td>
                                        <td>
                                            <?php
                                            $statut_class = [
                                                'Accepté' => 'success',
                                                'Proposé' => 'warning',
                                                'Refusé' => 'danger'
                                            ][$investissement['statut']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $statut_class; ?>">
                                                <?php echo $investissement['statut']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btn-action edit-investment" 
                                                    data-id="<?php echo $investissement['id']; ?>"
                                                    data-montant="<?php echo $investissement['montant']; ?>"
                                                    data-pourcentage="<?php echo $investissement['pourcentage']; ?>"
                                                    data-statut="<?php echo $investissement['statut']; ?>"
                                                    data-date="<?php echo $investissement['date_creation']; ?>"
                                                    onclick="editInvestissement(this)">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-action delete-investment" 
                                                    data-id="<?php echo $investissement['id']; ?>"
                                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet investissement ?')) deleteInvestissement(<?php echo $investissement['id']; ?>)">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tableau des Revenus -->
                <h2 class="section-title">Revenus des Projets</h2>
                <div class="accordion" id="accordionProjets">
                    <?php
                    // Regrouper les revenus par projet
                    $revenus_par_projet = [];
                    if (!empty($revenus_projets)) {
                        foreach ($revenus_projets as $revenu) {
                            $projet_id = $revenu['id_projet'];
                            if (!isset($revenus_par_projet[$projet_id])) {
                                $revenus_par_projet[$projet_id] = [
                                    'titre' => $revenu['titre_projet'],
                                    'revenus' => []
                                ];
                            }
                            $revenus_par_projet[$projet_id]['revenus'][] = $revenu;
                        }
                    }
                    
                    // Afficher les projets et leurs revenus
                    foreach ($revenus_par_projet as $projet_id => $projet):
                        $total_revenus = array_sum(array_column($projet['revenus'], 'montant'));
                    ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light" id="heading<?php echo $projet_id; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <button class="btn btn-link text-dark text-decoration-none" type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse<?php echo $projet_id; ?>" 
                                            aria-expanded="true" 
                                            aria-controls="collapse<?php echo $projet_id; ?>">
                                        <i class="fas fa-chevron-down me-2"></i>
                                        <?php echo htmlspecialchars($projet['titre']); ?>
                                    </button>
                                </h5>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-3">
                                        Total: <?php echo number_format($total_revenus, 2); ?> €
                                    </span>
                                    <button class="btn btn-primary btn-sm" onclick="addRevenu(<?php echo $projet_id; ?>, '<?php echo htmlspecialchars($projet['titre']); ?>')">
                                        <i class="fas fa-plus"></i> Ajouter un revenu
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Export PDF Button for Revenus -->
                        <div class="mb-2 d-flex justify-content-end" style="padding: 0 20px;">
                            <button class="btn btn-danger" onclick="exportRevenusPDF(<?php echo $projet_id; ?>)">
                                <i class="fas fa-file-pdf"></i> Exporter Revenus en PDF
                            </button>
                        </div>
                        <div id="collapse<?php echo $projet_id; ?>" class="collapse" aria-labelledby="heading<?php echo $projet_id; ?>" data-bs-parent="#accordionProjets">
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
                                                <td><?php echo $revenu['id']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($revenu['date_revenu'])); ?></td>
                                                <td><?php echo number_format($revenu['montant'], 2); ?> €</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm btn-action edit-revenue" 
                                                            data-id="<?php echo $revenu['id']; ?>"
                                                            data-montant="<?php echo $revenu['montant']; ?>"
                                                            data-date="<?php echo $revenu['date_revenu']; ?>"
                                                            onclick="editRevenu(this)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-action delete-revenue" 
                                                            data-id="<?php echo $revenu['id']; ?>"
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce revenu ?')) deleteRevenu(<?php echo $revenu['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

                    <?php if (empty($revenus_par_projet)): ?>
                    <div class="alert alert-info">
                        Aucun revenu enregistré pour le moment.
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Modal d'ajout de revenu -->
                <div class="modal fade" id="addRevenueModal" tabindex="-1" aria-labelledby="addRevenueModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addRevenueModalLabel">Ajouter un revenu</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addRevenueForm">
                                    <input type="hidden" id="addRevenueProjetId" name="id_projet">
                                    <div class="mb-3">
                                        <label class="form-label">Projet</label>
                                        <input type="text" class="form-control" id="addRevenueProjetTitre" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="addRevenueMontant" class="form-label">Montant</label>
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
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="button" class="btn btn-primary" onclick="saveNewRevenu()">Ajouter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="footer-links">
                <div class="footer-section">
                    <h5>À propos</h5>
                    <ul>
                        <li><a href="#">Qui sommes-nous</a></li>
                        <li><a href="#">Notre mission</a></li>
                        <li><a href="#">Carrières</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Services</h5>
                    <ul>
                        <li><a href="#">Investissements</a></li>
                        <li><a href="#">Formations</a></li>
                        <li><a href="#">Projets</a></li>
                        <li><a href="#">Événements</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Support</h5>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Centre d'aide</a></li>
                        <li><a href="#">Réclamations</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h5>Contact</h5>
                    <ul>
                        <li><i class="fas fa-phone me-2"></i> +216 XX XXX XXX</li>
                        <li><i class="fas fa-envelope me-2"></i> contact@skillboost.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Tunis, Tunisie</li>
                    </ul>
                </div>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; 2024 SkillBoost. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Modal de modification d'investissement -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier l'investissement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-3">
                            <label for="editMontant" class="form-label">Montant</label>
                            <input type="number" class="form-control" id="editMontant" name="montant" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPourcentage" class="form-label">Pourcentage</label>
                            <input type="number" class="form-control" id="editPourcentage" name="pourcentage" min="0" max="100" step="0.1" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDate" class="form-label">Date d'investissement</label>
                            <input type="datetime-local" class="form-control" id="editDate" name="date_investissement" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatut" class="form-label">Statut</label>
                            <select class="form-control" id="editStatut" name="statut" required>
                                <option value="Proposé">Proposé</option>
                                <option value="Accepté">Accepté</option>
                                <option value="Refusé">Refusé</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="updateInvestissement()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification de revenu -->
    <div class="modal fade" id="editRevenueModal" tabindex="-1" aria-labelledby="editRevenueModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRevenueModalLabel">Modifier le revenu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editRevenueForm">
                        <input type="hidden" id="editRevenueId" name="id">
                        <div class="mb-3">
                            <label for="editRevenueMontant" class="form-label">Montant</label>
                            <input type="number" class="form-control" id="editRevenueMontant" name="montant" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRevenueDate" class="form-label">Date du revenu</label>
                            <input type="date" class="form-control" id="editRevenueDate" name="date_revenu" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="updateRevenu()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jsPDF and html2canvas for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Custom JS -->
    <script>
    // Fonction pour ouvrir le modal de modification
    function editInvestissement(button) {
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        document.getElementById('editId').value = button.dataset.id;
        document.getElementById('editMontant').value = button.dataset.montant;
        document.getElementById('editPourcentage').value = button.dataset.pourcentage;
        document.getElementById('editStatut').value = button.dataset.statut;
        document.getElementById('editDate').value = formatDateForInput(button.dataset.date);
        modal.show();
    }

    function formatDateForInput(dateString) {
        const date = new Date(dateString);
        return date.toISOString().slice(0, 16);
    }

    // Fonction pour mettre à jour l'investissement
    function updateInvestissement() {
        const formData = {
            id: document.getElementById('editId').value,
            montant: document.getElementById('editMontant').value,
            pourcentage: document.getElementById('editPourcentage').value,
            statut: document.getElementById('editStatut').value,
            date_investissement: document.getElementById('editDate').value
        };

        fetch('../../controllers/investissementsController.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Réponse invalide du serveur');
                }
            });
        })
        .then(data => {
            if (data.success) {
                // Mettre à jour la ligne dans le tableau
                const row = document.querySelector(`button[data-id="${formData.id}"]`).closest('tr');
                row.querySelector('td:nth-child(4)').textContent = Number(formData.montant).toFixed(2) + ' €';
                row.querySelector('td:nth-child(5)').textContent = formData.pourcentage + '%';
                
                // Mettre à jour le badge de statut
                const statusBadge = row.querySelector('td:nth-child(7) .badge');
                const statusClasses = {
                    'Accepté': 'bg-success',
                    'Proposé': 'bg-warning',
                    'Refusé': 'bg-danger'
                };
                statusBadge.className = `badge ${statusClasses[formData.statut]}`;
                statusBadge.textContent = formData.statut;

                // Mettre à jour les compteurs
                if (data.counters) {
                    updateCounters(data.counters);
                }
                
                // Fermer le modal
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                
                // Afficher un message de succès
                showAlert('Investissement modifié avec succès !', 'success');
            } else {
                showAlert(data.error || 'Erreur lors de la modification', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Une erreur est survenue lors de la modification', 'danger');
        });
    }

    // Fonction pour supprimer un investissement
    function deleteInvestissement(id) {
        fetch('../../controllers/investissementsController.php?action=delete&id=' + id, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Réponse invalide du serveur');
                }
            });
        })
        .then(data => {
            if (data.success) {
                // Supprimer la ligne du tableau
                const row = document.querySelector(`button[data-id="${id}"]`).closest('tr');
                row.remove();
                
                // Mettre à jour les compteurs
                if (data.counters) {
                    updateCounters(data.counters);
                }
                
                // Afficher un message de succès
                showAlert('Investissement supprimé avec succès !', 'success');
            } else {
                showAlert(data.error || 'Erreur lors de la suppression', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Une erreur est survenue lors de la suppression', 'danger');
        });
    }

    // Fonction pour mettre à jour les compteurs
    function updateCounters(counters) {
        if (!counters) return;
        
        const elements = {
            'total-investissements': counters.total_investissements + ' €',
            'investissements-actifs': counters.investissements_actifs,
            'investissements-en-attente': counters.investissements_en_attente,
            'investissements-annules': counters.investissements_annules
        };

        for (const [className, value] of Object.entries(elements)) {
            const element = document.querySelector('.' + className);
            if (element) {
                element.textContent = value;
            }
        }
    }

    // Fonction pour afficher les messages d'alerte
    function showAlert(message, type) {
        // Créer l'élément d'alerte
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '1050';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Ajouter l'alerte au document
        document.body.appendChild(alertDiv);

        // Supprimer l'alerte après 3 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Fonctions pour les revenus
    function editRevenu(button) {
        const modal = new bootstrap.Modal(document.getElementById('editRevenueModal'));
        document.getElementById('editRevenueId').value = button.dataset.id;
        document.getElementById('editRevenueMontant').value = button.dataset.montant;
        document.getElementById('editRevenueDate').value = formatDateForInput(button.dataset.date);
        modal.show();
    }

    function updateRevenu() {
        const formData = {
            id: document.getElementById('editRevenueId').value,
            montant: document.getElementById('editRevenueMontant').value,
            date_revenu: document.getElementById('editRevenueDate').value
        };

        fetch('../../controllers/investissementsController.php?action=updateRevenu', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Réponse invalide du serveur');
                }
            });
        })
        .then(data => {
            if (data.success) {
                location.reload(); // Recharger la page pour afficher les modifications
                showAlert('Revenu modifié avec succès !', 'success');
            } else {
                showAlert(data.error || 'Erreur lors de la modification', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Une erreur est survenue lors de la modification', 'danger');
        });
    }

    function deleteRevenu(id) {
        fetch('../../controllers/investissementsController.php?action=deleteRevenu&id=' + id, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Réponse invalide du serveur');
                }
            });
        })
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`button[data-id="${id}"]`).closest('tr');
                row.remove();
                showAlert('Revenu supprimé avec succès !', 'success');
            } else {
                showAlert(data.error || 'Erreur lors de la suppression', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Une erreur est survenue lors de la suppression', 'danger');
        });
    }

    function addRevenu(projetId, projetTitre) {
        const modal = new bootstrap.Modal(document.getElementById('addRevenueModal'));
        document.getElementById('addRevenueProjetId').value = projetId;
        document.getElementById('addRevenueProjetTitre').value = projetTitre;
        document.getElementById('addRevenueMontant').value = '';
        document.getElementById('addRevenueDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('addRevenueDescription').value = '';
        modal.show();
    }

    function saveNewRevenu() {
        const formData = {
            id_projet: document.getElementById('addRevenueProjetId').value,
            montant: document.getElementById('addRevenueMontant').value,
            date_revenu: document.getElementById('addRevenueDate').value,
            description: document.getElementById('addRevenueDescription').value
        };

        fetch('../../controllers/investissementsController.php?action=addRevenu', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Réponse invalide du serveur');
                }
            });
        })
        .then(data => {
            if (data.success) {
                location.reload(); // Recharger la page pour afficher le nouveau revenu
                showAlert('Revenu ajouté avec succès !', 'success');
            } else {
                showAlert(data.error || 'Erreur lors de l\'ajout', 'danger');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Une erreur est survenue lors de l\'ajout', 'danger');
        });
    }

    function exportInvestissementsPDF() {
        const table = document.querySelector('.table-responsive table');
        html2canvas(table).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('l', 'pt', 'a4');
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
            pdf.addImage(imgData, 'PNG', 10, 10, pdfWidth - 20, pdfHeight);
            pdf.save('investissements.pdf');
        });
    }

    function exportRevenusPDF(projetId) {
        const table = document.querySelector(`#collapse${projetId} .table-responsive table`);
        html2canvas(table).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('l', 'pt', 'a4');
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
            pdf.addImage(imgData, 'PNG', 10, 10, pdfWidth - 20, pdfHeight);
            pdf.save('revenus_projet_' + projetId + '.pdf');
        });
    }

    let sortOrder = 'asc';
    function toggleSortProjects() {
        sortOrder = (sortOrder === 'asc') ? 'desc' : 'asc';
        sortProjects(sortOrder);
        // Change icon
        document.getElementById('sortResteIcon').className = sortOrder === 'asc'
            ? 'fas fa-sort-amount-up'
            : 'fas fa-sort-amount-down';
    }

    function sortProjects(order) {
        const projectsList = document.getElementById('projects-list');
        const cards = Array.from(projectsList.getElementsByClassName('project-card'));
        cards.sort((a, b) => {
            const resteA = parseFloat(a.querySelector('.reste-collecter').textContent.replace(/[^0-9.,]/g, '').replace(',', '.'));
            const resteB = parseFloat(b.querySelector('.reste-collecter').textContent.replace(/[^0-9.,]/g, '').replace(',', '.'));
            return order === 'asc' ? resteA - resteB : resteB - resteA;
        });
        cards.forEach(card => projectsList.appendChild(card));
    }
    </script>
</body>
</html> 