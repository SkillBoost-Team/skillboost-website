<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: mot_de_passe_oublie.php");
    exit();
}

if (isset($_POST['verifier_code'])) {
    $code_saisi = $_POST['code'];
    
    if ($code_saisi == $_SESSION['code_verification']) {
        header("Location: reinitialiser_mdp.php");
        exit();
    } else {
        $erreur = "Code incorrect. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification du code | SkillBoost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            max-width: 450px;
            margin: auto;
            margin-top: 80px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 12px;
            background-color: white;
        }
        .logo {
            font-size: 26px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">📩 SkillBoost</div>
    <h4 class="text-center mb-3">Vérification du code</h4>
    <p class="text-muted text-center">Veuillez saisir le code que vous avez reçu par email.</p>

    <?php if (isset($erreur)) : ?>
        <div class="alert alert-danger text-center"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="code" class="form-label">Code de vérification</label>
            <input type="text" name="code" id="code" class="form-control text-center" required placeholder="Ex: 123456" maxlength="6">
        </div>
        <button type="submit" name="verifier_code" class="btn btn-primary">
            Vérifier le code
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
