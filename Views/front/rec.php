<?php
// ==============================================
// PARTIE PHP - TRAITEMENT DU FORMULAIRE
// ==============================================

$message = '';
$alert_class = '';

// Connexion à la base de données
try {
    $db = new PDO('mysql:host=localhost;dbname=skillboost', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $data = [
        'full_name' => htmlspecialchars(trim($_POST['full_name'])),
        'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
        'subject' => htmlspecialchars(trim($_POST['subject'])),
        'type' => htmlspecialchars($_POST['type']),
        'priority' => htmlspecialchars($_POST['priority']),
        'description' => htmlspecialchars(trim($_POST['description'])),
        'status' => 'Nouveau',
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Validation supplémentaire côté serveur
    $errors = [];
    if (empty($data['full_name'])) $errors[] = "Le nom est requis";
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (strlen($data['description']) < 20) $errors[] = "La description doit faire au moins 20 caractères";

    // Si pas d'erreurs, insertion en base
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO reclamations 
                                 (full_name, email, subject, type, priority, description, status, created_at) 
                                 VALUES (:full_name, :email, :subject, :type, :priority, :description, :status, :created_at)");
            
            if ($stmt->execute($data)) {
                $message = "Votre réclamation a été envoyée avec succès!";
                $alert_class = "success";
                
                // Réinitialisation des champs après succès
                $_POST = [];
            }
        } catch(PDOException $e) {
            $message = "Erreur technique: " . $e->getMessage();
            $alert_class = "danger";
        }
    } else {
        $message = implode("<br>", $errors);
        $alert_class = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Réclamations - SkillBoost</title>
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
        .reclamation-form {
            background: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .reclamation-form:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .form-control, .form-select {
            height: 50px;
            border-radius: 5px;
            border: 1px solid #e1e1e1;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #061429;
            box-shadow: 0 0 0 0.25rem rgba(6, 20, 41, 0.25);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .btn-primary {
            background-color: #061429;
            border-color: #061429;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #0a1f3d;
            transform: translateY(-2px);
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .is-valid {
            border-color: #28a745 !important;
        }
        
        .contact-image {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .contact-image:hover {
            transform: scale(1.02);
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
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>Bloc E, Esprit, Cite La Gazelle</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+216 90 044 054</small>
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

    <!-- Navbar Start -->
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
                    <a href="index.php" class="nav-item nav-link">Accueil</a>
                    <a href="login.php" class="nav-item nav-link">Connexion</a>
                    <a href="#" class="nav-item nav-link">Projets</a>
                    <a href="Formations.php" class="nav-item nav-link">Formations</a>
                    <a href="evenements.php" class="nav-item nav-link">Événements</a>
                    <a href="gestionInvestissement.php" class="nav-item nav-link">Investissements</a>
                    <a href="reclamations.php" class="nav-item nav-link active">Réclamations</a>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Déposer une Réclamation</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="index.php">Accueil</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Réclamations</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Formulaire de Réclamation Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-7 wow fadeInUp" data-wow-delay="0.1s">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $alert_class ?> alert-dismissible fade show mb-4">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="reclamation-form">
                        <h2 class="mb-4">Remplissez le formulaire</h2>
                        <p class="mb-5">Nous traiterons votre demande dans les plus brefs délais</p>
                        
                        <form id="reclamationForm" method="POST" novalidate>
                            <!-- Nom Complet -->
                            <div class="mb-4">
                                <label for="full_name" class="form-label">Nom Complet *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                                <div class="error-message" id="full_name_error"></div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                <div class="error-message" id="email_error"></div>
                            </div>

                            <!-- Sujet -->
                            <div class="mb-4">
                                <label for="subject" class="form-label">Sujet *</label>
                                <input type="text" class="form-control" id="subject" name="subject"
                                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                                <div class="error-message" id="subject_error"></div>
                            </div>

                            <!-- Type et Priorité en ligne -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Type *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="" disabled selected>Choisissez un type</option>
                                        <option value="Technique" <?= ($_POST['type'] ?? '') === 'Technique' ? 'selected' : '' ?>>Technique</option>
                                        <option value="Service" <?= ($_POST['type'] ?? '') === 'Service' ? 'selected' : '' ?>>Service</option>
                                        <option value="Facturation" <?= ($_POST['type'] ?? '') === 'Facturation' ? 'selected' : '' ?>>Facturation</option>
                                        <option value="Autre" <?= ($_POST['type'] ?? '') === 'Autre' ? 'selected' : '' ?>>Autre</option>
                                    </select>
                                    <div class="error-message" id="type_error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="priority" class="form-label">Priorité *</label>
                                    <select class="form-select" id="priority" name="priority" required>
                                        <option value="" disabled selected>Choisissez une priorité</option>
                                        <option value="Haute" <?= ($_POST['priority'] ?? '') === 'Haute' ? 'selected' : '' ?>>Haute</option>
                                        <option value="Moyenne" <?= ($_POST['priority'] ?? '') === 'Moyenne' ? 'selected' : '' ?>>Moyenne</option>
                                        <option value="Basse" <?= ($_POST['priority'] ?? '') === 'Basse' ? 'selected' : '' ?>>Basse</option>
                                    </select>
                                    <div class="error-message" id="priority_error"></div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                <div class="error-message" id="description_error"></div>
                                <small class="text-muted">Minimum 20 caractères</small>
                            </div>

                            <!-- Bouton Soumettre -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary py-3 px-5">
                                    <i class="fas fa-paper-plane me-2"></i> Envoyer la Réclamation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

               
            </div>
        </div>
    </div>
    <!-- Formulaire de Réclamation End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">SkillBoost</h4>
                    <p>Plateforme complète pour l'entrepreneuriat et l'investissement.</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Liens rapides</h4>
                    <a class="btn btn-link" href="index.php">Accueil</a>
                    <a class="btn btn-link" href="Formations.php">Formations</a>
                    <a class="btn btn-link" href="evenements.php">Événements</a>
                    <a class="btn btn-link" href="reclamations.php">Réclamations</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p><i class="fa fa-map-marker-alt me-3"></i>Bloc E, Esprit, Cite La Gazelle</p>
                    <p><i class="fa fa-phone-alt me-3"></i>+216 90 044 054</p>
                    <p><i class="fa fa-envelope me-3"></i>SkillBoost@gmail.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Abonnez-vous à notre newsletter pour les dernières actualités.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Votre email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">S'inscrire</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">SkillBoost</a>, Tous droits réservés.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="#">Accueil</a>
                            <a href="#">Cookies</a>
                            <a href="#">Aide</a>
                            <a href="#">FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- Script de Validation -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reclamationForm');
        
        if (!form) return;

        // Validation en temps réel
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function() {
                validateField(this);
            });
            
            field.addEventListener('blur', function() {
                validateField(this);
            });
        });

        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const fieldsToValidate = [
                'full_name', 'email', 'subject', 
                'type', 'priority', 'description'
            ];
            
            fieldsToValidate.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            if (isValid) {
                // Animation avant soumission
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Envoi en cours...';
                submitBtn.disabled = true;
                
                // Soumission après un léger délai pour l'animation
                setTimeout(() => {
                    form.submit();
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Formulaire incomplet',
                    text: 'Veuillez corriger les erreurs indiquées',
                    confirmButtonColor: '#061429'
                });
                
                // Scroll vers le premier champ invalide
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });

        function validateField(field) {
            const errorElement = document.getElementById(`${field.id}_error`);
            
            // Réinitialisation
            field.classList.remove('is-invalid', 'is-valid');
            if (errorElement) errorElement.textContent = '';

            let isValid = true;
            let errorMessage = '';

            if (field.required && !field.value.trim()) {
                isValid = false;
                errorMessage = 'Ce champ est obligatoire';
            } 
            else if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                isValid = false;
                errorMessage = 'Veuillez entrer un email valide';
            } 
            else if (field.id === 'description' && field.value.trim().length < 20) {
                isValid = false;
                errorMessage = 'La description doit contenir au moins 20 caractères';
            } 
            else if ((field.id === 'type' || field.id === 'priority') && field.value === '') {
                isValid = false;
                errorMessage = 'Veuillez faire une sélection';
            }

            if (!isValid) {
                field.classList.add('is-invalid');
                if (errorElement) errorElement.textContent = errorMessage;
            } else {
                field.classList.add('is-valid');
            }

            return isValid;
        }
    });
    </script>
</body>
</html>