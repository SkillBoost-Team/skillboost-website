<?php
session_start();
include '../back/config.php';

// Vérifier si l'ID de l'utilisateur est passé en paramètre dans l'URL
if (!isset($_GET['id'])) {
    die("❌ Aucun ID utilisateur trouvé.");
}

$id = $_GET['id'];

// Requête pour supprimer l'utilisateur de la base de données
$sql = "DELETE FROM utilisateurs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<p>✅ Utilisateur supprimé avec succès.</p>";
} else {
    echo "<p>❌ Échec de la suppression de l'utilisateur.</p>";
}

// Rediriger vers la liste des utilisateurs après la suppression
header("Location: liste_utilisateurs.php");
exit();
