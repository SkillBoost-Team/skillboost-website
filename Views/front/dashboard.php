<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$nom = $_SESSION['nom'];
$role = $_SESSION['role'];
?>

<script src="login.js"></script>
