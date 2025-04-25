<?php
// Include the configuration file to connect to the database
require_once '../../config/config.php';

// Include the DashboardModel to interact with the database
require_once '../../model/DashboardModel.php';

// Create an instance of the DashboardModel
$model = new DashboardModel($pdo);

// Get the formation ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: dashboard.php');
    exit();
}

// Delete the formation from the database
$model->deleteFormation($id);

// Redirect to the dashboard
header('Location: dashboard.php');
exit();