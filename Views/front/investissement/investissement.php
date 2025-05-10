<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../controllers/investissementControllers.php';

session_start();

// Set the user ID and role here for testing. Change these values to simulate different users.
$_SESSION['user_id'] = 2;// Change this number to simulate a different user
$_SESSION['role'] = 'createur'; // Set the role to 'createur' for testing

$db = Database::getInstance()->getConnection();
$controller = InvestissementController::getInstance($db);

// Mettre à jour les lignes de revenus pour tous les projets complétés
$controller->updateRevenusProjet();

$id_user = $_SESSION['user_id'];

// Pour le test, on peut vérifier si l'utilisateur est à la fois investisseur et créateur
$is_creator = true;  // À remplacer par une vérification réelle des droits

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_projet']) && isset($_POST['montant']) && isset($_POST['pourcentage'])) {
        try {
            // Validate and sanitize the input data
            $montant = filter_var($_POST['montant'], FILTER_VALIDATE_FLOAT);
            $pourcentage = filter_var($_POST['pourcentage'], FILTER_VALIDATE_FLOAT);
            
            if ($montant === false || $pourcentage === false) {
                throw new Exception("Les valeurs saisies ne sont pas valides. Veuillez entrer des nombres.");
            }
            
            if ($montant <= 0 || $pourcentage <= 0) {
                throw new Exception("Le montant et le pourcentage doivent être supérieurs à 0.");
            }
            
            $result = $controller->store([
                'id_projet' => (int)$_POST['id_projet'],
                'montant' => $montant,
                'pourcentage' => $pourcentage
            ]);
            
            if ($result) {
                $message = "Investissement proposé avec succès.";
                $message_type = 'success';
            } else {
                $error = error_get_last();
                $message = $error ? $error['message'] : "Erreur lors de la proposition d'investissement.";
                $message_type = 'danger';
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// Récupérer tous les projets dans un tableau
$stmt = $controller->index();
$projets_array = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Filtrer d'abord les projets disponibles (avec montant_restant > 0)
$projets_disponibles = array_filter($projets_array, function($projet) {
    return $projet['montant_restant'] > 0;
});

// Configuration de la pagination
$projets_par_page = 3;
$nombre_total_projets = count($projets_disponibles);
$nombre_pages = ceil($nombre_total_projets / $projets_par_page);

// Récupération de la page courante
$page_courante = isset($_GET['page']) ? max(1, min($nombre_pages, intval($_GET['page']))) : 1;

// Calcul des indices de début et fin pour les projets à afficher
$debut = ($page_courante - 1) * $projets_par_page;
$projets_page = array_slice(array_values($projets_disponibles), $debut, $projets_par_page);

// Récupérer les projets du créateur séparément si l'utilisateur est un créateur
if ($is_creator) {
    $mes_projets = $controller->getMesProjetsEtInvestissements($id_user);
} else {
    $mes_projets = [];
}

// Récupérer les projets favoris depuis la session
$favoris = isset($_SESSION['favoris']) ? $_SESSION['favoris'] : [];
$projets_favoris = [];
if (!empty($favoris)) {
    $placeholders = implode(',', array_fill(0, count($favoris), '?'));
    $stmt = $db->prepare("SELECT * FROM projet WHERE id IN ($placeholders)");
    $stmt->execute($favoris);
    $projets_favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les gains des investissements
$gains_investissements = $controller->getGainsInvestissements($id_user);

// Regrouper les investissements par projet pour l'affichage global
$gains_par_projet = [];
foreach ($gains_investissements as $inv) {
    $id_projet = $inv['id_projet'];
    if (!isset($gains_par_projet[$id_projet])) {
        $gains_par_projet[$id_projet] = [
            'titre_projet' => $inv['titre_projet'],
            'montant_objectif' => $inv['montant_objectif'],
            'montant_investi' => 0,
            'pourcentage' => 0,
            'revenus' => [],
        ];
    }
    $gains_par_projet[$id_projet]['montant_investi'] += $inv['montant_investi'];
    $gains_par_projet[$id_projet]['pourcentage'] += $inv['pourcentage'];
    // Fusionner les revenus par date
    foreach ($inv['revenus'] as $rev) {
        $date = $rev['date_revenu'];
        if (!isset($gains_par_projet[$id_projet]['revenus'][$date])) {
            $gains_par_projet[$id_projet]['revenus'][$date] = [
                'gain_mensuel' => 0,
                'gains_cumules' => 0,
            ];
        }
        $gains_par_projet[$id_projet]['revenus'][$date]['gain_mensuel'] += $rev['gain_mensuel'];
        // On recalculera les gains cumulés après
    }
}
// Recalculer les gains cumulés pour chaque projet
foreach ($gains_par_projet as &$projet) {
    ksort($projet['revenus']);
    $cumul = 0;
    foreach ($projet['revenus'] as &$rev) {
        $cumul += $rev['gain_mensuel'];
        $rev['gains_cumules'] = $cumul;
    }
    unset($rev);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>SkillBoost - Investissements</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">

    <style>
    /* Favoris - étoile sur carte projet */
    .btn-favori {
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0.25rem 0.5rem;
        font-size: 1.3rem;
        z-index: 2;
        transition: color 0.2s;
    }
    .btn-favori .fa-star,
    .btn-favori .fa-star-o {
        transition: color 0.2s;
    }
    .btn-favori:hover .fa-star,
    .btn-favori:hover .fa-star-o {
        color: #ffc107 !important;
    }
    .btn-favori.selected .fa-star {
        color: #ffc107 !important;
    }
    .btn-favori:focus {
        outline: none;
        box-shadow: none;
    }
    /* Favoris - bouton poubelle dans le modal */
    .btn-remove-favori {
        background: #fff;
        border: none;
        color: #dc3545;
        padding: 0.35rem 0.6rem;
        font-size: 1.15rem;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        position: absolute;
        top: 14px;
        right: 18px;
        z-index: 2;
        outline: none;
    }
    .btn-remove-favori:hover {
        background: #dc3545;
        color: #fff;
        box-shadow: 0 4px 16px rgba(220,53,69,0.18);
    }
    /* Favoris - carte projet dans le modal */
    .favori-card {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        transition: box-shadow 0.25s, transform 0.18s, opacity 0.3s;
        cursor: pointer;
        border: none;
        background: #fff;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: stretch;
        position: relative;
        margin-bottom: 1.5rem;
        opacity: 1;
    }
    .favori-card.removing {
        opacity: 0;
        transform: scale(0.97) translateY(10px);
        transition: opacity 0.3s, transform 0.3s;
    }
    .favori-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.16);
        transform: translateY(-3px) scale(1.012);
    }
    .favori-card .card-header {
        border-radius: 18px 18px 0 0;
        background: linear-gradient(90deg, #0099e6 60%, #00c6fb 100%);
        color: #fff;
        position: relative;
        padding: 1rem 2.5rem 1rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .favori-card .card-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 0;
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .favori-card .fa-star {
        color: #ffc107;
        font-size: 1.2rem;
        margin-left: 0.5rem;
    }
    .favori-card .badge-favori {
        background: #ffc107;
        color: #222;
        font-size: 0.85rem;
        border-radius: 8px;
        padding: 0.2em 0.7em;
        margin-left: 0.7em;
        font-weight: 600;
    }
    .favori-card .card-body {
        padding: 1.1rem 1.2rem 1rem 1.2rem;
        flex: 1 1 auto;
        font-size: 1.01rem;
    }
    @media (max-width: 900px) {
        .favori-card {
            min-width: 100%;
            margin-bottom: 1.2rem;
        }
    }
    </style>
</head>

<body>
    <!-- Message d'alerte temporaire -->
    <?php if ($message): ?>
    <div id="alert-message" class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 9999;">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

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
                    <!-- Social media links (optional) -->
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar & Carousel Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="../index.html" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>SkillBoost</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="../index.html" class="nav-item nav-link">Accueil</a>
        
                    <a href="../login.php" class="nav-item nav-link">Connexion</a>
                    <a href="../projets.php" class="nav-item nav-link">Projets</a>
                    <a href="../formations.php" class="nav-item nav-link">Formations</a>
                    <a href="../evenements.php" class="nav-item nav-link">Événements</a>
                    <a href="investissement.php" class="nav-item nav-link active">Investissements</a>
                    <a href="../reclamations.php" class="nav-item nav-link">Réclamations</a>
                  
                </div>
            </div>
        </nav>


    <!-- Navbar & Carousel Start -->
    <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <!-- Premier slide -->
            <div class="carousel-item active">
                <img class="w-100" src="../img/carousel-1.jpg" alt="Entrepreneuriat">
                <div class="carousel-overlay"></div>
                <div class="carousel-caption d-flex flex-column justify-content-center">
                    <div class="container text-center py-5">
                        <div class="mb-4 animated slideInDown">
                            <h5 class="text-uppercase text-primary mb-3" style="letter-spacing: 3px; font-weight: 600;">PLATEFORME COMPLÈTE</h5>
                            <h1 class="display-3 text-white mb-4" style="font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                Boostez votre projet entrepreneurial
                            </h1>
                        </div>
                        <div class="animated fadeInUp">
                            <p class="lead text-light mb-5 mx-auto" style="max-width: 700px;">
                                Outils, formations et réseaux pour propulser votre startup vers le succès
                            </p>
                            <a href="#services" class="btn btn-primary btn-lg px-4 py-2">
                                Explorer nos solutions <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Deuxième slide -->
            <div class="carousel-item">
                <img class="w-100" src="../img/carousel-2.jpg" alt="Investissement">
                <div class="carousel-overlay"></div>
                <div class="carousel-caption d-flex flex-column justify-content-center">
                    <div class="container text-center py-5">
                        <div class="mb-4 animated slideInDown">
                            <h5 class="text-uppercase text-warning mb-3" style="letter-spacing: 3px; font-weight: 600;">INVESTISSEMENTS INTELLIGENTS</h5>
                            <h1 class="display-3 text-white mb-4" style="font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                Trouvez des opportunités prometteuses
                            </h1>
                        </div>
                        <div class="animated fadeInUp">
                            <p class="lead text-light mb-5 mx-auto" style="max-width: 700px;">
                                Connectez-vous avec les startups les plus innovantes du marché
                            </p>
                            <div class="d-flex justify-content-center gap-4">
                                <a href="#investissements" class="btn btn-primary btn-lg px-4 py-2">
                                    Voir les projets <i class="fas fa-lightbulb ms-2"></i>
                                </a>
                                <a href="#contact" class="btn btn-outline-light btn-lg px-4 py-2">
                                    Nous contacter <i class="fas fa-envelope ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Contrôles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>
    
    <style>
        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.6) 100%);
        }
        .carousel-caption {
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0;
        }
        .carousel-item {
            height: 100vh;
            min-height: 700px;
        }
        .carousel-item img {
            object-fit: cover;
            height: 100%;
        }
        @media (max-width: 768px) {
            .carousel-caption h1 {
                font-size: 2.5rem !important;
            }
            .carousel-caption p.lead {
                font-size: 1rem;
            }
        }
    </style>
    <!-- Navbar & Carousel End -->


    <!-- Full Screen Search Start -->
    <div class="modal fade" id="searchModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: rgba(9, 30, 62, .7);">
                <div class="modal-header border-0">
                    <button type="button" class="btn bg-white btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center">
                    <div class="input-group" style="max-width: 600px;">
                        <input type="text" class="form-control bg-transparent border-primary p-3" placeholder="Type search keyword">
                        <button class="btn btn-primary px-4"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Section Start -->
    <div class="container-fluid py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 500px;">
                <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">Projets Disponibles</h5>
                <h1 class="display-4">Investissez dans des Projets Prometteurs</h1>
            </div>

            <!-- Boutons en haut de la page -->
            <div class="text-center mb-4">
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#historiqueModal">
                    <i class="fas fa-history me-2"></i>Voir mes investissements
                </button>
                <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#favorisModal">
                    <i class="fas fa-star me-2"></i>Projets favoris
                </button>
                <?php if ($is_creator): ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#mesProjetsModal">
                    <i class="fas fa-project-diagram me-2"></i>Gérer mes projets
                </button>
                <?php endif; ?>
            </div>

            <!-- Modal pour l'historique des investissements -->
            <div class="modal fade" id="historiqueModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Mes investissements</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Filtres -->
                            <div class="mb-4">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="all">Tous</button>
                                    <button type="button" class="btn btn-outline-success filter-btn" data-filter="Accepté">Acceptés</button>
                                    <button type="button" class="btn btn-outline-warning filter-btn" data-filter="Proposé">Proposés</button>
                                    <button type="button" class="btn btn-outline-danger filter-btn" data-filter="Refusé">Refusés</button>
                                </div>
                            </div>

                            <!-- Liste des investissements -->
                            <div class="table-responsive mb-4">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Projet</th>
                                            <th>Montant</th>
                                            <th>Pourcentage</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $historique = $controller->getHistoriqueInvestissements($id_user);
                                        foreach ($historique as $invest): 
                                            $statut_class = [
                                                'Accepté' => 'success',
                                                'Proposé' => 'warning',
                                                'Refusé' => 'danger'
                                            ][$invest['statut']] ?? 'secondary';
                                        ?>
                                        <tr class="investissement-row" data-statut="<?php echo $invest['statut']; ?>">
                                            <td><?php echo htmlspecialchars($invest['projet_titre']); ?></td>
                                            <td><?php echo number_format($invest['montant'], 2); ?> DT</td>
                                            <td><?php echo $invest['pourcentage'] ? number_format($invest['pourcentage'], 1) . '%' : '-'; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($invest['date_investissement'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $statut_class; ?>">
                                                    <?php echo ucfirst($invest['statut']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Section des graphiques pour les projets complétés -->
                            <?php
                            if (!empty($gains_par_projet)):
                            ?>
                            <div class="row">
                                <?php foreach ($gains_par_projet as $id_projet => $projet):
                                    $total_investi = $projet['montant_investi'];
                                    $total_gains_mensuels = 0;
                                    $total_gains_cumules = 0;
                                    foreach ($projet['revenus'] as $rev) {
                                        $total_gains_mensuels += $rev['gain_mensuel'];
                                        $total_gains_cumules = $rev['gains_cumules'];
                                    }
                                    $roi = $total_investi > 0 ? (($total_gains_cumules - $total_investi) / $total_investi) * 100 : 0;
                                    $roi_class = $roi >= 0 ? 'text-success' : 'text-danger';
                                ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo htmlspecialchars($projet['titre_projet']); ?></h6>
                                            <p class="small text-muted">
                                                Investissement total: <?php echo number_format($total_investi, 2); ?> DT (<?php echo $projet['pourcentage']; ?>%)
                                            </p>
                                            <div class="row mb-3 justify-content-center gy-2 gx-3 flex-wrap">
                                                <div class="col-6 col-md-3">
                                                    <div class="card text-center shadow-sm border-0 h-100">
                                                        <div class="card-body py-3">
                                                            <div class="mb-2">
                                                                <i class="fas fa-coins fa-lg text-warning"></i>
                                                            </div>
                                                            <div class="fw-bold fs-5 text-dark">
                                                                <?php echo number_format($total_gains_mensuels, 2); ?> DT
                                                            </div>
                                                            <div class="small text-muted">Gains mensuels totaux</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="card text-center shadow-sm border-0 h-100">
                                                        <div class="card-body py-3">
                                                            <div class="mb-2">
                                                                <i class="fas fa-chart-line fa-lg text-primary"></i>
                                                            </div>
                                                            <div class="fw-bold fs-5 text-dark">
                                                                <?php echo number_format($total_gains_cumules, 2); ?> DT
                                                            </div>
                                                            <div class="small text-muted">Gains cumulés</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="card text-center shadow-sm border-0 h-100">
                                                        <div class="card-body py-3">
                                                            <div class="mb-2">
                                                                <i class="fas fa-percentage fa-lg <?php echo $roi_class; ?>"></i>
                                                            </div>
                                                            <div class="fw-bold fs-5 <?php echo $roi_class; ?>">
                                                                <?php echo number_format($roi, 2); ?>%
                                                            </div>
                                                            <div class="small text-muted">ROI</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <div class="card text-center shadow-sm border-0 h-100">
                                                        <div class="card-body py-3">
                                                            <div class="mb-2">
                                                                <i class="fas fa-balance-scale fa-lg <?php echo $roi_class; ?>"></i>
                                                            </div>
                                                            <div class="fw-bold fs-5 <?php echo $roi_class; ?>">
                                                                <?php echo number_format($total_gains_cumules - $total_investi, 2); ?> DT
                                                            </div>
                                                            <div class="small text-muted">Gain/Perte net</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert alert-info mb-3">
                                                <h6 class="alert-heading">Explication des gains :</h6>
                                                <ul class="mb-0">
                                                    <li><strong>Gains mensuels</strong> : Somme des montants gagnés chaque mois (tous investissements confondus)</li>
                                                    <li><strong>Gains cumulés</strong> : Somme totale des gains depuis le début de l'investissement</li>
                                                </ul>
                                            </div>
                                            <div class="d-flex justify-content-end mb-2">
                                                <button class="btn btn-sm btn-primary reset-zoom" data-chart-id="gainChartProjet<?php echo $id_projet; ?>">
                                                    <i class="fas fa-sync-alt me-1"></i> Réinitialiser la vue
                                                </button>
                                            </div>
                                            <div class="chart-container">
                                                <div class="chart-wrapper">
                                                    <canvas id="gainChartProjet<?php echo $id_projet; ?>"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour la gestion des projets -->
            <div class="modal fade" id="mesProjetsModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Gestion de mes projets</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php 
                            if (!empty($mes_projets)):
                            ?>
                                <div class="accordion" id="accordionProjets">
                                    <?php foreach ($mes_projets as $projet): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#projet<?php echo $projet['projet_id']; ?>">
                                                    <?php echo htmlspecialchars($projet['titre']); ?>
                                                    <span class="badge bg-primary ms-2">
                                                        <?php echo number_format($projet['montant_actuel'], 2); ?> / <?php echo number_format($projet['montant_objectif'], 2); ?> DT
                                                    </span>
                                                    <?php if ($projet['investissements_en_attente'] > 0): ?>
                                                    <span class="badge bg-warning ms-2">
                                                        <?php echo $projet['investissements_en_attente']; ?> en attente
                                                    </span>
                                                    <?php endif; ?>
                                                </button>
                                            </h2>
                                            <div id="projet<?php echo $projet['projet_id']; ?>" class="accordion-collapse collapse">
                                                <div class="accordion-body">
                                                    <!-- Progression -->
                                                    <div class="progress mb-3" style="height: 25px;">
                                                        <?php 
                                                        // Calcul du pourcentage basé uniquement sur les investissements acceptés
                                                        $pourcentage = ($projet['montant_actuel'] / $projet['montant_objectif']) * 100;
                                                        $couleur = "bg-danger";
                                                        if ($pourcentage >= 70) {
                                                            $couleur = "bg-success";
                                                        } elseif ($pourcentage >= 40) {
                                                            $couleur = "bg-warning";
                                                        }
                                                        ?>
                                                        <div class="progress-bar <?php echo $couleur; ?>" 
                                                             role="progressbar" 
                                                             style="width: <?php echo $pourcentage; ?>%"
                                                             aria-valuenow="<?php echo $pourcentage; ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?php echo round($pourcentage); ?>% (Investissements acceptés)
                                                        </div>
                                                    </div>

                                                    <!-- Tabs pour les investissements -->
                                                    <ul class="nav nav-tabs" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-bs-toggle="tab" 
                                                               href="#enAttente<?php echo $projet['projet_id']; ?>">
                                                                En attente
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab" 
                                                               href="#acceptes<?php echo $projet['projet_id']; ?>">
                                                                Acceptés
                                                            </a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab" 
                                                               href="#refuses<?php echo $projet['projet_id']; ?>">
                                                                Refusés
                                                            </a>
                                                        </li>
                                                        <?php if ($projet['montant_actuel'] >= $projet['montant_objectif']): ?>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-bs-toggle="tab" 
                                                               href="#revenus<?php echo $projet['projet_id']; ?>">
                                                                Revenus mensuels
                                                            </a>
                                                        </li>
                                                        <?php endif; ?>
                                                    </ul>

                                                    <!-- Contenu des tabs -->
                                                    <div class="tab-content pt-3">
                                                        <?php 
                                                        $investissements = $controller->getInvestissementsPourProjet($projet['projet_id']);
                                                        foreach(['Proposé' => 'enAttente', 'Accepté' => 'acceptes', 'Refusé' => 'refuses'] as $statut => $id):
                                                            $active = $statut === 'Proposé' ? ' show active' : '';
                                                        ?>
                                                        <div class="tab-pane fade<?php echo $active; ?>" 
                                                             id="<?php echo $id . $projet['projet_id']; ?>">
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Investisseur</th>
                                                                            <th>Montant</th>
                                                                            <th>Pourcentage</th>
                                                                            <th>Date</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php 
                                                                        $found = false;
                                                                        foreach ($investissements as $inv):
                                                                            if ($inv['statut'] === $statut):
                                                                                $found = true;
                                                                        ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?php echo htmlspecialchars($inv['nom_investisseur']); ?>
                                                                            </td>
                                                                            <td><?php echo number_format($inv['montant'], 2); ?> DT</td>
                                                                            <td><?php echo $inv['pourcentage']; ?>%</td>
                                                                            <td><?php echo date('d/m/Y H:i', strtotime($inv['date_investissement'])); ?></td>
                                                                            <td>
                                                                                <?php
                                                                                // Calculer si l'acceptation de cet investissement dépasserait le montant objectif
                                                                                $montant_total_si_accepte = $projet['montant_actuel'] + $inv['montant'];
                                                                                $peut_accepter = $montant_total_si_accepte <= $projet['montant_objectif'];
                                                                                
                                                                                if ($statut === 'Proposé'): ?>
                                                                                    <button class="btn btn-success btn-sm <?php echo !$peut_accepter ? 'disabled' : ''; ?>"
                                                                                            onclick="updateStatut(<?= $inv['id'] ?>, 'Accepté')"
                                                                                            <?php echo !$peut_accepter ? 'disabled title="L\'acceptation dépasserait le montant objectif"' : ''; ?>>
                                                                                    <i class="fas fa-check"></i> Accepter
                                                                                </button>
                                                                                <button class="btn btn-danger btn-sm"
                                                                                            onclick="updateStatut(<?= $inv['id'] ?>, 'Refusé')">
                                                                                    <i class="fas fa-times"></i> Refuser
                                                                                </button>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php 
                                                                            endif;
                                                                        endforeach;
                                                                        if (!$found):
                                                                        ?>
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">
                                                                                Aucun investissement <?php echo strtolower($statut); ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>

                                                        <?php if ($projet['montant_actuel'] >= $projet['montant_objectif']): 
                                                            $revenus_projets = $controller->getRevenusProjetComplet($id_user);
                                                            foreach ($revenus_projets as $projet_revenu):
                                                                if ($projet_revenu['projet_id'] === $projet['projet_id']):
                                                        ?>
                                                        <div class="tab-pane fade" id="revenus<?php echo $projet['projet_id']; ?>">
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Montant</th>
                                                                            <th>Statut</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($projet_revenu['revenus'] as $revenu): ?>
                                                                        <tr class="<?php echo $revenu['montant'] === null ? 'table-warning' : 'table-success'; ?>">
                                                                            <td><?php echo date('F Y', strtotime($revenu['date_revenu'])); ?></td>
                                                                            <td>
                                                                                <?php if ($revenu['montant'] === null): ?>
                                                                                    <span class="text-muted">Non renseigné</span>
                                                                                <?php else: ?>
                                                                                    <?php echo number_format($revenu['montant'], 2); ?> DT
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge bg-<?php echo $revenu['montant'] === null ? 'warning' : 'success'; ?>">
                                                                                    <?php echo $revenu['statut_revenu']; ?>
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <?php if ($revenu['montant'] === null): ?>
                                                                                <button class="btn btn-primary btn-sm" 
                                                                                        onclick="saisirRevenu(<?php echo $revenu['id']; ?>)">
                                                                                    <i class="fas fa-edit"></i> Saisir le revenu
                                                                                </button>
                                                                                <?php else: ?>
                                                                                <button class="btn btn-secondary btn-sm" 
                                                                                        onclick="modifierRevenu(<?php echo $revenu['id']; ?>, <?php echo $revenu['montant']; ?>)">
                                                                                    <i class="fas fa-pencil-alt"></i> Modifier
                                                                                </button>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <?php 
                                                                endif;
                                                            endforeach;
                                                        endif; 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Vous n'avez pas encore de projets.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour les favoris -->
            <div class="modal fade" id="favorisModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Mes projets favoris</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php if (!empty($projets_favoris)): ?>
                                <div class="row">
                                    <?php foreach ($projets_favoris as $projet): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card favori-card" id="favori-card-<?php echo $projet['id']; ?>">
                                                <button type="button"
                                                    class="btn btn-remove-favori"
                                                    title="Retirer des favoris"
                                                    onclick="removeFavoriWithFade(<?php echo $projet['id']; ?>)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <div class="card-header">
                                                    <span class="card-title">
                                                        <?php echo htmlspecialchars($projet['titre']); ?>
                                                        <i class="fas fa-star"></i>
                                                        <span class="badge-favori">Favori</span>
                                                    </span>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text text-muted mb-3">
                                                        <?php echo htmlspecialchars($projet['description']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">Aucun projet favori pour le moment.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sorting Buttons Start -->
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end">
                    <button id="sortResteBtn" class="btn btn-outline-primary" onclick="toggleSortProjects()">
                        <i id="sortResteIcon" class="fas fa-sort-amount-up"></i>
                        Trier par reste à collecter
                    </button>
                </div>
            </div>
            <!-- Sorting Buttons End -->

            <!-- Affichage des projets avec pagination -->
            <div class="row g-5 mb-5" id="projects-list">
                <?php if (!empty($projets_page)): ?>
                    <?php foreach ($projets_page as $projet): ?>
                        <?php $is_favori = in_array($projet['id'], $favoris); ?>
                        <div class="col-lg-4 col-md-6 project-card">
                            <div class="card h-100 shadow-sm hover-shadow position-relative">
                                <!-- Bouton favori -->
                                <button type="button"
                                    class="btn btn-favori position-absolute top-0 end-0 m-2 <?php echo $is_favori ? 'selected' : ''; ?>"
                                    title="<?php echo $is_favori ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>"
                                    data-favori-id="<?php echo $projet['id']; ?>"
                                    onclick="<?php echo $is_favori ? "removeFavoriWithFade({$projet['id']})" : "addToFavoris({$projet['id']})"; ?>">
                                    <i class="<?php echo $is_favori ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                                </button>
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($projet['titre']); ?></h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text text-muted mb-3">
                                        <?php echo htmlspecialchars($projet['description']); ?>
                                    </p>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user-tie text-primary me-2"></i>
                                            <small class="text-muted">
                                                Créé par: <?php echo htmlspecialchars($projet['nom_createur']); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="progress" style="height: 25px;">
                                            <?php 
                                            $pourcentage = ($projet['montant_actuel'] / $projet['montant']) * 100;
                                            $couleur = "bg-danger";
                                            if ($pourcentage >= 70) {
                                                $couleur = "bg-success";
                                            } elseif ($pourcentage >= 40) {
                                                $couleur = "bg-warning";
                                            }
                                            ?>
                                            <div class="progress-bar <?php echo $couleur; ?> progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $pourcentage; ?>%"
                                                 aria-valuenow="<?php echo $pourcentage; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?php echo round($pourcentage); ?>%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body p-2 text-center">
                                                    <h6 class="card-subtitle mb-1 text-muted small">Objectif</h6>
                                                    <h5 class="card-title mb-0"><?php echo number_format($projet['montant'], 2); ?> DT</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body p-2 text-center">
                                                    <h6 class="card-subtitle mb-1 text-muted small">Collecté</h6>
                                                    <h5 class="card-title mb-0"><?php echo number_format($projet['montant_actuel'], 2); ?> DT</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card bg-light mb-3">
                                        <div class="card-body p-2 text-center">
                                            <h6 class="card-subtitle mb-1 text-muted small">Reste à collecter</h6>
                                            <h5 class="card-title mb-0 text-primary reste-collecter"><?php echo number_format($projet['montant_restant'], 2); ?> DT</h5>
                                        </div>
                                    </div>

                                    <?php if ($projet['id_createur'] != $_SESSION['user_id']): ?>
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#investModal<?php echo $projet['id']; ?>">
                                            <i class="fas fa-hand-holding-usd me-2"></i>Investir
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Modal d'investissement -->
                        <div class="modal fade" id="investModal<?php echo $projet['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-hand-holding-usd me-2"></i>
                                            Investir dans <?php echo htmlspecialchars($projet['titre']); ?>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" data-type="investissement">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_projet" value="<?php echo $projet['id']; ?>">
                                            <input type="hidden" name="id_investisseur" value="<?php echo $id_user; ?>">
                                            
                                            <?php 
                                            $montant_investi = $controller->getMontantInvesti($projet['id'], $id_user);
                                            if ($montant_investi > 0): 
                                            ?>
                                            <div class="alert alert-info mb-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Vous avez déjà investi <strong><?php echo number_format($montant_investi, 2); ?> DT</strong> dans ce projet.
                                            </div>
                                            <?php endif; ?>

                                            <div class="mb-3">
                                                <label for="montant" class="form-label">
                                                    <i class="fas fa-money-bill-wave me-2"></i>Montant de l'investissement (DT)
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="montant" name="montant" 
                                                           data-max="<?php echo $projet['montant_restant']; ?>">
                                                    <span class="input-group-text">DT</span>
                                                </div>
                                                <small class="text-muted">Montant maximum: <?php echo number_format($projet['montant_restant'], 2); ?> DT</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="pourcentage" class="form-label">
                                                    <i class="fas fa-percent me-2"></i>Pourcentage de revenus demandé (%)
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="pourcentage" name="pourcentage">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="text-muted">Entrez le pourcentage de revenus que vous souhaitez obtenir (entre 1% et 100%)</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="fas fa-times me-2"></i>Annuler
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check me-2"></i>Investir
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun projet disponible pour l'investissement.
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($nombre_pages > 1): ?>
            <div class="row">
                <div class="col-12">
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center">
                            <!-- Bouton Précédent -->
                            <li class="page-item <?php echo $page_courante <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page_courante - 1; ?>" <?php echo $page_courante <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <!-- Numéros de pages -->
                            <?php for($i = 1; $i <= $nombre_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page_courante ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <!-- Bouton Suivant -->
                            <li class="page-item <?php echo $page_courante >= $nombre_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page_courante + 1; ?>" <?php echo $page_courante >= $nombre_pages ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Projects Section End -->

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

    <!-- footer end -->
    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>

    <!-- JavaScript pour la gestion de la pagination et du scroll -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du scroll pour la pagination
            const paginationLinks = document.querySelectorAll('.pagination .page-link');
            const projectsSection = document.querySelector('.container-fluid.py-5');

            // Sauvegarder la position du scroll dans le localStorage avant le changement de page
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Calculer la position relative du scroll par rapport à la section des projets
                    const rect = projectsSection.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const sectionTop = rect.top + scrollTop;
                    const relativeScroll = Math.max(0, scrollTop - sectionTop);

                    // Sauvegarder la position
                    localStorage.setItem('projectsScrollPosition', relativeScroll);
                });
            });

            // Restaurer la position du scroll après le chargement de la page
            const savedScrollPosition = localStorage.getItem('projectsScrollPosition');
            if (savedScrollPosition) {
                // Attendre que tout soit chargé
                window.addEventListener('load', function() {
                    setTimeout(() => {
                        const rect = projectsSection.getBoundingClientRect();
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        const sectionTop = rect.top + scrollTop;
                        const absoluteScroll = sectionTop + parseFloat(savedScrollPosition);
                        
                        window.scrollTo({
                            top: absoluteScroll,
                            behavior: 'smooth'
                        });

                        // Nettoyer le localStorage après utilisation
                        localStorage.removeItem('projectsScrollPosition');
                    }, 100);
                });
            }
        });

        // Code existant pour le message temporaire
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                setTimeout(function() {
                    const alert = new bootstrap.Alert(alertMessage);
                    alert.close();
            }, 3000);
            }
    </script>

    <!-- JavaScript pour le filtrage des investissements -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const investissementRows = document.querySelectorAll('.investissement-row');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    // Mettre à jour l'état actif des boutons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filtrer les lignes
                    investissementRows.forEach(row => {
                        if (filter === 'all' || row.getAttribute('data-statut') === filter) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>

    <!-- JavaScript pour la gestion des statuts -->
    <script>
    function updateStatut(idInvest, nouveauStatut) {
        console.log('Sending request with:', { idInvest, nouveauStatut });
        
        if (!confirm(
            'Confirmez-vous ' +
            (nouveauStatut === 'Accepté' ? 'l\'acceptation' : 'le refus') +
            ' ?'
        )) return;

            fetch('update_statut.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                'Accept': 'application/json'
                },
                body: JSON.stringify({
                id_investissement: parseInt(idInvest),
                nouveau_statut: nouveauStatut
            })
        })
        .then(async response => {
            const text = await response.text();
            console.log('Raw response:', text);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            }
            
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                throw new Error('Invalid JSON response from server');
            }
        })
        .then(data => {
            console.log('Parsed response:', data);
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Erreur côté serveur');
            }
        })
        .catch(err => {
            console.error('Error details:', err);
            alert('Erreur: ' + err.message);
        });
    }
    </script>

    <!-- JavaScript pour la gestion des revenus -->
    <script>
    function saisirRevenu(id) {
        const montant = prompt("Veuillez saisir le montant du revenu :");
        if (montant !== null) {
            updateRevenu(id, montant);
        }
    }

    function modifierRevenu(id, montantActuel) {
        const montant = prompt("Veuillez saisir le nouveau montant du revenu :", montantActuel);
        if (montant !== null) {
            updateRevenu(id, montant);
        }
    }

    function updateRevenu(id, montant) {
        fetch('update_revenu.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id,
                montant: montant
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                alert(data.message || 'Erreur lors de la mise à jour du revenu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            alert('Erreur lors de la mise à jour du revenu');
        });
    }
    </script>

    <!-- Ajouter Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Ajouter Chart.js Zoom -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>

    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            overflow: auto;
        }
        .chart-wrapper {
            min-width: 800px;
            min-height: 400px;
        }
    </style>

    <!-- Script pour initialiser les graphiques -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données des gains pour les graphiques (par projet)
        const gainsDataProjet = <?php
            $js_gains = [];
            foreach ($gains_par_projet as $id_projet => $projet) {
                // Ordonner les revenus par date et transformer en tableau indexé
                $dates = array_keys($projet['revenus']);
                sort($dates);
                $labels = [];
                $gainsMensuels = [];
                $gainsCumules = [];
                foreach ($dates as $date) {
                    $labels[] = $date;
                    $gainsMensuels[] = $projet['revenus'][$date]['gain_mensuel'];
                    $gainsCumules[] = $projet['revenus'][$date]['gains_cumules'];
                }
                $js_gains[] = [
                    'id_projet' => $id_projet,
                    'labels' => $labels,
                    'gainsMensuels' => $gainsMensuels,
                    'gainsCumules' => $gainsCumules,
                ];
            }
            echo json_encode($js_gains);
        ?>;

        // Créer un graphique pour chaque projet
        gainsDataProjet.forEach(proj => {
            if (!proj.labels.length) return;
            const ctx = document.getElementById(`gainChartProjet${proj.id_projet}`).getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: proj.labels.map(date => {
                        const d = new Date(date);
                        return d.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                    }),
                    datasets: [
                        {
                            label: 'Gains mensuels',
                            data: proj.gainsMensuels,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Gains cumulés',
                            data: proj.gainsCumules,
                            borderColor: 'rgb(153, 102, 255)',
                            tension: 0.1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Gains mensuels (DT)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Gains cumulés (DT)'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        zoom: {
                            pan: {
                                enabled: true,
                                mode: 'xy'
                            },
                            zoom: {
                                wheel: {
                                    enabled: true
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'xy'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'TND' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            labels: {
                                generateLabels: function(chart) {
                                    const datasets = chart.data.datasets;
                                    return datasets.map(function(dataset, i) {
                                        return {
                                            text: dataset.label + ' (' + new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'TND' }).format(dataset.data.reduce((a, b) => a + b, 0)) + ')',
                                            fillStyle: dataset.borderColor,
                                            hidden: chart.getDatasetMeta(i).hidden,
                                            lineCap: dataset.borderCapStyle,
                                            lineDash: dataset.borderDash,
                                            lineDashOffset: dataset.borderDashOffset,
                                            lineJoin: dataset.borderJoinStyle,
                                            lineWidth: dataset.borderWidth,
                                            strokeStyle: dataset.borderColor,
                                            pointStyle: dataset.pointStyle,
                                            rotation: 0
                                        };
                                    });
                                }
                            }
                        }
                    }
                }
            });
            window[`chart_projet_${proj.id_projet}`] = chart;
        });

        // Gestionnaire d'événements pour les boutons de réinitialisation
        document.querySelectorAll('.reset-zoom').forEach(button => {
            button.addEventListener('click', function() {
                const chartId = this.getAttribute('data-chart-id');
                const chart = window[`chart_projet_${chartId.replace('gainChartProjet', '')}`];
                if (chart) {
                    chart.resetZoom();
                    chart.options.scales.y.min = undefined;
                    chart.options.scales.y.max = undefined;
                    chart.options.scales.y1.min = undefined;
                    chart.options.scales.y1.max = undefined;
                    chart.update();
                }
            });
        });
    });
    </script>

    <!-- Ajouter le script JS pour gérer l'ajout aux favoris -->
    <script>
    function addToFavoris(idProjet) {
        fetch('add_favori.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_projet: idProjet })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de l\'ajout aux favoris');
            }
        })
        .catch(() => alert('Erreur lors de l\'ajout aux favoris'));
    }

    function removeFavoriWithFade(idProjet) {
        fetch('remove_favori.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_projet: idProjet })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = document.querySelector(`#favori-card-${idProjet}`);
                if (card) {
                    card.classList.add('removing');
                    setTimeout(() => card.remove(), 320);
                }
                // Mettre à jour l'icône étoile du projet dans la liste principale
                const starBtn = document.querySelector(`button[data-favori-id='${idProjet}']`);
                if (starBtn) {
                    starBtn.title = 'Ajouter aux favoris';
                    starBtn.onclick = function() { addToFavoris(idProjet); };
                    const icon = starBtn.querySelector('i');
                    if (icon) {
                        icon.className = 'far fa-star';
                        icon.classList.remove('text-warning');
                    }
                }
            } else {
                alert(data.message || "Erreur lors de la suppression du favori");
            }
        })
        .catch(() => alert("Erreur lors de la suppression du favori"));
    }
    </script>

    <!-- Script de tri des projets par reste à collecter -->
    <script>
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
