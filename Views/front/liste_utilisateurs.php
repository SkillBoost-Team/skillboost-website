<?php
session_start();
include '../front/config.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Traitement de la suppression
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    
    // Vérifier si l'utilisateur à supprimer est l'admin
    $stmt = $conn->prepare("SELECT role FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user['role'] !== 'admin') {
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: liste_utilisateurs.php");
        exit();
    } else {
        echo "<script>alert('❌ Impossible de supprimer le compte administrateur');</script>";
    }
}

// Récupérer la liste des utilisateurs
$stmt = $conn->prepare("SELECT id, nom, email, role FROM utilisateurs ORDER BY role DESC, nom ASC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .btn-action {
            margin-right: 5px;
        }
        .admin-row {
            background-color: #f8f9fa;
        }
        .role-admin {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Gestion des Utilisateurs</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?php echo $row['role'] === 'admin' ? 'admin-row' : ''; ?>">
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['nom']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td class="<?php echo $row['role'] === 'admin' ? 'role-admin' : ''; ?>">
                                <?php echo htmlspecialchars($row['role']); ?>
                            </td>
                            <td>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="modifier_utilisateur.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm btn-action">Modifier</a>
                                    <?php if ($row['role'] !== 'admin'): ?>
                                        <a href="liste_utilisateurs.php?supprimer=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn btn-secondary">Retour</a>
    </div>
</body>
</html> 