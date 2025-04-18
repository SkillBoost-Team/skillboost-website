<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>

<?php
// Include the model
require_once(__DIR__.'/../../model/FormationModel.php');
$formationModel = new FormationModel();
// Fetch formations from the model
$formations = $formationModel->getFormations();
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($formations)) {
    die("Aucune donnée de formation à afficher");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
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
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>


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
                    <a href="table.html" class="nav-item nav-link active"><i class="fa fa-table me-2"></i>Tables</a>
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
            </nav>
            <!-- Navbar End -->
            
            <!-- Table Start -->
            <h2 style="text-align: center;">Formation Backoffice</h2>

            <input type="text" id="searchInput" oninput="filterTable()" placeholder="Rechercher par titre ou catégorie">

            <table id="formationTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre <span onclick="sortTable(1)">▲</span></th>
                        <th>Description</th>
                        <th>Date de Création</th>
                        <th>Durée</th>
                        <th>Niveau</th>
                        <th>Certificat</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formations as $formation): ?>
                    <tr>
                        <td><?= htmlspecialchars($formation['id']) ?></td>
                        <td><?= htmlspecialchars($formation['titre']) ?></td>
                        <td><?= htmlspecialchars($formation['description']) ?></td>
                        <td><?= htmlspecialchars($formation['date_creation']) ?></td>
                        <td><?= htmlspecialchars($formation['duree']) ?> heures</td>
                        <td><?= htmlspecialchars($formation['niveau']) ?></td>
                        <td><?= htmlspecialchars($formation['certificat']) ?></td>
                        <td>
                        <form action="../../controller/afficher formation.php" method="POST" >
                                <input type="hidden" name="id" value="<?php echo $formation['id']; ?>">
                                <button type="submit" class="delete-btn">Supprimer</button>
                            </form>
                            <a href="../../controller/modifier formation.php?id=<?= $formation['id'] ?>" class="btn btn-warning">Modifier</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Content End -->
    </div>

    <!-- Include the html2pdf library -->
    <script src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
</body>

</html>