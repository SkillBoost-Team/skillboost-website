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
    
    <!-- CSS personnalisé -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

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
    </style>
</head>
<body>
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

    
    <!-- Scripts JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    
      
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
        
</body>
</html>
