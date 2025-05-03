<?php
// Connexion à la base de données
$host = 'localhost';
$db   = 'gestion_utilisateurs';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

if (isset($_GET['id'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=gestion_utilisateurs", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM admin WHERE id_admin = ?");
        $stmt->execute([$_GET['id']]);

        echo "Admin supprimé.";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
