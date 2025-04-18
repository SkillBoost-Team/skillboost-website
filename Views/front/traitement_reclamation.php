<?php
// Connexion à la base de données MySQL
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = ""; // Remplacez par votre mot de passe MySQL
$dbname = "skillboost";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupération des données du formulaire
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $type = $_POST['type'];
    $priority = $_POST['priority'];
    $description = $_POST['description'];
    $status = "Nouveau"; // Statut par défaut
    $created_at = date('Y-m-d H:i:s'); // Date actuelle
    
    // Préparation de la requête SQL
    $sql = "INSERT INTO reclamations (user_id, full_name, email, SUBJECT, TYPE, priority, description, STATUS, created_at)
            VALUES (:user_id, :full_name, :email, :subject, :type, :priority, :description, :status, :created_at)";
    
    $stmt = $conn->prepare($sql);
    
    // Liaison des paramètres
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':created_at', $created_at);
    
    // Exécution de la requête
    $stmt->execute();
    
    // Redirection avec message de succès
    header("Location: index.php?success=1");
    exit();
    
} catch(PDOException $e) {
    // En cas d'erreur, redirection avec message d'erreur
    header("Location: index.php?error=1");
    exit();
}

// Fermeture de la connexion
$conn = null;
?>