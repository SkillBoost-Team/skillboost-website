<?php
require_once '../../controller/reservationcontroller.php';
require_once '../../model/reservation.php';

$controller = new ReservationController();
$list = $controller->listReservations();

// Filtrer les réservations selon les critères de recherche
$statut_inscription = isset($_GET['statut_inscription']) ? $_GET['statut_inscription'] : '';
$methode_paiement = isset($_GET['methode_paiement']) ? $_GET['methode_paiement'] : '';
$date_inscription = isset($_GET['date_inscription']) ? $_GET['date_inscription'] : '';

if (isset($list) && is_array($list)) {
    $reservations = $list;  // Assigner la liste de réservations si elle est valide
} else {
    $reservations = [];  // Si $list est vide ou invalide, on initialise $reservations comme un tableau vide
}

// Appliquer les filtres
if (!empty($statut_inscription) || !empty($methode_paiement) || !empty($date_inscription)) {
    $filteredReservations = [];
    foreach ($reservations as $r) {
        if (
            ($statut_inscription === '' || $r['statut_inscription'] === $statut_inscription) &&
            ($methode_paiement === '' || $r['methode_paiement'] === $methode_paiement) &&
            ($date_inscription === '' || $r['date_inscription'] === $date_inscription)
        ) {
            $filteredReservations[] = $r;
        }
    }
    $reservations = $filteredReservations;
}

// Définir les variables de comptage
$en_attente = 0;
$payer = 0;
$annulee = 0;
$total_reservations = 0;

