<?php
session_start();
include '../back/config.php';

if (!$conn) {
    die("❌ Connexion échouée.");
}

// Requête
$sql = "SELECT id, nom, email, role FROM utilisateurs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    <!-- Lien vers le CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS personnalisé -->
    <style>
        /* Styles personnalisés */
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
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
        
        /* Styles existants */
        .container {
            max-width: 1000px;
        }

        h2 {
            font-size: 2rem;
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            background-color: #ffffff;
            border-radius: 8px;
        }

        .table th, .table td {
            text-align: center;
            padding: 15px;
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .alert {
            margin-top: 20px;
            padding: 15px;
            font-size: 1rem;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .footer {
            margin-top: 50px;
            padding: 10px 0;
            text-align: center;
            background-color: #343a40;
            color: white;
        }

        .btn-modifier {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-supprimer {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-modifier:hover, .btn-supprimer:hover {
            opacity: 0.8;
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
    <!-- Sidebar Start -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="img/admin-avatar.jpg" alt="Admin Photo">
            <h4>Sghier youssef</h4>
            <p>Administrateur</p>
        </div>
        <div class="sidebar-menu">
            <a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="admin-users.html" class="active"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="admin-projects.html"><i class="fas fa-project-diagram"></i> Projets</a>
            <a href="admin-formations.html"><i class="fas fa-graduation-cap"></i> Formations</a>
            <a href="admin-events.html"><i class="fas fa-calendar-alt"></i> Événements</a>
            <a href="admin-investments.html"><i class="fas fa-chart-line"></i> Investissements</a>
            <a href="admin-reclamations.php"><i class="fas fa-exclamation-circle"></i> Réclamations</a>
            <a href="admin-settings.html"><i class="fas fa-cog"></i> Paramètres</a>
            <a href="logout.html"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>
    </div>
    <!-- Sidebar End -->
    
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
                    <!-- Social media links (optional) -->
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->
        <div class="container mt-5">
            <h2>📋 Liste des utilisateurs inscrits</h2>

            <!-- Affichage du message d'erreur si la requête échoue -->
            <?php if ($result->num_rows == 0): ?>
                <div class="alert">Aucun utilisateur trouvé.</div>
            <?php endif; ?>

            <!-- Tableau des utilisateurs -->
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th> <!-- Nouvelle colonne pour les actions -->
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nom']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= isset($row['role']) ? $row['role'] : 'Non défini' ?></td>
                                
                                <td>
                                    <!-- Bouton Modifier -->
                                    <a href="modifier_utilisateur.php?id=<?= $row['id'] ?>" class="btn btn-modifier">Modifier</a>
                                   
                                    <!-- Bouton Supprimer -->
                                    
                                        <a href="supprimer_utilisateur.php?id=<?= $row['id'] ?>" class="btn btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                    
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
    </div>
    
    <!-- Scripts JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>