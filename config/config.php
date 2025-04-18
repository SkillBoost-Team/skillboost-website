<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
// Database configuration

define('DB_HOST', 'localhost');
define('DB_NAME', 'projet web');
define('DB_USER', 'root');
define('DB_PASS', '');

// DSN (Data Source Name)
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

try {
    // Create a PDO instance
    $pdo = new PDO($dsn, DB_USER, DB_PASS);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Handle error
    die('Database connection failed: ' . $e->getMessage());
}
?>
