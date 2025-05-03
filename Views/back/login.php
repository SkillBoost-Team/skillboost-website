<?php
session_start();
include '../back/config.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier les identifiants dans la base de données
    $sql = "SELECT * FROM utilisateurs WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Définir les variables de session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Rediriger en fonction du rôle
        if ($user['role'] === 'admin') {
            header("Location: ../back/admin-dashboard.php");
        } else {
            header("Location: ../front/index.php");
        }
        exit();
    } else {
        // Identifiants incorrects
        header("Location: ../front/index.php?error=1");
        exit();
    }
}
?> 