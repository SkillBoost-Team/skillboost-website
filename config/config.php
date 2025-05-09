<?php
class Database {
    private $host = "localhost";
    private $db_name = "skillboost";
    private $username = "root";
    private $password = "";
    private static $instance = null;
    private $conn = null;

    private function __construct() {
        try {
            // Connexion directe à la base de données
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,
                                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_PERSISTENT, false);
            $this->conn->exec("set names utf8mb4");
            
            // Vérifier si la table existe
            $stmt = $this->conn->query("SHOW TABLES LIKE 'investissement'");
            if ($stmt->rowCount() == 0) {
                echo "La table 'investissement' n'existe pas. Tentative de création...<br>";
                $sql = file_get_contents(__DIR__ . '/setup.sql');
                $this->conn->exec($sql);
                echo "Table créée avec succès.<br>";
            }
            
        } catch(PDOException $exception) {
            // Si la base de données n'existe pas, la créer
            if ($exception->getCode() == 1049) { // Code d'erreur pour "database not found"
                try {
                    $tempConn = new PDO("mysql:host=".$this->host, $this->username, $this->password);
                    $tempConn->exec("CREATE DATABASE IF NOT EXISTS ".$this->db_name);
                    $tempConn = null;
                    
                    // Reconnecter à la nouvelle base de données
                    $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,
                                        $this->username, $this->password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->conn->setAttribute(PDO::ATTR_PERSISTENT, false);
                    $this->conn->exec("set names utf8mb4");
                } catch(PDOException $e) {
                    die("Erreur lors de la création de la base de données: " . $e->getMessage());
                }
            } else {
                die("Erreur de connexion: " . $exception->getMessage());
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function __destruct() {
        $this->conn = null;
    }

    // Like a project
    public function likeProject($user_id, $project_id) {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO project_likes (user_id, project_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $project_id]);
    }

    // Unlike a project
    public function unlikeProject($user_id, $project_id) {
        $stmt = $this->conn->prepare("DELETE FROM project_likes WHERE user_id = ? AND project_id = ?");
        return $stmt->execute([$user_id, $project_id]);
    }

    // Get liked project IDs for a user
    public function getLikedProjectIds($user_id) {
        $stmt = $this->conn->prepare("SELECT project_id FROM project_likes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
