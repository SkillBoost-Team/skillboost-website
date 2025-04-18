<?php
include '../front/config.php'; // ajuste le chemin selon ton projet

// Si le formulaire est soumis
if (isset($_POST['inscrire'])) {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role'];

    // Vérifier si l'email existe déjà
    $check_sql = "SELECT id FROM utilisateurs WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert(' Cet email est deja utilise '); window.history.back();</script>";
        exit();
    }

    // Ajouter l'utilisateur
    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nom, $email, $mot_de_passe, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Compte cree avec succes !'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert(' Erreur lors de l\'inscription'); window.history.back();</script>";
    }
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
<style>
    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
        border: 2px solid #007BFF;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 16px;
        transition: 0.3s;
        width: 100%;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    select:focus {
        outline: none;
        border-color: #0056b3;
        box-shadow: 0 0 6px rgba(0, 123, 255, 0.5);
    }

    .card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    button.btn-primary {
        background-color: #007BFF;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    button.btn-primary:hover {
        background-color: #0056b3;
    }
</style>

    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="css/bootstrap.min.css" rel="stylesheet"> <!-- si tu as bootstrap -->
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
        <h2>Créer un compte</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="nom">Nom</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="form-control" required>
            </div>
            <div class="mb-3">
            <label for="role">Je suis :</label>
<select name="role" class="form-select" required>
    <option value="utilisateur">Utilisateur</option>
    <option value="investisseur">Investisseur</option>
    <option value="partenaire">Partenaire</option>
</select>

            </div>
            <button type="submit" name="inscrire" class="btn btn-primary">S'inscrire</button>
            <p class="mt-3">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
        </form>
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
    <script src="js/validation_insc.js"></script>
     
</body>
</html>