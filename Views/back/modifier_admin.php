<?php
// Connexion à la base de données
$host = 'localhost';
$db   = 'gestion_utilisateurs';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$pdo = new PDO("mysql:host=localhost;dbname=gestion_utilisateurs", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id'])) {
    $id_admin = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE id_admin = ?");
    $stmt->execute([$id_admin]);
    $admin = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $privileges = $_POST['privileges'];
    $stmt = $pdo->prepare("UPDATE admin SET privileges = ? WHERE id_admin = ?");
    $stmt->execute([$privileges, $_POST['id_admin']]);
    echo "<div class='message success'>Admin modifié avec succès.</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            padding: 40px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            width: 400px;
            margin-top: 20px;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        form select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .message.success {
            background-color: #2ecc71;
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            width: fit-content;
        }
    </style>
</head>
<body>

<?php if (isset($admin)): ?>
    <h2>Modifier les privilèges de l’administrateur</h2>
    <form method="post">
        <input type="hidden" name="id_admin" value="<?= $admin['id_admin'] ?>">
        
        <label for="privileges">Privilèges :</label>
        <select name="privileges" required>
            <option value="lecture" <?= $admin['privileges'] == 'lecture' ? 'selected' : '' ?>>Lecture</option>
            <option value="modification" <?= $admin['privileges'] == 'modification' ? 'selected' : '' ?>>Modification</option>
            <option value="admin" <?= $admin['privileges'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
        </select>

        <button class="btn" type="submit">Modifier</button>
    </form>
<?php endif; ?>

</body>
</html>
