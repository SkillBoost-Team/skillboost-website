<?php
session_start();
include '../back/config.php';

// Vérifier si l'ID de l'utilisateur est passé en paramètre dans l'URL
if (!isset($_GET['id'])) {
    die("❌ Aucun ID utilisateur trouvé.");
}

$id = $_GET['id'];

// Récupérer les informations actuelles de l'utilisateur
$sql = "SELECT * FROM utilisateurs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Utilisateur non trouvé.");
}

$user = $result->fetch_assoc();

// Mettre à jour les informations de l'utilisateur si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Requête de mise à jour de l'utilisateur
    $update_sql = "UPDATE utilisateurs SET nom = ?, email = ?, role = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $nom, $email, $role, $id);
    if ($update_stmt->execute()) {
        echo "<p>✅ Utilisateur mis à jour avec succès.</p>";
    } else {
        echo "<p>❌ Échec de la mise à jour de l'utilisateur.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier les informations de l'utilisateur</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                    <option value="utilisateur" <?= $user['role'] === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
