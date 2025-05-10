<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>SkillBoost Website</title>
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
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Bloc E, Esprit, Cite La Gazelle</small>
                    <small class="me-3 text-light"><i class="fab fa-whatsapp me-2"></i><a href="https://wa.me/21690044054" class="text-light" target="_blank" style="text-decoration:none;">+216 90 044 054</a></small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>SkillBoost@gmail.com</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->
    
    <!-- Navbar & Carousel Start -->
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
            <a href="index.php" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>SkillBoost</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link active">Accueil</a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="logout.php" class="nav-item nav-link">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-item nav-link">Connexion</a>
                    <?php endif; ?>
                    <a href="#" class="nav-item nav-link">Projets</a>
                    <a href="Formations.php" class="nav-item nav-link">Formations</a>
                    <a href="evenements.php" class="nav-item nav-link">Événements</a>
                    <a href="gestionInvestissement.php" class="nav-item nav-link">Investissements</a>
                    <a href="reclamation.php" class="nav-item nav-link">Réclamations</a>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar & Carousel End -->

    <!-- Carousel Start -->
    <div class="container-fluid p-0">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-inner">
                <div class="container-fluid position-relative" style="z-index: 1;">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-8 text-center">
                            <h1 class="display-3 text-white animated slideInDown mb-4">SkillBoost</h1>
                            <p class="fs-5 fw-medium text-white animated slideInDown mb-4 pb-2">SkillBoost est une plateforme de formation en ligne qui propose des cours de qualité pour tous les niveaux de compétences.</p>
                            <a href="Formations.php" class="btn btn-primary py-3 px-5 animated slideInDown">Formations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- About Start -->
    <div class="container-fluid overflow-hidden py-5 px-lg-0">
        <div class="container px-lg-0">
            <div class="row g-0 mx-lg-0">
                <div class="col-lg-6 ps-lg-0" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100" src="img/about.jpg" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="h-100 d-flex flex-column justify-content-center p-5">
                        <h2 class="display-5 mb-4">SkillBoost</h2>
                        <p class="mb-4 pb-2">SkillBoost est une plateforme de formation en ligne qui propose des cours de qualité pour tous les niveaux de compétences.</p>
                        <a href="Formations.php" class="btn btn-primary py-3 px-5">Formations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Services Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <h1 class="display-5 mb-4">Nos Services</h1>
            </div>
            <div class="row g-5">
                <div class="col-md-6 col-lg-3">
                    <div class="service-item bg-light d-flex flex-column justify-content-center text-center h-100">
                        <div class="service-icon mb-4">
                            <i class="fa fa-3x fa-user-tie text-primary"></i>
                        </div>
                        <h4 class="mb-3">Formation</h4>
                        <p class="mb-4">SkillBoost propose des cours de qualité pour tous les niveaux de compétences.</p>
                        <a class="btn btn-primary" href="Formations.php">En savoir plus</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-item bg-light d-flex flex-column justify-content-center text-center h-100">
                        <div class="service-icon mb-4">
                            <i class="fa fa-3x fa-project-diagram text-primary"></i>
                        </div>
                        <h4 class="mb-3">Projet</h4>
                        <p class="mb-4">SkillBoost propose des projets pour tous les niveaux de compétences.</p>
                        <a class="btn btn-primary" href="#">En savoir plus</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-item bg-light d-flex flex-column justify-content-center text-center h-100">
                        <div class="service-icon mb-4">
                            <i class="fa fa-3x fa-money-bill text-primary"></i>
                        </div>
                        <h4 class="mb-3">Investissement</h4>
                        <p class="mb-4">SkillBoost propose des investissements pour tous les niveaux de compétences.</p>
                        <a class="btn btn-primary" href="gestionInvestissement.php">En savoir plus</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-item bg-light d-flex flex-column justify-content-center text-center h-100">
                        <div class="service-icon mb-4">
                            <i class="fa fa-3x fa-headset text-primary"></i>
                        </div>
                        <h4 class="mb-3">Support</h4>
                        <p class="mb-4">SkillBoost propose un support pour tous les niveaux de compétences.</p>
                        <a class="btn btn-primary" href="#">En savoir plus</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Services End -->

    <!-- Features Start -->
    <div class="container-fluid overflow-hidden py-5 px-lg-0">
        <div class="container px-lg-0">
            <div class="row g-0 mx-lg-0">
                <div class="col-lg-6">
                    <div class="h-100 d-flex flex-column justify-content-center p-5">
                        <h2 class="display-5 mb-4">Nos Caractéristiques</h2>
                        <p class="mb-4 pb-2">SkillBoost est une plateforme de formation en ligne qui propose des cours de qualité pour tous les niveaux de compétences.</p>
                        <a href="Formations.php" class="btn btn-primary py-3 px-5">Formations</a>
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-0" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100" src="img/feature.jpg" style="object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->

    <!-- Testimonial Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 600px;">
                <h1 class="display-5 mb-4">Témoignages</h1>
            </div>
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="d-flex">
                        <img class="flex-shrink-0 img-fluid rounded-circle" src="img/testimonial-1.jpg" style="width: 80px; height: 80px;">
                        <div class="ps-3">
                            <h5 class="mb-1">John Doe</h5>
                            <small>Professeur</small>
                        </div>
                    </div>
                    <div class="h5 mt-3 mb-3">SkillBoost est une plateforme de formation en ligne qui propose des cours de qualité pour tous les niveaux de compétences.</div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex">
                        <img class="flex-shrink-0 img-fluid rounded-circle" src="img/testimonial-2.jpg" style="width: 80px; height: 80px;">
                        <div class="ps-3">
                            <h5 class="mb-1">Jane Doe</h5>
                            <small>Étudiant</small>
                        </div>
                    </div>
                    <div class="h5 mt-3 mb-3">SkillBoost est une plateforme de formation en ligne qui propose des cours de qualité pour tous les niveaux de compétences.</div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex">
                        <img class="flex-shrink-0 img-fluid rounded-circle" src="img/testimonial-3.jpg" style="width: 80px; height: 80px;">
                        <div class="ps-3">
                            <h5 class="mb-1">John Smith</h5>
                            <small>Étudiant</small>
                        </div>
                    </div>
                    <div class="h5 mt-3 mb-3">SkillBoost est une plateforme de formation en ligne qui propose des cours de qualité pour tous les niveaux de compétences.</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer mt-5 py-5">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-3">
                    <h5 class="text-light mb-4">Nos Services</h5>
                    <a class="btn btn-link text-light" href="#">Formation</a>
                    <a class="btn btn-link text-light" href="#">Projet</a>
                    <a class="btn btn-link text-light" href="#">Investissement</a>
                    <a class="btn btn-link text-light" href="#">Support</a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h5 class="text-light mb-4">Nos Services</h5>
                    <a class="btn btn-link text-light" href="#">Formation</a>
                    <a class="btn btn-link text-light" href="#">Projet</a>
                    <a class="btn btn-link text-light" href="#">Investissement</a>
                    <a class="btn btn-link text-light" href="#">Support</a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h5 class="text-light mb-4">Nos Services</h5>
                    <a class="btn btn-link text-light" href="#">Formation</a>
                    <a class="btn btn-link text-light" href="#">Projet</a>
                    <a class="btn btn-link text-light" href="#">Investissement</a>
                    <a class="btn btn-link text-light" href="#">Support</a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <h5 class="text-light mb-4">Contact</h5>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Bloc E, Esprit, Cite La Gazelle</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+216 90 044 054</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>SkillBoost@gmail.com</p>
                    <div class="d-flex pt-2">
 