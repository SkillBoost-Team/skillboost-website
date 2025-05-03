<?php
session_start();
include '../front/config.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: mot_de_passe_oublie.php");
    exit();
}

$message = "";

if (isset($_POST['reinitialiser'])) {
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirmer_mdp = $_POST['confirmer_mdp'];
    
    if ($nouveau_mdp !== $confirmer_mdp) {
        $message = "<div class='alert alert-danger text-center'>❌ Les mots de passe ne correspondent pas.</div>";
    } else {
        $mdp_hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?");
        $update->bind_param("ss", $mdp_hash, $_SESSION['reset_email']);
        $update->execute();
        
        session_unset();
        session_destroy();
        echo "<script>alert('✅ Mot de passe réinitialisé avec succès'); window.location='login.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau mot de passe | SkillBoost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            max-width: 480px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .logo {
            font-size: 26px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">🔐 SkillBoost</div>
    <h4 class="text-center mb-3">Réinitialisation du mot de passe</h4>
    <p class="text-muted text-center">Veuillez saisir votre nouveau mot de passe.</p>

    <?php if (!empty($message)) echo $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="nouveau_mdp" class="form-label">Nouveau mot de passe</label>
            <input type="password" name="nouveau_mdp" id="nouveau_mdp" class="form-control" required placeholder="Minimum 6 caractères">
        </div>
        <div class="mb-3">
            <label for="confirmer_mdp" class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="confirmer_mdp" id="confirmer_mdp" class="form-control" required>
        </div>
        <button type="submit" name="reinitialiser" class="btn btn-primary">
            Réinitialiser le mot de passe
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
