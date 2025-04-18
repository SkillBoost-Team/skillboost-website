<?php
$host = "localhost";
$user = "root"; // Par défaut, XAMPP n'a pas de mot de passe
$password = "";
$dbname = "gestion_utilisateurs";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>