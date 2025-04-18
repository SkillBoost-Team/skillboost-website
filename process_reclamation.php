<?php
require_once 'controllers/ReclamationController.php';

$controller = new ReclamationController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->processForm();
} else {
    header('Location: /reclamations/create');
    exit;
}
?>