foreach ($reservations as $r) {
    if ($r['statut_inscription'] == 'en attente') $en_attente++;
    elseif ($r['statut_inscription'] == 'payée') $payer++;
    elseif ($r['statut_inscription'] == 'annulée') $annulee++;
    $total_reservations++;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SkillBoost - Gestion des Réservations</title>
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
        .badge-en-attente { background-color: #ffc107; }
        .badge-paye { background-color: #198754; }
        .badge-annulee { background-color: #dc3545; }
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
            cursor: pointer;
        }
        .table th.sort-asc::after {
            content: ' ▲';
            color: #0dcaf0;
        }
        .table th.sort-desc::after {
            content: ' ▼';
            color: #0dcaf0;
        }
        .table th.sortable {
            color: #0dcaf0;
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
                    <a href="admin-events.php" class="nav-item nav-link">Événements</a>
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
                            <h2 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Gestion des Réservations</h2>
                            <div>
                                <a href="ajouter_reservation.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Nouvelle Réservation
                                </a>
                            </div>
                        </div>
                        <!-- Cartes Statistiques -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card bg-warning">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $en_attente ?></div>
                                            <div class="title">En attente</div>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-success">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $payer ?></div>
                                            <div class="title">Payées</div>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-danger">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $annulee ?></div>
                                            <div class="title">Annulées</div>
                                        </div>
                                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card bg-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="count"><?= $total_reservations ?></div>
                                            <div class="title">Total</div>
                                        </div>
                                        <i class="fas fa-list fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Filtres -->
                        <div class="filter-section mb-4">
                            <form id="filterForm" method="GET" action="">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Statut d'inscription</label>
                                        <select class="form-select" name="statut_inscription" id="statut_inscription">
                                            <option value="">Tous les statuts</option>
                                            <option value="en attente" <?= $statut_inscription == 'en attente' ? 'selected' : '' ?>>En attente</option>
                                            <option value="payée" <?= $statut_inscription == 'payée' ? 'selected' : '' ?>>Payée</option>
                                            <option value="annulée" <?= $statut_inscription == 'annulée' ? 'selected' : '' ?>>Annulée</option>
                                        </select>
                                        <div class="field-error" id="statut_inscription-error"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Méthode de paiement</label>
                                        <select class="form-select" name="methode_paiement" id="methode_paiement">
                                            <option value="">Toutes les méthodes</option>
                                            <option value="carte bancaire" <?= $methode_paiement == 'carte bancaire' ? 'selected' : '' ?>>Carte bancaire</option>
                                            <option value="virement bancaire" <?= $methode_paiement == 'virement bancaire' ? 'selected' : '' ?>>Virement bancaire</option>
                                            <option value="paypal" <?= $methode_paiement == 'paypal' ? 'selected' : '' ?>>PayPal</option>
                                        </select>
                                        <div class="field-error" id="methode_paiement-error"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date d'inscription</label>
                                        <input type="date" class="form-control" name="date_inscription" id="date_inscription" value="<?= htmlspecialchars($date_inscription) ?>">
                                        <div class="field-error" id="date_inscription-error"></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-secondary">Rechercher</button>
                                </div>
                            </form>
                        </div>
                        <!-- Tableau des réservations -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="reservationTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sortable" data-sort="idevenement">ID Événement</th>
                                                <th class="sortable" data-sort="id_utilisateur">ID Utilisateur</th>
                                                <th class="sortable" data-sort="date_inscription">Date Inscription</th>
                                                <th class="sortable" data-sort="nombre_places">Nombre Places</th>
                                                <th class="sortable" data-sort="statut_inscription">Statut Inscription</th>
                                                <th class="sortable" data-sort="methode_paiement">Méthode Paiement</th>
                                                <th class="sortable" data-sort="montant_paye">Montant Payé</th>
                                                <th class="sortable" data-sort="id_reservation">ID Réservation</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations as $r): ?>
                                                <tr>
                                                    <td><?= isset($r['idevenement']) ? htmlspecialchars($r['idevenement']) : 'ID non défini' ?></td>
                                                    <td><?= isset($r['id_utilisateur']) ? htmlspecialchars($r['id_utilisateur']) : 'ID Utilisateur non défini' ?></td>
                                                    <td><?= isset($r['date_inscription']) ? htmlspecialchars($r['date_inscription']) : 'Date non définie' ?></td>
                                                    <td><?= isset($r['nombre_places']) ? htmlspecialchars($r['nombre_places']) : 'Nombre de places non défini' ?></td>
                                                    <td>
                                                        <?php
                                                            $statut_inscription = isset($r['statut_inscription']) ? $r['statut_inscription'] : 'Statut non défini';
                                                            $badge = match($statut_inscription) {
                                                                'en attente' => 'badge-en-attente',
                                                                'payée' => 'badge-paye',
                                                                'annulée' => 'badge-annulee',
                                                                default => 'badge-secondary'
                                                            };
                                                        ?>
                                                        <span class="badge <?= $badge ?>"><?= $statut_inscription ?></span>
                                                    </td>
                                                    <td><?= isset($r['methode_paiement']) ? htmlspecialchars($r['methode_paiement']) : 'Méthode de paiement non définie' ?></td>
                                                    <td><?= isset($r['montant_paye']) ? htmlspecialchars($r['montant_paye']) : 'Montant payé non défini' ?></td>
                                                    <td><?= isset($r['id_reservation']) ? htmlspecialchars($r['id_reservation']) : 'ID non défini' ?></td>
                                                    <td>
                                                        <a href="voir_reservation.php?id=<?= isset($r['id_reservation']) ? $r['id_reservation'] : '' ?>" class="btn btn-sm btn-info action-btn">👁️</a>
                                                        <a href="update_reservation.php?id=<?= isset($r['id_reservation']) ? $r['id_reservation'] : '' ?>" class="btn btn-sm btn-warning action-btn">✏️</a>
                                                        <a href="delate_reservation.php?id=<?= isset($r['id_reservation']) ? $r['id_reservation'] : '' ?>" class="btn btn-sm btn-danger action-btn" onclick="return confirm('Supprimer cette réservation ?')">🗑️</a>
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

        // Validation en temps réel pour les filtres
        document.getElementById('statut_inscription').addEventListener('change', validateFilters);
        document.getElementById('methode_paiement').addEventListener('change', validateFilters);
        document.getElementById('date_inscription').addEventListener('change', validateFilters);

        function validateFilters() {
            const statut_inscription = document.getElementById('statut_inscription');
            const methode_paiement = document.getElementById('methode_paiement');
            const date_inscription = document.getElementById('date_inscription');

            // Réinitialiser les messages d'erreur
            document.getElementById('statut_inscription-error').innerText = '';
            document.getElementById('methode_paiement-error').innerText = '';
            document.getElementById('date_inscription-error').innerText = '';

            let valid = true;

            // Vérif Statut d'inscription : non vide
            if (statut_inscription.value === '') {
                showError(statut_inscription, 'statut_inscription-error', 'Le statut d\'inscription est obligatoire.');
                valid = false;
            } else {
                hideError(statut_inscription, 'statut_inscription-error');
            }

            // Vérif Méthode de paiement : non vide
            if (methode_paiement.value === '') {
                showError(methode_paiement, 'methode_paiement-error', 'La méthode de paiement est obligatoire.');
                valid = false;
            } else {
                hideError(methode_paiement, 'methode_paiement-error');
            }

            // Vérif Date d'inscription : non vide
            if (date_inscription.value === '') {
                showError(date_inscription, 'date_inscription-error', 'La date d\'inscription est obligatoire.');
                valid = false;
            } else {
                hideError(date_inscription, 'date_inscription-error');
            }

            return valid;
        }

        // Soumission du formulaire de filtre
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            if (!validateFilters()) {
                e.preventDefault();
            }
        });

        // Fonctions de tri
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('reservationTable');
            const headers = table.querySelectorAll('th.sortable');
            const tableBody = table.querySelector('tbody');
            const rows = Array.from(tableBody.querySelectorAll('tr'));

            let sortOrder = {};

            headers.forEach(header => {
                header.addEventListener('click', () => {
                    const sortKey = header.getAttribute('data-sort');
                    const currentSortOrder = sortOrder[sortKey] || 'asc';

                    // Réinitialiser les classes de tri
                    headers.forEach(th => {
                        th.classList.remove('sort-asc', 'sort-desc');
                    });

                    // Définir la nouvelle classe de tri
                    if (currentSortOrder === 'asc') {
                        header.classList.add('sort-desc');
                        sortOrder[sortKey] = 'desc';
                    } else {
                        header.classList.add('sort-asc');
                        sortOrder[sortKey] = 'asc';
                    }

                    // Tri des lignes
                    rows.sort((rowA, rowB) => {
                        const cellA = rowA.querySelector(`td:nth-child(${header.cellIndex + 1})`).innerText.trim();
                        const cellB = rowB.querySelector(`td:nth-child(${header.cellIndex + 1})`).innerText.trim();

                        switch (sortKey) {
                            case 'idevenement':
                            case 'id_utilisateur':
                            case 'nombre_places':
                            case 'montant_paye':
                            case 'id_reservation':
                                return currentSortOrder === 'asc' ? cellA - cellB : cellB - cellA;
                            default:
                                return currentSortOrder === 'asc' ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                        }
                    });

                    // Ajout des lignes triées dans le tbody
                    rows.forEach(row => tableBody.appendChild(row));
                });
            });
        });

        function showError(field, errorId, message) {
            document.getElementById(errorId).textContent = message;
            document.getElementById(errorId).style.display = 'block';
            field.classList.add('error-border');
            field.focus();
        }

        function hideError(field, errorId) {
            document.getElementById(errorId).textContent = '';
            document.getElementById(errorId).style.display = 'none';
            field.classList.remove('error-border');
        }
    </script>
</body>
</html>