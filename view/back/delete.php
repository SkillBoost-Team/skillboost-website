<?php
include '../../controller/eventcontroller.php'; 
$userController = new EvenementController(); 
$userController->deleteEvenement($_GET["id"]); 
header('Location:liste.php'); 
?>