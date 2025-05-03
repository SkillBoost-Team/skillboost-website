<?php
$host = 'localhost';
$db   = 'gestion_utilisateurs';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_utilisateur = $_POST['id_utilisateur'];
        $privileges = $_POST['privileges'];

        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id_utilisateur]);
        if ($stmt->rowCount() === 0) {
            throw new Exception("L'utilisateur avec l'ID $id_utilisateur n'existe pas.");
        }

        // Vérifier si l'utilisateur est déjà admin
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Cet utilisateur est déjà administrateur.");
        }

        // Insérer l'admin
        $stmt = $pdo->prepare("INSERT INTO admin (id_utilisateur, privileges) VALUES (?, ?)");
        $stmt->execute([$id_utilisateur, $privileges]);

        header("Location: liste_admins.php");
        exit;
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
