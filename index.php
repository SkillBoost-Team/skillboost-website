<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['role']) && !empty($_SESSION['role'])) {
    // Rediriger en fonction du rôle
    if ($_SESSION['role'] === 'admin') {
        header("Location: Views/back/admin-dashboard.php");
        exit();
    } else {
        header("Location: Views/front/user-dashboard.php");
        exit();
    }
}
?>
<script src="login.js"></script>
