<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Ajouter Formation</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">Jhon Doe</h6>
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="index.html" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Tableau de bord</a>
                    <a href="../../controller/afficher formation.php" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Formations</a>
                    <a href="ajouter_formation.php" class="nav-item nav-link active"><i class="fa fa-plus-circle me-2"></i>Ajouter Formation</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">John Doe</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">Mon Profil</a>
                            <a href="#" class="dropdown-item">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Form Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-light rounded h-100 p-4">
                            <h4 class="mb-4">Ajouter une Nouvelle Formation</h4>
                            
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                                    <?= $_SESSION['message'] ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                            <?php endif; ?>
                            
                            <!-- Update the form fields to use session data if available -->
                            <form action="../../controller/ajouter formation.php" method="POST">
                                <!-- Titre Field -->
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="titre" name="titre" placeholder="Titre de la formation" required>
                                    <label for="titre">Titre de la formation</label>
                                </div>
                                
                                <!-- Description Field -->
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" placeholder="Description" id="description" name="description" style="height: 150px;" required></textarea>
                                    <label for="description">Description</label>
                                </div>
                                
                                <div class="row mb-3">
                                    <!-- Durée Field -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="duree" name="duree" placeholder="Durée (heures)" min="1" required>
                                            <label for="duree">Durée (heures)</label>
                                        </div>
                                    </div>
                                    
                                    <!-- Niveau Field -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="niveau" name="niveau" required>
                                                <option value="" selected disabled>Sélectionner un niveau</option>
                                                <option value="Débutant">Débutant</option>
                                                <option value="Intermédiaire">Intermédiaire</option>
                                                <option value="Avancé">Avancé</option>
                                            </select>
                                            <label for="niveau">Niveau</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Certificat Field -->
                                <div class="mb-3">
                                    <label class="form-label">Certificat disponible</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="certificat" id="certificatOui" value="Oui" checked>
                                        <label class="form-check-label" for="certificatOui">Oui</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="certificat" id="certificatNon" value="Non">
                                        <label class="form-check-label" for="certificatNon">Non</label>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary">Ajouter la Formation</button>
                                
                                <!-- Cancel Button -->
                                <a href="../../controller/ajouter formation.php" class="btn btn-outline-secondary ms-2">Annuler</a>
                            </form>


                            <?php
                            // Clear form data after displaying
                            if (isset($_SESSION['form_data'])) {
                                unset($_SESSION['form_data']);
                            }
                            ?>
                                                        
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form End -->

            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 text-center">
                            &copy; <a href="#">Votre Plateforme de Formation</a>, Tous droits réservés.
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>