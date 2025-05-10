<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0">
    <a href="/" class="navbar-brand p-0">
        <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>SkillBoost</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0">
            <a href="/" class="nav-item nav-link">
                <i class="fas fa-home"></i> <?= $t['home'] ?? 'Accueil' ?>
            </a>
            <a href="/login" class="nav-item nav-link">
                <i class="fas fa-sign-in-alt"></i> <?= $t['login'] ?? 'Connexion' ?>
            </a>
            <a href="/projects" class="nav-item nav-link">
                <i class="fas fa-project-diagram"></i> <?= $t['projects'] ?? 'Projets' ?>
            </a>
            <a href="/trainings" class="nav-item nav-link">
                <i class="fas fa-graduation-cap"></i> <?= $t['trainings'] ?? 'Formations' ?>
            </a>
            <a href="/events" class="nav-item nav-link">
                <i class="fas fa-calendar-alt"></i> <?= $t['events'] ?? 'Événements' ?>
            </a>
            <a href="/investments" class="nav-item nav-link">
                <i class="fas fa-chart-line"></i> <?= $t['investments'] ?? 'Investissements' ?>
            </a>
            <a href="/complaints" class="nav-item nav-link active">
                <i class="fas fa-exclamation-circle"></i> <?= $t['complaints'] ?? 'Réclamations' ?>
            </a>
        </div>
    </div>
</nav>