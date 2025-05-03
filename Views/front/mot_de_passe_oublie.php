<?php
session_start();
include '../front/config.php';

// PHPMailer manuel
require '../front/PHPMailer/src/Exception.php';
require '../front/PHPMailer/src/PHPMailer.php';
require '../front/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['envoyer_code'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $code = rand(100000, 999999);
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $update = $conn->prepare("UPDATE utilisateurs SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sghaieryoussef7@gmail.com';
            $mail->Password = 'fqcyuugcfspsnecz';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('sghaieryoussef7@gmail.com', 'SkillBoost');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body = "Votre code de vérification est : <b>$code</b><br><br>
                           <a href='http://votresite.com/reinitialiser_mdp.php?token=$token'>Réinitialiser mon mot de passe</a>";
            $mail->AltBody = "Votre code est : $code. Lien : http://votresite.com/reinitialiser_mdp.php?token=$token";

            $mail->send();

            $_SESSION['reset_email'] = $email;
            $_SESSION['code_verification'] = $code;
            header("Location: verification_code.php");
            exit();
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erreur d'envoi : {$mail->ErrorInfo}</div>";
        }
    } else {
        echo "<script>alert('Email non trouvé');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié | SkillBoost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
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
    <div class="logo">🔐 SkillBoost</div>
    <h4 class="text-center mb-3">Mot de passe oublié</h4>
    <p class="text-muted text-center">Entrez votre adresse email pour recevoir un code de vérification.</p>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="exemple@email.com">
        </div>
        <button type="submit" name="envoyer_code" class="btn btn-primary">
            Envoyer le code
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
