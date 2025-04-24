<?php
// Active les erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclusion BDD
require __DIR__ . '/../../config/database.php';

// Test connexion
try {
    $pdo = Database::getInstance();
    echo "<p>Connexion DB réussie!</p>"; // Test
} catch (PDOException $e) {
    die("ERREUR DB: " . $e->getMessage());
}

// Test session
session_start();
$_SESSION['test'] = 'Works!';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
</head>
<body>
    <h1>Test Réclamation</h1>
    <p>Session: <?= $_SESSION['test'] ?? 'No session' ?></p>
</body>
</html>