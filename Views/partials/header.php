<!DOCTYPE html>
<html lang="<?= $this->currentLang ?>" dir="<?= $this->currentLang === 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SkillBoost' ?></title>
    
    <!-- Favicon -->
    <link href="/assets/img/favicon.ico" rel="icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS Personnalisé -->
    <link href="/assets/css/style.css" rel="stylesheet">
    
    <?php if ($this->currentLang === 'ar'): ?>
    <link href="/assets/css/rtl.css" rel="stylesheet">
    <?php endif; ?>
</head>
<body>
    <!-- Barre supérieure -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light">
                        <i class="fa fa-map-marker-alt me-2"></i>Bloc E, Esprit, Cite La Gazelle
                    </small>
                    <small class="me-3 text-light">
                        <i class="fab fa-whatsapp me-2"></i>
                        <a href="https://wa.me/21690044054" class="text-light" target="_blank">+216 90 044 054</a>
                    </small>
                    <small class="text-light">
                        <i class="fa fa-envelope-open me-2"></i>SkillBoost@gmail.com
                    </small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle me-2" href="#">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a class="btn btn-sm btn-outline-light btn-sm-square rounded-circle" href="#">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>