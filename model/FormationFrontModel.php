<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>

<?php
class FormationModel {
    public static function getAllFormations() {
        global $pdo;

        try {
            // Fetch all formations from the database
            $stmt = $pdo->query("SELECT id, titre, description FROM formation");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database query failed: ' . $e->getMessage());
        }
    }
}
?>