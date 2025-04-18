<?php
session_start();
include '../front/config.php';

if (isset($_POST['connecter'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $conn->prepare("SELECT id, nom, mot_de_passe, role FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $nom, $hashed_password, $role);
        $stmt->fetch();

        if ($mot_de_passe === $hashed_password) {
            $_SESSION['id'] = $id;
            $_SESSION['nom'] = $nom;
            $_SESSION['role'] = $role;
            header("Location: index.html"); // 🔁 redirection vers la page initiale
            exit();
        } else {
            echo "<script>alert('❌ Mot de passe incorrect'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('❌ Email non trouvé'); window.history.back();</script>";
    }
}
?>

<!-- Formulaire HTML -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
    input[type="email"],
    input[type="password"] {
        border: 2px solid #007BFF;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 16px;
        transition: 0.3s;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
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

    form {
        max-width: 500px;
        margin: 0 auto;
        background: #f9f9f9;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
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
        <h2>Connexion</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="form-control" required>
            </div>
            <button type="submit" name="connecter" class="btn btn-primary">Connexion</button>
            <p class="mt-3">Pas encore inscrit ? <a href="inscription.php">Créer un compte</a></p>
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
    <script src="js/validation.js"></script>

</body>
</html>
