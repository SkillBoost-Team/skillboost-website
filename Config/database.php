<?php
$conn = new mysqli("localhost", "root", "", "skillboost");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
